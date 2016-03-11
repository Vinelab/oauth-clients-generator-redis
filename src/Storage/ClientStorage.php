<?php

namespace Vinelab\ClientGenerator\Storage;

use Vinelab\Redis\Keys\ClientKey;
use Vinelab\Redis\RedisKeysManager;
use Vinelab\ClientGenerator\Entities\ClientEntity;
use Vinelab\ClientGenerator\Traits\GeneratorTrait;
use Vinelab\Redis\Interfaces\RedisClientInterface;

/**
 * @author Kinane Domloje <kinane@vinelab.com>
 */
class ClientStorage
{
    use GeneratorTrait;

    private $connection;
    private $redisKeysManager;

    public function __construct(RedisClientInterface $connection, RedisKeysManager $manager)
    {
        $this->connection = $connection;
        $this->redisKeysManager = $manager;
    }

    /**
     * Create an oauth Client
     *
     * @param string $name
     * @param string $password
     * @param string $redirectUri
     * @param string $grantType
     *
     * @return Vinelab\ClientGenerator\Entities\ClientEntity
     */
    public function create($name, $password, $redirectUri = null, $grantType = 'client_credentials')
    {
        $clientId = $this->generateUuid();
        $secret = $this->generateUuid();

        $pipe = $this->connection->pipeline();

        // Add $clientId to clients set
        $pipe->sadd($this->redisKeysManager->makeKey(ClientKey::make()), $clientId);

        // Add client hash
        $pipe->hmset($this->redisKeysManager->makeKey(ClientKey::make($clientId)),
            [
                'name'          => $name,
                'password'      => $password,
                'secret'        => $secret,
                'redirect_uri'  => $redirectUri,
                'grant_type'    => $grantType
            ]);

        $pipe->execute();

        return new ClientEntity($clientId, $name, $password, $secret, $redirectUri, $grantType);
    }

    /**
     * Read oauth Client
     *
     * @param string $clientId
     * @param string $password
     * @param bool   $showSecret
     *
     * @return Vinelab\ClientGenerator\Entities\ClientEntity
     */
    public function read($clientId, $password = null)
    {
        $client = new ClientEntity(null, null, null, null, null, null);

        // Check if password match with the Client's
        if($password && $password == $this->connection->hget($this->redisKeysManager->makeKey(ClientKey::make($clientId)), 'password')) {
            $hash = $this->connection->hmget($this->redisKeysManager->makeKey(ClientKey::make($clientId)), 'name', 'secret', 'redirect_uri', 'grant_type');

            $client  = new ClientEntity($clientId, $hash['name'], null, $hash['secret'], $hash['redirect_uri'], $hash['grant_type']);
        } else {
            $hash = $this->connection->hmget($this->redisKeysManager->makeKey(ClientKey::make($clientId)), 'name', 'redirect_uri', 'grant_type');

            $client  = new ClientEntity($clientId, $hash['name'], null, null, $hash['redirect_uri'], $hash['grant_type']);
        }

        return $client;
    }

    /**
     * List all oauth Clients
     *
     * @return array Vinelab\ClientGenerator\Entities\ClientEntity
     */
    public function listClients()
    {
        // Get all clients ids
        $clientsIds = $this->connection->smembers($this->redisKeysManager->makeKey(ClientKey::make()));

        $clients = [];
        foreach ($clientsIds as $clientId) {
            $name = $this->connection->hget($this->redisKeysManager->makeKey(ClientKey::make($clientId)), 'name');
            $clients[] = new ClientEntity($clientId, $name, null, null);
        }

        return $clients;
    }

    /**
     * Delete an oauth client
     *
     * @param string $clientId
     *
     * @return bool
     */
    public function delete($clientId)
    {
        return (bool) $this->connection->del($this->redisKeysManager->makeKey(ClientKey::make($clientId)));
    }

    /**
     * Update a client's secret
     *
     * @param string $clientId
     * @param string $secret
     *
     * @return Vinelab\ClientGenerator\Entities\ClientEntity
     */
    public function updateSecret($clientId, $secret)
    {
        $pipe = $this->connection->pipeline();

        $pipe->hset($this->redisKeysManager->makeKey(ClientKey::make($clientId)), 'secret', $secret);

        $pipe->hgetall($this->redisKeysManager->makeKey(ClientKey::make($clientId)));

        $result = $pipe->execute();

        $clientHash = end($result);

        return $this->mapClient($clientHash);
    }

    /**
     * Map a Client's hash into a Client Entity
     *
     * @param array $hash
     *
     * @return Vinelab\ClientGenerator\Entities\ClientEntity
     */
    private function mapClient($hash)
    {
        extract($hash);

        return new ClientEntity($client_id, $name, null, (isset($secret) ? $secret : null), $redirect_uri, $grant_type);
    }
}
