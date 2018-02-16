	<?php
	if ( ! defined( 'ABSPATH' ) ) exit;
	if ( ! is_admin() ) die;
	global $tdata;
	?>
	<?php if(is_rtl()){
		echo '<style type="text/css"> 
		#floating_save_button{left: 25px !important; right: auto !important;}
		.tab-links li {float:right !important;}
		</style>
		';
	}

	?>
	<div id="twp-wrap" class="wrap">
		<h1><?php  echo __("Telegram for WordPress", "twp-plugin") ?></h1>
		<p> <?php printf(__("Join our channel in Telegram: %s", "twp-plugin"), "<a href='https://telegram.me/notifcaster' >@notifcaster</a>"); ?> </p>
		<?php if( isset($_GET['settings-updated']) ) { ?>
		<div id="message" class="updated">
			<p><strong><?php _e('Settings saved.') ?></strong></p>
		</div>
		<?php } ?>
		<form method="post" action="options.php" id="twp_form">
			<div id="floating_save_button" title="<?php _e('Save Changes') ?>">&#x1f4be;</div>
			<?php settings_fields( 'twp-settings-group' ); ?>
			
			<div class="tabs">
				<ul class="tab">
					<li class="tab-item"><a href="#twp_tab1">&#128276;<span class="twp_label"><?php echo __("Notifications and Bot", "twp-plugin") ?></span></a></li>
					<li class="tab-item"><a href="#twp_tab2">&#x1f4e3;<span class="twp_label"><?php echo __("Post to Channel and Group", "twp-plugin") ?></span></a></li>
					<li class="tab-item"><a href="#twp_tab3">&#x1f4b0;<span class="twp_label"><?php echo __("Donate", "twp-plugin") ?></span></a></li>
				</ul>
				<div class="twp-tab-content">
					<div id="twp_tab1" class="container">
						<div class="columns">
							<div class="column col-6 col-xs-12">
								<div class="panel">
									<div class="panel-header text-center">
										<div class="panel-title h5 mt-10">Bot Info</div>
										<div class="panel-subtitle">This bot will be used for all actions.</div>
									</div>
									<div class="panel-body">
										<ol>
											<li><?php printf(__("Simply create a bot (if you don't already have one) by sending %s command to %s in Telegram app.", "twp-plugin"), "<code>/newbot</code>", "<a href='https://t.me/botfather'>@Botfather</a>") ?>
											</li>
											<li><?php echo __("Copy/Paste your bot token at the below field.", "twp-plugin") ?></li>
											<li><?php echo __("Click on authorize button to make sure everything is correct.", "twp-plugin") ?></li>
											<li><?php printf(__("Send %s command to your bot. It's very important to start chat with your bot", "twp-plugin"),"<code>/start</code>") ?></li>
										</ol>
										<div class="chip">
											<figure class="avatar avatar-sm" data-initial="" style="background-color: #5755d9;">
											</figure>
											<span id="bot_info_chip"><?=$tdata['twp_bot_name']->option_value."(".$tdata['twp_bot_username']->option_value.")"?></span>
										</div>
										<input type="hidden" name="twp_bot_name" value="<?=$tdata['twp_bot_name']->option_value?>" />
										<input type="hidden" name="twp_bot_username" value="<?=$tdata['twp_bot_username']->option_value?>" />
									</div>
									<div class="panel-footer">
										<div class="input-group">
											<span class="input-group-addon">Bot Token</span>
											<input class="form-input" id="twp_bot_token" type="text" name="twp_bot_token" value="<?php echo $tdata['twp_bot_token']->option_value; ?>" dir="auto" />
											<button id="checkbot" class="btn" type="button"><?php echo __("Authorize", "twp-plugin") ?></button>
										</div>
									</div>
								</div>
							</div>
							<div class="column col-6 col-xs-12">
								<div class="panel">
									<div class="panel-header text-center">
										<div class="panel-title h5 mt-10"><?php echo __("Send notifications to:", "twp-plugin") ?></div>
										<!-- <div class="panel-subtitle">User or Group?</div> -->
									</div>
									<nav class="tabs panel-nav">
										<ul class="tab tab-block">
											<li class="tab-item">
												<a href="#send_to_user_help">
													User
												</a>
											</li>
											<li class="tab-item">
												<a href="#send_to_group_help">
													Group
												</a>
											</li>
										</ul>
									</nav>
									<div class="panel-body">
										<div id="send_to_user_help">
											<?php echo __("If you want to send notifications only to your account:", "twp-plugin") ?>
											<ol>
												<li><?php printf(__("Send %s command to this bot : %s", "twp-plugin"),"<code>/start</code>","<a href='https://t.me/userinfobot'>@UserInfoBot</a>") ?>
												</li>
												<li><?php printf(__("The bot will give your %s to you. Copy/Paste your %s in the below field.", "twp-plugin"), "<code>chat_id</code>", "<code>chat_id</code>") ?></li>
												<li><?php echo __("Send a test message.", "twp-plugin") ?></li>
											</ol>
										</div>
										<div id="send_to_group_help">
											<?php echo __("If you want to send notifications to a group (includes you and other site admins):", "twp-plugin") ?>
											<ol>
												<li><?php echo __("Add your bot to your group.", "twp-plugin") ?>
												</li>
												<li><?php printf(__("Add %s to your group.", "twp-plugin"),"<a href='https://t.me/groupinfobot'>@GroupInfoBot</a>") ?>
												</li>
												<li><?php printf(__("The bot will give you the %s of the group. Copy/Paste group %s in the below field.", "twp-plugin"), "<code>chat_id</code>", "<code>chat_id</code>") ?></li>
													<dd class="howto">
														<?php echo __("Note: @GroupInfoBot will leave your group after showing the chat_id.", "twp-plugin") ?>
													</dd>
												<li><?php echo __("Send a test message.", "twp-plugin") ?></li>
											</ol>
										</div>
										
									</div>
									<div class="panel-footer">
										<div class="input-group">
											<span class="input-group-addon">Chat ID</span>
											<input class="form-input" id="twp_chat_id" type="text" name="twp_chat_id" value="<?=$tdata['twp_chat_id']->option_value?>" dir="auto" />
											<button id="send_test_msg" class="btn" type="button"><?php  echo __("Send now!", "twp-plugin") ?></button>
										</div>
										<div class="input-group mt-2">
											<span class="input-group-addon"><?=__("Hashtag (optional)", "twp-plugin")?></span>
											<input id="twp_hashtag" class="form-input" type="text" name="twp_hashtag" size="32" value="<?=$tdata['twp_hashtag']->option_value?>" dir="auto" />
										</div>
									</div>
								</div>
							</div>
						</div> <!-- .columns -->
					</div>
					<div id="twp_tab2">
						<table class="form-table">
							<tr>
								<th scope="row"><h3><?php  echo __("Introduction", "twp-plugin") ?></h3></th>
								<td>
									<p style="font-weight:700;font-size: 16px;">
										<?php echo __("Telegram channel is a great way for attracting people to your site.", "twp-plugin");?>
										<br> 
										<?php echo __("This option allows you to send posts to your Telegram channel. Intresting, no?", "twp-plugin"); ?>
										<br>
										<?php echo __("So let's start!", "twp-plugin"); ?> 
									</p>
									<ol>
										<li><?php echo __("Create a channel (if you don't already have one).", "twp-plugin") ?></li>
										<li><?php echo __("Create a bot (if you don't already have one).", "twp-plugin") ?></li>
										<li><?php echo __("Go to channel options and select 'Administrator' option.", "twp-plugin") ?></li>
										<li><?php echo __("Select 'Add Administrator' option.", "twp-plugin") ?></li>
										<li><?php echo __("Search the username of your bot and add it as administrator.", "twp-plugin") ?></li>
										<li><?php echo __("Copy the bot token (you got it in step two) and paste it in the below field.", "twp-plugin") ?></li>
										<li><?php echo __("Enter the username of the channel and hit SAVE button!!!", "twp-plugin") ?></li>
										<li><?php echo __("Yes! Now, whenever you publish or update a post you can choose whether send it to Telegram (from post editor page)", "twp-plugin") ?></li>
									</ol>
								</td>
							</tr>
							<tr>
								<th scope="row"><h3>Bot Token</h3></th>
								<td>
									
									<button id="checkbot" class="button-secondary" type="button"><?php echo __("Check bot token", "twp-plugin") ?></button>
									<p class="howto">
										<?php echo __("Bot Info: ", "twp-plugin") ?>
										<span id="bot_name"></span>
									</p>
								</td>
							</tr>
							<tr>
								<th scope="row"><h3><?php echo __("Channel Username", "twp-plugin") ?></h3></th>
								<td>
									<input id="twp_channel_username" type="text" name="twp_channel_username" size="32" value="<?php echo $tdata['twp_channel_username']->option_value; ?>" onblur="getMembersCount()" dir="auto" />
									<button id="channelbtn" type="button" class="button-secondary"> <?php  echo __("Send now!", "twp-plugin") ?></button>
									<p><span id="get_members_count" style="margin: auto 4px;" title='<?php echo __("Click here to refresh members count", "twp-plugin") ?>' ">&#128260;</span><?php  echo __("Members Count:", "twp-plugin") ?> <span style="color:royalblue; font-weight: bolder" id="members_count"></span></p>
									<p class="howto"><?php echo __("Don't forget <code>@</code> at the beginning", "twp-plugin") ?></p>
								</td>
							</tr>
							<tr>
								<?php
								$preview = $tdata['twp_web_preview']->option_value;
								$excerpt_length = $tdata['twp_excerpt_length']->option_value;
								$tstc = $tdata['twp_send_to_channel']->option_value;
								$tstc = $tstc != "" ? $tstc : 0 ;
								$cp = $tdata['twp_channel_pattern']->option_value;
								$s = $tdata['twp_send_thumb']->option_value;
								$m = $tdata['twp_markdown']->option_value;
								$ipos = $tdata['twp_img_position']->option_value;
								?>
								<th scope="row"><h3><?php echo __("Send to Telegram Status (Global)", "twp-plugin") ?></h3></th>
								<td>
									<p class="howto"><?php echo __("By checking one of the below options, you don't need to set \"Send to Telegram Status\" every time you create a new post.", "twp-plugin") ?></p><br>
									<div class="twp-radio-group">
										<input type="radio" id="twp_send_to_channel_yes" name="twp_send_to_channel" value="1" <?php checked( '1', $tstc ); ?>/><label for="twp_send_to_channel"><?php echo __('Always send', 'twp-plugin' ) ?> </label><br>
										<input type="radio" id="twp_send_to_channel_no" name="twp_send_to_channel" value="0" <?php checked( '0', $tstc ); ?>/><label for="twp_send_to_channel"><?php echo __('Never Send', 'twp-plugin' ) ?> </label>
									</div>
									<p class="howto"><?php echo __("You can override the above settings in post-edit screen", "twp-plugin") ?></p>
									<br>
								</td>
							</tr>

							<?php require_once(TWP_PLUGIN_DIR."/inc/composer.php"); ?>

							<tr>
								<th scope="row"><h3><?php echo __("Formatting messages", "twp-plugin") ?></h3></th>
								<td>
									<p class="howto"><?php echo __("Telegram supports basic markdown and some HTML tags (bold, italic, inline links). By checking one of the following options, your messages will be compatible with Telegram format.", "twp-plugin") ?></p><br>
									<fieldset>
										<input type="radio" name="twp_markdown" id="twp-markdown-0" <?php echo ($m==0)?'checked=checked':'' ?> value="0">
										<label for="twp-markdown-0"><?php echo __("Disable Markdown and remove HTML tags", "twp-plugin"); ?></label>
										<br>
										<input type="radio" name="twp_markdown" id="twp-markdown-1" data-markdown="markdown" <?php echo ($m==1)?'checked=checked':'' ?> value="1">
										<label for="twp-markdown-1"><?php echo __("Use basic Markdown (less characters)", "twp-plugin"); ?></label>
										<br>
										<input type="radio" name="twp_markdown" id="twp-markdown-2" data-markdown="html" <?php echo ($m==2)?'checked=checked':'' ?> value="2">
										<label for="twp-markdown-2"><?php echo __("Use HTML tags (more speed)", "twp-plugin"); ?></label>
										<br>
										<a href="https://core.telegram.org/bots/api#formatting-options" target="_blank" title='<?php echo __("Learn more", "twp-plugin") ?>'><?php echo __("Learn more", "twp-plugin") ?></a>
										<br>
									</fieldset>
								</td>
							</tr>
							<tr>
								<th scope="row"><h3><?php echo __("Excerpt length", "twp-plugin") ?></h3></th>
								<td>
									<p class="howto"><?php echo __("By default, the plugin replaces {excerpt} tag with 55 first words of your post content. You can change the number of words here:", "twp-plugin") ?></p><br>
									<input type="text" onkeypress="return event.charCode >= 48 && event.charCode <= 57" id="twp_excerpt_length" name="twp_excerpt_length" value="<?php echo $excerpt_length ?>"/>
									<label for="twp_excerpt_length"><?php echo __('words in excerpt', 'twp-plugin' ) ?> </label>
								</td>
							</tr>
							<tr>
								<th scope="row"><h3><?php echo __("Web page preview in messages", "twp-plugin") ?></h3></th>
								<td>
									<p class="howto"><?php echo __("Disables link previews for links in the sent message.", "twp-plugin") ?></p><br>
									<input type="checkbox" id="twp_web_preview" name="twp_web_preview" value="1" <?php checked( '1', $preview ); ?>/>
									<label for="twp_web_preview"><?php echo __('Disable Web page preview', 'twp-plugin' ) ?> </label>
								</td>
							</tr>
							<tr>
								<th scope="row"><h3><?php echo __("Photo position", "twp-plugin") ?></h3></th>
								<td>
									<p class="howto"><?php echo __("Telegram limits the photo caption to 200 characters. Here are two options if your message text exceeds this limit:", "twp-plugin") ?></p><br>
									<fieldset>
										<input type="radio" name="twp_img_position" id="twp-img-0" <?php echo ($ipos==0)?'checked=checked':'' ?> value="0">
										<label for="twp-img-0"><strong><?php echo __("Send photo before text", "twp-plugin"); ?></strong></label>
										<br>
										<p class="howto"><?php echo __("This will send the photo with the pattern content as the caption. If pattern content exceeds 200 characters limit, then this will send the photo with the caption specified in WordPress media library and the pattern content afterward (Each one in a separate message). <br> You can set a caption for the photo in the media library.", "twp-plugin") ?></p>
										<br>
										<input type="radio" name="twp_img_position" id="twp-img-1" <?php echo ($ipos==1)?'checked=checked':''; echo ($m==0)?'disabled=disabled':'' ?> value="1">
										<label for="twp-img-1"><strong><?php echo __("Send photo after text", "twp-plugin"); ?></strong></label>
										<br>
										<span id="twp-img-1-error" class="notice notice-error <?php if($m!=0){echo 'hidden';}?>"><?php echo __("Can't use this option while formatting is disabled", "twp-plugin")?></span>
										<p class="howto"><?php echo __("This will attach an invisible link of your photo to the beginning of your message. People wouldn't see the link, but Telegram clients will show the photo at the bottom of the message (All in one message). This will ignore any caption that has been set using media library.", "twp-plugin") ?></p>
										<br>
									</fieldset>
								</td>
							</tr>
						</table>
					</div>
					<div id="twp_tab3">
						<h2><?php echo __("My story...", "twp-plugin") ?></h2>
						<p style="font-size: 16px; width: 85%; padding: 0 20px 20px 20px; line-height: 2em;">
							Hi my friends!☺️<br> 
							I'm <a href="https://twitter.com/ameer_mousavi">Ameer Mousavi</a> (plugin author) from Iran, a country in the Middle East. I'm a Zoology junior student and part-time computer assistant at my former high school.<br>
							I put lots of energy and time (and also money) on the development of this plugin. If you enjoy this plugin and want to make this plugin better, please consider making a small donation using one of the below options:
						</p>
						<table class="form-table">
							<tr>
								<td style="text-align: center;">
									<h3><?php echo __("From anywhere", "twp-plugin") ?> &#127758;</h3>
									<p>
										<?php echo __("From anywhere on this planet, you can make me happy &#128525; <br> Just click on the below button:", "twp-plugin") ?><br>
									</p>
									<p>
										<a href="https://flattr.com/profile/Ameer_Mousavi" target="_blank" style="border-bottom: 0 !important; margin: 5px;"><img src="//button.flattr.com/flattr-badge-large.png" alt="Flattr this" title="Flattr this" border="0" style="vertical-align: middle; ">
										</a>
									</p>
								</td>
								<td style="text-align: center;">
									<h3><?php echo __("From Iran", "twp-plugin") ?> &#x1f1ee;&#x1f1f7;</h3>
									<p>
										اکر شما در ایران هستید، لطفا کمکهای نقدی خودتان را به شماره ملی کارت زیر واریز نمایید:<br>
									</p>
									<p><strong>6037-9971-9935-3583</strong></p>
									<p><em>به نام سید امیر محمد موسوی پور</em></p>
								</td>
							</tr>
						</table>
					</div>
				</div>
			</div> <!-- .tabs -->
			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			</p>
		</form>
		<div id="support">
			<?php  
			$message = sprintf (__('If you like this plugin, please rate it in %1$s. You can also support us by %2$s', "twp-plugin"), '<a href="https://wordpress.org/plugins/telegram-for-wp" target="_blank">'.__('Telegram for Wordpress page in wordpress.org', "twp-plugin").'</a>', '<a href="https://notifcaster.com/donate" target="_blank" title="donate">'.__("donating", "twp-plugin").'</a>' );
			echo $message; ?>
		</div>
	</div>
	<script type="text/javascript">
		
	</script>