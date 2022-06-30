<?php

use xPDO\Transport\xPDOTransport;
use MODX\Revolution\Transport\modTransportProvider;
use MODX\Revolution\Transport\modTransportPackage;

/**
 * Installs required dependencies packages
 *
 * @var xPDOTransport $transport
 * @var array $options
 */
$modx = $transport->xpdo;

$success = true;

switch ($options[xPDOTransport::PACKAGE_ACTION]) {
    case xPDOTransport::ACTION_INSTALL:
    case xPDOTransport::ACTION_UPGRADE:
        /**
         * Define required packages: name => minimum version
         */
        $packages = [
            'archivist' => '1.2.4',
            'getpage' => '1.2.4',
            'getresources' => '1.7.0',
            'taglister' => '1.1.7',
        ];

        /** @var modTransportProvider|\MODX\Revolution\Transport\modTransportProvider $provider */
        $provider = $modx->getObject('transport.modTransportProvider', [
            'service_url' => 'https://rest.modx.com/extras/',
        ]);
        if (!$provider) {
            $modx->log(modX::LOG_LEVEL_ERROR, "Could not find MODX.com provider; can't install dependencies");
        }

        foreach ($packages as $package_name => $version) {
            $modx->log(modX::LOG_LEVEL_INFO, "Installing dependency <b>{$package_name}</b> v{$version} (or higher)...");

            $installed = $modx->getIterator('transport.modTransportPackage', [
                'package_name' => $package_name,
            ]);
            /** @var modTransportPackage|\MODX\Revolution\Transport\modTransportPackage $package */
            foreach ($installed as $package) {
                if ($package->compareVersion($version, '<=')) {
                    $modx->log(modX::LOG_LEVEL_INFO, "- &check; {$package->get('signature')} already installed");
                    continue(2);
                }
            }


            $latest = $provider->latest($package_name, '>=' . $version);
            if (count($latest) === 0) {
                $modx->log(modX::LOG_LEVEL_ERROR, "- Could not find <b>{$package_name} v{$version}+</b> in package provider {$provider->get('name')}");
                $success = false;
                continue;
            }

            $latest = reset($latest);
            $modx->log(modX::LOG_LEVEL_INFO, "- Downloading <b>{$latest['signature']}</b> from {$provider->get('name')}...");
            $package = $provider->transfer($latest['signature']);

            if (!$package) {
                $modx->log(modX::LOG_LEVEL_ERROR, "- Download failed :(");
                $success = false;
                continue;
            }

            $modx->log(modX::LOG_LEVEL_WARN, "<b>--- Installing {$latest['signature']} ---</b>");
            $stime = microtime(true);
            $installSuccess = $package->install();
            $ttime = microtime(true) - $stime;

            if ($installSuccess) {
                $modx->log(modX::LOG_LEVEL_WARN,"<b>--- Installed {$latest['signature']} in " . number_format($ttime, 2) . "s ---</b>");
            }
            else {
                $modx->log(modX::LOG_LEVEL_ERROR,"- Installation failed. Please refer to the log above for details.");
                $success = false;
            }
        }

}

return $success;