<?php

namespace Polar\Authentication\Adapter;

use Zend\Authentication\Adapter\AbstractAdapter;
use Zend\Authentication\Result;

class Google extends AbstractAdapter
{
    private $provider;

    public function __construct(array $config)
    {

        $this->provider = new \League\OAuth2\Client\Provider\Google($config);
    }

    public function getRedirectUri()
    {
        return $this->provider->getAuthorizationUrl();
    }


    /**
     * Performs an authentication attempt
     *
     * @return \Zend\Authentication\Result
     * @throws \Zend\Authentication\Adapter\Exception\ExceptionInterface If authentication cannot be performed
     */
    public function authenticate()
    {

        $token = $this->provider->getAccessToken('authorization_code', [
            'code' => $this->getCredential()
        ]);
        try {
            /** @var \League\OAuth2\Client\Provider\GoogleUser $ownerDetails */
            $ownerDetails = $this->provider->getResourceOwner($token);
            return new Result(Result::SUCCESS, $ownerDetails);
        } catch( \Exception $e) {
            return new Result(Result::FAILURE, null, [$e->getMessage()]);
        }
    }
}