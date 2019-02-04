<?php
/**
 * @application    Cubo CMS
 * @type           Protected code
 * @class          n/a
 * @description    The .autoload.php script presets constants, automates registration of classes, loads the configuration,
 *                 and finally starts the application; this script is called from index.php
 * @version        1.2.0
 * @date           2019-02-03
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
	$pluginPath = __ROOT__.DS.'plugin'.DS.str_replace('plugin','',strtolower($class)).'.plugin.php';
	$modulePath = __ROOT__.DS.'module'.DS.str_replace('module','',strtolower($class)).'.module.php';
	// Include if file exists
	if(file_exists($frameworkPath))
		require_once($frameworkPath);
	elseif(file_exists($modelPath))
		require_once($modelPath);
	elseif(file_exists($viewPath) && strpos($class,'View') > 0)
		require_once($viewPath);
	elseif(file_exists($controllerPath) && strpos($class,'Controller') > 0)
		require_once($controllerPath);
	elseif(file_exists($pluginPath) && strpos($class,'Plugin') > 0)
		require_once($pluginPath);
	elseif(file_exists($modulePath) && strpos($class,'Module') > 0)
		require_once($modulePath);
	else
		throw new \Exception("Failed to include class '{$class}'");
});

// Detect install; if .config.php does not exist, then assume that it's a fresh install
if(file_exists(__ROOT__.DS.'.config.php')) {
	// Retrieve configuration parameters
	include_once('.config.php');
	// Start the application
	new Application();
} else {
	// Run installer
	new Installation();
}
?>