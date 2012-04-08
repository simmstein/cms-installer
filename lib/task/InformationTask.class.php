<?php
	
class InformationTask extends BasicTask {
	public function executeHelp() {
		foreach($this->cmsInstallerApp->getOpts()->getRules() as $opt => $rule) {
			$name        = '--'.$opt.(isset($rule['alias'][1]) ? '|-'.$rule['alias'][1] : '');
			$param       = mySfYaml::get('app_configuration_'.$opt.'_param');
			$description = mySfYaml::get('app_configuration_'.$opt.'_description');

			Cli::printNotice($opt, '');
			Cli::printInfo(' Command(s):', $name);
			if(!empty($param)) {
				Cli::printInfo(' Param(s):', $param);
			}
			Cli::printInfo(' Description:', $description);
			Cli::printBlankLine();
		}
	}

	public function executeVersion() {
		Cli::printNotice('Cms Installer', '');
		Cli::printBlankLine();
		Cli::printInfo('Version', CmsInstallerApp::VERSION);
		Cli::printInfo('Author', CmsInstallerApp::AUTHOR);
	}
}
