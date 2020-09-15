<?php 

function tableExists(PDO $pdo, string $table): bool {    
    $stmt = $pdo->prepare('SHOW TABLES LIKE :table;');
    $stmt->bindParam(':table', $table, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_NUM);    
    return count($result) === 1 && $result[0][0] === $table;
}

function dropTable(PDO $pdo, string $table) {
    $stmt = $pdo->prepare("DROP TABLE IF EXISTS ${table};");    
    $stmt->execute();
}

function fizzBuzzAbc($i) {
    $a = $i;
    $b = $a % 3;
    $c = $a % 5;
    return (object)["a"=>$a, "b"=>$b, "c"=>$c];
}

?>