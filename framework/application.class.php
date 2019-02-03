<?php
/**
 * @application    Cubo RestAPI
 * @type           Framework
 * @class          Application
 * @description    The Application framework calls the router and runs the application using the indicated method and method defaults
 * @version        1.0.0
 * @date           2019-02-02
 * @author         Dan Barto
 * @copyright      Copyright (C) 2017 - 2019 Papiando Riba Internet
 * @license        MIT License; see LICENSE.md
 */
namespace Cubo;

defined('__CUBO__') || new \Exception("No use starting a class without an include");

class Application {
	protected static $_router;
	protected static $_controller;
	protected static $_view;
	protected static $_defaults;
	protected static $_data;
	protected static $_params;
	protected static $_session;
	protected static $_template;
	protected static $_database;
	
	public static function getDB() {
		// Connect to database
		self::$_database || self::$_database = new Database(Configuration::get('database'));
		return self::$_database;
	}
	
	public static function getRouter() {
		return self::$_router;
	}
	
	public static function getController() {
		return self::$_controller;
	}
	
	public static function get($property,$default = null) {
		return isset(self::$_data->$property) ? self::$_data->$property : $default;
	}
	
	public static function getData() {
		return self::$_data;
	}
	
	public static function getDefault($property,$default = null) {
		return isset(self::$_defaults->$property) ? self::$_defaults->$property : $default;
	}
	
	public static function getDefaults() {
		return self::$_defaults;
	}
	
	public static function getParam($property,$default = null) {
		return isset(self::$_params->$property) ? self::$_params->$property : $default;
	}
	
	public static function getParams() {
		return self::$_params;
	}
	
	public static function run($uri) {
		// Get application defaults
		self::$_defaults = Configuration::getDefaults();
		// Declare the router
		self::$_router = new Router($uri);
		// Set params
		self::$_params = Configuration::getParams();
		if(!isset(self::$_params))
			self::$_params = new \stdClass();
		self::$_params->base_url = __BASE__;
		self::$_params->generator = "Cubo CMS by Papiando";
		self::$_params->generator_url = "https://cubo-cms.com";
		self::$_params->provider_name = "Papiando Riba Internet";
		self::$_params->provider_url = "https://papiando.com";
		self::$_params->site_name = Configuration::get('site_name');
		self::$_params->title = Configuration::get('site_name');
		self::$_params->uri = self::$_params->base_url.$_SERVER['REQUEST_URI'];
		self::$_params->url = self::$_params->base_url.current(explode('?',$_SERVER['REQUEST_URI']));
		// Preset controller's class and method
		$controller = __CUBO__.'\\'.ucfirst(self::$_router->getController()).'Controller';
		$method = (empty(self::$_router->getRoute()) ? strtolower(self::$_router->getMethod()) : strtolower(self::$_router->getRoute()).ucfirst(self::$_router->getMethod()));
		$view = __CUBO__.'\\'.ucfirst(self::$_router->getController()).'View';
		$format = (empty(self::$_router->getRoute()) ? strtolower(self::$_router->getFormat()) : strtolower(self::$_router->getRoute()).ucfirst(self::$_router->getFormat()));
		// Call the controller's method
		self::$_controller = new $controller();
		if(method_exists($controller,$method)) {
			self::$_controller->$method(self::$_router->getParam('name'));
			self::$_data = self::$_controller->getData();
			self::$_view = new $view();
			if(method_exists($view,$format)) {
				$output = self::$_view->$format(self::$_controller->getData());
			} else {
				throw new \Exception("Class '{$view}' does not have the '{$format}' method defined");
			}
		} else {
			throw new \Exception("Class '{$controller}' does not have the '{$method}' method defined");
		}
		// Display output
		echo $output;
	}
	
	public function __construct() {
		// Connect to database
		self::$_database || self::$_database = new Database(Configuration::get('database'));
		// Run the application and pass URI
		self::run($_SERVER['REQUEST_URI']);
	}
}
?>