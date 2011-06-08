<?php

/**
 * OAuthのシグニチャ生成に必要なメソッド類を保持
 *
 * @package     OAuth
 * @copyright   2010 ayumusato.com
 * @license     MIT License
 * @author      Ayumu Sato
 */
class OAuth_Signature
{
    /**
     * シグニチャをHMAC-SHA1で生成
     *
     * @param string $base
     * @param string $consumer_secret
     * @param string $token_secret
     * @return string
     */
    static public function hmacSha1($base, $consumer_secret, $token_secret = '')
    {
        $keys   = array(
                        OAuth_Signature::rfc3986($consumer_secret),
                        OAuth_Signature::rfc3986($token_secret),
                        );

        $key    = implode('&', $keys);

        return base64_encode(hash_hmac('sha1', $base, $key, true));
    }

    /**
     * RFC3986に基づいてURLエンコード
     *
     * @param string $str
     * @return mixed
     */
    static public function rfc3986($str)
    {
        return str_replace('%7E', '~', rawurlencode($str));
    }

}