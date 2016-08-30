<?php
require_once (dirname(__FILE__).'/autoloadTOA.php');

use Abraham\TwitterOAuth\TwitterOAuth;

class TwitterRegProcessor extends modObjectProcessor {
    public $classKey = 'Article';
    public $objectType = 'reg-twitter';
    public $languageTopics = array('resource','articles:default');
    /** @var Article $object */
    public $object;
    
    public $keys;

    public function initialize() {
        $initialized = parent::initialize();
        $id = $this->getProperty('id',null);
        if (empty($id)) { return $this->modx->lexicon('articles.container_err_ns'); }
        $this->object = $this->modx->getObject('ArticlesContainer',$id);
        if (empty($this->object)) return $this->modx->lexicon('articles.container_err_nf');
        
        if (!$container) return false;

        $this->keys = $container->getTwitterKeys();
        $this->object->decrypt($this->config['notifyTwitterAccessToken_'.$container->get('alias')]);
        $this->object->decrypt($this->config['notifyTwitterAccessTokenSecret_'.$container->get('alias')]);
        return $initialized;
    }
    
    public function process()
    {
        $url = $this->modx->makeUrl($this->object->get('id'), '', '', 'full');
        $connection = new TwitterOAuth($keys['consumer_key'],$keys['consumer_key_secret']);

        $request_token = $connection->oauth('oauth/request_token', ['oauth_callback' => $url]);

        switch ($connection->getLastHttpCode()) {
            case 200:
                $_SESSION['oauth_token'] = $request_token['oauth_token'];
                $_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];
                $redirectUrl = $connection->url('oauth/authorize', ['oauth_token' => $request_token['oauth_token']]);
                $this->modx->sendRedirect($redirectUrl);
                break;
            default:
                echo 'Could not connect to Twitter. Refresh the page or try again later.';
                break;
        }
    }
}
return 'TwitterRegProcessor';