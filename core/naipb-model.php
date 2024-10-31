<?php
/*notifier-and-ip-blocker*/
if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( class_exists( 'NaipbModel' ) ) return;

/**
* Class NaipbModel
* This class main model
*
* @class NaipbModel
* @version 1.0
* @author Mike Luskavets
*
*/
class NaipbModel
{

	/**
	* Version database. Set global tables name
	*
	* @since 1.0
	*
	* @var
	*
	*/
	private	$dbVersion      = '1.0',
	$dbTableSpam    = 'naipb_spam_list',
	$dbTableBlocked = 'naipb_blocked_list';

	/**
	* Construct.
	*
	* @since 1.0
	*
	* @param string $path
	* @param string $url
	* @param string $domain
	* @param string $slug
	* @param string $basename
	*
	* @return void.
	*/
	public function __construct($path, $url, $domain, $slug, $basename)
	{

		global $wpdb;
		$this->wpdb = $wpdb;

		$this->dirnamePath = $path;
		$this->dirnameUrl = $url;
		$this->domain = $domain;
		$this->slug = $slug;
		$this->basename = $basename;
	}

	/**
	* Get settings in database
	*
	* @since 1.0
	*
	* @param array $settings
	*
	* @return array
	*/
	public function getSettings($settings)
	{
		$newSettings = array();
		if(!empty($settings)){
			foreach($settings as $key){
				$value = get_option($this->slug.'_'.$key);
				if($key == 'message' || $key == 'blocked_ip_page_content') $value = html_entity_decode($value);
				$newSettings[$key] = $value;
			}
		}
		return $newSettings;
	}

	/**
	* Update settings in database
	*
	* @since 1.0
	*
	* @param array $settings
	*
	* @return void.
	*/
	public function updateSettings($settings)
	{
		if(!empty($settings)){
			foreach($settings as $key => $value){
				if($key == 'subject') $value = esc_html($value);
				elseif($key == 'message' || $key == 'blocked_ip_page_content') $value = htmlentities(stripslashes($value), ENT_QUOTES, 'UTF-8');
				update_option($this->slug.'_'.$key, $value);
			}
		}
	}

	/**
	* Delete settings in database
	*
	* @since 1.0
	*
	* @param array $settings
	*
	* @return void.
	*/
	public function deleteOption($settings)
	{
		if(!empty($settings)){
			foreach($settings as $key => $value){
				delete_option($this->slug.'_'.$key);
			}
		}
	}

	/**
	* Get blocked IP lists
	*
	* @since 1.0
	*
	* @return object.
	*/
	public function getBlockedIpLists()
	{
		return $this->wpdb->get_results("SELECT * FROM ".$this->wpdb->prefix.$this->dbTableBlocked);
	}

	/**
	* Search blocked IP
	*
	* @since 1.0
	*
	* @param string $search
	*
	* @return object.
	*/
	public function getSearchBlockedIpLists($search)
	{
		return $this->wpdb->get_results("SELECT * FROM ".$this->wpdb->prefix.$this->dbTableBlocked." WHERE ip LIKE '%".$search."%'");
	}

	/**
	* Get IP from spam lists
	*
	* @since 1.0
	*
	* @param array $where
	*
	* @return object|bool.
	*/
	public function getSpamIp($where)
	{
		$r = $this->wpdb->get_row("SELECT * FROM ".$this->wpdb->prefix.$this->dbTableSpam." WHERE hash = '".$where['hash']."'");
		return !is_null($r) ? $r : FALSE;
	}

	/**
	* Add IP from spam list
	*
	* @since 1.0
	*
	* @param array $set
	*
	* @return bool.
	*/
	public function addSpamIp($set)
	{
		return $this->wpdb->insert($this->wpdb->prefix.$this->dbTableSpam, $set);
	}

	/**
	* Update spam amount in spam lists
	*
	* @since 1.0
	*
	* @param array $set
	* @param array $where
	*
	* @return bool.
	*/
	public function updateSpamIp($set, $where)
	{
		return $this->wpdb->update($this->wpdb->prefix.$this->dbTableSpam, $set, $where);
	}

	/**
	* Get spam amount by IP
	*
	* @since 1.0
	*
	* @param array $where
	*
	* @return integer.
	*/
	public function getSpamIpCount($where)
	{
		$r = $this->wpdb->get_var("SELECT COUNT(*) FROM ".$this->wpdb->prefix.$this->dbTableSpam." WHERE ip = '".$where['ip']."' AND count = '1' GROUP BY ip");
		return ($r) ? $r : 0;
	}

	/**
	* Ckeck IP is blocked or not
	*
	* @since 1.0
	*
	* @param array $where
	*
	* @return integer|bool.
	*/
	public function ckeckBlockedIp($where)
	{
		$r = $this->wpdb->get_row("SELECT *, COUNT(*) as count FROM ".$this->wpdb->prefix.$this->dbTableBlocked." WHERE ip = '".$where['ip']."'");
		return ($r->count > 0) ? $r : FALSE;
	}

	/**
	* Add IP from blocked lists
	*
	* @since 1.0
	*
	* @param array $set
	*
	* @return bool.
	*/
	public function addBlockedIp($set)
	{
		return $this->wpdb->insert($this->wpdb->prefix.$this->dbTableBlocked, $set);
	}

	/**
	* Delete IP from blocked lists
	*
	* @since 1.0
	*
	* @param array $set
	*
	* @return bool.
	*/
	public function deleteBlockedIp($where)
	{
		$r = $this->wpdb->query("DELETE FROM ".$this->wpdb->prefix.$this->dbTableBlocked." WHERE ip = '".$where['ip']."' AND auto = '".$where['auto']."'");
		return ($r)?TRUE:FALSE;
	}

}