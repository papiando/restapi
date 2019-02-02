<?php
/**
 * @application    Cubo RestAPI
 * @type           Framework
 * @class          Router
 * @description    The Router framework analyses the URL and routes the visitor to the correct controller
 *                 and method
 * @version        1.0.0
 * @date           2019-02-02
 * @author         Dan Barto
 * @copyright      Copyright (C) 2017 - 2019 Papiando Riba Internet
 * @license        MIT License; see LICENSE.md
 */
namespace Cubo;

defined('__CUBO__') || new \Exception("No use starting a class without an include");

class Router {
	protected $_params;
	
	public function getDefault($param) {
		return Application::getDefault($param);
	}
	
	public function getParams() {
		return $this->_params;
	}
	
	public function getParam($param,$value = null) {
		return $this->_params->$param ?? $this->getDefault($param) ?? $value;
	}
	
	public function setParam($param,$value) {
		$this->_params->$param = $value;
	}
	
	public function getUri() {
		return $this->getParam('uri');
	}
	
	public function getController() {
		return $this->getParam('controller','article');
	}
	
	public function getMethod() {
		return $this->getParam('method','default');
	}
	
	public function getRoute() {
		return $this->getParam('route','');
	}
	
	public function getTemplate() {
		return $this->getParam('template','default');
	}
	
	public function getTheme() {
		return $this->getParam('theme','default');
	}
	
	public static function redirect($location) {
		exit(header("Location: {$location}"));
	}
	
	public function __construct($uri) {
		$this->_params = new \stdClass();
		$uri = urldecode(trim($uri,'/'));
		// Split URI
		$uri_parts = explode('?',$uri);
		$uri_parts[] = '';
		$path_parts = explode('/',$uri_parts[0]);
		// Get parameters from query string
		parse_str($uri_parts[1],$params);
		$this->_params = (object)$params;
		// See if there is a parameter without value; assume it's a shorthand method
		foreach($this->_params as $key=>$value) {
			if(empty($value) && empty($this->_params->method))
				$this->_params->method = $key;
		}
		// Define accepted routes and preset to site
		$this->_params->route = '';
		$routes = array(Application::get('site_route','')=>'',Application::get('api_route','api')=>'api');
		// Parse que rest of the query
		if(count($path_parts)) {
			$part = strtolower(current($path_parts));
			// Get route if given
			if(in_array($part,array_keys($routes))) {
				$this->_params->route = $routes[$part];
				array_shift($path_parts);
				$part = strtolower(current($path_parts));
			}
			// Get controller if given
			if($part) {
				$this->_params->controller = $part;
				array_shift($path_parts);
				$part = strtolower(current($path_parts));
			}
			// Remainder is optional name
			if($part)
				$this->_params->name = $part;
		}
		// Store representable URI
		$this->setParam('uri',$uri);
	}
}
?>