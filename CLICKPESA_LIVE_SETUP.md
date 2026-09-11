# ClickPesa live setup — GasPOA

Ujumbe **“ClickPesa bado haijasanidiwa”** unatokea kwa sababu project hii haina `CLICKPESA_*` credentials kwenye `.env` ya sasa. Kitufe cha **Lipa kwa ClickPesa** kimeunganishwa na Hosted Checkout; hakina uwezo wa kuanza malipo ya kweli hadi credentials na webhook ziwekwe.

## Usalama wa database

Hatua hizi hazifuti database. Usitumie `migrate:fresh`, `db:wipe`, `db:seed`, `schema:drop`, au SQL yenye `DROP TABLE`.

Kabla ya production, fanya backup. Ikiwa database yako ya zamani haina tables/columns za gateway, import `database/manual_production_payment_upgrade.sql` kwa phpMyAdmin au:

```powershell
Get-Content database\manual_production_payment_upgrade.sql -Raw | & C:\xampp\mysql\bin\mysql.exe -u root gaspoa_market
```

Script hiyo ni additive: huongeza columns, indexes na audit tables vinapokosekana; haifuti row, table, au column ya zamani.

## 1. Tengeneza Hosted Application kwenye ClickPesa

1. Ingia ClickPesa Dashboard → **Settings → Developers**.
2. Tengeneza Hosted Application yenye Hosted Checkout / Payment API.
3. Hifadhi **Client ID**, **API Key**, na **Checksum Key** kwa siri. API key huonyeshwa mara moja tu; usiiweke Git, screenshot, au chat.
4. Weka Hosted Checkout Return URL ya HTTPS, kwa mfano `https://app.example.co.tz/consumer/order/tracking`.
5. Kwenye Application Webhooks, ongeza `PAYMENT RECEIVED` na `PAYMENT FAILED`, zote zielekee:

   ```text
   https://app.example.co.tz/webhooks/clickpesa
   ```

ClickPesa hutuma HTTP POST kwenye webhook na inahitaji response ya 2xx. Mfumo huu huhakiki checksum kabla ya kubadili agizo kuwa `paid`.

## 2. Weka mazingira ya production

Badilisha domain kwa domain yako halisi ya HTTPS; `localhost` haiwezi kupokea webhook kutoka ClickPesa.

```dotenv
APP_ENV=production
APP_DEBUG=false
APP_URL=https://app.example.co.tz
SESSION_SECURE_COOKIE=true

CLICKPESA_ENABLED=true
CLICKPESA_BASE_URL=https://api.clickpesa.com/third-parties
CLICKPESA_CLIENT_ID=paste-client-id-here
CLICKPESA_API_KEY=paste-api-key-here
CLICKPESA_CHECKSUM_KEY=paste-checksum-key-here
```

Baada ya kuhifadhi `.env`, kwenye folder la project endesha:

```powershell
php artisan optimize:clear
php artisan config:cache
```

Amri hizo husafisha/cache configuration tu; hazifuti database wala ku-run migration/seeder.

Ikiwa unawasha au kuzima checksum kwenye Dashboard, tengeneza API token/key mpya kama ClickPesa inavyoelekeza, badilisha `CLICKPESA_CHECKSUM_KEY`, halafu rudia `php artisan optimize:clear`.

## 3. Jaribio salama kabla ya kwenda live

1. Weka public HTTPS URL (kwa majaribio ya XAMPP tumia tunnel iliyoelezwa kwenye `CLOUDFLARE_TUNNEL.md`).
2. Hakikisha `APP_URL` ni URL hiyo hiyo ya public, si `http://127.0.0.1:8000`.
3. Tengeneza order mpya ya test na bonyeza **Lipa kwa ClickPesa**.
4. Thibitisha kuwa browser inaelekezwa kwenye `checkoutLink` ya ClickPesa.
5. Kamilisha payment ya test/ndogo. Webhook ikifika kwa checksum sahihi, `payments.status` na `payment_status` ya order vitakuwa `success`/`paid` bila operator kuthibitisha kwa mkono.
6. Thibitisha callback audit kwa query ya kusoma tu:

   ```sql
   SELECT id, provider, provider_reference, status, provider_payment_reference, paid_at
   FROM payments
   ORDER BY id DESC
   LIMIT 20;
   ```

## Ukiona bado haifanyi kazi

- Ujumbe wa “bado haijasanidiwa”: angalia keys zote nne hapo juu, hasa `CLICKPESA_ENABLED=true`, kisha `php artisan optimize:clear`.
- Ujumbe wa “Imeshindikana kuanzisha”: angalia `storage/logs/laravel.log` kwenye server kwa response ya ClickPesa; usiweke API key kwenye log/screenshot.
- Checkout imekamilika lakini order ni pending: hakikisha webhook URL ni public HTTPS, event zote mbili zimewezeshwa, `APP_URL` ni sahihi, na checksum key inafanana na Dashboard.
- Callback inarudia: mfumo huweka event moja tu kwenye `payment_webhook_events`; hii ni kinga dhidi ya duplicate callback.

Rejea official: [Hosted Checkout setup](https://docs.clickpesa.com/application/hosted-application-setup), [Generate Checkout Link](https://docs.clickpesa.com/api-reference/collection/generate-checkout-link/generate-checkout-link), [Webhooks](https://docs.clickpesa.com/home/webhooks), na [Checksum](https://docs.clickpesa.com/home/checksum).
