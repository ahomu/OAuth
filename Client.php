<?php

/**
 * OAuthのリクエスト処理を制御するオブジェクト
 *
 * @package     OAuth
 * @copyright   2010 ayumusato.com
 * @license     MIT License
 * @author      Ayumu Sato
 */
class OAuth_Client
{
    protected $version = '1.0';
    protected $base;

    public $key;
    public $secret;

    public $token;
    public $token_secret;

    public $params = array(
        'oauth_callback'        => null,
        'scope'                 => null,
        'oauth_verifier'        => null,
        'oauth_session_handle'  => null,
        'xoauth_dispalay_name'  => null,
    );

    /**
     * コンストラクタ
     *
     * @param string $key
     * @param string $secret
     * @param string $token
     * @param string $token_secret
     */
    public function __construct($key, $secret, $token = null, $token_secret = null)
    {
        $this->key     = $key;
        $this->secret  = $secret;

        if ( !empty($token) && !empty($token_secret) )
        {
            $this->token        = $token;
            $this->token_secret = $token_secret;
        }
        else
        {
            $this->token        = null;
            $this->token_secret = null;
        }
    }

    /**
     * OAuthオブジェクトを，RequestTokenを保持したOAuthオブジェクトにして返す
     *
     * @param OAuth_Client $consumer
     * @param string $token
     * @param string $token_secret
     * @return OAuth_RequestClient
     */
    public static function RequestToken($consumer, $token, $token_secret)
    {
        return new OAuth_RequestClient($consumer, $token, $token_secret);
    }

    /**
     * OAuthオブジェクトを，AccessTokenを保持したOAuthオブジェクトにして返す
     *
     * @param OAuth_Client $consumer
     * @param string $token
     * @param string $token_secret
     * @return OAuth_AccessClient
     */
    public static function AccessToken($consumer, $token, $token_secret)
    {
        return new OAuth_AccessClient($consumer, $token, $token_secret);
    }

    /**
     * OAuth用のプロバイダ依存な追加パラメータをセットする
     *
     * @param string $key
     * @param string $val
     * @return void
     */
    public function setParam($key, $val)
    {
        $this->params[$key] = $val;
    }

    /**
     * セット済みのOAuth用のパラメータを，マージする
     *
     * @param array $params ( base parameter )
     * @param string $http_method ( get / post )
     * @return array $params
     */
    public function mergeOAuthParams($params, $http_method)
    {
        $params = array_merge($params, array(
            'oauth_version'         => $this->version,
            'oauth_nonce'           => md5(microtime().mt_rand()),
            'oauth_timestamp'       => time(),
            'oauth_consumer_key'    => $this->key,
            'oauth_signature_method'=> $http_method,
        ));

        // その他のパラメーターを，空白を切り詰めてからマージする
        $params = array_merge($params, array_merge(array_diff($this->params, array(''))));

        // トークンを保持していれば含ませる
        if ( !empty($this->token) ) {
            $params['oauth_token'] = $this->token;
        }

        return $params;
    }

    /**
     * APIのベースURL，パラメータ，APIのメソッド，HTTPリクエストメソッドから，
     * OAuthのシグニチャを生成する
     *
     * @param string $url ( base url )
     * @param array  $params
     * @param string $http_method
     * @param string $sig_method
     * @return string
     */
    public function buildSignature($url, $params, $http_method = 'GET', $sig_method)
    {
        $material   = array(
            OAuth_Signature::rfc3986($http_method),
            OAuth_Signature::rfc3986($url),
            OAuth_Signature::rfc3986($this->_httpBuildQuery($params)),
        );

        $this->base = implode('&', $material);

        switch ($sig_method)
        {
            case 'HMAC-SHA1' :
                $signature = OAuth_Signature::hmacSha1($this->base, $this->secret, $this->token_secret);
                return $signature;
            default:
                return '';
                break;
        }
    }

    /**
     * APIのベースURL，パラメータ，APIのメソッド，HTTPリクエストメソッドから，
     * リクエスト用のフルURLを作成
     *
     * @param string $url ( base url )
     * @param array  $params
     * @param string $http_method
     * @param string $sig_method
     * @return string complete url
     */
    public function buildRequest($url, $params, $http_method = 'GET', $sig_method)
    {
        $params = $this->mergeOAuthParams($params, $sig_method);

        $params['oauth_signature'] = $this->buildSignature($url, $params, $http_method, $sig_method);

        $query = $this->_httpBuildQuery($params);

        return $url.'?'.$query;
    }

    /**
     * OAuthを施した，HTTPリクエスト用のGETクエリを作成
     *
     * @param array $params
     * @return string
     */
    public function _httpBuildQuery($params)
    {
        $encoded = array();
        while ( list($key, $val) = each($params) ) {
            $key    = OAuth_Signature::rfc3986($key);
            $val    = OAuth_Signature::rfc3986($val);
            $encoded[$key] = $val;
        }

        $params = $encoded;
        ksort($params);

        $queries = array();
        foreach ( $params as $key => $val ) {
            $queries[] = $key.'='.$val;
        }

        return implode('&', $queries);
    }
}
