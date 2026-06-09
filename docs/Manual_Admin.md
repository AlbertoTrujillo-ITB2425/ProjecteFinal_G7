# Manual d'Administració Complet: CyberPyme Enterprise SOC
 
**Versió:** 7.7.0 | **Infraestructura:** AWS EC2 t3.large | **IP Producció:** `3.215.30.52`
 
---
 
## Índex
 
| # | Apartat | Descripció |
|---|---------|------------|
| 1 | **Arquitectura de Microserveis** | Taula de serveis S0–S12 i topologia de xarxa |
| 2 | **Requisits Previs i Accés SSH** | Prerequisits, clau `vockey.pem` i connexió al servidor |
| 3 | **Desplegament Inicial de la Plataforma** | Scripts automatitzats, docker-compose i HTTPS |
| 4 | **Desplegament del SOC** | S0 Firewall, Snort, Postfix i interfície `socmail.php` |
| 5 | **Desplegament del Motor d'IA (Chatbot)** | Chatbot Qwen/Llama, servidor Node.js i widget web |
| 6 | **Gestió de la Consola i Servei** | Comandes de manteniment i comandes PROHIBIDES |
| 7 | **Verificació d'Accés** | URLs del portal, Grafana, Ollama i socmail |
| 8 | **Comprovació Individual de Contenedors** | Diagnòstic per servei: Postfix, Snort i resta |
| 9 | **Configuració VPC Peering entre Comptes AWS** | Connexió entre dos comptes AWS pas a pas |
| 10 | **Gestió de Credencials i Secrets** | `.env`, permisos i rotació d'emergència |
| 11 | **Seguretat i Hardening** | Defensa en profunditat, SSH, Nginx, MariaDB, Snort |
| 12 | **Backup i Recuperació de Dades** | Backups manuals, restauració i gestió del disc |
| 13 | **Gestió d'Usuaris OpenLDAP (S6)** | Estructura LDAP i operacions bàsiques |
| 14 | **Gestió de Recursos Ollama IA (S12)** | Límits de CPU/RAM i monitoratge |
| 15 | **Limitacions del Learner Lab d'AWS** | Bones pràctiques i re-desplegament ràpid |
| 16 | **Resum de Riscos Crítics** | Taula dels 8 riscos d'alt nivell amb accions preventives |
 
---
 
## 1. Arquitectura de Microserveis
 
La plataforma s'orquestra mitjançant **Docker Compose** sobre una instància única AWS EC2 (`t3.large`, Ubuntu 22.04 LTS, 2 vCPU / 8 GB RAM):
 
| Mòdul | Contenidor | Funció Principal |
|---|---|---|
| **Firewall** | `s0_firewall` | Firewall perimetral iptables + NAT |
| **Gateway** | `s1_nginx` | Proxy invers i terminació SSL (Ports 80, 443) |
| **Core App** | `s2_node`, `s3_node` | Processament PHP-FPM (alta disponibilitat) |
| **Databases** | `s4_mariadb`, `s5_redis` | Persistència de dades i cau de sessió |
| **Autenticació** | `s6_openldap` | SSO centralitzat (directori `dc=g7,dc=local`) |
| **SIEM** | `s7_wazuh` | Gestió d'esdeveniments de seguretat |
| **Visualització** | `s8_grafana` | Dashboards de mètriques (port 3000) |
| **Audit Scanner** | `s9_scanner` | Motor d'escaneig de xarxa (Nmap + Shodan API) |
| **Correu alertes** | `s10_postfix` | SMTP intern per a notificació d'incidents |
| **IDS** | `s11_snort` | Detecció d'intrusions en temps real |
| **IA** | `s12_ollama` | Motor local d'IA (Llama 3.3) per a anàlisi de logs |
 
### Topologia de xarxa
 
| Xarxa | Serveis | Accés |
|---|---|---|
| **net_public** | S0, S1, S9 | Accés des d'Internet (ports 80/443) |
| **net_private** | S2–S8, S10–S12 | Accés intern entre contenedors únicament |
 
> **Regla crítica:** Els serveis S4 (MariaDB), S5 (Redis) i S6 (OpenLDAP) **no publiquen cap port extern**. Qualsevol modificació d'aquesta configuració suposa un risc crític (R04).
 
---
 
## 2. Requisits Previs i Accés SSH
 
Abans d'iniciar el desplegament, assegureu-vos que el vostre entorn compleix els requisits:
 
| Requisit | Detall | Estat |
|----------|--------|:-----:|
| **Sistema Operatiu** | Ubuntu 22.04 LTS o superior | OK |
| **Accés a Internet** | Connectivitat sortint per descarregar paquets | OK |
| **Tallafoc (Firewall)** | Ports 80/tcp i 443/tcp permesos per al trànsit entrant | OK |
 
### 2.1 — Obtenció de la Clau d'Accés
 
