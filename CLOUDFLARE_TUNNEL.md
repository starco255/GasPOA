# Cloudflare Temporary Tunnel (salama kwa database)

Faili hii huunganisha app ya local na URL ya muda bila kuendesha migration, seeder, reset, au amri yoyote ya database.

## 1. Washa Laravel

Katika terminal ya kwanza:

```powershell
cd C:\xampp\htdocs\GasPOA
.\scripts\start-local-server.ps1
```

Thibitisha kwa kufungua `http://127.0.0.1:8000`. Usianzishe Cloudflare kabla ya hatua hii; bila server ya local tunnel itatoa `connection refused`.

## 2. Washa temporary public URL

Katika terminal ya pili:

```powershell
cd C:\xampp\htdocs\GasPOA
.\scripts\start-temporary-tunnel.ps1 -CloudflaredPath "C:\full\path\to\cloudflared.exe"
```

Kama `cloudflared.exe` ipo kwenye PATH, tumia tu:

```powershell
.\scripts\start-temporary-tunnel.ps1
```

Amri itatoa URL inayofanana na `https://random-name.trycloudflare.com`. URL hii hubadilika kila unapoanzisha tunnel upya.

## 3. Weka URL ya sasa kwenye `.env`

Baada ya kupata URL, badilisha tu mistari hii kwenye `.env`:

```dotenv
APP_URL=https://random-name.trycloudflare.com
ASSET_URL=
SESSION_DOMAIN=
SESSION_SECURE_COOKIE=true
```

Kisha katika terminal ya kwanza bonyeza `Ctrl+C` na uanzishe tena `start-local-server.ps1`. Hii haisababishi mabadiliko ya database. Usitumie `config:cache` ukiwa na temporary URL; URL itabadilika tunnel ikianza upya.

## Tahadhari ya usalama

Temporary tunnel ni kwa demo/test pekee. Kwa live production tumia Cloudflare named tunnel pamoja na domain yako thabiti, `APP_ENV=production`, `APP_DEBUG=false`, na backup ya database kabla ya deploy.
