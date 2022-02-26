<?php
namespace Articles\Model\mysql;

use xPDO\xPDO;

class ArticlesContainer extends \Articles\Model\ArticlesContainer
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
        'composites' => 
        array (
            'Article' => 
            array (
                'class' => 'Articles\\Model\\Article',
                'local' => 'id',
                'foreign' => 'parent',
                'cardinality' => 'many',
                'owner' => 'local',
            ),
        ),
    );

}
