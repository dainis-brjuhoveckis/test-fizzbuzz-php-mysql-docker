<?php declare(strict_types=1);

include_once(__DIR__. '/config.php'); 

include_once(__DIR__. '/consts.php');
include_once(__DIR__. '/lib.php');

$pdo = new PDO(CONFIG["db"]["dsn"], CONFIG["db"]["user"], CONFIG["db"]["pass"], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

?>