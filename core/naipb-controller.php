<?php
/*notifier-and-ip-blocker*/
if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( class_exists( 'NaipbController' ) ) return;

/**
* Class NaipbController
* This class main controller
*
* @class NaipbController
* @version 1.0
* @author Mike Luskavets
*
*/
class NaipbController
{
	/**
	* Version plugin
	*
	* @since 1.0
	*
	* @var
	*
	*/
	private $version = '1.0';

	/**
	* Default settings
	*
	* @since 1.0
	*
	* @var
	*
	*/
	private $settings = array(
		'enable',
		'comment_need_pending',
		'enabled_cf7',
		'subject',
		'message',
		'enable_block_ip',
		'blocked_ip_login_page_exclude',
		'blocked_ip_manual_cant_unlock',
		'blocked_ip_page_noindex',
		'auto_block_ip_amount',
		'blocked_ip_page_title',
		'blocked_ip_page_content',
		'enable_grecaptca',
		'grecaptca_site_key',
		'grecaptca_secret_key',
		'db_truncate',
	);

	/**
	* Plugin shortcodes
	*
	* @since 1.0
	*
	* @var
	*
	*/
	private $shortcodes = array(
		'[name]',
		'[email]',
		'[url]',
		'[sitename]',
		'[siteurl]',
		'[blockedipurl]',
	);

	/**
	* Construct.
	*
	* @since 1.0
	*
	* @return void.
	*/
	public function __construct($path, $url, $domain, $slug, $basename)
	{

		$this->dirnamePath = $path;
		$this->dirnameUrl = $url;
		$this->domain = $domain;
		$this->slug = $slug;
		$this->basename = $basename;

		require_once $this->dirnamePath.'/inc/class-naipb-helper.php';
		require_once $this->dirnamePath.'/core/naipb-model.php';

		$this->helper = new NaipbHelper();
		$this->model = new NaipbModel($path, $url, $domain, $slug, $basename);

		$this->init();
	}

	/**
	* Initial method
	*
	* @since 1.0
	*
	* @return void
	*/
	public function init()
	{

		add_action( 'plugins_loaded', array($this,'loadPluginTextDomain') );

		$this->showPriviewBlockedPage = (isset($_GET['naipb']) && $_GET['naipb'] == 'blockedpagepreview') ? TRUE : FALSE;
		$this->addBlokerdIpHash = (isset($_GET['naipbspam']) && $_GET['naipbspam'] != '') ? $_GET['naipbspam'] : FALSE;

		$this->checkBlocked();
		$this->addBlockedIp();

		$this->supportCF7 = class_exists('WPCF7') ? TRUE: FALSE;

		if(get_option($this->slug) && get_option($this->slug) == 'install'){
			update_option($this->slug, 'installed');
			$this->settings = $this->setDefaultSettings();
		}

		add_action('admin_menu', array($this,'addMenu'));
		add_action('wp_ajax_'.$this->slug, array($this,'initAjaxAdmin'));

		add_action('comment_post', array($this,'sendLetterAfterCommentSent'), 11, 2);

		if($this->supportCF7){
			add_action('wpcf7_before_send_mail', array($this,'sendLetterAfterCF7Sent'), 10, 1);
		}

	}

	/**
	* Initial ajax method
	*
	* @since 1.0
	*
	* @return void
	*/
	public function initAjaxAdmin()
	{
		if(check_ajax_referer($this->slug, 'security')){
			if(isset($_POST['method']) && $_POST['method'] != ''){
				if($_POST['method'] == 'deleteip'){
					$ip = (isset($_POST['ip']) && $_POST['ip'] != '') ? $_POST['ip'] : FALSE;
					$auto = (isset($_POST['auto'])) ? $_POST['auto'] : FALSE;
					if($ip){
						$this->model->deleteBlockedIp(array('ip'  =>$ip,'auto'=>$auto));
					}
				}
			}
		}
		else{
			wp_die('Could not verify nonce');
		}
		wp_die();
	}

