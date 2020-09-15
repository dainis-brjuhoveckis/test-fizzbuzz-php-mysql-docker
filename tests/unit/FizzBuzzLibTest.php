<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

include_once(__DIR__ . '/../../src/databases/lib.php');

final class FizzBuzzLibTest extends TestCase
{
    public function testFizzBuzzAbc1(): void
    {
        self::assertEquals((object)['a'=>1, 'b'=>1, 'c'=>1], fizzBuzzAbc(1) );
    }

    public function testFizzBuzzAbc15(): void
    {
        self::assertEquals((object)['a'=>15, 'b'=>0, 'c'=>0], fizzBuzzAbc(15) );
    }

    /**
     * @dataProvider abc1To7Provider
     */
    public function testFizzBuzzAbc1To7(int $i, Object $expected): void
    {
        self::assertEquals(fizzBuzzAbc($i), $expected);
    }

    public function abc1To7Provider()
    {
        return [
            [1, (object)['a'=>1, 'b'=>1, 'c'=>1]],
            [2, (object)['a'=>2, 'b'=>2, 'c'=>2]],
            [3, (object)['a'=>3, 'b'=>0, 'c'=>3]],
            [4, (object)['a'=>4, 'b'=>1, 'c'=>4]],
            [5, (object)['a'=>5, 'b'=>2, 'c'=>0]],
            [6, (object)['a'=>6, 'b'=>0, 'c'=>1]],
            [7, (object)['a'=>7, 'b'=>1, 'c'=>2]],
        ];
    }
}
