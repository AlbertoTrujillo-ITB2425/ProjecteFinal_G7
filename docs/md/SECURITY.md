# 🔒 Seguretat — CyberPyme G7

Especificacions de seguretat, informes de pentesting i documentació de compliment normatiu del projecte **CyberPyme G7**.

---

## Taula de continguts

- [Arquitectura de seguretat](#arquitectura-de-seguretat)
- [Informe de pentesting — Fase 1 (Lab IsardVDI)](#informe-de-pentesting--fase-1-lab-isardvdi)
- [Auditoria AWS — Fase 2 (Producció)](#auditoria-aws--fase-2-producció)
- [Compliment normatiu](#compliment-normatiu)

---

## Arquitectura de seguretat

### Defensa en profunditat

CyberPyme G7 utilitza un enfocament de seguretat per capes amb múltiples controls independents:

```
Internet → Cloudflare (DDoS/CDN) → AWS Security Groups → S0 Firewall (iptables)
                                                        ↘ S1 Nginx (proxy invers)
                                                        ↘ S11 Snort IDS
                                                        ↘ S7 Wazuh SIEM ← Totes les capes
```

### Tallafoc perimetral: S0 (iptables)

- Primera línia de defensa de la xarxa interna Docker
- Habilita `ip_forward` per a enrutament entre `net_public` i `net_private`
- Regles NAT `MASQUERADE` per a tràfic de sortida controlat
- Requereix `CAP_NET_ADMIN`, `CAP_NET_RAW` i `CAP_SYS_ADMIN`
- Protegeix contra: accés directe als serveis interns, tràfic no autoritzat entre xarxes

> ⚠️ **Nota:** El projecte **no utilitza BunkerWeb WAF** ni **AWS ALB**. La protecció WAF es realitza directament via Nginx (S1) amb capçaleres de seguretat i regles de rate limiting, i Snort (S11) actua com a IDS.

### Detecció d'intrusions: Snort (S11)

- Mode de captura passiva (`-A fast`) amb logs a `/var/log/snort/alert`
- Conjunt de regles personalitzat per a la pila d'aplicació del projecte (patrons SQLi, agents maliciosos)
- Totes les alertes llegides per `socmail.php` i reenviades a Wazuh SIEM (S7)
- Volum `./snort_logs` compartit en lectura (`:ro`) amb els contenedors PHP (S2/S3)

### Protecció DDoS: Cloudflare

- Mitigació d'atacs capa 3/4/7
- Sempre HTTPS, TLS mínim 1.2, DNSSEC activat
- Gestió de bots: desafia bots maliciosos, permet rastrejadors verificats
- DNS centralitzat amb protecció contra amplificació DNS

### SIEM: Wazuh (S7)

- Monitoratge centralitzat de seguretat a la instància EC2
- Monitoratge d'integritat de fitxers (FIM) a `/etc`, `/var/www/html`, `/root`
- Verificacions de compliment: RGPD, NIS2
- Alertes personalitzades: força bruta SSH, canvis en directoris web, indicadors de cryptomining
- Alertes enviades per correu via Postfix (S10) i visibles a `socmail.php`

> ⚠️ **Nota:** El projecte **no utilitza AWS CloudTrail ni GuardDuty** (requereixen configuració addicional fora de l'àmbit del Learner Lab). La integració amb Wazuh es fa directament via syslog des dels contenedors.

### Gestió de vulnerabilitats: S9 Scanner (Nmap + Shodan API)

- Escanejos setmanals automatitzats dels ports i dominis del projecte
- Alertes per: serveis perillosos exposats (FTP, Telnet, RDP, SMB), programari desactualitzat, errors de configuració SSL, CVEs conegudes
- Resultats accessibles des de la interfície web del projecte

---

## Informe de pentesting — Fase 1 (Lab IsardVDI)

> **Fase**: Validació de seguretat pre-migració (Lab IsardVDI)
> **Auditor principal**: Joel Muñoz (`@joel㉿kali2025`)
> **Entorn**: IsardVDI (Kali Linux 2025)

### Metodologia

El projecte segueix un marc modular alineat amb **PTES** (*Penetration Testing Execution Standard*) per a la traçabilitat de les evidències:

```
01_recon/       # Descobriment d'hosts i escaneig de ports
02_scan/        # Anàlisi de vulnerabilitats (Trivy, DNS, SSH, SMB)
03_exploits/    # Logs de PoC i explotació
04_web_tests/   # Auditoria de Nginx, PHP, SQLi i XSS
05_wazuh_tests/ # Validació d'alertes SIEM i telemetria FIM
06_reports/     # Documentació final
scripts/        # Suite d'automatització en Bash
```

### Reconeixement — Descobriment de xarxa

Segment: `192.168.120.0/22`
- **Passarel·la**: `192.168.120.1` (pfSense/QEMU)
- **Màquina d'auditoria**: `192.168.123.167` (Kali Linux)

### Enumeració detallada de serveis — pfSense (`192.168.120.1`)

- **DNS (53/TCP)**: `dnsmasq 2.91` — versió exposada per fingerprinting (`bind.version`)
- **SSH (2022/TCP)**: `OpenSSH 10.0` — port no estàndard, versió actualitzada
- **VNC (5700–5719/TCP)**: Exposició massiva de ports WebSocket (QEMU VNC) de gestió

### Suite d'automatització

**Auditoria web** (`scripts/audit_web.sh`): Escaneig de vulnerabilitats Nmap, força bruta de directoris Gobuster, preparació SQLi

```bash
nmap -sV --script http-enum,http-vuln*,http-security-headers -p 80,443 $TARGET
gobuster dir -u http://$TARGET -w /usr/share/wordlists/dirb/common.txt
```

**Validador SIEM** (`scripts/test_wazuh.sh`): Genera telemetria d'atac real per verificar Wazuh

```bash
# Simulació de força bruta SSH (activa regles 5710/5720 de Wazuh)
for i in {1..5}; do ssh -o ConnectTimeout=2 invalid_user@$TARGET "exit"; done

# Activació de FIM
echo "test_auditoria_$(date)" >> /tmp/wazuh_fim_test
```

**Cloud i Windows** (`aws_audit.sh`, `audit_windows.sh`): Compliment CIS amb Prowler, enumeració SMB

### Vulnerabilitats trobades

| ID | Vulnerabilitat | Gravetat | Evidència | Mitigació |
|---|---|---|---|---|
| **V-01** | Exposició VNC | 🔴 **CRÍTICA** | `pfsense_completo.txt` | Tancar ports 5700–5800; usar túnel SSH |
| **V-02** | Vulnerabilitats Docker | 🟠 **ALTA** | `02_scan/docker/` | Migrar `nginx:latest` → `nginx:alpine` |
| **V-03** | Filtració DNS | 🔵 **BAIXA** | `pfsense_completo.txt` | Afegir `no-version` a `dnsmasq.conf` |
| **V-04** | MariaDB sense TLS | 🟡 **MITJANA** | Auditoria local | Forçar `require_secure_transport=ON` |

### Full de ruta cap a AWS (Regles d'or)

1. **Enduriment de xarxa**: Replicar les regles estrictes del tallafoc via AWS Security Groups (eliminar exposició VNC)
2. **Seguretat de contenedors**: Implementar Trivy per escanejar imatges Docker abans de desplegar
3. **Auditoria cloud**: Executar Prowler immediatament després del desplegament d'infraestructura

---

## Auditoria AWS — Fase 2 (Producció)

> **Estat**: ✅ Infraestructura auditada (entorn en viu)
> **IP objectiu**: `3.215.30.52` (`ec2-3-215-30-52.compute-1.amazonaws.com`)

### Reconeixement extern

```
PORT    ESTAT  SERVEI   VERSIÓ
22/tcp  obert  ssh      OpenSSH 8.9 (Ubuntu)
80/tcp  obert  http     nginx 1.24.0
443/tcp obert  ssl/http nginx 1.24.0
```

**Millores de seguretat respecte al lab**:
- ✅ **Ports VNC neutralitzats**: Tots els ports WebSocket (5700–5719) ara tancats
- ✅ **SSH endurit**: Autenticació exclusivament per clau; contrasenyes desactivades
- ✅ **Serveis interns aïllats**: S4 MariaDB, S5 Redis, S6 OpenLDAP sense ports publicats

### Enduriment de l'aplicació web

- ✅ `X-XSS-Protection: 1; mode=block`
- ✅ `X-Content-Type-Options: nosniff`
- ✅ `Content-Security-Policy` — implementació bàsica
- ⚠️ **Pendent (Baix)**: `Strict-Transport-Security` (HSTS) pendent de configurar

### Enumeració avançada de directoris

- HTTP 403 global per a `.bash_history`, `.ssh`, `.git` — enduriment consistent
- El servidor implementa una estratègia de Wildcard/Honeytoken (resposta constant de 8849 bytes per a rutes WordPress)

### Validació WAF i injecció SQL

Test:
```bash
curl -i -k "https://3.215.30.52/?id=1'%20OR%20'1'='1"
```
**Resultat**: ✅ Snort (S11) va detectar i registrar el patró SQLi — alerta generada a `/var/log/snort/alert`

### Auditoria de contenedors Docker

| Contenedor | Servei | Port extern | Estat |
|---|---|---|---|
| `s0_firewall` | Tallafoc iptables | — | ✅ Actiu |
| `s1_nginx` | Servidor web | 80, 443 | ✅ Endurit |
| `s2_node` / `s3_node` | PHP-FPM | Intern | ✅ Segur |
| `s4_mariadb` | Base de dades | Intern | ✅ Segur |
| `s5_redis` | Sessions | Intern | ✅ Segur |
| `s6_openldap` | Directori | Intern | ✅ Segur |
| `s7_wazuh` | SIEM | 1514, 55000 | 🟡 Restringit per SG |
| `s8_grafana` | Monitoratge | 3000 | 🟡 Risc de filtració d'info |
| `s10_postfix` | Servidor de correu | 25, 587 | 🔴 Risc de relay obert |
| `s11_snort` | IDS | Intern | ✅ Segur |
| `s12_ollama` | Motor IA | 11434 | 🔴 Risc de segrest de recursos |

> **Nota**: Tot i que Ollama (11434) i Postfix (25, 587) estan mapejats internament a `0.0.0.0`, els **AWS Security Groups** descarten tot el tràfic extern cap a aquests ports — confirmat per timeout de connexió des de la màquina d'auditoria.

**Acció recomanada**: Afegir `GF_SERVER_ROOT_URL` a Grafana i restringir el port 3000 al Security Group per evitar l'accés anònim al dashboard.

### Escaneig complet de ports

- **65.532 ports**: `filtered` (sense resposta)
- **Oberts**: 22 (SSH), 80 (HTTP), 443 (HTTPS)

**Veredicte de l'auditor**: El 99,99% de la superfície d'atac és invisible per a amenaces externes. Els AWS Security Groups implementen correctament la política de "Denegar per defecte".

**Estat final**: ✅ **APROVAT** — El sistema compleix els estàndards de seguretat del sector per a desplegaments cloud de PYME.

### Avaluació final de seguretat

| Mètrica | Lab (IsardVDI) | Producció (AWS) | Estat |
|---|---|---|---|
| **Superfície d'atac** | 🟠 Alta (20+ ports) | 🟢 Baixa (2 ports) | **Millorat** |
| **Gestió de vulnerabilitats** | 🔴 Crítica (desactualitzat) | 🟡 Mitjana (endurit) | **Millorat** |
| **Seguretat web** | 🔴 Deficient (sense capçaleres) | 🟢 Forta (Snort + Nginx) | **Millorat** |
| **Integració SIEM** | ✅ Actiu | ✅ Actiu | **Verificat** |

---

## Compliment normatiu

### Estàndards coberts

| Estàndard | Cobertura | Tipus d'informe |
|---|---|---|
| **RGPD** | Protecció de dades, notificació de bretxes, registre d'accessos | Informe mensual automatitzat via `socmail.php` |
| **NIS2** | Directiva UE de seguretat de xarxes i informació | Monitoratge continu amb Wazuh (S7) |
| **ISO 27001** | Gestió de la seguretat de la informació | Suport a auditoria anual |

> ⚠️ **Nota:** El document original incloïa **PCI-DSS 3.2.1** i **HIPAA**. Aquests estàndards s'han eliminat perquè el projecte actual **no processa pagaments amb targeta** (no hi ha integració de pagament real) ni gestiona dades de salut com a activitat principal.

### Mesures de protecció de dades

- Totes les dades xifrades en repòs (volums Docker amb permisos `600`) i en trànsit (TLS 1.2+ via Nginx)
- Regió AWS: `us-east-1` (Virgínia del Nord) — instància Learner Lab
- Retenció de backups: 30 dies (base de dades), 7 dies (logs)
- Registre d'accés a totes les operacions sensibles (Wazuh FIM)
- Flux automatitzat de detecció i notificació de bretxes via Postfix (S10)

### Hardening aplicat al projecte

```bash
# Permisos del fitxer .env
chmod 600 ~/ProjecteFinal_G7/.env

# Desactivar login SSH per contrasenya
sudo sed -i 's/PasswordAuthentication yes/PasswordAuthentication no/' /etc/ssh/sshd_config
sudo systemctl restart sshd

# Verificar que els serveis interns no publiquen ports
docker inspect s4_mariadb | grep -i "hostport"
# Ha de retornar buit (cap port publicat)

# Comprovar regles iptables de S0
docker exec s0_firewall iptables -L -n -v

# Activar TLS a MariaDB
docker exec s4_mariadb mysql -u root -p -e "SET GLOBAL require_secure_transport=ON;"
```

