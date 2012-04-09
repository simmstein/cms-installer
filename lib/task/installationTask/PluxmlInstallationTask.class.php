<?php
	
class PluxmlInstallationTask extends BasicInstallationTask {
	public function execute() {
		$racine = Cli::prompt('Site url:', true, 'http://localhost/pluxml/');

		$content = array(
			'name'  => Cli::prompt('Your name:', true),
			'login' => Cli::prompt('Username:', true),
			'pwd'   => Cli::prompt('Password:', true, StringUtil::genPassword()),
		);

		
		define('PLX_ROOT', $this->destination.DIRECTORY_SEPARATOR.'pluxml'.DIRECTORY_SEPARATOR);
		define('PLX_CORE', PLX_ROOT.'core'.DIRECTORY_SEPARATOR);
		define('PLX_CONF', PLX_ROOT.'data'.DIRECTORY_SEPARATOR.'configuration'.DIRECTORY_SEPARATOR.'parametres.xml');
		
		require_once PLX_ROOT.DIRECTORY_SEPARATOR.'config.php';
		
		$lang = DEFAULT_LANG;

		loadLang(PLX_CORE.'lang/'.$lang.DIRECTORY_SEPARATOR.'install.php');
		loadLang(PLX_CORE.'lang/'.$lang.DIRECTORY_SEPARATOR.'core.php');	

		require_once PLX_CORE.'lib'.DIRECTORY_SEPARATOR.'class.plx.glob.php';
		require_once PLX_CORE.'lib'.DIRECTORY_SEPARATOR.'class.plx.utils.php';

		if(!is_dir($dir=$this->destination.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'images')) {
			@mkdir($dir, 0755);
		}
		if(!is_dir($dir=$this->destination.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'documents')) {
			@mkdir($dir, 0755);
		}

		$version = trim(file_get_contents(PLX_ROOT.'version'));

		$config = array('title'=>'PluXml',
			'description'=>plxUtils::strRevCheck(L_SITE_DESCRIPTION),
			'meta_description'=>'',
			'meta_keywords'=>'',
			'racine'=>$racine,
			'delta'=>'+00:00',
			'allow_com'=>1,
			'mod_com'=>0,
			'mod_art'=>0,
			'capcha'=>1,
			'style'=>'defaut',
			'clef'=>plxUtils::charAleatoire(15),
			'bypage'=>5,
			'bypage_archives'=>5,
			'bypage_admin'=>10,
			'bypage_admin_coms'=>10,
			'bypage_feed'=>8,
			'tri'=>'desc',
			'tri_coms'=>'asc',
			'images_l'=>800,
			'images_h'=>600,				
			'miniatures_l'=>200,
			'miniatures_h'=>100,
			'images'=>'data/images/',
			'documents'=>'data/documents/',
			'racine_articles'=>'data/articles/',
			'racine_commentaires'=>'data/commentaires/',
			'racine_statiques'=>'data/statiques/',
			'racine_themes'=>'themes/',
			'racine_plugins'=>'plugins/',
			'statiques'=>'data/configuration/statiques.xml',
			'categories'=>'data/configuration/categories.xml',
			'users'=>'data/configuration/users.xml',
			'tags'=>'data/configuration/tags.xml',
			'plugins'=>'data/configuration/plugins.xml',
			'homestatic'=>'',
			'urlrewriting'=>0,
			'gzip'=>0,
			'feed_chapo'=>0,
			'feed_footer'=>'',
			'version'=>$version,
			'default_lang'=>$lang,
			'userfolders'=>0,
		);	

		$this->install($content, $config);

		Cli::printNotice('Finished', 'You can now use the Pluxml.');
	}

