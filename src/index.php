<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Control de Acceso estricto
if (!isset($_SESSION['user_id'])) {
    header("Location: auth.php");
    exit();
}

require_once "includes/components.php";
include "includes/header.php";

// 2. Obtener Telemetría Estática Inicial desde Docker
$infrastructure = getInfrastructure();
$nodeStatusList = getNodeStatus($infrastructure);

// Calcular métricas reales de los contenedores
$totalNodes = count($nodeStatusList);
$activeNodesCount = count(array_filter($nodeStatusList, fn($n) => $n['status'] === 'Online'));
$sysHealth = calculateHealth($activeNodesCount, $totalNodes);

$isOllamaReady = checkContainer('s12_ollama', 11434);
$grafanaUrl = "grafana_access.php";
?>

<main class="p-6 md:p-10 max-w-7xl mx-auto space-y-8 animate-fade-in text-main">
    
    <!-- HEADER PRINCIPAL CON RELOJ DIGITAL -->
    <header class="mb-8 border-b border-slate-200 dark:border-white/10 pb-5 flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
        <div>
            <div class="flex items-center gap-3">
                <div class="w-3 h-3 bg-blue-500 rounded-full animate-pulse shadow-[0_0_8px_#3b82f6]"></div>
                <h1 class="text-3xl font-black tracking-tighter uppercase italic">
                    SOC<span class="text-blue-500">COMMAND</span> <span class="text-muted font-thin">v3.0</span>
                </h1>
            </div>
            <p class="text-[10px] text-muted uppercase tracking-[0.4em] mt-2 font-bold opacity-80">
                Data Center: <span class="text-slate-700 dark:text-slate-300">AWS-EC2-US-EAST-1</span> // Auditor: <?= htmlspecialchars($_SESSION['user_name'] ?? 'Root') ?>
            </p>
        </div>
        
        <!-- RELOJ DIGITAL INTEGRADO -->
        <div class="flex items-center gap-4">
            <div class="flex flex-col text-right">
                <span id="digital-clock" class="text-xl font-mono font-black text-slate-800 dark:text-white tracking-widest bg-slate-100 dark:bg-white/5 border border-slate-200 dark:border-white/10 px-4 py-1.5 rounded-xl shadow-inner">00:00:00</span>
                <span class="text-[8px] uppercase font-bold text-muted tracking-widest mt-1">System Time (Local)</span>
            </div>
            <span class="text-[10px] font-black uppercase tracking-widest text-emerald-600 dark:text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 px-4 py-3 rounded-xl flex items-center gap-2 backdrop-blur-md shadow-lg">
                <i class="fas fa-shield-check"></i> Firewall Activo
            </span>
        </div>
    </header>

    <!-- MÉTRICAS CRÍTICAS DINÁMICAS -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <?= panelMetric("Nodos Activos", "$activeNodesCount / $totalNodes", "fa-network-wired", "text-blue-500") ?>
        <?= panelMetric("Salud Sistema", "$sysHealth%", "fa-heart-pulse", $sysHealth > 80 ? "text-emerald-500" : "text-amber-500") ?>
        <?= panelMetric("IA Engine", $isOllamaReady ? "READY" : "OFFLINE", "fa-microchip", $isOllamaReady ? "text-purple-500" : "text-red-500") ?>
        <?= panelMetric("Email Alerts", "ACTIVE", "fa-envelope-open-text", "text-orange-500") ?>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- COLUMNA IZQUIERDA: INFRAESTRUCTURA -->
        <div class="lg:col-span-2 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="<?= $grafanaUrl ?>" target="_blank" class="block group">
                    <div class="glass-panel p-5 rounded-2xl border border-slate-200 dark:border-white/5 bg-white/50 dark:bg-white/5 hover:bg-blue-50 dark:hover:bg-white/10 transition-all duration-300 shadow-lg h-full">
                        <div class="flex justify-between items-start mb-3">
                            <div class="w-10 h-10 bg-blue-500/20 rounded-xl flex items-center justify-center text-blue-500 group-hover:scale-110 transition-transform">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <i class="fas fa-external-link-alt text-[10px] text-muted"></i>
                        </div>
                        <h3 class="text-sm font-bold uppercase tracking-wider text-main">Métricas Live</h3>
                        <p class="text-[10px] text-muted mt-1 line-clamp-2">Telemetría segura vía CyberPyme Proxy.</p>
                    </div>
                </a>
                <?= panelLink("Threat Intelligence", "Escaneo de vulnerabilidades.", "modules/scanner/scanner.php", "fa-user-secret", "bg-purple-500/20 text-purple-500") ?>
                <?= panelLink("Alertas Email", "Configurar notificaciones SOC.", "modules/email/socemail.php", "fa-paper-plane", "bg-orange-500/20 text-orange-500") ?>
            </div>
            
            <div class="glass-panel rounded-2xl overflow-hidden border border-slate-200 dark:border-white/5 bg-white/50 dark:bg-white/5 shadow-xl">
                <div class="p-5 border-b border-slate-200 dark:border-white/5 bg-slate-50/50 dark:bg-white/5 flex justify-between items-center">
                    <h3 class="text-xs font-black uppercase tracking-widest text-muted">Estado de Infraestructura Docker</h3>
                    <span class="text-[9px] bg-blue-500/10 text-blue-600 dark:text-blue-400 px-2 py-1 rounded-md font-bold uppercase flex items-center gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span> Real-time
                    </span>
                </div>
                <div class="overflow-x-auto">
                    <?= panelTable("", $nodeStatusList) ?>
                </div>
            </div>
        </div>

        <!-- COLUMNA DERECHA: TELEMETRÍA REACTIVA ASÍNCRONA -->
        <aside class="space-y-6">
            <div class="glass-panel p-6 rounded-2xl border border-slate-200 dark:border-white/5 bg-white/50 dark:bg-white/5 shadow-xl">
                <h3 class="text-xs font-black uppercase tracking-widest text-muted mb-6 italic">Diagnóstico Global</h3>
                <?= panelHealth($sysHealth) ?>
                
                <div class="mt-4 pt-4 border-t border-slate-200 dark:border-white/10">
                    <div class="flex justify-between text-[10px] font-bold text-muted mb-1">
                        <span>Carga CPU Host</span>
                        <span id="cpu-text-percentage">Calculando...</span>
                    </div>
                    <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-1.5 overflow-hidden">
                        <div id="cpu-progress-bar" class="bg-blue-500 h-1.5 rounded-full transition-all duration-500" style="width: 0%"></div>
                    </div>
                </div>
            </div>
            
            <!-- SOC EVENTS FEED -->
            <div class="glass-panel p-6 rounded-2xl border border-slate-200 dark:border-white/5 bg-white/50 dark:bg-white/5 shadow-xl">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xs font-black uppercase tracking-widest text-muted italic flex items-center gap-2">
                        <i class="fas fa-shield-alt text-emerald-500"></i> SOC Events Feed
                    </h3>
                    <span class="text-[9px] bg-red-500/10 text-red-400 px-2 py-1 rounded-md font-bold uppercase animate-pulse">Live</span>
                </div>
                
                <div id="soc-logs-container" class="space-y-2 max-h-[300px] overflow-y-auto pr-2 custom-scrollbar">
                    <div class="text-center py-4 text-slate-500 text-xs italic">Cargando eventos de seguridad...</div>
                </div>
            </div>
        </aside>

    </div>
