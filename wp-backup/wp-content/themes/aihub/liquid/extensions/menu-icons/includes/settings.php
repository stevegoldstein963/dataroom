<?php

/**
 * Settings
 *
 * @package Liquid_Menu_Icons
 * @author  Dzikri Aziz <kvcrvt@gmail.com>
 */

/**
 * Menu Icons Settings module
 */
final class Liquid_Menu_Icons_Settings {

	const UPDATE_KEY = 'menu-icons-settings-update';

	const RESET_KEY = 'menu-icons-settings-reset';

	const TRANSIENT_KEY = 'menu_icons_message';

	/**
	 * Default setting values
	 *
	 * @since  0.3.0
	 * @var   array
	 * @access protected
	 */
	protected static $defaults = array(
		'global' => array(
			'icon_types' => array( 'akar-icons' ),
		),
	);

	/**
	 * Setting values
	 *
	 * @since  0.3.0
	 * @var   array
	 * @access protected
	 */
	protected static $settings = array();

	/**
	 * Script dependencies
	 *
	 * @since  0.9.0
	 * @access protected
	 * @var    array
	 */
	protected static $script_deps = array( 'jquery' );

	/**
	 * Settings init
	 *
	 * @since 0.3.0
	 */
	public static function init() {
		/**
		 * Allow themes/plugins to override the default settings
		 *
		 * @since 0.9.0
		 *
		 * @param array $default_settings Default settings.
		 */
		self::$defaults = apply_filters( 'menu_icons_settings_defaults', self::$defaults );

		self::$settings = get_option( 'menu-icons', self::$defaults );

		foreach ( self::$settings as $key => &$value ) {
			if ( 'global' === $key ) {
				// Remove unregistered icon types.

				if ( empty( $value['icon_types'] ) ){
					$value['icon_types'] = [ 'akar-icons' ];
				}
				$value['icon_types'] = array_values(
					array_intersect(
						array_keys( Liquid_Menu_Icons::get( 'types' ) ),
						array_filter( (array) $value['icon_types'] )
					)
				);
			} else {
				// Backward-compatibility.
				if ( isset( $value['width'] ) && ! isset( $value['svg_width'] ) ) {
					$value['svg_width'] = $value['width'];
				}

				unset( $value['width'] );
			}
		}

		unset( $value );

		/**
		 * Allow themes/plugins to override the settings
		 *
		 * @since 0.9.0
		 *
		 * @param array $settings Menu Icons settings.
		 */
		self::$settings = apply_filters( 'menu_icons_settings', self::$settings );

		if ( self::is_menu_icons_disabled_for_menu() ) {
			return;
		}

		if ( ! empty( self::$settings['global']['icon_types'] ) ) {
			require_once Liquid_Menu_Icons::get( 'dir' ) . 'includes/picker.php';
			Liquid_Menu_Icons_Picker::init();
			self::$script_deps[] = 'icon-picker';
		}

		add_action( 'load-nav-menus.php', array( __CLASS__, '_load_nav_menus' ), 1 );
		add_action( 'wp_ajax_menu_icons_update_settings', array( __CLASS__, '_ajax_menu_icons_update_settings' ) );
	}

	/**
	 * Check if menu icons is disabled for a menu
	 *
	 * @since 0.8.0
	 *
	 * @param int $menu_id Menu ID. Defaults to current menu being edited.
	 *
	 * @return bool
	 */
	public static function is_menu_icons_disabled_for_menu( $menu_id = 0 ) {
		if ( empty( $menu_id ) ) {
			$menu_id = self::get_current_menu_id();
		}

		// When we're creating a new menu or the recently edited menu
		// could not be found.
		if ( empty( $menu_id ) ) {
			return true;
		}

		$menu_settings = self::get_menu_settings( $menu_id );
		$is_disabled   = ! empty( $menu_settings['disabled'] );

		return $is_disabled;
	}

	/**
	 * Get ID of menu being edited
	 *
	 * @since  0.7.0
	 * @since  0.8.0 Get the recently edited menu from user option.
	 *
	 * @return int
	 */
	public static function get_current_menu_id() {
		global $nav_menu_selected_id;

		if ( ! empty( $nav_menu_selected_id ) ) {
			return $nav_menu_selected_id;
		}

		if ( is_admin() && isset( $_REQUEST['menu'] ) ) {
			$menu_id = absint( $_REQUEST['menu'] );
		} else {
			$menu_id = absint( get_user_option( 'nav_menu_recently_edited' ) );
		}

		return $menu_id;
	}

