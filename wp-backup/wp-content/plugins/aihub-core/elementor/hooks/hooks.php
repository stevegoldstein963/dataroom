<?php

defined( 'ABSPATH' ) || exit;

// WordPress Init
add_action( 'wp', function(){
	$script_id = liquid_helper()->get_script_id();

	if ( function_exists('liquid_helper') && ! liquid_helper()->get_scripts_cache( $script_id ) ) {
		include LQD_CORE_PATH . 'elementor/optimization/dynamic-scripts.php';
	}

    include LQD_CORE_PATH . 'elementor/optimization/parse-css/parse-css.php';
} );

// load elementor styles in the editor
add_action( 'wp_enqueue_scripts', function(){

    // Load elementor-fronend css on archive pages
    if ( is_archive() || is_search() || is_home() || is_404() || !liquid_helper()->is_page_elementor() ) {
        wp_enqueue_style('elementor-frontend');
    }

    if ( \Elementor\Plugin::$instance->preview->is_preview_mode() ) {
        wp_dequeue_style( 'liquid-theme' );

        wp_enqueue_style(
            'theme-css',
            LQD_CORE_URL . 'assets/css/themes/aihub/theme.css',
            ['elementor-frontend'],
            LQD_CORE_VERSION
        );

        wp_enqueue_style(
            'theme-editor',
            LQD_CORE_URL . 'assets/css/themes/aihub/theme.editor.css',
            ['theme-css'],
            LQD_CORE_VERSION
        );
    }
});

// Register controls
add_action( 'elementor/controls/register', [ $this, 'register_controls' ] );

// Register widgets
add_action( 'elementor/widgets/register', [ $this, 'register_widgets' ] );

// Elementor After Enqueue
add_action( 'elementor/editor/after_enqueue_scripts', function() {

	wp_enqueue_style(
        'liquid-elementor-editor-controls-style',
        LQD_CORE_URL . 'assets/css/themes/aihub/theme.editor-controls.css',
        ['elementor-editor'],
        LQD_CORE_VERSION
    );

	wp_enqueue_script(
        'liquid-elementor-editor-controls-script',
        LQD_CORE_URL . 'assets/js/themes/aihub/theme.editor-controls.js',
        [],
        LQD_CORE_VERSION,
        true
    );

	wp_localize_script( 'elementor-editor', 'liquidTheme', [
        'uris' => [
            'ajax' => admin_url( 'admin-ajax.php' ),
            'theme' => get_template_directory_uri(),
        ]
    ] );

	// Collections CSS
	wp_add_inline_style( 'liquid-elementor-editor-controls-style', '#elementor-template-library-templates{padding-bottom:40px}.elementor-template-library-expanded-template{position:absolute;right:10px;top:10px;background:#f9da68;color:#34383c;padding:2px 6px;border-radius:1.5px;font-weight:bold}.elementor-template-library-expanded-template-alert{display:grid;place-content:center;position:absolute;width:100%;bottom:0;left:0;z-index:999;height:40px;padding:20px;font-size:14px;font-weight:bold;background:#f9da68;color:#34383c}.elementor-template-library-expanded-template-alert a{color:inherit;text-decoration:underline}.elementor-template-library-hub-title{margin-top:.5em}.elementor-template-library-hub-title span{opacity:.5}' );

	// Liquid Template Editor JS
	wp_add_inline_script( 'elementor-editor', '

		let tmpl_id = 0,
		new_tmpl_id = 0,
		tmpl_control = "",
		tmpl_action = "";

		function lqd_edit_tmpl(event){

			tmpl_action = "edit";
			document.querySelector("#lqd-tmpl-edit").style.display = "block";

			// get current template id
			var parent = (event.target).parentElement.parentElement.parentElement;
			var children = parent.children;
			tmpl_control = children[0].children[0].control;
			tmpl_id = tmpl_control.value ? tmpl_control.value : "";

			if ( tmpl_id ) {
				document.getElementById("lqd-tmpl-edit-iframe").setAttribute("src", "'. admin_url() .'post.php?post=" + tmpl_id + "&action=elementor");
				console.log("LIQUID - Editing Template: " + tmpl_id);
			} else {
				console.log("LIQUID - Template ID not found!");
			}

		}

		function lqd_add_tmpl(event){

			tmpl_action = "add";
			document.querySelector("#lqd-tmpl-edit").style.display = "block";

			// get current template id
			var parent = (event.target).parentElement.parentElement.parentElement;
			var children = parent.children;
			tmpl_control = children[0].children[0].control;
			tmpl_id = tmpl_control.value ? tmpl_control.value : "";


			jQuery.post(ajaxurl, { "action": "lqd_add_tmpl" }, function (response) {
				new_tmpl_id = response.data;
				jQuery(tmpl_control).append("<option value="+ new_tmpl_id +">Template #" + new_tmpl_id + "</option>");
				document.getElementById("lqd-tmpl-edit-iframe").setAttribute("src", "'. admin_url() .'post.php?post=" + new_tmpl_id + "&action=elementor");
				console.log("LIQUID - New Template Added: Template #" + new_tmpl_id );
			});


			if ( tmpl_id ) {
				console.log("LIQUID - Editing Template: " + tmpl_id);
			} else {
				console.log("LIQUID - Template ID not found!");
			}

		}

		// Edit Custom CPT
		elementor.on( "document:loaded", () => {

			console.log("LIQUID - Elementor iframe loaded!");

			const elementorPreviewIframe = document.querySelector("#elementor-preview-iframe");

			// Get the button element from the iframe

			const editButtons = elementorPreviewIframe.contentWindow.document.querySelectorAll(".lqd-tmpl-edit-cpt--btn");

			editButtons.forEach(function(button) {
				button.addEventListener("click", function(event) {

					tmpl_id = button.getAttribute("data-post-id");
					document.querySelector("#lqd-tmpl-edit").style.display = "block";
					document.getElementById("lqd-tmpl-edit-iframe").setAttribute("src", "'. admin_url() .'post.php?post=" + tmpl_id + "&action=elementor");
					console.log("LIQUID - Editing Template: " + tmpl_id);
				});
			});

			// Close iFrame
			document.querySelector(".lqd-tmpl-edit--close").addEventListener("click", function(){
				document.getElementById("lqd-tmpl-edit-iframe").setAttribute("src", "about:blank");
				document.querySelector("#lqd-tmpl-edit").style.display = "none";
				if ( tmpl_action === "add" ) {
					jQuery(tmpl_control).val( new_tmpl_id );
					jQuery(tmpl_control).trigger( "change" );
				} else if ( tmpl_action === "edit" ) {
					jQuery(tmpl_control).val( tmpl_id );
					jQuery(tmpl_control).trigger( "change" );
				} else if ( tmpl_action === "cpt" ) {
					// do something
				}

			});

		} );

		'
	);
} );

