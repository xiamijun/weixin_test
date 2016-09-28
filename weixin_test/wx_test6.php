<?php
/**
  * 发送的是位置，进行导航
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

				case 'location':

					$textTpl = "<xml>
						<ToUserName><![CDATA[%s]]></ToUserName>
						<FromUserName><![CDATA[%s]]></FromUserName>
						<CreateTime>%s</CreateTime>
						<MsgType><![CDATA[text]]></MsgType>
						<Content><![CDATA[%s]]></Content>
						<FuncFlag>0</FuncFlag>
						</xml>";
					$title=urlencode('上海大学');
					$content=urlencode('上海大学');
					$contentStr="<a href='http://api.baidu.com/market?location=31.3151480000,121.3957100000&content=".$content."&title=".$title."&output=html'>导航</a>";

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