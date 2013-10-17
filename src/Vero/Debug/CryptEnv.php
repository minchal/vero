<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Debug;

/**
 * Crypt and decrypt request and environment data for debug message in production mode.
 */
class CryptEnv
{
    /**
     * Get environment and request data as encrypted base64 string.
     * 
     * @param array
     * @param string
     * @return string
     */
    public static function encrypt($data, $public)
    {
        self::checkAvailability();
        
        $message = sprintf(
            "Exception [%s]: %s\n URL: %s\n\n%s", //\n\n%s\n\n%s\n\n%s
            $data['exception'],
            $data['message'],
            $data['request'],
            $data['trace']
            /*print_r($_POST, true),
            print_r($_COOKIE, true),
            print_r($_FILES, true)*/
        );
        
        $public = openssl_pkey_get_public($public);
        
        if (!$public) {
            throw new \RuntimeException('Public key can not be loaded.');
        }
        
        $encrypted = '';
        $envelopes = [];
        
        if (!openssl_seal($message, $encrypted, $envelopes, [$public])) {
            throw new \RuntimeException('Data can not be encrypted.');
        }
        
        return base64_encode(serialize([$envelopes[0], $encrypted]));
    }
    
    /**
     * Decrypt string returned by crypt method.
     * 
     * @param string
     * @param string
     * @return string
     */
    public static function decrypt($data, $private)
    {
        self::checkAvailability();
        
        $arr = @unserialize(base64_decode($data));
        
        if (!$arr) {
            throw new \RuntimeException('Encrypted data can not be unserialized.');
        }
        
        if (count($arr) !== 2) {
            throw new \RuntimeException(
                sprintf(
                    'Encoded data must have 2 elements in array afted deserialization. %s found.',
                    count($arr)
                )
            );
        }
        
        $private = openssl_pkey_get_private($private);
        
        if (!$private) {
            throw new \RuntimeException('Private key can not be loaded.');
        }
        
        $decrypted = '';
        
        if (!openssl_open($arr[1], $decrypted, $arr[0], $private)) {
            throw new \RuntimeException('Encrypted data can not be decrypted.');
        }
        
        return $decrypted;
    }
    
    /**
     * Check, if crypting service is available in current environment.
     */
    protected static function checkAvailability()
    {
        if (!extension_loaded('openssl')) {
            throw new \RuntimeException('OpenSSL extension is not available.');
        }
        
        return true;
    }
}