Descarregueu la clau privada (`vockey.pem`) des de l'enllaç segur proporcionat:
 
**https://drive.google.com/file/d/10NKAE9waCh9OtFUaGFFJ-ZP5xLfD7hV5/view?usp=sharing**
 
### 2.2 — Ajust de Permisos de la Clau
 
Per seguretat, restringiu els permisos del fitxer:
 
```
chmod 600 vockey.pem
```
 
### 2.3 — Connexió al Servidor
 
```
ssh -i "vockey.pem" ubuntu@ec2-3-215-30-52.compute-1.amazonaws.com
```
 
O bé, directament per IP:
 
```
ssh ubuntu@3.215.30.52
```
 
---
 
## 3. Desplegament Inicial de la Plataforma
 
### Pas 1 — Preparació del Sistema
 
Si el servidor és nou, executeu el script de configuració. Aquest script fa el següent de forma automàtica:
 
- Valida que el sistema operatiu sigui Ubuntu compatible
- Detecta i instal·la Docker Engine i Docker Compose
- Clona la darrera versió del repositori CyberPyme
- Genera els fitxers de configuració per a producció
- Orquestra i aixeca tota la pila amb Docker Compose
```
curl -sSL https://raw.githubusercontent.com/AlbertoTrujillo-ITB2425/ProjecteFinal_G7/main/scripts/project_setup.sh | sudo bash
```
 
### Pas 2 — Execució del Deploy Automatitzat
 
```
cd ~/ProjecteFinal_G7
sudo bash .deploy.sh
```
 
En finalitzar correctament, es mostrarà el missatge de confirmació:
 
```
OK: La plataforma CyberPyme ha estat desplegada correctament.
   Podeu accedir-hi a través de: http://<LA_VOSTRA_IP_PUBLICA>
```
 
### Pas 3 (Opcional) — Activació de HTTPS
 
**Requisit previ:** Disposar d'un domini que apunti a la IP del servidor (registre DNS tipus A).
 
```
curl -sSL https://raw.githubusercontent.com/AlbertoTrujillo-ITB2425/ProjecteFinal_G7/main/scripts/enable_https.sh | sudo bash
```
 
L'script sol·licitarà interactivament:
 
- El vostre **nom de domini** (ex: `portal.lamevaempresa.com`)
- El vostre **correu electrònic** (per a notificacions de renovació del certificat)
Configurarà Nginx com a reverse proxy, generarà un certificat Let's Encrypt i activarà la renovació automàtica.
 
### Verificació post-desplegament (OBLIGATÒRIA)
 
```
# 1. Verificar que tots els contenedors estan actius
docker ps --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}"
 
# 2. Comprovar que el firewall S0 ha aplicat les regles correctament
docker exec s0_firewall iptables -L -n -v
docker logs s0_firewall
 
# 3. Verificar que MariaDB NO publica ports externs
docker inspect s4_mariadb | grep -i "hostport"
# Ha de retornar buit — si retorna algun port, pareu el servei immediatament
 
# 4. Confirmar que el certificat TLS és valid
docker exec s1_nginx certbot certificates
```
 
---
 
## 4. Desplegament del SOC: S0 Firewall + socmail.php
 
Aquesta secció explica com afegir el servei S0 (Firewall perimetral), actualitzar el `docker-compose.yml` i desplegar la interfície de Mail + Snort (`socmail.php`).
 
### 4.1 — Connectar-se al Servidor
 
Executar des del PC local:
 
```
ssh ubuntu@3.215.30.52
```
 
### 4.2 — Crear els Fitxers del Firewall S0
 
Executar des del servidor AWS (dins de la sessió SSH):
 
```
cd ~/ProjecteFinal_G7
```
 
Crear el `Dockerfile.s0`:
 
```
cat > Dockerfile.s0 << 'EOF'
FROM ubuntu:22.04
ENV DEBIAN_FRONTEND=noninteractive
RUN apt-get update && apt-get install -y \
    iptables iproute2 net-tools nftables \
    iputils-ping curl && rm -rf /var/lib/apt/lists/*
COPY firewall.sh /firewall.sh
RUN chmod +x /firewall.sh
CMD ["/firewall.sh"]
EOF
```
 
Crear el script `firewall.sh`:
 
```
cat > firewall.sh << 'EOF'
#!/bin/bash
echo 1 > /proc/sys/net/ipv4/ip_forward
iptables -t nat -A POSTROUTING -o eth0 -j MASQUERADE
iptables -A FORWARD -i eth1 -o eth0 -j ACCEPT
iptables -A FORWARD -i eth0 -o eth1 -m state --state RELATED,ESTABLISHED -j ACCEPT
echo "S0 Firewall activo"
tail -f /dev/null
EOF
chmod +x firewall.sh
```
 
Crear les carpetes de volums:
 
