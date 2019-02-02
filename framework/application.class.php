<?php
/**
 * @application    Cubo RestAPI
 * @type           Framework
 * @class          Model
 * @description    The application framework calls the router and runs the application using the indicated method and method defaults
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
		// Log entry that Application was started
		new Log(array('name'=>"Application::run",'title'=>"Start application",'description'=>"Application was started with '{$uri}'"));
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
		self::$_params->language = self::$_router->getLanguage();
		self::$_params->provider_name = "Papiando Riba Internet";
		self::$_params->provider_url = "https://papiando.com";
		self::$_params->site_name = Configuration::get('site_name');
		self::$_params->template = self::$_router->getTemplate();
		self::$_params->theme = self::$_router->getTheme();
		self::$_params->title = Configuration::get('site_name');
		self::$_params->uri = self::$_params->base_url.$_SERVER['REQUEST_URI'];
		self::$_params->url = self::$_params->base_url.current(explode('?',$_SERVER['REQUEST_URI']));
		// Retrieve layout
		$route = self::$_router->getRoute();
		if($route == Configuration::get('admin_route') && !Session::exists('user')) {
			Session::setMessage(array('alert'=>'info','icon'=>'exclamation','text'=>"This page requires login access"));
			Session::set('login_redirect',$uri);
			Router::redirect('/user?method=login');
		}
		// Preset controller's class and method
		$class = __CUBO__.'\\'.ucfirst(self::$_router->getController()).'Controller';
		$method = strtolower(str_replace(DS,'_',self::$_router->getAdmin()).self::$_router->getMethod());
		// Call the controller's method
		self::$_controller = new $class();
		if(method_exists($class,$method)) {
			self::$_controller->$method();
			self::$_data = self::$_controller->getData();
			$view = new View();
			if(self::$_router->getController() == 'image' && $method == 'default') {
				$view->renderImage(self::$_controller->getData());
				return;
			} else {
				$html = $view->render(self::$_controller->getData());
			}
		} else {
			throw new \Exception("Class '{$class}' does not have the '{$method}' method defined");
		}
		// Render template
		self::$_template = Template::get(self::$_params->template);
		$html = self::$_template->render($html);
		// Run plugins
		$plugins = self::$_database->loadItems("SELECT * FROM `plugin` WHERE `status`='".STATUS_PUBLISHED."' ORDER BY `id` DESC");
		foreach($plugins as $plugin) {
			$class = __CUBO__.'\\'.ucfirst($plugin['name']).'Plugin';
			$html = $class::run($html);
		}
		// Display output
		echo $html;
	}
	
	public function __construct() {
		// Connect to database
		self::$_database || self::$_database = new Database(Configuration::get('database'));
		$query = self::$_database->select('*')->from('country');
		$countries = self::$_database->load();
		show($countries);
		// Run the application and pass URI
		self::run($_SERVER['REQUEST_URI']);
	}
}
?>