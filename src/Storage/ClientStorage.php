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

    public function construct(RedisClientInterface $connection, RedisKeysManager $manager)
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

        $pipe->execute;

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
    public function read($clientId, $password, bool $showSecret = null)
    {
        $client = null;

        // Check if passowrd match with the Client's
        if($password == $this->connection->hget($this->redisKeysManager->makeKey(ClientKey::make($clientId)), 'password')) {

            if($showSecret) {
                $client = $this->connection->hgetall($this->redisKeysManager->makeKey(ClientKey::make($clientId)));

                $client  = new ClientEntity($clientid, $client['name'], $client['password'], $client['secret'], $client['redirect_uri'], $client['grant_type']);
            } else {
                $client = $this->connection->hmget($this->redisKeysManager->makeKey(ClientKey::make($clientId)), 'name', 'redirect_uri', 'grant_type');

                $client  = new ClientEntity($clientid, $client['name'], null, null, $client['redirect_uri'], $client['grant_type']);
            }
        }

        // If we set all ClientEntity constructor defaults to null then this here is possible
        //return $client ? new ClientEntity($clientId, extract($client, EXTR_OVERWRITE)) : new ClientEntity();

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
}
