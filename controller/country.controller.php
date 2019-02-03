<?php
/**
 * @application    Cubo RestAPI
 * @type           Controller
 * @class          CountryController
 * @description    The controller that holds the methods for the country object
 * @version        1.0.0
 * @date           2019-02-02
 * @author         Dan Barto
 * @copyright      Copyright (C) 2017 - 2019 Papiando Riba Internet
 * @license        MIT License; see LICENSE.md
 */
namespace Cubo;

defined('__CUBO__') || new \Exception("No use starting a class without an include");

class CountryController extends Controller {
	protected $columns = "`code`,`name`";
}
?>