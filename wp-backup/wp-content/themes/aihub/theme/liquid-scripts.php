<?php
/**
 * The Asset Manager
 * Enqueue scripts and styles for the frontend
 */

if( ! defined( 'ABSPATH' ) )
	exit; // Exit if accessed directly

class Liquid_Theme_Assets extends Liquid_Base {

    /**
     * Hold data for wa_theme for frontend
     * @var array
     */
    private static $theme_json = array();

	/**
	 * [__construct description]
	 * @method __construct
	 */
    public function __construct() {

        $this->add_action( 'wp_enqueue_scripts', 'register' );
        $this->add_action( 'wp_enqueue_scripts', 'enqueue' );
        $this->add_action( 'wp_enqueue_scripts', 'woo_register' );
        $this->add_action( 'wp_enqueue_scripts', 'script_data' );

        self::add_config( 'uris', array(
            'ajax' => admin_url( 'admin-ajax.php' )
        ));

		// Add defer attr
		add_filter( 'script_loader_tag', function( $tag, $handle, $src ) {

			// include

			if ( $handle === 'liquid-theme-frontend' ){
				return str_replace( '<script', '<script type="module"', $tag );
			}
			return $tag; // exit

			// exclude
			if ( in_array( $handle, array('wp-i18n', 'wp-hooks', 'admin-bar', 'jquery', 'jquery-core') ) ) {
				return $tag;
			}

			// include
			if ( in_array( $handle, array( 'fastdom', 'fastdom-promised', 'underscore', 'backbone', 'backbone-native', 'backbone-radio', 'gsap', 'gsap-scrolltrigger', 'gsap-scrollto', 'gsap-flip', 'gsap-draw-svg') ) ) {
				return str_replace( '<script', '<script defer', $tag );
			}

			return str_replace( '<script', '<script defer', $tag );

		}, 10, 3 );

    }

    /**
     * Register Scripts and Styles
     * @method register
     * @return [type]   [description]
     */
    public function register() {

		//Theme Css
		wp_register_style( 'liquid-wp-style', get_template_directory_uri() . '/style.css' );
		wp_register_style( 'liquid-theme', $this->get_css_uri( 'themes/creative/theme-creative' ) );

		// Register ----------------------------------------------------------
		wp_register_script( 'fastdom', $this->get_vendor_uri( 'fastdom/fastdom.min.js' ), [], null, true );
		wp_register_script( 'fastdom-promised', $this->get_vendor_uri( 'fastdom/fastdom-promised.js' ), [], null, true );
		wp_register_script( 'underscore', $this->get_vendor_uri( 'underscore/underscore-umd-min.js' ), [], null, true );
		wp_register_script( 'backbone', $this->get_vendor_uri( 'backbone/backbone-min.js' ), [], null, true );
		wp_register_script( 'backbone-native', $this->get_vendor_uri( 'backbone-native/backbone.native.min.js' ), [], null, true );
		wp_register_script( 'lazyload', $this->get_vendor_uri( 'lazyload.min.js' ), [], null, true );
		wp_register_script( 'gsap', $this->get_vendor_uri( 'gsap/minified/gsap.min.js' ), [], null, true );
		wp_register_script( 'gsap-flip', $this->get_vendor_uri( 'gsap/minified/Flip.min.js' ), [], null, true );
		wp_register_script( 'gsap-scrolltrigger', $this->get_vendor_uri( 'gsap/minified/ScrollTrigger.min.js' ), [], null, true );
		wp_register_script( 'gsap-splittext', $this->get_vendor_uri( 'gsap/minified/SplitText.min.js' ), [], null, true );
		wp_register_script( 'gsap-scrollto', $this->get_vendor_uri( 'gsap/minified/ScrollToPlugin.min.js' ), [], null, true );
        wp_register_script( 'gsap-draw-svg', $this->get_vendor_uri('gsap/minified/DrawSVGPlugin.min.js'), [], null, true );
		wp_register_script( 'google-maps-api', $this->google_map_api_url(), [], null, true );
		wp_register_script( 'tsparticles', $this->get_vendor_uri( 'tsparticles/tsparticles.bundle.min.js' ), [], null, true );

		$deps = array(
			'fastdom',
			'fastdom-promised',
			'underscore',
			'backbone',
			'backbone-native',
			'gsap',
			'gsap-scrolltrigger',
			'gsap-scrollto',
			'gsap-draw-svg',
		);

		if ( 'on' === liquid_helper()->get_kit_option( 'liquid_lazy_load' ) ) {
			array_push( $deps,
				'lazyload'
			);
		}

		if ( !empty(liquid_helper()->get_kit_option( 'liquid_google_api_key' ) ) ){
			array_push( $deps,
				'google-maps-api'
			);
		}

		wp_register_script( 'liquid-theme-frontend', wp_upload_dir()['baseurl'] . '/liquid-scripts/liquid-frontend-script-'. get_the_ID() .'.js', $deps, null, true );
    }

