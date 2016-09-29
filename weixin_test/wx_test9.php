<?php
/**
  * sae数据库查询，输入关键字获得电影
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
					$content=$postObj->Content;
					$mysql=new SaeMysql();
					$sql="select * from weixin where title like '%{$content}%'";
					$data=$mysql->getData($sql);
					$contentStr=$data[0][content];

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