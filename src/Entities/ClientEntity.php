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

    /**
     * possible Grant Types:
     * Client Credentials           client_credentials (default)
     * Authori`ation Code           authorization_code
     * Resource Owner Credentials   password
     * Refresh Token                refresh_token
     *
     */
    public function __construct($clientId, $name, $password, $secret, $redirectUri = null, $grantType = 'client_credentials')
    {
        $this->clientId = $clientId;
        $this->name = $name;
        $this->password = $password;
        $this->secret = $secret;
        $this->redirectUri = $redirectUri;
        $this->grantType = $grantType;
    }

    public function getClientId()
    {
        return $this->clientId;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getSecret()
    {
        return $this->secret;
    }

    public function getRedirectUri()
    {
        return $this->redirectUri;
    }

    public function getGrantType()
    {
        return $this->grantType;
    }
}
