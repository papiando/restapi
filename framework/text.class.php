<?php
/**
 * @application    Cubo CMS
 * @type           Framework
 * @class          Text
 * @version        1.1.0
 * @date           2019-01-30
 * @author         Dan Barto
 * @copyright      Copyright (C) 2017 - 2019 Papiando Riba Internet
 * @license        MIT License; see LICENSE.md
 */
namespace Cubo;

defined('__CUBO__') || new \Exception("No use starting a class without an include");

class Text {
	public static function getLanguage() {
		return Language::get(Application::getRouter()->getLanguage());
	}
	
	public static function translate($property,$default = null) {
		$language = self::getLanguage();
		$query = "SELECT `title` FROM `translation` WHERE `language`=:language AND `name`=:property LIMIT 1";
		$result = Application::getDB()->loadItem($query,array(':language'=>$language->id,':property'=>$property));
		return ($result ? $result['title'] : ($default ? $default : $property));
	}
	
	public static function _($property,$default = null) {
		return self::translate($property,$default);
	}
	
	public static function plural($property,$count = 'n',$default = null) {
		$language = self::getLanguage();
		$query = "SELECT `title` FROM `translation` WHERE `language`=:language AND `name`=:property LIMIT 1";
		$result = Application::getDB()->loadItem($query,array(':language'=>$language->id,':property'=>$property.'-'.$count));
		if(!$result) {
			$result = Application::getDB()->loadItem($query,array(':language'=>$language->id,':property'=>$property.'-n'));
			if(!$result) {
				$result = Application::getDB()->loadItem($query,array(':language'=>$language->id,':property'=>$property.'-n'));
			}
		}
		return ($result ? $result['title'] : ($default ? $default : $property));
	}
	
	public static function retro($property,$language) {
		$query = "SELECT `name` FROM `translation` WHERE `language`=:language AND `seo`=:property LIMIT 1";
		$result = Application::getDB()->loadItem($query,array(':language'=>$language->id,':property'=>$property));
		return ($result ? $result['name'] : $property);
	}
}
?>