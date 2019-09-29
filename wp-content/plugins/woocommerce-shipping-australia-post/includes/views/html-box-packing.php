<?php
/**
 * Template to render custom boxes input fields
 *
 * @package WC_Shipping_Australia_Post
 */
?>
<tr valign="top" id="packing_options">
	<th scope="row" class="titledesc"><?php esc_html_e( 'Box Sizes', 'woocommerce-shipping-australia-post' ); ?></th>
	<td class="forminp">
		<style type="text/css">
			.australia_post_boxes td, .australia_post_services td {
				vertical-align: middle;
				padding: 4px 7px;
			}

			.australia_post_boxes th, .australia_post_services th {
				vertical-align: middle;
				padding: 9px 7px;
			}

			.australia_post_boxes td input {
				margin-right: 4px;
			}

			.australia_post_boxes .check-column {
				vertical-align: middle;
				text-align: left;
				padding: 0 7px;
			}

			.australia_post_services th.sort {
				width: 16px;
			}

			.australia_post_services td.sort {
				cursor: move;
				width: 16px;
				padding: 0 16px;
				background: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAgAAAAICAYAAADED76LAAAAHUlEQVQYV2O8f//+fwY8gJGgAny6QXKETRgEVgAAXxAVsa5Xr3QAAAAASUVORK5CYII=) no-repeat center;
			}
		</style>
		<table class="australia_post_boxes widefat">
			<thead>
			<tr>
				<th class="check-column"><input type="checkbox"/></th>
				<th><?php esc_html_e( 'Name', 'woocommerce-shipping-australia-post' ); ?></th>
				<th><?php esc_html_e( 'Outer Length', 'woocommerce-shipping-australia-post' ); ?></th>
				<th><?php esc_html_e( 'Outer Width', 'woocommerce-shipping-australia-post' ); ?></th>
				<th><?php esc_html_e( 'Outer Height', 'woocommerce-shipping-australia-post' ); ?></th>
				<th><?php esc_html_e( 'Inner Length', 'woocommerce-shipping-australia-post' ); ?></th>
				<th><?php esc_html_e( 'Inner Width', 'woocommerce-shipping-australia-post' ); ?></th>
				<th><?php esc_html_e( 'Inner Height', 'woocommerce-shipping-australia-post' ); ?></th>
				<th><?php esc_html_e( 'Weight of box', 'woocommerce-shipping-australia-post' ); ?></th>
				<th><?php esc_html_e( 'Max Weight', 'woocommerce-shipping-australia-post' ); ?></th>
				<th><?php esc_html_e( 'Type', 'woocommerce-shipping-australia-post' ); ?></th>
				<th><?php esc_html_e( 'Enabled', 'woocommerce-shipping-australia-post' ); ?></th>

			</tr>
			</thead>
			<tfoot>
			<tr>
				<th colspan="3">
					<a href="#" class="button plus insert"><?php esc_html_e( 'Add Box', 'woocommerce-shipping-australia-post' ); ?></a>
					<a href="#" class="button minus remove"><?php esc_html_e( 'Remove selected box(es)', 'woocommerce-shipping-australia-post' ); ?></a>
				</th>
				<th colspan="7">
					<small
						class="description"><?php esc_html_e( 'Items will be packed into these boxes depending based on item dimensions and volume. Outer dimensions will be passed to australia Post, whereas inner dimensions will be used for packing. Items not fitting into boxes will be packed individually.', 'woocommerce-shipping-australia-post' ); ?></small>
				</th>
			</tr>
			</tfoot>
			<tbody id="rates">
			<?php
			$default_box_count = count( $this->default_boxes );
			$i                 = 0;

			foreach ( $this->get_all_boxes() as $key => $box ) {
				$dim_label    = 'envelope' === $box['type'] ? 'mm' : 'cm';
				$weight_label = 'envelope' === $box['type'] ? 'g' : 'kg';
				$default_box  = $i < $default_box_count;
				$i++;
				?>
				<tr>
					<td class="check-column">
						<?php if ( ! $default_box ) { ?>
							<input title="select" type="checkbox"/>
						<?php } ?>
					</td>
					<td>
					<?php
					if ( $default_box ) {
						echo esc_html( $box['name'] );
					} else {
						?>
						<input title="<?php esc_attr_e( 'Name', 'woocommerce-shipping-australia-post' ); ?>" placeholder="<?php esc_attr_e( 'Name', 'woocommerce-shipping-australia-post' ); ?>" type="text" size="10" name="boxes_name[<?php echo esc_attr( $key ); ?>]"
							value="<?php echo esc_attr( $box['name'] ); ?>"/>
					<?php } ?>
					</td>

					<td>
						<label class="dimension">
							<input <?php readonly( $default_box ); ?> type="text" size="5" name="boxes_outer_length[<?php echo esc_attr( $key ); ?>]"
								value="<?php echo esc_attr( $box['outer_length'] ); ?>"/>
							<span><?php echo esc_html( $dim_label ); ?></span>
						</label>
					</td>
					<td>
						<label class="dimension">
							<input <?php readonly( $default_box ); ?> type="text" size="5" name="boxes_outer_width[<?php echo esc_attr( $key ); ?>]"
								value="<?php echo esc_attr( $box['outer_width'] ); ?>"/>
							<span><?php echo esc_html( $dim_label ); ?></span>
						</label>
					</td>
					<td>
						<label class="dimension">
							<input <?php readonly( $default_box ); ?> type="text" size="5" name="boxes_outer_height[<?php echo esc_attr( $key ); ?>]"
								value="<?php echo esc_attr( $box['outer_height'] ); ?>"/>
							<span><?php echo esc_html( $dim_label ); ?></span>
						</label>
					</td>
					<td>
						<label class="dimension">
							<input <?php readonly( $default_box ); ?> type="text" size="5" name="boxes_inner_length[<?php echo esc_attr( $key ); ?>]"
								value="<?php echo esc_attr( $box['inner_length'] ); ?>"/>
							<span><?php echo esc_html( $dim_label ); ?></span>
						</label>
					</td>
					<td>
						<label class="dimension">
							<input <?php readonly( $default_box ); ?> type="text" size="5" name="boxes_inner_width[<?php echo esc_attr( $key ); ?>]"
								value="<?php echo esc_attr( $box['inner_width'] ); ?>"/>
							<span><?php echo esc_html( $dim_label ); ?></span>
						</label>
					</td>
					<td>
						<label class="dimension">
							<input <?php readonly( $default_box ); ?> type="text" size="5" name="boxes_inner_height[<?php echo esc_attr( $key ); ?>]"
								value="<?php echo esc_attr( $box['inner_height'] ); ?>"/>
							<span><?php echo esc_html( $dim_label ); ?></span>
						</label>
					</td>
					<td>
						<label class="weight">
							<input <?php readonly( $default_box ); ?> type="text" size="5" name="boxes_box_weight[<?php echo esc_attr( $key ); ?>]"
								value="<?php echo esc_attr( $box['box_weight'] ); ?>"/>
							<span><?php echo esc_html( $weight_label ); ?></span>
						</label>
					</td>
					<td>
						<label class="weight">
							<input <?php readonly( $default_box ); ?> type="text" size="5" name="boxes_max_weight[<?php echo esc_attr( $key ); ?>]"
								value="<?php echo esc_attr( $box['max_weight'] ); ?>" placeholder="22"/>
							<span><?php echo esc_html( $weight_label ); ?></span>
						</label>
					</td>
					<td>
						<select title="<?php esc_attr_e( 'Type', 'woocommerce-shipping-australia-post' ); ?>" <?php disabled( $default_box ); ?> name="boxes_type[<?php echo esc_attr( $key ); ?>]">
							<option value="box" <?php selected( $box['type'], 'box' ); ?>><?php esc_html_e( 'Box', 'woocommerce-shipping-australia-post' ); ?></option>
							<option value="envelope" <?php selected( $box['type'], 'envelope' ); ?>><?php esc_html_e( 'Envelope', 'woocommerce-shipping-australia-post' ); ?></option>
							<option value="packet" <?php selected( $box['type'], 'packet' ); ?>><?php esc_html_e( 'Packet', 'woocommerce-shipping-australia-post' ); ?></option>
							<option value="tube" <?php selected( $box['type'], 'tube' ); ?>><?php esc_html_e( 'Tube', 'woocommerce-shipping-australia-post' ); ?></option>
						</select>
					</td>
					<td>
						<input type="checkbox" title="<?php esc_attr_e( 'Enabled', 'woocommerce-shipping-australia-post' ); ?>" name="boxes_enabled[<?php echo esc_attr( $key ); ?>]" <?php checked( ! isset( $box['enabled'] ) || $box['enabled'], true ); ?> />
					</td>
				</tr>

				<?php
			}
			?>
			</tbody>
		</table>
		<script type="text/javascript">

			jQuery( function () {

				jQuery( '#woocommerce_australia_post_packing_method' ).change( function () {

					if ( jQuery( this ).val() == 'box_packing' ) {
						jQuery( '#packing_options' ).show();
					} else {
						jQuery( '#packing_options' ).hide();
					}

					if ( jQuery( this ).val() == 'weight' ) {
						jQuery( '#woocommerce_australia_post_max_weight' ).closest( 'tr' ).show();
					} else {
						jQuery( '#woocommerce_australia_post_max_weight' ).closest( 'tr' ).hide();
					}

				} ).change();

				jQuery( '.australia_post_boxes .insert' ).click( function () {
					var $tbody = jQuery( '.australia_post_boxes' ).find( 'tbody' );
					var size = $tbody.find( 'tr' ).size();
					var code = '<tr class="new">\
							<td class="check-column"><input type="checkbox" /></td>\
							<td><input title="<?php esc_attr_e( 'Name', 'woocommerce-shipping-australia-post' ); ?>" placeholder="<?php esc_attr_e( 'Name', 'woocommerce-shipping-australia-post' ); ?>" size="10" name="boxes_name[' + size + ']" /></td>\
							<td><label class="dimension"><input type="text" size="5" name="boxes_outer_length[' + size + ']" /><span>cm</span></label></td>\
							<td><label class="dimension"><input type="text" size="5" name="boxes_outer_width[' + size + ']" /><span>cm</span></label></td>\
							<td><label class="dimension"><input type="text" size="5" name="boxes_outer_height[' + size + ']" /><span>cm</span></label></td>\
							<td><label class="dimension"><input type="text" size="5" name="boxes_inner_length[' + size + ']" /><span>cm</span></label></td>\
							<td><label class="dimension"><input type="text" size="5" name="boxes_inner_width[' + size + ']" /><span>cm</span></label></td>\
							<td><label class="dimension"><input type="text" size="5" name="boxes_inner_height[' + size + ']" /><span>cm</span></label></td>\
							<td><label class="weight"><input type="text" size="5" name="boxes_box_weight[' + size + ']" /><span>kg</span></label></td>\
							<td><label class="weight"><input type="text" size="5" name="boxes_max_weight[' + size + ']" /><span>kg</span></label></td>\
							<td><select title="<?php esc_attr_e( 'Type', 'woocommerce-shipping-australia-post' ); ?>" name="boxes_type[' + size + ']">\
								<option value="box" selected="selected"><?php esc_html_e( 'Box', 'woocommerce-shipping-australia-post' ); ?></option>\
								<option value="envelope"><?php esc_html_e( 'Envelope', 'woocommerce-shipping-australia-post' ); ?></option>\
								<option value="packet"><?php esc_html_e( 'Packet', 'woocommerce-shipping-australia-post' ); ?></option>\
								<option value="tube"><?php esc_html_e( 'Tube', 'woocommerce-shipping-australia-post' ); ?></option>\
								</select></td>\
							<td><input type="checkbox" title="<?php esc_attr_e( 'Name', 'woocommerce-shipping-australia-post' ); ?>" name="boxes_enabled[' + size + ']" checked="checked" /></td> \
						</tr>';

					$tbody.append( code );

					return false;
				} );

				jQuery( '.australia_post_boxes .remove' ).click( function () {
					var $tbody = jQuery( '.australia_post_boxes' ).find( 'tbody' );

					$tbody.find( '.check-column input:checked' ).each( function () {
						jQuery( this ).closest( 'tr' ).hide().find( 'input' ).val( '' );
					} );

					return false;
				} );

				// Ordering
				jQuery( '.australia_post_services tbody' ).sortable( {
					items: 'tr',
					cursor: 'move',
					axis: 'y',
					handle: '.sort',
					scrollSensitivity: 40,
					forcePlaceholderSize: true,
					helper: 'clone',
					opacity: 0.65,
					placeholder: 'wc-metabox-sortable-placeholder',
					start: function ( event, ui ) {
						ui.item.css( 'background-color', '#f6f6f6' );
					},
					stop: function ( event, ui ) {
						ui.item.removeAttr( 'style' );
						australia_post_services_row_indexes();
					}
				} );

				jQuery( '.australia_post_boxes select' ).each( function () {
					jQuery( this ).data('prev', jQuery( this ).val());
				});

				jQuery( '.australia_post_boxes' ).on( 'change', 'select', function() {
					var parentRow = jQuery( this ).closest( 'tr' ),
						$this = jQuery( this ),
						prev = $this( 'prev' ),
						current = $this.val();

					// need to convert measurements to mm and g for letter type
					if ( 'envelope' === current && 'envelope' !== prev ) {
						parentRow.find('label.dimension span').html( 'mm' );
						parentRow.find('label.weight span').html( 'g' );

						// convert units
						parentRow.find('label.dimension input').each( function( index, textBox ) {
							var cmToMm = jQuery( textBox ).val() * 10;
							jQuery( textBox ).val(  cmToMm );
							return true;
						});
						parentRow.find( 'label.weight input' ).each( function( index, textBox ) {
							var kgToG = jQuery( textBox ).val() * 1000;
							jQuery( textBox ).val( kgToG );
							return true;
						});

					} else if ( 'envelope' === prev && 'envelope' !== current ) {
						parentRow.find('label.dimension span').html( 'cm' );
						parentRow.find('label.weight span').html( 'kg' );

						// convert units
						parentRow.find('label.dimension input').each( function( index, textBox ) {
							var mmToCm = jQuery( textBox ).val() / 10;
							jQuery( textBox ).val( mmToCm );
							return true;
						});
						parentRow.find('label.weight input').each( function( index, textBox ) {
							var gToKg = jQuery( textBox ).val() / 1000;
							jQuery( textBox ).val( gToKg );
							return true;
						});
					}
					$this.data( 'prev', current );
				});

				function australia_post_services_row_indexes() {
					jQuery( '.australia_post_services tbody tr' ).each( function ( index, el ) {
						jQuery( 'input.order', el ).val( parseInt( jQuery( el ).index( '.australia_post_services tr' ) ) );
					} );
				};

			} );

		</script>
	</td>
</tr>
