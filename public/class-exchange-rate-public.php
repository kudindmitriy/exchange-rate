<?php

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Exchange_Rate
 * @subpackage Exchange_Rate/public
 * @author     Dmitry Kudin <kudin.dima@gmail.com>
 */
class Exchange_Rate_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

    /**
     * @var string
     */
    private $ajax_nonce_action;

    /**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
        $this->ajax_nonce_action = $this->plugin_name . 'ajax-nonce';

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/exchange-rate-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/exchange-rate-public.js', array( 'jquery' ), $this->version, false );

        wp_localize_script( $this->plugin_name, 'localize',
            array(
                'nonce' => wp_create_nonce($this->ajax_nonce_action),
                'ajaxurl' => admin_url('admin-ajax.php')
            )
        );

	}

    public function register_shortcodes() {
        add_shortcode('exchange_rate', array($this, 'shortcode_exchange_rate'));
    }

    public function shortcode_exchange_rate() {
        global $post;

        $currency_pair = explode('-', strtoupper($post->post_name) ) ;

        $result = $this->get_exchange_rate($currency_pair);

        if (empty($result)) {
            return 'No current exchange rate for currency pairs';
        }

        $html = '<div class="showInfo info">
                    <div class="container-alert">
                        <input id="currency_pair" type="hidden"  name="currency_pair" value="'. strtoupper($post->post_name) .'">
                        <p>' . __('Курс EXMO' , $this->plugin_name) . ' ' . $currency_pair[0] . ' -> ' . $currency_pair[1] . ' ' . __('на' , $this->plugin_name) . ' <span id="exchange_last_update">' . $result->stt_last_update . '</span>                
                            <br>' . __('Продажа' , $this->plugin_name) . ': <strong id="exchange_sell_price">' . $result->sell_price . '</strong>. ' . __('Покупка' , $this->plugin_name) . ': <strong id="exchange_buy_price">' . $result->buy_price . '</strong>                
                        </p>  
                    </div>
                </div>';

        return $html;
    }

    public function get_exchange_rate($currency_pair) {
        global $wpdb;

        $first_cur_id = $wpdb->get_var(
            "SELECT `stv_vlt_id`
					FROM `{$wpdb->prefix}stock_vlts`
					WHERE `stv_name`= '{$currency_pair[0]}'"
        );

        $second_cur_id = $wpdb->get_var(
            "SELECT `{$wpdb->prefix}iso_wallet`.iso_wallet_id
					FROM `{$wpdb->prefix}iso_wallet`
					LEFT JOIN `{$wpdb->prefix}vlt` ON `{$wpdb->prefix}iso_wallet`.wallet_id = `{$wpdb->prefix}vlt`.vltid
					WHERE `{$wpdb->prefix}vlt`.vltname= '{$currency_pair[1]}'"
        );

        $result = $wpdb->get_row(
            "SELECT `{$wpdb->prefix}stock_tickers`.sell_price, `{$wpdb->prefix}stock_tickers`.buy_price, `{$wpdb->prefix}stock_tickers`.stt_last_update
					FROM `{$wpdb->prefix}stock_tickers`
					WHERE `{$wpdb->prefix}stock_tickers`.pair_x = '{$first_cur_id}' AND `{$wpdb->prefix}stock_tickers`.pair_y = '{$second_cur_id}'"
        );

        $result->sell_price = floatval(  $result->sell_price);
        $result->buy_price = floatval(  $result->buy_price);

        return $result;
    }

    public function ajax_update_exchange_rate() {

        if( ! wp_verify_nonce( $_POST['nonce_code'], $this->ajax_nonce_action ) ) {
            wp_die( __('Invalid nonce code', $this->plugin_name) , 403 );
        }

        $currency_pair = explode('-', $_POST['currency_pair'] ) ;

        $result = $this->get_exchange_rate($currency_pair);

        if (empty($result)) {
            wp_die( __('No current exchange rate for currency pairs', $this->plugin_name) , 403 );
        }

        die( json_encode( $result ) );

    }

}
