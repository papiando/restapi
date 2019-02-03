<?php
/**
 * @application    Cubo RestAPI
 * @type           Framework
 * @class          Configuration
 * @description    The Configuration framework loads settings, parameters and defaults from .config.php and provides these to the application
 * @version        1.0.0
 * @date           2019-02-02
 * @author         Dan Barto
 * @copyright      Copyright (C) 2017 - 2019 Papiando Riba Internet
 * @license        MIT License; see LICENSE.md
 */
namespace Cubo;

defined('__CUBO__') || new \Exception("No use starting a class without an include");

define('ROLE_ADMINISTRATOR',6);
define('ROLE_ANY',-1);
define('ROLE_AUTHOR',2);
define('ROLE_EDITOR',3);
define('ROLE_GUEST',0);
define('ROLE_MANAGER',5);
define('ROLE_PUBLISHER',4);
define('ROLE_USER',1);

class Configuration {
	protected static $_settings = null;	// Holds settings read from .config.php
	protected static $_defaults = null;	// Holds defaults read from .config.php
	protected static $_params = null;	// Holds parameters read from .config.php
	
	public static function get($property) {
		return isset(self::$_settings->$property) ? self::$_settings->$property : null;
	}
	
	public static function set($property,$value) {
		if(!isset(self::$_settings))
			self::$_settings = new \stdClass();
		self::$_settings->$property = $value;
	}
	
	public static function getDefaults() {
		return self::$_defaults;
	}
	
	public static function getDefault($property) {
		return isset(self::$_defaults->$property) ? self::$_defaults->$property : null;
	}
	
	public static function setDefault($property,$value) {
		if(!isset(self::$_defaults))
			self::$_defaults = new \stdClass();
		self::$_defaults->$property = $value;
	}
	
	public static function getParams() {
		return self::$_params;
	}
	
	public static function getParam($property) {
		return isset(self::$_params->$property) ? self::$_params->$property : null;
	}
	
	public static function setParam($property,$value) {
		if(!isset(self::$_params))
			self::$_params = new \stdClass();
		self::$_params->$property = $value;
	}
}
?>