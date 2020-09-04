<div class="woocommerce-shipping-debug-info-container">
	<div>
		<?php echo esc_html( sprintf( __( '%s debug mode is on - to hide these messages, turn debug mode off in the settings.', 'woocommerce-shipping-usps' ), $service_name ) ); ?>
	</div>
	<div class="woocommerce-shipping-debug-info-accordion">
		<h1>
			<?php echo esc_html( sprintf( __( '%s debug info', 'woocommerce-shipping-usps' ), $service_name ) ); ?>
		</h1>
		<div>
			<h2>Request</h2>
			<pre><?php echo esc_html( $request ); ?></pre>
			<h2>Response</h2>
			<pre><?php echo esc_html( $response ); ?></pre>
			<h2>Debug notes</h2>
			<div>
				<?php
				foreach ( $notes as $note ) {
					echo '<pre>' . esc_html( $note ) . '</pre>';
				}
				?>
			</div>
		</div>
	</div>
</div>