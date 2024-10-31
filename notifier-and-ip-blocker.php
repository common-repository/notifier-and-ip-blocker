<?php
/*
Plugin Name: Notifier and IP Blocker
Plugin URI: http://ml.lviv.ua/projects/plugins/notifier-and-ip-blocker/
Description: Notify a user about when he sent comment or form via Contact Form 7 and automatically blocked spammer IP by notifier users.
Tags: alerts, banned, blocked IP, comment, customize, email, from, html, letter, mail, notice, notifications, notify, Pingback, plain, security, shortcode, spam, spam blocker, tag, trackback, user, users, wp_mail.
Version: 1.0
Requires at least: 3.0
Author: Mike Luskavets
Author URI: http://ml.lviv.ua
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html
Text Domain: notifier-and-ip-blocker
Domain Path: /languages/
*/
if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

define('NAIPB_PATH', plugin_dir_path(__FILE__));
define('NAIPB_URL', plugin_dir_url(__FILE__));
define('NAIPB_DOMAIN', plugin_basename(NAIPB_PATH));
define('NAIPB_SLUG', str_replace('-', '_', NAIPB_DOMAIN));
define('NAIPB_BASENAME', plugin_basename(__FILE__));


register_activation_hook( __FILE__ , 'naipb_activate');
register_deactivation_hook( __FILE__ , 'naipb_deactivate');
register_uninstall_hook( __FILE__ , 'naipb_uninstall');

function naipb_activate(){
	require_once NAIPB_PATH.'/inc/class-naipb-loader.php';
	
	$loader = new NaipbLoader(NAIPB_SLUG);
	$loader->activate();
}
function naipb_deactivate(){
	require_once NAIPB_PATH.'/inc/class-naipb-loader.php';
	
	$loader = new NaipbLoader(NAIPB_SLUG);
	$loader->deactivate();
}
function naipb_uninstall(){
	require_once NAIPB_PATH.'/inc/class-naipb-loader.php';
	
	$loader = new NaipbLoader(NAIPB_SLUG);
	$loader->uninstall();
}

require_once NAIPB_PATH.'/core/naipb-controller.php';

new NaipbController(NAIPB_PATH, NAIPB_URL, NAIPB_DOMAIN, NAIPB_SLUG, NAIPB_BASENAME);