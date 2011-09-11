<?php
// determine base include folder, if @php_bin@ contains @php_bin then we do not install via PEAR
$base_include_folder = (strpos('@php_dir@', '@php_dir') === 0)
  ? dirname(__FILE__) . '/../../src'
  : '@php_dir@/DocBlox/src';

// set path to add lib folder, load the Zend Autoloader
set_include_path($base_include_folder . PATH_SEPARATOR . get_include_path());

require_once $base_include_folder.'/ZendX/Loader/StandardAutoloader.php';
$autoloader = new ZendX_Loader_StandardAutoloader(
    array(
        'prefixes' => array(
            'Zend'    => $base_include_folder.'/Zend',
            'DocBlox' => $base_include_folder.'/DocBlox'
        ),
        'fallback_autoloader' => true
    )
);
$autoloader->register();

$application = new DocBlox_Core_Application();
$application->main();
