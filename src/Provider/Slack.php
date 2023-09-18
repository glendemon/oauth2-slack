<?php

namespace Chadhutchins\OAuth2\Client\Provider;

use Chadhutchins\OAuth2\Client\Provider\Exception\SlackProviderException;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use Psr\Http\Message\ResponseInterface;

/**
 * Class Slack
 *
 * @author Adam Paterson <hello@adampaterson.co.uk>
 * @author Chad Hutchins <hutchins.chad@gmail.com>
 *
 * @package Chadhutchins\OAuth2\Client\Provider
 */
class Slack extends AbstractProvider
{
    use BearerAuthorizationTrait;

    /**
     * Returns the base URL for authorizing a client.
     *
     * @return string
     */
    public function getBaseAuthorizationUrl()
    {
        return 'https://slack.com/openid/connect/authorize';
    }

    /**
     * Returns the base URL for requesting an access token.
     *
     * @param array $params
     *
     * @return string
     */
    public function getBaseAccessTokenUrl(array $params)
    {
        return 'https://slack.com/api/openid.connect.token';
    }

    /**
     * Returns the URL for requesting the resource owner's details.
     *
     * @param AccessToken $token
     *
     * @return string
     */
    public function getResourceOwnerDetailsUrl(AccessToken $token)
    {
        return 'https://slack.com/api/openid.connect.userInfo';
    }

    /**
     * Checks a provider response for errors.
     *
     * @param ResponseInterface $response
     * @param array|string      $data Parsed response data
     *
     * @return \League\OAuth2\Client\Provider\Exception\IdentityProviderException
     * @throws \Chadhutchins\OAuth2\Client\Provider\Exception\SlackProviderException
     */
    protected function checkResponse(ResponseInterface $response, $data)
    {
        if (isset($data['ok']) && $data['ok'] === false) {
            return SlackProviderException::fromResponse($response, $data['error']);
        }
    }

    /**
     * Create new resources owner using the generated access token.
     *
     * @param array       $response
     * @param AccessToken $token
     *
     * @return SlackResourceOwner
     */
    protected function createResourceOwner(array $response, AccessToken $token)
    {
        return new SlackResourceOwner($response);
    }

    /**
     * @return array
     */
    protected function getDefaultScopes()
    {
        return ['openid', 'profile'];
    }
}
