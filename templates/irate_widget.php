<p>
	<label for="irate_title">
		<?php _e('Title', 'irate'); ?>:
		<input class="widefat" id="irate_title" name="irate_title" type="text" value="<?php echo $title; ?>" />
	</label>
</p>
<p>
	<label for="irate_username">
		<?php _e('Username', 'irate'); ?>:
		<input class="widefat" id="irate_username" name="irate_username" type="text" value="<?php echo $username; ?>" />
	</label>
</p>
<p>
	<label for="irate_username">
		<?php echo __('Timestamp', 'irate'). ": $timestamp"; ?>
	</label>
</p>
<input type="hidden" id="irate-submit" name="irate-submit" value="1" />