	/**
	* Init text domain
	*
	* @since 1.0
	*
	* @return void.
	*/
	public function loadPluginTextDomain()
	{

		$locale = apply_filters('plugin_locale', get_locale(), $this->domain);

		if( $loaded = load_textdomain( $this->domain, trailingslashit( WP_LANG_DIR ) . $this->domain . '/' . $this->domain . '-' . $locale . '.mo' ) )
		return $loaded;
		else
		load_plugin_textdomain( $this->domain, FALSE, $this->domain . '/languages' );

	}

	/**
	* Add menu to admin panel
	*
	* @since 1.0
	*
	* @return void.
	*/
	public function addMenu()
	{
		add_menu_page (
			__( 'Notifier and IP Blocker', $this->domain ),
			__( 'N&IP Blocker', $this->domain ),
			'manage_options',
			$this->domain.'-settings',
			array($this,'menuSettings'),
			$this->dirnameUrl.'/assets/images/icon.png',
			NULL
		);
	}

	/**
	* Initial settings page in admin panel
	*
	* @since 1.0
	*
	* @return
	*/
	public function menuSettings()
	{

		$updated = $error   = FALSE;

		if( isset( $_POST[$this->slug] ) && ! wp_verify_nonce( $_POST['_wpnonce'], $this->slug ) )
		wp_die( 'Could not verify nonce' );

		$method = (isset($_POST['method']) && $_POST['method'] != '') ? $_POST['method'] : 'Error';

		if($method == 'update_settings'){
			foreach($this->settings as $key){

				$value = isset($_POST[$this->slug.'_'.$key]) ? $_POST[$this->slug.'_'.$key] : '';

				if(
					$key == 'enable' ||
					$key == 'comment_need_pending' ||
					$key == 'enabled_cf7' ||
					$key == 'enable_block_ip' ||
					$key == 'blocked_ip_login_page_exclude' ||
					$key == 'blocked_ip_page_noindex' ||
					$key == 'blocked_ip_manual_cant_unlock' ||
					$key == 'enable_grecaptca' ||
					$key == 'db_truncate'
				){
					$value = isset($_POST[$this->slug.'_'.$key]) ? 1 : 0;
				}

				$this->settings[$key] = $value;
			}

			$this->model->updateSettings($this->settings);

			$updated = TRUE;
			$update_message = esc_html('Settings saved', $this->domain);
		}
		elseif($method == 'restore_default_settings'){

			$this->SetDefaultSettings();
			$updated = TRUE;
			$update_message = esc_html('Settings restored default', $this->domain);

		}
		elseif($method == 'add_ip'){

			$ip = (isset($_POST['ip']) && !empty($_POST['ip'])) ? $_POST['ip'] : FALSE;
			if($ip){
				$updated = TRUE;
				if($this->model->ckeckBlockedIp(array('ip'=>$ip))){
					$error = TRUE;
					$update_message = esc_html('IP Address is already added', $this->domain);
				}
				else{
					$this->model->addBlockedIp(array('ip'  =>$ip,'auto'=>0));
					$update_message = esc_html('IP Address has been added', $this->domain);
				}
			}

		}

		if($method == 'search_ip'){
			$search = (isset($_POST['ip']) && !empty($_POST['ip'])) ? $_POST['ip'] : FALSE;
			if($search){
				$this->blockedIpLists = $this->model->getSearchBlockedIpLists($search);
			}
			else{
				$this->blockedIpLists = $this->model->getBlockedIpLists();
			}

		}
		else{
			$this->blockedIpLists = $this->model->getBlockedIpLists();
		}

		$this->settings = $this->model->getSettings($this->settings);

		wp_enqueue_script('admin-naipb-scripts', $this->dirnameUrl.'/assets/js/admin-notifier-and-ip-blocker.js', array('jquery'), '', true );

		wp_localize_script('admin-naipb-scripts', 'naipb_ajax_object', array(
				'ajax_url'=>	admin_url('admin-ajax.php?action='.$this->slug),
				'security'=>	wp_create_nonce($this->slug),
			));

		require_once $this->dirnamePath.'/views/tpl-admin-settings.php';

	}

