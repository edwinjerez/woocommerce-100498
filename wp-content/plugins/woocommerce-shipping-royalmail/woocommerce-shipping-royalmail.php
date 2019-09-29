<?php
/**
 * Plugin Name: WooCommerce Royal Mail
 * Plugin URI: https://woocommerce.com/products/royal-mail/
 * Description: Offer Royal Mail shipping rates automatically to your customers. Prices according to <a href="https://www.royalmail.com/sites/default/files/royal-mail-our-prices-25-march-2019.pdf">the 2019 price guide</a>.
 * Version: 2.5.17
 * Author: WooCommerce
 * Author URI: https://woocommerce.com/
 * Copyright: 2019 WooCommerce.
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * WC tested up to: 3.7
 * WC requires at least: 2.6
 * Tested up to: 5.1
 *
 * Woo: 182719:03839cca1a16c4488fcb669aeb91a056
 *
 * @package WC_RoyalMail
 */

/**
 * Required functions
 */
if ( ! function_exists( 'woothemes_queue_update' ) ) {
	require_once( 'woo-includes/woo-functions.php' );
}

/**
 * Plugin updates
 */
woothemes_queue_update( plugin_basename( __FILE__ ), '03839cca1a16c4488fcb669aeb91a056', '182719' );

/**
 * Only load the plugin if WooCommerce is activated
 */
