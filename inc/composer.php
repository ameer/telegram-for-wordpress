<style type="text/css">
	textarea#twp_channel_pattern {resize: vertical; width: 48%; height: auto;min-height: 128px;}
	div#output {width: 48%; display: inline-block; vertical-align: top; white-space: pre; border: 1px solid #ddd; height: 122px; background: #F1F1F1; cursor: not-allowed;padding: 2px 6px; overflow-y: auto } 
	.emojione{font-size:inherit;height:3ex;width:3.1ex;min-height:20px;min-width:20px;display:inline-block;margin:-.2ex .15em .2ex;line-height:normal;vertical-align:middle}
	img.emojione{width:auto}
</style>
<tr>
	<th scope="row">
		<h3><?php echo __("Message Pattern", "twp-plugin"); ?></h3><br>
		<p style="font-weight:400;margin:0;"><span style="color:#309152;font-size: 1.5em;">&#x25a0;</span> <?php echo __("Wordpress default tags", "twp-plugin") ?> </p>
		<p style="font-weight:400;margin:0;"><span style="color:#a46497;font-size: 1.5em;">&#x25a0;</span> <?php echo __("WooCommerce default tags", "twp-plugin"); ?> </p>
	</th>
	<td>
		<p class="howto">
			<?php 
			echo __("Here you can define the structure of messages that are sent to the channel. Click on the below tags to make your custom pattern. Also you can type anything else in the below textarea (including Emoji shortname)", "twp-plugin");
			echo "<a href='http://ameer.ir/emoji-cheatsheet?utm_source=emoji&utm_medium=twp_settings&utm_campaign=twp' title='Click to see a full list of available emojis and their shortnames' target='_blank'>".__("Emoji full list", "twp-plugin")."</a>"; 
			?>
		</p>
		<div class="toolbar">
			<ul class="patterns" style="margin-bottom:0;">
				<li>{title}</li>
				<li>{excerpt}</li>
				<li>{content}</li>
				<li>{author}</li>
				<li>{short_url}</li>
				<li>{full_url}</li>
				<li>{tags}</li>
				<li>{categories}</li>
			</ul>
			<?php if ($is_wc_active) {
				?>
				<ul class="patterns wc-patterns" style="margin-top:0;">
					<li>{width}</li>
					<li>{length}</li>
					<li>{height}</li>
					<li>{weight}</li>
					<li>{price}</li>
					<li>{regular_price}</li>
					<li>{sale_price}</li>
					<li>{sku}</li>
					<li>{stock}</li>
					<li>{downloadable}</li>
					<li>{virtual}</li>
					<li>{sold_individually}</li>
					<li>{tax_status}</li>
					<li>{tax_class}</li>
					<li>{stock_status}</li>
					<li>{backorders}</li>
					<li>{featured}</li>
					<li>{visibility}</li>
				</ul>
				<?php } ?>
			</div>
			<textarea id="twp_channel_pattern" name="twp_channel_pattern"><?php echo $cp ?></textarea>
			<div id="output"><?php echo $cp ?></div>
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