	/**
	* Send letter after comment sent
	*
	* @since 1.0
	*
	* @param integer $comment_ID
	* @param integer $approved
	*
	* @return void.
	*/
	public function sendLetterAfterCommentSent($comment_ID, $approved)
	{

		if(!$this->settings['enable']) return;

		if(!$this->settings['comment_need_pending'] || $this->settings['comment_need_pending'] && $approved == 0){

			$comment = get_comment($comment_ID);

			$this->letter['username'] = trim($comment->comment_author);
			$this->letter['email'] = $comment->comment_author_email;
			$this->letter['url'] = get_comment_link($comment);

			$this->getLetter();

			$this->sentLetter();

			$this->addSpamIp();

		}

	}

	/**
	* Send letter after CF7 form submit
	*
	* @since 1.0
	*
	* @param object $cf7
	*
	* @return void.
	*/
	public function sendLetterAfterCF7Sent($cf7)
	{

		if(!$this->settings['enable']) return;
		if(!$this->settings['enabled_cf7']) return;

		$this->letter['username'] = trim($_POST['your-name']);
		$this->letter['email'] = trim($_POST['your-email']);
		$this->letter['url'] = $this->helper->currentPageUrl;

		$this->getLetter();

		$this->sentLetter();

		$this->addSpamIp();

	}

