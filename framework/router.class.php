<?php
/**
 * @application    Cubo CMS
 * @type           Framework
 * @class          Router
 * @description    The Router framework analyses the URL and routes the visitor to the correct controller
 *                 and method; the router also includes language intelligence
 * @version        1.2.0
 * @date           2019-02-03
 * @author         Dan Barto
 * @copyright      Copyright (C) 2017 - 2019 Papiando Riba Internet
 * @license        MIT License; see LICENSE.md
 */
namespace Cubo;

defined('__CUBO__') || new \Exception("No use starting a class without an include");

class Router {
	protected $_params;
	protected $_language;
	
	// Constructor presets parameters and starts parsing the URI
	public function __construct($uri) {
		$this->_params = new \stdClass();
		$this->parse($uri);
	}
	
	public function getDefault($param) {
		return Application::getDefault($param);
	}
	
	public function getParams() {
		return $this->_params;
	}
	
	public function getParam($param,$default = null) {
		return $this->_params->$param ?? $this->getDefault($param) ?? $default;
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
	
	public function getFormat() {
		return $this->getParam('format','html');
	}
	
	public function getLanguage() {
		return $this->getParam('language');
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
	
	// Parse the given URI
	public function parse($uri) {
		$uri = urldecode(trim($uri,'/'));
		// Split URI
		$uri_parts = explode('?',$uri);
		$uri_parts[] = '';
		$path_parts = explode('/',$uri_parts[0]);
		// Preset language
		$this->_language = Language::get(LANGUAGE_UNDEFINED);
		// Get parameters from query string
		parse_str($uri_parts[1],$params);
		$this->_params = (object)$params;
		// See if there is a parameter without value; assume it's a shorthand method
		foreach($this->_params as $key=>$value) {
			if(empty($value) && empty($this->_params->method))
				$this->_params->method = $key;
		}
		// Define accepted routes and preset to site
		$routes = array(Application::get('site_route','')=>'',Application::get('admin_route','admin')=>'admin',Application::get('api_route','api')=>'api');
		// Parse que rest of the query
		if(count($path_parts)) {
			$part = strtolower(current($path_parts));
			// Get route or language if given
			if(in_array($part,array_keys($routes))) {
				$this->_params->route = $routes[$part];
				array_shift($path_parts);
				$part = strtolower(current($path_parts));
			} elseif(Language::exists($part)) {
				$this->_language = Language::get($part);
				$this->_params->language = $this->_language->{'iso639-1'};
				array_shift($path_parts);
				$part = strtolower(current($path_parts));
			}
			// Get controller if given
			if($part) {
				$this->_params->controller = Text::retro($part,$this->_language);
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