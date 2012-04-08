<?php
	
class InformationTask extends BasicTask {
	public function executeHelp() {
		foreach($this->cmsInstallerApp->getOpts()->getRules() as $opt => $rule) {
			if(($this->value !== true && $this->value == $opt) || $this->value === true) {
				$name        = '--'.$opt.(isset($rule['alias'][1]) ? ',-'.$rule['alias'][1] : '');
				$param       = mySfYaml::get('app_configuration_'.$opt.'_param');
				$description = mySfYaml::get('app_configuration_'.$opt.'_description');

				Cli::printNotice($opt, '');
				Cli::printInfo(' Command', $name);
				if(!empty($param)) {
					Cli::printInfo(' Param', $param);
				}
				Cli::printInfo(' Description', $description);
				Cli::printBlankLine();
			}
		}
	}

	public function executeVersion() {
		Cli::printNotice('Cms Installer', '');
		Cli::printBlankLine();
		Cli::printInfo('Version', mySfYaml::get('app_version'));
		Cli::printInfo('Author', mySfYaml::get('app_author'));
		Cli::printInfo('Contact', mySfYaml::get('app_contact'));
	}
}
