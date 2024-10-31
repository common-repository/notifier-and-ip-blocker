<?php
/*notifier-and-ip-blocker*/
if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( class_exists( 'NaipbLoader' ) ) return;

/**
* Class NaipbLoader
* Loader class create, truncate, drop and install all tables and plugin settings when plugin is activate, deactivate or uninstall.
*
* @class NaipbLoader
* @version 1.0
* @author Mike Luskavets
*
*/
class NaipbLoader
{

	/**
	* Setup vars.
	*
	* @since 1.0
	*
	* @var
	*
	*/
	private	$slug,
	$dbVersion      = '1.0',
	$dbTableSpam    = 'naipb_spam_list',
	$dbTableBlocked = 'naipb_blocked_list';

	/**
	* Construct.
	*
	* @param string $slug
	*
	* @return void.
	*/
	public function __construct($slug)
	{

		global $wpdb;
		$this->wpdb = $wpdb;
		$this->slug = $slug;
	}

	/**
	* Uses when plugin activate.
	*
	* @since 1.0
	*
	* @return void.
	*/
	public function activate()
	{

		$sql        = 'CREATE TABLE IF NOT EXISTS '.$this->wpdb->prefix.$this->dbTableSpam.' (
		hash varchar(32) NOT NULL,
		ip varchar(20) NOT NULL,
		count int(10) NOT NULL,
		UNIQUE KEY (hash),
		KEY ip (ip))';
		$tableLists = $this->wpdb->query($sql);

		$sql        = 'CREATE TABLE IF NOT EXISTS '.$this->wpdb->prefix.$this->dbTableBlocked.' (
		ip varchar(20) NOT NULL,
		auto int(1) NOT NULL,
		UNIQUE KEY (ip),
		KEY auto (auto))';
		$tableBanned= $this->wpdb->query($sql);

		if($tableLists && $tableBanned){
			add_option($this->slug.'_db_version', $this->dbVersion, '', 'no');
		}
		add_option($this->slug, 'install', '', 'yes');

	}

	/**
	* Uses when plugin deactivate.
	*
	* @since 1.0
	*
	* @return void.
	*/
	public function deactivate()
	{

		if(get_option($this->slug.'_db_truncate')){

			$sql        = 'TRUNCATE '.$this->wpdb->prefix.$this->dbTableSpam;
			$tableLists = $this->wpdb->query($sql);

			$sql        = 'TRUNCATE '.$this->wpdb->prefix.$this->dbTableBlocked;
			$tableBanned= $this->wpdb->query($sql);

			if($tableLists && $tableBanned){
				delete_option($this->slug.'_db_version');
			}

		}
		delete_option($this->slug);
	}

	/**
	* Uses when plugin uninstall.
	*
	* @since 1.0
	*
	* @return void.
	*/
	public function uninstall()
	{

		$sql        = 'DROP TABLE IF EXISTS '.$this->wpdb->prefix.$this->dbTableSpam;

		$tableLists = $this->wpdb->query($sql);

		$sql        = 'DROP TABLE IF EXISTS '.$this->wpdb->prefix.$this->dbTableBlocked;

		$tableBanned= $this->wpdb->query($sql);
		if($tableLists && $tableBanned){
			delete_option($this->slug.'_db_version');
			delete_option($this->slug);
		}

	}
}