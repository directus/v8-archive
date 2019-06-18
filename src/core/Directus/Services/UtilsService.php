<?php

namespace Directus\Services;

use Directus\Hash\HashManager;
use Directus\Util\StringUtils;
use PHPGangsta_GoogleAuthenticator;

class UtilsService extends AbstractService
{
    public function hashString($string, $hasher, array $options = [])
    {
        $this->validate([
            'string' => $string
        ], [
            'string' => 'required|string'
        ]);

        $options['hasher'] = $hasher;
        /** @var HashManager $hashManager */
        $hashManager = $this->container->get('hash_manager');
        $hashedString = $hashManager->hash($string, $options);

        return [
            'data' => [
                'hash' => $hashedString
            ]
        ];
    }

    public function verifyHashString($string, $hash, $hasher, array $options = [])
    {
        $this->validate([
            'string' => $string,
            'hash' => $hash
        ], [
            'string' => 'required|string',
            'hash' => 'required|string'
        ]);

        $options['hasher'] = $hasher;
        /** @var HashManager $hashManager */
        $hashManager = $this->container->get('hash_manager');
        $valid = $hashManager->verify($string, $hash, $options);

        return [
            'data' => [
                'valid' => $valid
            ]
        ];
    }

    public function randomString($length, $options = [])
    {
        $this->validate(['length' => $length], ['length' => 'numeric']);

        // TODO: Add more options
        $randomString = StringUtils::randomString($length);

        return [
            'data' => [
                'random' => $randomString
            ]
        ];
    }

    public function generate2FASecret()
    {
        $ga = new PHPGangsta_GoogleAuthenticator();
        $tfa_secret = $ga->createSecret();
        return ['2fa_secret' => $tfa_secret];
    }
}
