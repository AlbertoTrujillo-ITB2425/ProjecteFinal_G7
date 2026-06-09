# 📋 Evolució del Projecte: CyberPyme G7
 
**Projecte:** 2526-PF-ASIXc2-G12-CyberPyme
**Institut:** Institut Tecnològic de Barcelona
**Equip:** Joel Muñoz Llerin · Alberto Trujillo Mingorance · Luka Ukleba
**Exportat:** 09/06/2026
 
---
 
## Índex
 
- [Sprint 1 — Fonaments i Infraestructura](#sprint-1)
- [Sprint 2 — Seguretat, IA i Tancament](#sprint-2)
- [Resum global](#resum-global)
---
 
## Sprint 1
### Fonaments i Infraestructura
**Període:** 13/04/2026 → 04/05/2026
 
L'Sprint 1 va ser el de posada en marxa. L'objectiu era tenir tota la base del projecte funcionant: repositori, entorn de desenvolupament, tots els contenidors Docker definits i les primeres decisions d'arquitectura preses. A més, es va fer un estudi previ del mercat i de les tecnologies que s'utilitzarien.
 
Totes les tasques d'aquest sprint estan **completades al 100%**.
 
---
 
### 1.1 Configuració inicial i repositori
 
Abans de tocar res tècnic, calia tenir el lloc on treballar i les eines preparades.
 
| Tasca | Responsable | Completada |
|-------|-------------|-----------|
| Crear Repositori de GitHub | Luka,Alberto,Joel | 13/04/2026 |
| Configurar docker-compose inicial | Luka,Alberto,Joel | 27/04/2026 |
| Crear scripts de desplegament ràpid en bash | Luka,Alberto,Joel | 20/04/2026 |
 
**Què es va fer:**
- Es va crear el repositori central de GitHub que serviria com a base de tot el codi i la documentació del projecte.
- Es va configurar el `docker-compose.yml` inicial, definint l'estructura bàsica de tots els serveis.
- Es van crear scripts en Bash per poder aixecar tot l'entorn ràpidament, evitant haver de fer passos manuals cada vegada.
---
 
### 1.2 Investigació i anàlisi previ
 
Abans de construir, l'equip va dedicar temps a entendre el mercat i les tecnologies disponibles.
 
| Tasca | Responsable | Completada |
|-------|-------------|-----------|
| Investigació competència en el mercat | Muñoz Llerin Joel | 04/05/2026 |
| Estudi previ de tecnologies disponibles i empreses al mercat | Equip complet | 04/05/2026 |
| Anàlisi de mercat: solucions existents i necessitats | Equip complet | 04/05/2026 |
| Aplicar tecnologies i metodologies explicades a classe | Equip complet | 04/05/2026 |
 
**Què es va fer:**
- Es va investigar la competència: quines solucions de ciberseguretat existien, quins costos tenien i quin impacte econòmic té un atac per a les empreses.
- Es va definir la topologia de xarxa: quines màquines hi haurien, com estarien connectades i quins rols complirien.
- Es van seleccionar les tecnologies del projecte:
  - **Virtualització:** AWS, VirtualBox
  - **Firewall / IDS:** pfSense, Snort
  - **Pentesting:** Kali Linux
  - **Serveis:** Ubuntu Server, Nginx, Postfix
  - **Base de dades:** MariaDB
  - **Orquestació:** Docker, Docker Compose
  - **Monitoratge:** Grafana, Wazuh (SIEM)
  - **Documentació:** Markdown (GitHub)
---
 
### 1.3 Disseny de l'arquitectura
 
Amb les decisions preses, es va dissenyar i documentar l'arquitectura del sistema.
 
| Tasca | Responsable | Completada |
|-------|-------------|-----------|
| Dibuixar esquema de xarxa | Trujillo Mingorance Alberto | 04/05/2026 |
| Dissenyar base de dades (MariaDB) | Trujillo Mingorance Alberto | 04/05/2026 |
| Desplegar imatge MariaDB en Docker | Trujillo Mingorance Alberto | 04/05/2026 |
| Crear màquina Kali Linux | Muñoz Llerin Joel | 14/04/2026 |
 
**Què es va fer:**
- Es va crear l'esquema visual de la xarxa, mostrant com es connecten tots els serveis entre ells.
- Es va dissenyar l'estructura de la base de dades MariaDB i es va desplegar la primera versió al Docker.
- Es va preparar la màquina Kali Linux per a les tasques de pentesting i proves d'intrusió.
---
 
### 1.4 Construcció dels contenidors Docker
 
El nucli tècnic de l'Sprint 1 va ser definir i aixecar tots els serveis com a contenidors Docker. Primer es va crear la plantilla base i després cada servei individualment.
 
| Tasca | Responsable | Completada |
|-------|-------------|-----------|
| Crear plantilla base del projecte | Trujillo Mingorance Alberto | 20/04/2026 |
| S1: Gateway & Load Balancer (Nginx) | Ukleba Luka | 20/04/2026 |
| S2 & S3: Web Nodes (PHP-FPM) | Trujillo Mingorance Alberto | 20/04/2026 |
| S4: Database Persistence (MariaDB) | Trujillo Mingorance Alberto | 20/04/2026 |
| S5: Cache & Session Manager (Redis) | Muñoz Llerin Joel | 20/04/2026 |
| S6: Identity & Directory (LDAP) | Trujillo Mingorance Alberto | 20/04/2026 |
| S7: SIEM (Wazuh) | Trujillo Mingorance Alberto | 20/04/2026 |
| S8: Metrics & Analytics (Grafana) | Trujillo Mingorance Alberto | 20/04/2026 |
| S9: Auditoría & Recon (Dockerfile unificat PHP) | Trujillo Mingorance Alberto | 20/04/2026 |
| S10: Postfix (Dockerfile.s10_s11) | Ukleba Luka | 20/04/2026 |
| S11: Snort (mateix Dockerfile que S10) | Ukleba Luka | 20/04/2026 |
 
**Què es va fer:**
- Es va crear una plantilla Docker unificada que serviria de base per als nodes web i el scanner.
- Es van definir i aixecar els 11 serveis principals (S1–S11), cadascun en el seu propi contenidor:
  - **S1** — Nginx com a gateway i balancejador de càrrega
  - **S2/S3** — Dos nodes web PHP-FPM per alta disponibilitat
  - **S4** — Persistència de dades amb MariaDB
  - **S5** — Caché i gestió de sessions amb Redis
  - **S6** — Directori d'identitat amb OpenLDAP
  - **S7** — SIEM amb Wazuh per a detecció d'amenaces
  - **S8** — Mètriques i analítiques amb Grafana
  - **S9** — Escàner d'auditoria i reconeixement
  - **S10/S11** — Postfix i Snort compartint Dockerfile
---
 
### 1.5 Documentació de l'Sprint 1
 
| Tasca | Responsable | Completada |
|-------|-------------|-----------|
| Documentació: Markdown (GitHub) | Equip complet | 27/04/2026 |
 
**Què es va fer:**
- Es va redactar la documentació inicial del projecte en format Markdown i es va pujar al repositori de GitHub, recollint l'arquitectura, les decisions preses i els passos de desplegament.
---
 
## Sprint 2
### Seguretat, Intel·ligència Artificial i Tancament
**Període:** 27/04/2026 → 09/06/2026
 
L'Sprint 2 va ser el de completar i afinar el sistema. Amb la infraestructura base funcionant, l'objectiu era afegir les capes de seguretat avançada (firewall S0, Snort, Postfix), integrar la IA, corregir errors i preparar el tancament del projecte (presentació i documentació final).
 
Totes les tasques d'aquest sprint estan **completades al 100%**.
 
---
 
### 2.1 Recuperació i estabilització de l'entorn
 
Abans de continuar, l'equip va haver de fer front a una incidència: la caiguda de l'entorn AWS.
 
| Tasca | Responsable | Completada |
|-------|-------------|-----------|
| Replicar aplicació en AWS | Trujillo Mingorance Alberto | 09/06/2026 |
| Levantar totes les màquines de nou | Equip complet | 11/05/2026 |
| Comprovació de funcionament de cada servei dels Dockers | Muñoz Llerin Joel | 11/05/2026 |
 
**Què es va fer:**
- La instància AWS va caure i va obligar l'equip a recrear tots els fitxers de configuració i tornar a desplegar tots els contenidors Docker. Tot i ser una tasca tediosa, es va completar en poc temps gràcies als scripts creats a l'Sprint 1.
- Es va replicar l'aplicació completa a AWS i es va verificar que cada servei Docker funcionava correctament de forma individual.
---
 
### 2.2 Seguretat avançada: Firewall S0, Snort i Postfix
 
Amb l'entorn estable, es va afegir la capa de seguretat perimetral que faltava.
 
| Tasca | Responsable | Completada |
|-------|-------------|-----------|
| Crear la S0 que farà de firewall | Ukleba Luka | 11/05/2026 |
| Crear manual d'accés al Docker | Ukleba Luka | 11/05/2026 |
| Montar Firewall | Ukleba Luka | 11/05/2026 |
| Crear PHP de Snort i Postfix | Ukleba Luka | 05/05/2026 |
| Implementar el PHP de Snort i Postfix | Trujillo Mingorance Alberto | 11/05/2026 |
 
**Què es va fer:**
- Es va decidir **no usar pfSense** perquè no es pot conteneritzar amb Docker. En el seu lloc, es va crear el servei **S0** basat en `iptables` + NAT com a firewall perimetral.
- Es va crear i implementar el codi PHP que permet visualitzar a la interfície web les alertes generades per Snort i els correus de Postfix, mostrant-los en temps real al tauler SOC (`socmail.php`).
- Es va redactar el manual d'accés al Docker per facilitar el manteniment futur.
---
 
### 2.3 Integració de la Intel·ligència Artificial
 
| Tasca | Responsable | Completada |
|-------|-------------|-----------|
| Integració model gemma.2b a l'aplicació | Trujillo Mingorance Alberto | 11/05/2026 |
 
**Què es va fer:**
- Es va integrar el model d'IA **Gemma 2B** (posteriorment substituït per Qwen/Llama via Ollama) directament a l'aplicació web, permetent als usuaris fer preguntes sobre seguretat i rebre respostes generades localment sense dependre de serveis externs de pagament.
---
 
### 2.4 Correccions i millores
 
| Tasca | Responsable | Completada |
|-------|-------------|-----------|
| Arreglar el scanner de la pàgina web | Trujillo Mingorance Alberto | 11/05/2026 |
| Canviar disseny de l'esquema de xarxa | Muñoz Llerin Joel | 11/05/2026 |
 
**Què es va fer:**
- El scanner de xarxa integrat a la web no funcionava correctament i es va revisar i corregir.
- Es va actualitzar l'esquema de xarxa per reflectir les noves màquines afegides durant l'Sprint 2 (S0, S12), deixant-lo visualment clar i net.
---
 
### 2.5 Recopilació i scripting
 
| Tasca | Responsable | Completada |
|-------|-------------|-----------|
| Recopilació d'informació per a la realització del projecte | Equip complet | 09/06/2026 |
 
**Què es va fer:**
- Es van documentar tots els comandos que s'havien anat usant durant el projecte, creant una col·lecció de scripts operatius. L'objectiu era tenir un "salvavides": si en qualsevol moment caigués una màquina o calgués reconstruir l'entorn, tots els comandos necessaris estarien documentats i provats.
---
 
### 2.6 Validació i execució final
 
| Tasca | Responsable | Completada |
|-------|-------------|-----------|
| Definir i executar un joc de proves per validar la solució | Equip complet | 09/06/2026 |
| Executar el projecte | Equip complet | 09/06/2026 |
 
**Què es va fer:**
- Es va definir un conjunt de proves per validar que tot el sistema funcionava correctament de forma integrada: proves de connectivitat, proves d'alertes de Snort, proves del chatbot d'IA, proves del scanner i proves d'autenticació LDAP.
- Es va fer l'execució final del projecte amb tota la pila activa i verificada.
---
 
### 2.7 Documentació i presentació final
 
| Tasca | Responsable | Completada |
|-------|-------------|-----------|
| Documentació: Markdown (GitHub) | Equip complet | 09/06/2026 |
| Crear la presentació del projecte (Prezi) | Equip complet | 09/06/2026 |
 
**Què es va fer:**
- Es va completar i tancar tota la documentació tècnica del projecte al repositori de GitHub en format Markdown.
- Es va crear la presentació final del projecte a **Prezi**, recollint l'arquitectura, les decisions preses, les dificultats trobades i els resultats obtinguts.
---
 
## Resum Global
 
### Estadístiques generals
 
| | Sprint 1 | Sprint 2 | Total |
|--|:--------:|:--------:|:-----:|
| **Tasques completades** | 23 | 16 | **39** |
| **Tasques pendents** | 0 | 0 | **0** |
| **Percentatge completat** | 100% | 100% | **100%** |
| **Inici** | 13/04/2026 | 27/04/2026 | 13/04/2026 |
| **Fi** | 04/05/2026 | 09/06/2026 | 09/06/2026 |
 
### Participació per membre
 
| Membre | Sprint 1 | Sprint 2 |
|--------|----------|----------|
| **Trujillo Mingorance Alberto** | S2/S3, S4, S6, S7, S8, S9, BD, esquema, docker-compose, GitHub, scripts | Replicar AWS, PHP Snort/Postfix, integració IA, scanner |
| **Ukleba Luka** | S1, S10, S11, coordinació general | S0 Firewall, PHP Snort/Postfix, manual Docker, coordinació |
| **Muñoz Llerin Joel** | S5, Kali Linux, investigació mercat | Esquema de xarxa, comprovació serveis, proves |
 
### Serveis desplegats al final del projecte
 
| Servei | Nom | Tecnologia | Sprint |
|--------|-----|------------|--------|
| S0 | Firewall perimetral | iptables + NAT | Sprint 2 |
| S1 | Gateway & Load Balancer | Nginx | Sprint 1 |
| S2/S3 | Web Nodes | PHP-FPM | Sprint 1 |
| S4 | Base de dades | MariaDB | Sprint 1 |
| S5 | Caché i sessions | Redis | Sprint 1 |
| S6 | Directori d'identitat | OpenLDAP | Sprint 1 |
| S7 | SIEM | Wazuh | Sprint 1 |
| S8 | Mètriques | Grafana | Sprint 1 |
| S9 | Scanner d'auditoria | Nmap + PHP | Sprint 1 |
| S10 | Correu d'alertes | Postfix | Sprint 1 |
| S11 | Detector d'intrusos | Snort | Sprint 1 |
| S12 | Motor d'IA | Ollama (Gemma/Qwen) | Sprint 2 |
 
### Principals decisions tècniques preses
 
- **pfSense descartada** → substituïda per un contenidor S0 propi basat en `iptables`, ja que pfSense no és conteneritzable amb Docker.
- **Dockerfile unificat** → S10 (Postfix) i S11 (Snort) comparteixen el mateix `Dockerfile.s10_s11` per reduir redundància.
- **IA local** → Es va optar per un model d'IA que s'executa al propi servidor (Ollama), evitant dependre de serveis externs i mantenint la privacitat de les dades.
- **Scripts de recuperació** → Davant la caiguda d'AWS a l'Sprint 2, es va prioritzar documentar tots els comandos operatius per poder reconstruir l'entorn en el mínim temps possible.
---
