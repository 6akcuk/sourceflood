<h2>Work Horse Settings</h2>

<?php if (!sourceflood_configured()): ?>
    <?php SourceFlood\View::make('sourceflood.misconfigured') ?>
<?php endif; ?>

<form method="post" action="options.php">
	<?php settings_fields('workhorse_settings'); ?>

	<table class="form-table">
        <tr valign="top">
            <th scope="row">License Key</th>
            <td>
                <?php 
                    use SourceFlood\License;

                    $license_key = get_option('workhorse_license_key');
                    if ($license_key) License::checkThatLicenseIsValid();
                ?>
                <input type="text" name="workhorse_license_key" style="width: 300px" value="<?php echo $license_key ?>" />
                <?php if ($license_key): ?>
                    <div>
                        License expire at: <strong><?= License::$status->until ?></strong>
                        
                    </div>
                <?php endif; ?>
            </td>
        </tr>
        <tr valign="top">
	        <th scope="row">Pixabay Key</th>
	        <td>
	        	<input type="text" name="workhorse_pixabay_key" style="width: 300px" value="<?php echo get_option('workhorse_pixabay_key'); ?>" />
        	</td>
        </tr>
        <tr valign="top">
            <th scope="row">Google Maps API Key</th>
            <td>
                <input type="text" name="workhorse_google_api_key" style="width: 300px" value="<?php echo get_option('workhorse_google_api_key'); ?>" />
            </td>
        </tr>
        <tr valign="top">
            <th scope="row">YouTube API Key</th>
            <td>
                <input type="text" name="workhorse_youtube_key" style="width: 300px" value="<?php echo get_option('workhorse_youtube_key'); ?>" />
            </td>
        </tr>
        <tr valign="top">
            <th scope="row">Word AI Email</th>
            <td>
                <input type="text" name="workhorse_word_ai_email" style="width: 300px" value="<?php echo get_option('workhorse_word_ai_email'); ?>" />
            </td>
        </tr>
        <tr valign="top">
            <th scope="row">Word AI Password</th>
            <td>
                <input type="text" name="workhorse_word_ai_pass" style="width: 300px" value="<?php echo get_option('workhorse_word_ai_pass'); ?>" />
            </td>
        </tr>
    </table>
    
    <p class="submit">
    	<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
    </p>
</form>