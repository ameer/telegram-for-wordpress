<?php
if ( ! defined( 'ABSPATH' ) ) exit;
if ( ! is_admin() ) die;
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
    #If WooCommerce is active then show WC tags for message pattern
	$is_wc_active = true;
} else {
	$is_wc_active = false;
}
?>
<style type="text/css">
	textarea#twp_channel_pattern {resize: vertical; width: 48%; height: auto;min-height: 128px;}
	div#output {box-sizing: border-box; width: 48%; display: inline-block; vertical-align: top; white-space: pre; border: 1px solid #ddd; height: 128px; background: #F1F1F1; cursor: not-allowed;padding: 2px 6px; overflow-y: auto }
	#output a {border-bottom: 0 !important;}
	.emojione{font-size:inherit;height:3ex;width:3.1ex;min-height:20px;min-width:20px;display:inline-block;margin:-.2ex .15em .2ex;line-height:normal;vertical-align:middle}
	img.emojione{width:auto}
	.toolbar {margin-top: 5px;}
	.toolbar select {margin: 5px 0 !important;}
	#twp_metabox td, #twp_metabox th {padding-top: 0 !important;}
	#twp_box_container * {margin-top: 0 !important;}
	.twp-file-container {margin-top: 10px;}
	#twp-file-details, #twp-file-icon {display: inline-block;vertical-align: top;}
	#twp-file-details {margin: 0 12px;}
	#twp-file-details span {color:royalblue;}
	span#cheatsheet-link {padding: 5px; }
	@media screen and (max-width: 782px) {
		#profile-page .form-table textarea, .form-table span.description, .form-table td input[type=email], .form-table td input[type=password], .form-table td input[type=text], .form-table td select, .form-table td textarea {
			width: 48%;
			display: inline-block;
		}
		.button-secondary {
			height: 47px !important;
		}
		span#cheatsheet {
			margin-bottom: 5px !important;
			display: block !important;
		}
	@media screen and (max-width: 1215px){
		textarea#twp_channel_pattern {width: 98%;}
		div#output {width: 98%;}
	}
	@media screen and (max-width: 782px) {
		div#output {margin: 5px auto;}
		.form-table td {padding: 0 13px;}
	}