```
mkdir -p mail_logs snort_logs
```
 
### 4.3 — Canvis Clau al docker-compose.yml
 
Editar el fitxer amb `nano ~/ProjecteFinal_G7/docker-compose.yml`. Els canvis principals respecte al desplegament base són:
 
| Servei | Canvi |
|--------|-------|
| **S0** | Servei nou: firewall perimetral |
| **S1** | `depends_on: s0_firewall` afegit |
| **S2 / S3** | 2 volums `:ro` nous per llegir mail i snort |
| **S10** | Volum extra `./mail_logs:/var/mail` per exposar el buzó |
| **S11** | `-A console` canviat per `-A fast -l /var/log/snort` per escriure el fitxer `alert` |
 
Configuració de S0 al docker-compose:
 
```
s0_firewall:
  build:
    context: .
    dockerfile: Dockerfile.s0
  container_name: s0_firewall
  cap_add:
    - NET_ADMIN
    - NET_RAW
    - SYS_ADMIN
  sysctls:
    - net.ipv4.ip_forward=1
    - net.ipv4.conf.all.forwarding=1
  networks:
    - net_public
    - net_private
  restart: always
```
 
### 4.4 — Pujar socmail.php
 
**Opció A — Des del PC local (recomanat):**
 
```
scp /ruta/local/socmail.php ubuntu@3.215.30.52:~/ProjecteFinal_G7/g7_src/socmail.php
```
 
Windows PowerShell:
 
```
scp C:\Users\TuUsuario\Downloads\socmail.php ubuntu@3.215.30.52:~/ProjecteFinal_G7/g7_src/socmail.php
```
 
**Opció B — Directament al servidor:**
 
```
nano ~/ProjecteFinal_G7/g7_src/socmail.php
# Enganxa el contingut -> Ctrl+O -> Enter -> Ctrl+X
```
 
### 4.5 — Aixecar els Contenedors
 
```
cd ~/ProjecteFinal_G7
docker compose down
docker compose up -d --build
```
 
### 4.6 — Verificar el Desplegament SOC
 
```
# Tots els contenedors actius
docker ps --format "table {{.Names}}\t{{.Status}}"
 
# S2 veu els fitxers de mail i snort
docker exec s2_node ls -la /var/mail
docker exec s2_node ls -la /var/log/snort
 
# Snort escriu el fitxer alert
docker exec s11_snort ls -la /var/log/snort/
 
# S0 firewall actiu
docker logs s0_firewall
```
 
Resultat esperat:
 
```
s0_firewall    Up X seconds
s1_nginx       Up X seconds
s2_node        Up X seconds
s3_node        Up X seconds
s10_postfix    Up X seconds
s11_snort      Up X seconds
 
# /var/mail      -> fitxer del buzo de Postfix
# /var/log/snort -> fitxer alert de Snort
# logs s0        -> "S0 Firewall activo"
```
 
### 4.7 — Fitxers Involucrats
 
| Fitxer | Ubicació al servidor | Acció |
|--------|----------------------|-------|
| `Dockerfile.s0` | `~/ProjecteFinal_G7/` | Nou |
| `firewall.sh` | `~/ProjecteFinal_G7/` | Nou |
| `docker-compose.yml` | `~/ProjecteFinal_G7/` | Modificat |
| `socmail.php` | `~/ProjecteFinal_G7/g7_src/` | Nou |
| `mail_logs/` | `~/ProjecteFinal_G7/` | Carpeta nova |
| `snort_logs/` | `~/ProjecteFinal_G7/` | Carpeta nova |
 
### 4.8 — Obrir la Web SOC
 
Des del navegador del PC local:
 
```
# Login
http://3.215.30.52/auth.php
 
# Interficie Mail + Snort
http://3.215.30.52/socmail.php
```
 
> **Nota:** Si `socmail.php` mostra error de sessió, assegura't que `auth.php` i `socmail.php` comparteixen el mateix `session_start()` i estan al mateix domini/contenedor PHP.
 
---
 
## 5. Desplegament del Motor d'IA (Chatbot)
 
El sistema inclou un chatbot basat en **Qwen (Alibaba)** que respon preguntes sobre els serveis de seguretat en llenguatge senzill. El chatbot apareix com una bombolla a la cantonada de la web i utilitza únicament la documentació que se li proporciona.
 
### Flux de funcionament
 
```
L'usuari escriu una pregunta a la web
         |
El widget (JavaScript) la mana al servidor
         |
El servidor consulta a Qwen (IA d'Alibaba)
         |
Qwen respon usant els documents proporcionats
         |
La resposta apareix al chat de la web
```
 
### 5.1 — Requisits Previs
 
- **Node.js** instal·lat → verificar: `node --version`
- Compte a **https://modelstudio.console.alibabacloud.com** (Qwen)
- Documents de servei en format `.txt` a la carpeta `knowledge-base/`
### 5.2 — Obtenir Clau API de Qwen
 
