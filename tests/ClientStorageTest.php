<?php

namespace Vinelab\ClientGenerator\Tests;

use Mockery as M;
use PHPUnit_Framework_TestCase;
use Vinelab\Redis\RedisKeysManager;
use Vinelab\Redis\Clients\RedisClient;
use Vinelab\ClientGenerator\Storage\ClientStorage;

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
        $this->redis->shouldReceive('pipeline')->andReturn($this->redis);

        $this->redis->shouldReceive('sadd')->once()->with('oauth:clients', '123')->andReturn(1);
        $this->redis->shouldReceive('hmset')->once()->with('oauth:clients:123',
            [
                'client_id'     => '123',
                'name'          => 'john',
                'password'      => 'unknownpassword',
                'secret'        => 'secret',
                'redirect_uri'  => 'uri',
                'grant_type'    => 'client_credentials'
            ])->andReturn(1);

        $this->redis->shouldReceive('execute')->andReturn([1, 1]);

        $client = $this->clientStorage->create('123', 'john', 'unknownpassword', 'secret', 'uri', 'client_credentials');

        $this->assertInstanceOf('Vinelab\ClientGenerator\Entities\ClientEntity', $client);
        $this->assertEquals('123', $client->getClientId());
        $this->assertEquals('john', $client->getName());
        $this->assertNull($client->getPassword());
        $this->assertEquals('secret', $client->getSecret());
        $this->assertEquals('uri', $client->getRedirectUri());
        $this->assertEquals('client_credentials', $client->getGrantType());
    }

    public function test_read_with_password()
    {
        $this->redis->shouldReceive('hgetall')->with('oauth:clients:123')->andReturn(
            [
                'client_id'     => '123',
                'name'          => 'john',
                'password'      => 'unknownpassword',
                'secret'        => 'mysecret',
                'redirect_uri'  => 'uri',
                'grant_type'    => 'client_credentials'
            ]);

        $client = $this->clientStorage->read('123', 'unknownpassword');

        $this->assertInstanceOf('Vinelab\ClientGenerator\Entities\ClientEntity', $client);
        $this->assertEquals('123', $client->getClientId());
        $this->assertEquals('john', $client->getName());
        $this->assertNull($client->getPassword());
        $this->assertEquals('mysecret', $client->getSecret());
        $this->assertEquals('uri', $client->getRedirectUri());
        $this->assertEquals('client_credentials', $client->getGrantType());
    }

    public function test_read_without_password()
    {
        $this->redis->shouldReceive('hgetall')->with('oauth:clients:123')->andReturn(
            [
                'client_id'     => '123',
                'name'          => 'john',
                'password'      => 'unknownpassword',
                'secret'        => 'mysecret',
                'redirect_uri'  => 'uri',
                'grant_type'    => 'client_credentials'
            ]);

        $client = $this->clientStorage->read('123');

        $this->assertInstanceOf('Vinelab\ClientGenerator\Entities\ClientEntity', $client);
        $this->assertEquals('123', $client->getClientId());
        $this->assertEquals('john', $client->getName());
        $this->assertNull($client->getPassword());
        $this->assertNull($client->getSecret());
        $this->assertEquals('uri', $client->getRedirectUri());
        $this->assertEquals('client_credentials', $client->getGrantType());
    }

    /**
     * @expectedException        Vinelab\ClientGenerator\Exceptions\RedisKeyNotFoundException
     * @expectedExceptionCode    404
     */
    public function test_read_with_wrong_id()
    {
        $this->redis->shouldReceive('hgetall')->with('oauth:clients:123')->andReturn([]);

        $client = $this->clientStorage->read('123');
    }

    public function test_all()
    {
        $this->redis->shouldReceive('smembers')->once()->andReturn(['123', '456', '789']);

        $this->redis->shouldReceive('hgetall')->with('oauth:clients:123')->andReturn(
            [
                'client_id'     => '123',
                'name'          => 'john',
                'password'      => 'unknownpassword',
                'secret'        => 'mysecret',
                'redirect_uri'  => 'uri',
                'grant_type'    => 'client_credentials'
            ]);

        $this->redis->shouldReceive('hgetall')->with('oauth:clients:456')->andReturn(
            [
                'client_id'     => '456',
                'name'          => 'oliver',
                'password'      => 'unknownpass',
                'secret'        => 'mysecrety',
                'redirect_uri'  => 'uri',
                'grant_type'    => 'client_credentials'
            ]);

        $this->redis->shouldReceive('hgetall')->with('oauth:clients:789')->andReturn(
            [
                'client_id'     => '789',
                'name'          => 'stewart',
                'password'      => 'unknown',
                'secret'        => 'secret',
                'redirect_uri'  => 'uri',
                'grant_type'    => 'client_credentials'
            ]);

        $clients = $this->clientStorage->all();

        $this->assertNotEmpty($clients);
        foreach ($clients as $client) {
            $this->assertInstanceOf('Vinelab\ClientGenerator\Entities\ClientEntity', $client);
        }
    }

    public function test_update_secret_with_password()
    {
        $this->redis->shouldReceive('pipeline')->andReturn($this->redis);

        $this->redis->shouldReceive('hget')->once()->with('oauth:clients:123', 'password')->andReturn('unknownpassword');
        $this->redis->shouldReceive('hset')->once()->with('oauth:clients:123', 'secret', 'newsecret')->andReturn(0);

        $update = $this->clientStorage->updateSecret('123', 'newsecret', 'unknownpassword');

        $this->assertTrue($update);
    }

    public function test_update_secret_without_password()
    {
        $this->redis->shouldReceive('hget')->once()->with('oauth:clients:123', 'password')->andReturn('unknownpassword');

        $update = $this->clientStorage->updateSecret('123', 'newsecret');

        $this->assertNull($update);
    }

    public function test_delete()
    {
        $this->redis->shouldReceive('del')->once()->with('oauth:clients:123')->andReturn('1');

        $result = $this->clientStorage->delete('123');

        $this->assertTrue($result);
    }
}