</style>
<tr>
	<th scope="row">
		<h3><?php echo __("Message Pattern", "twp-plugin"); ?></h3>
	</th>
	<td>
		<p class="howto">
			<?php 
			echo __("Define the structure of messages that are sent to the channel. Use tags, emoji shortnames and any other text.", "twp-plugin");
			?>
		</p>
		<div class="toolbar">
			<select id="patterns-select">
			<option selected="true" style="display:none;"><?php echo __("Select a tag...", "twp-plugin"); ?></option>
				<optgroup label="WordPress Tags">
					<option title='<?php echo __("The ID of this post", "twp-plugin"); ?>'>{ID}</option>
					<option title='<?php echo __("The title of this post", "twp-plugin"); ?>'>{title}</option>
					<option title='<?php echo __("The first 55 words of this post", "twp-plugin"); ?>'>{excerpt}</option>
					<option title='<?php echo __("The whole content of this post", "twp-plugin"); ?>'>{content}</option>
					<option title='<?php echo __("The display name of author of this post", "twp-plugin"); ?>'>{author}</option>
					<option title='<?php echo __("The short url of this post", "twp-plugin"); ?>'>{short_url}</option>
					<option title='<?php echo __("The permalink of this post", "twp-plugin"); ?>'>{full_url}</option>
					<option title='<?php echo __("The tags of this post. Tags are automatically converted to Telegram hashtags", "twp-plugin"); ?>'>{tags}</option>
					<option title='<?php echo __("The categories of this post. Categories are automatically separated by | symbol", "twp-plugin"); ?>'>{categories}</option>
				</optgroup>
				<?php if ($is_wc_active) {
					?>
					<optgroup label="WooCommerc Tags">
						<option title='<?php echo __("The width of this product", "twp-plugin"); ?>'>{width}</option>
						<option title='<?php echo __("The length of this product", "twp-plugin"); ?>'>{length}</option>
						<option title='<?php echo __("The height of this product", "twp-plugin"); ?>'>{height}</option>
						<option title='<?php echo __("The weight of this product", "twp-plugin"); ?>'>{weight}</option>
						<option title='<?php echo __("The price of this product", "twp-plugin"); ?>'>{price}</option>
						<option title='<?php echo __("The regular price of this product", "twp-plugin"); ?>'>{regular_price}</option>
						<option title='<?php echo __("The sale price of this product", "twp-plugin"); ?>'>{sale_price}</option>
						<option title='<?php echo __("The SKU (Stock Keeping Unit) of this product", "twp-plugin"); ?>'>{sku}</option>
						<option title='<?php echo __("The stock amount of this product", "twp-plugin"); ?>'>{stock}</option>
						<option title='<?php echo __("Is this product downloadable? (Yes or No)", "twp-plugin"); ?>'>{downloadable}</option>
						<option title='<?php echo __("Is this product virtual? (Yes or No)", "twp-plugin"); ?>'>{virtual}</option>
						<option title='<?php echo __("Is this product sold individually? (Yes or No)", "twp-plugin"); ?>'>{sold_individually}</option>
						<option title='<?php echo __("The tax status of this product", "twp-plugin"); ?>'>{tax_status}</option>
						<option title='<?php echo __("The tax class of this product", "twp-plugin"); ?>'>{tax_class}</option>
						<option title='<?php echo __("The stock status of this product", "twp-plugin"); ?>'>{stock_status}</option>
						<option title='<?php echo __("Whether or not backorders allowed? ", "twp-plugin"); ?>'>{backorders}</option>
						<option title='<?php echo __("Is this a featured product? (Yes or No)", "twp-plugin"); ?>'>{featured}</option>
						<option title='<?php echo __("Is this product visible? (Yes or No)", "twp-plugin"); ?>'>{visibility}</option>
						<?php } ?>
					</optgroup>
				</select>
				<select id="emoji-select" class="wp-exclude-emoji">
				<option selected="true" style="display:none;"><?php echo __("Emoji...", "twp-plugin"); ?></option>
					<optgroup label="Common Emojis">
						<option title=':smile:'>😄</option>
						<option title=':smiley:'>😃</option>
						<option title=':grinning:'>😀</option>
						<option title=':blush:'>😊</option>
						<option title=':relaxed:'>☺️</option>
						<option title=':wink:'>😉</option>
						<option title=':heart_eyes:'>😍</option>
						<option title=':kissing_heart:'>😘</option>
						<option title=':kissing_closed_eyes:'>😚</option>
						<option title=':kissing:'>😗</option>
						<option title=':kissing_smiling_eyes:'>😙</option>
						<option title=':stuck_out_tongue_winking_eye:'>😜</option>
						<option title=':stuck_out_tongue_closed_eyes:'>😝</option>
						<option title=':stuck_out_tongue:'>😛</option>
						<option title=':flushed:'>😳</option>
						<option title=':grin:'>😁</option>
						<option title=':pensive:'>😔</option>
						<option title=':relieved:'>😌</option>
						<option title=':unamused:'>😒</option>
						<option title=':disappointed:'>😞</option>
						<option title=':persevere:'>😣</option>
						<option title=':cry:'>😢</option>
						<option title=':joy:'>😂</option>
						<option title=':sob:'>😭</option>
						<option title=':sleepy:'>😪</option>
						<option title=':disappointed_relieved:'>😥</option>
						<option title=':cold_sweat:'>😰</option>
						<option title=':sweat_smile:'>😅</option>
						<option title=':sweat:'>😓</option>
						<option title=':weary:'>😩</option>
						<option title=':tired_face:'>😫</option>
						<option title=':fearful:'>😨</option>
						<option title=':scream:'>😱</option>
						<option title=':angry:'>😠</option>
						<option title=':rage:'>😡</option>
						<option title=':triumph:'>😤</option>
						<option title=':confounded:'>😖</option>
						<option title=':laughing:'>😆</option>
						<option title=':yum:'>😋</option>
						<option title=':mask:'>😷</option>
						<option title=':sunglasses:'>😎</option>
						<option title=':sleeping:'>😴</option>
						<option title=':dizzy_face:'>😵</option>
						<option title=':astonished:'>😲</option>
						<option title=':worried:'>😟</option>
						<option title=':frowning:'>😦</option>
						<option title=':anguished:'>😧</option>
						<option title=':smiling_imp:'>😈</option>
						<option title=':imp:'>👿</option>
						<option title=':open_mouth:'>😮</option>
						<option title=':grimacing:'>😬</option>
						<option title=':neutral_face:'>😐</option>
						<option title=':confused:'>😕</option>
						<option title=':hushed:'>😯</option>
						<option title=':no_mouth:'>😶</option>
						<option title=':innocent:'>😇</option>
						<option title=':smirk:'>😏</option>
						<option title=':expressionless:'>😑</option>
						<option title=':man_with_gua_pi_mao:'>👲</option>
						<option title=':man_with_turban:'>👳</option>
						<option title=':cop:'>👮</option>
						<option title=':construction_worker:'>👷</option>
						<option title=':guardsman:'>💂</option>
						<option title=':baby:'>👶</option>
						<option title=':boy:'>👦</option>
						<option title=':girl:'>👧</option>
						<option title=':man:'>👨</option>
						<option title=':woman:'>👩</option>
						<option title=':older_man:'>👴</option>
						<option title=':older_woman:'>👵</option>
						<option title=':person_with_blond_hair:'>👱</option>
						<option title=':angel:'>👼</option>
						<option title=':princess:'>👸</option>
						<option title=':smiley_cat:'>😺</option>
						<option title=':smile_cat:'>😸</option>
						<option title=':heart_eyes_cat:'>😻</option>
						<option title=':kissing_cat:'>😽</option>
						<option title=':smirk_cat:'>😼</option>
						<option title=':scream_cat:'>🙀</option>
						<option title=':crying_cat_face:'>😿</option>
						<option title=':joy_cat:'>😹</option>
						<option title=':pouting_cat:'>😾</option>
						<option title=':japanese_ogre:'>👹</option>
						<option title=':japanese_goblin:'>👺</option>
						<option title=':see_no_evil:'>🙈</option>
						<option title=':hear_no_evil:'>🙉</option>
						<option title=':speak_no_evil:'>🙊</option>
						<option title=':skull:'>💀</option>
						<option title=':alien:'>👽</option>
						<option title=':hankey:'>💩</option>
						<option title=':fire:'>🔥</option>
						<option title=':sparkles:'>✨</option>
						<option title=':star2:'>🌟</option>
						<option title=':dizzy:'>💫</option>
						<option title=':boom:'>💥</option>
						<option title=':anger:'>💢</option>
						<option title=':sweat_drops:'>💦</option>
						<option title=':droplet:'>💧</option>
						<option title=':zzz:'>💤</option>
						<option title=':dash:'>💨</option>
						<option title=':ear:'>👂</option>
						<option title=':eyes:'>👀</option>
						<option title=':nose:'>👃</option>
						<option title=':tongue:'>👅</option>
						<option title=':lips:'>👄</option>
						<option title=':+1:'>👍</option>
						<option title=':-1:'>👎</option>
						<option title=':ok_hand:'>👌</option>
						<option title=':punch:'>👊</option>
						<option title=':fist:'>✊</option>
						<option title=':v:'>✌️</option>
						<option title=':wave:'>👋</option>
						<option title=':hand:'>✋</option>
						<option title=':open_hands:'>👐</option>
						<option title=':point_up_2:'>👆</option>
						<option title=':point_down:'>👇</option>
						<option title=':point_right:'>👉</option>
						<option title=':point_left:'>👈</option>
						<option title=':raised_hands:'>🙌</option>
						<option title=':pray:'>🙏</option>
						<option title=':point_up:'>☝️</option>
						<option title=':clap:'>👏</option>
						<option title=':muscle:'>💪</option>
						<option title=':walking:'>🚶</option>
						<option title=':runner:'>🏃</option>
						<option title=':dancer:'>💃</option>
						<option title=':couple:'>👫</option>
						<option title=':family:'>👪</option>
						<option title=':two_men_holding_hands:'>👬</option>
						<option title=':two_women_holding_hands:'>👭</option>
						<option title=':couplekiss:'>💏</option>
						<option title=':couple_with_heart:'>💑</option>
						<option title=':dancers:'>👯</option>
						<option title=':ok_woman:'>🙆</option>
						<option title=':no_good:'>🙅</option>
						<option title=':information_desk_person:'>💁</option>
						<option title=':raising_hand:'>🙋</option>
						<option title=':massage:'>💆</option>
						<option title=':haircut:'>💇</option>
						<option title=':nail_care:'>💅</option>
						<option title=':bride_with_veil:'>👰</option>
						<option title=':person_with_pouting_face:'>🙎</option>
						<option title=':person_frowning:'>🙍</option>
						<option title=':bow:'>🙇</option>
						<option title=':tophat:'>🎩</option>
						<option title=':crown:'>👑</option>
						<option title=':womans_hat:'>👒</option>
						<option title=':athletic_shoe:'>👟</option>
						<option title=':mans_shoe:'>👞</option>
						<option title=':sandal:'>👡</option>
						<option title=':high_heel:'>👠</option>
						<option title=':boot:'>👢</option>
						<option title=':shirt:'>👕</option>
						<option title=':necktie:'>👔</option>
						<option title=':womans_clothes:'>👚</option>
						<option title=':dress:'>👗</option>
						<option title=':running_shirt_with_sash:'>🎽</option>
						<option title=':jeans:'>👖</option>
						<option title=':kimono:'>👘</option>
						<option title=':bikini:'>👙</option>
						<option title=':briefcase:'>💼</option>
						<option title=':handbag:'>👜</option>
						<option title=':pouch:'>👝</option>
						<option title=':purse:'>👛</option>
						<option title=':eyeglasses:'>👓</option>
						<option title=':ribbon:'>🎀</option>
						<option title=':closed_umbrella:'>🌂</option>
						<option title=':lipstick:'>💄</option>
						<option title=':yellow_heart:'>💛</option>
						<option title=':blue_heart:'>💙</option>
						<option title=':purple_heart:'>💜</option>
						<option title=':green_heart:'>💚</option>
						<option title=':heart:'>❤️</option>
						<option title=':broken_heart:'>💔</option>
						<option title=':heartpulse:'>💗</option>
						<option title=':heartbeat:'>💓</option>
						<option title=':two_hearts:'>💕</option>
						<option title=':sparkling_heart:'>💖</option>
						<option title=':revolving_hearts:'>💞</option>
						<option title=':cupid:'>💘</option>
						<option title=':love_letter:'>💌</option>
						<option title=':kiss:'>💋</option>
						<option title=':ring:'>💍</option>
						<option title=':gem:'>💎</option>
						<option title=':bust_in_silhouette:'>👤</option>
						<option title=':busts_in_silhouette:'>👥</option>
						<option title=':speech_balloon:'>💬</option>
						<option title=':footprints:'>👣</option>
						<option title=':thought_balloon:'>💭</option>
					</optgroup>
				</select>
				<?php 
				echo "<span id='cheatsheet-link'>🔗<a href='http://ameer.ir/emoji-cheatsheet?utm_source=emoji&utm_medium=twp_settings&utm_campaign=twp' title='".__("Click to see a full list of available emojis and their shortnames", "twp-plugin")."' target='_blank'>".__("Emoji full list", "twp-plugin")."</a></span>";
				echo "<span id='patterns-template'>🔗<a href='http://ameer.ir/telegram-for-wp#twp-patterns?utm_source=patterns&utm_medium=twp_settings&utm_campaign=twp' title='".__("Click to see some patterns template", "twp-plugin")."' target='_blank'>".__("Patterns Template", "twp-plugin")."</a></span>";
				?>
			</div>
			<div id="twp_box_container">
				<textarea id="twp_channel_pattern" name="twp_channel_pattern" dir="auto"><?php echo $cp ?></textarea>
				<div id="output" dir="auto"><?php echo $cp ?></div>
			</div>
			<div class="twp-radio-group">
				<input type="radio" name="twp_send_thumb" id="twp-send-thumb-0" <?php echo ($s==0)?'checked=checked':'' ?> value="0">
				<label for="twp-send-thumb-0"><?php echo __("Don't send featured image", "twp-plugin"); ?></label>
				<br>
				<input type="radio" name="twp_send_thumb" id="twp-send-thumb-1" <?php echo ($s==1)?'checked=checked':'' ?> value="1">
				<label for="twp-send-thumb-1"><?php echo __("Send featured image", "twp-plugin"); ?></label>
				<br>
				<?php
				$hook = get_current_screen();
				# if we are in post pages, then show the custom image option.
				if ($hook->base == 'post'){
				?>
				<input type="radio" name="twp_send_thumb" id="twp-send-thumb-2" <?php echo ($s==2)?'checked=checked':'' ?> value="2">
				<label for="twp-send-thumb-2"><?php echo __("Send custom image", "twp-plugin"); ?></label>
				<div class="twp-img-container">
					<?php if ( $twp_have_img ) : ?>
						<img src="<?php echo $twp_img_src[0] ?>" alt="" style="max-width:100%;" />
					<?php endif; ?>
				</div>
				<!-- twp add & remove image links -->
				<p id="twp-upload-link" class="hide-if-no-js <?php if ($s != 2) { echo 'hidden'; } ?>">
					<a class="upload-custom-img <?php if ( $twp_have_img  ) { echo 'hidden'; } ?>" 
						href="<?php echo $twp_img_upload_link ?>">
						<?php _e('Set custom image') ?>
					</a>
					<a class="delete-custom-img <?php if ( ! $twp_have_img  ) { echo 'hidden'; } ?>" 
						href="#">
						<?php _e('Remove this image') ?>
					</a>
				</p>
				<!-- A hidden input to set and post the chosen image id -->
				<input class="twp-img-id" name="twp_img_id" type="hidden" value="<?php echo esc_attr( $twp_img_id ); ?>" />
				<?php } #end if ?>
			</div>
		</td>
	</tr>
	<?php 
	// if we are in post pages, then show the custom image option.
	if ($hook->base == 'post'){
	?>
	<tr>
		<th scope="row">
			<h3><?php echo __("Send file", "twp-plugin"); ?></h3>
		</th>
		<td>
			<p class="howto">
				<?php 
				echo __("Select the file that you want to send with this post.", "twp-plugin");
				?>
			</p>
			<div class="twp-file-container">
				<div id="twp-file-icon">
					<?php if ( $twp_have_file ) : ?>
						<a href="#" title="<?php echo __("Edit file", "twp-plugin") ?>"><img src="<?php echo $attachment['icon'] ?>" alt="" style="max-width:100%;" /></a>
					<?php endif; ?>
				</div>
				<div id="twp-file-details">
					<?php if ( $twp_have_file ) : ?>
						<p> Title: <span><?php echo $attachment['filename'] ?></span></p>
						<p> Caption: <span><?php echo $attachment['caption'] ?></span></p>
						<p> Size: <span id="filesize-span"><?php echo $attachment['filesizeHumanReadable'] ?></span></p>
					<?php endif; ?>
				</div>
			</div>
			<!-- twp add & remove image links -->
			<p id="twp-upload-link" class="hide-if-no-js">
				<a class="upload-custom-file <?php if ( $twp_have_file  ) { echo 'hidden'; } ?>" 
					href="<?php echo $twp_file_upload_link ?>">
					<?php echo __('Set custom file', 'twp-plugin') ?>
				</a>
				<a class="delete-custom-file <?php if ( ! $twp_have_file  ) { echo 'hidden'; } ?>" 
					href="#">
					<?php echo __('Remove this file', 'twp-plugin') ?>
				</a>
			</p>
			<!-- A hidden input to set and post the chosen file id -->
			<input class="twp-file-id" name="twp_file_id" type="hidden" value="<?php echo esc_attr( $twp_file_id ); ?>" />
		</td>
	</tr>
	<?php } #end if ?>