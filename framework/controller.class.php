<?php
/**
 * @application    Cubo RestAPI
 * @type           Framework
 * @class          Controller
 * @description    All controllers are based on this framework; each controller
 *                 describes the allowed methods of an object
 * @version        1.0.0
 * @date           2019-02-02
 * @author         Dan Barto
 * @copyright      Copyright (C) 2017 - 2019 Papiando Riba Internet
 * @license        MIT License; see LICENSE.md
 */
namespace Cubo;

defined('__CUBO__') || new \Exception("No use starting a class without an include");

class Controller {
	protected $columns = "*";
	protected $class;
	protected $_attributes;
	protected $_data;
	protected $_language;
	protected $_model;
	protected $_params;
	
	// Default access levels
	protected static $_authors = array(ROLE_AUTHOR,ROLE_EDITOR,ROLE_PUBLISHER,ROLE_MANAGER,ROLE_ADMINISTRATOR);
	protected static $_editors = array(ROLE_EDITOR,ROLE_PUBLISHER,ROLE_MANAGER,ROLE_ADMINISTRATOR);
	protected static $_publishers = array(ROLE_PUBLISHER,ROLE_MANAGER,ROLE_ADMINISTRATOR);
	protected static $_managers = array(ROLE_MANAGER,ROLE_ADMINISTRATOR);
	
	// Standard method: get
	public function get($id) {
		$this->_data = $this->_model->get($id,$this->columns);
	}
	
	// Standard method: getAll
	public function getAll() {
		$this->_data = $this->_model->getAll($this->columns);
	}
	
	// Standard default method
	public function default($id) {
		if(is_null($id)) {
			// No name provided: retrieve all objects
			$this->getAll();
		} else {
			// Name provided: retrieve this object
			$this->get($id);
		}
	}
	
	// API default method
	public function apiDefault($id) {
		if(is_null($id) || strtolower($id) == 'all') {
			// No name provided, or all: retrieve all objects
			$this->getAll();
		} else {
			// Name provided: retrieve this object
			$this->get($id);
		}
	}
	
	public function getData() {
		return $this->_data;
	}
	
	public function getModel() {
		return $this->_model;
	}
	
	public function getDefault($param) {
		return Application::getDefault($param);
	}
	
	public function getParams() {
		return $this->_params;
	}
	
	public function getParam($param) {
		if(isset($this->_params->$param)) {
			return $this->_params->$param;
		} else {
			return $this->getDefault($param);
		}
	}
	
	public function __construct($data = array()) {
		$this->class = basename(str_replace('\\','/',get_called_class()),'Controller');
		$class = __CUBO__.'\\'.$this->class;
		$this->_model = new $class();
		$this->_data = $data;
		$this->_params = Application::getRouter()->getParams();
	}
}
?>