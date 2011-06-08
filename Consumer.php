<?php

/**
 * OAuthコンシューマー
 *
 * @package     OAuth
 * @copyright   2010 ayumusato.com
 * @license     MIT License
 * @author      Ayumu Sato
 */
abstract class OAuth_Consumer
{
    public $OAuth;

    protected $request_token_url;
    protected $access_token_url;
    protected $authorize_url;

    /**
     * request, access, authorizeの各urlプロパティをセットする
     *
     * @abstract
     * @return void
     */
    abstract public function setUrl();

    /**
     * OAuthリクエストを発行して，結果を格納する
     * 正常に取得できた場合は，レスポンスボディを返す
     *
     * @abstract
     * @param string $url
     * @param array $params
     * @param string $http_method
     * @return bool|string
     */
    abstract public function httpRequest($url, $params = array(), $http_method = 'GET');

    /**
     * 初期化
     *
     * @param string $key
     * @param string $secret
     * @return void
     */
    protected function __construct($key, $secret)
    {
        $this->setUrl();
        $this->OAuth = new OAuth_Client($key, $secret);
    }

    /**
     * レスポンスのクエリをパースして配列に変換する
     *
     * @param  $query
     * @return array|bool
     */
    protected function _parseQuery($query)
    {
        if ( empty($query) ) return false;

        $ary    = explode('&', $query);

        if ( !is_array($ary) ) return false;

        $parsed = array();
        foreach ( $ary as $a ) {
            list($key, $val) = explode('=', $a);
            $parsed[$key]  = $val;
        }

        return !empty($parsed) ? $parsed : false;
    }

    /**
     * トークンが取得できたら，OAuthをRequestToken取得済みのインスタンスに昇格
     *
     * @return array|bool
     */
    public function getReqToken()
    {
        if ( !!($response = $this->httpRequest($this->request_token_url)) ) {
            $token  = $this->_parseQuery($response);

            if ( !empty($token) ) {
                $this->OAuth = OAuth_Client::RequestToken($this->OAuth, $token['oauth_token'], $token['oauth_token_secret']);
                return $token;
            }
        }
        return $response;
    }

    /**
     * トークンが取得できたら，OAuthをAccessToken取得済みのインスタンスに昇格
     *
     * @return array|bool
     */
    public function getAcsToken()
    {
        if ( !!($response = $this->httpRequest($this->access_token_url)) ) {
            $token  = $this->_parseQuery($response);

            if ( !empty($token) ) {
                $this->OAuth = OAuth_Client::AccessToken($this->OAuth, $token['oauth_token'], $token['oauth_token_secret']);
                return $token;
            }
        }
        return $response;
    }
}
