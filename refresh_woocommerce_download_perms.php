<?php
/*
Plugin Name: Refresh Woocommerce Download perms
Version: 1.0
Description: It Refreshes perms for download permissions in woocommerce products
Author: Francesco Dattolo
Author URI: https://www.francescodattolo.it
*/

/**
 * Regenerate the WooCommerce download permissions for an order
 * @param  Integer $order_id
 */
function regen_woo_downloadable_product_permissions( $order_id ){

    // Remove all existing download permissions for this order.
    // This uses the same code as the "regenerate download permissions" action in the WP admin (https://github.com/woocommerce/woocommerce/blob/3.5.2/includes/admin/meta-boxes/class-wc-meta-box-order-actions.php#L129-L131)
    // An instance of the download's Data Store (WC_Customer_Download_Data_Store) is created and
    // uses its method to delete a download permission from the database by order ID.
    $data_store = WC_Data_Store::load( 'customer-download' );
    $data_store->delete_by_order_id( $order_id );

    // Run WooCommerce's built in function to create the permissions for an order (https://docs.woocommerce.com/wc-apidocs/function-wc_downloadable_product_permissions.html)
    // Setting the second "force" argument to true makes sure that this ignores the fact that permissions
    // have already been generated on the order.
    wc_downloadable_product_permissions( $order_id, true );

}

add_action('init','refresh_woocommerce_download_perms');
function refresh_woocommerce_download_perms() {

	$orders = get_posts( 
		array(
			'post_type'      => 'shop_order',
			'post_status'    => 'wc-completed',
			'posts_per_page' => -1
		)
	);

	//print("@@@@@@@@@@@@@@@@@@@@@@@@@@@ ORDERS -> refresh_woocommerce_download_perms");
	var_dump($orders);

	foreach ( $orders as $order ) {
		regen_woo_downloadable_product_permissions( $order->ID );
		//wc_downloadable_product_permissions( $order->ID, true );
	}

}


?>