1. Registra't a https://modelstudio.console.alibabacloud.com (elegir la regió **Singapore** — la més ràpida des d'Espanya)
2. Al registrar-se s'obtenen **1 milió de tokens gratuïts** sense targeta de crèdit
3. Perfil (dalt a la dreta) → **API Keys** → **Create API Key** → nom: `cyberpyme-chatbot`
4. Copia la clau: `sk-xxxxxxxxxxxxxxxxxxxxxxxx`
> **IMPORTANT:** Guarda la clau immediatament. Només es mostra una vegada. Si es perd, cal crear-ne una de nova.
 
### 5.3 — Preparar els Documents de Coneixement
 
El chatbot respon únicament amb la informació que se li proporciona. Cal convertir la documentació a `.txt` i col·locar-la a la carpeta `knowledge-base/`.
 
**Conversió de PDF a text (Linux/Mac):**
 
```
sudo apt install poppler-utils
pdftotext el_meu_document.pdf el_meu_document.txt
```
 
**Conversió en Windows:** Obre el PDF, selecciona tot el text (Ctrl+A), copia'l i enganxa'l al Bloc de notas. Desa'l com `.txt`.
 
Estructura recomanada per als documents:
 
```
knowledge-base/
├── serveis.txt
├── preguntes_frequents.txt
└── contractacio.txt
```
 
Format recomanat dins de cada document:
 
```
SERVEI D'ANTIVIRUS EMPRESARIAL
 
Que inclou:
El nostre servei d'antivirus protegeix tots els ordinadors de l'empresa.
Inclou actualitzacions automatiques i suport 24 hores.
 
Com s'activa:
1. T'enviem un email amb l'enllaç d'instal·lació
2. Fas doble clic a l'arxiu descarregat
3. Segueixes els 4 passos de l'instal·lador
 
Preu:
Des de 9,99 euros al mes per ordinador.
```
 
### 5.4 — Crear l'Estructura del Projecte
 
```
mkdir chatbot-seguridad && cd chatbot-seguridad
mkdir knowledge-base
npm init -y
npm install express cors openai dotenv
```
 
### 5.5 — Crear el Fitxer .env
 
Crea un fitxer anomenat **`.env`** (comença amb un punt):
 
```
QWEN_API_KEY=sk-xxxxxxxxxxxxxxxxxxxxxxxx
PORT=3000
```
 
> **Seguretat:** Aquest fitxer és secret. Afegeix `.env` al `.gitignore` perquè mai es pugi a GitHub.
 
### 5.6 — Crear el Servidor server.js
 
Crea el fitxer `server.js` amb el següent contingut. Substitueix `[NOM DE LA TEVA EMPRESA]` i el correu de contacte:
 
```
const express = require('express');
const cors = require('cors');
const OpenAI = require('openai');
const fs = require('fs');
const path = require('path');
 
const app = express();
app.use(cors());
app.use(express.json());
 
const clienteQwen = new OpenAI({
  apiKey: process.env.QWEN_API_KEY,
  baseURL: 'https://dashscope-intl.aliyuncs.com/compatible-mode/v1'
});
 
function leerDocumentos() {
  const carpeta = './knowledge-base';
  let todo = '';
  const archivos = fs.readdirSync(carpeta).filter(f => f.endsWith('.txt'));
  for (const archivo of archivos) {
    const contenido = fs.readFileSync(path.join(carpeta, archivo), 'utf8');
    todo += '\n\n=== ' + archivo + ' ===\n' + contenido;
  }
  return todo;
}
 
const documentos = leerDocumentos();
 
const INSTRUCCIONES = `Ets l'assistent virtual de [NOM DE LA TEVA EMPRESA].
La teva feina es ajudar als clients explicant els nostres serveis de seguretat.
 
REGLES:
- Respon NOMES amb informacio dels documents que t'adjunto
- Si no saps la resposta, di: "No tinc aquesta informacio. Contacta'ns a suport@empresa.com"
- Explica les coses de forma senzilla
- Si hi ha passos a seguir, posa'ls numerats
- Respon en el mateix idioma que el client
 
INFORMACIO DE L'EMPRESA:
${documentos}`;
 
app.post('/api/chat', async (req, res) => {
  const { mensaje, historial = [] } = req.body;
  if (!mensaje || mensaje.trim() === '') {
    return res.status(400).json({ error: 'El missatge esta buit' });
  }
  try {
    const conversacion = [
      { role: 'system', content: INSTRUCCIONES },
      ...historial.slice(-6),
      { role: 'user', content: mensaje }
    ];
    const respuesta = await clienteQwen.chat.completions.create({
      model: 'qwen-plus',
      messages: conversacion,
      temperature: 0.2,
      max_tokens: 800
    });
    res.json({ respuesta: respuesta.choices[0].message.content });
  } catch (error) {
    console.error('Error:', error.message);
    res.status(500).json({ error: 'Error al servidor' });
  }
});
 
const PUERTO = process.env.PORT || 3000;
app.listen(PUERTO, () => {
  console.log('Servidor del chatbot funcionant al port ' + PUERTO);
});
```
 