// Elementor Preview CSS / JS
add_action( 'elementor/preview/enqueue_styles', function() {
    wp_enqueue_script(
        'tinycolor',
        LQD_CORE_URL . 'assets/vendors/tinycolor.js',
        [
			'fastdom',
			'fastdom-promised',
			'underscore',
			'backbone',
			'backbone-native',
			'gsap',
			'gsap-scrolltrigger',
			'gsap-draw-svg',
			'gsap-scrollto',
			'elementor-frontend'
		],
        LQD_CORE_VERSION,
        true
    );

    wp_enqueue_script(
        'theme-js',
        LQD_CORE_URL . 'assets/js/themes/aihub/theme.js',
        [
			'fastdom',
			'fastdom-promised',
			'underscore',
			'backbone',
			'backbone-native',
			'gsap',
			'gsap-scrolltrigger',
			'gsap-scrollto',
			'gsap-draw-svg',
			'elementor-frontend'
		],
        LQD_CORE_VERSION,
        true
    );

    wp_enqueue_script(
        'theme-editor',
        LQD_CORE_URL . 'assets/js/themes/aihub/theme.editor.js',
        ['elementor-frontend'],
        LQD_CORE_VERSION,
        true
    );

	wp_enqueue_script( 'tsparticles' );
} );

// Elementor Template Editor - Add new template / ajax
add_action( 'wp_ajax_lqd_add_tmpl', function(){

	$post_id = wp_insert_post(
		[
			'post_type' => 'elementor_library',
			'meta_input' => [ '_elementor_template_type' => 'section' ]
		]
	);

	if( ! is_wp_error( $post_id ) ) {
		wp_update_post(
			[
				'ID' => $post_id,
				'post_title'=> sprintf( 'Template #%s', $post_id )
			]
		);
		wp_send_json_success( $post_id );
	}

} );

// Elementor Template Editor - Template & Style
add_action( 'elementor/editor/footer', function() {
	?>
		<style>
			.lqd-tmpl-edit-editor-buttons{
				display: flex;
				gap: 1em;
				width: 100%;
			}
			.lqd-tmpl-edit-editor-buttons button {
				width: 100%;
				padding: .7em;
				text-transform: capitalize;
				font-size: 10px;
			}
			#lqd-tmpl-edit {
				position: fixed;
				z-index: 99999;
				width: 90%;
				height: 90%;
				left:5%;
				top: calc(5% - 20px);
				background: #fff;
				box-shadow: 0 0 120px #000;
			}
			.lqd-tmpl-edit--header {
				display: flex;
				justify-content: space-between;
				align-items: center;
				background-color: #26292C;
				height: 39px;
				border-bottom: solid 1px #404349;
				padding: 1em;
			}
			.lqd-tmpl-edit--logo {
				display: inline-flex;
				align-items: center;
				gap: 10px;
				font-weight: 500;
			}
			.lqd-tmpl-edit--close {
				font-size: 20px;
				cursor: pointer;
				padding: 20px;
				margin-inline-end: -20px;
			}
		</style>
		<div id="lqd-tmpl-edit" class="lqd-tmpl-edit" style="display:none;">
			<div class="lqd-tmpl-edit--header">
				<div class="lqd-tmpl-edit--logo"><img src="<?php echo esc_url( LQD_CORE_URL . 'assets/img/logo/liquid-logo.svg' );?>" height="20"><?php esc_html_e( 'Edit Template' ); ?></div>
				<div class="lqd-tmpl-edit--close">&times;</div>
			</div>
			<iframe src="about:blank" width="100%" height="100%" frameborder="0" id="lqd-tmpl-edit-iframe"></iframe>
		</div>
		<script>
			(() => {
				const closeModal = document.querySelector('.lqd-tmpl-edit--close');
				if ( !closeModal ) return;
				closeModal.addEventListener('click', async () => {
					if ( typeof $e === 'undefined' ) return;
					await $e.run('document/save/update', { force: true });
					elementor.reloadPreview();
				})
			})();
		</script>
	<?php
} );

