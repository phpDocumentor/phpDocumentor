<?php
/**
 * This file is part of the Sphoof framework.
 * Copyright (c) 2010-2011 Sphoof
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code. You can also view the
 * LICENSE file online at http://www.sphoof.nl/new-bsd.txt
 *
 * @category	Sphoof
 * @copyright	Copyright (c) 2010-2011 Sphoof (http://sphoof.nl)
 * @license		http://sphoof.nl/new-bsd.txt	New BSD License
 * @package		Autoload
 */

require_once dirname( __FILE__ ) . '/sphoof.php';
require_once dirname( __FILE__ ) . '/exception.php';

/**
 * An Exception that will be thrown if you're trying to define a classname that
 * is already known in the autoloader.
 *
 * @package		Autoload
 * @subpackage	Exception
 */
class SpDuplicateDefinition extends SpException {}

/**
 * An Exception that will be thrown when trying to initiate a class that the
 * autoloader can not find.
 *
 * @package		Autoload
 * @subpackage	Exception
 */
class SpClassDefinitionNotFound extends SpException {}

/**
 * An Exception that will be thrown when the class is known, but the associated
 * filename could not be found or opened.
 *
 * @package		Autoload
 * @subpackage	Exception
 */
class SpClassFileNotFound extends SpException {}

/**
 * An interface all autoloaders should implement.
 *
 * @package Autoload
 */
interface SpAutoloadsClasses {
	public function loadClass( $classname );
}

/**
 * Class that Autoloading classes can implement with common functionality.
 *
 * @package Autoload
 */
abstract class SpAutoloader {
	/**
	 * Tries to include a file and throws an exception if it fails.
	 *
	 * @param string $filename
	 * @return boolean
	 */
	public function includefile( $filename ) {
		if( sphoof_file_exists( $filename ) ) {
			return include_once $filename;
		}
		return false;
	}
}

/**
 * This class loads the files containing class definitions when needed. You can
 * pass it an array of classname => filename mappings so it can figure out which
 * file to include.
 *
 * @package Autoload
 * @todo Wouldn't it be better to pass an array into this class?
 */
class SpArrayAutoloader extends SpAutoloader implements SpAutoloadsClasses {
	/**
	 * Contains the files which contain the class definitions
	 *
	 * @var array
	 */
	protected $mapping = array( );

	/**
	 * Constructs the array autoloader. This will only accept an array of class
	 * to filename mappings.
	 *
	 * @param array $mapping
	 */
	public function __construct( Array $mapping ) {
		$this->registerArray( $mapping );
	}

	/**
	 * Register a single class definition
	 *
	 * @param string $classname
	 * @param string $filename
	 * @return SpArrayAutoloader
	 */
	public function register( $classname, $filename ) {
		$classname = strtolower( $classname );
		if( isset( $this->mapping[$classname] ) ) {
			throw new SpDuplicateDefinition( 'Duplicate class definition location' );
		}
		$this->mapping[$classname] = $filename;
		return $this;
	}

	/**
	 * Register an array of class definitions.
	 *
	 * @param array $mappings
	 * @return <type>
	 */
	public function registerArray( Array $mappings ) {
		foreach( $mappings as $classname => $filename ) {
			$this->register( $classname, $filename );
		}
		return $this;
	}

	/**
	 * Load the definition file for the class
	 */
	public function loadClass( $classname ) {
		$classname = strtolower($classname);
		if( isset( $this->mapping[$classname] ) && ( $filename = $this->mapping[$classname] ) ) {
			return $this->includefile( $filename );
		}
		return false;
	}
}

/**
 * This class loads the files containing class definitions when needed.
 *
 * @package Autoload
 */
class SpPearAutoloader extends SpAutoloader implements SpAutoloadsClasses {
	public function loadClass( $classname ) {
		$filename = str_replace( '_', '/', strtolower( $classname ) . '.php' );
		if( sphoof_file_exists( $filename ) ) {
			return $this->includefile( $filename );
		}
		return false;
	}
}