	private function install($content, $config) {

# Création du fichier de configuration
		$xml = '<?xml version="1.0" encoding="'.PLX_CHARSET.'"?>'."\n";
		$xml .= '<document>'."\n";
		foreach($config  as $k=>$v) {
			if(is_numeric($v))
				$xml .= "\t<parametre name=\"$k\">".$v."</parametre>\n";
			else
				$xml .= "\t<parametre name=\"$k\"><![CDATA[".plxUtils::cdataCheck($v)."]]></parametre>\n";
		}
		$xml .= '</document>';
		plxUtils::write($xml,PLX_CONF);

# Création du fichier des utilisateurs
		$salt = plxUtils::charAleatoire(10);
		$xml = '<?xml version="1.0" encoding="'.PLX_CHARSET.'"?>'."\n";
		$xml .= "<document>\n";
		$xml .= "\t".'<user number="001" active="1" profil="0" delete="0">'."\n";
		$xml .= "\t\t".'<login><![CDATA['.trim($content['login']).']]></login>'."\n";
		$xml .= "\t\t".'<name><![CDATA['.trim($content['name']).']]></name>'."\n";
		$xml .= "\t\t".'<infos><![CDATA[]]></infos>'."\n";
		$xml .= "\t\t".'<password><![CDATA['.sha1($salt.md5(trim($content['pwd']))).']]></password>'."\n";
		$xml .= "\t\t".'<salt><![CDATA['.$salt.']]></salt>'."\n";
		$xml .= "\t\t".'<email><![CDATA[]]></email>'."\n";
		$xml .= "\t\t".'<lang><![CDATA['.$config['default_lang'].']]></lang>'."\n";
		$xml .= "\t</user>\n";
		$xml .= "</document>";
		plxUtils::write($xml,PLX_ROOT.$config['users']);

# Création du fichier des categories
		$xml = '<?xml version="1.0" encoding="'.PLX_CHARSET.'"?>'."\n";
		$xml .= '<document>'."\n";
		$xml .= "\t".'<categorie number="001" tri="'.$config['tri'].'" bypage="'.$config['bypage'].'" menu="oui" url="'.L_DEFAULT_CATEGORY_URL.'" template="categorie.php"><name><![CDATA['.plxUtils::strRevCheck(L_DEFAULT_CATEGORY_TITLE).']]></name><description><![CDATA[]]></description><meta_description><![CDATA[]]></meta_description><meta_keywords><![CDATA[]]></meta_keywords></categorie>'."\n";
		$xml .= '</document>';
		plxUtils::write($xml,PLX_ROOT.$config['categories']);

# Création du fichier des pages statiques
		$xml = '<?xml version="1.0" encoding="'.PLX_CHARSET.'"?>'."\n";
		$xml .= '<document>'."\n";
		$xml .= "\t".'<statique number="001" active="1" menu="oui" url="'.L_DEFAULT_STATIC_URL.'" template="static.php"><group><![CDATA[]]></group><name><![CDATA['.plxUtils::strRevCheck(L_DEFAULT_STATIC_TITLE).']]></name><meta_description><![CDATA[]]></meta_description><meta_keywords><![CDATA[]]></meta_keywords></statique>'."\n";
		$xml .= '</document>';
		plxUtils::write($xml,PLX_ROOT.$config['statiques']);
		$cs = '<p><?php echo \''.plxUtils::strRevCheck(L_DEFAULT_STATIC_CONTENT).'\'; ?></p>';
		plxUtils::write($cs,PLX_ROOT.$config['racine_statiques'].'001.'.L_DEFAULT_STATIC_URL.'.php');

# Création du premier article
		$xml = '<?xml version="1.0" encoding="'.PLX_CHARSET.'"?>'."\n";
		$xml .= '<document>
			<title><![CDATA['.plxUtils::strRevCheck(L_DEFAULT_ARTICLE_TITLE).']]></title>
			<allow_com>1</allow_com>
			<template><![CDATA[article.php]]></template>
			<chapo>
			<![CDATA[]]>
			</chapo>
			<content>
			<![CDATA[<p>'.plxUtils::strRevCheck(L_DEFAULT_ARTICLE_CONTENT).'</p>]]>
			</content>
			<tags>
			<![CDATA[PluXml]]>
			</tags>
			<meta_description>
			<![CDATA[]]>
			</meta_description>
			<meta_keywords>
			<![CDATA[]]>
			</meta_keywords>
			<title_htmltag>
			<![CDATA[]]>
			</title_htmltag>	
			</document>';
		plxUtils::write($xml,PLX_ROOT.$config['racine_articles'].'0001.001.001.'.@date('YmdHi').'.'.L_DEFAULT_ARTICLE_URL.'.xml');

# Création du fichier des tags servant de cache
		$xml = '<?xml version="1.0" encoding="'.PLX_CHARSET.'"?>'."\n";
		$xml .= '<document>'."\n";
		$xml .= "\t".'<article number="0001" date="'.@date('YmdHi').'" active="1"><![CDATA[PluXml]]></article>'."\n";
		$xml .= '</document>';
		plxUtils::write($xml,PLX_ROOT.$config['tags']);

# Création du fichier des plugins
		$xml = '<?xml version="1.0" encoding="'.PLX_CHARSET.'"?>'."\n";
		$xml .= '<document>'."\n";
		$xml .= '</document>';
		plxUtils::write($xml,PLX_ROOT.$config['plugins']);

# Création du premier commentaire
		$xml = '<?xml version="1.0" encoding="'.PLX_CHARSET.'"?>'."\n";
		$xml .= '<comment>
			<author><![CDATA[pluxml]]></author>
			<type>normal</type>
			<ip>127.0.0.1</ip>
			<mail><![CDATA[contact@pluxml.org]]></mail>
			<site><![CDATA[http://www.pluxml.org]]></site>
			<content><![CDATA['.plxUtils::strRevCheck(L_DEFAULT_COMMENT_CONTENT).']]></content>
			</comment>';
		plxUtils::write($xml,PLX_ROOT.$config['racine_commentaires'].'0001.'.@date('U').'-1.xml');
	}
}
