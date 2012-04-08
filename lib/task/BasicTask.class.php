<?php

class BasicTask {
	protected $value;
	protected $cmsInstallerApp;

	public function __construct($value, CmsInstallerApp $app) {
		$this->value = $value;
		$this->cmsInstallerApp = $app;
	}
}
