<?php
/**
  * 手机游戏,调用微网站
  */

//define your token
define("TOKEN", "weixin");
$wechatObj = new wechatCallbackapiTest();
$wechatObj->responseMsg();

class wechatCallbackapiTest
{

    public function responseMsg()
    {
		//get post data, May be due to the different environments
		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

      	//extract post data
		if (!empty($postStr)){
			/* libxml_disable_entity_loader is to prevent XML eXternal Entity Injection,
			   the best way is to check the validity of xml by yourself */
			libxml_disable_entity_loader(true);
			$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
			$fromUsername = $postObj->FromUserName;
			$toUsername = $postObj->ToUserName;
			$msgType=$postObj->Msgtype;
			$time = time();

			switch($msgType){

				case 'text':

					$textTpl = "<xml>
						<ToUserName><![CDATA[%s]]></ToUserName>
						<FromUserName><![CDATA[%s]]></FromUserName>
						<CreateTime>%s</CreateTime>
						<MsgType><![CDATA[text]]></MsgType>
						<Content><![CDATA[%s]]></Content>
						<FuncFlag>0</FuncFlag>
						</xml>";
					$contentStr="<a href='http://dp.sina.cn/dpool/tools/translate/texttranslate.php?vt=3&wm=4002&pos=63'>翻译</a>\n\n<a href='http://h.lexun.com/game/DouDiZhu/play.aspx'>斗地主</a>\n\n<a href='http://m.toutiao.com'>今日头条</a>\n\n<a href='http://pictoword.hortorgame.com/'>疯狂猜图</a>";

					$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time,$contentStr);
					echo $resultStr;
					break;
			}
			



        }else {
        	echo "";
        	exit;
        }
    }

}

?>