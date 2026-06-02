# 🏗️ Arquitectura — CyberPyme G7

Documentació tècnica de l'arquitectura de la plataforma **CyberPyme G7**, un entorn de ciberseguretat virtualitzat per a PYME desplegat a AWS EC2 amb Docker Compose.

---

## Taula de continguts

- [Disseny del sistema](#disseny-del-sistema)
- [Topologia de xarxa](#topologia-de-xarxa)
- [Infraestructura](#infraestructura)
- [Serveis principals](#serveis-principals)
- [Capa de seguretat](#capa-de-seguretat)
- [Monitorització i observabilitat](#monitorització-i-observabilitat)

---

## Disseny del sistema

L'arquitectura segueix principis de **separació de serveis mitjançant contenedors Docker**, dissenyada per garantir **disponibilitat**, **seguretat** i **facilitat de desplegament** en una instància única d'AWS EC2.

```
┌─────────────────────────────────────────────────────────────────┐
│                    INTERNET (Accés públic)                      │
└────────────────────────────┬────────────────────────────────────┘
                             │
                    ┌────────▼────────┐
                    │   Cloudflare    │ ◄── Protecció DDoS
                    │   DNS + CDN     │     Terminació SSL/TLS
                    └────────┬────────┘     Rate Limiting
                             │
                    ┌────────▼────────┐
                    │   AWS EC2       │ ◄── Instància única
                    │  t3.large       │     Ubuntu 22.04 LTS
                    │ 3.215.30.52     │     2 vCPU / 8 GB RAM
                    └────────┬────────┘
                             │
          ┌──────────────────┼──────────────────┐
          │                  │                  │
   ┌──────▼──────┐   ┌───────▼──────┐   ┌──────▼──────┐
   │  S0 Firewall│   │  S7 Wazuh    │   │  S8 Grafana │
   │  iptables   │   │  SIEM        │   │  Monitoring │
   └──────┬──────┘   └───────┬──────┘   └──────┬──────┘
          │                  │                  │
   ┌──────▼──────────────────▼──────────────────▼──────┐
   │            Docker Compose Orchestration            │
   ├────────────────────────────────────────────────────┤
   │  ┌──────────┐  ┌──────────┐  ┌─────────────────┐  │
   │  │ S1 Nginx │  │ S2/S3   │  │  S4 MariaDB     │  │
   │  │  Proxy   │  │ PHP-FPM  │  │  Base de dades  │  │
   │  └──────────┘  └──────────┘  └─────────────────┘  │
   │  ┌──────────┐  ┌──────────┐  ┌─────────────────┐  │
   │  │ S5 Redis │  │ S6 LDAP  │  │  S10 Postfix    │  │
   │  │ Sessions │  │   Auth   │  │  Correu SMTP    │  │
   │  └──────────┘  └──────────┘  └─────────────────┘  │
   │  ┌──────────┐  ┌──────────┐  ┌─────────────────┐  │
   │  │ S11 Snort│  │ S12 Ollama│ │  S9 Scanner     │  │
   │  │  IDS/IPS │  │  IA       │  │  Nmap/Shodan    │  │
   │  └──────────┘  └──────────┘  └─────────────────┘  │
   └──────────────────────┬─────────────────────────────┘
                          │
              ┌───────────┼───────────┐
              │           │           │
       ┌──────▼────┐ ┌────▼────┐ ┌───▼──────┐
       │  db_data  │ │mail_logs│ │snort_logs│
       │  (volum)  │ │ (volum) │ │  (volum) │
       └───────────┘ └─────────┘ └──────────┘
```

---

## Topologia de xarxa

**Arquitectura de dues xarxes** (Defensa en profunditat):

| Xarxa | Serveis | Accés |
|---|---|---|
| **net_public** | S0, S1, S9 | Accés des d'Internet (ports 80/443) |
| **net_private** | S2–S8, S10–S12 | Accés intern entre contenedors únicament |

Els serveis de dades (S4 MariaDB, S5 Redis, S6 OpenLDAP) **no publiquen cap port** i només són accessibles des de `net_private`, mai des d'Internet.

---

## Infraestructura

El projecte s'executa sobre **una única instància AWS EC2** gestionada manualment, sense Terraform ni Auto Scaling. Tot el desplegament es realitza amb `docker compose`.

Recursos principals:
- **EC2 `t3.large`** — Ubuntu 22.04 LTS, IP pública estàtica `3.215.30.52`
- **Security Groups d'AWS** — Tallafoc de primer nivell (ports 22, 80, 443, 1514, 3000, 55000)
- **Volums Docker locals** — Persistència de dades a `~/ProjecteFinal_G7/`
- **AWS S3** — Backups de la base de dades (opcionals)
- **Cloudflare** — DNS i protecció DDoS bàsica

> ⚠️ **Nota del projecte:** L'entorn s'executa sota el **AWS Learner Lab**, que té un límit de sessió de 4 hores i aproximadament 100 USD de crèdits. Tot el codi està al repositori GitHub per poder re-desplegar ràpidament.

---

## Serveis principals

### S0 — Tallafoc perimetral (iptables)
- Primer punt d'entrada de la xarxa interna
- Habilita `ip_forward` per a enrutament entre `net_public` i `net_private`
- Regles NAT amb `MASQUERADE` per a tràfic de sortida
- Requereix `CAP_NET_ADMIN`, `CAP_NET_RAW`, `CAP_SYS_ADMIN`

Fitxer: [`Dockerfile.s0`](./Dockerfile.s0) | [`firewall.sh`](./firewall.sh)

### S1 — Servidor web: Nginx 1.24
- Proxy invers cap a S2 i S3 (PHP-FPM)
- Suport HTTP/2, capçaleres de seguretat (`X-Frame-Options`, `HSTS`, `X-Content-Type-Options`)
- Certificat TLS via Let's Encrypt (Certbot)
- Balanceig de càrrega entre S2 i S3

Configuració: [`config/nginx/default.conf`](./config/nginx/default.conf)

### S2 / S3 — Backend PHP-FPM
- Dos nodes PHP per alta disponibilitat
- Aplicació web de pedidos del client (inspirada en Nestlé)
- Autenticació via sessions PHP + OpenLDAP
- Accés en lectura als logs de Snort i Postfix (volums `:ro`)
- Interfície SOC: `socmail.php` per visualitzar alertes i correus

### S4 — Base de dades: MariaDB 10.11
- Emmagatzemat al volum local `./db_data`
- Usuari de BD amb permisos mínims (`SELECT`, `INSERT`, `UPDATE`)
- Credencials gestionades via `.env` (no inclòs al repositori)
- Taules principals: `users`, `orders`, `products`, `security_events`

Esquema: [`setup/database/schema.sql`](./setup/database/schema.sql)

### S5 — Gestió de sessions: Redis 7
- Emmagatzematge de sessions PHP (compartides entre S2 i S3)
- Comptadors de rate limiting per a l'API
- Tokens OTP i restabliment de contrasenya (expiració per TTL)
- Protegit amb contrasenya via variable d'entorn `REDIS_PASSWORD`

### S6 — Autenticació: OpenLDAP 2.6
- Autenticació i autorització centralitzada d'usuaris
- Estructura del directori:
  ```
  dc=g7,dc=local
  ├── ou=users
  ├── ou=groups (admins, workers)
  └── ou=services
  ```
- Integració PHP via extensió `ldap` (SSO)
- Totes les consultes LDAP xifrades amb TLS

### S7 — SIEM: Wazuh Manager 4.7
- Monitorització centralitzada de seguretat
- Esdeveniments monitoritzats: fallades SSH, integritat de fitxers, anàlisi de logs
- Integrat amb Snort (S11) per rebre alertes IDS
- Alertes enviades per correu via Postfix (S10)

### S8 — Monitorització: Grafana 10.2
- Accessible al port `3000`
- Dashboards principals:
  1. **Estat del sistema** — CPU, RAM, disc, xarxa, estat dels contenedors
  2. **Seguretat** — Alertes Snort per tipus, intents de login fallits
  3. **Aplicació** — Temps de resposta PHP, errors HTTP

### S9 — Scanner: Nmap / Shodan API
- Auditoria periòdica de ports exposats
- Integració amb la API de Shodan per a reconeixement extern
- Resultats disponibles a la interfície web

### S10 — Servidor de correu: Postfix
- SMTP per a correus d'alerta interns
- Buzó d'entrada accessible via `socmail.php`
- Volum `./mail_logs` compartit en lectura amb S2/S3

### S11 — IDS/IPS: Snort
- Mode `fast` amb logs a `/var/log/snort/alert`
- Conjunt de regles personalitzat per a la xarxa del projecte
- Alertes llegides per `socmail.php` i enviades a Wazuh (S7)
- Volum `./snort_logs` compartit en lectura amb S2/S3

### S12 — Motor IA: Ollama
- Inferència de models LLM locals (sense dependència de tercers)
- Limitat a `mem_limit: 3g` i `cpus: 0.8` per no saturar la instància
- Models disponibles per a anàlisi d'events de seguretat

---

## Capa de seguretat

### S0 Tallafoc (iptables)
- Primera línia de defensa de la xarxa interna
- `MASQUERADE` per a NAT de sortida
- `FORWARD` permès de `net_private` a `net_public` i viceversa en estat `ESTABLISHED`

### Snort IDS (S11)
- Mode de captura passiva (`-A fast`)
- Regles personalitzades al directori `/etc/snort/rules/`
- Actualització manual de regles via script
- Totes les alertes reenviades a Wazuh SIEM (S7)

### Cloudflare
- Mitigació DDoS capa 3/4/7
- Gestió DNS i certificats SSL automàtics
- Protecció contra bots

### Estratègia de backup
- **Diari** — Backup complet de MariaDB a les 2 AM → AWS S3 (xifrat AES-256)
- **Setmanal** — Snapshot dels volums Docker locals
- **Retenció** — 30 dies (BD), 7 dies (logs)
- **RTO**: < 30 minuts (re-desplegament amb `docker compose up -d --build`)
- **RPO**: < 24 hores

Script: [`scripts/backup_database.sh`](./scripts/backup_database.sh)

---

## Monitorització i observabilitat

### Wazuh SIEM (S7)
- Monitorització centralitzada de seguretat a l'instància EC2
- Esdeveniments monitoritzats: fallades d'autenticació SSH, anàlisi de logs, integritat de fitxers
- Integrat amb Snort (S11) via alertes al port `1514`

### Grafana (S8)
Tres dashboards principals:

1. **Estat del sistema** — CPU, memòria, disc, xarxa, estat dels contenedors Docker
2. **Seguretat** — Alertes Snort per tipus, intents de login SSH fallits, activitat de Wazuh
3. **Aplicació** — Errors HTTP, temps de resposta PHP, pedidos del client

### Alertes

| Nivell | Temps resposta | Exemples | Canal |
|---|---|---|---|
| **P1 Crític** | < 15 min | Servei caigut, bretxa activa | Correu via Postfix + Wazuh |
| **P2 Alt** | < 1 hora | Alta CPU a MariaDB, backup fallat | Correu via Postfix |
| **P3 Mitjà** | < 4 hores | Disc >80%, alta taxa d'errors | Correu via Postfix |
| **P4 Baix** | < 24 hores | Resum setmanal, nova alerta Snort | Interfície `socmail.php` |

---

## Resum de fitxers clau

| Fitxer | Descripció |
|---|---|
| `docker-compose.yml` | Orquestració de tots els serveis S0–S12 |
| `Dockerfile` | Imatge base PHP-FPM per a S2, S3, S9 |
| `Dockerfile.s0` | Imatge del tallafoc iptables (S0) |
| `Dockerfile.s10_s11` | Imatge compartida Postfix + Snort |
| `firewall.sh` | Script de regles iptables per a S0 |
| `config/nginx/default.conf` | Configuració Nginx (proxy + balanceig) |
| `.env` | Variables d'entorn amb credencials (no al repositori) |
| `g7_src/` | Codi font PHP de l'aplicació web |
| `g7_src/socmail.php` | Interfície SOC: correus + alertes Snort |
| `scripts/backup_database.sh` | Script de backup diari a AWS S3 |
| `setup/database/schema.sql` | Esquema SQL de MariaDB |