	/**
	 * Get menu settings
	 *
	 * @since  0.3.0
	 *
	 * @param  int $menu_id
	 *
	 * @return array
	 */
	public static function get_menu_settings( $menu_id ) {
		$menu_settings = self::get( sprintf( 'menu_%d', $menu_id ) );
		$menu_settings = apply_filters( 'menu_icons_menu_settings', $menu_settings, $menu_id );

		if ( ! is_array( $menu_settings ) ) {
			$menu_settings = array();
		}

		return $menu_settings;
	}

	/**
	 * Get setting value
	 *
	 * @since  0.3.0
	 * @return mixed
	 */
	public static function get() {
		$args = func_get_args();

		return kucrut_get_array_value_deep( self::$settings, $args );
	}

	/**
	 * Prepare wp-admin/nav-menus.php page
	 *
	 * @since   0.3.0
	 * @wp_hook action load-nav-menus.php
	 */
	public static function _load_nav_menus() {
		add_action( 'admin_enqueue_scripts', array( __CLASS__, '_enqueue_assets' ), 99 );

		/**
		 * Allow settings meta box to be disabled.
		 *
		 * @since 0.4.0
		 *
		 * @param bool $disabled Defaults to FALSE.
		 */
		$settings_disabled = apply_filters( 'menu_icons_disable_settings', false );
		if ( true === $settings_disabled ) {
			return;
		}

		self::_maybe_update_settings();
		self::_add_settings_meta_box();

		add_action( 'admin_notices', array( __CLASS__, '_admin_notices' ) );
	}

	/**
	 * Update settings
	 *
	 * @since 0.3.0
	 */
	public static function _maybe_update_settings() {
		if ( ! empty( $_POST['menu-icons']['settings'] ) ) {
			check_admin_referer( self::UPDATE_KEY, self::UPDATE_KEY );

			$redirect_url = self::_update_settings( $_POST['menu-icons']['settings'] ); // Input var okay.
			wp_redirect( $redirect_url );
		} elseif ( ! empty( $_REQUEST[ self::RESET_KEY ] ) ) {
			check_admin_referer( self::RESET_KEY, self::RESET_KEY );
			wp_redirect( self::_reset_settings() );
		}
	}

	/**
	 * Update settings
	 *
	 * @since  0.7.0
	 * @access protected
	 *
	 * @param  array $values Settings values.
	 *
	 * @return string    Redirect URL.
	 */
	protected static function _update_settings( $values ) {
		update_option(
			'menu-icons',
			wp_parse_args(
				kucrut_validate( $values ),
				self::$settings
			)
		);
		set_transient( self::TRANSIENT_KEY, 'updated', 30 );

		$redirect_url = remove_query_arg(
			array( 'menu-icons-reset' ),
			wp_get_referer()
		);

		return $redirect_url;
	}

	/**
	 * Reset settings
	 *
	 * @since  0.7.0
	 * @access protected
	 * @return string    Redirect URL.
	 */
	protected static function _reset_settings() {
		delete_option( 'menu-icons' );
		set_transient( self::TRANSIENT_KEY, 'reset', 30 );

		$redirect_url = remove_query_arg(
			array( self::RESET_KEY, 'menu-icons-updated' ),
			wp_get_referer()
		);

		return $redirect_url;
	}

	/**
	 * Settings meta box
	 *
	 * @since  0.3.0
	 * @access private
	 */
	private static function _add_settings_meta_box() {
		add_meta_box(
			'menu-icons-settings',
			__( 'Menu Icons Settings', 'aihub' ),
			array( __CLASS__, '_meta_box' ),
			'nav-menus',
			'side',
			'low',
			array()
		);
	}

