# 🛡️ Manual de Desplegament: CyberPyme Enterprise SOC

Aquest document detalla el procés de desplegament i gestió de la infraestructura **CyberPyme**, una solució integral de ciberseguretat basada en microserveis dockeritzats.

### **1. Arquitectura de Microserveis**

La plataforma s'orquestra mitjançant **Docker Compose** i es divideix en els següents mòduls:

| Mòdul | Contenidor | Funció Principal |
| --- | --- | --- |
| **Gateway** | `s1_nginx` | Proxy invers i terminació SSL (Ports 80, 443). |
| **Core App** | `s2_node`, `s3_node` | Processament de l'aplicació PHP-FPM. |
| **Audit Scanner** | `s9_scanner` | Motor d'escaneig de xarxa (nmap/scripts). |
| **Intel·ligència** | `s12_ollama` | Motor local d'IA (Qwen/Llama) per a logs. |
| **SIEM / IDS** | `s7_wazuh`, `s11_snort` | Detecció d'intrusions i gestió d'esdeveniments. |
| **Databases** | `s4_mariadb`, `s5_redis` | Persistència de dades i cau de sessió. |
| **Visualització** | `s8_grafana` | Dashboards de mètriques i seguretat. |

---

### **2. Desplegament de la Plataforma**

El desplegament s'ha simplificat per ser executat amb una sola comanda des de l'arrel del repositori.

#### **Pas 1: Preparació del Sistema (Host)**

Si el servidor és nou, executeu el script de configuració per instal·lar Docker, Compose i les dependències de xarxa:

```bash
curl -sSL https://raw.githubusercontent.com/AlbertoTrujillo-ITB2425/ProjecteFinal_G7/main/scripts/project_setup.sh | sudo bash

```

#### **Pas 2: Execució del Deploy Automatitzat**

Un cop tingueu el codi a la vostra màquina, accediu al directori principal i executeu el script de desplegament:

```bash
cd ~/ProjecteFinal_G7
sudo bash .deploy.sh

```

> **Nota operativa**: Aquest script construeix les imatges personalitzades, configura els volums de dades per a la base de dades i aixeca els 12 contenidors en mode fons (*detached*).

---

### **3. Gestió de la Consola i Servei**

Podeu verificar l'estat de tots els mòduls amb la següent comanda:

```bash
docker ps --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}"

```

#### **Comandes de Manteniment**

* **Reiniciar el SOC complet:** `sudo docker compose restart`
* **Actualitzar l'aplicació:** `git pull && sudo bash .deploy.sh`
* **Logs del sistema en viu:** `sudo docker compose logs -f`
* **Accés a la línia de comandes de la IA:** `docker exec -it s12_ollama ollama list`

---

### **4. Verificació d'Accés**

Un cop el sistema estigui "Up", les interfícies estaran disponibles a:

1. **Portal Principal**: `http://<EL_TEU_IP_HOST>` (Gestionat per `s1_nginx`).
2. **Panell de Control Grafana**: `http://<EL_TEU_IP_HOST>:3000`.
3. **Endpoint d'IA (Ollama)**: `http://<EL_TEU_IP_HOST>:11434`.

---

**CyberPyme SOC v7.6.5** | *Guia d'administració per a desplegaments en contenidors.*
