<?php
session_start();
if (!isset($_SESSION['user_id'])) { 
    header('Location: ../../auth.php'); 
    exit; 
}
$auditor_name = $_SESSION['user_name'] ?? "Auditor_Desconocido";

// Generar la firma TXT esperada basándonos en la sesión del usuario para mostrarla en la UI
$expected_token = "auditchain-verify=" . substr(md5($_SESSION['user_id'] . "salt-seguro"), 0, 16);
?>
<?php include '../../includes/header.php'; ?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    @import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;700&family=Inter:wght@400;600;800&display=swap');
    
    :root {
        --bg-dark: #0f172a;
        --glass-bg: rgba(30, 41, 59, 0.7);
        --glass-border: rgba(148, 163, 184, 0.1);
        --text-main: #e2e8f0;
    }

    body { background-color: var(--bg-dark); color: var(--text-main); font-family: 'Inter', sans-serif; }
    .terminal-font { font-family: 'JetBrains Mono', monospace; }
    
    .glass-panel { 
        background: var(--glass-bg); 
        backdrop-filter: blur(12px); 
        border: 1px solid var(--glass-border); 
        box-shadow: 0 4px 30px rgba(0, 0, 0, 0.3);
    }

    .scan-line {
        height: 2px; width: 100%;
        background: linear-gradient(90deg, transparent, #3b82f6, transparent);
        animation: scan 1.5s infinite linear;
    }
    @keyframes scan { 0% { transform: translateX(-100%); } 100% { transform: translateX(100%); } }

    ::-webkit-scrollbar { width: 8px; }
    ::-webkit-scrollbar-track { background: #1e293b; }
    ::-webkit-scrollbar-thumb { background: #475569; border-radius: 4px; }
</style>

<main class="max-w-7xl mx-auto px-4 py-8">
    
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-black italic text-white tracking-tighter flex items-center gap-3">
                <i class="fas fa-shield-halved text-blue-500"></i> SOC <span class="text-blue-500">AUDIT</span> CONSOLE
            </h1>
            <p class="text-xs terminal-font text-slate-400 mt-1">
                OPERADOR: <span class="text-blue-400 font-bold"><?= htmlspecialchars($auditor_name) ?></span> | 
                ESTADO: <span id="system-status" class="text-slate-500">ESPERANDO INPUT...</span>
            </p>
        </div>
        
        <div class="glass-panel px-6 py-3 rounded-lg flex gap-6 text-xs font-bold uppercase tracking-wide opacity-50 transition-opacity duration-300" id="stats-panel">
            <div class="flex flex-col items-center">
                <span class="text-slate-400 text-[10px]">Objetivo</span>
                <span id="stat-target" class="text-white text-lg truncate max-w-[150px]">--</span>
            </div>
            <div class="w-px bg-slate-700 h-8 self-center"></div>
            <div class="flex flex-col items-center">
                <span class="text-slate-400 text-[10px]">Riesgo</span>
                <span id="stat-risk" class="text-slate-500 text-lg">--</span>
            </div>
            <div class="w-px bg-slate-700 h-8 self-center"></div>
            <div class="flex flex-col items-center">
                <span class="text-slate-400 text-[10px]">Tiempo</span>
                <span id="stat-time" class="text-blue-400 text-lg">0s</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <div class="lg:col-span-4 space-y-6">
            
            <section class="glass-panel p-6 rounded-xl border-l-4 border-blue-500">
                <label class="text-[10px] font-bold text-slate-400 uppercase mb-2 block">Target Asset</label>
                <div class="relative mb-4">
                    <input type="text" id="target" placeholder="ej. mipyme.com" class="w-full bg-slate-900/80 border border-slate-700 rounded-lg p-3 pl-10 text-blue-300 outline-none focus:border-blue-500 transition terminal-font text-sm">
                    <i class="fas fa-globe absolute left-3 top-3.5 text-slate-500"></i>
                </div>

                <div class="mb-4">
                    <label class="text-[10px] font-bold text-slate-400 uppercase mb-2 block">Modo</label>
                    <select id="type" class="w-full bg-slate-900/80 border border-slate-700 rounded-lg p-3 text-sm text-slate-300 outline-none focus:border-blue-500 terminal-font">
                        <option value="quick">⚡ Quick (Puertos Comunes)</option>
                        <option value="full">🛡️ Full (Detección Versiones)</option>
                    </select>
                </div>

                <div class="mb-6 p-3 bg-slate-900/60 rounded-lg border border-slate-700/50 text-xs">
                    <span class="text-slate-400 font-bold block mb-1 uppercase tracking-wider text-[10px]"><i class="fas fa-key text-blue-400 mr-1"></i> Firma de Validación</span>
                    <p class="text-[11px] text-slate-400 mb-2 leading-relaxed">Para auditar un dominio, añade este registro <strong>TXT</strong> en su configuración DNS externa:</p>
                    <div class="flex items-center gap-2 bg-black/40 p-2 rounded border border-blue-500/20 font-mono text-blue-400 select-all tracking-tight break-all">
                        <?= htmlspecialchars($expected_token) ?>
                    </div>
                </div>

                <div class="grid grid-cols-4 gap-2">
                    <button id="btn-run" onclick="startAudit()" class="col-span-3 bg-blue-600 hover:bg-blue-500 text-white font-bold py-3 rounded-lg uppercase text-xs transition shadow-lg shadow-blue-900/20 flex items-center justify-center gap-2">
                        <i class="fas fa-play"></i> INICIAR
                    </button>
                    <button onclick="clearConsole()" class="bg-slate-800 hover:bg-red-900/30 hover:text-red-400 text-slate-400 font-bold py-3 rounded-lg transition flex items-center justify-center">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </section>

            <section id="recommendations-panel" class="glass-panel p-6 rounded-xl hidden">
                <h3 class="text-xs font-bold text-yellow-400 uppercase mb-4 border-b border-slate-700 pb-2">
                    <i class="fas fa-exclamation-triangle mr-2"></i>Acciones Recomendadas
                </h3>
                <div id="recommendations-list" class="space-y-2 text-xs text-slate-300"></div>
            </section>
        </div>

        <div class="lg:col-span-8 space-y-6">
            
            <div id="risk-status-card" class="glass-panel p-6 rounded-xl border-l-4 border-slate-600 hidden transition-all duration-500">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Postura de Seguridad</h2>
                        <div class="flex items-center gap-3">
                            <i id="risk-icon" class="fas fa-question text-3xl text-slate-500"></i>
                            <span id="risk-label" class="text-2xl font-black text-white">ANALIZANDO...</span>
                        </div>
                        <p id="risk-summary" class="text-sm text-slate-400 mt-2">Esperando resultados...</p>
                    </div>
                    <div class="text-right hidden md:block">
                        <div class="text-[10px] text-slate-500 uppercase">IP Resuelta</div>
                        <div id="detected-ip" class="font-mono text-blue-400 font-bold">--.--.--.--</div>
                    </div>
                </div>
            </div>

            <div id="ai-panel" class="hidden glass-panel p-6 rounded-xl border border-purple-500/30 bg-purple-900/10 relative overflow-hidden">
                <div class="absolute top-0 right-0 p-4 opacity-10"><i class="fas fa-brain text-8xl text-purple-500"></i></div>
                <div class="flex items-center gap-2 mb-3">
                    <i class="fas fa-sparkles text-purple-400 animate-pulse"></i>
                    <h3 class="text-xs font-bold text-purple-400 uppercase tracking-widest">Análisis de Inteligencia Artificial</h3>
                </div>
                <div id="ai-content" class="text-sm text-slate-300 terminal-font leading-relaxed whitespace-pre-wrap max-h-[300px] overflow-y-auto pr-2 border-l-2 border-purple-500/30 pl-4"></div>
            </div>

            <div class="glass-panel rounded-xl overflow-hidden flex flex-col h-[400px] relative">
                <div id="scan-progress-container" class="hidden absolute top-0 left-0 w-full h-1 bg-slate-800 z-20">
                    <div class="h-full bg-blue-500 w-full scan-line"></div>
                </div>

                <div class="bg-slate-900/90 px-4 py-3 border-b border-slate-700 flex justify-between items-center">
                    <span class="text-[10px] font-bold text-slate-400 uppercase"><i class="fas fa-terminal mr-2"></i>Live Output</span>
                    <span id="timer-display" class="text-[10px] font-mono text-blue-400 hidden">TIEMPO: 0s</span>
                </div>

                <div class="flex-1 overflow-y-auto p-4 bg-black/60 font-mono text-xs relative">
                    <pre id="console-output" class="whitespace-pre-wrap break-all leading-5 text-green-400/80"></pre>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <button id="btn-pdf" onclick="exportToPDF()" disabled class="glass-panel p-3 rounded-lg text-xs font-bold text-slate-500 uppercase border border-slate-700 hover:border-blue-500 hover:text-blue-400 transition disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                    <i class="fas fa-file-pdf"></i> Generar Informe PDF
                </button>
                <button onclick="window.location.reload()" class="glass-panel p-3 rounded-lg text-xs font-bold text-slate-500 uppercase border border-slate-700 hover:border-red-500 hover:text-red-400 transition flex items-center justify-center gap-2">
                    <i class="fas fa-power-off"></i> Reiniciar
                </button>
            </div>
        </div>
    </div>
</main>

<script>
    let isScanning = false;
    let lastScanData = null;
    let startTime = 0;
    let timerInterval;
    let currentAIResponse = ""; 

    const consoleOut = document.getElementById('console-output');
    const btnRun = document.getElementById('btn-run');
    const statsPanel = document.getElementById('stats-panel');

    function log(msg, type = 'info') {
        const timestamp = new Date().toLocaleTimeString('es-ES');
        let colorClass = 'text-green-400';
        if (type === 'warn') colorClass = 'text-yellow-400';
        if (type === 'error') colorClass = 'text-red-400';
        if (type === 'header') colorClass = 'text-blue-400 font-bold mt-2 block border-b border-blue-500/30 pb-1';

        const line = `<div class="${colorClass}"><span class="opacity-40 mr-2">[${timestamp}]</span>${msg}</div>`;
        consoleOut.innerHTML += line;
        consoleOut.scrollTop = consoleOut.scrollHeight;
    }

    function clearConsole() {
        if(isScanning) return;
        consoleOut.innerHTML = "";
        document.getElementById('risk-status-card').classList.add('hidden');
        document.getElementById('recommendations-panel').classList.add('hidden');
        document.getElementById('ai-panel').classList.add('hidden');
        document.getElementById('btn-pdf').disabled = true;
        
        document.getElementById('stat-target').innerText = "--";
        document.getElementById('stat-risk').innerText = "--";
        document.getElementById('stat-risk').className = "text-slate-500 text-lg";
        document.getElementById('stat-time').innerText = "0s";
        document.getElementById('system-status').innerText = "ESPERANDO INPUT...";
        document.getElementById('system-status').className = "text-slate-500";
        statsPanel.classList.add('opacity-50');
        currentAIResponse = "";
    }

    async function startAudit() {
        const target = document.getElementById('target').value.trim();
        const type = document.getElementById('type').value;
        
        if (!target) return alert("Introduce un objetivo válido.");
        if (isScanning) return;

        isScanning = true;
        clearConsole();
        
        btnRun.disabled = true;
        btnRun.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> ESCANEANDO...';
        document.getElementById('scan-progress-container').classList.remove('hidden');
        document.getElementById('system-status').innerText = "VERIFICANDO Y ESCANEANDO...";
        document.getElementById('system-status').className = "text-blue-400 animate-pulse";
        statsPanel.classList.remove('opacity-50');
        document.getElementById('stat-target').innerText = target;

        startTime = Date.now();
        document.getElementById('timer-display').classList.remove('hidden');
        timerInterval = setInterval(() => {
            const elapsed = Math.floor((Date.now() - startTime) / 1000);
            document.getElementById('timer-display').innerText = `TIEMPO: ${elapsed}s`;
            document.getElementById('stat-time').innerText = `${elapsed}s`;
        }, 1000);

        try {
            log(`>>> INICIANDO COMPROBACIÓN Y ESCANEO A: ${target}`, 'header');
            
            // 1. Fetch al Backend mediante POST JSON (Clean & SMB friendly)
            const response = await fetch(`../../api/scan_async.php`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ target: target, type: type })
            });
            
            if (!response.ok) throw new Error(`Error HTTP: ${response.status}`);
            
            const data = await response.json();
            lastScanData = data;

            if (data.status === 'success') {
                updateUIWithRealData(data);
                log(data.technical_logs, 'info');
                
                // 2. Llamar a la IA con los logs técnicos
                log(">>> CONSULTANDO INTELIGENCIA ARTIFICIAL...", 'header');
                await fetchAIAnalysis(data.technical_logs, target);

                document.getElementById('system-status').innerText = "ESCANEO COMPLETADO";
                document.getElementById('system-status').className = "text-green-400";
                document.getElementById('btn-pdf').disabled = false;
                confetti({ particleCount: 50, spread: 60 });
            } else {
                // Captura el fallo de validación DNS o de IP pública directo desde el backend
                log(`ERROR: ${data.message}`, 'error');
                document.getElementById('system-status').innerText = "ESCANEO DENEGADO";
                document.getElementById('system-status').className = "text-red-400";
            }

        } catch (error) {
            log(`FALLO CRÍTICO: ${error.message}`, 'error');
            document.getElementById('system-status').innerText = "ERROR DE CONEXIÓN";
            document.getElementById('system-status').className = "text-red-500";
        } finally {
            clearInterval(timerInterval);
            isScanning = false;
            btnRun.disabled = false;
            btnRun.innerHTML = '<i class="fas fa-play"></i> INICIAR';
            document.getElementById('scan-progress-container').classList.add('hidden');
        }
    }

    async function fetchAIAnalysis(logs, target) {
        try {
            const aiResponse = await fetch("../../api/ai_analysis.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ scan_data: logs, target: target })
            });

            const aiData = await aiResponse.json();
            
            if (aiData.response) {
                currentAIResponse = aiData.response;
                const aiPanel = document.getElementById('ai-panel');
                const aiContent = document.getElementById('ai-content');
                
                aiPanel.classList.remove('hidden');
                aiContent.innerText = currentAIResponse;
            } else {
                log("La IA no pudo generar un análisis.", 'warn');
            }
        } catch (error) {
            log("Error conectando con la IA.", 'error');
            console.error(error);
        }
    }

    function updateUIWithRealData(data) {
        const card = document.getElementById('risk-status-card');
        card.classList.remove('hidden');
        card.style.borderColor = data.risk_color || '#cbd5e1';

        const riskLabel = document.getElementById('risk-label');
        riskLabel.innerText = data.risk_level || 'DESCONOCIDO';
        riskLabel.style.color = data.risk_color || '#fff';

        const icon = document.getElementById('risk-icon');
        icon.className = `fas ${data.risk_icon || 'fa-shield'} text-3xl`;
        icon.style.color = data.risk_color || '#fff';

        document.getElementById('risk-summary').innerText = data.summary || "Sin resumen disponible.";
        document.getElementById('detected-ip').innerText = data.dns_ip || "No resuelto";

        const statRisk = document.getElementById('stat-risk');
        statRisk.innerText = data.risk_level || '--';
        statRisk.style.color = data.risk_color || '#94a3b8';

        const recPanel = document.getElementById('recommendations-panel');
        const recList = document.getElementById('recommendations-list');
        recList.innerHTML = '';
        
        if (data.recommendations && data.recommendations.length > 0) {
            recPanel.classList.remove('hidden');
            data.recommendations.forEach(rec => {
                const div = document.createElement('div');
                div.className = 'bg-yellow-500/10 border-l-2 border-yellow-500 p-2 mb-1 text-yellow-200';
                div.innerText = rec;
                recList.appendChild(div);
            });
        } else {
            recPanel.classList.add('hidden');
        }
    }

    function exportToPDF() {
        if (!lastScanData) return;

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'generate_report.php';
        
        const createInput = (name, value) => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = name;
            input.value = value || '';
            form.appendChild(input);
        };

        createInput('target', lastScanData.target);
        createInput('logs', lastScanData.technical_logs);
        createInput('ai_analysis', currentAIResponse); 
        createInput('auditor', "<?= htmlspecialchars($auditor_name) ?>");
        
        document.body.appendChild(form);
        form.submit();
    }
</script>
</body></html>
