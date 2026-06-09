# Integració de l’Assignatura: Seguretat i Alta Disponibilitat (MP0378)

Aquest document detalla la justificació tècnica de la infraestructura del projecte final per al nostre client (**Nestlea**), estructurat segons els Resultats d’Aprenentatge (RA) oficials de l’assignatura de Seguretat i Alta Disponibilitat del Cicle Formatiu ASIXc2.

---

## 🛠️ Matriu d’Aplicació dels Resultats d’Aprenentatge (RA)

### RA1 - Seguretat Física i Lògica
* **Hardening operatiu:** Securització activa dels sistemes base desplegats mitjançant configuracions restrictives de permisos a l’entorn Linux.
* **Prevenció d’atacs de phishing i incidents:** Configuració del servidor de correu corporatiu (**Postfix**) amb registres i directives estrictes per evitar tècniques de *spoofing* i intercepció analitzades a les auditories de classe.
* **Polítiques de contrasenyes:** Gestió estricta de credencials d’accés per a les transferències de dades segures a través del servei **SFTP**.

### RA2 - Criptografia, Certificats Digitals i Bastionament Actiu
* **Xifrat d’extrem a extrem:** Implementació de criptografia asimètrica per a l’autenticació SSH (eliminant els accessos per contrasenya) i gestió de certificats digitals SSL/TLS per al trànsit HTTPS del web de comandes.
* **Protecció activa amb Fail2ban:** Monitoratge dels logs i bloqueig automatitzat d’adreces IP malicioses que intentin intrusions o força bruta.
* **Auditoria externa (API Shodan):** Escanejos programats per garantir que l’entorn perimetral no exposi ports vulnerables de manera pública.

### RA3 - VPNs (Connexions Remotes Segures)
* **Aïllament de l’entorn de gestió:** Creació d’un canal xifrat perimetral (VPN) dedicat. Tots els serveis crítics interns (administració de contenidors **Docker**, la base de dades **MariaDB** i el directori **OpenLDAP**) requereixen connexió obligatòria a la VPN, i queden invisibles des d’Internet pública.

### RA4 - Implantació de Tallafocs (Firewalls i IDS/IPS)
* **Tallafoc perimetral (pfSense):** Segmentació completa de la infraestructura virtualitzada dividint el trànsit en zones de confiança: LAN per als serveis interns de dades i DMZ per al servidor web exposat.
* **Sistema de detecció d’intrusions (Snort):** Configuració de **Snort** per inspeccionar paquets de xarxa en temps real. Com a evidència al repositori, les alertes generades per aquest servei es centralitzen directament a la nostra carpeta `/snort_logs`.

### RA5 - Implantació de Servidors Proxy Intermediaris
* **Proxy invers amb Nginx:** Configuració de **Nginx** actuant com a proxy invers perimetral. S’encarrega de mitigar atacs directes a l’aplicació web, ocultar l’arquitectura dels contenidors interns i centralitzar la terminació criptogràfica SSL/TLS.
* **Control de navegació sortint:** Implementació de proxies intermediaris (com ara Privoxy o CCProxy) per auditar i filtrar el contingut del trànsit sortint dels usuaris interns de la PIME.

### RA6 - Alta Disponibilitat
* **Orquestració i redundància:** Desplegament de l’aplicació de comandes mitjançant contenidors **Docker** en entorns locals (`Isard` / `VirtualBox`) i replicació escalable a la infraestructura cloud d’**AWS**.
* **Automatització de còpies de seguretat:** Scripts en Bash (`/scripts`) automatitzats per realitzar còpies de seguretat xifrades de la base de dades **MariaDB** i de les dades de **SFTP**, pujant-se periòdicament a emmagatzematges externs al núvol per garantir la continuïtat del negoci davant de desastres.

### RA7 - Legislació i Normativa sobre Seguretat i Protecció de Dades
* **Compliment del RGPD:** Aplicació de tècniques de xifrat en repòs per a les dades personals i els registres de transaccions emmagatzemats a **MariaDB**.
* **Centralització d’auditoria (SIEM / SOC):** Retenció i anàlisi centralitzada dels logs de la infraestructura. Tota la telemetria es processa per a auditories legals i de compliment tècnic, complementant el desplegament del SOC documentat a `docs/despliegue-soc.md`.
