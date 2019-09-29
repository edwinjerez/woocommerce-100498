<?php
/**
 * WC_Shipping_Debug class.
 */

class WCShippingDebug {
	/**
	 * Array of note strings to be displayed in notes section of debug notice.
	 *
	 * @var array
	 */
	private $notes;

	/**
	 * API request XML string.
	 *
	 * @var string
	 */
	private $request;

	/**
	 * API response XML string.
	 *
	 * @var string
	 */
	private $response;

	/**
	 * Shipping service name to be displayed in debug notice.
	 *
	 * @var string
	 */
	private $service_name;

	/**
	 * Constructor.
	 *
	 * @param string $service_name Shipping service name to be displayed in debug notice.
	 */
	public function __construct( $service_name ) {
		$this->service_name = $service_name;
		$this->notes        = array();
		$this->request      = '';
		$this->response     = '';
	}

	/**
	 * Enqueue style and script for debug notice.
	 */
	public static function enqueue_resources() {
		if ( self::should_display_debug() ) {
			wp_enqueue_script( 'woocommerce-shipping-debug-viewer-js', plugin_dir_url( __FILE__ ) . 'shipping-debug.js', array( 'jquery-ui-accordion' ) );
			wp_enqueue_style( 'woocommerce-shipping-debug-viewer-style', plugin_dir_url( __FILE__ ) . 'shipping-debug.css' );
		}
	}

	/**
	 * Print all debug info as WC admin notice.
	 */
	public function display() {
		if ( self::should_display_debug() ) {
			$notes        = $this->notes;
			$request      = $this->try_prettify_xml( $this->request );
			$response     = $this->try_prettify_xml( $this->response );
			$service_name = $this->service_name;

			ob_start();
			include( 'html-shipping-debug-notice.php' );
			$notice_html = ob_get_clean();
			wc_add_notice( $notice_html );
		}
	}

	/**
	 * Whether or not debug mode should be displayed.
	 */
	public static function should_display_debug() {
		return ( current_user_can( 'manage_options' ) && ( is_cart() || is_checkout() ) );
	}

	/**
	 * Prettify XML.
	 *
	 * @param string $maybe_xml String to be prettified as XML.
	 *
	 * @return string
	 */
	protected function try_prettify_xml( $maybe_xml ) {
		if ( class_exists( 'DOMDocument' ) ) {
			// Many APIs have info before header, so separate out so they can be parsed.
			$xml_start = strpos( $maybe_xml, '<' );
			$pre_xml   = substr( $maybe_xml, 0, $xml_start );
			$xml       = substr( $maybe_xml, $xml_start );

			// Prettify xml.
			$dom                     = new DOMDocument;
			$dom->preserveWhiteSpace = false;
			$dom->loadXML( $xml );
			$dom->formatOutput = true;
			return $pre_xml . $dom->saveXML();
		}
		return $maybe_xml;
	}

	/**
	 * Add note to notes array to be displayed in notes section of debug notice.
	 *
	 * @param string $note Note text to be added.
	 */
	public function add_note( $note ) {
		array_push( $this->notes, $note );
	}

	/**
	 * Set request XML to be displayed in request section of debug notice.
	 *
	 * @param string $request Request XML string.
	 */
	public function set_request( $request ) {
		$this->request = $request;
	}

	/**
	 * Set response XML to be displayed in response section of debug notice.
	 *
	 * @param string $response Response XML string.
	 */
	public function set_response( $response ) {
		$this->response = $response;
	}
}
