<?php

namespace Secrecy\Tests;

use PHPUnit\Framework\TestCase;
use Secrecy\Adapter\JsonFileAdapter;
use Secrecy\SecretManager;

class SecretManagerTest extends TestCase
{
    public function testGetSecret()
    {
        $adapter = $this->createMock(JsonFileAdapter::class);
        $adapter->expects($this->once())
            ->method('get')
            ->with($this->equalTo('FOO'))
            ->willReturn('BAR');

        $manager = new SecretManager($adapter);

        $this->assertEquals('BAR', $manager->get('FOO'));
    }

    public function testListSecrets()
    {
        $adapter = $this->createMock(JsonFileAdapter::class);
        $adapter->expects($this->once())
            ->method('list')
            ->willReturn(['FOO' => 'BAR']);

        $manager = new SecretManager($adapter);

        $this->assertEquals(['FOO' => 'BAR'], $manager->list());
    }

    public function testCreateSecret()
    {
        $adapter = $this->createMock(JsonFileAdapter::class);
        $adapter->expects($this->once())
            ->method('create')
            ->with($this->equalTo('FOO'), $this->equalTo('BAR'));

        $manager = new SecretManager($adapter);
        $manager->create('FOO', 'BAR');
    }

    public function testUpdateSecret()
    {
        $adapter = $this->createMock(JsonFileAdapter::class);
        $adapter->expects($this->once())
            ->method('update')
            ->with($this->equalTo('FOO'), $this->equalTo('BAR'));

        $manager = new SecretManager($adapter);
        $manager->update('FOO', 'BAR');
    }

    public function testRemoveSecret()
    {
        $adapter = $this->createMock(JsonFileAdapter::class);
        $adapter->expects($this->once())
            ->method('remove')
            ->with($this->equalTo('FOO'));

        $manager = new SecretManager($adapter);
        $manager->remove('FOO');
    }
}