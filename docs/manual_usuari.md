# Manual d'Usuari: CyberPyme
 
---
 
## Què és CyberPyme?
 
CyberPyme és una plataforma de ciberseguretat per a empreses. La seva funció és **protegir la teva empresa d'atacs informàtics**, vigilar el que passa a la teva xarxa i avisar-te si alguna cosa va malament.
 
Pensa-ho com el sistema de seguretat d'un edifici: té càmeres (que ho enregistren tot), alarmes (que salten si hi ha intrusos) i un guarda de seguretat (que analitza les alertes). CyberPyme fa exactament això, però per a la teva xarxa informàtica.
 
---
 
## Què fa cada part del sistema?
 
El sistema està dividit en "mòduls", cadascun amb una funció concreta. No els has de tocar directament, però és útil saber per a què serveixen:
 
| Nom | Per a què serveix |
|-----|-------------------|
| **Firewall (S0)** | És la "porta d'entrada" de la teva xarxa. Decideix quin trànsit pot entrar i quin s'ha de bloquejar. Com el porter d'un club. |
| **Nginx (S1)** | És el "recepcionista" web. Rep les visites d'internet i les dirigeix al lloc correcte de la plataforma. |
| **Nodes web (S2 i S3)** | Són els que processen i mostren l'aplicació. N'hi ha dos perquè si un falla, l'altre segueixi funcionant. |
| **Base de dades (S4)** | Aquí es guarden totes les dades: usuaris, registres, configuracions. Com un arxivador digital. |
| **Redis (S5)** | Guarda informació temporal perquè la web vagi més ràpida. Com una memòria d'accés ràpid. |
| **LDAP (S6)** | Gestiona els usuaris i contrasenyes de la plataforma. És el "llibre d'empleats" del sistema. |
| **Wazuh (S7)** | És el SIEM: analitza tot el que passa al sistema i avisa si detecta alguna cosa sospitosa. |
| **Grafana (S8)** | Mostra gràfiques i estadístiques del sistema en temps real. Com el quadre de comandaments d'un cotxe. |
| **Scanner (S9)** | Analitza la teva xarxa buscant vulnerabilitats o dispositius desprotegits. |
| **Postfix (S10)** | És el servidor de correu intern. Envia alertes per email quan passa alguna cosa important. |
| **Snort (S11)** | És el detector d'intrusos (IDS). Vigila el trànsit de xarxa i llança alertes si detecta atacs. |
| **Ollama IA (S12)** | És la intel·ligència artificial integrada. Ajuda a analitzar registres i respondre preguntes de seguretat. |
 
---
 
## Índex
 
| # | Secció |
|---|--------|
| 1 | Com accedir a la plataforma |
| 2 | El portal principal |
| 3 | Tauler SOC: correu i alertes de xarxa (socmail) |
| 4 | Tauler de mètriques: Grafana |
| 5 | L'assistent d'IA (chatbot) |
| 6 | Què fer si alguna cosa no funciona |
| 7 | Preguntes freqüents |
 
---
 
## 1. Com Accedir a la Plataforma
 
Per entrar a CyberPyme només necessites un navegador web (Chrome, Firefox, Edge...) i l'adreça del servidor.
 
### Pas 1 — Obre el navegador i entra a l'adreça
 
```
http://3.215.30.52/auth.php
```
 
Veuràs una pantalla d'inici de sessió.
 
### Pas 2 — Introdueix les teves credencials
 
Escriu el teu **nom d'usuari** i la teva **contrasenya**. Aquestes dades te les haurà donat l'administrador del sistema.
 
> **No tens usuari?** Contacta amb l'administrador perquè et creï un compte. Ell gestiona els accessos des del tauler d'usuaris (LDAP).
 
### Pas 3 — Ja ets dins
 
Un cop identificat, accediràs al portal principal de CyberPyme.
 
---
 
## 2. El Portal Principal
 
El portal és la pantalla d'inici des d'on pots navegar a les diferents seccions del sistema.
 
Des d'aquí tens accés a:
 
- L'**escàner de vulnerabilitats** — per analitzar dispositius de la teva xarxa
- El **tauler SOC** (socmail) — per veure alertes i correus de seguretat
- El **chatbot d'IA** — per fer preguntes sobre els serveis o sobre alertes
### Tancar sessió
 
Quan acabis de treballar, tanca sempre la sessió amb el botó de sortida. No tanquis simplement la pestanya del navegador, perquè la sessió podria quedar-se oberta.
 
---
 
## 3. Tauler SOC: Correu i Alertes de Xarxa (socmail)
 
El **tauler SOC** (Security Operations Center) és on veus en temps real el que està passant a la teva xarxa: correus d'alerta i avisos del detector d'intrusos.
 
### Com accedir
 
