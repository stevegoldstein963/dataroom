<?php
/**
 * Default footer template
 *
 * @package Hub
 */

$footer_id = liquid_get_custom_footer_id();

?>
<footer <?php liquid_helper()->attr( 'footer' ); ?>>

	<?php liquid_helper()->get_elementor_edit_cpt( $footer_id ); ?>

	<div id="lqd-page-footer" data-lqd-view="liquidPageFooter">
		<?php 

			if( function_exists( 'icl_object_id' ) ) {
				$footer_id = icl_object_id( $footer_id, 'page', false, ICL_LANGUAGE_CODE );
			}
			if ( function_exists( 'pll_get_post' ) ) {
				$footer_id = pll_get_post( $footer_id );
			}
					
			if ( defined( 'ELEMENTOR_VERSION' ) ){

				if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
					$content = Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $footer_id );
				} else {
					$content = get_post_meta($footer_id, '_liquid_post_content', true);
					if (!$content) {
						$content = Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $footer_id, true );
						update_post_meta( $footer_id, '_liquid_post_content', $content );
					}
	
					$has_bg = get_post_meta($footer_id, '_liquid_post_content_has_bg', true);
					$has_bg_ids = get_option( 'lqd_el_container_bg' );
					if (!$has_bg && $has_bg_ids) {
						$content = liquid_helper()->lqd_el_container_bg( $content, $has_bg_ids );
						update_post_meta( $footer_id, '_liquid_post_content', $content );
						update_post_meta( $footer_id, '_liquid_post_content_has_bg', 'yes' );
					}
				}

				echo $content;
	
			}

		?>
	</div>
</footer>