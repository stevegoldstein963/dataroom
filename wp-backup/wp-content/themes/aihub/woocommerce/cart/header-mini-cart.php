<?php
/**
 * Mini-cart
 *
 * Contains the markup for the mini-cart, used by the cart widget.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/mini-cart.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.7.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$order_count = WC()->cart->get_cart_contents_count();
$is_empty    = WC()->cart->is_empty();
$sub_total   = WC()->cart->get_cart_subtotal();

?>
<span class="item-count" style="display:none;"><?php echo esc_attr( $order_count ); ?></span>
<div class="ld-cart-head flex items-center w-full h2">
	<?php esc_html_e( 'Cart', 'aihub' ); ?> <span class="ld-module-trigger-count flex items-centeer justify-center"><?php echo esc_html( $order_count ) ?></span>
</div>

<div class="ld-cart-products woocommerce-mini-cart-item w-full relative">

	<?php if ( ! $is_empty ) : ?>

		<?php

			foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
				$_product     = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
				$product_id   = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

				if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_widget_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
					$product_name      = apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key );
					$thumbnail         = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );
					$product_price     = apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
					$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
					?>
					<div class="<?php echo esc_attr( apply_filters( 'woocommerce_mini_cart_item_class', 'ld-cart-product flex flex-wrap items-center w-full relative', $cart_item, $cart_item_key ) ); ?>">
						<?php
						echo apply_filters( 'woocommerce_cart_item_remove_link', sprintf(
							'<a href="%s" class="ld-cart-product-remove remove remove_from_cart_button" title="%s" data-product_id="%s" data-cart_item_key="%s" data-product_sku="%s"><svg width="32" height="32" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" style="width: 1em; height: 1em;"><path fill="currentColor" d="M7.45 7.45a1.12 1.12 0 0 1 1.57 0l7.05 7.04 7.05-7.04a1.12 1.12 0 1 1 1.58 1.57l-7.04 7.05 7.04 7.05a1.12 1.12 0 1 1-1.58 1.58l-7.05-7.04-7.05 7.04a1.12 1.12 0 1 1-1.57-1.58l7.04-7.05-7.04-7.05a1.12 1.12 0 0 1 0-1.57z"></path></svg></a>',
							esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
							__( 'Remove this item', 'aihub' ),
							esc_attr( $product_id ),
							esc_attr( $cart_item_key ),
							esc_attr( $_product->get_sku() )
						), $cart_item_key );
						?>
						<div class="ld-cart-product-info flex items-center grow relative">
						<?php if ( empty( $product_permalink ) ) : ?>
							<?php if( $thumbnail ) { ?>
								<figure>
									<?php echo apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key ); ?>
								</figure>
							<?php } ?>
							<span class="ld-cart-product-details flex flex-col grow">
								<span class="ld-cart-product-name h2 mt-0 mb-0"><?php echo wp_kses_post( $product_name ); ?></span>
								<?php echo wc_get_formatted_cart_item_data( $cart_item ); ?>
							</span>
						<?php else : ?>
							<a class="flex items-center grow" href="<?php echo esc_url( $product_permalink ); ?>">
								<?php if( $thumbnail ) { ?>
									<figure>
										<?php echo apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key ); ?>
									</figure>
								<?php } ?>
								<span class="ld-cart-product-details flex flex-col grow">
									<span class="ld-cart-product-name h2 mt-0 mb-0"><?php echo wp_kses_post( $product_name ); ?></span>
									<?php echo wc_get_formatted_cart_item_data( $cart_item ); ?>

									<span class="ld-cart-product-price flex flex-col items-start">
										<?php echo apply_filters( 'woocommerce_widget_cart_item_quantity', '<span class="quantity flex items-center">' . sprintf( '<span>%s</span> <span class="ld-cart-product-quantity ms-4">&times;%s</span>', $cart_item['quantity'], $product_price ) . '</span>', $cart_item, $cart_item_key ); ?>
									</span>

								</span>
							</a>
						<?php endif; ?>
						</div>

					</div>
					<?php
				}
			}
		?>

	<?php else : ?>

		<div class="empty w-full text-center"><h3><?php esc_html_e( 'No products in the cart.', 'aihub' ); ?></h3></div>

	<?php endif; ?>

</div>

<?php if ( !$is_empty ) : ?>
<div class="ld-cart-foot w-full">
	<div class="ld-cart-total flex flex-wrap items-center justify-between">
		<span class="ld-cart-total-label font-bold uppercase tracking-200"><?php esc_html_e( 'Subtotal', 'aihub' ); ?></span>
		<span class="ld-cart-total-price color-primary"><?php echo wp_kses_post( $sub_total ); ?></span>
	</div>
	<div class="ld-cart-button flex flex-col">
		<a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="btn btn-xlg btn-solid uppercase tracking-200 text-center">
            <span class="btn-txt"><?php esc_html_e( 'Checkout', 'aihub' ); ?></span>
            <span class="btn-icon"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="32" viewBox="0 0 12 32" style="width: 1em; height: 1em;"><path fill="currentColor" d="M8.375 16L.437 8.062C-.125 7.5-.125 6.5.438 5.938s1.563-.563 2.126 0l9 9c.562.562.624 1.5.062 2.062l-9.063 9.063c-.312.312-.687.437-1.062.437s-.75-.125-1.063-.438c-.562-.562-.562-1.562 0-2.125z"></path></svg></span>
		</a>
		<a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="btn btn-naked uppercase tracking-200 text-center">
            <span class="btn-txt"><?php esc_html_e( 'View Cart', 'aihub' ); ?></span>
            <span class="btn-icon"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="32" viewBox="0 0 12 32" style="width: 1em; height: 1em;"><path fill="currentColor" d="M8.375 16L.437 8.062C-.125 7.5-.125 6.5.438 5.938s1.563-.563 2.126 0l9 9c.562.562.624 1.5.062 2.062l-9.063 9.063c-.312.312-.687.437-1.062.437s-.75-.125-1.063-.438c-.562-.562-.562-1.562 0-2.125z"></path></svg></span>
		</a>
	</div>
</div>
<?php endif; ?>