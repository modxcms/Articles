<?php

namespace Articles\Model\Update;

use Articles\Model\Article;
use MODX\Revolution\modX;

/**
 * @package articles
 * @subpackage updateservices
 */
abstract class ArticlesUpdateService {
    /** @var modX $xpdo */
    public $modx;
    /** @var Article $article */
    public $article;
    /** @var array $config */
    public $config = [];

    function __construct(Article $article,array $config = []) {
        $this->article =& $article;
        $this->modx =& $article->xpdo;
        $this->config = array_merge([

        ],$config);
    }

    /**
     * @abstract
     * @param string $title The title of the Article
     * @param string $url The full URL of the Article
     */
    abstract public function notify($title,$url);
}