<?php
//简化版

/**
 * wechat php test
 */

//define your token
define("TOKEN", "weixin");
$wechatObj = new wechatCallbackapiTest();
//$wechatObj->valid();
$wechatObj->responseMsg();

class wechatCallbackapiTest
{
    public function valid()
    {
        $echoStr = $_GET["echostr"];

        //valid signature , option
        if($this->checkSignature()){
            echo $echoStr;
            exit;
        }
    }

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
            $type=$postObj->MsgType;
            $customevent=$postObj->Event;
            $latitude=$postObj->Location_X;
            $longitude=$postObj->Location_Y;
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
            switch($type){
                case 'event':
                    if($customevent=='subscribe'){
                        $contentStr='感谢关注，回复1，2，3';
                    }
                break;
                case 'image':
                    $contentStr='你的图片不错';
                    break;
                case 'location':
                    $contentStr="你的纬度是{$latitude}，经度{$longitude}";
                    break;
                case 'link':
                    $contentStr='你的链接有毒';
                    break;
                case 'text':
                    switch($keyword){
                        case '1':
                            $contentStr='关键词为1';
                            break;
                        case '2':
                            $contentStr='关键词为1';
                            break;
                        case '3':
                            $contentStr='关键词为1';
                            break;
                        default:
                            $contentStr='稍后回复你';
                    }
                    break;
                default:
                    $contentStr='此功能未开发';
            }
            $msgType='text';
            $resultStr=sprintf($textTpl,$fromUsername,$toUsername,$time,$msgType,$contentStr);
            echo $resultStr;

        }else {
            echo "";
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