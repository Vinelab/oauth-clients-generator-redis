<?php

namespace Vinelab\ClientGenerator\Tests;

use Mockery as M;
use PHPUnit_Framework_TestCase;
use Vinelab\Redis\RedisKeysManager;
//use Vinelab\Redis\Clients\RedisClient;
use Vinelab\ClientGenerator\Storage\ClientStorage;
//use Vinelab\ClientGenerator\Entities\ClientEntity;

/**
 * @author Kinane Domloje <kinane@vinelab.com>
 */
class ClientStorageTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->redis = M::mock('Vinelab\Redis\Interfaces\RedisClientInterface');
        $this->client = M::mock('Vinelab\ClientGenerator\Entities\ClientEntity');
        $this->storage = M::mock('Vinelab\ClientGenerator\Storage\ClientStorage');

        $this->clientStorage = new ClientStorage($this->redis, new RedisKeysManager());
    }

    public function tearDown()
    {
        M::close();
    }

    public function test_create()
    {
        $this->markTestIncomplete(
          'This test is not complete due to the fact that the uuid is been generated within the method, so we have no reference of its value to be considered for testing. In order to run this test, comment the uuid generation and set theirvalues to 123 and mysecret respectively.'
        );

        $this->redis->shouldReceive('pipeline')->andReturn($this->redis);

        $this->redis->shouldReceive('sadd')->once()->with('oauth:clients', '123')->andReturn(1);
        $this->redis->shouldReceive('hmset')->once()->with('oauth:clients:123',
            [
                'name'          => 'john',
                'password'      => 'unknownpassword',
                'secret'        => 'mysecret',
                'redirect_uri'  => 'uri',
                'grant_type'    => 'client_credentials'
            ])->andReturn(1);
        $this->redis->shouldReceive('execute')->andReturn([1, 1]);

        $this->storage->shouldReceive('create')->with('john', 'unknownpassword', 'uri', 'client_credentials')->andReturn($this->client);
        $this->storage->shouldReceive('generateUuid')->andReturn('123');
        $this->storage->shouldReceive('generateUuid')->andReturn('my secret');

        $client = $this->clientStorage->create('john', 'unknownpassword', 'uri', 'client_credentials');

        $this->assertInstanceOf('Vinelab\ClientGenerator\Entities\ClientEntity', $client);
    }

    public function test_read_with_password()
    {
        $this->redis->shouldReceive('hget')->once()->with('oauth:clients:123', 'password')->andReturn('unknownpassword');

        $this->redis->shouldReceive('hmget')->with('oauth:clients:123', 'name', 'secret', 'redirect_uri', 'grant_type')->andReturn(
            [
                'name'          => 'john',
                'secret'        => 'mysecret',
                'redirect_uri'  => 'uri',
                'grant_type'    => 'client_credentials'
            ]);

        $this->redis->shouldReceive('hmget')->with('oauth:clients:123', 'name', 'redirect_uri', 'grant_type')->andReturn(
            [
                'name'          => 'john',
                'redirect_uri'  => 'uri',
                'grant_type'    => 'client_credentials'
            ]);

        $this->storage->shouldReceive('read')->with('123', 'unknownpassword')->andReturn($this->client);

        $client = $this->clientStorage->read('123', 'unknownpassword');

        $this->assertInstanceOf('Vinelab\ClientGenerator\Entities\ClientEntity', $client);
    }

    public function test_read_without_password()
    {
        $this->redis->shouldReceive('hmget')->with('oauth:clients:123', 'name', 'redirect_uri', 'grant_type')->andReturn(
            [
                'name'          => 'john',
                'redirect_uri'  => 'uri',
                'grant_type'    => 'client_credentials'
            ]);

        $this->storage->shouldReceive('read')->with('123')->andReturn($this->client);

        $client = $this->clientStorage->read('123');

        $this->assertInstanceOf('Vinelab\ClientGenerator\Entities\ClientEntity', $client);
    }

    public function test_list_clients()
    {
        $this->redis->shouldReceive('smembers')->once()->andReturn(['123', '456', '789']);

        $this->redis->shouldReceive('hget')->with('oauth:clients:123', 'name')->andReturn('john');
        $this->redis->shouldReceive('hget')->with('oauth:clients:456', 'name')->andReturn('oliver');
        $this->redis->shouldReceive('hget')->with('oauth:clients:789', 'name')->andReturn('stewart');

        $clients = $this->clientStorage->listClients();

        $this->assertNotEmpty($clients);
    }

    public function test_delete()
    {
        $this->redis->shouldReceive('del')->once()->with('oauth:clients:123')->andReturn('1');

        $result = $this->clientStorage->delete('123');

        $this->assertTrue($result);
    }
}
