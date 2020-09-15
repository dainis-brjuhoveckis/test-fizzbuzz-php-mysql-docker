<?php
include_once(__DIR__. '/../../../app.php');

header( 'Content-Type: application/json');

$page = $_GET['page'] ?? JSON_PAGE_DEFAULT;
$pageSize = $_GET['page_size'] ?? JSON_PAGE_SIZE_DEFAULT;

if ($page < 1) {
    $page = JSON_PAGE_DEFAULT;
}
if ($pageSize < 1) {
    $pageSize = JSON_PAGE_SIZE_DEFAULT;
}

$stmt = $pdo->prepare('SELECT `a`, `b`, `c` 
                       FROM `' . FIZZ_BUZZ_TABLE . '` ORDER BY `a` ASC 
                       LIMIT :limit OFFSET :offset ;');
$limit = $pageSize;
$offset = ($page - 1) * $pageSize;
$stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($result);

?>