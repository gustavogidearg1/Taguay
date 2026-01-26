<?php
// public/api_vw_contratos_detalle.php
declare(strict_types=1);

/* =========================
   CONFIG (ideal: mover a /home.../secure/config.php)
   =========================
$DB_HOST = '127.0.0.1';
$DB_PORT = 3306;
$DB_NAME = 'taguay_BdSistema';
$DB_USER = 'taguay_Usuario';
$DB_PASS = 'Taguay2552.';
*/

$DB_HOST = '127.0.0.1';
$DB_PORT = 3306;
$DB_NAME = 'taguay_bdsistem';
$DB_USER = 'root';
$DB_PASS = '';


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

function parse_int(?string $s): ?int {
  if ($s === null) return null;
  $s = trim($s);
  if ($s === '') return null;
  if (!preg_match('/^\d+$/', $s)) return null;
  return (int)$s;
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

// Filtro por fecha (campo "fecha" del contrato)
$from = parse_date_ymd($_GET['from'] ?? null);
$to   = parse_date_ymd($_GET['to'] ?? null);

// Filtros por IDs
$campania_id     = parse_int($_GET['campania_id'] ?? null);
$cultivo_id      = parse_int($_GET['cultivo_id'] ?? null);
$moneda_id       = parse_int($_GET['moneda_id'] ?? null);
$organizacion_id = parse_int($_GET['organizacion_id'] ?? null);

// Filtros por valores
$nro_contrato = parse_int($_GET['nro_contrato'] ?? null);
$num_forward  = parse_int($_GET['num_forward'] ?? null);

// Búsqueda libre (nombre/código organización, vendedor, nro, forward)
$q = trim((string)($_GET['q'] ?? ''));
$q = $q !== '' ? $q : null;

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

  if ($campania_id !== null) {
    $where[] = "campania_id = ?";
    $types .= 'i';
    $params[] = $campania_id;
  }
  if ($cultivo_id !== null) {
    $where[] = "cultivo_id = ?";
    $types .= 'i';
    $params[] = $cultivo_id;
  }
  if ($moneda_id !== null) {
    $where[] = "moneda_id = ?";
    $types .= 'i';
    $params[] = $moneda_id;
  }
  if ($organizacion_id !== null) {
    $where[] = "organizacion_id = ?";
    $types .= 'i';
    $params[] = $organizacion_id;
  }

  if ($nro_contrato !== null) {
    $where[] = "nro_contrato = ?";
    $types .= 'i';
    $params[] = $nro_contrato;
  }
  if ($num_forward !== null) {
    $where[] = "num_forward = ?";
    $types .= 'i';
    $params[] = $num_forward;
  }

  if ($q !== null) {
    // LIKE seguro con parámetro
    $where[] = "("
      . "organizacion_name LIKE ? OR organizacion_codigo LIKE ? OR "
      . "vendedor LIKE ? OR "
      . "CAST(nro_contrato AS CHAR) LIKE ? OR "
      . "CAST(num_forward AS CHAR) LIKE ?"
      . ")";
    $types .= 'sssss';
    $like = '%' . $q . '%';
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
  }

  $whereSql = count($where) ? ('WHERE ' . implode(' AND ', $where)) : '';

  // ✅ Selecciono un set razonable (podés agregar más columnas si querés)
  $sql = "
    SELECT
      id,
      nro_contrato,
      num_forward,
      fecha,
      entrega_inicial,
      entrega_final,

      campania_id,
      campania_name,
      campania_codfinneg,

      cultivo_id,
      cultivo_name,
      cultivo_codfinneg,

      moneda_id,
      moneda_name,
      moneda_codfinne,

      organizacion_id,
      organizacion_codigo,
      organizacion_name,

      vendedor,

      caracteristica_precio,
      formacion_precio,
      condicion_precio,
      condicion_pago,
      lista_grano,
      destino,
      formato,
      disponible_tipo,

      cantidad_tn,
      precio,
      precio_fijado,

      comision,
      paritaria,
      volatil,
      obs,
      importante,

      created_by,
      created_by_name,
      created_by_email,
      updated_by,
      updated_by_name,
      updated_by_email,

      created_at,
      updated_at

    FROM vw_contratos_detalle
    $whereSql
    ORDER BY fecha DESC, id DESC
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
      'source' => 'vw_contratos_detalle',
      'limit' => $limit,
      'offset' => $offset,
      'count' => count($rows),
      'from' => $from,
      'to' => $to,
      'filters' => [
        'campania_id' => $campania_id,
        'cultivo_id' => $cultivo_id,
        'moneda_id' => $moneda_id,
        'organizacion_id' => $organizacion_id,
        'nro_contrato' => $nro_contrato,
        'num_forward' => $num_forward,
        'q' => $q,
      ],
      'server_time' => date('c'),
    ],
    'data' => $rows,
  ]);

} catch (Throwable $e) {
  json_out(['ok' => false, 'error' => 'Server error', 'detail' => $e->getMessage()], 500);
}
