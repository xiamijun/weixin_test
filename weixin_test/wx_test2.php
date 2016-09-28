<?php
/**
  * 发送的是文本、图片、语音、视频、位置、链接
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
			$event=$postObj->Event;
			$keyword = trim($postObj->Content);
			$time = time();
			$textTpl = "<xml>
						<ToUserName><![CDATA[%s]]></ToUserName>
						<FromUserName><![CDATA[%s]]></FromUserName>
						<CreateTime>%s</CreateTime>
						<MsgType><![CDATA[%s]]></MsgType>
						<Content><![CDATA[%s]]></Content>
						<FuncFlag>0</FuncFlag>
						</xml>";
			switch($msgType){
				case 'event':
					if($event=='subscribe'){
						$contentStr='感谢你的关注，回复1，2，3';
					}
				break;
				case 'text':
					switch($keyword){
						case '1':
							$contentStr="关键词为1";
							break;
						case '2':
							$contentStr='关键词为2';
							break;
						case '3':
							$contentStr='关键词为2';
							break;
						default:
							$content=$postObj->Content;
							$contentStr='你发送的是文本消息'.$content;
					}
					break;
				case 'image':
					$picurl=$postObj->PicUrl;
					$contentStr='你发送的是图片，地址：'.$picurl;
					break;
				case 'voice':
					$recognition=$postObj->Recognition;
					$contentStr='你发送的是语音，内容：'.$recognition;
					break;
				case 'video':
					$contentStr='你发送的是视频';
					break;
				case 'location':
					$location_X=$postObj->Location_X;
					$location_Y=$postObj->Location_Y;
					$contentStr='你发送的是位置,纬度：'.$location_X.'经度：'.$location_Y;
					break;
				case 'link':
					$title=$postObj->Title;
					$contentStr='你发送的是链接，标题：'.$title;
					break;
			}
			$msgType='text';
				$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
				echo $resultStr;


        }else {
        	echo "";
        	exit;
        }
    }

}

?>