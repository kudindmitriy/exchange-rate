<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://github.com/kudindmitriy
 * @since      1.0.0
 *
 * @package    Exchange_Rate
 * @subpackage Exchange_Rate/admin/partials
 */
?>

<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
<fieldset>
    <div class="admin-text"><?php _e('Use the shortcode', $this->plugin_name);?> <span class="admin-shortcode">[exchange_rate]</span> <?php _e('to display the current exchange rate for currency pairs', $this->plugin_name);?></div>
</fieldset>



<!-- This file should primarily consist of HTML with a little bit of PHP. -->
