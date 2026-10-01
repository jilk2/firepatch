# Firepatch WebSocket op Synology

Deze map is een los Container Manager-project. Plaats hem niet in de Web Station-map.

## 1. DNS en certificaat

1. Laat `ws.joeymalta.com` naar hetzelfde publieke IP-adres wijzen als `www.joeymalta.com`.
2. Voeg in DSM bij **Configuratiescherm > Beveiliging > Certificaat** een Let's Encrypt-certificaat voor `ws.joeymalta.com` toe.

## 2. Container starten

1. Kopieer deze hele map naar een vaste map op de NAS, bijvoorbeeld `/volume1/docker/firepatch-websocket`.
2. Open **Container Manager > Project > Maken**.
3. Kies die map en laat Container Manager `docker-compose.yml` gebruiken.
4. Bouw en start het project.
5. Controleer op je lokale netwerk: `http://NAS-IP:8080/health` moet JSON met `"ok": true` tonen.

## 3. Veilige publieke WebSocket

Open **Configuratiescherm > Aanmeldingsportaal > Geavanceerd > Reverse Proxy** en maak deze regel:

- Bronprotocol: `HTTPS`
- Bronhostnaam: `ws.joeymalta.com`
- Bronpoort: `443`
- Doelprotocol: `HTTP`
- Doelhostnaam: `127.0.0.1`
- Doelpoort: `8080`

Kies daarna bij **Aangepaste koptekst** voor **Maken > WebSocket**. Koppel zo nodig het certificaat van `ws.joeymalta.com` aan deze service.

De website en Unreal verbinden vervolgens allebei met:

```text
wss://ws.joeymalta.com
```

De kaart stuurt bijvoorbeeld:

```json
{"X":0.5,"Y":0.5}
```

De server stuurt elk geldig JSON-object door naar alle andere verbonden clients. Er is geen API-key of account nodig.