// Add custom fonts to elementor from redux
if ( function_exists( 'liquid_helper' ) ){

    if ( !empty( liquid_helper()->get_kit_option( 'liquid_custom_fonts' ) ) ){
        // Add Fonts Group
        add_filter( 'elementor/fonts/groups', function( $font_groups ) {
            $font_groups['liquid_custom_fonts'] = __( 'Liquid Custom Fonts' );
            return $font_groups;
        } );

        // Add Group Fonts
        add_filter( 'elementor/fonts/additional_fonts', function( $additional_fonts ) {
            $font_list = liquid_helper()->get_kit_option( 'liquid_custom_fonts' );
            foreach( $font_list as $font){
                if ( !isset( $font['title']) ) return;
                // Font name/font group
                $additional_fonts[$font['title']] = 'liquid_custom_fonts';
            }
            return $additional_fonts;
        } );

    }

    // Google Fonts display
    if ( get_option( 'elementor_font_display' ) !== liquid_helper()->get_kit_option( 'liquid_google_font_display' ) ) {
        update_option( 'elementor_font_display', liquid_helper()->get_kit_option( 'liquid_google_font_display' ) );
    }

}

// Add missing Google Fonts
add_filter( 'elementor/fonts/additional_fonts', function( $additional_fonts ){
    if ( !is_array($additional_fonts) ) {
        $additional_fonts = [];
    }
    $fonts = array(
        // font name => font file (system / googlefonts / earlyaccess / local)
        'Outfit' => 'googlefonts',
        'Golos Text' => 'googlefonts',
        'Wix Madefor Text' => 'googlefonts',
        'Gloock' => 'googlefonts',
    );
    $fonts = array_merge( $fonts, $additional_fonts );
    return $fonts;
} );

// Custom Shapes
add_action( 'elementor/shapes/additional_shapes', function( $additional_shapes ) {

    for ($i=1; $i<=16; $i++){
        $additional_shapes[ 'lqd-custom-shape-'.$i ] = [
            'title' => __('Liquid Shape - '.$i, 'aihub-core'),
            'path' => LQD_CORE_PATH . 'elementor/params/shape-divider/'.$i.'.svg',
            'url' => LQD_CORE_PLUGIN_URL . 'elementor/params/shape-divider/'.$i.'.svg',
            'has_flip' => false,
            'has_negative' => false,
        ];
    }
    return $additional_shapes;
});

// Woocommerce Session Handler
if ( class_exists( 'WooCommerce' ) && (! empty( $_REQUEST['action'] ) && 'elementor' === $_REQUEST['action'] && is_admin()) ) {
    add_action( 'admin_action_elementor', function(){
        \WC()->frontend_includes();
        if ( is_null( \WC()->cart ) ) {
            global $woocommerce;
            $session_class = apply_filters( 'woocommerce_session_handler', 'WC_Session_Handler' );
            $woocommerce->session = new $session_class();
            $woocommerce->session->init();

            $woocommerce->cart     = new \WC_Cart();
            $woocommerce->customer = new \WC_Customer( get_current_user_id(), true );
        }
    }, 5 );
}

// Regenerate css after save for footer
add_action( 'elementor/editor/after_save', function( $post_id ) {

    if (
        get_post_type( $post_id ) === 'liquid-header' ||
        get_post_type( $post_id ) === 'liquid-footer' ||
        get_post_type( $post_id ) === 'liquid-mega-menu'
    ){
        \Elementor\Plugin::instance()->files_manager->clear_cache();
		liquid_helper()->purge_scripts_cache( true );
		delete_post_meta( $post_id, '_liquid_post_content' );
		delete_post_meta( $post_id, '_liquid_post_content_has_bg' );
		update_option( 'liquid_utils_css', '' );
    } else {
        \Elementor\Plugin::instance()->files_manager->clear_cache();
		liquid_helper()->purge_scripts_cache( $post_id );
    }

	update_option( 'liquid_widget_asset_css', array() );
	update_option( 'lqd_el_container_bg', array() );

});

// Hide performance kit with css
add_action( 'elementor/editor/wp_head', function(){
	echo '<style>.elementor-panel-menu-item-liquid-performance-kit,.elementor-panel-menu-item-liquid-portfolio-kit,.elementor-control-section_liquid-extras-kit_custom_cursor,.elementor-control-liquid_cc,.elementor-control-section_liquid-extras-kit_preloader,.elementor-control-section_liquid-extras-kit_local_scroll,.elementor-control-section_liquid-extras-kit_back_to_top{display:none!important}</style>';
});