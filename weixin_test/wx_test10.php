<?php
/**
  * sae数据库
  * 识别新老用户，输入你好
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

				case 'text':

					$textTpl = "<xml>
						<ToUserName><![CDATA[%s]]></ToUserName>
						<FromUserName><![CDATA[%s]]></FromUserName>
						<CreateTime>%s</CreateTime>
						<MsgType><![CDATA[text]]></MsgType>
						<Content><![CDATA[%s]]></Content>
						<FuncFlag>0</FuncFlag>
						</xml>";
					$content=$postObj->Content;
					if($content=='你好'){
						$mysql=new SaeMysql();
						$sql="select * from CRm where user='{$fromUsername}'";
						$data=$mysql->getData($sql);
						$user=$data[0][user];
						if(empty($user)){
							$sql2="insert into CRM(id,user) VALUES (NULL,'{$fromUsername}')";
							$mysql->runSql($sql2);
							$contentStr='欢迎新朋友';
						}else{
							$contentStr='欢迎老朋友';
						}
					}

					$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time,$contentStr);
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