Des del portal principal fes clic a **"SOC Mail"**, o entra directament a:
 
```
http://3.215.30.52/socmail.php
```
 
> Has d'haver iniciat sessió abans a `auth.php`. Si veus un error de sessió en entrar, torna a fer login.
 
### Què veuràs en aquesta pantalla
 
La pantalla està dividida en dues parts:
 
**Part esquerra — Correus d'alerta (Postfix)**
 
Aquí apareixen els correus que el sistema envia automàticament quan detecta un esdeveniment de seguretat. Per exemple:
 
- Un intent d'accés fallit repetit
- Un servei que ha deixat de funcionar
- Una alerta generada pel detector d'intrusos
Cada correu indica: la data i hora, el tipus d'alerta i una descripció breu.
 
**Part dreta — Alertes de xarxa (Snort)**
 
Aquí apareixen les alertes del **detector d'intrusos (Snort)**. Snort vigila tot el trànsit que entra i surt de la xarxa, i si detecta alguna cosa sospitosa (com un possible atac), ho registra aquí.
 
Cada alerta indica:
 
- **Data i hora** — quan va ocórrer
- **Tipus d'alerta** — quin tipus d'atac o anomalia s'ha detectat
- **IP d'origen** — des de quina adreça va venir el trànsit sospitós
- **IP de destinació** — a quina part de la teva xarxa anava dirigit
### Què faig si veig una alerta?
 
