<?php

namespace Secrecy\Tests;

use PHPUnit\Framework\TestCase;
use Secrecy\Adapter\JsonFileAdapter;
use Secrecy\Exception\JsonFileLoadException;
use Secrecy\Exception\JsonFilePersistenceException;
use Secrecy\Exception\SecretAlreadyExistsException;
use Secrecy\Exception\SecretNotFoundException;

class JsonFileAdapterTest extends TestCase
{
    private function createJsonFile(array $data)
    {
        $path = tempnam(sys_get_temp_dir(), 'TEST');
        file_put_contents($path, json_encode($data));
        return $path;
    }

    public function testReadingSecrets()
    {
        $path = $this->createJsonFile([
            'secrets' => [
                'FOO' => 'BAR',
                'BAZ' => 'QUX',
                'JSON_STRING' => '{"prop":"val"}'
            ]
        ]);

        $adapter = new JsonFileAdapter($path);

        $this->assertEquals('BAR', $adapter->get('FOO'));
        $this->assertEquals('QUX', $adapter->get('BAZ'));

        // Test reading json strings
        $json = json_decode($adapter->get('JSON_STRING'));
        $this->assertEquals('val', $json->prop);

        $this->assertEquals([
            'FOO' => 'BAR',
            'BAZ' => 'QUX',
            'JSON_STRING' => '{"prop":"val"}'
        ], $adapter->list());
    }

    public function testAccessingNonExistentSecrets()
    {
        $path = $this->createJsonFile(['secrets' => []]);

        $adapter = new JsonFileAdapter($path);
        $this->expectException(SecretNotFoundException::class);
        $adapter->get('SOMETHING_SECRET');
    }

    public function testWritingSecrets()
    {
        $path = $this->createJsonFile(['secrets' => ['a' => 'b']]);
        $adapter = new JsonFileAdapter($path);

        $adapter->create('foo', 'bar');
        $adapter->create('json', '{"some": "json"}');
        $adapter->update('a', 'c');

        $this->assertEquals('c', $adapter->get('a'));
        $this->assertEquals('bar', $adapter->get('foo'));
        $this->assertEquals('{"some": "json"}', $adapter->get('json'));
        $this->assertEquals([
            'secrets' => [
                'a' => 'c',
                'foo' => 'bar',
                'json' => '{"some": "json"}'
            ]
        ], json_decode(file_get_contents($path), true));
    }

    public function testRemoveSecret()
    {
        $path = $this->createJsonFile(['secrets' => ['FOO' => 'BAR']]);
        $adapter = new JsonFileAdapter($path);

        $adapter->remove('FOO');
        $this->assertEquals([], $adapter->list());
    }

    public function  testCantUpdateSecretThatDoesntExist()
    {
        $this->expectException(SecretNotFoundException::class);

        $path = $this->createJsonFile(['secrets' => []]);
        $adapter = new JsonFileAdapter($path);
        $adapter->update('FOO', 'BAR');
    }

    public function  testCantCreateSecretThatAlreadyExists()
    {
        $this->expectException(SecretAlreadyExistsException::class);

        $path = $this->createJsonFile(['secrets' => ['FOO' => 'BAZ']]);
        $adapter = new JsonFileAdapter($path);
        $adapter->create('FOO', 'BAR');
    }

    public function testWritingToReadOnlyFile()
    {
        $this->expectException(JsonFilePersistenceException::class);

        $path = $this->createJsonFile(['secrets' => ['FOO' => 'BAR']]);

        // Make file read-only
        chmod($path, 0444);

        $adapter = new JsonFileAdapter($path);
        $adapter->create('testing', 'ok');
    }

    public function testFileDoesNotExist()
    {
        $this->expectException(JsonFileLoadException::class);
        new JsonFileAdapter('/tmp/my/json/secrets.json');
    }

    /**
     * @dataProvider invalidJsonProvider
     */
    public function testLoadingInvalidJson($data)
    {
        $this->expectException(JsonFileLoadException::class);
        new JsonFileAdapter($this->createJsonFile($data));
    }

    public function invalidJsonProvider()
    {
        yield [
            ['INVALID_JSON' => 'SCHEMA']
        ];

        yield [
            [
                'secrets' => ['Foo' => 'bar', 'Baz' => ['testing']]
            ]
        ];

        yield [[]];
    }
}