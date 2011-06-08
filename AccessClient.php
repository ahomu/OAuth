<?php

/**
 * AccessTokenを保持してる状態のOAuthオブジェクト
 *
 * @package     OAuth
 * @copyright   2010 ayumusato.com
 * @license     MIT License
 * @author      Ayumu Sato
 */
class OAuth_AccessClient extends OAuth_Client
{
    public function __construct($consumer, $token, $token_secret)
    {
        parent::__construct($consumer->key, $consumer->secret, $token, $token_secret);
    }
}