No totes les alertes són atacs reals. Algunes són "falsos positius" (el sistema s'ha posat nerviós sense motiu). Aquí tens una guia ràpida:
 
| Tipus d'alerta | Què significa | Què fer |
|----------------|---------------|---------|
| `ICMP Echo` / `ping` | Algú ha fet un "ping" al teu servidor | Normal si és trànsit intern. Sospitós si ve de fora. |
| `SQL Injection attempt` | Algú ha intentat atacar la teva base de dades | Notifica l'administrador immediatament. |
| `Port Scan` | Algú està explorant els ports del teu servidor | Pot ser un reconeixement previ a un atac. Avisa l'admin. |
| `Brute Force` | Intents repetits d'endevinar una contrasenya | Avisa l'administrador. Potser cal bloquejar aquella IP. |
 
> **Regla general:** Si veus una alerta que es repeteix moltes vegades des de la mateixa IP en poc temps, avisa l'administrador.
 
---
 
## 4. Tauler de Mètriques: Grafana
 
**Grafana** és el tauler visual on pots veure l'estat del sistema d'un cop d'ull: ús de CPU, memòria, estat dels serveis, etc.
 
### Com accedir
 
```
http://3.215.30.52:3000
```
 
L'administrador et proporcionarà les credencials d'accés a Grafana.
 
### Què pots veure
 
Grafana mostra **dashboards** (taulers visuals amb gràfiques). Els principals són:
 
**Estat general del sistema**
 
Mostra si tots els serveis estan actius (en verd) o si algun ha fallat (en vermell). Si veus alguna cosa en vermell, avisa l'administrador.
 
**Ús de recursos**
 
Gràfiques de:
 
- **CPU** — l'esforç que està fent el servidor. Si està constantment al 90-100%, alguna cosa va malament.
- **Memòria RAM** — quanta memòria està usant el sistema. Si arriba al màxim, el sistema pot anar lent.
- **Disc** — quant espai d'emmagatzematge queda. Si s'omple, el sistema pot deixar de registrar esdeveniments.
**Alertes de seguretat**
 
Un resum visual de les alertes generades per Snort i Wazuh en les últimes hores o dies.
 
### Com llegir les gràfiques
 
No et preocupis si no entens tots els números. El més important és:
 
- **Línies verdes o valors baixos** = tot va bé
- **Pics o valors molt alts** = alguna cosa està consumint més del normal, avisa l'admin
- **Color vermell en qualsevol tauler** = problema, avisa l'admin
---
 
## 5. L'Assistent d'IA (Chatbot)
 
CyberPyme inclou un **assistent d'intel·ligència artificial** que pots consultar directament des de la web. Apareix com una bombolla a la cantonada inferior dreta de la pantalla.
 
### Per a què serveix
 
- Preguntar-li sobre els serveis de seguretat que ofereix l'empresa
- Obtenir explicacions senzilles de conceptes tècnics
- Demanar orientació davant d'una alerta que no entens
### Com usar-lo
 
1. Fes clic a la **bombolla de xat** (icona de missatge) a la cantonada inferior dreta
2. Escriu la teva pregunta al quadre de text
3. Prem **Enter** o el botó d'enviar
4. L'assistent respondrà en uns segons
### Exemples de preguntes que pots fer
 
```
"Què és un atac de força bruta?"
"Què he de fer si veig una alerta de SQL Injection?"
"Com funciona el servei d'antivirus?"
"Explica'm què és el firewall amb paraules simples"
```
 
### Limitacions de l'assistent
 
L'assistent només coneix la informació que l'administrador li ha proporcionat. Si li preguntes alguna cosa que està fora d'aquella documentació, et dirà que no té aquella informació i et donarà un correu de contacte.
 
> **No facis servir el chatbot per compartir contrasenyes o informació confidencial.** És una eina de consulta, no un canal segur de comunicació.
 
---
 
## 6. Què Fer si Alguna Cosa No Funciona
 
Abans de trucar a l'administrador, prova aquests passos bàsics:
 
### La web no carrega
 
1. Comprova que tens connexió a internet (obre una altra web qualsevol)
2. Prova a recarregar la pàgina amb **F5** o **Ctrl+R**
3. Prova en un altre navegador
4. Si continua sense funcionar, avisa l'administrador amb l'adreça que intentaves obrir i el missatge d'error que veus
### No puc iniciar sessió
 
1. Comprova que estàs escrivint bé l'usuari i la contrasenya (les majúscules activades?)
2. Prova a esborrar les galetes del navegador: **Configuració → Privadesa → Esborrar dades de navegació**
3. Si has oblidat la contrasenya, l'administrador la pot restablir
### Veig moltes alertes a socmail de cop
 
No entris en pànic. Pot ser:
 
- Un pic de trànsit normal (per exemple, una actualització automàtica)
- Un fals positiu del sistema de detecció
- O bé, un atac real
En qualsevol cas, **apunta l'hora i el tipus d'alertes que veus** i avisa l'administrador amb aquesta informació.
 
### El chatbot no respon
 
1. Espera uns segons i torna-ho a intentar (pot estar processant)
2. Recarrega la pàgina
3. Si continua sense respondre, avisa l'administrador indicant que "el servei d'IA no respon"
### Grafana no mostra dades o apareix en blanc
 
Pot ser que el sistema de mètriques estigui tardant a carregar. Espera 30 segons i recarrega. Si continua en blanc, avisa l'administrador.
 
---
 
## 7. Preguntes Freqüents
 
**Puc accedir a CyberPyme des de casa?**
 
Sí, sempre que tinguis l'adreça del servidor i les teves credencials. Obre el navegador i ves a l'adreça que t'hagi donat l'administrador.
 
**Cada quant he de revisar el tauler SOC?**
 
Depèn del rol que tinguis a l'empresa. Com a mínim, revisa les alertes un cop al dia. Si la teva empresa té molt trànsit de xarxa, pot ser convenient revisar-ho diverses vegades al dia.
 
**Les alertes de Snort signifiquen que m'han hackejat?**
 
No necessàriament. Snort és molt sensible i genera alertes davant qualsevol trànsit que li sembli inusual. La majoria són falsos positius. Però si veus el mateix tipus d'alerta repetir-se moltes vegades en poc temps, sí que és senyal que alguna cosa mereix atenció.
 
**Qui pot veure les meves dades a la plataforma?**
 
Només els usuaris amb els permisos adequats. L'administrador gestiona qui pot accedir a quines seccions.
 
**Què faig si crec que hi ha una bretxa de seguretat?**
 
1. No apaguis ni reiniciïs cap equip (podries esborrar evidències)
2. Apunta l'hora i el que has vist
3. Avisa immediatament l'administrador
4. Si l'empresa té un protocol d'incidències, segueix-lo
**El sistema funciona encara que tanqui el navegador?**
 
Sí. CyberPyme funciona en un servidor al núvol. Tu només veus la interfície, però el sistema està vigilant contínuament encara que no tinguis el navegador obert.
 
**Puc canviar la meva contrasenya?**
 
Depèn de la configuració de la teva empresa. Consulta amb l'administrador si tens aquesta opció disponible.
 
---
 
## Resum d'Adreces d'Accés
 
| Secció | Adreça web |
|--------|------------|
| Login (inici de sessió) | `http://3.215.30.52/auth.php` |
| Portal principal | `http://3.215.30.52` |
| Tauler SOC (alertes i correu) | `http://3.215.30.52/socmail.php` |
| Grafana (mètriques i gràfiques) | `http://3.215.30.52:3000` |
 
---
 
## Contacte amb l'Administrador
 
Si tens qualsevol problema o dubte que no es resolgui amb aquest manual, contacta amb l'administrador del sistema. Quan ho facis, intenta indicar:
 
- **Què estaves fent** quan va ocórrer el problema
- **Quin missatge d'error** vas veure (una captura de pantalla ajuda molt)
- **A quina hora** va ocórrer
Com més informació donis, més ràpid ho podrà resoldre.
 
---
