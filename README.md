# WeChat Provider for OAuth 2.0 Client

![License](https://img.shields.io/badge/license-MIT-blue.svg) [![Latest Stable Version](https://img.shields.io/packagist/v/nomiscz/oauth2-wechat.svg)](https://packagist.org/packages/nomiscz/oauth2-wechat)

This package provides WeChat OAuth 2.0 support for the PHP League's [OAuth 2.0 Client](https://github.com/thephpleague/oauth2-client).

## Installation

To install, use composer:

```
composer require nomiscz/oauth2-wechat
```

## Usage

Usage is the same as The League's OAuth client, using `NomisCZ\OAuth2\Client\Provider\WeChat` as the provider.

### Authorization Code Flow

```php
$provider = new NomisCZ\OAuth2\Client\Provider\WeChat([
    'appid' => '{wechat-client-id}',
    'secret' => '{wechat-client-secret}',
    'redirect_uri' => 'https://example.com/callback-url'
]);

if (!isset($_GET['code'])) {

    // If we don't have an authorization code then get one
    $authUrl = $provider->getAuthorizationUrl();
    $_SESSION['oauth2state'] = $provider->getState();
    header('Location: '.$authUrl);
    exit;

// Check given state against previously stored one to mitigate CSRF attack
} elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {

    unset($_SESSION['oauth2state']);
    exit('Invalid state');

} else {

    // Try to get an access token (using the authorization code grant)
    $token = $provider->getAccessToken('authorization_code', [
        'code' => $_GET['code']
    ]);

    // Optional: Now you have a token you can look up a users profile data
    try {

        // We got an access token, let's now get the user's details
        $user = $provider->getResourceOwner($token);

        // Use these details to create a new profile
        printf('Hello %s!', $user->getNickname());

    } catch (Exception $e) {

        // Failed to get user details
        exit('Oh dear...');
    }

    // Use this to interact with an API on the users behalf
    echo $token->getToken();
}
```

## Credits

- [NomisCZ](https://github.com/nomiscz)
- [All Contributors](https://github.com/nomiscz/oauth2-wechat/contributors)

## License

The MIT License (MIT). Please see [License File](https://github.com/nomiscz/oauth2-wechat/blob/master/LICENSE) for more information.