<?php
/**
 * UnderStrap functions and definitions
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

// UnderStrap's includes directory.
$understrap_inc_dir = 'inc';

// Array of files to include.
$understrap_includes = array(
    '/theme-settings.php', // Initialize theme default settings.
    '/setup.php', // Theme setup and custom theme supports.
    '/widgets.php', // Register widget area.
    '/enqueue.php', // Enqueue scripts and styles.
    '/template-tags.php', // Custom template tags for this theme.
    '/pagination.php', // Custom pagination for this theme.
    '/hooks.php', // Custom hooks.
    '/extras.php', // Custom functions that act independently of the theme templates.
    '/customizer.php', // Customizer additions.
    '/custom-comments.php', // Custom Comments file.
    '/class-wp-bootstrap-navwalker.php', // Load custom WordPress nav walker. Trying to get deeper navigation? Check out: https://github.com/understrap/understrap/issues/567.
    '/editor.php', // Load Editor functions.
    '/block-editor.php', // Load Block Editor functions.
    '/deprecated.php', // Load deprecated functions.
);

// Load WooCommerce functions if WooCommerce is activated.
if ( class_exists( 'WooCommerce' ) ) {
    $understrap_includes[] = '/woocommerce.php';
}

// Load Jetpack compatibility file if Jetpack is activiated.
if ( class_exists( 'Jetpack' ) ) {
    $understrap_includes[] = '/jetpack.php';
}

// Include files.
foreach ( $understrap_includes as $file ) {
    require_once get_theme_file_path( $understrap_inc_dir . $file );
}

// Add Filter loop_shop_columns && Function
function understrap_loop_shop_columns( $productrow ) {
    return 3;
}
add_filter( 'loop_shop_columns', 'understrap_loop_shop_columns' );

// Add Filter woocommerce_product_query && Function
// function understrap_woocommerce_product_query( $wpq ) {
//     $wpq->set( 'post__not_in', array( 17, 24 ) );
//     return $wpq;
// }
// add_filter( 'woocommerce_product_query', 'understrap_woocommerce_product_query' );

// Add Filter woocommerce_product_query && Function
function understrap_woocommerce_product_query_tax( $wpq ) {
    $tax_query = (array) $wpq->get( 'tax_query' );
    $tax_query[] = array(
        'taxonomy' => 'product_cat',
        'field'    => 'slug',
        'terms'    => array( 'accessories' ),
        'operator' => 'NOT IN',
    );
    $wpq->set( 'tax_query', $tax_query );
    return $wpq;
}
add_filter( 'woocommerce_product_query', 'understrap_woocommerce_product_query_tax' );

// Product Category & SubCategory Show in Shop PAGE
function understrap_woocommerce_before_shop_loop() {
    $term_id = get_queried_object_id();
    $parent = $term_id;
    echo "$parent";
    if ( $term_id == 0 ) {
        echo "<ul class='products columns-3'>";
        woocommerce_output_product_categories();
        echo "</ul>";
    }
    if ( $parent > 0 ) {
        $term_id = $parent;
    }
    if ( $term_id ) {
        echo "<ul class='products columns-3'>";
        woocommerce_output_product_categories( array(
            'parent_id' => $term_id,
        ) );
        echo '</ul>';
    }
    echo "<div class='clearfix'></div>";
}
//add_filter( 'woocommerce_before_shop_loop', 'understrap_woocommerce_before_shop_loop' );

// Justified Gallery

function denver_woocommerce_before_shop_loop_jg() {
    // localhost/woocommerce/shop/?cg=1
    if ( isset( $_GET['cg'] ) && $_GET['cg'] == 1 ) {
        $cat_args = array(
            'orderby'    => 'name',
            'order'      => 'asc',
            'hide_empty' => true,
        );
        $product_categories = get_terms( 'product_cat', $cat_args );
        ?>
    <div id="justifiedgallery">
		<?php foreach ( $product_categories as $product_category ): ?>
            <?php
// get_woocommerce_term_meta > get_term_meta
        $thumbnail_id = get_term_meta( $product_category->term_id, 'thumbnail_id', true );
        $thumbnail = wp_get_attachment_image_url( $thumbnail_id, 'large' );
        if ( !$thumbnail ) {
            continue;
        }
        ?>
            <a href="<?php echo esc_url( get_term_link( $product_category, 'product_cat' ) ); ?>">
                <img alt="<?php echo esc_attr( $product_category->name ); ?>" src="<?php echo $thumbnail; ?>"/>
            </a>
        <?php endforeach;?>
    </div>
	<?php
}
}
add_action( 'woocommerce_before_shop_loop', 'denver_woocommerce_before_shop_loop_jg', 8 );

// Query String for Shop Page DEMO
function understrap_wpq( $u_wpq ) {
    if ( isset( $_GET['postsperpage'] ) && $_GET['postsperpage'] == 6 ) {
        $u_wpq->set( 'posts_per_page', 6 );
    }
    return $u_wpq;
}
add_action( 'woocommerce_product_query', 'understrap_wpq' );

// Query String for Different Columns DEMO
function understrap_dc( $u_dc ) {
    if ( isset( $_GET['dc'] ) && $_GET['dc'] > 0 ) {
        $u_dc = sanitize_text_field( $_GET['dc'] );
    }
    return $u_dc;
}
add_action( 'loop_shop_columns', 'understrap_dc' );

// Fields Remove FORM Checkout PAGE
function understrap_frfcp( $fields ) {
/* echo "<pre>";
print_r($fields);
echo "</pre>"; */

    unset( $fields['billing']['billing_company'] );
    unset( $fields['billing']['billing_address_1'] );
    unset( $fields['billing']['billing_address_2'] );
    unset( $fields['billing']['billing_city'] );
    unset( $fields['billing']['billing_postcode'] );
    unset( $fields['billing']['billing_country'] );
    unset( $fields['billing']['billing_state'] );

    unset( $fields['shipping']['shipping_company'] );
    unset( $fields['shipping']['shipping_address_1'] );
    unset( $fields['shipping']['shipping_address_2'] );
    unset( $fields['shipping']['shipping_city'] );
    unset( $fields['shipping']['shipping_postcode'] );
    unset( $fields['shipping']['shipping_country'] );
    unset( $fields['shipping']['shipping_state'] );

    return $fields;
}
add_filter( 'woocommerce_checkout_fields', 'understrap_frfcp' );