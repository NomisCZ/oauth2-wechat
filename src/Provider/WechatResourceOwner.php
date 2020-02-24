<?php

namespace NomisCZ\OAuth2\Client\Provider;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;

class WeChatResourceOwner implements ResourceOwnerInterface
{
    /**
     * Raw response
     *
     * @var array
     */
    protected $response;

    /**
     * Creates new resource owner.
     *
     * @param array  $response
     */
    public function __construct(array $response = array())
    {
        $this->response = $response;
    }

    /**
     * Returns the ID for the user as a string if present.
     *
     * @return string|null
     */
    public function getId()
    {
        return $this->response['openid'] ?: null;
    }

    /**
     * Returns the nickname for the user as a string if present.
     *
     * @return string|null
     */
    public function getNickname()
    {
        return $this->response['nickname'] ?: null;
    }

    /**
     * Returns the gender for the user as a string if present.
     * 1 - male, 2 - female
     * @return string|null
     */
    public function getSex()
    {
        return $this->response['sex'] ?: null;
    }

    /**
     * Returns the current province of the user as an array.
     *
     * @return string|null
     */
    public function getProvince()
    {
        return $this->response['province'] ?: null;
    }

    /**
     * Returns the current city of the user as an array.
     *
     * @return string|null
     */
    public function getCity()
    {
        return $this->response['city'] ?: null;
    }

    /**
     * Returns the current country of the user as an array.
     *
     * @return string|null
     */
    public function getCountry()
    {
        return $this->response['country'] ?: null;
    }

   /**
     * Returns the avatar picture of the user as a string if present.
     *
     * @return string|null
     */
    public function getHeadImgUrl()
    {
        return $this->response['headimgurl'] ?: null;
    }

    /**
     * Returns user privilege information, in the form of a JSON array. For example, WeChat Woka users have the value "chinaunicom".
     *
     * @return string|null
     */
    public function getPrivilege()
    {
        return $this->response['privilege'] ?: null;
    }

    /**
     * Returns the user's unified ID. A user's apps under the same WeChat Open Platform account share the same UnionID.
     *
     * @return string|null
     */
    public function getUnionId()
    {
        return $this->response['unionid'] ?: null;
    }

    /**
     * Returns all the data obtained about the user.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->response;
    }
}
