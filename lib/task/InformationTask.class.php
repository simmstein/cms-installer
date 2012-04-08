<?php
	
class InformationTask extends BasicTask {
	public function executeHelp() {
		Cli::printNotice('Cms Installer Help', '');

		foreach(mySfYaml::get('app_rules') as $opt => $info) {
			Cli::printInfo('--'.$opt, $info['description']);
		}
	}

	public function executeVersion() {
		Cli::printNotice('Cms Installer', '');
		Cli::printInfo('Version', CmsInstallerApp::VERSION);
		Cli::printInfo('Author', CmsInstallerApp::AUTHOR);
	}
}
