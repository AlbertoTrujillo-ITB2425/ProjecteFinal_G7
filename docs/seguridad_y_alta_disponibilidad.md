# Integración de la Asignatura: Seguridad y Alta Disponibilidad (MP0378)

Este documento detalla la justificación técnica de la infraestructura del proyecto final para nuestro cliente (**Nestlea**), estructurado bajo los Resultados de Aprendizaje (RA) oficiales de la asignatura de Seguridad y Alta Disponibilidad del Ciclo Formativo ASIXc2.

---

## 🛠️ Matriz de Aplicación de Resultados de Aprendizaje (RA)

### RA1 - Seguridad Física y Lógica
* **Hardening Operativo:** Securización activa de los sistemas base desplegados mediante configuraciones restrictivas de permisos en el entorno Linux.
* **Prevención de Ataques de Phishing e Incidentes:** Configuración del servidor de correo corporativo (**Postfix**) con registros y directivas estrictas para evitar técnicas de spoofing e interceptación analizadas en las auditorías de clase.
* **Políticas de Contraseñas:** Gestión estricta de credenciales de acceso para las transferencias de datos seguras a través del servicio **SFTP**.

### RA2 - Criptografía, Certificados Digitales y Bastionado Activo
* **Cifrado de Extremo a Extremo:** Implementación de criptografía asimétrica para la autenticación SSH (eliminando accesos por contraseña) y gestión de certificados digitales SSL/TLS para el tráfico HTTPS de la web de pedidos.
* **Protección Activa con Fail2ban:** Monitorización de logs y bloqueo automatizado de direcciones IP maliciosas que realicen intentos de intrusión o fuerza bruta.
* **Auditoría Externa (API Shodan):** Escaneos programados para garantizar que el entorno perimetral no exponga puertos vulnerables de forma pública.

### RA3 - VPNs (Conexiones Remotas Seguras)
* **Aislamiento del Entorno de Gestión:** Creación de un canal cifrado perimetral (VPN) dedicado. Todos los servicios críticos internos (administración de contenedores **Docker**, la base de datos **MariaDB** y el directorio **OpenLDAP**) requieren conexión obligatoria a la VPN, quedando invisibles desde la Internet pública.

### RA4 - Implantación de Tallafocs (Firewalls e IDS/IPS)
* **Cortafuegos Perimetral (pfSense):** Segmentación completa de la infraestructura virtualizada dividiendo el tráfico en zonas de confianza: LAN para servicios internos de datos y DMZ para el servidor web expuesto.
* **Sistema de Detección de Intrusiones (Snort):** Configuración de **Snort** inspeccionando paquetes de red en tiempo real. Como evidencia en el repositorio, las alertas generadas por este servicio se centralizan directamente en nuestra carpeta `/snort_logs`.

### RA5 - Implantación de Servidores Proxy Intermediarios
* **Proxy Inverso con Nginx:** Configuración de **Nginx** actuando como proxy inverso perimetral. Se encarga de mitigar ataques directos a la aplicación web, ocultar la arquitectura de los contenedores internos y centralizar la descarga criptográfica SSL/TLS.
* **Control de Navegación Saliente:** Implementación de proxies intermedios (como Privoxy o CCProxy) para auditar y filtrar el contenido del tráfico saliente de los usuarios internos de la PYME.

### RA6 - Alta Disponibilidad
* **Orquestación y Redundancia:** Despliegue de la aplicación de pedidos mediante contenedores **Docker** en entornos locales (`Isard` / `VirtualBox`) y replicación escalable en la infraestructura cloud de **AWS**.
* **Automatización de Backups:** Scripts en Bash (`/scripts`) automatizados para realizar copias de seguridad cifradas de la base de datos **MariaDB** y datos de **SFTP**, subiéndose periódicamente a almacenes externos en la nube para garantizar la continuidad del negocio ante desastres.

### RA7 - Legislación y Normativa sobre Seguridad y Protección de Datos
* **Cumplimiento del RGPD:** Aplicación de técnicas de cifrado en reposo para los datos personales y registros de transacciones almacenados en **MariaDB**.
* **Centralización de Auditoría (SIEM / SOC):** Retención y análisis centralizado de los logs de la infraestructura. Toda la telemetría se procesa para auditorías legales y de cumplimiento técnico, complementando el despliegue del SOC documentado en `docs/despliegue-soc.md`.
