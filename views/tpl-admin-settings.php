<?php
/*notifier-and-ip-blocker*/
if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?><div class="wrap">
	<?php if( $updated ) : ?>
	<div id="message" class="<?php echo($error)?'error':'updated';?> fade">
		<p><?php echo $update_message;?></p>
	</div>
	<?php endif; ?>
	<h1><?php esc_html_e('Notifier and IP Blocker', $this->domain);?></h1>
	<p><?php esc_html_e( 'Can secure your web-site from spam bots and notify users. The many users, who write a comment want to know, whether the administrator had got it or not. Send a message where it is written that the comment had been received and the answer would be sent as soon as possible also if it is spam user can go to link and blocked IP', $this->domain ); ?></p>
	
	<div class="wrap">
        <h2><?php esc_html_e('Customize options', $this->domain);?></h2>
        <h2 class="nav-tab-wrapper">		
        	<a href="#settings" class="nav-tab nav-tab-active"><?php esc_html_e('Settings', $this->domain);?></a>		
			<a href="#dashboard" class="nav-tab"><?php esc_html_e('Blocked IPs', $this->domain);?></a>
		</h2>
        <div class="tabs-content">
			<div id="settings">
				<h3><span><?php esc_html_e('Settings', $this->domain);?></span></h3>
				<div class="inside">
					<form method="post">
						<table class="form-table">
							<tr>
								<th scope="row"><label><?php esc_html_e( 'Notifier', $this->domain ); ?></label></th>
								<td>
									<fieldset>
										<legend class="screen-reader-text"><span><?php esc_html_e( 'Notifier', $this->domain ); ?></span></legend>
										<label for="<?php echo $this->slug.'_enable';?>">
											<input type="checkbox" name="<?php echo $this->slug.'_enable';?>" id="<?php echo $this->slug.'_enable';?>" value="1" <?php checked( $this->settings['enable'] ); ?> /> <?php esc_html_e( 'Enable notify', $this->domain ); ?>
										</label>
										<br>
										<label for="<?php echo $this->slug.'_comment_need_pending';?>">
											<input type="checkbox" name="<?php echo $this->slug.'_comment_need_pending';?>" id="<?php echo $this->slug.'_comment_need_pending';?>" value="1" <?php checked( $this->settings['comment_need_pending'] ); ?> /> <?php esc_html_e( 'Only send notify when a comment is held for moderation', $this->domain ); ?>
										</label>
										<?php if($this->supportCF7):?>
										<br>
										<label for="<?php echo $this->slug.'_enabled_cf7';?>">
											<input type="checkbox" name="<?php echo $this->slug.'_enabled_cf7';?>" id="<?php echo $this->slug.'_enabled_cf7';?>" value="1" <?php checked( $this->settings['enabled_cf7'] ); ?> /> <?php esc_html_e( 'Send notify via Contact Form 7', $this->domain ); ?>
										</label>
										<p class="description">
											<?php esc_html_e( 'Only support default cf7 fields name:', $this->domain );?>
											<?php echo ' <code>[your-email]</code>, <code>[your-name]</code>'; ?>
										</p>
										<?php endif;?>
										<br>
										<br>
										<br>
									</fieldset>
								</td>
							</tr>
							<tr>
								<th scope="row"><label><?php esc_html_e( 'Letter', $this->domain ); ?></label></th>
								<td>
									<fieldset>
										<legend class="screen-reader-text"><span><?php esc_html_e( 'Letter', $this->domain ); ?></span></legend>
										<label for="<?php echo $this->slug.'_subject';?>"><?php esc_html_e( 'Subject', $this->domain ); ?></label>
										<p>
											<input type="text" name="<?php echo $this->slug.'_subject';?>" id="<?php echo $this->slug.'_subject';?>" class="large-text" value="<?php echo esc_attr( $this->settings['subject'] ); ?>" />
										</p>
										<br>
										<p>
											<label for="<?php echo $this->slug.'_message';?>"><?php esc_html_e( 'Message', $this->domain ); ?></label>
											<?php wp_editor( $this->settings['message'], $this->slug.'_message', array('wpautop'=>true));?>
										</p>										
										<?php if(!empty($this->shortcodes)):?>
										<br>
										<p>
											<label><?php esc_html_e( 'Available shortcodes:', $this->domain ); ?></label>
											<?php echo '<code>'.implode('</code>, <code>', $this->shortcodes).'</code>';?>
											<p class="description">
												<?php esc_html_e( 'Shortcodes work in the fields subject and message', $this->domain ); ?>	
											</p>
										</p>
										<?php endif;?>
										<br>
										<br>
										<br>
									</fieldset>							
								</td>
							</tr>
							<tr>
								<th scope="row"><label><?php esc_html_e( 'Block IP', $this->domain ); ?></label></th>
								<td>
									<fieldset>
										<legend class="screen-reader-text"><span><?php esc_html_e( 'Notifier', $this->domain ); ?></span></legend>
										<label for="<?php echo $this->slug.'_enable_block_ip';?>">
											<input type="checkbox" name="<?php echo $this->slug.'_enable_block_ip';?>" id="<?php echo $this->slug.'_enable_block_ip';?>" value="1" <?php checked( $this->settings['enable_block_ip'] ); ?> /> <?php esc_html_e( 'Enable Block IP', $this->domain ); ?>
										</label>
										<br>
										<label for="<?php echo $this->slug.'_blocked_ip_login_page_exclude';?>">
											<input type="checkbox" name="<?php echo $this->slug.'_blocked_ip_login_page_exclude';?>" id="<?php echo $this->slug.'_blocked_ip_login_page_exclude';?>" value="1" <?php checked( $this->settings['blocked_ip_login_page_exclude'] ); ?> /> <?php esc_html_e( 'Exclude with block wp-login page', $this->domain ); ?>
										</label>
										<br>
										<label for="<?php echo $this->slug.'_blocked_ip_page_noindex';?>">
											<input type="checkbox" name="<?php echo $this->slug.'_blocked_ip_page_noindex';?>" id="<?php echo $this->slug.'_blocked_ip_page_noindex';?>" value="1" <?php checked( $this->settings['blocked_ip_page_noindex'] ); ?> /> <?php esc_html_e( 'Blocked page noindex, nofollow', $this->domain ); ?>
										</label>
										<br>
										<label for="<?php echo $this->slug.'_blocked_ip_manual_cant_unlock';?>">
											<input type="checkbox" name="<?php echo $this->slug.'_blocked_ip_manual_cant_unlock';?>" id="<?php echo $this->slug.'_blocked_ip_manual_cant_unlock';?>" value="1" <?php checked( $this->settings['blocked_ip_manual_cant_unlock'] ); ?> /> <?php esc_html_e( 'Manually blocked IPs, user can\'t unblock', $this->domain ); ?>
										</label>
										<br>										
										<label for="<?php echo $this->slug.'_auto_block_ip_amount';?>">
											<?php esc_html_e( 'Auto block IP', $this->domain ); ?> <input type="number" name="<?php echo $this->slug.'_auto_block_ip_amount';?>" id="<?php echo $this->slug.'_auto_block_ip_amount';?>" value="<?php echo $this->settings['auto_block_ip_amount']; ?>" min="0" step="1" class="small-text" /> <?php esc_html_e( 'spam amount', $this->domain ); ?>										
										</label>
										<p class="description"><?php esc_html_e( 'Automatically block IP after spam amount. Zero - disable automatically block IP.', $this->domain ); ?></p>
										<br>
										<br>
										<br>																				
									</fieldset>
								</td>
							</tr>							
							<tr>
								<th scope="row"><label><?php esc_html_e( 'Block page', $this->domain ); ?></label></th>
								<td>
									<fieldset>
										<legend class="screen-reader-text"><span><?php esc_html_e( 'Block page', $this->domain ); ?></span></legend>
										<label><a href="<?php echo site_url('?naipb=blockedpagepreview');?>" target="_blank"><?php esc_html_e( 'Preview Blocked page', $this->domain ); ?></a></label>
										<br>
										<br>
										<br>
										<label for="<?php echo $this->slug.'_blocked_ip_page_title';?>"><?php esc_html_e( 'Title', $this->domain ); ?></label>
										<p>
											<input type="text" name="<?php echo $this->slug.'_blocked_ip_page_title';?>" id="<?php echo $this->slug.'_blocked_ip_page_title';?>" class="large-text" value="<?php echo esc_attr( $this->settings['blocked_ip_page_title'] ); ?>" />
										</p>
										<br>
										<p>
										<label for="<?php echo $this->slug.'_blocked_ip_page_content';?>"><?php esc_html_e( 'Content', $this->domain ); ?></label>
										<?php wp_editor( $this->settings['blocked_ip_page_content'], $this->slug.'_blocked_ip_page_content', array('wpautop'=>true));?>
										</p>
										<p class="description">
											<?php esc_html_e( 'Show content in the block page.', $this->domain ); ?>
										</p>
										<br>
										<br>
										<br>
										<label for="<?php echo $this->slug.'_enable_grecaptca';?>">
											<p><?php esc_html_e( 'Anyone can unlock your IP automatically, confirming that he is not a robot.', $this->domain ); ?></p>
											<br>
											<input type="checkbox" name="<?php echo $this->slug.'_enable_grecaptca';?>" id="<?php echo $this->slug.'_enable_grecaptca';?>" value="1" <?php checked( $this->settings['enable_grecaptca'] ); ?> /> <?php esc_html_e( 'Enable Google reCAPTCHA', $this->domain ); ?>
										</label>										
										<br>
										<label for="<?php echo $this->slug.'_grecaptca_site_key';?>">
											<input type="text" name="<?php echo $this->slug.'_grecaptca_site_key';?>" id="<?php echo $this->slug.'_grecaptca_site_key';?>" value="<?php echo $this->settings['grecaptca_site_key']; ?>" class="regular-text" /> <?php esc_html_e( 'Site Key (Public)', $this->domain ); ?>
												
										</label>
										<br>
										<label for="<?php echo $this->slug.'_grecaptca_secret_key';?>">
											<input type="text" name="<?php echo $this->slug.'_grecaptca_secret_key';?>" id="<?php echo $this->slug.'_grecaptca_secret_key';?>" value="<?php echo $this->settings['grecaptca_secret_key']; ?>" class="regular-text" /> <?php esc_html_e( 'Secret Key (Private)', $this->domain ); ?>
										</label>
										<p class="description"><?php esc_html_e( 'Get the public key and private key', $this->domain ); ?> <a href="https://www.google.com/recaptcha/admin" title="google recaptcha" target="_blank"><?php esc_html_e( 'Manage reCAPTCHA API keys', $this->domain ); ?></a></p>
										<br>
										<br>
										<br>										
									</fieldset>
								</td>
							</tr>               
							<tr>
								<th scope="row"><label><?php esc_attr_e( 'Save settitngs', $this->domain ); ?></label></th>
								<td>
									<fieldset>
										<legend class="screen-reader-text"><span><?php esc_attr_e( 'Save settitngs', $this->domain ); ?></span></legend>
										<?php wp_nonce_field( $this->slug ); ?>
										<input type="hidden" name="method" value="update_settings"/>
										<label for="<?php echo $this->slug.'_enable_block_ip';?>">
											<input type="submit" class="button submit button-primary" name="<?php echo $this->slug;?>" value="<?php esc_attr_e( 'Save', $this->domain ); ?>" />
										</label>
										<br>
										<br>
										<br>
										<label for="<?php echo $this->slug.'_db_truncate';?>">
											<input type="checkbox" name="<?php echo $this->slug.'_db_truncate';?>" id="<?php echo $this->slug.'_db_truncate';?>" value="1" <?php checked( $this->settings['db_truncate'] ); ?> /> <?php esc_html_e( 'Truncate plugin database when deactivate', $this->domain ); ?>
										</label>
									</fieldset>
								</td>
							</tr>							
						</table>
					</form>
					<hr>			
					<form method="post">
						<table class="form-table">
							<th scope="row"><label><?php esc_html_e( 'Restore default', $this->domain ); ?></label></th>
							<td>
								<fieldset>
									<legend class="screen-reader-text"><span><?php esc_html_e( 'Restore default', $this->domain ); ?></span></legend>
									<div id="default_settings">
										<label>
											<button class="button" type="button" onclick="document.getElementById('default_settings').hidden = true;document.getElementById('default_settings_confirm').hidden = false; return false;"><?php esc_html_e( 'Default settings', $this->domain ); ?></button>
										</label>
										<p class="description"><?php esc_html_e( 'Restore all settings to default', $this->domain ); ?></p>
									</div>
									<div id="default_settings_confirm" hidden="hidden">
										<label>
											<?php wp_nonce_field( $this->slug ); ?>		
											<input type="hidden" name="method" value="restore_default_settings"/>					
											<button class="button button-primary" type="submit" name="<?php echo $this->slug;?>" value="yes"><?php esc_html_e( 'Yes', $this->domain ); ?></button>
											<button class="button" type="button" onclick="document.getElementById('default_settings').hidden = false;document.getElementById('default_settings_confirm').hidden = true; return false;"><?php esc_html_e( 'No', $this->domain ); ?></button>										
										</label>
										<p class="description"><?php esc_html_e( 'Are you sure?', $this->domain ); ?></p>
									</div>
								</fieldset>
							</td>
						</table>
					</form>
				</div>
			</div>	
			
			<div id="dashboard">
				<h3><span><?php esc_html_e('Blocked IPs', $this->domain);?></span></h3>
				<div class="inside">					
					<p>
						<?php esc_html_e('Your current IP Address:', $this->domain);?>
						<strong><?php echo $this->helper->currentUserIp;?></strong>
						<?php esc_html_e('Don\'t block your own IP.', $this->domain);?>
						<strong><?php esc_html_e('If you block your own IP Address you will be unable to view your site.', $this->domain);?></strong>
					</p>
					<form method="post">
						<p class="search-box">						
							<label><?php esc_html_e('Search IP:', $this->domain);?></label>
							<input type="search" maxlength="15" name="ip" value="<?php if(isset($search)) echo $search;?>">
							<?php wp_nonce_field( $this->slug ); ?>
							<input type="hidden" name="method" value="search_ip"/>
							<input type="submit" name="<?php echo $this->slug;?>" class="button" value="<?php esc_html_e('Search', $this->domain);?>">						
						</p>
					</form>	
					<br class="clear">		
					<table class="wp-list-table widefat striped">
						<thead>
							<tr>
								<th style="width: 15%;"><?php esc_html_e('No.', $this->domain);?></th>
								<th style="text-align: center;"><?php esc_html_e('IP Address', $this->domain);?></th>
								<th style="text-align: center;"><?php esc_html_e('Status', $this->domain);?></th>
								<th style="text-align: center;"><?php esc_html_e('Delete', $this->domain);?></th>
							</tr>
						</thead>						
						<tbody>
							<?php
							if(!empty($this->blockedIpLists)): $i = 1;
								foreach($this->blockedIpLists as $row):?>
								<tr>
									<td><?php echo $i++; ?></td>
									<td style="text-align: center;"><?php echo $row->ip; ?></td>
									<td style="text-align: center;"><?php echo $row->auto==1?'auto':'manual'; ?></td>
									<td style="text-align: center;">
										<a href="#" class="actionip"
											data-method="deleteip"
											data-confirm="<?php esc_html_e('Are you sure deleting IP', $this->domain);?>"
											data-ip="<?php echo $row->ip; ?>"
											data-auto="<?php echo $row->auto;?>"											
										><?php esc_html_e('Delete', $this->domain);?></a>
									</td>
								</tr>
								<?php endforeach;?>
							<?php else:?>
								<tr>									
									<td colspan="4" style="text-align: center;"><?php esc_html_e('Not found blocked IPs', $this->domain);?></td>
								</tr>							
							<?php endif;?>							
						</tbody>
						<tfoot>
							<tr>
								<th style="width: 15%;"><?php esc_html_e('No.', $this->domain);?></th>
								<th style="text-align: center;"><?php esc_html_e('IP Address', $this->domain);?></th>
								<th style="text-align: center;"><?php esc_html_e('Status', $this->domain);?></th>
								<th style="text-align: center;"><?php esc_html_e('Delete', $this->domain);?></th>
							</tr>
						</tfoot>
					</table>	
					<div class="tablenav bottom">
						<div class="alignleft">
						<form method="post">
							<label><?php esc_html_e('Block new IP:', $this->domain);?></label>
							<input type="text" pattern="((^|\.)((25[0-5])|(2[0-4]\d)|(1\d\d)|([1-9]?\d))){4}$" maxlength="15" name="ip" value="" />
							<?php wp_nonce_field( $this->slug ); ?>
							<input type="hidden" name="method" value="add_ip"/>
							<input type="submit" name="<?php echo $this->slug;?>" class="button-secondary" value="<?php esc_html_e('Block', $this->domain);?>" />
						</form>
						</div>
						<div class="alignleft actions"></div>					
					</div>				
				</div>
			</div>			
		</div>
	</div>
</div>