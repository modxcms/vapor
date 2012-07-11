<?php
/**
 * @var xPDOTransport $transport
 * @var modSystemSetting $object
 * @var array $options
 * @var array $fileMeta
 */
$results = array();
if (isset($fileMeta['classes'])) {
    foreach ($fileMeta['classes'] as $class) {
        $results[$class] = $transport->xpdo->exec('TRUNCATE TABLE ' . $transport->xpdo->getTableName($class));
    }
}
$transport->xpdo->log(xPDO::LOG_LEVEL_INFO, "Table truncation results: " . print_r($results, true));
return !array_search(false, $results, true);