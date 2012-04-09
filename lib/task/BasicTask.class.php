<?php

abstract class BasicTask {
	private $value;
	private $cmsInstallerApp;

	public function __construct($value, CmsInstallerApp $app) {
		$this->value = $value;
		$this->cmsInstallerApp = $app;
	}

	protected function getValue() {
		return $this->value;
	}

	protected function getCmsInstallerApp() {
		return $this->cmsInstallerApp;
	}
}
