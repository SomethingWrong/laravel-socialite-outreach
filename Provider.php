<?php

namespace SocialiteProviders\Outreach;

use GuzzleHttp\ClientInterface;
use SocialiteProviders\Manager\OAuth2\AbstractProvider;
use SocialiteProviders\Manager\OAuth2\User;

class Provider extends AbstractProvider
{
    /**
     * Unique Provider Identifier.
     */
    const IDENTIFIER = 'OUTREACH';

    /**
     * {@inheritdoc}
     */
    protected $scopeSeparator = ' ';

    /**
     * {@inheritdoc}
     */
    protected $scopes = [];

    /**
     * {@inheritdoc}
     */
    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase('https://api.outreach.io/oauth/authorize', $state);
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenUrl()
    {
        return 'https://api.outreach.io/oauth/token';
    }

    /**
     * {@inheritdoc}
     */
    public function getAccessTokenResponse($code)
    {
        $postKey = (version_compare(ClientInterface::VERSION, '6') === 1) ? 'form_params' : 'body';

        $response = $this->getHttpClient()->post($this->getTokenUrl(), [
            'headers' => ['Accept' => 'application/json', 'Authorization' => 'Basic '.base64_encode($this->clientId.':'.$this->clientSecret)],
            $postKey  => $this->getTokenFields($code),
        ]);

        $this->credentialsResponseBody = json_decode($response->getBody(), true);

        return json_decode($response->getBody(), true);
    }

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get('https://api.outreach.io/api/v2', [
            'headers' => [
                'Authorization' => 'Bearer '.$token,
            ],
        ]);

        return json_decode($response->getBody(), true);
    }

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user)
    {
        return (new User())->setRaw($user)->map([
            'id'       => $user['user']['meta']['user']['id'],
            'name'     => $user['user']['meta']['user']['firstname'] . ' ' . $user['user']['meta']['user']['firstname'],
            'email'    => $user['user']['meta']['user']['email'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenFields($code)
    {
        return array_merge(parent::getTokenFields($code), [
            'grant_type' => 'authorization_code',
        ]);
    }
}