### 5.7 — Arrencar i Provar el Servidor
 
```
node -r dotenv/config server.js
```
 
Resultat esperat: `Servidor del chatbot funcionant al port 3000`
 
Per provar que respon, en una altra terminal:
 
```
curl -X POST http://localhost:3000/api/chat \
  -H "Content-Type: application/json" \
  -d '{"mensaje": "Hola, quins serveis oferiu?"}'
```
 
Ha de retornar una resposta JSON del bot.
 
### 5.8 — Integrar el Widget a la Web
 
Afegeix al fitxer HTML principal just abans de `</body>`:
 
```
<script src="chatbot-widget.js"></script>
```
 
**Per a WordPress:** Instal·la el plugin "Insert Headers and Footers" i afegeix l'script a la secció Footer:
 
```
<script src="https://EL-TEU-DOMINI.com/chatbot-widget.js"></script>
```
 
### 5.9 — Desplegar el Servidor a Internet (Render.com)
 
Render.com ofereix un pla gratuït adequat per a aquest servei:
 
1. Puja el projecte a **GitHub** (sense el `.env`)
2. Crea compte a https://render.com i selecciona **New Web Service**
3. Connecta el repositori de GitHub
4. Configura:
   - **Build Command:** `npm install`
   - **Start Command:** `node server.js`
5. A **Environment Variables** afegeix `QWEN_API_KEY` amb el valor de la teva clau
6. Render et donarà una URL: `https://chatbot-seguridad.onrender.com`
7. Actualitza `chatbot-widget.js` canviant `localhost:3000` per la nova URL
### 5.10 — Estructura Final del Projecte Chatbot
 
```
chatbot-seguridad/
├── server.js              <- El servidor principal
├── chatbot-widget.js      <- El widget per a la web
├── .env                   <- Clau API (NO pujar a GitHub)
├── .gitignore             <- Conte: .env i node_modules
├── package.json           <- Es crea sol amb npm init
├── node_modules/          <- Es crea sol amb npm install
└── knowledge-base/
    ├── serveis.txt
    ├── instal·lacio.txt
    └── faq.txt
```
 
### 5.11 — Resolució de Problemes Habituals
 
| Problema | Causa | Solució |
|----------|-------|---------|
| La bombolla no apareix | L'script no es carrega | Comprova que la ruta al `.js` és correcta |
| El bot no respon | El servidor no està arrencat | Executa `node -r dotenv/config server.js` |
| Error "401 Unauthorized" | La clau API és incorrecta | Comprova el `.env`, no ha de tenir espais |
| El bot respon en anglès | Idioma no configurat | Afegeix "Respon sempre en català" a les instruccions |
| El bot s'inventa coses | Documents poc detallats | Afegeix més informació als `.txt` |
| Funciona en local però no a la web | URL del servidor incorrecta | Canvia `localhost` per la URL de Render |
 
### 5.12 — Comprovació Final del Chatbot
 
- Obro la web i veig la bombolla a la cantonada inferior dreta
- Faig clic a la bombolla i s'obre el xat
- Escric una pregunta sobre els serveis i el bot respon correctament
- El bot NO inventa coses que no estan als meus documents
- Si pregunto quelcom fora dels docs, el bot diu que no sap i dona el correu de contacte
- El xat funciona al mòbil
---
 
## 6. Gestió de la Consola i Servei
 
```
# Verificar l'estat de tots els moduls
docker ps --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}"
```
 
### Comandes de Manteniment
 
- **Reiniciar el SOC complet:** `sudo docker compose restart`
- **Actualitzar l'aplicació:** `git pull && sudo bash .deploy.sh`
- **Logs del sistema en viu:** `sudo docker compose logs -f`
- **Accés a la IA (Ollama):** `docker exec -it s12_ollama ollama list`
### Comandes que MAI s'han d'Executar en Producció
 
```
# PROHIBIT: Elimina tots els volums — es perden dades de MariaDB, LDAP i Ollama
docker compose down -v
 
# PROHIBIT sense backup previ:
docker volume rm projectefinal_g7_db_data
```
 
> Abans de qualsevol operació que pugui afectar volums, feu sempre un backup manual (vegeu apartat 12).
 
---
 
## 7. Verificació d'Accés
 
Un cop el sistema estigui actiu, les interfícies estan disponibles a:
 
