<?php
// src/api/scan_async.php - Versión Business/SMB Friendly

session_start(); // Mantener sesión para validar al usuario
if (ob_get_level()) ob_clean();
set_time_limit(120);
ignore_user_abort(true);

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, must-revalidate');

$SHODAN_API_KEY = 'cTZNVRd5Qf4GOpq7TVUoe3K9TdMN1ubt'; 
$MAX_EXEC_TIME = 60; 

// Obtener datos
$target = '';
$type = 'quick';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $target = $data['target'] ?? '';
    $type = $data['type'] ?? 'quick';
} else {
    $target = $_GET['target'] ?? '';
    $type = $_GET['type'] ?? 'quick';
}

if (empty($target)) {
    echo json_encode(["status" => "error", "message" => "Objetivo vacío"]);
    exit;
}

// Validación estricta
$isIP = filter_var($target, FILTER_VALIDATE_IP);
$isDomain = preg_match('/^[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $target);

if (!$isIP && !$isDomain) {
    echo json_encode(["status" => "error", "message" => "Objetivo inválido: $target"]);
    exit;
}

// =========================
// MÓDULO DE VERIFICACIÓN DNS (SEGURIDAD AÑADIDA)
// =========================
if ($isDomain && isset($_SESSION['user_id'])) {
    $expected_token = "auditchain-verify=" . substr(md5($_SESSION['user_id'] . "salt-seguro"), 0, 16);
    $records = dns_get_record($target, DNS_TXT);
    $verified = false;
    foreach ($records as $record) {
        if (isset($record['txt']) && trim($record['txt'], '"\' ') === $expected_token) {
            $verified = true;
            break;
        }
    }
    if (!$verified) {
        echo json_encode(["status" => "error", "message" => "Validación fallida: Registro TXT no encontrado para $target"]);
        exit;
    }
}

// Variables de análisis
$riskScore = 0;
$detectedPorts = [];
$recommendations = [];
$technicalLogs = ""; // Para el PDF

// =========================
// MÓDULO 1: OSINT (SHODAN)
// =========================
$shodanInfo = "No disponible o IP Local.";
if ($isIP && !filter_var($target, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
    $shodanInfo = "IP Privada (Local).";
} elseif ($isIP) {
    $url = "https://api.shodan.io/shodan/host/$target?key=$SHODAN_API_KEY";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $shodanJson = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode == 200 && $shodanJson) {
        $json = json_decode($shodanJson, true);
        $vulns = isset($json['vulns']) ? count($json['vulns']) : 0;
        $org = $json['org'] ?? 'Desconocido';
        $shodanInfo = "ISP: $org | Vulns Públicas: $vulns";
        if ($vulns > 0) $riskScore += 50;
    }
}

// =========================
// MÓDULO 2: DNS & WHOIS
// =========================
$dnsInfo = "N/A";
$whoisInfo = "N/A";

if ($isDomain) {
    $dns = shell_exec("dig +short {$target} A 2>&1");
    $dnsInfo = trim($dns ?: "No resuelto");

    $whois = shell_exec("timeout 5 whois {$target} 2>&1");
    if ($whois && preg_match('/Registry Expiry Date:\s*(.*)/i', $whois, $matches)) {
        $whoisInfo = "Expira: " . date('d/m/Y', strtotime($matches[1]));
    }
}

// =========================
// MÓDULO 3: NMAP INTELIGENTE
// =========================
$nmapFlags = ($type === 'full') ? "-sT -Pn -sV -F --open" : "-sT -Pn -F --open";
$safeTarget = escapeshellarg($target);
$cmd = "timeout {$MAX_EXEC_TIME} nmap {$nmapFlags} {$safeTarget} 2>&1";
$rawNmap = shell_exec($cmd);

$technicalLogs = $rawNmap ? $rawNmap : "Escaneo fallido o timeout.\n";

if ($rawNmap && strpos($rawNmap, 'failed') === false) {
    $lines = explode("\n", $rawNmap);
    
    foreach ($lines as $line) {
        // Filtrar ruido técnico
        if (strpos($line, 'SF-') !== false) continue;
        if (strpos($line, 'Service detection performed') !== false) continue;
        if (strpos($line, 'Nmap scan report') !== false) continue;
        if (strpos($line, 'Host is up') !== false) continue;
        
        // Extraer puertos
        if (preg_match('/(\d+)\/(tcp|udp)\s+open\s+(\S+)/', $line, $matches)) {
            $portNum = $matches[1];
            $proto = $matches[2];
            $service = $matches[3];
            
            $detectedPorts[] = ['port' => "$portNum/$proto", 'service' => $service];
            
            // Lógica de Riesgo
            if (in_array(strtolower($service), ['telnet', 'ftp', 'rsh'])) {
                $riskScore += 40;
                $recommendations[] = "⚠️ El puerto $portNum usa protocolo inseguro ($service). Desactivar inmediatamente.";
            } elseif (strpos($service, 'http') !== false && strpos($service, 'ssl') === false && strpos($service, 'https') === false) {
                $riskScore += 10;
                $recommendations[] = "ℹ️ Web sin cifrado (HTTP) en puerto $portNum. Instalar certificado SSL (HTTPS).";
            }
        }
    }
}

// Determinar Estado Visual
$statusLabel = "SECURE";
$statusColor = "#10b981";
$statusIcon = "fa-shield-check";

if ($riskScore > 20) {
    $statusLabel = "REVIEW NEEDED";
    $statusColor = "#f59e0b";
    $statusIcon = "fa-triangle-exclamation";
}
if ($riskScore > 60) {
    $statusLabel = "VULNERABLE";
    $statusColor = "#ef4444";
    $statusIcon = "fa-radiation";
}

$summaryText = empty($detectedPorts) 
    ? "No se detectaron servicios abiertos comunes. El sistema parece seguro." 
    : "Se detectaron " . count($detectedPorts) . " servicios activos. Se recomienda revisar las alertas.";

// =========================
// SALIDA JSON
// =========================
echo json_encode([
    "status" => "success",
    "target" => $target,
    "risk_level" => $statusLabel,
    "risk_color" => $statusColor,
    "risk_icon" => $statusIcon,
    "summary" => $summaryText,
    "ports" => $detectedPorts,
    "recommendations" => $recommendations,
    "dns_ip" => $dnsInfo,
    "domain_info" => $whoisInfo,
    "shodan_info" => $shodanInfo,
    "technical_logs" => $technicalLogs
]);
exit;
?>
