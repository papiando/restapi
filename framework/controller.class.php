<?php
/**
 * @application    Cubo CMS
 * @type           Framework
 * @class          Controller
 * @description    All controllers are based on this framework;
 *                 each controller describes the methods of an object
 * @version        1.2.0
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
	
	// Standard method: view
	public function view() {
		// Generate output
		if(self::getParam('id'))
			$id = self::getParam('id');
		elseif(self::getParam('name'))
			$id = self::getParam('name');
		else
			$id = Application::getDefault(self::getParam('controller'));
		$this->_data = $this->_model->get($id,"*",Session::requiresViewAccess());
		if(empty($this->_data)) {
			if(!Session::exists('user')) {
				Session::setMessage(array('alert'=>'info','icon'=>'exclamation','text'=>"{$this->class} requires login credentials"));
				Session::set('login_redirect',Application::getParam('uri'));
				Router::redirect('/user?login');
			} else {
				Session::setMessage(array('alert'=>'error','icon'=>'exclamation','text'=>"This user has no access to {$this->class}"));
				Session::set('login_redirect',Application::getParam('uri'));
				Router::redirect('/user?noaccess');
			}
		}
		if(isset($this->_data->{'@attributes'})) $this->_attributes = json_decode($this->_data->{'@attributes'});
	}
	
	// Standard method: get
	public function get($id) {
		$this->_data = $this->_model->get($id,$this->columns);
	}
	
	// Standard method: list
	public function list() {
		$this->_data = $this->_model->getList("*",Session::requiresListAccess());
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
	
	// Admin method: list
	public function adminList($columns = "*",$filter = "1",$order = "`title`") {
		$this->_data = $this->_model->getList($columns,$filter,$order);
	}
	
	// Admin method: create
	public function adminCreate() {
		// Save posted data
		if($_POST) {
			if(isset($_FILES)) {
				foreach($_FILES as $file=>$data) {
					$_POST['$'.$file] = $data;
				}
			}
			if($this->_model->save($_POST)) {
				Session::setMessage("Item was created");
			} else {
				Session::setMessage("Failed to create item");
			}
			Router::redirect('/admin/'.strtolower($this->class));
		}
	}
	
	// Admin method: add
	public function adminAdd() {
		$this->adminCreate();
	}
	
	// Admin method: edit
	public function adminEdit() {
		// Save posted data
		if($_POST) {
			if(isset($_FILES)) {
				foreach($_FILES as $file=>$data) {
					$_POST['$'.$file] = $data;
				}
			}
			if($this->_model->save($_POST,$_POST['id'])) {
				Session::setMessage("Item was edited");
			} else {
				Session::setMessage("Failed to edit item");
			}
			Router::redirect('/admin/'.strtolower($this->class));
		}
		if(isset($_GET['id'])) {
			$this->_data = $this->_model->get($_GET['id']);
			if(isset($this->_data->{'@attributes'})) $this->_attributes = json_decode($this->_data->{'@attributes'});
		} else {
			Session::setMessage("This item does not exist");
			Router::redirect('/admin/'.strtolower($this->class));
		}
	}
	
	// Admin method: trash
	public function adminTrash() {
		// Remove object
		if(isset($_GET['id'])) {
			if($this->_model->trash($_GET['id'])) {
				Session::setMessage("Item was trashed");
			} else {
				Session::setMessage("Failed to trash item");
			}
		} else {
			Session::setMessage("This item does not exist");
		}
		Router::redirect('/admin/'.strtolower($this->class));
	}
	
	// Admin method: delete
	public function adminDelete() {
		$this->adminTrash();
	}
		
	// Admin default method: directs to admin_list
	public function adminDefault() {
		$this->adminList();
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