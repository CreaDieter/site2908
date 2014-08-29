<?php
	/* This partial adds all needed files for bootstrap */

	// javascript files
	$this->headScript()->appendFile('/js/bootstrap.min.js');

	// css files
	$this->headLink()->appendStylesheet('/css/bootstrap.min.css');
	$this->headLink()->appendStylesheet('/css/bootstrap-theme.min.css');