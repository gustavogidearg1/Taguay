<?php
// public/api_vista_lluvia.php
declare(strict_types=1);

/* =========================
   CONFIG (ideal: mover a /home.../secure/config.php)
   ========================= */
$DB_HOST = '127.0.0.1';
$DB_PORT = 3306;
$DB_NAME = 'taguay_BdSistema';
$DB_USER = 'taguay_Usuario';
$DB_PASS = 'Taguay2552.';

$API_TOKEN = 'Taguay2025';

$MAX_LIMIT = 5000;

/* =========================
   HELPERS
   ========================= */
function json_out($data, int $status = 200): void {
  http_response_code($status);
  header('Content-Type: application/json; charset=utf-8');
  header('Cache-Control: no-store');
  echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
  exit;
}

function parse_date_ymd(?string $s): ?string {
  if ($s === null) return null;
  $s = trim($s);
  if ($s === '') return null;
  // Acepta YYYY-MM-DD
  if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $s)) return null;
  return $s;
}

/* =========================
   AUTH
   ========================= */
$token = trim((string)($_GET['token'] ?? ''));
if ($token === '' || !hash_equals($API_TOKEN, $token)) {
  json_out(['ok' => false, 'error' => 'Unauthorized'], 401);
}

/* =========================
   INPUT
   ========================= */
$limit  = isset($_GET['limit']) ? (int)$_GET['limit'] : 1000;
$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;

if ($limit < 1) $limit = 1;
if ($limit > $MAX_LIMIT) $limit = $MAX_LIMIT;
if ($offset < 0) $offset = 0;

// Filtros opcionales por fecha
$from = parse_date_ymd($_GET['from'] ?? null);
$to   = parse_date_ymd($_GET['to'] ?? null);

/* =========================
   QUERY
   ========================= */
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
  $mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME, $DB_PORT);
  $mysqli->set_charset('utf8mb4');

  $where = [];
  $types = '';
  $params = [];

  if ($from !== null) {
    $where[] = "fecha >= ?";
    $types .= 's';
    $params[] = $from;
  }
  if ($to !== null) {
    $where[] = "fecha <= ?";
    $types .= 's';
    $params[] = $to;
  }

  $whereSql = count($where) ? ('WHERE ' . implode(' AND ', $where)) : '';

  // Ojo: vista_lluvia debe existir como VIEW
  $sql = "
    SELECT
      id,
      establecimiento_id,
      establecimiento_nombre,
      fecha,
      hora,
      mm,
      comentario,
      user_id,
      user_name
    FROM vista_lluvia
    $whereSql
    ORDER BY fecha DESC, hora DESC, id DESC
    LIMIT ? OFFSET ?
  ";

  // limit/offset
  $types .= 'ii';
  $params[] = $limit;
  $params[] = $offset;

  $stmt = $mysqli->prepare($sql);
  $stmt->bind_param($types, ...$params);
  $stmt->execute();

  $res = $stmt->get_result();
  $rows = [];
  while ($row = $res->fetch_assoc()) {
    $rows[] = $row;
  }

  json_out([
    'ok' => true,
    'meta' => [
      'source' => 'vista_lluvia',
      'limit' => $limit,
      'offset' => $offset,
      'count' => count($rows),
      'from' => $from,
      'to' => $to,
      'server_time' => date('c')
    ],
    'data' => $rows
  ]);

} catch (Throwable $e) {
  json_out(['ok' => false, 'error' => 'Server error', 'detail' => $e->getMessage()], 500);
}
