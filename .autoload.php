<?php
/**
 * @application    Cubo RestAPI
 * @type           Protected code
 * @class          n/a
 * @description    The .autoload.php script presets constants, automates registration of classes, loads the configuration,
 *                 and finally starts the application; this script is called from index.php
 * @version        1.0.0
 * @date           2019-02-02
 * @author         Dan Barto
 * @copyright      Copyright (C) 2017 - 2019 Papiando Riba Internet
 * @license        MIT License; see LICENSE.md
 */
namespace Cubo;

// Auto-register classes
spl_autoload_register(function($class) {
	// Get the last part of the class (since all classes will be named Cubo\*)
	$class = basename(str_replace('\\','/',$class));
	// Set path names
	$frameworkPath = __ROOT__.DS.'framework'.DS.strtolower($class).'.class.php';
	$modelPath = __ROOT__.DS.'model'.DS.strtolower($class).'.model.php';
	$viewPath = __ROOT__.DS.'view'.DS.str_replace('view','',strtolower($class)).'.view.php';
	$controllerPath = __ROOT__.DS.'controller'.DS.str_replace('controller','',strtolower($class)).'.controller.php';
	// Include if file exists
	if(file_exists($frameworkPath))
		require_once($frameworkPath);
	elseif(file_exists($modelPath))
		require_once($modelPath);
	elseif(file_exists($viewPath) && strpos($class,'View') > 0)
		require_once($viewPath);
	elseif(file_exists($controllerPath) && strpos($class,'Controller') > 0)
		require_once($controllerPath);
	else
		throw new \Exception("Failed to include class '{$class}'");
});

// Retrieve configuration parameters
include_once('.config.php');

// Start the application
new Application();
?>