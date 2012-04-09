<?php

abstract class BasicInstallationTask {
	protected $destination;

	public function __construct($destination) {
		$this->destination = $destination;
	}
	
	abstract public function execute();
}
