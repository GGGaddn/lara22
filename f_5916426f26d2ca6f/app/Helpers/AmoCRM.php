<?php
namespace App\Helpers;
use League\OAuth2\Client\Token\AccessToken;

class AmoCRM {
    private $token_file = DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR . 'token_info.json';

    /**
     * @param array $accessToken
     */
    public function saveToken($accessToken)
    {
        if (
            isset($accessToken)
            && isset($accessToken['accessToken'])
            && isset($accessToken['refreshToken'])
            && isset($accessToken['expires'])
            && isset($accessToken['baseDomain'])
        ) {
            $data = [
                'accessToken' => $accessToken['accessToken'],
                'expires' => $accessToken['expires'],
                'refreshToken' => $accessToken['refreshToken'],
                'baseDomain' => $accessToken['baseDomain'],
            ];

            file_put_contents($this->token_file, json_encode($data));
        } else {
            exit('Invalid access token ' . var_export($accessToken, true));
        }
    }

    /**
     * @return AccessToken
     */
    public function getToken()
    {
        if (!file_exists($this->token_file)) {
            return false;
        }

        $accessToken = json_decode(file_get_contents($this->token_file), true);

        if (
            isset($accessToken)
            && isset($accessToken['accessToken'])
            && isset($accessToken['refreshToken'])
            && isset($accessToken['expires'])
            && isset($accessToken['baseDomain'])
        ) {
            return new AccessToken([
                'access_token' => $accessToken['accessToken'],
                'refresh_token' => $accessToken['refreshToken'],
                'expires' => $accessToken['expires'],
                'baseDomain' => $accessToken['baseDomain'],
            ]);
        } else {
            exit('Invalid access token ' . var_export($accessToken, true));
        }
    }
}