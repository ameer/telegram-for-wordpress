<style type="text/css">
	textarea#twp_channel_pattern {resize: vertical; width: 48%; height: auto;min-height: 128px;}
	div#output {box-sizing: border-box; width: 48%; display: inline-block; vertical-align: top; white-space: pre; border: 1px solid #ddd; height: 128px; background: #F1F1F1; cursor: not-allowed;padding: 2px 6px; overflow-y: auto } 
	.emojione{font-size:inherit;height:3ex;width:3.1ex;min-height:20px;min-width:20px;display:inline-block;margin:-.2ex .15em .2ex;line-height:normal;vertical-align:middle}
	img.emojione{width:auto}
	#twp_metabox td, #twp_metabox th {padding-top: 0 !important;}
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
		<p style="font-weight:400;margin:0;"><span style="color:#309152;font-size: 1.5em;">&#x25a0;</span> <?php echo __("Wordpress default tags", "twp-plugin") ?> </p>
		<?php if ($is_product){echo '<p style="font-weight:400;margin:0;"><span style="color:#a46497;font-size: 1.5em;">&#x25a0;</span>'. __("WooCommerce default tags", "twp-plugin").'</p>';}?>
	</th>
	<td>
		<p class="howto">
			<?php 
			echo __("Here you can define the structure of messages that are sent to the channel. <br> Click on the below tags to make your custom pattern. <br>Also you can type anything else in the below textarea (including Emoji shortname)", "twp-plugin");
			echo " <a href='http://ameer.ir/emoji-cheatsheet?utm_source=emoji&utm_medium=twp_settings&utm_campaign=twp' title='Click to see a full list of available emojis and their shortnames' target='_blank'>".__("Emoji full list", "twp-plugin")."</a>"; 
			?>
		</p>
		<div class="toolbar">
			<ul class="patterns" style="margin-bottom:0;">
				<li title='<?php echo __("The title of this post", "twp-plugin"); ?>'>{title}</li>
				<li title='<?php echo __("The first 55 words of this post", "twp-plugin"); ?>'>{excerpt}</li>
				<li title='<?php echo __("The whole content of this post", "twp-plugin"); ?>'>{content}</li>
				<li title='<?php echo __("The display name of author of this post", "twp-plugin"); ?>'>{author}</li>
				<li title='<?php echo __("The short url of this post", "twp-plugin"); ?>'>{short_url}</li>
				<li title='<?php echo __("The permalink of this post", "twp-plugin"); ?>'>{full_url}</li>
				<li title='<?php echo __("The tags of this post. Tags are automatically converted to Telegram hashtags", "twp-plugin"); ?>'>{tags}</li>
				<li title='<?php echo __("The categories of this post. Categories are automatically separated by | symbol", "twp-plugin"); ?>'>{categories}</li>
			</ul>
			<?php if ($is_wc_active) {
				?>
				<ul class="patterns wc-patterns" style="margin-top:0;">
					<li title='<?php echo __("The width of this product", "twp-plugin"); ?>'>{width}</li>
					<li title='<?php echo __("The length of this product", "twp-plugin"); ?>'>{length}</li>
					<li title='<?php echo __("The height of this product", "twp-plugin"); ?>'>{height}</li>
					<li title='<?php echo __("The weight of this product", "twp-plugin"); ?>'>{weight}</li>
					<li title='<?php echo __("The price of this product", "twp-plugin"); ?>'>{price}</li>
					<li title='<?php echo __("The regular price of this product", "twp-plugin"); ?>'>{regular_price}</li>
					<li title='<?php echo __("The sale price of this product", "twp-plugin"); ?>'>{sale_price}</li>
					<li title='<?php echo __("The SKU (Stock Keeping Unit) of this product", "twp-plugin"); ?>'>{sku}</li>
					<li title='<?php echo __("The stock amount of this product", "twp-plugin"); ?>'>{stock}</li>
					<li title='<?php echo __("Is this product downloadable? (Yes or No)", "twp-plugin"); ?>'>{downloadable}</li>
					<li title='<?php echo __("Is this product virtual? (Yes or No)", "twp-plugin"); ?>'>{virtual}</li>
					<li title='<?php echo __("Is this product sold individually? (Yes or No)", "twp-plugin"); ?>'>{sold_individually}</li>
					<li title='<?php echo __("The tax status of this product", "twp-plugin"); ?>'>{tax_status}</li>
					<li title='<?php echo __("The tax class of this product", "twp-plugin"); ?>'>{tax_class}</li>
					<li title='<?php echo __("The stock status of this product", "twp-plugin"); ?>'>{stock_status}</li>
					<li title='<?php echo __("Whether or not backorders allowed? ", "twp-plugin"); ?>'>{backorders}</li>
					<li title='<?php echo __("Is this a featured product? (Yes or No)", "twp-plugin"); ?>'>{featured}</li>
					<li title='<?php echo __("Is this product visible? (Yes or No)", "twp-plugin"); ?>'>{visibility}</li>
				</ul>
				<?php } ?>
			</div>
			<div id="twp_box_container">
				<textarea id="twp_channel_pattern" name="twp_channel_pattern"><?php echo $cp ?></textarea>
				<div id="output"><?php echo $cp ?></div>
			</div>
			<div id="send-thumb-select">
				<input type="radio" name="twp_send_thumb" id="twp-send-thumb-0" <?php echo ($s==0)?'checked=checked':'' ?> value="0">
				<label for="twp-send-thumb-0">Don't send featured image</label>
				<br>
				<input type="radio" name="twp_send_thumb" id="twp-send-thumb-1" <?php echo ($s==1)?'checked=checked':'' ?> value="1">
				<label for="twp-send-thumb-1">Send featured image</label>
				<br>
			</div>
		</td>
	</tr>