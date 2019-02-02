<?php
/**
 * @application    Cubo RestAPI
 * @type           Framework
 * @class          Database
 * @description    The Database framework handles and simplifies access to the database and tables
 * @version        1.0.0
 * @date           2019-02-02
 * @author         Dan Barto
 * @copyright      Copyright (C) 2017 - 2019 Papiando Riba Internet
 * @license        MIT License; see LICENSE.md
 */
namespace Cubo;

defined('__CUBO__') || new \Exception("No use starting a class without an include");

class Database {
	private static $connection;
	protected static $dbh = null;
	protected static $error = 0;
	private static $className = null;
	protected static $query;
	protected static $method;
	
	public function __construct($options) {
		self::$query = new \stdClass();
		self::$connection = (object)$options;
		return self::connect();
	}
	
	public function __destruct() {
		self::connected() && self::disconnect();
	}
	
	public static function connect() {
		if(empty(self::$connection->dsn)) {
			throw new \Exception("[".get_class($this)."] [001] No database connection string");
			return null;
		} else {
			try {
				!empty(self::$connection->dsn) && !empty(self::$connection->user) && !empty(self::$connection->password) && self::$dbh = new \PDO(self::$connection->dsn,self::$connection->user,self::$connection->password,array(\PDO::ATTR_ERRMODE=>\PDO::ERRMODE_SILENT));
			} catch(\PDOException $e) {
				self::$error = $e->getMessage();
			}
		}
		return self::connected();
	}
	
	public static function error() {
		return self::$error;
	}
	
	public static function connected() {
		return self::$dbh;
	}
	
	public static function disconnected() {
		return !self::connected();
	}
	
	public static function disconnect() {
		self::$dbh = null;
	}
	
	public static function query($query = null) {
		if(!is_string($query)) {
			switch(self::$method) {
				case 'select':
					$query = "SELECT ".self::$query->select;
					$query .= isset(self::$query->from) ? " FROM ".self::quote(self::$query->from) : "";
					$query .= isset(self::$query->innerjoin) ? " INNER JOIN ".self::quote(self::$query->innerjoin) : "";
					$query .= isset(self::$query->on) ? " ON ".self::quote(self::$query->on)."=".self::quote(self::$query->innerjoin).".`id`" : "";
					$query .= isset(self::$query->where) ? " WHERE ".self::$query->where : "";
					$query .= isset(self::$query->group) ? " GROUP BY ".self::$query->group : "";
					$query .= isset(self::$query->having) ? " HAVING ".self::$query->having : "";
					$query .= isset(self::$query->order) ? " ORDER BY ".self::$query->order : "";
					$query .= isset(self::$query->limit) ? " LIMIT ".self::$query->limit : "";
					$query .= isset(self::$query->offset) ? " OFFSET ".self::$query->offset : "";
					break;
				case 'update':
					$query = "UPDATE ".self::string(self::$query['update']);
					$query .= isset(self::$query['set']) ? " SET ".self::comma(self::$query['set']) : "";
					$query .= isset(self::$query['where']) ? " WHERE ".self::$query['where'] : "";
					break;
				default:
					$query = "";
			}
			self::$method = null;
			self::$query = new \stdClass();
		}
		return $query;
	}
	
	public function load($query = null,$list = null) {
		if(is_array($query)) {
			$list = $query;
			$query = null;
		}
		$sth = self::$dbh->prepare(empty($query) ? self::query() : $query);
		$sth->execute($list);
		$result = $sth->fetchAll(\PDO::FETCH_CLASS|\PDO::FETCH_PROPS_LATE,'\\'.__NAMESPACE__.'\\'.self::$className);
		self::$className = null;
		return $result;
	}
	
