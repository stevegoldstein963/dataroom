<?php
/**
 * Menu editor handler
 *
 * @package Liquid_Menu_Icons
 * @author  Dzikri Aziz <kvcrvt@gmail.com>
 */


/**
 * Nav menu admin
 */
final class Liquid_Menu_Icons_Picker {

	/**
	 * Initialize class
	 *
	 * @since 0.1.0
	 */
	public static function init() {
		add_action( 'load-nav-menus.php', array( __CLASS__, '_load_nav_menus' ) );
		add_filter( 'wp_nav_menu_item_custom_fields', array( __CLASS__, '_fields' ), 10, 4 );
		add_filter( 'manage_nav-menus_columns', array( __CLASS__, '_columns' ), 99 );
		add_action( 'wp_update_nav_menu_item', array( __CLASS__, '_save' ), 10, 3 );
		add_filter( 'icon_picker_type_props', array( __CLASS__, '_add_extra_type_props_data' ), 10, 3 );

		if ( ! version_compare( get_bloginfo( 'version' ), '5.4', '>=' ) ) {
			add_filter(
				'wp_edit_nav_menu_walker', function() {
					return 'Menu_Item_Custom_Fields_Walker';
				}, 99
			);
		}
	}


	/**
	 * Load Icon Picker
	 *
	 * @since   0.9.0
	 * @wp_hook action load-nav-menus.php
	 */
	public static function _load_nav_menus() {
		Icon_Picker::instance()->load();

		add_action( 'print_media_templates', array( __CLASS__, '_media_templates' ) );
	}


	/**
	 * Get menu item setting fields
	 *
	 * @since  0.9.0
	 * @access protected
	 * @param  array     $meta Menu item meta value.
	 * @return array
	 */
	protected static function _get_menu_item_fields( $meta ) {
		$fa_icon       = sprintf( '%s-%s', $meta['type'], $meta['icon'] );
		$font_awesome5 = font_awesome_backward_compatible();

		if ( array_key_exists( $fa_icon, $font_awesome5 ) ) {
			$fa5_icon     = $font_awesome5[ $fa_icon ];
			$fa5_class    = explode( ' ', $fa5_icon );
			$type         = reset( $fa5_class );
			$icon         = end( $fa5_class );
			$meta['icon'] = sprintf( '%s %s', $type, $icon );
		}

		$fields = array_merge(
			array(
				array(
					'id'    => 'type',
					'label' => __( 'Type', 'aihub' ),
					'value' => $meta['type'],
				),
				array(
					'id'    => 'icon',
					'label' => __( 'Icon', 'aihub' ),
					'value' => $meta['icon'],
				),
			),
			Liquid_Menu_Icons_Settings::get_settings_fields( $meta )
		);

		return $fields;
	}


	/**
	 * Print fields
	 *
	 * @since   0.1.0
	 * @access  protected
	 * @uses    add_action() Calls 'menu_icons_before_fields' hook
	 * @uses    add_action() Calls 'menu_icons_after_fields' hook
	 * @wp_hook action       menu_item_custom_fields
	 *
	 * @param object $item  Menu item data object.
	 * @param int    $depth Nav menu depth.
	 * @param array  $args  Menu item args.
	 * @param int    $id    Nav menu ID.
	 *
	 * @return string Form fields
	 */
	public static function _fields( $id, $item, $depth, $args ) {
		$input_id      = sprintf( 'menu-icons-%d', $item->ID );
		$input_name    = sprintf( 'menu-icons[%d]', $item->ID );
		$menu_settings = Liquid_Menu_Icons_Settings::get_menu_settings( Liquid_Menu_Icons_Settings::get_current_menu_id() );
		$meta          = Liquid_Menu_Icons_Meta::get( $item->ID, $menu_settings );
		$fields        = self::_get_menu_item_fields( $meta );
		?>
			<div class="field-icon description-wide menu-icons-wrap" data-id="<?php echo json_encode( $item->ID ); ?>">
				<?php
					/**
					 * Allow plugins/themes to inject HTML before menu icons' fields
					 *
					 * @param object $item  Menu item data object.
					 * @param int    $depth Nav menu depth.
					 * @param array  $args  Menu item args.
					 * @param int    $id    Nav menu ID.
					 *
					 */
					do_action( 'menu_icons_before_fields', $item, $depth, $args, $id );
				?>
				<p class="description submitbox">
					<label><?php esc_html_e( 'Icon:', 'aihub' ) ?></label>
					<?php printf( '<a class="_select button button-large" style="border-width:1px;">%s</a>', esc_html__( 'Select', 'aihub' ) ); ?>
					<?php printf( '<a class="_remove submitdelete button button-large hidden" style="border:1px solid !important;color: #d63638">%s</a>', esc_html__( 'Remove', 'aihub' ) ); ?>
				</p>
				<div class="_settings hidden">
					<?php
					foreach ( $fields as $field ) {
						printf(
							'<label>%1$s: <input type="text" name="%2$s" class="_mi-%3$s" value="%4$s" /></label><br />',
							esc_html( $field['label'] ),
							esc_attr( "{$input_name}[{$field['id']}]" ),
							esc_attr( $field['id'] ),
							esc_attr( $field['value'] )
						);
					}

					// The fields below will not be saved. They're only used for the preview.
					printf( '<input type="hidden" class="_mi-url" value="%s" />', esc_attr( $meta['url'] ) );
					?>
				</div>
				<?php
					/**
					 * Allow plugins/themes to inject HTML after menu icons' fields
					 *
					 * @param object $item  Menu item data object.
					 * @param int    $depth Nav menu depth.
					 * @param array  $args  Menu item args.
					 * @param int    $id    Nav menu ID.
					 *
					 */
					do_action( 'menu_icons_after_fields', $item, $depth, $args, $id );
				?>
			</div>
		<?php
	}


