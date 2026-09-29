<?php
/**
 * Articles
 *
 * Copyright 2011-12 by Shaun McCormick <shaun+articles@modx.com>
 *
 * Articles is free software; you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * Articles is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * Articles; if not, write to the Free Software Foundation, Inc., 59 Temple
 * Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @package articles
 */
/**
 * @var modX $modx
 * @var array $scriptProperties
 */
$string = $modx->getOption('string',$scriptProperties,'');
$delimiter = $modx->getOption('delimiter',$scriptProperties,',');
$tpl = $modx->getOption('tpl',$scriptProperties,'articlerssitem');
$outputSeparator = $modx->getOption('outputSeparator',$scriptProperties,"\n");
$outputSeparator = str_replace('\\n',"\n",$outputSeparator);
$toPlaceholder = $modx->getOption('toPlaceholder',$scriptProperties,'');

$items = explode($delimiter,$string);
$items = array_unique($items);
$list = [];
foreach ($items as $item) {
    $list[] = $modx->getChunk($tpl, [
        'item' => $item,
    ]);
}

$output = implode($outputSeparator,$list);
if (!empty($toPlaceholder)) {
    $modx->setPlaceholder($toPlaceholder,$output);
    return '';
}
return $output;