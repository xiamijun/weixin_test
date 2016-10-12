<?php
/**
  * 发送的是文本、图片、语音、视频、位置、链接、天气
  * 位置使用百度地图api
  */

//define your token
define("TOKEN", "weixin");
$wechatObj = new wechatCallbackapiTest();
$wechatObj->valid();
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
					$location_X=$postObj->Location_X;
					$location_Y=$postObj->Location_Y;

					$weatherurl="http://api.map.baidu.com/telematics/v3/weather?location={$location_Y},{$location_X}&ak=wEr4lhGVDKf8m41ZQbZSI9SEprXFlqBO";
					$apistr3=file_get_contents($weatherurl);
					$apiobj3=simplexml_load_string($apistr3);
					$placeobj=$apiobj3->results->currentcity;
					$todayobj=$apiobj3->results->weather_data->date[0];
					$weatherobj=$apiobj3->results->weather_data->weather[0];
					$windobj=$apiobj3->results->weather_data->wind[0];
					$temobj=$apiobj3->results->weather_data->temperature[0];
					$contentStr="{$placeobj}{$todayobj}天气{$weatherobj}，风力{$windobj}，温度{$temobj}";
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
					$geourl="http://api.map.baidu.com/telematics/v3/reverseGeocoding?location={$location_Y},{$location_X}&ak=wEr4lhGVDKf8m41ZQbZSI9SEprXFlqBO";
					$apistr=file_get_contents($geourl);
					$apiobj=simplexml_load_string($apistr);
					$addstr=$apiobj->results->result[0]->name;

					$geourl2="http://api.map.baidu.com/telematics/v3/distance?waypoints=31.3151480000,121.3957100000;{$location_Y},{$location_X}&ak=wEr4lhGVDKf8m41ZQbZSI9SEprXFlqBO";
					$apistr2=file_get_contents($geourl2);
					$apiobj2=simplexml_load_string($apistr2);
					$distanceobj=$apiobj2->results->distance;
					$distanceint=intval($distanceobj);
					$diskmint=$distanceint/1000;

					$weatherurl="http://api.map.baidu.com/telematics/v3/weather?location={$location_Y},{$location_X}&ak=wEr4lhGVDKf8m41ZQbZSI9SEprXFlqBO";
					$apistr3=file_get_contents($weatherurl);
					$apiobj3=simplexml_load_string($apistr3);
					$placeobj=$apiobj3->results->currentcity;
					$todayobj=$apiobj3->results->weather_data->date[0];
					$weatherobj=$apiobj3->results->weather_data->weather[0];
					$windobj=$apiobj3->results->weather_data->wind[0];
					$temobj=$apiobj3->results->weather_data->temperature[0];
					$contentStr="我知道你在{$addstr}附近。你距离我有{$diskmint}公里远。{$placeobj}{$todayobj}天气{$weatherobj}，风力{$windobj}，温度{$temobj}";
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

	public function valid()
	{
		$echoStr = $_GET["echostr"];

		//valid signature , option
		if($this->checkSignature()){
			echo $echoStr;
			exit;
		}
	}

	private function checkSignature()
	{
		// you must define TOKEN by yourself
		if (!defined("TOKEN")) {
			throw new Exception('TOKEN is not defined!');
		}

		$signature = $_GET["signature"];
		$timestamp = $_GET["timestamp"];
		$nonce = $_GET["nonce"];

		$token = TOKEN;
		$tmpArr = array($token, $timestamp, $nonce);
		// use SORT_STRING rule
		sort($tmpArr, SORT_STRING);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );

		if( $tmpStr == $signature ){
			return true;
		}else{
			return false;
		}
	}

}

?>