	/**
	 * Update settings via ajax
	 *
	 * @since   0.7.0
	 * @wp_hook action wp_ajax_menu_icons_update_settings
	 */
	public static function _ajax_menu_icons_update_settings() {
		check_ajax_referer( self::UPDATE_KEY, self::UPDATE_KEY );

		if ( empty( $_POST['menu-icons']['settings'] ) ) {
			wp_send_json_error();
		}

		$redirect_url = self::_update_settings( $_POST['menu-icons']['settings'] ); // Input var okay.
		wp_send_json_success( array( 'redirectUrl' => $redirect_url ) );
	}

	/**
	 * Print admin notices
	 *
	 * @since   0.3.0
	 * @wp_hook action admin_notices
	 */
	public static function _admin_notices() {
		$messages = array(
			'updated' => __( '<strong>Menu Icons Settings</strong> have been successfully updated.', 'aihub' ),
			'reset'   => __( '<strong>Menu Icons Settings</strong> have been successfully reset.', 'aihub' ),
		);

		$message_type = get_transient( self::TRANSIENT_KEY );

		if ( ! empty( $message_type ) && ! empty( $messages[ $message_type ] ) ) {
			printf(
				'<div class="updated notice is-dismissible"><p>%s</p></div>',
				wp_kses( $messages[ $message_type ], array( 'strong' => true ) )
			);
		}

		delete_transient( self::TRANSIENT_KEY );
	}

	/**
	 * Settings meta box
	 *
	 * @since 0.3.0
	 */
	public static function _meta_box() {
		?>
		<div class="taxonomydiv">
			<ul id="menu-icons-settings-tabs" class="taxonomy-tabs add-menu-item-tabs hide-if-no-js">
				<?php foreach ( self::get_fields() as $section ) : ?>
					<?php
					printf(
						'<li><a href="#" title="%s" class="mi-settings-nav-tab" data-type="menu-icons-settings-%s">%s</a></li>',
						esc_attr( $section['description'] ),
						esc_attr( $section['id'] ),
						esc_html( $section['title'] )
					);
					?>
				<?php endforeach; ?>
			</ul>
			<?php foreach ( self::_get_fields() as $section_index => $section ) : ?>
				<div id="menu-icons-settings-<?php echo esc_attr( $section['id'] ) ?>"
					 class="tabs-panel _<?php echo esc_attr( $section_index ) ?>">
					<h4 class="hide-if-js"><?php echo esc_html( $section['title'] ) ?></h4>
					<?php foreach ( $section['fields'] as $field ) : ?>
						<div class="_field">
							<?php
							printf(
								'<label for="%s" class="_main">%s</label>',
								esc_attr( $field->id ),
								esc_html( $field->label )
							);
							// Help text.
							if ( $field->help_text ) :
								printf( '<i>%s</i>', esc_html( $field->help_text ) );
							endif;

							$field->render();
							?>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endforeach; ?>
		</div>
		<p class="submitbox button-controls">
			<?php wp_nonce_field( self::UPDATE_KEY, self::UPDATE_KEY ) ?>
			<span class="list-controls">
					<?php
					printf(
						'<a href="%s" title="%s" class="select-all submitdelete">%s</a>',
						esc_url(
							wp_nonce_url(
								admin_url( '/nav-menus.php' ),
								self::RESET_KEY,
								self::RESET_KEY
							)
						),
						esc_attr__( 'Discard all changes and reset to default state', 'aihub' ),
						esc_html__( 'Reset', 'aihub' )
					);
					?>
				</span>

			<span class="add-to-menu">
					<span class="spinner"></span>
				<?php
				submit_button(
					__( 'Save Settings', 'aihub' ),
					'secondary',
					'menu-icons-settings-save',
					false
				);
				?>
				</span>
		</p>
		<?php
	}