| Interfície | URL | Notes |
|---|---|---|
| **Portal Principal** | `http://<IP_HOST>` | Gestionat per `s1_nginx` |
| **Grafana** | `http://<IP_HOST>:3000` | Restringir al Security Group d'AWS |
| **Ollama (IA)** | `http://<IP_HOST>:11434` | Blocat per SG extern |
| **SOC Mail + Snort** | `http://<IP_HOST>/socmail.php` | Requereix login previ a `auth.php` |
 
> **Seguretat port 3000 (Grafana):** Afegiu `GF_SERVER_ROOT_URL` i restringiu el port 3000 al Security Group per evitar accés anònim.
 
---
 
## 8. Comprovació Individual de Contenedors
 
### Postfix (S10)
 
```
# Entrar al contenedor
docker exec -it s10_postfix bash
 
# Dins del contenedor:
postfix status                        # Estat del servei
ss -tlnp | grep -E "25|587"          # Ports actius
cat /var/spool/mail/root              # Correus rebuts per root
tail -f /var/log/mail.log             # Logs en temps real
```
 
### Snort (S11)
 
```
# Entrar al contenedor
docker exec -it s11_snort bash
 
# Dins del contenedor:
ps aux | grep snort                   # Verificar proces actiu
snort -T -c /etc/snort/snort.conf    # Validar configuracio
ls /var/log/snort/                    # Veure fitxers de log
tail -f /var/log/snort/alert          # Alertes en temps real
```
 
### Des del Host AWS (sense entrar al contenedor)
 
```
docker logs -f s11_snort              # Snort en temps real
docker logs s10_postfix               # Logs de Postfix
docker logs --tail 50 s10_postfix     # Ultimes 50 linies de Postfix
```
 
### Verificació Ràpida de Tots els Serveis
 
```
# Estat general
docker ps --format "table {{.Names}}\t{{.Status}}"
 
# Recursos en temps real
docker stats --no-stream
 
# Logs combinats amb filtre d'errors
docker compose logs --tail 20 | grep -i error
```
 
---
 
## 9. Configuració VPC Peering entre Comptes AWS
 
Guia per connectar dos comptes AWS separats (per exemple, Compte A amb OPNsense i Compte B amb el servidor principal del projecte).
 
**Informació prèvia necessària de cada compte:**
 
- **Account ID** → dalt a la dreta de la consola AWS
- **VPC ID** → VPC → Your VPCs
### Pas 1 — Crear les VPCs (cada compte a la seva consola)
 
**Compte A (OPNsense):**
 
```
VPC -> Your VPCs -> Create VPC
Resources: VPC only
Name:      vpc-opnsense
IPv4 CIDR: 10.1.0.0/16
```
 
**Compte B (Projecte):**
 
```
VPC -> Your VPCs -> Create VPC
Resources: VPC only
Name:      vpc-proyecto
IPv4 CIDR: 10.2.0.0/16
```
 
### Pas 2 — Crear les Subnets
 
**Compte A:**
 
```
VPC -> Subnets -> Create Subnet
VPC:   vpc-opnsense | Name: subnet-opnsense | AZ: us-east-1a | CIDR: 10.1.1.0/24
```
 
**Compte B:**
 
```
VPC -> Subnets -> Create Subnet
VPC:   vpc-proyecto | Name: subnet-proyecto | AZ: us-east-1a | CIDR: 10.2.1.0/24
```
 
### Pas 3 — Crear i Adjuntar Internet Gateways
 
**Compte A:**
 
```
VPC -> Internet Gateways -> Create -> Name: igw-opnsense -> Create
Actions -> Attach to VPC -> vpc-opnsense
```
 
**Compte B:**
 
```
VPC -> Internet Gateways -> Create -> Name: igw-proyecto -> Create
Actions -> Attach to VPC -> vpc-proyecto
```
 
### Pas 4 — Crear les Route Tables
 
**Compte A:**
 
```
VPC -> Route Tables -> Create route table
Name: rt-opnsense | VPC: vpc-opnsense
 
Routes -> Edit routes -> Add route:
  Destination: 0.0.0.0/0 | Target: igw-opnsense
 
Subnet associations -> Edit -> marcar subnet-opnsense -> Save
```
 
**Compte B:** Mateix procés amb `rt-proyecto`, `igw-proyecto` i `subnet-proyecto`.
 
### Pas 5 — Crear el VPC Peering
 
**Des del Compte A (inicia la sol·licitud):**
 
```
VPC -> Peering Connections -> Create Peering Connection
Name:      peering-g7
Local VPC: vpc-opnsense
Account:   Another account -> introduir l'Account ID del Compte B
Region:    us-east-1
VPC ID:    introduir el VPC ID del Compte B (vpc-proyecto)
```
 
Creat → queda en estat **Pending Acceptance**
 
**Des del Compte B (accepta la sol·licitud):**
 