    /**
     * Enqueue Scripts and Styles
     * @method enqueue
     * @return [type]  [description]
     */
    public function enqueue() {

		// Styles-----------------------------------------------------

		//Base css files
		wp_enqueue_style( 'liquid-wp-style' );

		if ( ( !defined( 'ELEMENTOR_VERSION' ) || !is_callable( 'Elementor\Plugin::instance' ) ) ) {
			wp_enqueue_style( 'liquid-theme-utils', $this->get_css_uri( 'themes/creative/theme-utils' ) );
			wp_enqueue_style( 'liquid-theme' );
			wp_enqueue_script( 'liquid-theme-static', $this->get_js_uri( 'themes/creative/theme-creative-static' ), ['theme-js'], null, true );
		}

        if ( is_singular( 'post' ) ) {
			wp_enqueue_style( 'liquid-theme-sidebar', get_template_directory_uri() . '/assets/css/sidebar/sidebar.css' );
		}

		// Merged files
		if ( liquid_helper()->get_kit_option( 'liquid_enable_optimized_files' ) === 'on' && get_the_ID() ) {
			if ( liquid_helper()->is_page_elementor() && !\Elementor\Plugin::$instance->preview->is_preview_mode() ) {
				wp_enqueue_style('liquid-theme-merged-styles',  wp_upload_dir()['baseurl'] . '/liquid-styles/liquid-merged-styles-' . get_the_ID() . '.css', ['elementor-frontend'], null );
				if ( liquid_helper()->get_kit_option( 'liquid_combine_js' ) === 'on' ){
					wp_enqueue_script('liquid-theme-merged-scripts',  wp_upload_dir()['baseurl'] . '/liquid-styles/liquid-merged-scripts-' . get_the_ID() . '.js', ['jquery'], null, true);
				}
			}
		}

		// Liquid Animations CSS
		if ( defined( 'ELEMENTOR_VERSION' ) && !\Elementor\Plugin::$instance->preview->is_preview_mode() ){
			if ( file_exists( $file = wp_upload_dir()['basedir'] . '/liquid-scripts/liquid-frontend-animation-' . get_the_ID() . '.css' ) ){
				wp_enqueue_style('liquid-theme-animations',  wp_upload_dir()['baseurl'] . '/liquid-scripts/liquid-frontend-animation-' . get_the_ID() . '.css', ['elementor-frontend'], null );
			}
		}

		// Scripts -----------------------------------------------------

		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}

		// Liquid Theme Frontend js, Load except for the editor.
		if ( liquid_helper()->is_page_elementor( true ) && !\Elementor\Plugin::$instance->preview->is_preview_mode() ){
			wp_enqueue_script( 'liquid-theme-frontend' );
		} else {
			wp_enqueue_script(
				'theme-js',
				$this->get_js_uri( 'themes/creative/theme-creative' ),
				[
					'fastdom',
					'fastdom-promised',
					'underscore',
					'backbone',
					'backbone-native',
					'gsap',
					//'elementor-frontend'
				],
				null,
				true
			);
		}

		if( !class_exists( 'Liquid_Addons' ) ) {
			wp_enqueue_style( 'google-font-rubik', $this->google_rubik_font_url(), array(), '1.0' );
			wp_enqueue_style( 'google-font-manrope', $this->google_manrope_font_url(), array(), '1.0' );
		}

		// enqueue scripts only on liquid gdpr is enabled
		if ( liquid_helper()->get_kit_option( 'liquid_gdpr' ) === 'on' ){

			wp_enqueue_script(
				'liquid-gdpr-box',
				$this->get_vendor_uri( 'liquid-gdpr/liquid-gdpr.min.js' ),
				array(),
				null,
				true
			);

			wp_enqueue_style(
				'liquid-gdpr-box',
				$this->get_vendor_uri( 'liquid-gdpr/liquid-gdpr.min.css' ),
				array(),
				null,
			);

		}

