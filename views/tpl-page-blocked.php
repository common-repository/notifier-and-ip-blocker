<?php 
/*notifier-and-ip-blocker*/
if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
ob_start();
?><!DOCTYPE>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="MobileOptimized" content="width" />
		<meta name="HandheldFriendly" content="True"/>
		<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0" />
		<?php if($this->settings['blocked_ip_page_noindex']):?><meta name="robots" content="noindex,nofollow" /><?php endif;?>
		<title><?php echo $this->settings['blocked_ip_page_title'];?></title>		
	</head>
	<body>
		<header></header>
		<section>
			<article>
				<?php echo $this->helper->getContent($this->settings['blocked_ip_page_content']);?>
			</article>
			<?php if($this->settings['enable_grecaptca'] && ($this->checkBlockedIp=='preview' || ($this->checkBlockedIp->auto == 1 || !$this->settings['blocked_ip_manual_cant_unlock'] && $this->checkBlockedIp->auto == 0))):?>
				<script src="https://www.google.com/recaptcha/api.js" async defer></script>
				<script type="text/javascript">
					var unlock = function(){document.getElementById('unlock').submit()};
				</script>			
				<form id="unlock" method="POST">
					<center>
						<p><?php esc_html_e( 'You can unlock your IP Address!', $this->domain ); ?></p>
						<div class="g-recaptcha" data-sitekey="<?php echo $this->settings['grecaptca_site_key'];?>" data-callback="unlock"></div>
					</center>
					<input type="hidden" name="security" value="<?php echo md5($this->slug.$this->helper->currentUserIp);?>" />
			    </form>
			<?php endif;?>
		</section>
	</body>
</html>
<?php echo $this->helper->htmlCompress(ob_get_clean());