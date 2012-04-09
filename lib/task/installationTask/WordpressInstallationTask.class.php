<?php
	
class WordpressInstallationTask extends BasicInstallationTask {
	public function execute() {
		Cli::printNotice('Installation', 'Please complete the form:');
		Cli::printBlankLine();

		Cli::printNotice('Database configuration', '');
		$this->db_name     = Cli::prompt('Database name:');	
		$this->db_username = Cli::prompt('Database username:', true, 'root');	
		$this->db_password = Cli::prompt('Database password:', false, '');	
		$this->db_hostname = Cli::prompt('Database hostname:', true, 'localhost');
		$this->db_charset  = Cli::prompt('Database charset:', true, 'utf8');
		$this->db_prefix   = Cli::prompt('Database table prefix:', true, 'wp_');
		Cli::printBlankLine();

		Cli::printNotice('Security configuration', '');
		$this->auth_key         = Cli::prompt('Auth key (unique):', true, StringUtil::genPassword());
		$this->secure_auth_key  = Cli::prompt('Secure auth key (unique):', true, StringUtil::genPassword());
		$this->logged_in_key    = Cli::prompt('Logged in key (unique):', true, StringUtil::genPassword());
		$this->nonce_key        = Cli::prompt('Nonce key (unique):', true, StringUtil::genPassword());
		$this->secure_auth_salt = Cli::prompt('Secure auth salt (unique):', true, StringUtil::genPassword());
		$this->auth_salt        = Cli::prompt('Auth salt (unique):', true, StringUtil::genPassword());
		$this->logged_in_salt   = Cli::prompt('Logged in salt (unique):', true, StringUtil::genPassword());
		$this->nonce_salt       = Cli::prompt('Nonce in salt (unique):', true, StringUtil::genPassword());
		Cli::printBlankLine();

		Cli::printNotice('Wordpress configuration', '');
		$this->wplang = Cli::prompt('Lang', true, 'fr_FR');
		$this->wptitle = Cli::prompt('Blog title', true, 'My new blog');
		Cli::printBlankLine();

		Cli::printNotice('Admin user configuration', '');
		$this->username = Cli::prompt('Admin username:', true, 'admin');
		$this->password = Cli::prompt('Admin password:', true, StringUtil::genPassword());

		$this->email = null;
		do {
			if($this->email !== null) {
				Cli::printNotice(' > Please enter en valid email.', '');
			}
			$this->email = Cli::prompt('Admin email:', true);
		}
		while(!filter_var($this->email, FILTER_VALIDATE_EMAIL));

		Cli::printBlankLine();

		try {
			$this->setDatabaseConfigurationFile();
			//$this->createDatabase();
		}
		catch(CmsInstallerException $e) {
			Cli::printError('Error during configuration', $e->getMessage());
		}
	}

	private function setDatabaseConfigurationFile() {
		$configSample = $this->destination.DIRECTORY_SEPARATOR.'wordpress'.DIRECTORY_SEPARATOR.'wp-config-sample.php';
		$configFile   = $this->destination.DIRECTORY_SEPARATOR.'wordpress'.DIRECTORY_SEPARATOR.'wp-config.php';

		if(!file_exists($configSample)) {
			throw new CmsInstallerException('Database configuration sample does not exist.');
		}


$config = <<<EOF
<?php
define('DB_NAME', '$this->db_name');
define('DB_USER', '$this->db_username');
define('DB_PASSWORD', '$this->db_password');
define('DB_HOST', '$this->db_hostname');
define('DB_CHARSET', '$this->db_charset');
define('DB_COLLATE', '');
define('AUTH_KEY',         '$this->auth_key'); 
define('SECURE_AUTH_KEY',  '$this->secure_auth_key'); 
define('LOGGED_IN_KEY',    '$this->logged_in_key'); 
define('NONCE_KEY',        '$this->nonce_key'); 
define('AUTH_SALT',        '$this->auth_salt'); 
define('SECURE_AUTH_SALT', '$this->secure_auth_salt'); 
define('LOGGED_IN_SALT',   '$this->logged_in_salt'); 
define('NONCE_SALT',       '$this->nonce_salt'); 
\$table_prefix  = '$this->db_prefix';
define('WPLANG', '$this->wplang');
define('WP_DEBUG', false); 
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');
require_once(ABSPATH . 'wp-settings.php');
EOF;
		
		if(!@file_put_contents($configFile, $config)) {
			throw new CmsInstallerException('Error while creation database configuration file.');
		}

		Cli::printNotice('Database configuration file', 'done.');
	}
}
