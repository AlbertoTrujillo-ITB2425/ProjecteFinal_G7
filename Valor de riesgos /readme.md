# Valoració de Riscos — CyberPyme G7

## 1. Introducció

Aquest document identifica, analitza i proposa mesures de mitigació per als riscos tècnics, operatius i de seguretat associats al desplegament del projecte CyberPyme G7. La infraestructura comprèn 13 serveis Docker (S0–S12) allotjats en una instància AWS EC2 (`t3.large`, IP pública `3.215.30.52`), amb serveis com Nginx, MariaDB, OpenLDAP, Wazuh, Snort, Postfix, Grafana, Redis, Ollama i un firewall perimetral basat en iptables (S0).

La metodologia de valoració segueix un model estàndard de **Probabilitat × Impacte**, classificant cada risc en una matriu de 3×3 nivells: Baix, Mitjà i Alt.

---

## 2. Metodologia

### Escala de Probabilitat

| Valor | Nivell | Descripció |
|---|---|---|
| 1 | Baixa | Poc probable en condicions normals d'operació |
| 2 | Mitjana | Podria ocórrer en alguna fase del projecte |
| 3 | Alta | És previsible que ocorri sense mesures preventives |

### Escala d'Impacte

| Valor | Nivell | Descripció |
|---|---|---|
| 1 | Baix | Afecta aspectes secundaris, fàcilment recuperable |
| 2 | Mitjà | Atura parcialment el servei o exposa dades no crítiques |
| 3 | Alt | Compromet la integritat del sistema o de les dades del client |

### Nivell de Risc

```
Risc = Probabilitat × Impacte

1–2  → 🟢 Baix
3–4  → 🟡 Mitjà
6–9  → 🔴 Alt
```

---

## 3. Inventari de Riscos

### 3.1 Riscos d'Infraestructura
Ara que el que farem és enumerar cada risc amb R i el numero de risc i posteriorment direm la Descripció, Probabilitat, Impacte, Nivell de risc, Causa probable, Mesura preventiva ,Mesura correctiva

#### R01 — Caiguda de la instància EC2

| Camp | Detall |
|---|---|
| **Descripció** | La instància `CyberPyme S1` (`i-0328a683ecd5729b6`) es reinicia o s'atura de forma inesperada, deixant tots els contenedors Docker inactius |
| **Probabilitat** | 2 — Mitjana |
| **Impacte** | 3 — Alt |
| **Nivell de risc** | 🔴 **6 — Alt** |
| **Causa probable** | Esgotament de recursos (CPU/RAM en `t3.large`), error de AWS, o aturada accidental des de la consola |
| **Mesura preventiva** | Activar Auto Recovery a AWS CloudWatch; configurar `restart: always` a tots els serveis Docker (ja fet al docker-compose) |
| **Mesura correctiva** | Script de reinici automàtic: `docker compose up -d` al crontab del servidor |

---

#### R02 — Manca d'espai en disc

| Camp | Detall |
|---|---|
| **Descripció** | Els logs de Snort (`./snort_logs`), els correus de Postfix (`./mail_logs`) i les dades de MariaDB (`./db_data`) creixen fins a omplir el disc de la instància |
| **Probabilitat** | 3 — Alta |
| **Impacte** | 2 — Mitjà |
| **Nivell de risc** | 🔴 **6 — Alt** |
| **Causa probable** | Atac de flooding que genera milers d'alertes Snort; acumulació de correus d'alertes de Wazuh |
| **Mesura preventiva** | Configurar logrotate dins dels contenedors S10 i S11; definir límit de mida als volums Docker |
| **Mesura correctiva** | Monitoritzar ús de disc amb alarma de CloudWatch al 80% |

---

#### R03 — Fallada del firewall S0

