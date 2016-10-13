<?php
/**
  * 发送的是位置，周边检索，查找周边酒店
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
			$time = time();

			switch($msgType){

				case 'location':
					$location_X=$postObj->Location_X;
					$location_Y=$postObj->Location_Y;

					$textTpl = "<xml>
						<ToUserName><![CDATA[%s]]></ToUserName>
						<FromUserName><![CDATA[%s]]></FromUserName>
						<CreateTime>%s</CreateTime>
						<MsgType><![CDATA[news]]></MsgType>
						<ArticleCount>4</ArticleCount>
						<Articles>
						<item>
						<Title><![CDATA[你周边酒店如下]]></Title>
						<Description><![CDATA[]]></Description>
						<PicUrl><![CDATA[http://exp.cdn-hotels.com/hotels/1000000/30000/22700/22628/22628_91_z.jpg]]></PicUrl>
						<Url>![CDATA[]]</Url>
						</item>
						<item>
						<Title>![CDATA[%s]</Title>
						<Description><![CDATA[]]></Description>
						<PicUrl><![CDATA[http://exp.cdn-hotels.com/hotels/1000000/30000/22700/22628/22628_91_z.jpg]]></PicUrl>
						<Url>![CDATA[]]</Url>
						</item>
						<item>
						<Title>![CDATA[%s]</Title>
						<Description><![CDATA[]]></Description>
						<PicUrl><![CDATA[http://exp.cdn-hotels.com/hotels/1000000/30000/22700/22628/22628_91_z.jpg]]></PicUrl>
						<Url>![CDATA[]]</Url>
						</item>
						<item>
						<Title>![CDATA[%s]</Title>
						<Description><![CDATA[]]></Description>
						<PicUrl><![CDATA[http://exp.cdn-hotels.com/hotels/1000000/30000/22700/22628/22628_91_z.jpg]]></PicUrl>
						<Url>![CDATA[]]</Url>
						</item>
						</Articles>
						<FuncFlag>0</FuncFlag>
						</xml>";

					$hotel="http://api.map.baidu.com/telematics/v3/local?location={$location_Y},{$location_X}&keyword=酒店&ak=wEr4lhGVDKf8m41ZQbZSI9SEprXFlqBO";
					$apistr=file_get_contents($hotel);
					$apiobj=simplexml_load_string($apistr);
					
					$name1=$apiobj->poiList->point[1]->additionalInfo->name;
					$add1=$apiobj->poiList->point[1]->additionalInfo->address;
					$tel1=$apiobj->poiList->point[1]->additionalInfo->telephone;
					$price1=$apiobj->poiList->point[1]->additionalInfo->price;
					$title1="{$name1}地址：{$add1}，电话：{$tel1}，价格：{$price1}元";

					$name2=$apiobj->poiList->point[2]->additionalInfo->name;
					$add2=$apiobj->poiList->point[2]->additionalInfo->address;
					$tel2=$apiobj->poiList->point[2]->additionalInfo->telephone;
					$price2=$apiobj->poiList->point[2]->additionalInfo->price;
					$title2="{$name2}地址：{$add2}，电话：{$tel2}，价格：{$price2}元";

					$name3=$apiobj->poiList->point[3]->additionalInfo->name;
					$add3=$apiobj->poiList->point[3]->additionalInfo->address;
					$tel3=$apiobj->poiList->point[3]->additionalInfo->telephone;
					$price3=$apiobj->poiList->point[3]->additionalInfo->price;
					$title3="{$name3}地址：{$add3}，电话：{$tel3}，价格：{$price3}元";

					$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $title1,$title2,$title3);
					echo $resultStr;
					break;
			}
			



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
		$signature = $_GET["signature"];
		$timestamp = $_GET["timestamp"];
		$nonce = $_GET["nonce"];

		$token = TOKEN;
		$tmpArr = array($token, $timestamp, $nonce);
		sort($tmpArr);
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