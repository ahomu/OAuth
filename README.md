#OAuth

OAuth 1.0仕様です．ドキュメント未整備ごめんなさい．

##OAuth_Consumer
OAuth_Consumerはabstractクラスです．このクラスを継承して，各サービス固有のConsumerクラスを実装します．

##OAuth_Clinet
OAuth_Clientは，Consumerのプロパティとして動作し，OAuthに適合したリクエストの生成をサポートします．

##OAuth_AccessClient
OAuth_Clientを継承したクラスで，アクセストークンを保持している状態です．

##OAuth_RequestClient
OAuth_Clientを継承したクラスで，リクエストトークンを保持している状態です．

##OAuth Signature
シグニチャの生成に必要なスタティックメソッドを提供します．

###サンプル

sample/index.php
sample/Twitter.php