| Camp | Detall |
|---|---|
| **Descripció** | El contenedor `s0_firewall` no arrenca correctament o les regles iptables no s'apliquen, deixant la xarxa interna exposada directament a Internet |
| **Probabilitat** | 2 — Mitjana |
| **Impacte** | 3 — Alt |
| **Nivell de risc** | 🔴 **6 — Alt** |
| **Causa probable** | El kernel del host no permet `net.ipv4.ip_forward=1` dins del contenedor; falta de `CAP_NET_ADMIN` |
| **Mesura preventiva** | Verificar amb `docker logs s0_firewall` i `docker exec s0_firewall iptables -L -n -v` després de cada desplegament |
| **Mesura correctiva** | Segon nivell de defensa: Security Groups d'AWS com a firewall de fallback |

---

### 3.2 Riscos de Seguretat

#### R04 — Exposició accidental de ports interns

| Camp | Detall |
|---|---|
| **Descripció** | Serveis com MariaDB (3306), Redis (6379) o OpenLDAP (389) queden accesibles des de l'exterior per una mala configuració del Security Group d'AWS o del docker-compose |
| **Probabilitat** | 2 — Mitjana |
| **Impacte** | 3 — Alt |
| **Nivell de risc** | 🔴 **6 — Alt** |
| **Causa probable** | Error de configuració en publicar ports (`ports:` en lloc de no publicar-los), o regla 0.0.0.0/0 al Security Group |
| **Mesura preventiva** | S4, S5, S6 només estan a `net_private` sense `ports:` al docker-compose; revisar periòdicament amb `nmap -sV 3.215.30.52` |
| **Mesura correctiva** | Auditoria amb S9 Scanner (Nmap/Shodan API) de forma setmanal |

---

#### R05 — Credencials en clar al fitxer `.env`

| Camp | Detall |
|---|---|
| **Descripció** | El fitxer `.env` conté contrasenyes de MariaDB, Redis, LDAP i Grafana en text pla al servidor |
| **Probabilitat** | 2 — Mitjana |
| **Impacte** | 3 — Alt |
| **Nivell de risc** | 🔴 **6 — Alt** |
| **Causa probable** | Upload accidental del `.env` a GitHub; accés no autoritzat al servidor via SSH |
| **Mesura preventiva** | `.env` inclòs al `.gitignore`; permisos `chmod 600 .env`; usar AWS Secrets Manager per a producció |
| **Mesura correctiva** | Rotació immediata de totes les credencials si hi ha compromís |

---

#### R06 — Atac de força bruta SSH

| Camp | Detall |
|---|---|
| **Descripció** | L'IP pública `3.215.30.52` rep intents continus de login per SSH des de bots automatitzats |
| **Probabilitat** | 3 — Alta |
| **Impacte** | 2 — Mitjà |
| **Nivell de risc** | 🔴 **6 — Alt** |
| **Causa probable** | Port 22 exposat a Internet; és un vector d'atac molt comú contra instàncies EC2 |
| **Mesura preventiva** | Autenticació per clau SSH (desactivar login per contrasenya); restringir port 22 al Security Group només a IPs conegudes |
| **Mesura correctiva** | Wazuh (S7) detecta i alerta via Postfix (S10); bloqueig automàtic amb fail2ban |

---

#### R07 — Compromís del contenedor web (S2/S3)

| Camp | Detall |
|---|---|
| **Descripció** | Un atacant explota una vulnerabilitat PHP (RCE, LFI, SQLi) a l'aplicació web i accedeix al sistema de fitxers del contenedor |
| **Probabilitat** | 2 — Mitjana |
| **Impacte** | 3 — Alt |
| **Nivell de risc** | 🔴 **6 — Alt** |
| **Causa probable** | Codi PHP vulnerable (SQLi a formularis, LFI a includes, RCE a funcions `exec()`); falta de validació d'entrada |
| **Mesura preventiva** | Revisar tot el codi PHP amb `php -l`; usar PDO amb prepared statements; Snort (S11) amb regles web application |
| **Mesura correctiva** | Els volums `snort_logs` i `mail_logs` estan muntats en `:ro` a S2/S3, limitant l'accés |

---

#### R08 — Fuga de dades de MariaDB (S4)

