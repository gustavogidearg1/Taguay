<?php
// public_html/api.php
declare(strict_types=1);

// =========================
// CONFIG
// =========================
$DB_HOST = '127.0.0.1';            // dentro del hosting
$DB_PORT = 3306;
$DB_NAME = 'taguay_BdSistema';
$DB_USER = 'taguay_Usuario';
$DB_PASS = 'Taguay2552.';

// Token simple (cambialo por uno largo y único)
$API_TOKEN = 'Taguay2025';

// Tablas/vistas permitidas (whitelist). Agregá las que necesites.
$ALLOWED = [
  'lluvias',
  // 'otra_tabla',
  // 'vista_reporte_x',
];

// Límite máximo por request para no sobrecargar
$MAX_LIMIT = 5000;

// =========================
// HELPERS
// =========================
function json_out($data, int $status = 200): void {
  http_response_code($status);
  header('Content-Type: application/json; charset=utf-8');
  header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
  echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
  exit;
}

function get_bearer_token(): ?string {
  $hdr = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
  if (preg_match('/Bearer\s+(.+)/i', $hdr, $m)) return trim($m[1]);
  return null;
}

function client_ip(): string {
  // Nota: con proxy/CDN puede variar; sirve como referencia.
  return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
}

// =========================
// AUTH
// =========================
$token = $_GET['token'] ?? null;
if (!$token) $token = get_bearer_token();

$token = trim((string)$token);
$expected = trim((string)$API_TOKEN);

if ($token === '' || !hash_equals($expected, $token)) {
  json_out([
    'ok' => false,
    'error' => 'Unauthorized',
    'hint' => 'Token inválido o faltante'
  ], 401);
}
// =========================
// INPUT
// =========================
$table  = $_GET['table']  ?? '';
$limit  = isset($_GET['limit']) ? (int)$_GET['limit'] : 1000;
$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;

$table = trim($table);

if ($table === '') {
  json_out([
    'ok' => false,
    'error' => 'Missing parameter: table',
    'allowed' => $ALLOWED,
  ], 400);
}

if (!in_array($table, $ALLOWED, true)) {
  json_out([
    'ok' => false,
    'error' => 'Table/view not allowed',
    'allowed' => $ALLOWED,
  ], 403);
}

if ($limit < 1) $limit = 1;
if ($limit > $MAX_LIMIT) $limit = $MAX_LIMIT;
if ($offset < 0) $offset = 0;

// =========================
// QUERY
// =========================
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
  $mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME, $DB_PORT);
  $mysqli->set_charset('utf8mb4');

  // IMPORTANTE: tabla viene de whitelist, aún así la escapamos con backticks.
  $safeTable = str_replace('`', '``', $table);

  // Query paginada (sin ORDER BY por defecto; podés agregar si querés)
  $sql = "SELECT * FROM `{$safeTable}` LIMIT ? OFFSET ?";

  $stmt = $mysqli->prepare($sql);
  $stmt->bind_param('ii', $limit, $offset);
  $stmt->execute();

  $res = $stmt->get_result();
  $rows = [];
  while ($row = $res->fetch_assoc()) {
    $rows[] = $row;
  }

  json_out([
    'ok' => true,
    'meta' => [
      'db' => $DB_NAME,
      'table' => $table,
      'limit' => $limit,
      'offset' => $offset,
      'count' => count($rows),
      'server_time' => date('c'),
      'client_ip' => client_ip(),
    ],
    'data' => $rows,
  ]);

} catch (Throwable $e) {
  json_out([
    'ok' => false,
    'error' => 'Server error',
    'detail' => $e->getMessage(),
  ], 500);
}
