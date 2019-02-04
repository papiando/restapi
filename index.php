<?php
/**
 * @application    Cubo CMS
 * @type           Default document
 * @class          n/a
 * @description    The index.php script is the default document. Its only purpose is to autoload the application.
 * @version        1.2.0
 * @date           2019-02-03
 * @author         Dan Barto
 * @copyright      Copyright (C) 2017 - 2019 Papiando Riba Internet
 * @license        MIT License; see LICENSE.md
 */
namespace Cubo;

// Define global constants
define('DS',DIRECTORY_SEPARATOR);
define('__ROOT__',dirname(__FILE__));
define('__CUBO__',__NAMESPACE__);
define('__BASE__',sprintf("%s://%s",isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',$_SERVER['HTTP_HOST']));
define('__VERSION__','1.1.0');

// Added to allow debugging
if(isset($_GET['debug'])) {
	error_reporting(E_ALL);
	ini_set('display_errors',1);

	// Shows variable
	function show(&$var,$terminate = true) {
		echo "<pre>";
		print_r($var);
		echo "</pre>";
		$terminate && die("Application terminated");
	}
}

// Auto-start Cubo framework
require_once('.autoload.php');
?>