        if( is_404() ) {
			wp_enqueue_style( 'not-found', $this->get_css_uri( 'pages/not-found' ) );
		}

		if (
            is_singular( 'post' ) ||
            (
				is_singular() &&
                ( !defined( 'ELEMENTOR_VERSION' ) || !is_callable( 'Elementor\Plugin::instance' ) )
            )
        ){
			$style = liquid_helper()->get_page_option( 'post_style' );
			$style = $style ? $style : liquid_helper()->get_kit_frontend_option( 'liquid_blog_single_post_style' );

			wp_enqueue_style( 'blog-single-base', $this->get_css_uri( 'blog/blog-single/blog-single-base' ) );

			if ( $style && in_array( $style, array( 'classic', 'dark', 'minimal', 'modern-full-screen', 'overlay', 'wide') ) ){
				wp_enqueue_style( 'blog-single-style-'. $style, $this->get_css_uri( 'blog/blog-single/blog-single-style-'. $style .'' ) );
			}
		}

	}

	public function google_map_api_url() {
		$api_key = liquid_helper()->get_kit_option( 'liquid_google_api_key' );
		$google_map_api = add_query_arg( 'key', $api_key, '//maps.googleapis.com/maps/api/js' );

		return $google_map_api;
	}

	public function google_rubik_font_url() {
		$font_url = add_query_arg( array( 'family' => 'Rubik', 'display' => liquid_helper()->get_kit_option( 'liquid_google_font_display' ) ), "//fonts.googleapis.com/css2" );
		return $font_url;
	}

	public function google_manrope_font_url() {
		$font_url = add_query_arg( array( 'family' =>  'Manrope:wght@600', 'display' => liquid_helper()->get_kit_option( 'liquid_google_font_display' ) ), "//fonts.googleapis.com/css2" );
		return $font_url;
	}

	//Register the woocommerce  shop styles
	public function woo_register() {
		//check if woocommerce is activated and styles are loaded
		if( class_exists( 'WooCommerce' ) ) {
			$deps = array( 'woocommerce-layout', 'woocommerce-smallscreen', 'woocommerce-general' );
			wp_register_style( 'theme-shop', $this->get_css_uri('themes/creative/theme-creative.shop'), $deps );
			wp_enqueue_style( 'theme-shop' );
		}

		// Fix shop page enq elementor-frontend-css
		if ( class_exists('WooCommerce') && is_shop() ) {
			wp_dequeue_style( 'elementor-frontend' );
			wp_enqueue_style( 'elementor-frontend' );
		}
	}


    /**
     * Localize Data Object
     * @method script_data
     * @return [type]      [description]
     */
    public function script_data() {
        wp_localize_script( 'liquid-theme-frontend', 'liquidTheme', self::$theme_json );
    }

    /**
     * Add items to JSON object
     * @method add_config
     * @param  [type]     $id    [description]
     * @param  string     $value [description]
     */
    public static function add_config( $id, $value = '' ) {

        if(!$id) {
            return;
        }

        if(isset(self::$theme_json[$id])) {
            if(is_array(self::$theme_json[$id])) {
                self::$theme_json[$id] = array_merge(self::$theme_json[$id],$value);
            }
            elseif(is_string(self::$theme_json[$id])) {
                self::$theme_json[$id] = self::$theme_json[$id].$value;
            }
        }
        else {
            self::$theme_json[$id] = $value;
        }
    }

    // Uri Helpers ---------------------------------------------------------------

    public function get_theme_uri($file = '') {
        return get_template_directory_uri() . '/' . $file;
    }

    public function get_child_uri($file = '') {
        return get_stylesheet_directory_uri() . '/' . $file;
    }

    public function get_css_uri($file = '') {
        return $this->get_theme_uri('assets/css/'.$file.'.css');
    }

    public function get_widgets_uri( $file = '' ) {
		return $this->get_theme_uri( 'assets/css/widgets/' . $file . '.css' );
    }

    public function get_js_uri($file = '') {
        return $this->get_theme_uri('assets/js/'.$file.'.js');
    }

    public function get_vendor_uri($file = '') {
        return $this->get_theme_uri('assets/vendors/'.$file);
    }
}
new Liquid_Theme_Assets;