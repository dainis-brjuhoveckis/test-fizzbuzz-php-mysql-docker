<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

include_once(__DIR__ . '/../../src/dbs/lib.php');

final class FizzBuzzIntegrationWebApiTest extends TestCase
{

    private $http;

    public function setUp(): void
    {
        $this->http = new GuzzleHttp\Client(['base_uri' => 'http://php-apache:80/dbs/foo/tables/source/']);
    }

    public function tearDown(): void {
        $this->http = null;
    }

    public function testJson(): void
    {
        $response = $this->http->request('GET', 'json');
        $contentType = $response->getHeaders()["Content-Type"][0];

        self::assertEquals(200, $response->getStatusCode());        
        self::assertEquals("application/json", $contentType);
        self::assertJsonStringEqualsJsonFile( __DIR__ . '/json.json', (string) $response->getBody());
    }

    public function testJsonPage2PageSize3(): void
    {
        $response = $this->http->request('GET', 'json', [
            'query' => ['page' => 2, 'page_size' => 3]
        ]);
        $contentType = $response->getHeaders()["Content-Type"][0];

        self::assertEquals(200, $response->getStatusCode());        
        self::assertEquals("application/json", $contentType);
        self::assertJsonStringEqualsJsonFile( __DIR__ . '/jsonPage2PageSize3.json', (string) $response->getBody());
    }

    public function testCsvHeaders(): void
    {
        $response = $this->http->request('GET', 'csv', ['stream' => true]);
        $contentType = $response->getHeaders()["Content-Type"][0];
        $contentDisposition = $response->getHeaders()["Content-Disposition"][0];

        self::assertEquals(200, $response->getStatusCode());        
        self::assertEquals("application/csv", $contentType);
        self::assertEquals("attachment; filename=\"source.csv\";", $contentDisposition);
    }


    public function testCsvFileContents(): void
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'testCsvFile.guzzle-download2');
        $handle = fopen($tmpFile, 'w');

        $response = $this->http->request('GET', 'csv', ['sink' => $handle]);

        fclose($handle);
        
        $handle = fopen($tmpFile, "r");
        $first2Lines = [fgets($handle), fgets($handle)];
        fclose($handle);

        // this raised memory usage from about 6 Mb to 86 MB
        $lines = file($tmpFile);

        $last2Lines = array_slice($lines, -2);

        self::assertFileExists($tmpFile);
        self::assertEquals(["a,b,c\n", "1,1,1\n"], $first2Lines);
        self::assertEquals(1_000_000 + 1, count($lines));
        self::assertEquals(["999999,0,4\n", "1000000,1,0\n"], $last2Lines);

        unlink($tmpFile);
    }

}