	/**
	* Set default settings
	*
	* @since 1.0
	*
	* @return void.
	*/
	private function setDefaultSettings()
	{

		$this->settings['enable'] = 1;
		$this->settings['comment_need_pending'] = 1;
		$this->settings['enabled_cf7'] = (int)$this->supportCF7;
		$this->settings['subject'] = __( 'Notify from [sitename]', $this->domain );
		$this->settings['message'] = __( '
			<strong>Hello, [name], We got your message.</strong>
			<strong>Answer would be sent as soon as possible.</strong>

			&nbsp;

			<strong>If you didn\'t do this, someone has done it for you from the page [url], using your e-mail [email]. We are sorry for this email (we are apologize).</strong>

			<strong>You can tell us what is spam [blockedipurl]</strong>

			&nbsp;

			<strong>Sincerely, [sitename]</strong>
			<strong>[siteurl]</strong>
			', $this->domain);
		$this->settings['enable_block_ip'] = 1;
		$this->settings['blocked_ip_login_page_exclude'] = 0;
		$this->settings['blocked_ip_page_noindex'] = 0;
		$this->settings['blocked_ip_manual_cant_unlock'] = 0;
		$this->settings['auto_block_ip_amount'] = 3;
		$this->settings['blocked_ip_page_title'] = __( 'IP is Blocked', $this->domain );;
		$this->settings['blocked_ip_page_content'] = __( '<h1 style="text-align: center;">Your IP is Blocked by the Administrator</h1>', $this->domain );
		$this->settings['enable_grecaptca'] = 0;
		$this->settings['db_truncate'] = 0;

		$this->model->updateSettings($this->settings);

	}

	/**
	* Initial shortcodes
	*
	* @since 1.0
	*
	* @return void.
	*/
	private function getShortcodes()
	{

		$this->shortcodes = array(
			'[name]'        => $this->letter['username'],
			'[email]'       => $this->letter['email'],
			'[url]'         => $this->letter['url'],
			'[sitename]'    => wp_specialchars_decode(get_option('blogname'), ENT_QUOTES),
			'[siteurl]'     => site_url(),
			'[blockedipurl]'=> site_url('?naipbspam='.$this->letter['ipHash']),
		);

	}

	/**
	* Create letter
	*
	* @since 1.0
	*
	* @return void.
	*/
	private	function getLetter()
	{

		$this->letter['ipHash'] = md5($this->helper->currentUserIp.time());
		$this->letter['ip'] = $this->helper->currentUserIp;

		$this->getShortcodes();

		$this->letter['subject'] = str_replace(array_keys($this->shortcodes), array_values($this->shortcodes), $this->settings['subject']);
		$this->letter['content'] = str_replace(array_keys($this->shortcodes), array_values($this->shortcodes), $this->settings['message']);

		$this->letter['message'] = '
		<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
		<html>
		<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>'.$this->letter['subject'].'</title>
		</head>
		<body width="100%" marginheight="0" topmargin="0" marginwidth="0" leftmargin="0" style="margin:0;padding:0 30px;font-family:Calibri, Tahoma, Arial">
		'.$this->helper->getContent($this->letter['content']).'
		</body>
		</html>
		';
		$this->letter['headers'] = array('Content-Type: text/html; charset=UTF-8'."\r\n");
		$this->letter['attachment'] = NULL;

	}

	/**
	* Sent Letter
	*
	* @since 1.0
	*
	* @return void.
	*/
	private	function sentLetter()
	{

		if(!is_null($this->letter['email']) && is_email($this->letter['email'])){
			$this->send = @wp_mail( $this->letter['email'], $this->letter['subject'], $this->letter['message'], $this->letter['headers'], $this->letter['attachment']);
		}

	}

	/**
	* Add IP to spam list
	*
	* @since 1.0
	*
	* @return vaid
	*/
	private function addSpamIp()
	{

		if(!$this->send) return;

		$getSpamIp = $this->model->getSpamIp(array('hash'=>$this->letter['ipHash']));

		if(!$getSpamIp) $this->model->addSpamIp(array('ip'   =>$this->letter['ip'],'hash' =>$this->letter['ipHash'],'count'=>0));

	}

	/**
	* Initial Block Page
	*
	* @since 1.0
	*
	* @return vaid
	*/
	private function checkBlocked()
	{

		if(is_admin()) return;

		$this->settings = $this->model->getSettings($this->settings);

		if(!$this->settings['enable_block_ip'] && !$this->showPriviewBlockedPage) return;

		$this->checkBlockedIp = $this->model->ckeckBlockedIp(array('ip'=>$this->helper->currentUserIp));

		$this->checkBlockedIp = ($this->showPriviewBlockedPage) ? 'preview' : $this->checkBlockedIp;

		$this->unBlockedIp();

		if(!$this->checkBlockedIp) return;

		if(preg_match('/wp-login.php/i', $_SERVER['REQUEST_URI']) && $this->settings['blocked_ip_login_page_exclude']) return;

		require_once $this->dirnamePath.'/views/tpl-page-blocked.php';
		die;
	}

	/**
	* Add IP to Blocked list
	*
	* @since 1.0
	*
	* @return vaid
	*/
	private function addBlockedIp()
	{

		if(!$this->addBlokerdIpHash) return;

		$getSpamIp = $this->model->getSpamIp(array('hash'=>$this->addBlokerdIpHash));

		if($getSpamIp && $getSpamIp->count == 0){

			$set = array('count'=>1);
			$where = array('hash'=>$getSpamIp->hash);
			$this->model->updateSpamIp($set, $where);

			$count = $this->model->getSpamIpCount(array('ip'=>$getSpamIp->ip));

			if($this->settings['auto_block_ip_amount'] != 0 && $this->settings['auto_block_ip_amount'] <= $count){

				if(!$this->model->ckeckBlockedIp(array('ip'=>$getSpamIp->ip))){

					$this->model->addBlockedIp(array('ip'  =>$getSpamIp->ip,'auto'=>1));
				}

			}

		}

	}

	/**
	* Unlock IP if user submit form
	*
	* @since 1.0
	*
	* @return vaid
	*/
	private function unBlockedIp()
	{

		$security = (isset($_POST['security']) && !empty($_POST['security']) && $_POST['security'] == md5($this->slug.$this->helper->currentUserIp)) ? TRUE : FALSE;
		$gRecaptchaResponse = (isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])) ? $_POST['g-recaptcha-response'] : FALSE;

		if($security && $gRecaptchaResponse){
			$url      = 'https://www.google.com/recaptcha/api/siteverify'.
			'?secret='.$this->settings['grecaptca_secret_key'].
			'&response='.$_POST['g-recaptcha-response'].
			'&remoteip='.$this->helper->currentUserIp;

			$response = $this->helper->getRequest($url);
			$response = json_decode($response, true);

			if($response['success'] && is_object($this->checkBlockedIp)){

				if($this->settings['blocked_ip_manual_cant_unlock'] && $this->checkBlockedIp->auto == 0) return;

				$unlockIp = $this->model->deleteBlockedIp(array('ip'  =>$this->checkBlockedIp->ip,'auto'=>$this->checkBlockedIp->auto));
				if($unlockIp) $this->checkBlockedIp = FALSE;
			}

		}

	}

}