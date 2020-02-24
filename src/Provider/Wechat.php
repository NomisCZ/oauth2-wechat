<?php

namespace NomisCZ\OAuth2\Client\Provider;

use League\OAuth2\Client\Grant\AbstractGrant;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Tool\ArrayAccessorTrait;
use Psr\Http\Message\ResponseInterface;

class WeChat extends AbstractProvider
{
    use ArrayAccessorTrait;

    protected $appid;
    protected $secret;
    protected $redirect_uri;

    /**
     * Authorization API
     *
     * @const string
     */
    const BASE_AUTH_URL = 'https://open.weixin.qq.com/connect';

    /**
     * Acess Token API
     *
     * @const string
     */
    const BASE_ACCESS_TOKEN_URL = 'https://api.weixin.qq.com';

    /**
     * Returns the base URL for authorizing a client.
     *
     * @return string
     */
    public function getBaseAuthorizationUrl()
    {
        return self::BASE_AUTH_URL.'/qrconnect';
    }

    /**
     * Returns authorization parameters based on provided options.
     *
     * @param  array $options
     * @return array Authorization parameters
     */
    protected function getAuthorizationParameters(array $options)
    {
        $options += [
            'appid' => $this->appid
        ];

        if (!isset($options['redirect_uri'])) {
            $options['redirect_uri'] = $this->redirect_uri;
        }

        $options += [
            'response_type' => 'code'
        ];

        if (empty($options['scope'])) {
            $options['scope'] = 'snsapi_login';
        }

        if (empty($options['state'])) {
            $options['state'] = $this->getRandomState();
        }

        // Store the state as it may need to be accessed later on.
        $this->state = $options['state'];

        return $options;
    }

    /**
     * Returns the base URL for requesting an access token.
     *
     * @param array $params
     * @return string
     */
    public function getBaseAccessTokenUrl(array $params)
    {
        return self::BASE_ACCESS_TOKEN_URL.'/sns/oauth2/access_token';
    }

    /**
     * Requests an access token using a specified grant and option set.
     *
     * @param  mixed $grant
     * @param  array $options
     * @return AccessToken
     */
    public function getAccessToken($grant, array $options = [])
    {
        $grant = $this->verifyGrant($grant);
        $params = [
            'appid'     => $this->appid,
            'secret' => $this->secret
        ];

        $params   = $grant->prepareRequestParameters($params, $options);
        $request  = $this->getAccessTokenRequest($params);
        $response = $this->getParsedResponse($request);
        $prepared = $this->prepareAccessTokenResponse($response);
        $token    = $this->createAccessToken($prepared, $grant);

        return $token;
    }

    /**
     * Creates an access token from a response.
     *
     * The grant that was used to fetch the response can be used to provide
     * additional context.
     *
     * @param  array $response
     * @param  AbstractGrant $grant
     * @return AccessToken
     */
    protected function createAccessToken(array $response, AbstractGrant $grant)
    {
        return new AccessToken($response);
    }

    /**
     * Returns the URL for requesting the resource owner's details.
     *
     * @param AccessToken $token
     * @return string
     */
    public function getResourceOwnerDetailsUrl(AccessToken $token)
    {
        $access_token = $token->getToken();
        $openid = $token->getValues()['openid'];

        return sprintf("%s/sns/userinfo?access_token=%s&openid=%s", self::BASE_ACCESS_TOKEN_URL, $access_token, $openid);
    }

    /**
     * Returns the default scopes used by this provider.
     *
     * This should only be the scopes that are required to request the details
     * of the resource owner, rather than all the available scopes.
     *
     * @return array
     */
    protected function getDefaultScopes()
    {
        return ['snsapi_userinfo'];
    }

    /**
     * Checks a provider response for errors.
     *
     * @throws IdentityProviderException
     * @param  ResponseInterface $response
     * @param  array|string|\Psr\Http\Message\ResponseInterface $data Parsed response data
     * @return void
     */
    protected function checkResponse(ResponseInterface $response, $data)
    {
        $errcode = $this->getValueByKey($data, 'errcode');
        $errmsg = $this->getValueByKey($data, 'errmsg');

        if ($errcode || $errmsg) {
            throw new IdentityProviderException($errmsg, $errcode, $response);
        };
    }

    /**
     * Generates a resource owner object from a successful resource owner
     * details request.
     *
     * @param  array $response
     * @param  AccessToken $token
     * @return ResourceOwnerInterface
     */
    protected function createResourceOwner(array $response, AccessToken $token)
    {
        return new WeChatResourceOwner($response);
    }
}
