<?php

namespace Neodynamic\SDK\Web;

use Exception;
use phpseclib\Crypt\AES;
use phpseclib\Crypt\RSA;

/**
 * Utility class for encrypting file content and passwords.
 */
class SecUtils
{

    private static function getPubKey()
    {
        return '<RSAKeyValue><Modulus>reXqa092+txbh684R9kUsMMIG2UTEJQChhFkZ3u/kg1OsPAspaWnjRgecq1lTKIbppPXa4NztFNPw5c7W6sN+3GiuRAbOT6E+ynQIyo298znCoeW+W93WZ8imF32HwWn9lUvI6VFJULwjZ16G91ok/YPTuREc8ri7jclC3ie8g0=</Modulus><Exponent>AQAB</Exponent></RSAKeyValue>';
    }

    public static function rsaVerifyAndEncrypt($pubKeyBase64, $pubKeySignatureBase64, $dataToEncrypt)
    {
        $rsa = new RSA();
        $rsa->loadKey(self::getPubKey());
        $rsa->setSignatureMode(2); //SIGNATURE_PKCS1
        if ($rsa->verify(base64_decode($pubKeyBase64), base64_decode($pubKeySignatureBase64))) {
            $rsa->loadKey(base64_decode($pubKeyBase64));
            $rsa->setEncryptionMode(2); //ENCRYPTION_PKCS1
            return $rsa->encrypt($dataToEncrypt);
        } else {
            throw new Exception('Cannot verify the provided RSA Public Key.');
        }
    }

    public static function aesEncrypt($dataToEncrypt, $password, $salt, $iv, $iterations)
    {
        $aes = new AES(AES::MODE_CBC);
        $aes->setPassword(
            $password,
            'pbkdf2' /* key extension algorithm */,
            'sha1' /* hash algorithm */,
            $salt,
            $iterations,
            256 / 8
        );
        $aes->setIV($iv);
        return $aes->encrypt($dataToEncrypt);
    }
}