<?php

class InstallationTask extends BasicTask {
	public function executeInstall() {
		$this->arg_cms     = $this->getValue();
		$this->arg_version = ($v = $this->getCmsInstallerApp()->get('version')) ? $v : 'default';

		$this->cms_configuration = mySfYaml::get('ressources_cms_'.$this->arg_cms);
		$this->cms_version       = mySfYaml::get('ressources_cms_'.$this->arg_cms.'_versions_'.$this->arg_version);
		$this->cms_version_url   = mySfYaml::get('ressources_cms_'.$this->arg_cms.'_versions_'.$this->arg_version.'_url');
		$this->cms_callback      = mySfYaml::get('ressources_cms_'.$this->arg_cms.'_versions_'.$this->arg_version.'_callback');
		$this->destination       = ($to = $this->getCmsInstallerApp()->get('to')) ? $to : '.';

		try {
			if(!$this->cms_configuration) {
				throw new CmsInstallerException($this->arg_cms.' is not a valid cms. Show cms list by using --list argument.');
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
			echo PHP_EOL;
			$this->unpack();	
			$this->clearFile();

			if($this->cms_callback) {
				$callback = $this->cms_callback.'InstallationTask';

				if(!class_exists($callback)) {
					throw new CmsInstallerException($this->arg_cms.' (version '.$this->arg_version.') has not valid configuration (callback "'.$callback.'" class not found).');
				}
				Cli::printBlankLine();
				$callback = new $callback($this->destination);
				$callback->execute();
			}
		}
		catch(CmsInstallerException $e) {
			Cli::printError('Error', $e->getMessage());
			exit(1);
		}

		exit(0);	
	}

	public static function progressBar($curl, $fd) {
		if($curl) {
			$purcent  = round(100*$fd/$curl, 2);
			$progress = str_pad($purcent.'%', 5, ' ', STR_PAD_RIGHT);
			echo "\r";
			Cli::printInfoNoEOL('Progress', $progress);
		}
	}

	public function download() {
		Cli::printInfo('Download started', 'Please wait...');
	
		$fileopen = fopen($this->tempName, 'w');
		$curl     = curl_init($this->cms_version_url);

		curl_setopt($curl, CURLOPT_FILE, $fileopen);
		curl_setopt($curl, CURLOPT_NOPROGRESS, false);
		curl_setopt($curl, CURLOPT_PROGRESSFUNCTION, 'InstallationTask::progressBar');
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);


		if(curl_errno($curl)) {
			fclose($fileopen);
			$this->clearFile();
			throw new CmsInstallerException('Download has failed.');
		}

		curl_exec($curl);
		curl_close($curl);
		fclose($fileopen);
	}

	public function unpack() {
		Cli::printInfo('Unpack', 'Please wait...');

		$finfo = new finfo(FILEINFO_MIME);
		$ftype = explode(';', $finfo->file($this->tempName));
		$type  = $ftype[0];

		if(!is_dir($this->destination)) {
			throw new CmsInstallerException('Extract destination does not exist (extract to '.$this->destination.').');
		}
		else {
			if(!is_writable($this->destination)) {
				throw new CmsInstallerException('Extract destination is not writable (extract to '.$this->destination.').');
			}
		}

		
		if(in_array($type, array('application/zip', 'application/x-zip', 'application/x-zip-compressed'))) {
			$zip = new ZipArchive();
			if(!$zip->open($this->tempName)) {
				throw new CmsInstallerException('ZipArchive can not open package.');
			}

			$zip->extractTo($this->destination);
			$zip->close();
			Cli::printInfo('Extact to', $this->destination);
		}
		elseif(in_array($type, array('application/x-tar', 'application/x-gtar', ))) {
			throw new CmsInstallerException('Sorry but tar files are not supported yet...');
		}
		else {
			throw new CmsInstallerException('The archive is either in unknown format or damaged.');
		}
	}

	public function getTempName() {
		return $this->arg_cms.'-'.mt_rand();
	}

	public function clearFile() {
		if(is_file($this->tempName)) {
			unlink($this->tempName);
		}
	}
}
