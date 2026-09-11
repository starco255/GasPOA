# GasPOA payment configuration

> Kwa database dump uliyoambatanisha, tumia [manual_production_payment_upgrade.sql](database/manual_production_payment_upgrade.sql) na [PRODUCTION_CONFIGURATION_GUIDE.md](PRODUCTION_CONFIGURATION_GUIDE.md). Faili hiyo mpya ina audit ya webhook na haina data-update statements; mwongozo huu wa zamani umeachwa kwa compatibility ya setup za awali.

Mfumo umechagua **ClickPesa Hosted Checkout**. Inafaa kwa mfumo huu kwa sababu checkout moja inakubali M-Pesa, TigoPesa/Mixx, Airtel Money, HaloPesa, benki na kadi; callback yenye checksum ndiyo hubadilisha oda kutoka `pending` kwenda `paid`.

## 1. Boresha database iliyopo

Import faili [manual_payment_upgrade.sql](database/manual_payment_upgrade.sql) kwa phpMyAdmin au MySQL client baada ya ku-import `gaspoa_market.sql`:

```powershell
Get-Content database\manual_payment_upgrade.sql -Raw | & C:\xampp\mysql\bin\mysql.exe -u root gaspoa_market
```

Faili hii huongeza columns/tables pekee. Usitumie `php artisan migrate` kwenye database dump ya zamani isipokuwa umeiweka Laravel migrations table sawa; migration za project zimetolewa kwa mazingira mapya ya Laravel.

## 2. Weka ClickPesa kwenye `.env`

Ongeza thamani hizi (usihifadhi key halisi kwenye Git):

```dotenv
APP_URL=https://app.your-domain.co.tz
CLICKPESA_ENABLED=true
CLICKPESA_CLIENT_ID=your_clickpesa_client_id
CLICKPESA_API_KEY=your_clickpesa_api_key
CLICKPESA_CHECKSUM_KEY=your_clickpesa_checksum_key
CLICKPESA_BASE_URL=https://api.clickpesa.com/third-parties
```

Kisha endesha `php artisan config:clear`.

## 3. Sanidi dashboard ya ClickPesa

1. Tengeneza API application yenye **Payment API** na uwashe **Hosted Checkout**.
2. Weka checksum kwenye application na nakili checksum key kwenye `.env`.
3. Weka application webhook kwa `PAYMENT RECEIVED` na `PAYMENT FAILED`:
   `https://app.your-domain.co.tz/webhooks/clickpesa`
4. Whitelist IP ya production server kwenye ClickPesa. `localhost` ya XAMPP haiwezi kupokea webhook ya mtandao wa nje; tumia HTTPS public URL wakati wa majaribio.

## Mtiririko wa malipo

- Consumer huchagua Retailer na njia ambazo Retailer ameweka; order huandikwa `pending` kwenye `retail_orders`.
- Retailer huchagua njia zilizowekwa na kila Wholesaler; order huandikwa `pending` kwenye `wholesale_orders`.
- Kwa njia zisizo cash, rekodi huandikwa kwenye `payments`. Kitufe **Lipa kwa ClickPesa** hutengeneza checkout link.
- ClickPesa ikituma webhook sahihi ya `PAYMENT RECEIVED`, mfumo huweka `payments.status=success`, huandika transaction reference, na hubadilisha `payment_status=paid` kwenye order husika atomically.
- Cash hubaki `pending` hadi retailer athibitishe pesa imepokelewa wakati wa delivery. Kwa Lipa Namba/benki za moja kwa moja bila ClickPesa, hakuna API inayoweza kuthibitisha uhamisho; hivyo hubaki `pending` hadi uthibitisho wa gateway au wa muuzaji.

`payouts` imetengwa kwa settlement ya baadaye. Usianzishe payout automatically baada ya callback bila sera ya commission/refund na idhini ya fedha.
