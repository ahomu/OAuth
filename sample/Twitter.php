<?php
/**
 * TwitterでOAuthでリクエストをするSampleクラス
 */
class SampleTwitter extends OAuth_Consumer
{
    protected $api_host = 'https://api.twitter.com/1/';

    public function __construct($key, $secret, $token_key = null, $token_secret = null, $token_type = 'access')
    {
        // 親のコンストラクタで初期化
        parent::__construct($key, $secret);

        // 状態の昇格を試みる
        if ( !empty($token_key) && !empty($token_secret) ) {
            if ( $token_type == 'access' )
            $this->OAuth = OAuth_Client::AccessToken($this->OAuth, $token_key, $token_secret);

            if ( $token_type == 'request' )
            $this->OAuth = OAuth_Client::RequestToken($this->OAuth, $token_key, $token_secret);
        }
    }

    public function setUrl()
    {
        $this->request_token_url    = 'https://twitter.com/oauth/request_token';
        $this->authorize_url        = 'https://twitter.com/oauth/authorize';
        $this->access_token_url     = 'https://twitter.com/oauth/access_token';
    }

    public function getAuthUrl()
    {
        return $this->authorize_url."?oauth_token={$this->OAuth->token}";
    }

    // サンプルでは，手を抜いてfile_get_contents関数を利用しています．
    // リクエストヘッダを検査する等，レスポンスを格納したりしたい場合は，
    // OAuth_Consumerを継承したクラスのプロパティを拡張します．
    public function httpRequest($url, $params = array(), $http_method = 'GET')
    {
        $url        = !!(strpos($url, 'https') === 0) ? $url : $this->api_host.$url;

        $request    = $this->OAuth->buildRequest($url, $params, $http_method, 'HMAC-SHA1');
        $method     = strtoupper($http_method);

        if ( $method === 'POST' ) {
            $context = stream_context_create(array(
                'http' => array(
                    'method'       => $method,
                    'header'       => implode("\r\n", array('Content-Type: application/x-www-form-urlencoded')),
                    'content'      => http_build_query($params),
                    'ignore_erros' => true,
                )
            ));
        } else {
            $context = stream_context_create(array());
        }

        return file_get_contents($request, false, $context);
    }
}
