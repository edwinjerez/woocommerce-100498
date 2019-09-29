<h2>Cognito Forms</h2>

<form method="post" action="options.php">
    <?php settings_fields('cognito_plugin'); ?>
	
    <table class="form-table">
        <tr valign="top">
        <th scope="row">API Key</th>
        <td><input type="text" name="cognito_api_key" style="width:300px;" value="<?php echo get_option('cognito_api_key'); ?>" /></td>
        </tr>
        <tr valign="top">
        <th scope="row">Admin API Key</th>
        <td><input type="text" name="cognito_admin_key" style="width:300px;" value="<?php echo get_option('cognito_admin_key'); ?>" /></td>
        </tr>
        <tr valign="top">
        <th scope="row">Public API Key</th>
        <td><input type="text" name="cognito_public_key" style="width:300px;" value="<?php echo get_option('cognito_public_key'); ?>" /></td>
        </tr>
        <tr valign="top">
        <th scope="row">Organization Code</th>
        <td><input type="text" name="cognito_organization" style="width:300px;" value="<?php echo get_option('cognito_organization'); ?>" /></td>
        </tr>
    </table>
    
    <p class="submit">
    <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
    </p>

</form>