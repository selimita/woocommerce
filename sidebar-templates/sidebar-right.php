<?php
/**
 * The right sidebar containing the main widget area
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

// Cart & Checkout Sidebar Condition
if ( is_cart() || is_checkout() ) {
    return;
}
// Shop Sidebar Condition
if ( is_shop() ) {
    $understrap_sidebar = 'shop-sidebar';
} else {
    $understrap_sidebar = 'right-sidebar';
}
// Category SubCategory Sidebar Off
if ( is_archive( 'product-cat' ) ) {
    return;
}

if ( !is_active_sidebar( $understrap_sidebar ) ) {
    return;
}

// when both sidebars turned on reduce col size to 3 from 4.
$sidebar_pos = get_theme_mod( 'understrap_sidebar_position' );
?>

<?php if ( 'both' === $sidebar_pos ): ?>
	<div class="col-md-3 widget-area" id="right-sidebar">
<?php else: ?>
	<div class="col-md-4 widget-area" id="right-sidebar">
<?php endif;?>
<?php dynamic_sidebar( $understrap_sidebar );?>

</div><!-- #right-sidebar -->
