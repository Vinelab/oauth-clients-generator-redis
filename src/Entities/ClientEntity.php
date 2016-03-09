<?php

namespace Vinelab\ClientGenerator\Entities;

/**
 * @author Kinane Domloje <kinane@vinelab.com>
 */
class ClientEntity
{
    private $clientId;
    private $name;
    private $password;
    private $secret;
    private $redirectUri;
    private $grantType;

    public function __construct($clientId, $name, $password, $secret, $redirectUri = null, $grantType = 'client_credentials')
    {
        $this->clientId = $clientId;
        $this->name = $name;
        $this->password = $password;
        $this->secret = $secret;
        $this->redirectUri = $redirectUri;
        $this->grantType = $grantType;

        return $this;
    }
}
