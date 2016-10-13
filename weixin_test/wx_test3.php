<?php
/**
  * 回复单图文和多图文消息
  * 1发送单图文
  * 2发送多图文
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
			$keyword = trim($postObj->Content);
			$time = time();

			if($keyword==1){
				$singlenews="
							<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime><![CDATA[%s]]></CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<ArticleCount>1</ArticleCount>
							<Articles>
							<item>
							<Title><![CDATA[%s]]></Title>
							<Description><![CDATA[%s]]></Description>
							<PicUrl><![CDATA[%s]]></PicUrl>
							<Url><![CDATA[%s]]></Url>
							</item>
							</Articles>
							</xml>";

			$msgType='news';

			$title='美总统候选人电视辩论开始 特朗普希拉里正面交锋';
			$description='美总统候选人电视辩论开始 特朗普希拉里正面交锋';
			$picurl='http://cms-bucket.nosdn.127.net/catchpic/C/C0/C0C666DDD81213B0818AD600C22C5462.jpg?imageView&thumbnail=550x0';
			$url='http://news.163.com/16/0927/09/C1V7QO0D000146BE.html?f=bj_news#loc=13';

				$resultStr = sprintf($singlenews, $fromUsername, $toUsername, $time, $msgType, $title,$description,$picurl,$url);
				echo $resultStr;
			}
			elseif($keyword==2){
				$multinews="
							<xml>
							<ToUserName>$fromUsername</ToUserName>
							<FromUserName>$toUsername</FromUserName>
							<CreateTime>$time</CreateTime>
							<MsgType><![CDATA[news]]></MsgType>
							<ArticleCount>2</ArticleCount>
							<Articles>
							<item>
							<Title><![CDATA[美总统候选人电视辩论开始 特朗普希拉里正面交锋]]></Title>
							<Description><![CDATA[美总统候选人电视辩论开始 特朗普希拉里正面交锋]]></Description>
							<PicUrl><![CDATA[http://cms-bucket.nosdn.127.net/catchpic/C/C0/C0C666DDD81213B0818AD600C22C5462.jpg?imageView&thumbnail=550x0]]></PicUrl>
							<Url><![CDATA[http://news.163.com/16/0927/09/C1V7QO0D000146BE.html?f=bj_news#loc=13]]></Url>
							</item>
							<item>
							<Title><![CDATA[中国前首富出狱]]></Title>
							<Description><![CDATA[中国前首富出狱]]></Description>
							<PicUrl><![CDATA[http://zxpic.gtimg.com/infonew/0/news_pics_-2162774.jpg/800]]></PicUrl>
							<Url><![CDATA[http://info.3g.qq.com/g/s?aid=template&g_ut=3&tid=news20160927026624&sid=]]></Url>
							</item>
							</Articles>
							</xml>";

				echo $multinews;
			}
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