	/**
	 * Get settings sections
	 *
	 * @since  0.3.0
	 * @uses   apply_filters() Calls 'menu_icons_settings_sections'.
	 * @return array
	 */
	public static function get_fields() {
		$menu_id    = self::get_current_menu_id();
		$icon_types = wp_list_pluck( Liquid_Menu_Icons::get( 'types' ), 'name' );

		asort( $icon_types );

		$sections = array(
			'global' => array(
				'id'          => 'global',
				'title'       => __( 'Global', 'aihub' ),
				'description' => __( 'Global settings', 'aihub' ),
				'fields'      => array(
					array(
						'id'      => 'icon_types',
						'type'    => 'checkbox',
						'label'   => __( 'Icon Types', 'aihub' ),
						'choices' => $icon_types,
						'value'   => self::get( 'global', 'icon_types' ),
					),
					array(
						'id'        => 'fa5_extra_icons',
						'type'      => 'textarea',
						'label'     => __( 'FA Custom Icon Classes', 'aihub' ),
						'value'     => self::get( 'global', 'fa5_extra_icons' ),
						'help_text' => '( comma separated icons )',
					),
				),
				'args'        => array(),
			),
		);

		if ( ! empty( $menu_id ) ) {
			$menu_term     = get_term( $menu_id, 'nav_menu' );
			$menu_key      = sprintf( 'menu_%d', $menu_id );
			$menu_settings = self::get_menu_settings( $menu_id );

			$sections['menu'] = array(
				'id'          => $menu_key,
				'title'       => __( 'Current Menu', 'aihub' ),
				'description' => sprintf(
					__( '"%s" menu settings', 'aihub' ),
					apply_filters( 'single_term_title', $menu_term->name )
				),
				'fields'      => self::get_settings_fields( $menu_settings ),
				'args'        => array( 'inline_description' => true ),
			);
		}

		return apply_filters( 'menu_icons_settings_sections', $sections, $menu_id );
	}

	/**
	 * Get settings fields
	 *
	 * @since  0.4.0
	 *
	 * @param  array $values Values to be applied to each field.
	 *
	 * @uses   apply_filters()          Calls 'menu_icons_settings_fields'.
	 * @return array
	 */
	public static function get_settings_fields( array $values = array() ) {
		$fields = array(
			'position'       => array(
				'id'      => 'position',
				'type'    => 'select',
				'label'   => __( 'Position', 'aihub' ),
				'default' => 'start',
				'choices' => array(
					array(
						'value' => 'start',
						'label' => __( 'Start', 'aihub' ),
					),
					array(
						'value' => 'end',
						'label' => __( 'End', 'aihub' ),
					),
				),
			),
			'font_size'      => array(
				'id'          => 'font_size',
				'type'        => 'number',
				'label'       => __( 'Font Size', 'aihub' ),
				'default'     => '',
				'description' => 'em',
                'attributes' => [
                    'min' => 0
                ]
			),
			'svg_width'      => array(
				'id'          => 'svg_width',
				'type'        => 'number',
				'label'       => __( 'SVG Width', 'aihub' ),
				'default'     => '',
				'description' => 'em',
                'attributes' => [
                    'min' => 0
                ]
			),
			'image_size'     => array(
				'id'      => 'image_size',
				'type'    => 'select',
				'label'   => __( 'Image Size', 'aihub' ),
				'default' => 'thumbnail',
				'choices' => kucrut_get_image_sizes(),
			),
		);

		$fields = apply_filters( 'menu_icons_settings_fields', $fields );

		foreach ( $fields as &$field ) {
			if ( isset( $values[ $field['id'] ] ) ) {
				$field['value'] = $values[ $field['id'] ];
			}

			if ( ! isset( $field['value'] ) && isset( $field['default'] ) ) {
				$field['value'] = $field['default'];
			}
		}

		unset( $field );

		return $fields;
	}

	/**
	 * Get processed settings fields
	 *
	 * @since  0.3.0
	 * @access private
	 * @return array
	 */
	private static function _get_fields() {
		if ( ! class_exists( 'Kucrut_Form_Field' ) ) {
			require_once Liquid_Menu_Icons::get( 'dir' ) . 'includes/library/form-fields.php';
		}

		$keys     = array( 'menu-icons', 'settings' );
		$sections = self::get_fields();

		foreach ( $sections as &$section ) {
			$_keys = array_merge( $keys, array( $section['id'] ) );
			$_args = array_merge( array( 'keys' => $_keys ), $section['args'] );

			foreach ( $section['fields'] as &$field ) {
				$field = Kucrut_Form_Field::create( $field, $_args );
			}

			unset( $field );
		}

		unset( $section );

		return $sections;
	}

