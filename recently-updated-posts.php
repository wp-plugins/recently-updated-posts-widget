<?php
/*
* Plugin Name: Recently updated posts widget
* Description: The latests posts and pages updated (who are not the most recents).
* Author: Luciole135
* Version: 1.0
* Author URI: http://additifstabac.free.fr/
* Plugin URI: http://additifstabac.free.fr/index.php/recently-updated-posts-widget/
* Text Domain: recently-updated-posts-domain 
* Domain Path: /languages
* License: GPL2
*/

// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) 
		{	echo "Hi there!  I'm just a plugin, not much I can do when called directly.";
			exit;
		}

/* Function that registers our widget. */
function recently_updated_posts_init() {
	register_widget( 'recently_updated_posts_Widget' );
}

/* Add our function to the widgets_init hook. */
add_action('widgets_init', 'recently_updated_posts_init' );
load_plugin_textdomain('recently-updated-posts-domain', null, dirname( plugin_basename( __FILE__ ) ).'/languages');

if (is_admin()) {
		add_action('post_updated', 'recently_updated_posts_delete_transient');
		
	function recently_updated_posts_delete_transient($attachment_id) {
		global $wpdb;
		delete_transient('widget_recently_updated_posts');
		// On optimise la base de données après les suppressions
        $wpdb->query('OPTIMIZE TABLE ' . $wpdb->options);
		}
}

class recently_updated_posts_Widget extends WP_Widget {
	function recently_updated_posts_Widget() {
		/* Widget settings. */
		$widget_ops = array("classname" => 'recently_updated_posts',"description" => __('The latests posts and pages updated (who are not the most recents).','recently-updated-posts-domain'),);
		
		/* Create the widget. */
		$this->WP_Widget('recently_updated_posts', __('Recently updated posts','recently-updated-posts-domain'), $widget_ops);
	}
	
	function widget( $args, $instance ) {
		global $wpdb;
		extract( $args );

		/* User-selected settings. */
		$title = apply_filters('widget_title', $instance['title'] );
		$nb_display = $instance['nb_display'];

		/* Before widget (defined by themes). */
		echo $before_widget;

		/* Title of widget (before and after defined by themes). */
		if($title)
			echo $before_title . $title . $after_title;
			
		echo recently_updated_posts_transient($nb_display);
		/* After widget (defined by themes). */
		?><div class="clear"></div><?php 
		echo $after_widget;
	}
	
	function update($new_instance, $old_instance) {
	// Modification des paramètres du widget
    $instance = $old_instance;
	
	/* si le widget est modifié, alors supprimer le transient */
	if (isset($new_instance)) delete_transient('widget_recently_updated_posts');
    
	/* Récupération des paramètres envoyés */
    $instance['title'] = strip_tags($new_instance['title']);
    $instance['nb_display'] = $new_instance['nb_display'];
 
    return $instance;
    }
	
	function form($instance) {
	global $wpdb;
	// Affichage des paramètres du widget dans l'admin
    $title = esc_attr($instance['title']);
    $nb_display = esc_attr($instance['nb_display']);
	 ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>">
                <?php _e('Title:','recently-updated-posts-domain'); ?>
                <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
            </label>
        </p>
 
        <p>
            <label for="<?php echo $this->get_field_id('nb_display'); ?>">
                <?php _e('Number:','recently-updated-posts-domain'); ?>
                <input class="widefat" id="<?php echo $this->get_field_id('nb_display'); ?>" name="<?php echo $this->get_field_name('nb_display'); ?>" type="text" value="<?php echo $nb_display; ?>" />
            </label>
        </p>
    <?php
    }	
}

function recently_updated_posts_transient($nb_display){   
    // Le transient est-il inexistant ou expiré ?
    if (false === ($transient=get_transient('widget_recently_updated_posts'))) {

        // Si oui, je donne une valeur au futur transient.
        $value = requete_dernier_article($nb_display);

        // Je met à jour la valeur du transient avec $value, sans délai d'expiration
        set_transient('widget_recently_updated_posts', $value);

        // Je met à jour la valeur de ma variable $transient
        $transient = get_transient( 'widget_recently_updated_posts' );
    }
    //Nous renvoyons la valeur du transient mis à jour
    return $transient; 
}

function requete_dernier_article($nb_display) {
		global $wpdb;
		/* Nous allons ici récupérer les dernière articles mis à jour qui ne sont pas les derniers écrits */
		$today  = current_time('mysql', 1);
        $last_update = $wpdb->get_results("SELECT post_modified, post_title, id
										FROM $wpdb->posts
										WHERE post_type <> 'revision' AND post_type <> 'attachment' AND post_type <> 'nav_menu_item' AND post_status = 'publish' 
										AND post_date NOT IN (SELECT * FROM (SELECT post_date 
																			FROM $wpdb->posts
																			WHERE post_type <> 'revision' AND post_type <> 'attachment' AND post_type <> 'nav_menu_item' AND post_status = 'publish'
																			ORDER BY post_date DESC
																			LIMIT $nb_display)
																AS temp)
										ORDER BY post_modified DESC
										LIMIT $nb_display");	
		// Décommenter la ligne suivante pour afficher les infobulles en français sur un site local sous windows
		//if (stripos(PHP_OS,"win") !== false) setlocale (LC_TIME, 'french');
		$display= '<ul>';								
		foreach ($last_update as $post)
            { $format_OS=(stripos(PHP_OS,"win") !== false ? "%A %#d %B %Y" : "%A %e %B %Y");
			$title = sprintf(__('Last updated on %1$s at %2$s','recently-updated-posts-domain'),strftime($format_OS, strtotime($post->post_modified)),strftime("%H:%M", strtotime($post->post_modified)));
			$display.= "<li><a href=".get_permalink($post->id)." title='$title' >".$post->post_title."</a></li>";
			}
		$display.= '</ul>';

		return $display;
		}

/**
 * Register style sheet.
 */
add_action('wp_enqueue_scripts', 'recently_updated_posts_styles' );

function recently_updated_posts_styles() {
	wp_register_style('recently_updated_posts', plugins_url('recently-updated-posts-widget/style.css'));
	wp_enqueue_style('recently_updated_posts');
}