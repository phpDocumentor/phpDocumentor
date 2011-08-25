<?php
/**
 * DocBlox
 *
 * @category   DocBlox
 * @package    Tests
 * @copyright  Copyright (c) 2010-2011 Mike van Riel / Naenius. (http://www.naenius.com)
 */

// add the project and its lib path to the include path
set_include_path(
  get_include_path()
  . PATH_SEPARATOR . realpath(dirname(__FILE__) . '/../..')
  . PATH_SEPARATOR . realpath(dirname(__FILE__) . '/../../src')
);

if(!class_exists('Markdown'))
{
  require_once('markdown.php');
}
require_once('PHPUnit/Extensions/OutputTestCase.php');

// include and initialize the autoloader
require_once dirname(__FILE__) . '/../../src/ZendX/Loader/StandardAutoloader.php';
$autoloader = new ZendX_Loader_StandardAutoloader(
    array(
         'prefixes' => array(
             'Zend' => dirname(__FILE__) . '/../../src/Zend',
             'DocBlox' => dirname(__FILE__) . '/../../src/DocBlox'
         ),
         'fallback_autoloader' => true
    )
);
$autoloader->register();
