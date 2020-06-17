<?php

namespace Neodynamic\SDK\Web;

use Exception;
use Neodynamic\SDK\Web\Utils;
use Neodynamic\SDK\Web\SecUtils;

/**
 * It specifies encryption metadata.
 */
class EncryptMetadata
{

    /**
     * Gets the RSA Public Key in Base64 format.
     */
    public $publicKeyBase64 = '';
    /**
     * Gets the RSA Public Key Signature in Base64 format.
     */
    public $publicKeySignatureBase64 = '';
    /**
     * Gets or sets the password used to derive the encryption key. It must be 100 ASCII chars/bytes max.
     */
    public $password = '';
    /**
     * Gets or sets the salt used to derive the key. It must be 100 ASCII chars/bytes max.
     */
    public $salt = '';
    /**
     * Gets or sets the Initialization Vector to be used for the encryption algorithm. It must be 32 ASCII chars/bytes.
     */
    public $iv = '';
    /**
     * Gets or sets the number of iterations to derive the key. Minimum is 1000.
     */
    public $iterations = 1000;


    /**
     * 
     * @param type $pubKeyBase64 The RSA Public Key in Base64 format sent by WCPP Client Utility.
     * @param type $pubKeySignatureKeyBase64 The RSA Public Key Signature in Base64 format sent by WCPP Client Utility.
     */
    public function __construct($pubKeyBase64, $pubKeySignatureKeyBase64)
    {
        $this->publicKeyBase64 = $pubKeyBase64;
        $this->publicKeySignatureBase64 = $pubKeySignatureKeyBase64;
    }

    public function serialize()
    {

        $this->validateMetadata();

        $sep = '|';

        $buffer = base64_encode(SecUtils::rsaVerifyAndEncrypt($this->publicKeyBase64, $this->publicKeySignatureBase64, $this->password));
        $buffer .= $sep;
        $buffer .= base64_encode(SecUtils::rsaVerifyAndEncrypt($this->publicKeyBase64, $this->publicKeySignatureBase64, $this->salt));
        $buffer .= $sep;
        $buffer .= base64_encode(SecUtils::rsaVerifyAndEncrypt($this->publicKeyBase64, $this->publicKeySignatureBase64, $this->iv));
        $buffer .= $sep;
        $buffer .= base64_encode(SecUtils::rsaVerifyAndEncrypt($this->publicKeyBase64, $this->publicKeySignatureBase64, strval($this->iterations)));

        return $buffer;
    }

    public function validateMetadata()
    {
        if (Utils::isNullOrEmptyString($this->password)) {
            $this->password = Utils::genRandomString(33, 126, 32);
        } else if (strlen($this->password) > 100) {
            throw new Exception("Password cannot be greater than 100 ASCII chars/bytes.");
        }

        if (Utils::isNullOrEmptyString($this->salt)) {
            $this->salt = Utils::genRandomString(33, 126, 32);
        } else if (strlen($this->salt) > 100) {
            throw new Exception("Salt cannot be greater than 100 ASCII chars/bytes.");
        }

        if (Utils::isNullOrEmptyString($this->iv)) {
            $this->iv = Utils::genRandomString(33, 126, 16);
        } else if (strlen($this->iv) > 16) {
            throw new Exception("IV cannot be greater than 16 ASCII chars/bytes.");
        }

        if ($this->iterations < 1000) {
            $this->iterations = 1000;
        }
    }
}