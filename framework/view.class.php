<?php
/**
 * @application    Cubo CMS
 * @type           Framework
 * @class          View
 * @description    The View framework generates the output based on a given format and prepares it
 *                 for rendering
 * @version        1.2.0
 * @date           2019-02-03
 * @author         Dan Barto
 * @copyright      Copyright (C) 2017 - 2019 Papiando Riba Internet
 * @license        MIT License; see LICENSE.md
 */
namespace Cubo;

defined('__CUBO__') || new \Exception("No use starting a class without an include");

if(!function_exists('xml_encode')) {
	function toXml($object,$xml = null,$class = null) {
		$returnType = $xml;
		if(!is_object($xml)) {
			$type = (is_object($object) ? basename(str_replace('\\','/',get_class($object))) : $xml.'-list');
			$xml = new \SimpleXMLElement("<{$type}/>");
		}
		foreach((array)$object as $key=>$value) {
			if(is_array($value) || is_object($value)) {
				$type = (is_object($value) ? basename(str_replace('\\','/',get_class($value))) : $key);
				toXml($value,$xml->addChild($type),$key);
			} else {
				$xml->addChild($key,$value);
			}
		}
		return $xml;
	}
	function xml_encode($object,$class) {
		$simpleXml = toXml($object,$class);
		$dom = new \DOMDocument('1.0','utf-8');
		$dom->preserveWhiteSpace = false;
		$dom->formatOutput = true;
		$dom->loadXML($simpleXml->asXML());
		return $dom->saveXML();
	}
}

class View {
	protected $_attributes;
	protected $_data;
	protected $_router;
	protected $path;
	public $sharedPath;
	protected $class;
	
	public function __construct() {
		$this->_router = Application::getRouter();
		$this->class = $this->_router->getController();
	}
	
	public function getAttribute($attribute) {
		return (isset($this->_attributes->$attribute) ? $this->_attributes->$attribute : null);
	}
	
	public function getPath() {
		if(!$this->_router) {
			return false;
		}
		return $this->path = __ROOT__.DS.'view'.DS.$this->class.DS.(empty($this->_router->getRoute()) ? '' : $this->_router->getRoute().DS).$this->_router->getMethod().'.php';
	}
	
	public function getDefaultPath() {
		if(!$this->_router) {
			return false;
		}
		return $this->path = __ROOT__.DS.'view'.DS.$this->class.DS.(empty($this->_router->getRoute()) ? '' : $this->_router->getRoute().DS).$this->_router->getMethod().'.php';
	}
	
	public function getSharedPath() {
		if(!$this->_router) {
			return false;
		}
		return $this->sharedPath = __ROOT__.DS.'view'.DS.'shared'.DS.(empty($this->_router->getRoute()) ? '' : $this->_router->getRoute().DS);
	}
	
	public function html($data = array()) {
		$this->_data = $data;
		if(isset($data->{'@attributes'})) $this->_attributes = json_decode($data->{'@attributes'});
		if(file_exists($this->getPath()) || file_exists($this->getDefaultPath())) {
			// Start buffering output
			ob_start();
			// Write output to buffer
			include($this->path);
			// Return buffered output
			return ob_get_clean();
		} else {
			throw new \Exception("Template file '{$this->path}' does not exist");
		}
	}
	
	public function apiHtml($data = array('output'=>"No data")) {
		return "<pre>".json_encode($data,JSON_PRETTY_PRINT)."</pre>";
	}
	
	public function apiJson($data = array('output'=>"No data")) {
		header("Content-Type: application/json");
		return json_encode($data);
	}
	
	public function apiXml($data = array('output'=>"No data")) {
		header("Content-Type: application/xml");
		return xml_encode($data,$this->class);
	}
}
?>