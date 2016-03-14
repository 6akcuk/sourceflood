<h2>Work Horse Settings</h2>

<form method="post" action="options.php">
	<?php settings_fields('sourceflood_settings'); ?>

	<table class="form-table">
        <tr valign="top">
	        <th scope="row">Pixabay Key</th>
	        <td>
	        	<input type="text" name="sourceflood_pixabay_key" style="width: 300px" value="<?php echo get_option('sourceflood_pixabay_key'); ?>" />
        	</td>
        </tr>
    </table>
    
    <p class="submit">
    	<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
    </p>
</form>