if ( is_woocommerce_active() ) {

	/**
	 * Main Royal Mail class
	 */
	class WC_RoyalMail {
		/**
		 * Plugin's version.
		 *
		 * @since 2.5.0 introduced.
		 *
		 * @var string
		 */
		public $version = '2.5.17';

		/**
		 * Constructor.
		 */
		public function __construct() {
			add_action( 'admin_init', array( $this, 'maybe_install' ), 5 );
			add_action( 'plugins_loaded', array( $this, 'load_plugin_textdomain' ) );
			add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'plugin_action_links' ) );
			add_action( 'woocommerce_shipping_init', array( $this, 'shipping_init' ) );
			add_filter( 'woocommerce_shipping_methods', array( $this, 'shipping_methods' ) );
			add_action( 'admin_notices', array( $this, 'environment_check' ) );
			add_action( 'admin_notices', array( $this, 'upgrade_notice' ) );
			add_action( 'wp_ajax_royal_mail_dismiss_upgrade_notice', array( $this, 'dismiss_upgrade_notice' ) );
		}

		/**
		 * Localisation.
		 */
		public function load_plugin_textdomain() {
			load_plugin_textdomain( 'woocommerce-shipping-royalmail', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		}

		/**
		 * Check environment.
		 *
		 * Hooked into `admin_notices` so it'll render admin notice if there's
		 * a failed check.
		 *
		 * @access public
		 * @return void
		 */
		public function environment_check() {
			if ( version_compare( WC_VERSION, '2.6.0', '<' ) ) {
				return;
			}

			if ( 'GBP' !== get_woocommerce_currency() ) {
				echo '<div class="error">
					<p>' . sprintf( __( 'Royal Mail requires that the <a href="%s">currency</a> is set to Pound sterling.', 'woocommerce-shipping-royalmail' ), admin_url( 'admin.php?page=wc-settings&tab=general' ) ) . '</p>
				</div>';
			}

			if ( 'GB' !== WC()->countries->get_base_country() ) {
				echo '<div class="error">
					<p>' . sprintf( __( 'Royal Mail requires that the <a href="%s">base country/region</a> is set to United Kingdom.', 'woocommerce-shipping-royalmail' ), admin_url( 'admin.php?page=wc-settings&tab=general' ) ) . '</p>
				</div>';
			}
		}

		/**
		 * Add plugin action links to the plugins page.
		 *
		 * @param array $links Links.
		 *
		 * @return array Links.
		 */
		public function plugin_action_links( $links ) {
			$plugin_links = array(
				'<a href="https://woocommerce.com/my-account/create-a-ticket?broken=primary&select=182719">' . __( 'Support', 'woocommerce-shipping-royalmail' ) . '</a>',
				'<a href="https://www.woocommerce.com/products/royal-mail">' . __( 'Docs', 'woocommerce-shipping-royalmail' ) . '</a>',
			);
			return array_merge( $plugin_links, $links );
		}

		/**
		 * Load our shipping class.
		 */
		public function shipping_init() {
			require_once( dirname( __FILE__ ) . '/includes/class-wc-royalmail-privacy.php' );

			if ( version_compare( WC_VERSION, '2.6.0', '<' ) ) {
				include_once( dirname( __FILE__ ) . '/includes/class-wc-shipping-royalmail-deprecated.php' );
			} else {
				include_once( dirname( __FILE__ ) . '/includes/class-wc-shipping-royalmail.php' );
			}
		}

		/**
		 * Add our shipping method to woocommerce.
		 *
		 * @param array $methods Shipping methods.
		 *
		 * @return array Shipping methods.
		 */
		public function shipping_methods( $methods ) {
			if ( version_compare( WC_VERSION, '2.6.0', '<' ) ) {
				$methods[] = 'WC_Shipping_Royalmail';
			} else {
				$methods['royal_mail'] = 'WC_Shipping_Royalmail';
			}

			return $methods;
		}

		/**
		 * Checks the plugin version.
		 *
		 * @since 2.5.0
		 * @version 2.5.0
		 *
		 * @return bool
		 */
		public function maybe_install() {
			// Only need to do this for versions less than 2.5.0 to migrate
			// settings to shipping zone instance.
			$doing_ajax = defined( 'DOING_AJAX' ) && DOING_AJAX;
			if ( ! $doing_ajax
			     && ! defined( 'IFRAME_REQUEST' )
			     && version_compare( WC_VERSION, '2.6.0', '>=' )
			     && version_compare( get_option( 'wc_royal_mail_version' ), '2.5.0', '<' ) ) {

				$this->install();

			}

			return true;
		}

		/**
		 * Update/migration script.
		 *
		 * @since 2.5.0
		 * @version 2.5.0
		 */
		public function install() {
			// Get all saved settings and cache it.
			$royal_mail_settings = get_option( 'woocommerce_royal_mail_settings', false );

			// If settings exists.
			if ( $royal_mail_settings ) {
				global $wpdb;

				// Unset un-needed settings.
				unset( $royal_mail_settings['enabled'] );
				unset( $royal_mail_settings['availability'] );
				unset( $royal_mail_settings['countries'] );

				// First add it to the "rest of the world" zone when no Royal Mail
				// instance.
				if ( ! $this->is_zone_has_royal_mail( 0 ) ) {
					$wpdb->query( $wpdb->prepare( "INSERT INTO {$wpdb->prefix}woocommerce_shipping_zone_methods ( zone_id, method_id, method_order, is_enabled ) VALUES ( %d, %s, %d, %d )", 0, 'royal_mail', 1, 1 ) );
					// Add settings to the newly created instance to options table.
					$instance = $wpdb->insert_id;
					add_option( 'woocommerce_royal_mail_' . $instance . '_settings', $royal_mail_settings );
				}
				update_option( 'woocommerce_royal_mail_show_upgrade_notice', 'yes' );
			}
			update_option( 'wc_royal_mail_version', $this->version );
		}

		/**
		 * Show the user a notice for plugin updates
		 *
		 * @since 2.5.0
		 */
		public function upgrade_notice() {
			$show_notice = get_option( 'woocommerce_royal_mail_show_upgrade_notice' );

			if ( 'yes' !== $show_notice ) {
				return;
			}

			$query_args = array(
				'page' => 'wc-settings',
				'tab'  => 'shipping',
			);

			$zones_admin_url = add_query_arg( $query_args, get_admin_url() . 'admin.php' );
			?>
			<div class="notice notice-success is-dismissible wc-royal-mail-notice">
				<p>
				<?php
				echo sprintf(
					/* translators: 1) opening anchor tag 2) closing anchor tag */
					__( 'Royal Mail now supports shipping zones. The zone settings were added to a new Royal Mail method on the "Rest of the World" Zone. See the zones %1$shere%2$s ', 'woocommerce-shipping-royal-mail' ),
					'<a href="' . $zones_admin_url . '">',
					'</a>'
				);
				?>
				</p>
			</div>

			<script type="application/javascript">
				jQuery( '.notice.wc-royal-mail-notice' ).on( 'click', '.notice-dismiss', function () {
					wp.ajax.post('royal_mail_dismiss_upgrade_notice');
				});
			</script>
			<?php
		}

		/**
		 * Turn of the dismisable upgrade notice.
		 *
		 * @since 2.5.0
		 * @version 2.5.0
		 */
		public function dismiss_upgrade_notice() {
			update_option( 'woocommerce_royal_mail_show_upgrade_notice', 'no' );
		}

		/**
		 * Helper method to check whether given zone_id has royal_mail method instance.
		 *
		 * @since 2.5.0
		 * @version 2.5.0
		 *
		 * @param int $zone_id Zone ID.
		 *
		 * @return bool True if given zone_id has royal_mail method instance.
		 */
		public function is_zone_has_royal_mail( $zone_id ) {
			global $wpdb;

			return (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(instance_id) FROM {$wpdb->prefix}woocommerce_shipping_zone_methods WHERE method_id = 'royal_mail' AND zone_id = %d", $zone_id ) ) > 0;
		}
	}

	new WC_RoyalMail();
}