	/**
	 * Add our field to the screen options toggle
	 *
	 * @since   0.1.0
	 * @access  private
	 * @wp_hook action  manage_nav-menus_columns
	 * @link    http://codex.wordpress.org/Plugin_API/Filter_Reference/manage_posts_columns
	 *
	 * @param array $columns Menu item columns
	 *
	 * @return array
	 */
	public static function _columns( $columns ) {
		$columns['icon'] = __( 'Icon', 'aihub' );

		return $columns;
	}


	/**
	 * Save menu item's icons metadata
	 *
	 * @since   0.1.0
	 * @access  protected
	 * @wp_hook action    wp_update_nav_menu_item
	 * @link    http://codex.wordpress.org/Plugin_API/Action_Reference/wp_update_nav_menu_item
	 *
	 * @param int   $menu_id         Nav menu ID.
	 * @param int   $menu_item_db_id Menu item ID.
	 * @param array $menu_item_args  Menu item data.
	 */
	public static function _save( $menu_id, $menu_item_db_id, $menu_item_args ) {
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return;
		}

		if ( ! function_exists( 'get_current_screen' ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( ! $screen instanceof WP_Screen || 'nav-menus' !== $screen->id ) {
			return;
		}

		check_admin_referer( 'update-nav_menu', 'update-nav-menu-nonce' );

		// Sanitize
		if ( ! empty( $_POST['menu-icons'][ $menu_item_db_id ] ) ) {
			$value = array_map(
				'sanitize_text_field',
				wp_unslash( (array) $_POST['menu-icons'][ $menu_item_db_id ] )
			);
		} else {
			$value = array();
		}

		Liquid_Menu_Icons_Meta::update( $menu_item_db_id, $value );
	}


	/**
	 * Get and print media templates from all types
	 *
	 * @since   0.2.0
	 * @since   0.9.0  Deprecate menu_icons_media_templates filter.
	 * @wp_hook action print_media_templates
	 */
	public static function _media_templates() {
		$id_prefix = 'tmpl-menu-icons';

		// Deprecated.
		$templates = apply_filters( 'menu_icons_media_templates', array() );

		if ( ! empty( $templates ) ) {
			if ( WP_DEBUG ) {
				_deprecated_function( 'menu_icons_media_templates', '0.9.0', 'menu_icons_js_templates' );
			}

			foreach ( $templates as $key => $template ) {
				$id = sprintf( '%s-%s', $id_prefix, $key );
				self::_print_tempate( $id, $template );
			}
		}

		require_once dirname( __FILE__ ) . '/media-template.php';
	}


	/**
	 * Print media template
	 *
	 * @since 0.2.0
	 * @param string $id       Template ID.
	 * @param string $template Media template HTML.
	 */
	protected static function _print_tempate( $id, $template ) {
		?>
			<script type="text/html" id="<?php echo esc_attr( $id ) ?>">
				<?php echo $template; // xss ok ?>
			</script>
		<?php
	}


	/**
	 * Add extra icon type properties data
	 *
	 * @since   0.9.0
	 * @wp_hook action icon_picker_type_props
	 *
	 * @param   array            $props Icon type properties.
	 * @param   string           $id    Icon type ID.
	 * @param   Icon_Picker_Type $type  Icon_Picker_Type object.
	 *
	 * @return  array
	 */
	public static function _add_extra_type_props_data( $props, $id, $type ) {
		$settings_fields = array(
			'position',
		);

		if ( 'Font' === $props['controller'] ) {
			$settings_fields[] = 'font_size';
		}

		switch ( $id ) {
			case 'image':
				$settings_fields[] = 'image_size';
				break;
			case 'svg':
				$settings_fields[] = 'svg_width';
				break;
		}

		$props['data']['settingsFields'] = $settings_fields;

		return $props;
	}
}
