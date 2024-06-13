<?php
/**
 * The template for displaying Author bios
 */

$style = liquid_helper()->get_page_option( 'post_style' );
$style = $style ? $style : liquid_helper()->get_kit_frontend_option( 'liquid_blog_single_post_style' );
$post_author_meta_enable = liquid_helper()->get_page_option( 'post_author_meta_enable' );

if( 'on' !== $post_author_meta_enable ) {
	return;
}

$enable_reading_time = liquid_helper()->get_page_option( 'post_reading_time' );
if ( $enable_reading_time && $enable_reading_time === 'on' ){
	$reading_time = sprintf( '%s %s', liquid_get_post_reading_time(), liquid_helper()->get_page_option( 'post_reading_time_label' ));
} elseif ( $enable_reading_time === '' ) {
	$enable_reading_time = liquid_helper()->get_kit_option( 'liquid_blog_single_reading_time' );
	if ( $enable_reading_time === 'on' ){
		$reading_time = sprintf( '%s %s', liquid_get_post_reading_time(), liquid_helper()->get_kit_option( 'liquid_blog_single_reading_time_label' ));
	}
}

global $post;

$style = !empty( $style ) ? $style : 'classic';

$cat_before_title = false;

if( 'minimal' == $style || 'classic' == $style || 'wide' == $style ) {
	$cat_before_title = true;
}

?>

<div class="entry-meta flex flex-wrap items-center text-center">
	<div class="byline">

		<figure>
			<?php echo get_avatar( get_the_author_meta( 'user_email' ), 57 ); ?>
		</figure>

		<span class="flex flex-col">
			<span><?php esc_html_e( 'Author', 'aihub' ); ?></span>
			<?php liquid_author_link() ?>
		</span>

	</div>

	<div class="posted-on">
		<span><?php esc_html_e( 'Published on:', 'aihub' ); ?></span>
		<a href="<?php the_permalink(); ?>" rel="bookmark">
			<?php
				$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time>';
				printf( $time_string,
					esc_attr( get_the_date( 'c' ) ),
					get_the_date()
				);
			?>
		</a>
	</div>

	<?php if ( !$cat_before_title ) : ?>
	<div class="cat-links">
		<span><?php esc_html_e( 'Published in:', 'aihub' ); ?></span>
		<?php liquid_get_category(); ?>
	</div>
	<?php endif; ?>


	<?php if ( $enable_reading_time && !empty( $reading_time ) ) : ?>
	<div class="read-time">
		<span><?php echo esc_html( $reading_time ); ?></span>
	</div>

	<?php endif; ?>
</div>