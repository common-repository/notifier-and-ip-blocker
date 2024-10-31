<?php
/*notifier-and-ip-blocker*/
if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( class_exists( 'NaipbHelper' ) ) return;

/**
* Class NaipbHelper
* Helper class, methods and functions
*
* @class NaipbHelper
* @version 1.0
* @author Mike Luskavets
*
*/
class NaipbHelper
{

	/**
	* Construct.
	*
	* @since 1.0
	*
	* @return void.
	*/
	public function __construct()
	{

		$this->currentPageUrl = $this->getCurrentPageUrl();
		$this->currentUserIp = $this->getCurrentUserIp();

	}

	/**
	* Get current agent IP Address.
	*
	* @since 1.0
	*
	* @return string.
	*/
	private function getCurrentUserIp()
	{

		if(!empty($_SERVER['HTTP_CLIENT_IP']))
		$ip = $_SERVER['HTTP_CLIENT_IP'];
		elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		else
		$ip = $_SERVER['REMOTE_ADDR'];

		return $ip;
	}

	/**
	* Get content filter. WP filter hook.
	*
	* @since 1.0
	*
	* @param string $content
	*
	* @return html.
	*/
	public function getContent($content)
	{
		$content = apply_filters('the_content', $content);
		$content = str_replace(']]>', ']]&gt;', $content);
		return $content;
	}

	/**
	* Get current page url.
	*
	* @since 1.0
	*
	* @return string.
	*/
	public function getCurrentPageUrl()
	{
		$url = 'http';
		if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') $url .= 's';
		$url .= "://";
		if(isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] != '80') $url .= $_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'].$_SERVER['REQUEST_URI'];
		else $url .= $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
		return $url;
	}

	/**
	* Get remote request by custom url.
	*
	* @since 1.0
	*
	* @param string @url
	*
	* @return string.
	*/
	public function getRequest($url)
	{
		$response = FALSE;
		if(!function_exists('curl_init') && !function_exists('curl_setopt')){
			$cl       = curl_init();
			curl_setopt($cl, CURLOPT_URL, $url);
			curl_setopt($cl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($cl, CURLOPT_TIMEOUT, 10);
			curl_setopt($cl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.16) Gecko/20110319 Firefox/3.6.16");
			$response = curl_exec($cl);
			curl_close($cl);
		}
		else{
			$response = file_get_contents($url);
		}
		return $response;
	}

	/**
	* Compress some text
	* @param string $html
	*
	* @return string.
	*/
	public function htmlCompress($html)
	{
		return preg_replace(array('/<!--(?>(?!\[).)(.*)(?>(?!\]).)-->/Uis','/[[:blank:]]+/'),array('',' '),str_replace(array("\n","\r","\t"),'',$html));
	}

}