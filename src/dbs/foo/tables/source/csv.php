<?php
include_once(__DIR__. '/../../../app.php');

header( 'Content-Type: application/csv' );
header( 'Content-Disposition: attachment; filename="' . CSV_FILE_NAME . '";' );

$stmt = $pdo->prepare('SELECT `a`, `b`, `c` FROM `source` ORDER BY `a` ASC;');
$stmt->execute();

$fp = fopen('php://output', 'w');
fputcsv($fp, CSV_HEADER);

$cnt = 0;
while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
    fputcsv($fp, $row);
    if ( ++$cnt % CSV_CHUNK_ROWS === 0) {
        flush();
    }
}

fclose($fp);

?>