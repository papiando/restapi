<?php
/**
 * @application    Cubo RestAPI
 * @type           Framework
 * @class          Model
 * @description    All models are based on this framework; each model has
 *                 a database table associated to it
 * @version        1.0.0
 * @date           2019-02-02
 * @author         Dan Barto
 * @copyright      Copyright (C) 2017 - 2019 Papiando Riba Internet
 * @license        MIT License; see LICENSE.md
 */
namespace Cubo;

defined('__CUBO__') || new \Exception("No use starting a class without an include");

class Model {
	protected static $class;		// Class name
	
	// Retrieve a single record from the model
	public static function get($id,$columns = "*",$filter = "1") {
		Application::getDB()->select($columns)->from(strtolower(self::getClass()));
		if(is_null($id)) {
			return null;										// Safety net if no valid $id is provided
		} elseif(is_numeric($id)) {
			Application::getDB()->where("`id`=:id AND {$filter}");
		} else {
			Application::getDB()->where("`code`=:id AND {$filter}");
		}
		$result = Application::getDB()->loadObject(array(':id'=>$id));
		return (is_object($result) ? $result : null);			// Only return the object, otherwise return nothing
	}
	
	// Retrieve set of records from the model
	public static function getAll($columns = "*",$filter = "1",$order = "`name`") {
		Application::getDB()->select($columns)->from(strtolower(self::getClass()))->where($filter)->order($order);
		$result = Application::getDB()->load();
		return (is_array($result) ? $result : null);			// Return an array of objects, otherwise return nothing
	}
	
	// Retrieves the class name
	public static function getClass() {
		return basename(str_replace('\\','/',get_called_class()));
	}
	
	public function __toString() {
		return self::$class;
	}
	
	// Retrieve the class name when creating a model object
	public function __construct() {
		self::$class = self::getClass();
	}
}
?>