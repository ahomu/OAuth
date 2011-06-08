<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', './error_log');

require_once('../Consumer.php');
require_once('../Signature.php');

require_once('../Client.php');
require_once('../AccessClient.php');
require_once('../RequestClient.php');

require_once('./Twitter.php');

$key    = @$_GET['consumer_key'];
$secret = @$_GET['consumer_secret'];

$reqToken   = @$_GET['request_token'];
$reqSecret  = @$_GET['request_token_secret'];

$step1  = $step2 = $step3 = $step4 = 'none';

if ( !empty($key) && !empty($secret) && empty($reqToken) && empty($reqSecret) )
{
    $API    = new SampleTwitter($key, $secret);
    $req    = $API->getReqToken();
    $auth   = '<a href="'.$API->getAuthUrl().'" target="_blank">認証URLから、別ウインドウでTwitterのOAuth認証を完了します</a>';

    $step2  = 'block';
    $step3  = 'block';
}
elseif ( !empty($key) && !empty($secret) && !empty($reqToken) && !empty($reqSecret) )
{
    $API    = new SampleTwitter($key, $secret, $reqToken, $reqSecret);
    $acs    = $API->getAcsToken();

    $step4  = 'block';
} else {
    $step1  = 'block';
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja">
    <head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8" />
        <title>Twitter OAuth アクセストークンの取得</title>
        <style type="text/css">
        #step1 { display: <?php echo $step1; ?>; }
        #step2 { display: <?php echo $step2; ?>; }
        #step3 { display: <?php echo $step3; ?>; }
        #step4 { display: <?php echo $step4; ?>; }
        </style>
    </head>
    <body>
        <h1>Twitter OAuth アクセストークンを取得します</h1>
        <div id="step1">
            <form action="" method="get" />
            <h2>Step1. 認証用URL取得のために、APIキーをセットします</h2>
            <dl>
                <dt>Consumer Key</dt>
                <dd><input type="text" name="consumer_key" value="<?php print @$key;?>" size="30" /></dd>
                <dt>Consumer Secret</dt>
                <dd><input type="text" name="consumer_secret" value="<?php print @$secret;?>" size="40" /></dd>
            </dl>
            <p><input type="submit" name="submit" value="認証用URLを取得" /></p>
            </form>
        </div>

        <div id="step2">
            <h2>Step2. Twitter側でOAuth認証を行います</h2>
            <p><?php print @$auth; ?></p>
        </div>

        <div id="step3">
            <form action="" method="get" />
            <h2>Step3. 認証後、アクセストークンを取得します</h2>
            <dl>
                <dt>Consumer Key</dt>
                <dd><?php print @$key;?><input type="hidden" name="consumer_key" value="<?php print @$key;?>" size="30" /></dd>
                <dt>Consumer Secret</dt>
                <dd><?php print @$secret;?><input type="hidden" name="consumer_secret" value="<?php print @$secret;?>" size="40" /></dd>
                <dt>Request Token</dt>
                <dd><?php print @$req['oauth_token'];?><input type="hidden" name="request_token" value="<?php print @$req['oauth_token'];?>" size="30" /></dd>
                <dt>Request Token Secret</dt>
                <dd><?php print @$req['oauth_token_secret'];?><input type="hidden" name="request_token_secret" value="<?php print @$req['oauth_token_secret'];?>" size="40" /></dd>
            </dl>
            <p><input type="submit" name="submit" value="アクセストークンを取得" onclick="confirm('Step2のTwitter OAuth認証は済みましたか？');" /></p>
            </form>
        </div>

        <div id="step4">
            <h2>Step4. アクセストークンを取得しました！</h2>
            <dl>
                <dt>Access Token</dt>
                <dd><?php print @$acs['oauth_token'];?></dd>
                <dt>Access Token Secret</dt>
                <dd><?php print @$acs['oauth_token_secret'];?></dd>
            </dl>
        </div>
    </body>
</html>