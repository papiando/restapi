<?php
/**
 * @application    Cubo RestAPI
 * @type           Framework
 * @class          Model
 * @description    All models are based on this framework;
 *                 each model has a database table associated to it
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
	
	// Retrieves the item referenced with code
	public static function get($name) {
	}
	
	// Retrieves all items
	public static function getAll() {
	}
	
	// Retrieves the class name
	public static function getClass() {
		return basename(str_replace('\\','/',get_called_class()));
	}
	
	// Retrieve the class name when creating a model object
	public function __construct() {
		self::$class = self::getClass();
	}
}
?>