| Camp | Detall |
|---|---|
| **Descripció** | Accés no autoritzat a la base de dades del client (la web de pedidos inspirada en Nestlé) |
| **Probabilitat** | 1 — Baixa |
| **Impacte** | 3 — Alt |
| **Nivell de risc** | 🟡 **3 — Mitjà** |
| **Causa probable** | Explotació via SQLi des de l'aplicació web; accés directe si s'exposa el port 3306 per error |
| **Mesura preventiva** | S4 aïllat a `net_private` sense ports publicats; usuari DB amb permisos mínims (`GRANT SELECT, INSERT, UPDATE`) |
| **Mesura correctiva** | Backups diaris del volum `./db_data` a S3 d'AWS |

---

### 3.3 Riscos Operatius

#### R09 — Pèrdua de dades per reinici de contenedors

| Camp | Detall |
|---|---|
| **Descripció** | Dades persistents (DB, LDAP, Ollama) es perden si els volums Docker no estan configurats correctament |
| **Probabilitat** | 1 — Baixa |
| **Impacte** | 3 — Alt |
| **Nivell de risc** | 🟡 **3 — Mitjà** |
| **Causa probable** | `docker compose down -v` executat per error (l'opció `-v` elimina els volums) |
| **Mesura preventiva** | Documentar clarament que mai s'usa `-v`; fer backups previs a qualsevol operació de manteniment |
| **Mesura correctiva** | Script de backup setmanal dels directoris `db_data`, `ldap_data`, `ollama_data` |

---

#### R10 — Consum excessiu de recursos per Ollama (S12)

| Camp | Detall |
|---|---|
| **Descripció** | El motor d'IA Ollama consumeix tota la CPU/RAM de la instància `t3.large` (2 vCPU, 8 GB RAM), degradant la resta de serveis |
| **Probabilitat** | 3 — Alta |
| **Impacte** | 2 — Mitjà |
| **Nivell de risc** | 🔴 **6 — Alt** |
| **Causa probable** | Models LLM grans (7B+ paràmetres) requereixen molta memòria; inferències simultànies |
| **Mesura preventiva** | Limitar recursos a S12: `mem_limit: 3g` i `cpus: '0.8'` al docker-compose |
| **Mesura correctiva** | Monitoritzar amb `docker stats` i Grafana (S8) |

---

#### R11 — Wazuh (S7) no detecta incidents en temps real

| Camp | Detall |
|---|---|
| **Descripció** | S7 no rep logs dels contenedors perquè els agents Wazuh no estan instal·lats als serveis |
| **Probabilitat** | 2 — Mitjana |
| **Impacte** | 2 — Mitjà |
| **Nivell de risc** | 🟡 **4 — Mitjà** |
| **Causa probable** | Wazuh Manager (S7) necessita agents als contenedors monitoritzats o integració via syslog |
| **Mesura preventiva** | Configurar els contenedors PHP (S2/S3) per enviar logs via syslog a S7 al port 1514 |
| **Mesura correctiva** | Com a mínim, Snort (S11) envia alertes directament i Postfix (S10) les distribueix per correu |

---

#### R12 — Caducitat del certificat SSL (Let's Encrypt)

| Camp | Detall |
|---|---|
| **Descripció** | El certificat TLS de Nginx (S1) caduca als 90 dies i la web queda accessible només per HTTP |
| **Probabilitat** | 2 — Mitjana |
| **Impacte** | 2 — Mitjà |
| **Nivell de risc** | 🟡 **4 — Mitjà** |
| **Causa probable** | El procés de renovació automàtica amb Certbot falla si el servei no té accés al directori `./certbot/www` |
| **Mesura preventiva** | Verificar que el volum `./certbot/www:/var/www/certbot` està muntat a S1; afegir cron per a renovació |
| **Mesura correctiva** | `docker exec s1_nginx certbot renew --webroot -w /var/www/certbot` |

---

### 3.4 Riscos del Projecte (Acadèmics)

#### R13 — Limitacions del Learner Lab d'AWS

| Camp | Detall |
|---|---|
| **Descripció** | El compte AWS Learner Lab té un límit de crèdits i temps de sessió; els serveis s'apaguen automàticament |
| **Probabilitat** | 3 — Alta |
| **Impacte** | 2 — Mitjà |
| **Nivell de risc** | 🔴 **6 — Alt** |
| **Causa probable** | Sessions de Learner Lab amb límit de 4 hores; crèdits limitats a ~100 USD |
| **Mesura preventiva** | Documentar tot localment (Markdown/GitHub); tenir el `docker-compose.yml` llest per re-desplegar en minuts |
| **Mesura correctiva** | Script de re-desplegament ràpid: `git clone + docker compose up -d --build` |

---

#### R14 — Falta de coordinació entre membres de l'equip

| Camp | Detall |
|---|---|
| **Descripció** | Canvis simultanis al docker-compose o al codi PHP generen conflictes de Git o incompatibilitats |
| **Probabilitat** | 2 — Mitjana |
| **Impacte** | 1 — Baix |
| **Nivell de risc** | 🟢 **2 — Baix** |
| **Causa probable** | Treball en paral·lel sense branques Git diferenciades |
| **Mesura preventiva** | Usar branques per funcionalitat (`feature/s0-firewall`, `feature/socmail`); fer PRs per fusionar |
| **Mesura correctiva** | Reunió de sincronització setmanal entre Joel, Alberto i Luka |

---

## 4. Matriu de Riscos

| ID | Risc | Probabilitat | Impacte | Nivell |
|---|---|:---:|:---:|:---:|
| R01 | Caiguda instància EC2 | 2 | 3 | 🔴 Alt (6) |
| R02 | Manca d'espai en disc | 3 | 2 | 🔴 Alt (6) |
| R03 | Fallada del firewall S0 | 2 | 3 | 🔴 Alt (6) |
| R04 | Exposició de ports interns | 2 | 3 | 🔴 Alt (6) |
| R05 | Credencials en clar al `.env` | 2 | 3 | 🔴 Alt (6) |
| R06 | Força bruta SSH | 3 | 2 | 🔴 Alt (6) |
| R07 | Compromís del contenedor web | 2 | 3 | 🔴 Alt (6) |
| R08 | Fuga de dades MariaDB | 1 | 3 | 🟡 Mitjà (3) |
| R09 | Pèrdua de dades per reinici | 1 | 3 | 🟡 Mitjà (3) |
| R10 | Consum excessiu d'Ollama | 3 | 2 | 🔴 Alt (6) |
| R11 | Wazuh sense agents | 2 | 2 | 🟡 Mitjà (4) |
| R12 | Caducitat SSL | 2 | 2 | 🟡 Mitjà (4) |
| R13 | Limitacions Learner Lab | 3 | 2 | 🔴 Alt (6) |
| R14 | Falta de coordinació | 2 | 1 | 🟢 Baix (2) |

---

## 5. Resum Executiu

De 14 riscos identificats, **8 són d'alt nivell**, **4 de nivell mitjà** i **1 de baix**. Els riscos més crítics es concentren en:

- **Seguretat perimetral:** exposició de ports, fallada de S0, força bruta SSH — mitigats per la combinació S0 (iptables) + Security Groups d'AWS + Snort (S11)
- **Confidencialitat:** credencials al `.env` i fuga de MariaDB — mitigats per aïllament de xarxa `net_private` i permisos mínims
- **Disponibilitat:** espai en disc i consum d'Ollama — mitigats amb límits de recursos i monitorització via Grafana (S8)
- **Continuïtat del projecte:** limitacions del Learner Lab — mitigades amb documentació Markdown al GitHub i scripts de re-desplegament ràpid

L'arquitectura proposada, amb S0 com a firewall perimetral, S7 Wazuh com a SIEM, S11 Snort com a IDS i S10 Postfix per a notificacions, cobreix de forma adequada la majoria dels vectors d'atac previsibles per a una PYME.
