<?php
// db_test.php  (BORRAR cuando termine)
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$host = '192.185.2.250';          // desde el hosting, 127.0.0.1 suele estar bien
$port = 3306;
$db   = 'taguay_BdSistema';
$user = 'taguay_Usuario';
$pass = 'Taguay2552.';

header('Content-Type: text/plain; charset=utf-8');

try {
    $mysqli = new mysqli($host, $user, $pass, $db, $port);
    $mysqli->set_charset('utf8mb4');

    echo "OK: Conectó a MySQL\n";
    echo "Server info: " . $mysqli->server_info . "\n";

    $res = $mysqli->query("SELECT DATABASE() AS db, CURRENT_USER() AS user, @@hostname AS mysql_host");
    $row = $res->fetch_assoc();

    echo "DB: {$row['db']}\n";
    echo "MySQL current_user(): {$row['user']}\n";
    echo "MySQL @@hostname: {$row['mysql_host']}\n";

} catch (Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
