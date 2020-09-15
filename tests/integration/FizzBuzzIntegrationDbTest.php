<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

include_once(__DIR__ . '/../../src/dbs/lib.php');

final class FizzBuzzIntegrationDbTest extends TestCase
{
    private PDO $pdo;

    public function setUp(): void
    {
        $this->pdo = new PDO('mysql:host=mysql;dbname=foo', 'foo', 'foo', [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    }

    public function testTableRowCount1000000(): void
    {
        $stmt = $this->pdo->prepare('SELECT count(*) as cnt FROM `bar`');
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        self::assertEquals(1_000_000, (int)$result[0]["cnt"]);
    }

    public function testTableRowA1(): void
    {
        $stmt = $this->pdo->prepare('SELECT `a`, `b`, `c` FROM `bar` where `a` = 1');
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        self::assertEquals(['a'=>'1', 'b'=>'1', 'c'=>'1'], $result[0]);
    }

    public function testTableRowA1000000(): void
    {
        $stmt = $this->pdo->prepare(
                'SELECT `a`, `b`, `c` FROM `bar` where `a` = 1000000');
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        self::assertEquals(['a'=>'1000000', 'b'=>'1', 'c'=>'0'], $result[0]);
    }

    public function testTableRowAFrom1To7(): void
    {
        $stmt = $this->pdo->prepare(
                'SELECT `a`, `b`, `c` FROM `bar` where `a` between 1 and 7 order by `a`');
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $expected = [
                ['a'=>'1', 'b'=>'1', 'c'=>'1'],
                ['a'=>'2', 'b'=>'2', 'c'=>'2'],
                ['a'=>'3', 'b'=>'0', 'c'=>'3'],
                ['a'=>'4', 'b'=>'1', 'c'=>'4'],
                ['a'=>'5', 'b'=>'2', 'c'=>'0'],
                ['a'=>'6', 'b'=>'0', 'c'=>'1'],
                ['a'=>'7', 'b'=>'1', 'c'=>'2'],
            ];

        self::assertEquals($expected, $result);
    }


    public function testTableRowsAFrom123456To123458(): void
    {
        $stmt = $this->pdo->prepare(
            'SELECT `a`, `b`, `c` FROM `bar`
                    where `a`
                    between 123456 and 123458 order by `a`');
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $expected = [
                ['a'=>'123456', 'b'=>'0', 'c'=>'1'],
                ['a'=>'123457', 'b'=>'1', 'c'=>'2'],
                ['a'=>'123458', 'b'=>'2', 'c'=>'3'],
            ];

        self::assertEquals($expected, $result);
    }



    public function testTableRowsAFrom999998To1000000(): void
    {
        $stmt = $this->pdo->prepare(
            'SELECT `a`, `b`, `c` FROM `bar`
                    where `a`
                    between 999998 and 1000000 order by `a`');
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $expected = [
                ['a'=> '999998', 'b'=>'2', 'c'=>'3'],
                ['a'=> '999999', 'b'=>'0', 'c'=>'4'],
                ['a'=>'1000000', 'b'=>'1', 'c'=>'0'],
            ];

        self::assertEquals($expected, $result);
    }

}
