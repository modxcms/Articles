<?php
namespace Articles\Model\mysql;

use xPDO\xPDO;

class Article extends \Articles\Model\Article
{

    public static $metaMap = array (
        'package' => 'Articles\\Model',
        'version' => '3.0',
        'extends' => 'MODX\\Revolution\\modResource',
        'tableMeta' => 
        array (
            'engine' => 'InnoDB',
        ),
        'fields' => 
        array (
        ),
        'fieldMeta' => 
        array (
        ),
        'aggregates' => 
        array (
            'Container' => 
            array (
                'class' => 'Articles\\Model\\ArticlesContainer',
                'local' => 'parent',
                'foreign' => 'id',
                'cardinality' => 'one',
                'owner' => 'foreign',
            ),
        ),
    );

}