```
VPC -> Peering Connections
Seleccionar la sol·licitud en estat Pending
Actions -> Accept Request -> Confirmar
```
 
### Pas 6 — Afegir les Rutes del Peering (els dos comptes)
 
**Compte A:**
 
```
Route Tables -> rt-opnsense -> Routes -> Edit routes -> Add route:
Destination: 10.2.0.0/16 | Target: pcx-xxxxxxxxx (ID del peering)
```
 
**Compte B:**
 
```
Route Tables -> rt-proyecto -> Routes -> Edit routes -> Add route:
Destination: 10.1.0.0/16 | Target: pcx-xxxxxxxxx (mateix ID del peering)
```
 
### Pas 7 — Verificar la Connectivitat
 
```
# Des de la instancia del Compte A -> ping al Compte B
ping 10.2.1.X
 
# Des d'una maquina del Compte B -> ping al Compte A
ping 10.1.1.X
```
 
---
 
## 10. Gestió de Credencials i Secrets
 
```
# Permisos restrictius obligatoris
chmod 600 ~/ProjecteFinal_G7/.env
 
# Verificar que .env NO ha estat pujat a GitHub
cat ~/ProjecteFinal_G7/.gitignore | grep ".env"
```
 
### Procediment de Rotació d'Emergència
 
En cas de compromís del `.env`:
 
1. Aturar tots els serveis: `docker compose down`
2. Generar noves contrasenyes (mínim 20 caràcters)
3. Actualitzar el `.env` amb les noves credencials
4. Reiniciar: `sudo bash .deploy.sh`
5. Revocar i regenerar les claus SSH actives a AWS EC2
> Per a producció real, substituir el `.env` per **AWS Secrets Manager**.
 
---
 
## 11. Seguretat i Hardening
 
### Arquitectura de Defensa en Profunditat
 
```
Internet -> Cloudflare (DDoS/CDN) -> AWS Security Groups -> S0 Firewall (iptables)
                                                          |-> S1 Nginx (proxy + capcaleres)
                                                          |-> S11 Snort IDS
                                                          |-> S7 Wazuh SIEM
```
 
### Ports Oberts en Producció (Auditoria Verificada)
 
| Port | Servei | Estat |
|------|--------|-------|
| 22/tcp | SSH (OpenSSH 8.9) | Obert — accés per clau exclusivament |
| 80/tcp | HTTP (Nginx 1.24) | Obert — redirecció a HTTPS |
| 443/tcp | HTTPS (Nginx 1.24) | Obert — TLS 1.2+ |
| **65.532 ports** | Resta | `filtered` — invisibles des d'Internet |
 
### Hardening SSH
 
```
sudo sed -i 's/PasswordAuthentication yes/PasswordAuthentication no/' /etc/ssh/sshd_config
sudo systemctl restart sshd
# Restringir port 22 al Security Group d'AWS unicament a IPs conegudes
```
 
### Capçaleres de Seguretat Nginx (S1) — Estat Verificat per Auditoria
 
- OK `X-XSS-Protection: 1; mode=block`
- OK `X-Content-Type-Options: nosniff`
- OK `X-Frame-Options: SAMEORIGIN`
- OK `Content-Security-Policy` — implementació bàsica
- PENDENT `Strict-Transport-Security` (HSTS)
### Hardening MariaDB (S4)
 
```
# Activar transport segur TLS
docker exec s4_mariadb mysql -u root -p -e "SET GLOBAL require_secure_transport=ON;"
 
# Verificar permisos minims de l'usuari d'aplicacio
docker exec s4_mariadb mysql -u root -p -e "SHOW GRANTS FOR 'appuser'@'%';"
# Ha de mostrar unicament: GRANT SELECT, INSERT, UPDATE
```
 
### Monitoratge d'Integritat de Fitxers — Wazuh (S7)
 
Wazuh monitora en temps real: `/etc`, `/var/www/html` i `/root`. Les regles actives per força bruta SSH són la **5710** i **5720** de Wazuh.
 
### Verificació IDS Snort (S11)
 
```
# Test de deteccio SQLi
curl -i -k "https://3.215.30.52/?id=1'%20OR%20'1'='1"
# Ha de generar alerta a /var/log/snort/alert
 
# Visualitzar alertes recents
docker exec s11_snort tail -f /var/log/snort/alert
```
 
---
 
## 12. Backup i Recuperació de Dades
 
### Backups Manuals (Fer Sempre Abans de Manteniment)
 
```
# Backup de MariaDB
docker exec s4_mariadb mysqldump -u root -p --all-databases > backup_$(date +%Y%m%d).sql
 
# Backup dels directoris persistents
tar -czf backup_volums_$(date +%Y%m%d).tar.gz \
  ~/ProjecteFinal_G7/db_data \
  ~/ProjecteFinal_G7/mail_logs \
  ~/ProjecteFinal_G7/snort_logs
 
# Pujada opcional a AWS S3
aws s3 cp backup_$(date +%Y%m%d).sql s3://cyberpyme-backups/
```
 
