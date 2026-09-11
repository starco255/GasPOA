# Mwongozo wa production: Malipo, email OTP, SMS na WhatsApp

Faili hii inaeleza jinsi ya kuwasha huduma halisi za GasPOA. Usihifadhi secrets kwenye Git, screenshots, au chat. Kwa sababu app password ya Gmail ilishawahi kushirikiwa, ibadilishe (rotate) baada ya kujaribu mfumo.

## 1. Database bila kuharibu ya zamani

Kwanza fanya backup ya database. Kisha import [manual_production_payment_upgrade.sql](database/manual_production_payment_upgrade.sql) kupitia phpMyAdmin, au kwa command hii (badilisha `gaspoa_market` kama jina lako ni tofauti):

```powershell
Get-Content database\manual_production_payment_upgrade.sql -Raw | & C:\xampp\mysql\bin\mysql.exe -u root gaspoa_market
```

Faili hii haifuti table, column, au row yoyote. Inaongeza tu audit columns za gateway, `payment_webhook_events` kwa callbacks za ClickPesa, na indexes. Usiendeshe `php artisan migrate` kwenye dump yako ya zamani kwa ajili ya upgrade hii; tumia SQL hapo juu manual.

## 2. `.env` ya Gmail SMTP

Tumia account ya Gmail yenye **2-Step Verification** na **App Password**. Katika `.env` ya server, weka (password halisi ibaki kwenye `.env` tu):

```dotenv
APP_URL=https://app.example.co.tz
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-gmail-address@gmail.com
MAIL_PASSWORD=your-16-character-gmail-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-gmail-address@gmail.com
MAIL_FROM_NAME="GasPOA Market"
```

Mradi huu tayari umewekewa SMTP username, app password, na sender address ulizotoa ndani ya `.env` ya local; hazijaandikwa kwenye source code. Baada ya kubadilisha `.env`, endesha:

```powershell
php artisan optimize:clear
```

Usajili wenye email hutuma `GasPOA - Karibu kwenye mfumo`. Forgot Password hutengeneza OTP ya tarakimu 6, kuituma kwa email, kuithibitisha, halafu kumruhusu mtumiaji kuweka nywila mpya. Ili email ifike kwenye production, `MAIL_MAILER` isiwe `log` au `array`, na server iruhusu outbound TCP port 587.

## 3. ClickPesa Hosted Checkout

Katika ClickPesa Dashboard, tengeneza application ya API yenye **Hosted Checkout/Payment API**, halafu nakili Client ID, API Key, na checksum key kwenye `.env`:

```dotenv
APP_URL=https://app.example.co.tz
CLICKPESA_ENABLED=true
CLICKPESA_BASE_URL=https://api.clickpesa.com/third-parties
CLICKPESA_CLIENT_ID=your_clickpesa_client_id
CLICKPESA_API_KEY=your_clickpesa_api_key
CLICKPESA_CHECKSUM_KEY=your_clickpesa_checksum_key
```

Kwenye dashboard ya ClickPesa:

1. Weka webhook ya production: `https://app.example.co.tz/webhooks/clickpesa`.
2. Washa matukio `PAYMENT RECEIVED` na `PAYMENT FAILED`.
3. Weka Return URL kwenye Hosted Checkout settings (mfano `https://app.example.co.tz/consumer/order/tracking`).
4. Washa checksum na tengeneza token/API key mpya kama dashboard inavyoagiza baada ya kubadilisha checksum settings.
5. Whitelist IP ya server yako kama account yako ya ClickPesa inatumia IP whitelisting.

Baada ya `php artisan optimize:clear`, kitufe **Lipa kwa ClickPesa** hutengeneza Hosted Checkout URL na kumpeleka mteja kwenye menu halisi ya ClickPesa ya kuchagua/kukamilisha malipo. Mfumo haukusanyi PIN, OTP, au taarifa ya kadi. Webhook yenye checksum sahihi ndiyo huweka order `paid` atomically; callback inarudiwa huhifadhiwa mara moja kwenye `payment_webhook_events`.

`localhost` ya XAMPP haiwezi kupokea webhook kutoka ClickPesa. Kwa test ya local tumia HTTPS public tunnel, kwa mfano URL ya ngrok/Cloudflare Tunnel, uiweke kama `APP_URL` na webhook URL ya muda. Usitumie tunnel ya development kwa production.

Kwa marejeo ya API: [ClickPesa Hosted Checkout](https://docs.clickpesa.com/checkout/hosted-checkout/hosted-checkout), [Generate checkout link](https://docs.clickpesa.com/api-reference/collection/generate-checkout-link/generate-checkout-link), na [checksum validation](https://docs.clickpesa.com/home/checksum).

> Muhimu kwa marketplace: credentials moja ya ClickPesa hukusanya fedha kwenye merchant account ya GasPOA. `payouts` table ipo kwa settlement ya wauzaji, lakini usianzishe auto-payout kabla ya kuamua commission, refund policy, na kupata payout approval/credentials za ClickPesa. Hili huzuia kupeleka fedha kwa muuzaji asiye sahihi.

## 4. Kutuma SMS OTP halisi (Beem Africa)

Mradi una `SmsService` tayari. Ili utumie Beem Africa, fungua account ya biashara, sajili Sender ID `GasPOA`, halafu weka:

```dotenv
BEEM_API_KEY=your_beem_api_key
BEEM_SECRET_KEY=your_beem_secret_key
BEEM_SENDER_ID=GasPOA
```

Kabla ya kuwezesha production, badilisha `SmsService::send()` itumie `sendViaBeem()` na ishughulikie response/error ya provider; usiweke OTP kwenye `storage/logs`. Kwa template, tuma ujumbe mfupi kama: `GasPOA: OTP yako ni 123456. Inaisha baada ya dakika 10. Usimpe mtu yeyote.` Tumia rate limit (angalau 1 kwa dakika kwa account) na usijibu kwa SMS OTP kwenye response ya browser.

## 5. WhatsApp OTP halisi (Meta WhatsApp Cloud API)

1. Tengeneza WhatsApp Business app kwenye Meta for Developers na uthibitishe business/phone number.
2. Unda template iliyokubaliwa, mfano `gaspoa_otp`, yenye variable `{{1}}` kwa OTP na `{{2}}` kwa muda wa kuisha.
3. Weka credentials kwenye `.env`:

```dotenv
WHATSAPP_PHONE_NUMBER_ID=your_phone_number_id
WHATSAPP_ACCESS_TOKEN=your_permanent_system_user_token
WHATSAPP_TEMPLATE=gaspoa_otp
WHATSAPP_GRAPH_VERSION=v23.0
```

4. Tuma `POST https://graph.facebook.com/{version}/{phone-number-id}/messages` kwa bearer token, `to` katika E.164 bila `+` (mfano `2557XXXXXXXX`), `type=template`, language `sw`, na OTP kama template parameter.
5. Hifadhi delivery status ya WhatsApp webhook, tumia OTP ileile ya database, na usitume OTP code kwenye log au screen.

Kwa SMS/WhatsApp, user awe na opt-in na njia ya fallback ya email. Email OTP ndiyo flow inayotumika sasa kwenye Forgot Password; SMS/WhatsApp ni channel za kuongeza baada ya credentials na template approvals kupatikana.
