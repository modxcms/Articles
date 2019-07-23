<?php
/**
 * @package articles
 */
$xpdo_meta_map['Article']= [
  'package' => 'articles',
  'version' => '1.1',
  'extends' => 'modResource',
  'fields' => 
  [
  ],
  'fieldMeta' => 
  [
  ],
  'aggregates' => 
  [
    'Container' => 
    [
      'class' => 'ArticlesContainer',
      'local' => 'parent',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ],
  ],
];
