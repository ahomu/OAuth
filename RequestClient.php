<?php

/**
 * RequestTokenを保持している状態のOAuthオブジェクト
 *
 * @package     OAuth
 * @copyright   2010 ayumusato.com
 * @license     MIT License
 * @author      Ayumu Sato
 */
class OAuth_RequestClient extends OAuth_Client
{
    public function __construct($consumer, $token, $token_secret)
    {
        parent::__construct($consumer->key, $consumer->secret, $token, $token_secret);
    }
}
