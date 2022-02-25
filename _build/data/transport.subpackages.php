<?php

use MODX\Revolution\Transport\modPackageBuilder;
use xPDO\Transport\xPDOTransportVehicle;

/**
 * Package in subpackages
 *
 * @var modX $modx
 * @var modPackageBuilder $builder
 * @var array $sources
 * @package articles
 */
$subpackages = [
    'archivist'      => 'archivist-1.2.4-pl',
    'getpage'        => 'getpage-1.2.4-pl',
    'getresources'   => 'getresources-1.7.0-pl',
    'quip'           => 'quip-2.3.5-pl',
    'taglister'      => 'taglister-1.1.7-pl',
];
$spAttr = ['vehicle_class' => xPDOTransportVehicle::class];

foreach ($subpackages as $name => $signature) {
    $vehicle = $builder->createVehicle([
        'source' => $sources['subpackages'] . $signature.'.transport.zip',
        'target' => "return MODX_CORE_PATH . 'packages/';",
    ],$spAttr);
    $vehicle->validate('php', [
        'source' => $sources['validators'].'validate.'.$name.'.php'
    ]);
    $vehicle->resolve('php', [
        'source' => $sources['resolvers'].'packages/resolve.'.$name.'.php'
    ]);
    $builder->putVehicle($vehicle);
}
return true;