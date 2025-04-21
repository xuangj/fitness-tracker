<!--Olivia Chambers-->
<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  http_response_code(401);
  exit;
}
$db = pg_connect("…");
header('Content-Type: application/json');
$res = pg_query_params(
  $db,
  "SELECT … FROM activities WHERE userid=$1 ORDER BY activity_datetime DESC",
  [$_SESSION['user_id']]
);
echo json_encode(pg_fetch_all($res) ?: []);
