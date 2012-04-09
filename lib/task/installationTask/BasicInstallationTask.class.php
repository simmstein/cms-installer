<?php

abstract class BasicInstallationTask {
	protected $destination;

	public function __construct($destination) {
		$this->destination = getcwd().DIRECTORY_SEPARATOR.$destination;
	}
	
	abstract public function execute();
}