	public function loadObject($query = null,$list = null) {
		if(is_array($query)) {
			$list = $query;
			$query = null;
		}
		$sth = self::$dbh->prepare(empty($query) ? self::query() : $query);
		$sth->execute($list);
		$sth->setFetchMode(\PDO::FETCH_CLASS|\PDO::FETCH_PROPS_LATE,'\\'.__NAMESPACE__.'\\'.self::$className);
		$result = $sth->fetch();
		self::$className = null;
		return $result;
	}
	
	public function loadItems($query = null,$list = null) {
		if(is_array($query)) {
			$list = $query;
			$query = null;
		}
		$sth = self::$dbh->prepare(empty($query) ? self::query() : $query);
		$sth->execute($list);
		$sth->setFetchMode(\PDO::FETCH_ASSOC);
		$result = $sth->fetchAll();
		self::$className = null;
		return $result;
	}
	
	public function loadItem($query = null,$list = null) {
		if(is_array($query)) {
			$list = $query;
			$query = null;
		}
		$sth = self::$dbh->prepare(empty($query) ? self::query() : $query);
		$sth->execute($list);
		$sth->setFetchMode(\PDO::FETCH_ASSOC);
		$result = $sth->fetch();
		self::$className = null;
		return $result;
	}
	
	public function execute($query = null,$list = null) {
		if(is_array($query)) {
			$list = $query;
			$query = null;
		}
		$sth = self::$dbh->prepare(empty($query) ? self::query() : $query);
		$result = $sth->execute($list);
		self::$className = null;
		return $result;
	}
	
	public function id() {
		return self::$dbh->lastInsertId();
	}
	
	public function columns($fields) {
		self::$query->columns = $fields;
		return $this;
	}
	
	public function from($table) {
		self::$query->from = $table;
		self::$className = $table;
		return $this;
	}
	
	public function group($fields) {
		self::$query->group = $fields;
		return $this;
	}
	
	public function having($conditions) {
		self::$query->having = $conditions;
		return $this;
	}
	
	public function innerjoin($table) {
		self::$query->innerjoin = $table;
		return $this;
	}
	
	public function join($table,$id) {
		self::$query->innerjoin = $table;
		self::$query->on = $id;
		return $this;
	}
	
	public function limit($num) {
		self::$query->limit = $num;
		return $this;
	}
	
	public function offset($num) {
		self::$query->offset = $num;
		return $this;
	}
	
	public function on($id) {
		self::$query->on = $id;
		return $this;
	}
	
	public function order($sort) {
		self::$query->order = $sort;
		return $this;
	}
	
	public function select($fields) {
		self::$method = 'select';
		self::$query->select = $fields;
		return $this;
	}
	
	public function values($fields) {
		self::$query->values = $fields;
		return $this;
	}
	
	public function where($conditions) {
		self::$query->where = $conditions;
		return $this;
	}
	
	public static function quote($object) {
		if(is_array($object)) {
			$result = "";
			$first = true;
			foreach($object as $item) {
				$first ? $first = false : $result .= ',';
				$result .= self::quote($item);
			}
		} elseif(strpos($object,',')) {
			$result = self::quote(explode(',',$object));
		} else {
			$result = "`{$object}`";
		}
		return $result;
	}
	
	public static function seo($string,$separator = '-') {
		$accents_regex = '~&([a-z]{1,2})(?:acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml);~i';
		$special_cases = array('&'=>'and',"'"=>'');
		$string = strtolower(trim($string));
		$string = str_replace(array_keys($special_cases),array_values($special_cases),$string);
		$string = preg_replace($accents_regex,'$1',htmlentities($string,ENT_QUOTES,'UTF-8'));
		$string = preg_replace("/[^a-z0-9]/u","$separator",$string);
		$string = preg_replace("/[$separator]+/u","$separator",$string);
		return $string;
	}
	
	public static function string($object) {
		if(is_array($object)) {
			$result = "";
			$first = true;
			foreach($object as $item) {
				$first ? $first = false : $result .= ',';
				$result .= self::string($item);
			}
		} else {
			$result = self::$dbh->quote($object);
		}
		return $result;
	}
}
?>