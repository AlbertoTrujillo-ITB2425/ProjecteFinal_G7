# 🛡️ CyberPyme — Plataforma d'Auditoria de Seguretat per a PIMES

> **Projecte Final de Grau Superior — Administració de Sistemes Informàtics en Xarxa (ASIX)**  
> **Institut Tecnològic de Barcelona (ITB) — Curs Acadèmic 2025/2026**

---

## 📝 Resum Executiu

**CyberPyme** és una aplicació web i infraestructura SaaS dissenyada perquè les Petites i Mitjanes Empreses (PIMES) puguin auditar, monitoritzar i gestionar els seus riscos informàtics sense necessitat de comptar amb un departament tècnic avançat ni suportar els alts costos d'una consultoria externa.

L'aplicació permet als usuaris donar d'alta els seus dominis i realitzar comprovacions automatitzades sobre la seva superfície d'exposició a Internet:
*   **Escaneig de ports:** Identificació de ports oberts de forma innecessària o desactualitzats.
*   **Auditoria de Certificats:** Verificació de l'estat, caducitat i nivell de xifratge dels seus certificats SSL/TLS.
*   **Anàlisi Forense amb IA:** Processament intel·ligent dels logs d'escaneig per generar un informe de vulnerabilitats clar i solucions tècniques llestes per aplicar.

---

## ✨ Característiques Tècniques Principals

*   🔍 **Auditoria de Xarxa Externa:** Detecció automàtica de serveis exposats i vulnerabilitats perimetrals.
*   🔥 **Generació d'Informes per IA:** Integració interna (Groq/Llama 3.3) que tradueix logs complexos de seguretat en plans de mitigació tècnica llegibles.
*   🛡️ **Protecció Perimetral Activa:** Mitigació d'atacs web comuns (OWASP Top 10, injeccions de codi, XSS) i mitigació de DDoS mitjançant **Cloudflare** i **IPS Snort 3**.
*   📊 **Monitorització d'Esdeveniments (SIEM):** Centralització i anàlisi de logs mitjançant **Wazuh** i quadres de comandament visuals en **Grafana**.
*   🔐 **Autenticació Centralitzada:** Gestió d'accessos i control d'usuaris mitjançant el directori corporatiu **OpenLDAP** sota canals xifrats.

---

## 🔒 Control d'Auditoria i Seguretat Legal

> ⚠️ **Nota sobre l'ús d'Nmap i Escanejos de Xarxa:**  
> Llançar escanejos de ports de manera automatitzada o indiscriminada contra qualsevol domini a Internet és una pràctica de risc que activa de forma immediata les alarmes dels sistemes de detecció d'intrusions (IDS/IPS) perimetrals i pot provocar el bloqueig o llistat negre (*blacklisting*) de la IP del nostre servidor per part dels proveïdors de xarxa.
>
> Per aquest motiu, **CyberPyme** s'ha dissenyat sota un estricte principi de control: l'aplicació obliga l'usuari a registrar i configurar manualment les propietats del seu propi domini i dels hosts concrets que es volen analitzar. D'aquesta manera es garanteix un entorn auditat controlat, evitant falses alarmes d'atacs a la xarxa i assegurant que l'activitat s'executa exclusivament sobre la infraestructura autoritzada pel client.

---

## 🛠️ Stack Tecnològic

| Capa | Tecnologies Utilitzades |
| :--- | :--- |
| **Frontend i Backend** | PHP 8.2-FPM, HTML5, CSS3, JavaScript |
| **Servidors i Proxy** | Nginx 1.24, Cloudflare (WAF/CDN), Snort 3 (IDS/IPS) |
| **Base de Dades i Cache** | MariaDB 10.11, Redis 7.2 |
| **Seguretat i Monitorització** | Wazuh SIEM, Grafana, OpenLDAP |
| **Infraestructura i CI/CD** | Docker, Docker Compose, AWS (EC2, S3), GitHub Actions |

---

## 💡 Per què fem servir aquestes tecnologies? (Justificació Tècnica)

*   **Docker i Docker Compose:** Permet l'orquestració i el desplegament de toda la infraestructura multi-contenidor en qüestió de minuts, garantint l'aïllament de serveis i la portabilitat total entre entorns de desenvolupament i producció.
*   **Cloudflare i Snort 3:** S'ha estructurat una defensa en capes sòlida. Cloudflare actua com a primera línia de defensa perimetral al núvol, filtrant el trànsit maliciós (WAF contra OWASP Top 10) i mitigant atacs DDoS abans que arribin al servidor, mentre que Snort 3 inspecciona localment el trànsit de xarxa a nivell de paquet.
*   **Wazuh SIEM i Grafana:** Wazuh centralitza la recol·lecció de logs de seguretat, la detecció de malware i el compliment normatiu. Grafana es connecta directament a l'origen de les dades per oferir panells visuals intuïtius de l'estat del sistema en temps real.
*   **OpenLDAP:** Implementa un directori actiu centralitzat per a l'autenticació i gestió d'usuaris de forma segura mitjançant consultes xifrades com TLS.
*   **Redis:** S'utilitza com a memòria cau (*cache*) d'alta velocitat per a la gestió de sessions i el control de concurrència (*rate-limiting*), evitant la saturació de la base de dades MariaDB.
*   **Integració amb IA (Groq/Llama):** Automatitza la feina d'un analista de seguretat júnior traduint dumps de dades de logs en brut a informes executius i ordres de consola directament aplicables per l'administrador de la PIME.

---

## 🚀 Desplegament i Manual d'Administració

Totes les instruccions detallades per a la instal·lació en local, configuració del fitxer d'entorn `.env`, claus de l'API de Groq, tasques de manteniment i arquitectura del servidor es troben completament documentades a la guia oficial del projecte.

> 📘 **Accés al Manual d'Administrador:**  
> Consulta la [**Guia d'Administració i Desplegament (Manual Admin.md)**](./docs/Manual_Admin.md) per posar en marxa el projecte.

---

## 📁 Estructura del Projecte

```text
ProjecteFinal_G7/
├── docker-compose.yml     # Orquestració de tots els contenidors de l'ecosistema
├── src/                   # Codi font de l'aplicació Web (PHP, API, Frontend)
├── config/                # Fitxers de configuració aïllats (Nginx, PHP, Redis)
├── scripts/               # Scripts d'automatització (còpies de seguretat, auditories, deploys)
├── setup/                 # Esquemes inicials de la base de dades SQL
├── infrastructure/        # Fitxers de desplegament al núvol mitjançant Terraform (AWS)
└── docs/                  # Manuals d'usuari i documentació tècnica
    └── ADMIN.md           # <-- MANUAL DE DESPLEGAMENT COMPLET PER A ADMINISTRADORS

```

---

## 👥 Equip de Treball (Grup 12)

* **Alberto Trujillo** — [GitHub](https://github.com/AlbertoTrujillo-ITB2425)
* **Joel Muñoz** — [GitHub](https://github.com/JoelMunoz-ITB2425)
* **Luka Ukleba** — [GitHub](https://github.com/LukaUkleba-ITB2425)

---

## 📄 Llicència

Aquest projecte està sota la Llicència MIT. Consulta el fitxer [LICENSE](https://github.com/AlbertoTrujillo-ITB2425/ProjecteFinal_G7/blob/main/LICENSE) per a més detalls.
