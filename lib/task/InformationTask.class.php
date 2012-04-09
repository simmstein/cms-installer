<?php
	
class InformationTask extends BasicTask {
	public function executeHelp() {
		foreach($this->getCmsInstallerApp()->getOpts()->getRules() as $opt => $rule) {
			if(!mySfYaml::get('app_configuration_'.$opt.'_hide')) {
				if(($this->getValue() !== true && $this->getValue() == $opt) || $this->getValue() === true) {
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
	}

	public function executeVersion() {
		Cli::printNotice('Cms Installer', '');
		Cli::printBlankLine();
		Cli::printInfo('Version', mySfYaml::get('app_version'));
		Cli::printInfo('Author', mySfYaml::get('app_author'));
		Cli::printInfo('Contact', mySfYaml::get('app_contact'));
	}

	public function executeList() {
		$cmss = mySfYaml::get('ressources_cms');

		if(is_array($cmss)) {
			foreach($cmss as $cms_name => $cms_info) {
				if(($this->getValue() !== true && $this->getValue() == $cms_name) || $this->getValue() === true) {
					Cli::printNotice($cms_name, '');
					if(isset($cms_info['versions']) && is_array($cms_info['versions'])) {
						foreach($cms_info['versions'] as $version => $info) {
							$description = !empty($info['description']) ? $info['description'] : 'No description.';
							Cli::printInfo(' '.$version, $description);
							Cli::printInfo(' ', $info['url']);
							Cli::printInfo(' ', $this->getCmsInstallerApp()->getArgv(0).' --install "'.$cms_name.'" --version "'.$version.'"');
							Cli::printBlankLine();
						}
					}
				}
			}
		}
	}
}