</main>

<!-- MOTOR JAVASCRIPT: RELOJ + CONSULTAS TELEMETRÍA DINÁMICA -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    // 1. Lógica del Reloj Digital del Sistema
    function updateClock() {
        const now = new Date();
        const timeString = now.toTimeString().split(' ')[0];
        document.getElementById('digital-clock').textContent = timeString;
    }
    setInterval(updateClock, 1000);
    updateClock();

    // 2. Lógica de Actualización Asíncrona (CPU + Eventos)
    const cpuText = document.getElementById('cpu-text-percentage');
    const cpuBar = document.getElementById('cpu-progress-bar');
    const logsContainer = document.getElementById('soc-logs-container');

    function fetchTelemetry() {
        fetch('telemetry.php')
            .then(response => response.json())
            .then(data => {
                // Actualizar interfaz de la CPU
                cpuText.textContent = data.cpu;
                cpuBar.style.width = data.cpu;

                // Actualizar interfaz de logs
                if (data.logs.length === 0) {
                    logsContainer.innerHTML = `
                        <div class="text-center py-4 text-slate-500 text-xs italic">
                            <i class="fas fa-info-circle mr-1"></i> Esperando eventos de seguridad...
                        </div>`;
                } else {
                    let html = '';
                    data.logs.forEach(log => {
                        html += `
                        <div class="flex gap-3 items-start p-2 rounded hover:bg-white/5 transition-colors group animate-fade-in">
                            <div class="mt-1">
                                <i class="fas ${log.icon} ${log.class} text-[10px]"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-[9px] font-bold uppercase ${log.class}">${log.source}</span>
                                    <span class="text-[9px] text-slate-500 font-mono">${log.time}</span>
                                </div>
                                <p class="text-[10px] text-slate-300 leading-tight break-words">${log.message}</p>
                            </div>
                        </div>`;
                    });
                    logsContainer.innerHTML = html;
                }
            })
            .catch(error => console.error("Error cargando telemetría del SOC:", error));
    }

    // Consultar el endpoint de telemetría automáticamente cada 2 segundos
    setInterval(fetchTelemetry, 2000);
    fetchTelemetry();
});
</script>

<?php include "includes/chatbot.php"; ?>
<?php include "includes/footer.php"; ?>
// test