### Recuperació Ràpida (Re-desplegament)
 
```
git clone https://github.com/AlbertoTrujillo-ITB2425/ProjecteFinal_G7.git
cd ProjecteFinal_G7
# Restaurar el .env des de la copia segura
sudo bash .deploy.sh
```
 
### Gestió del Disc (Prevenció R02 — Risc Alt)
 
```
# Alerta CloudWatch al 80% (configurar des de la consola AWS)
 
# Rotacio de logs de Snort
docker exec s11_snort logrotate -f /etc/logrotate.d/snort
 
# Verificar espai actual
df -h ~/ProjecteFinal_G7/
du -sh ~/ProjecteFinal_G7/snort_logs ~/ProjecteFinal_G7/mail_logs ~/ProjecteFinal_G7/db_data
```
 
---
 
## 13. Gestió d'Usuaris (OpenLDAP S6)
 
### Estructura del Directori LDAP
 
```
dc=g7,dc=local
├── ou=users          <- Comptes d'usuaris finals
├── ou=groups
│   ├── cn=admins     <- Administradors del sistema
│   └── cn=workers    <- Usuaris de l'aplicacio
└── ou=services       <- Comptes de servei interns
```
 
### Operacions Bàsiques
 
```
# Llistar tots els usuaris
docker exec s6_openldap ldapsearch -x -b "dc=g7,dc=local" "(objectClass=inetOrgPerson)" cn mail
 
# Afegir un nou usuari
docker exec s6_openldap ldapadd -x -D "cn=admin,dc=g7,dc=local" -W -f nou_usuari.ldif
 
# Canviar contrasenya d'un usuari
docker exec s6_openldap ldappasswd -x -D "cn=admin,dc=g7,dc=local" -W -S "uid=usuari,ou=users,dc=g7,dc=local"
```
 
> El port 389 (LDAP) **no publica cap port extern** — únicament accessible des de `net_private`.
 
---
 
## 14. Gestió de Recursos — Ollama IA (S12)
 
Configuració recomanada al `docker-compose.yml`:
 
```
s12_ollama:
  mem_limit: 3g        # Maxim 3 GB de RAM
  cpus: '0.8'          # Maxim 0.8 CPUs
```
 
```
# Monitoratge en temps real
docker stats
docker stats s12_ollama --no-stream
 
# Si Ollama consumeix >80% de RAM, reiniciar
docker compose restart s12_ollama
```
 
L'estat és visible al **Dashboard d'Estat** de Grafana (port 3000).
 
---
 
## 15. Limitacions del Learner Lab d'AWS
 
> Límit de sessió de **4 hores** i aproximadament **100 USD de crèdits** (R13 — Risc Alt).
 
### Bones Pràctiques
 
- Atura la instància quan no s'estigui treballant
- Mantén el codi actualitzat al GitHub per re-desplegar ràpidament
- Conserva una còpia del `.env` en un lloc segur fora del repositori
- Monitoritza el consum des de la consola AWS Learner Lab
### Script de Re-desplegament Ràpid
 
```
git clone https://github.com/AlbertoTrujillo-ITB2425/ProjecteFinal_G7.git
cd ProjecteFinal_G7
# Restaurar .env des de la copia segura
curl -sSL https://raw.githubusercontent.com/AlbertoTrujillo-ITB2425/ProjecteFinal_G7/main/scripts/project_setup.sh | sudo bash
sudo bash .deploy.sh
```
 
---
 
## 16. Resum de Riscos Crítics per a l'Administrador
 
| ID | Risc | Probabilitat | Impacte | Accions preventives clau |
|---|---|:---:|:---:|---|
| R01 | Caiguda instància EC2 | Mitjana | Alt | `restart: always` + script crontab |
| R02 | Manca d'espai en disc | Alta | Mitjà | `logrotate` a S10/S11 + alerta CloudWatch 80% |
| R03 | Fallada del firewall S0 | Mitjana | Alt | Verificar `iptables -L` i logs S0 post-desplegament |
| R04 | Exposició de ports interns | Mitjana | Alt | `docker inspect` + `nmap -sV 3.215.30.52` setmanalment |
| R05 | Credencials en clar al `.env` | Mitjana | Alt | `chmod 600 .env` + mai pujar a GitHub |
| R06 | Força bruta SSH | Alta | Mitjà | Autenticació per clau + restringir port 22 al SG d'AWS |
| R10 | Consum excessiu Ollama | Alta | Mitjà | `mem_limit: 3g` + `cpus: 0.8` + `docker stats` |
| R12 | Caducitat certificat SSL | Mitjana | Mitjà | `certbot renew` via cron cada 60 dies |
 
---
