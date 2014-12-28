<?php
/**
 * Uninstall Last updated posts widget - Single and Multisite
 * Source: http://wordpress.stackexchange.com/q/80350/12615
 */
if (!defined('WP_UNINSTALL_PLUGIN')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}

if ( !is_user_logged_in() )
	wp_die( 'You must be logged in to run this script.' );

if ( !current_user_can( 'install_plugins' ) )
	wp_die( 'You do not have permission to run this script.' );

if ( !is_multisite() ) 
{
    delete_option( 'widget_recently_updated_posts' );
	delete_transient('widget_recently_updated_posts');
} 
else 
{
    global $wpdb;
    $blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
    $original_blog_id = get_current_blog_id();

    foreach ( $blog_ids as $blog_id ) 
    {
        switch_to_blog( $blog_id );
        delete_option( 'widget_recently_updated_posts' );
		delete_transient('widget_recently_updated_posts'); 
		// On optimise la base de données après les suppressions
		$wpdb->query('OPTIMIZE TABLE ' . $wpdb->options);
    }
    switch_to_blog( $original_blog_id );
}