	/**
	 * Enqueue scripts & styles for Block Icons
	 *
	 * @since   0.3.0
	 * @wp_hook action enqueue_block_assets
	 */
	public static function _enqueue_font_awesome() {
		$url = Liquid_Menu_Icons::get( 'url' );

		wp_register_style(
			'font-awesome-5',
			"{$url}css/fontawesome/css/all.min.css"
		);
	}

	/**
	 * Enqueue scripts & styles for Appearance > Menus page
	 *
	 * @since   0.3.0
	 * @wp_hook action admin_enqueue_scripts
	 */
	public static function _enqueue_assets() {
		$url    = Liquid_Menu_Icons::get( 'url' );
		$suffix = kucrut_get_script_suffix();

		if ( defined( 'MENU_ICONS_SCRIPT_DEBUG' ) && MENU_ICONS_SCRIPT_DEBUG ) {
			$script_url = '//localhost:8081/';
		} else {
			$script_url = $url;
		}

		wp_enqueue_style(
			'menu-icons',
			"{$url}css/admin{$suffix}.css",
			false,
			Liquid_Menu_Icons::VERSION
		);

		wp_enqueue_script(
			'menu-icons',
			"{$script_url}js/admin{$suffix}.js",
			self::$script_deps,
			Liquid_Menu_Icons::VERSION,
			true
		);

		$customizer_url = add_query_arg(
			array(
				'autofocus[section]' => 'custom_css',
				'return'             => admin_url( 'nav-menus.php' ),
			),
			admin_url( 'customize.php' )
		);

		/**
		 * Allow plugins/themes to filter the settings' JS data
		 *
		 * @since 0.9.0
		 *
		 * @param array $js_data JS Data.
		 */
		$menu_current_theme = '';
		$theme              = wp_get_theme();
		if ( ! empty( $theme ) ) {
			if ( is_child_theme() && $theme->parent() ) {
				$menu_current_theme = $theme->parent()->get( 'Name' );
			} else {
				$menu_current_theme = $theme->get( 'Name' );
			}
		}
		$upsell_notices = array();
		$box_data = '<div id="menu-icons-sidebar">';

		if ( ! empty( $upsell_notices ) ) {
			$rand_key                     = array_rand( $upsell_notices );
			$menu_upgrade_hestia_box_text = $upsell_notices[ $rand_key ]['content'];

			$box_data               .= '<div class="nv-upgrade-notice postbox new-card">';
			$box_data               .= wp_kses_post( wpautop( $menu_upgrade_hestia_box_text ) );
			$box_data               .= '<a class="button" href="' . $upsell_notices[ $rand_key ]['url'] . '" target="_blank">' . $upsell_notices[ $rand_key ]['btn_text'] . '</a>';
			$box_data               .= '</div></div>';
		}
		$js_data = apply_filters(
			'menu_icons_settings_js_data',
			array(
				'text'           => array(
					'title'        => __( 'Select Icon', 'aihub' ),
					'select'       => __( 'Select', 'aihub' ),
					'remove'       => __( 'Remove', 'aihub' ),
					'change'       => __( 'Change', 'aihub' ),
					'all'          => __( 'All', 'aihub' ),
					'preview'      => __( 'Preview', 'aihub' ),
					'settingsInfo' => sprintf(
						'<div> %1$s <p>' . esc_html__( 'Please note that the actual look of the icons on the front-end will also be affected by the style of your active theme. You can add your own CSS using %2$s or a plugin such as %3$s if you need to override it.', 'aihub' ) . '</p></div>',
						$box_data,
						sprintf(
							'<a href="%s">%s</a>',
							esc_url( $customizer_url ),
							esc_html__( 'the customizer', 'aihub' )
						),
						'<a target="_blank" href="https://wordpress.org/plugins/advanced-css-editor/">Advanced CSS Editor</a>'
					),
				),
				'settingsFields' => self::get_settings_fields(),
				'activeTypes'    => self::get( 'global', 'icon_types' ),
				'ajaxUrls'       => array(
					'update' => add_query_arg( 'action', 'menu_icons_update_settings', admin_url( '/admin-ajax.php' ) ),
				),
				'menuSettings'   => self::get_menu_settings( self::get_current_menu_id() ),
			)
		);

		wp_localize_script( 'menu-icons', 'menuIcons', $js_data );
	}
}
