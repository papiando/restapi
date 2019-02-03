<?php
/**
 * @application    Cubo RestAPI
 * @type           Framework
 * @class          Controller
 * @description    All controllers are based on this framework; each controller
 *                 describes the allowed methods of an object
 * @version        1.0.0
 * @date           2019-02-03
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
	
	// Constructor loads the class name and loads the model and retrieved data
	public function __construct($data = array()) {
		$this->class = basename(str_replace('\\','/',get_called_class()),'Controller');
		$class = __CUBO__.'\\'.$this->class;
		$this->_model = new $class();
		$this->_data = $data;
		$this->_params = Application::getRouter()->getParams();
	}
	
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
	
	// Retrieve the data supplied by the model
	public function getData() {
		return $this->_data;
	}
	
	// Retrieve the configured language
	public function getLanguage() {
		return $this->_language;
	}
	
	// Retrieve the model
	public function getModel() {
		return $this->_model;
	}
	
	// Get the default value
	public function getDefault($param) {
		return Application::getDefault($param);
	}
	
	// Get the parameter
	public function getParam($param) {
		if(isset($this->_params->$param)) {
			return $this->_params->$param;
		} else {
			return $this->getDefault($param);
		}
	}
	
	// Get all parameters
	public function getParams() {
		return $this->_params;
	}
	
	// Returns true if current user has permitted role to create an item
	public static function canCreate() {
		return in_array(Session::getRole(),self::$_authors);
	}
	
	// Returns true if current user does not have permitted role to create an item
	public static function cannotCreate() {
		return !self::canCreate($author);
	}
	
	// Returns true if current user is the author or has permitted role to edit an item
	public static function canEdit($author = 0) {
		return in_array(Session::getRole(),self::$_editors) || Session::getUser() == $author;
	}
	
	// Returns true if current user is not the author and does not have permitted role to edit an item
	public static function cannotEdit($author = 0) {
		return !self::canEdit($author);
	}
	
	// Returns true if current user is the author or has permitted role to publish an item
	public static function canPublish() {
		return in_array(Session::getRole(),self::$_publishers);
	}
	
	// Returns true if current user is not the author and does not have permitted role to publish an item
	public static function cannotPublish() {
		return !self::canPublish();
	}
	
	// Returns true if current user is the author or has permitted role to publish an item
	public static function canManage() {
		return in_array(Session::getRole(),self::$_publishers);
	}
	
	// Returns true if current user is not the author and does not have permitted role to publish an item
	public static function cannotManage() {
		return !self::canManage();
	}
}
?>