<?php
/*
* Plugin Name: Recently updated posts widget
* Description: The latests posts and pages updated (which are not the most recent).
* Author: Luciole135
* Version: 1.4.1
* Author URI: http://additifstabac.free.fr/
* Plugin URI: http://additifstabac.free.fr/index.php/recently-updated-posts-widget/
* Text Domain: recently-updated-posts-domain 
* Domain Path: /languages
* License: GPL2
*/

// don't load directly
if (!function_exists('is_admin')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}

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
    function __construct() {
 
                $widget_args = array("classname" => 'recently_updated_posts',
							"description" => __('The latests posts and pages updated (which are not the most recent).','recently-updated-posts-domain'),);
		
                parent::__construct('recently_updated_posts', 
									__('Recently updated posts',
									'recently-updated-posts-domain'), $widget_args);
        }
		
    function widget( $args, $instance ) {
		global $wpdb;
		extract( $args );

		/* User-selected settings. */
		$title = apply_filters('widget_title',$instance['title'] );
		$nb_display = $instance['nb_display'];

		/* Before widget (defined by themes). */
		echo $before_widget;

		/* Title of widget (before and after defined by themes). */
		if($title)
			echo $before_title . $title . $after_title;
			
		echo recently_updated_posts_transient($nb_display,$instance);
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
    $instance['title'] = sanitize_text_field($new_instance['title']);
    $instance['nb_display'] =(isset($new_instance['nb_display'])? absint($new_instance['nb_display']): 5) ;
	$instance['show_date'] = $new_instance['show_date'];
 
    return $instance;
    }
	
	function form($instance) {
	global $wpdb;
	// Affichage des paramètres du widget dans l'admin
    $title = esc_attr($instance['title']);
    $nb_display = esc_attr($instance['nb_display']);
	$show_date = $instance['show_date'];
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
		<p>
            <?php _e('Display post date: ','recently-updated-posts-domain'); ?>
				<br/><input type='radio' 
						id="<?php echo $this->get_field_id('show_date'); ?>_1"
						name="<?php echo $this->get_field_name('show_date'); ?>" 
						value='tooltip' 
						<?php checked( $show_date == 'tooltip', true) ?> />
				<label for="<?php echo $this->get_field_id('show_date'); ?>_1"><?php _e('on the ToolTip','recently-updated-posts-domain'); ?> 
				</label> 
				<br/><input 	type='radio'
						id="<?php echo $this->get_field_id('show_date'); ?>_3"
						name="<?php echo $this->get_field_name('show_date'); ?>" 
						value='after' 
						<?php checked( $show_date == 'after', true) ?>/>
				<label for="<?php echo $this->get_field_id('show_date'); ?>_3"><?php _e('after the title','recently-updated-posts-domain'); ?>
				</label>
				<br/><input 	type='radio'
						id="<?php echo $this->get_field_id('show_date'); ?>_2"
						name="<?php echo $this->get_field_name('show_date'); ?>" 
						value='below' 
						<?php checked( $show_date == 'below', true) ?>/>
				<label for="<?php echo $this->get_field_id('show_date'); ?>_2"><?php _e('below the title','recently-updated-posts-domain'); ?>
				</label>
				
				<br/><input 	type='radio'
						id="<?php echo $this->get_field_id('show_date'); ?>_4"
						name="<?php echo $this->get_field_name('show_date'); ?>" 
						value='none' 
						<?php checked( $show_date == 'none', true) ?>/>
				<label for="<?php echo $this->get_field_id('show_date'); ?>_4"><?php _e('do not display','recently-updated-posts-domain'); ?>
				</label>
        </p>
    <?php
    }
}

function recently_updated_posts_transient($nb_display,$instance){   
    // Le transient est-il inexistant ?
    if (false === ($transient=get_transient('widget_recently_updated_posts'))) {

        // Si oui, nous donnons une valeur au futur transient.
        $value = requete_dernier_article($nb_display,$instance);

        // Nous mettons à jour la valeur du transient avec $value, sans délai d'expiration
        set_transient('widget_recently_updated_posts',$value);

        // Nous mettons à jour la valeur du $transient
        $transient = get_transient('widget_recently_updated_posts');
    }
    //Nous renvoyons la valeur du transient mis à jour
    return $transient; 
}

function requete_dernier_article($nb_display,$instance) {
		global $wpdb;
		/* Nous allons ici récupérer les dernière articles mis à jour qui ne sont pas les derniers écrits */
        $last_update = $wpdb->get_results("SELECT post_modified, post_title, id
										FROM $wpdb->posts
										WHERE post_type <> 'revision' AND post_type <> 'attachment' AND post_type <> 'nav_menu_item' AND post_type<>'tablepress_table' 
										AND post_type <> 'acp-order-summary' AND post_type <> 'acp-links' AND post_type<>'acp-orders' AND post_status = 'publish' 
										AND id NOT IN (SELECT * FROM (SELECT id 
																	  FROM $wpdb->posts
																	  WHERE post_type <> 'revision' AND post_type <> 'attachment' AND post_type <> 'nav_menu_item' AND post_type<>'tablepress_table' AND post_status = 'publish'
																	  ORDER BY post_date DESC
																	  LIMIT $nb_display)
														AS temp)
										ORDER BY post_modified DESC
										LIMIT $nb_display");	
		
		$display= '<ul>';								
		foreach ($last_update as $post)
            { 
			$tooltip = $date = $below='';
			if ($instance['show_date']=='below') $below='<br/>';
			if (($instance['show_date'] == 'below')	or 	($instance['show_date'] == 'after'))		
				$date = date_i18n(get_option('date_format'),strtotime($post->post_modified));
			elseif ($instance['show_date'] == 'tooltip')
				$tooltip =date_i18n(get_option('date_format'),strtotime($post->post_modified)).__(' at&nbsp;','recently-updated-posts-domain').date_i18n(get_option('time_format'),strtotime($post->post_modified));
			$display.= "<li><a href=".get_permalink($post->id)." title='$tooltip' >".$post->post_title."</a>$below<span style='white-space: nowrap;'> $date</span></li>";
			}
		$display.= '</ul>';

		return $display;
		}

/**
* Register style sheet.
* décommenter les lignes 197 à 208 pour utiliser le style.css personnalisé inclus dans ce plugin, sinon ajouter une classe CSS dans votre thème enfant appelée .recently_updated_posts{ }
*/
/*
add_action('wp_enqueue_scripts','recently_updated_posts_styles' );

function recently_updated_posts_styles() {
	wp_register_style('recently_updated_posts', plugins_url('recently-updated-posts-widget/style.css'));
	wp_enqueue_style('recently_updated_posts');
}
*/

