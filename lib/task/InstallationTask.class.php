<?php
	
class InstallationTask extends BasicTask {
	public function executeInstall() {
		$this->arg_cms     = $this->value;
		$this->arg_version = ($v = $this->cmsInstallerApp->get('version')) ? $v : 'default';

		$this->cms_configuration = mySfYaml::get('ressources_cms_'.$this->arg_cms);
		$this->cms_version       = mySfYaml::get('ressources_cms_'.$this->arg_cms.'_versions_'.$this->arg_version);
		$this->cms_version_url   = mySfYaml::get('ressources_cms_'.$this->arg_cms.'_versions_'.$this->arg_version.'_url');

		try {
			if(!$this->cms_configuration) {
				throw new CmsInstallerException($arg_cms.' is not a valid cms. Show cms list by using --list argument.');
			}

			if(!$this->cms_version) {
				throw new CmsInstallerException($this->arg_version.' is not a valid cms version for '.$this->arg_cms.'. Show cms list by using --list argument.');
			}

			if(!$this->cms_version_url) {
				throw new CmsInstallerException($this->arg_cms.' (version '.$this->arg_version.') has not valid configuration (url not found). Show cms list by using --list argument.');
			}

			Cli::printNotice('CMS: ', $this->arg_cms);
			Cli::printNotice('Version: ', $this->arg_version);
			Cli::printNotice('Download from: ', $this->cms_version_url);
			Cli::printBlankLine();

			$this->tempName = $this->getTempName();
			$this->download();
			$this->unpack();			
		}
		catch(CmsInstallerException $e) {
			Cli::printError('Error', $e->getMessage());
			exit(1);
		}

		exit(0);	
	}

	public function download() {
		Cli::printInfo('Download started', 'Please wait...');
	
		$fileopen = fopen($this->tempName, 'w');
		$curl = curl_init($this->cms_version_url);
		curl_setopt($curl, CURLOPT_FILE, $fileopen);
		$data = curl_exec($curl);
		curl_close($curl);
		fclose($fileopen);

		if(!$data) {
			unlink($this->tempName);	
			throw new CmsInstallerException('Download has failed.');
		}

		Cli::printInfo('Finished', '');
	}

	public function unpack() {
		Cli::printInfo('Unpack', 'Please wait...');

		

		Cli::printInfo('Finished', '');
	}

	public function getTempName() {
		return $this->arg_cms.'-'.mt_rand();
	}
}
