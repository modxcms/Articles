<?php

namespace Articles\Model\Import;

use Articles\Articles;
use Articles\Processors\Container\Import;
use MODX\Revolution\modX;

/**
 * @abstract
 * @package articles
 * @subpackage import
 */
abstract class ArticlesImport {
    /** @var modX $xpdo */
    public $modx;
    /** @var Articles $articles */
    public $articles;
    /** @var array $config */
    public $config = [];
    /** @var boolean $debug */
    public $debug = false;
    /** @var Import $processor */
    public $processor;

    function __construct(Articles $articles, Import $processor ,array $config = []) {
        $this->articles =& $articles;
        $this->processor =& $processor;
        $this->modx =& $articles->modx;
        $this->config = array_merge([

        ],$config);
        if (!empty($this->config['id'])) {
            $this->config['id'] = trim(trim($this->config['id'],'#'));
        }

        $this->initialize();
    }

    /**
     * Initialize the importer and load the Quip package
     */
    public function initialize() {
        @set_time_limit(0);
        @ini_set('memory_limit','1024M');
        $quipPath = $this->modx->getOption('quip.core_path',null,$this->modx->getOption('core_path').'components/quip/');
        $this->modx->addPackage('quip',$quipPath.'model/');
    }

    /**
     * Abstract method that is called to import from a specific service.
     * @abstract
     * @return boolean
     */
    abstract public function import();

    /**
     * Add an error to the response
     * @param string $field
     * @param string $message
     * @return mixed
     */
    public function addError($field,$message = '') {
        return $this->processor->addFieldError($field,$message);
    }
}