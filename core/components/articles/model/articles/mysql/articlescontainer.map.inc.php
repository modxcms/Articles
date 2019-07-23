<?php
/**
 * @package articles
 */
$xpdo_meta_map['ArticlesContainer']= [
  'package' => 'articles',
  'version' => '1.1',
  'extends' => 'modResource',
  'fields' => 
  [
  ],
  'fieldMeta' => 
  [
  ],
  'composites' => 
  [
    'Articles' => 
    [
      'class' => 'Article',
      'local' => 'id',
      'foreign' => 'parent',
      'cardinality' => 'many',
      'owner' => 'local',
    ],
  ],
];
