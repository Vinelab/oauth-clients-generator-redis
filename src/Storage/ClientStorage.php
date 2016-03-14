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
     * @parsm string $clientId
     * @param string $name
     * @param string $password
     * @param string $secret
     * @param string $redirectUri
     * @param string $grantType
     *
     * @return Vinelab\ClientGenerator\Entities\ClientEntity
     */
    public function create($clientId, $name, $password, $secret, $redirectUri = null, $grantType = 'client_credentials')
    {
        $payload = [
                        'client_id'     => $clientId,
                        'name'          => $name,
                        'password'      => $password,
                        'secret'        => $secret,
                        'redirect_uri'  => $redirectUri,
                        'grant_type'    => $grantType
                    ];

        $pipe = $this->connection->pipeline();

        // Add $clientId to clients set
        $pipe->sadd($this->redisKeysManager->makeKey(ClientKey::make()), $clientId);

        // Add client hash
        $pipe->hmset($this->redisKeysManager->makeKey(ClientKey::make($clientId)), $payload);

        $pipe->execute();

        return $this->mapClient($payload);
    }

    /**
     * Read oauth Client
     *
     * @param string $clientId
     * @param string $password
     *
     * @return Vinelab\ClientGenerator\Entities\ClientEntity
     */
    public function read($clientId, $password = null)
    {
        $clientHash = $this->connection->hgetall($this->redisKeysManager->makeKey(ClientKey::make($clientId)));

        // Check if password match with the Client's
        if(!$password || $password != $clientHash['password']) {
            unset($clientHash['secret']);
        }

        return $this->mapClient($clientHash);
    }

    /**
     * List all oauth Clients
     *
     * @return array Vinelab\ClientGenerator\Entities\ClientEntity
     */
    public function all()
    {
        // Get all clients ids
        $clientsIds = $this->connection->smembers($this->redisKeysManager->makeKey(ClientKey::make()));

        $clients = [];
        foreach ($clientsIds as $clientId) {
            $clientHash = $this->connection->hgetall($this->redisKeysManager->makeKey(ClientKey::make($clientId)));
            $clients[] = $this->mapClient($clientHash);
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
     * @param string $password
     * @param string $secret
     *
     * @return Vinelab\ClientGenerator\Entities\ClientEntity
     */
    public function updateSecret($clientId, $password, $secret)
    {
        $pipe = $this->connection->pipeline();

        if($password == $this->connection->hget($this->redisKeysManager->makeKey(ClientKey::make($clientId)), 'password')) {
            $pipe->hset($this->redisKeysManager->makeKey(ClientKey::make($clientId)), 'secret', $secret);
        }

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
