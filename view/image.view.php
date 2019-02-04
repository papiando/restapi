<?php
/**
 * @application    Cubo CMS
 * @type           View
 * @class          ArticleView
 * @description    The view that generates and prepares the output in different formats for the article object
 * @version        1.2.0
 * @date           2019-02-03
 * @author         Dan Barto
 * @copyright      Copyright (C) 2017 - 2019 Papiando Riba Internet
 * @license        MIT License; see LICENSE.md
 */
namespace Cubo;

defined('__CUBO__') || new \Exception("No use starting a class without an include");

class ImageView extends View {
	public function html($data = array('output'=>"No data")) {
		$this->_data = $data;
		if(file_exists($this->getPath()) || file_exists($this->getDefaultPath())) {
			// Write output to buffer
			include($this->path);
			return;
		} else {
			throw new \Exception("Template file '{$this->path}' does not exist");
		}
	}
}
?>