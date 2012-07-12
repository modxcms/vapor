<?php
try {
    include dirname(dirname(__FILE__)) . '/config.core.php';
    include MODX_CORE_PATH . 'model/modx/modx.class.php';

    $options = array(
        'log_level' => xPDO::LOG_LEVEL_INFO,
        'log_target' => XPDO_CLI_MODE ? 'ECHO' : 'HTML',
        xPDO::OPT_CACHE_DB => false,
    );
    $modx = new modX('', $options);
    $modx->setLogTarget($options['log_target']);
    $modx->setLogLevel($options['log_level']);
    $modx->setOption(xPDO::OPT_CACHE_DB, false);
    $modx->setDebug(-1);

    $modx->getVersionData();
    $modxVersion = $modx->version['full_version'];

    if (version_compare($modxVersion, '2.2.1-pl', '>=')) {
        $modx->initialize('mgr', $options);
    } else {
        $modx->initialize('mgr');
    }

    $modx->setLogTarget($options['log_target']);
    $modx->setLogLevel($options['log_level']);
    $modx->setOption(xPDO::OPT_CACHE_DB, false);
    $modx->setDebug(-1);

    $modx->loadClass('transport.modPackageBuilder', '', false, true);

    $builder = new modPackageBuilder($modx);

    /** @var modWorkspace $workspace */
    $workspace = $modx->getObject('modWorkspace', 1);
    if (!$workspace) {
        $modx->log(modX::LOG_LEVEL_FATAL, "no workspace!");
    }

    if (!defined('PKG_NAME')) define('PKG_NAME', $modx->getOption('http_host', $options, 'cloud_import'));
    define('PKG_VERSION', strftime("%y%m%d.%H%M.%S"));
    define('PKG_RELEASE', $modxVersion);

    $package = $builder->createPackage(PKG_NAME, PKG_VERSION, PKG_RELEASE);

    $attributes = array(
        'vehicle_class' => 'xPDOFileVehicle'
    );

    /* get all files from the components directory */
    $package->put(
        array(
            'source' => MODX_CORE_PATH . 'components',
            'target' => 'return MODX_CORE_PATH;'
        ),
        array(
            'vehicle_class' => 'xPDOFileVehicle'
        )
    );
    /* get all files from the assets directory */
    $package->put(
        array(
            'source' => MODX_BASE_PATH . 'assets',
            'target' => 'return MODX_BASE_PATH;'
        ),
        array(
            'vehicle_class' => 'xPDOFileVehicle'
        )
    );

    /* Defines the classes to extract (also used for truncation) */
    $classes= array (
        'modAccessAction',
        'modAccessActionDom',
        'modAccessCategory',
        'modAccessContext',
        'modAccessElement',
        'modAccessMenu',
        'modAccessPermission',
        'modAccessPolicy',
        'modAccessPolicyTemplate',
        'modAccessPolicyTemplateGroup',
        'modAccessResource',
        'modAccessResourceGroup',
        'modAccessTemplateVar',
        'modAction',
        'modActionDom',
        'modActionField',
        'modActiveUser',
        'modCategory',
        'modCategoryClosure',
        'modChunk',
        'modClassMap',
        'modContentType',
        'modContext',
        'modContextResource',
        'modContextSetting',
        'modDashboard',
        'modDashboardWidget',
        'modDashboardWidgetPlacement',
        'modElementPropertySet',
        'modEvent',
        'modFormCustomizationProfile',
        'modFormCustomizationProfileUserGroup',
        'modFormCustomizationSet',
        'modLexiconEntry',
        'modManagerLog',
        'modMenu',
        'modNamespace',
        'modPlugin',
        'modPluginEvent',
        'modPropertySet',
        'modResource',
        'modResourceGroup',
        'modResourceGroupResource',
        'modSession',
        'modSnippet',
        'modSystemSetting',
        'modTemplate',
        'modTemplateVar',
        'modTemplateVarResource',
        'modTemplateVarResourceGroup',
        'modTemplateVarTemplate',
        'modUser',
        'modUserProfile',
        'modUserGroup',
        'modUserGroupMember',
        'modUserGroupRole',
        'modUserMessage',
        'modUserSetting',
        'modWorkspace',
        'registry.db.modDbRegisterMessage',
        'registry.db.modDbRegisterTopic',
        'registry.db.modDbRegisterQueue',
        'transport.modTransportProvider',
        'transport.modTransportPackage',
        'sources.modAccessMediaSource',
        'sources.modMediaSource',
        'sources.modMediaSourceElement',
        'sources.modMediaSourceContext',
    );

    $attributes = array(
        'preserve_keys' => true,
        'update_object' => true
    );

    /* get the extension_packages and resolver */
    $object = $modx->getObject('modSystemSetting', array('key' => 'extension_packages'));
    if ($object) {
        $extPackages = $object->get('value');
        $extPackages = $modx->fromJSON($extPackages);
        foreach ($extPackages as $extPkgKey => $extPackage) {
            if (!empty($extPackage['path'])) {
                if (strpos($extPackage['path'], MODX_CORE_PATH) === 0) {
                    $extPackages[$extPkgKey]['path'] = str_replace(MODX_CORE_PATH, '[[++core_path]]', $extPackage['path'], 1);
                } elseif (strpos($extPackage['path'], $modx->getOption('assets_path', $options, MODX_ASSETS_PATH)) === 0) {
                    $extPackages[$extPkgKey]['path'] = str_replace($modx->getOption('assets_path', $options, MODX_ASSETS_PATH), '[[++assets_path]]', $extPackage['path'], 1);
                } elseif (strpos($extPackage['path'], $modx->getOption('manager_path', $options, MODX_MANAGER_PATH)) === 0) {
                    $extPackages[$extPkgKey]['path'] = str_replace($modx->getOption('manager_path', $options, MODX_MANAGER_PATH), '[[++manager_path]]', $extPackage['path'], 1);
                } elseif (strpos($extPackage['path'], $modx->getOption('base_path', $options, MODX_BASE_PATH)) === 0) {
                    $extPackages[$extPkgKey]['path'] = str_replace($modx->getOption('base_path', $options, MODX_BASE_PATH), '[[++base_path]]', $extPackage['path'], 1);
                }
            }
        }
        $object->set('value', $modx->toJSON($extPackages));
        $package->put($object, array_merge($attributes,
            array(
                'validate' => array(
                    array(
                        'type' => 'php',
                        'source' => 'scripts/validate.truncate_tables.php',
                        'classes' => $classes
                    ),
                ),
                'resolve' => array(
                    array(
                        'type' => 'php',
                        'source' => 'scripts/resolve.extension_packages.php'
                    ),
                )
            )
        ));
    }

    /* loop through the classes and package the objects */
    foreach ($classes as $class) {
        $instances = 0;
        $classCriteria = null;
        $classAttributes = $attributes;
        switch ($class) {
            case 'modSession':
                /* skip sessions */
                continue 2;
            case 'modSystemSetting':
                $classCriteria = array('key:!=' => 'extension_packages');
                break;
            case 'modWorkspace':
                /** @var modWorkspace $object */
                foreach ($modx->getIterator('modWorkspace', $classCriteria) as $object) {
                    if (strpos($object->path, MODX_CORE_PATH) === 0) {
                        $object->set('path', str_replace(MODX_CORE_PATH, '{core_path}', $object->path, 1));
                    } elseif (strpos($object->path, $modx->getOption('assets_path', $options, MODX_ASSETS_PATH)) === 0) {
                        $object->set('path', str_replace($modx->getOption('assets_path', $options, MODX_ASSETS_PATH), '{assets_path}', $object->path, 1));
                    } elseif (strpos($object->path, $modx->getOption('manager_path', $options, MODX_MANAGER_PATH)) === 0) {
                        $object->set('path', str_replace($modx->getOption('manager_path', $options, MODX_MANAGER_PATH), '{manager_path}', $object->path, 1));
                    } elseif (strpos($object->path, $modx->getOption('base_path', $options, MODX_BASE_PATH)) === 0) {
                        $object->set('path', str_replace($modx->getOption('base_path', $options, MODX_BASE_PATH), '{base_path}', $object->path, 1));
                    }
                    if ($package->put($object, $classAttributes)) {
                        $instances++;
                    } else {
                        $modx->log(modX::LOG_LEVEL_WARN, "Could not package {$class} instance with pk: " . print_r($object->getPrimaryKey()));
                    }
                }
                $modx->log(modX::LOG_LEVEL_INFO, "Packaged {$instances} of {$class}");
                continue 2;
            case 'transport.modTransportPackage':
                $modx->loadClass($class);
                $response = $modx->call('modTransportPackage', 'listPackages', array(&$modx, $workspace->get('id')));
                if (isset($response['collection'])) {
                    foreach ($response['collection'] as $object) {
                        $packagesDir = MODX_CORE_PATH . 'packages/';
                        if ($object->getOne('Workspace')) {
                            $packagesDir = $object->Workspace->get('path') . 'packages/';
                        }
                        $pkgSource = $object->get('source');
                        $folderPos = strrpos($pkgSource, '/');
                        $sourceDir = $folderPos > 1 ? substr($pkgSource, 0, $folderPos + 1) : '';
                        $source = realpath($packagesDir . $pkgSource);
                        $target = 'MODX_CORE_PATH . "packages/' . $sourceDir . '"';
                        $classAttributes = array_merge($attributes, array(
                            'resolve' => array(
                                array(
                                    'type' => 'file',
                                    'source' => $source,
                                    'target' => "return {$target};"
                                )
                            )
                        ));
                        if ($package->put($object, $classAttributes)) {
                            $instances++;
                        } else {
                            $modx->log(modX::LOG_LEVEL_WARN, "Could not package {$class} instance with pk: " . print_r($object->getPrimaryKey()));
                        }
                    }
                }
                $modx->log(modX::LOG_LEVEL_INFO, "Packaged {$instances} of {$class}");
                continue 2;
            default:
                break;
        }
        /** @var xPDOObject $object */
        foreach ($modx->getIterator($class, $classCriteria) as $object) {
            if ($package->put($object, $classAttributes)) {
                $instances++;
            } else {
                $modx->log(modX::LOG_LEVEL_WARN, "Could not package {$class} instance with pk: " . print_r($object->getPrimaryKey()));
            }
        }
        $modx->log(modX::LOG_LEVEL_INFO, "Packaged {$instances} of {$class}");
    }

    $package->pack();
} catch (Exception $e) {
    if ($modx) {
        $modx->log(modX::LOG_LEVEL_ERROR, $e->getMessage());
    } else {
        echo $e->getMessage() . "\n";
    }
}

$modx->log(modX::LOG_LEVEL_INFO, "Completed extracting package: {$package->signature}");
