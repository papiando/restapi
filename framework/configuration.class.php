<?php
/**
 * @application    Cubo CMS
 * @type           Framework
 * @class          Configuration
 * @description    The configuration framework loads settings, parameters and defaults from .config.php and provides these to the application
 * @version        1.2.0
 * @date           2019-02-03
 * @author         Dan Barto
 * @copyright      Copyright (C) 2017 - 2019 Papiando Riba Internet
 * @license        MIT License; see LICENSE.md
 */
namespace Cubo;

defined('__CUBO__') || new \Exception("No use starting a class without an include");

define('ACCESS_ANY',-1);
define('ACCESS_GUEST',3);
define('ACCESS_NONE',0);
define('ACCESS_PRIVATE',4);
define('ACCESS_PUBLIC',1);
define('ACCESS_REGISTERED',2);
define('CATEGORY_ANY',-1);
define('CATEGORY_NONE',0);
define('CATEGORY_UNDEFINED',1);
define('COLLECTION_ANY',-1);
define('COLLECTION_NONE',0);
define('COLLECTION_UNDEFINED',1);
define('GLOBAL_SETTING',1);
define('GROUP_ANY',-1);
define('GROUP_NONE',0);
define('GROUP_UNDEFINED',1);
define('LANGUAGE_ANY',-1);
define('LANGUAGE_UNDEFINED',1);
define('LINKTYPE_ARTICLE',1);
define('LINKTYPE_CATEGORY',2);
define('LINKTYPE_COLLECTION',6);
define('LINKTYPE_CONTACT',3);
define('LINKTYPE_GROUP',4);
define('LINKTYPE_IMAGE',5);
define('LINKTYPE_NONE',0);
define('LINKTYPE_SEPARATOR',9);
define('LINKTYPE_URL',8);
define('LINKTYPE_USER',7);
define('OPTION_ANY',-1);
define('OPTION_NONE',0);
define('ROLE_ADMINISTRATOR',6);
define('ROLE_ANY',-1);
define('ROLE_AUTHOR',2);
define('ROLE_EDITOR',3);
define('ROLE_GUEST',0);
define('ROLE_MANAGER',5);
define('ROLE_PUBLISHER',4);
define('ROLE_USER',1);
define('SETTING_ABOVECONTENT',4);
define('SETTING_ABOVETITLE',2);
define('SETTING_AUTHOR',4);
define('SETTING_BELOWCONTENT',5);
define('SETTING_BELOWTITLE',3);
define('SETTING_CREATEDDATE',4);
define('SETTING_EDITOR',5);
define('SETTING_FLOATLEFT',6);
define('SETTING_FLOATRIGHT',7);
define('SETTING_GLOBAL',1);
define('SETTING_HIDE',2);
define('SETTING_MODIFIEDDATE',5);
define('SETTING_NO',0);
define('SETTING_OFF',1);
define('SETTING_ON',0);
define('SETTING_PARAGRAPH',3);
define('SETTING_PUBLISHEDDATE',6);
define('SETTING_PUBLISHER',6);
define('SETTING_SHOW',3);
define('SETTING_TENLINES',4);
define('SETTING_YES',1);
define('STATUS_ANY',-1);
define('STATUS_PUBLISHED',1);
define('STATUS_SYSTEM',0);
define('STATUS_TRASHED',3);
define('STATUS_UNPUBLISHED',2);
define('USER_ANY',-1);
define('USER_NOBODY',0);
define('USER_SYSTEM',1);

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