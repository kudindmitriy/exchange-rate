<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Exchange_Rate
 * @subpackage Exchange_Rate/admin
 * @author     Dmitry Kudin <kudin.dima@gmail.com>
 */
class Exchange_Rate_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

    /**
     * The plugin options.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_options    The plugin options.
     */
    private $plugin_options;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
        $this->plugin_options = get_option($this->plugin_name);

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/exchange-rate-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/exchange-rate-admin.js', array( 'jquery' ), $this->version, false );

	}

    /**
     * Register the administration menu for this plugin into the WordPress Dashboard menu.
     */

    public function add_plugin_admin_menu() {

        /*
         * Add a settings page for this plugin to the Settings menu.
        */
        add_options_page( 'Exchange Rate Setup', 'Exchange Rate', 'manage_options', $this->plugin_name, array($this, 'display_plugin_setup_page')
        );
    }

    /**
     * Add settings action link to the plugins page.
     */

    public function add_action_links( $links ) {

        $settings_link = array(
            '<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_name ) . '">' . __('Settings', $this->plugin_name) . '</a>',
        );
        return array_merge(  $settings_link, $links );

    }

    /**
     * Render the settings page for this plugin.
     */

    public function display_plugin_setup_page() {

        include_once( 'partials/exchange-rate-admin-display.php' );

    }

    /**
     * Render the settings page for this plugin.
     */

    public function create_db_tables() {
        global $wpdb;
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );


        if ( empty( $this->plugin_options ) ) {

            add_option( $this->plugin_name, ['plugin_db_version' => '1.0'] );

            $sql = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}iso_wallet` (
                id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                wallet_id int(11) NOT NULL  REFERENCES `{$wpdb->prefix}vlt`(vltid),
                iso_wallet_id int(11) NOT NULL  REFERENCES `{$wpdb->prefix}stock_vlts`(stv_vlt_id)) ENGINE = InnoDB AUTO_INCREMENT = 1 DEFAULT CHARSET = latin1";
            dbDelta( $sql );


            $sql = "INSERT INTO `{$wpdb->prefix}iso_wallet` (wallet_id, iso_wallet_id)
                SELECT `{$wpdb->prefix}vlt`.vltid as wallet_id, `{$wpdb->prefix}stock_vlts`.stv_vlt_id as iso_wallet_id
                FROM `{$wpdb->prefix}vlt`
                INNER JOIN `{$wpdb->prefix}stock_vlts` ON `{$wpdb->prefix}vlt`.vltname_list LIKE  CONCAT('%', `{$wpdb->prefix}stock_vlts`.stv_name, '%')";
            dbDelta( $sql );

        }
    }

}
