<?php

namespace Articles\Model\Update;

use Exception;
use MODX\Revolution\modX;
use SimpleXMLElement;

/**
 * @package articles
 * @subpackage updateservices
 */
class ArticlesPingomatic extends ArticlesUpdateService {
    public $ch;

    public function notify($title,$url) {
        $request='<?xml version="1.0"?>'.
                '<methodCall>'.
                ' <methodName>weblogUpdates.ping</methodName>'.
                '  <params>'.
                '   <param>'.
                '    <value>'.$title.'</value>'.
                '   </param>'.
                '  <param>'.
                '   <value>'.$url.'</value>'.
                '  </param>'.
                ' </params>'.
                '</methodCall>';
        $this->prepareQuery($request);
        $result = $this->query();
	    if (empty($result)) {
	        $this->modx->log(modX::LOG_LEVEL_ERROR,'Could not connect to pingomatic!');
	        return false;
	    }
	    return $this->processResult($result,$title,$url);
    }

    public function prepareQuery($request) {
        $server = $this->modx->getOption('articles.pingomatic_server',null,'http://rpc.pingomatic.com/');
        $this->ch = curl_init();
	    curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($this->ch, CURLOPT_URL, $server);
	    curl_setopt($this->ch, CURLOPT_HTTPHEADER,
            [
                'Content-type: text/xml',
                'Content-length: '.strlen($request),
                'User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.1) Gecko/20090624 Firefox/3.5 (.NET CLR 3.5.30729',
            ]
        );
        curl_setopt($this->ch, CURLOPT_POST, true);
	    curl_setopt($this->ch, CURLOPT_POSTFIELDS, $request);
	    return $this->ch;
    }

    public function query() {
	    return curl_exec($this->ch);
    }

    public function processResult($result,$title,$url) {
        $success = false;
        try {
            /** @var SimpleXMLElement $xml */
            $xml = simplexml_load_string($result);
            if ($xml->params && $xml->params->param && $xml->params->param->value && $xml->params->param->value->struct && $xml->params->param->value->struct->member && $xml->params->param->value->struct->member[0]) {
                $errorCode = $xml->params->param->value->struct->member[0];
                $errorMessage = $xml->params->param->value->struct->member[1];
                if ((string)$errorCode->value->boolean == '1') {
                    $this->modx->log(modX::LOG_LEVEL_ERROR,'[Articles] Pingomatic error: '.$errorMessage->value->string);
                } else {
                    $this->modx->log(modX::LOG_LEVEL_INFO,'[Articles] Sent Ping-o-matic request for "'.$title.'" at URL: '.$url);
                    $success = true;
                }
            }
        } catch (Exception $e) {
            $this->modx->log(modX::LOG_LEVEL_ERROR,'[Articles] '.$e->getMessage());
        }
        return $success;
    }
}