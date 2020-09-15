<?php
include_once(__DIR__. '/../../../app.php');

if ( tableExists($pdo, FIZZ_BUZZ_TABLE) ) {
    // do nothing
} else {    
    createFizzBuzzTable($pdo, FIZZ_BUZZ_TABLE);
    insertFizzBuzzData($pdo, FIZZ_BUZZ_TABLE);
}

function createFizzBuzzTable(PDO $pdo, string $table) {
    $pdo->prepare("
            CREATE TABLE `${table}` (
                `a` int NOT NULL,
                `b` int NOT NULL,
                `c` int NOT NULL
            ) ENGINE=InnoDB;
        ")->execute();

        $pdo->prepare("CREATE INDEX `IDX_${table}_A` ON `${table}` (`a`);")
            ->execute();
}

function insertFizzBuzzData(PDO $pdo, string $table) {
    $stmt = $pdo->prepare("INSERT INTO `${table}` (`a`, `b`, `c`) 
                           VALUES (:a, :b, :c);");

    $pdo->beginTransaction();
    for ($i = FIZZ_BUZZ_MIN ; $i <= FIZZ_BUZZ_MAX; $i++) {          
        $abc = fizzBuzzAbc($i);
        $stmt->bindParam(':a', $abc->a, PDO::PARAM_INT);
        $stmt->bindParam(':b', $abc->b, PDO::PARAM_INT);    
        $stmt->bindParam(':c', $abc->c, PDO::PARAM_INT);
        $stmt->execute();    
    }
    $pdo->commit();
}

?>