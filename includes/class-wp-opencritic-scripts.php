<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class vou_Bedu_Scripts{

	

	/**
	 * Enqueue a script in the WordPress admin on edit.php.
	 *
	 * @param int $hook Hook suffix for the current admin page.
	 */
	function wp_opencritic_enqueue_admin_script( $hook ) {  		
	  		

	    	wp_enqueue_script( 'wp-opencritic-admin-scripts', plugin_dir_url( __FILE__ ) . 'js/wp-opencritic-admin-scripts.js', array(), '1.0' );
			wp_enqueue_script( 'select2', plugin_dir_url( __FILE__ ) . 'js/select2.min.js', array('jquery'), '1.0' );	

	    	wp_localize_script( 'wp-opencritic-admin-scripts' , 'WpOcAdmin' , array( 
				'ajaxurl' 		=> admin_url( 'admin-ajax.php', ( is_ssl() ? 'https' : 'http' ) ),
			));		
	    	
	    	wp_enqueue_style( 'wp-opencritic-admin-style', plugin_dir_url( __FILE__ ) . 'css/wp-opencritic-admin-style.css', array(), '1.0' );
	    	wp_enqueue_style( 'select2', plugin_dir_url( __FILE__ ) . 'css/select2.min.css', array(), '1.0' );

		// please create also an empty JS file in your theme directory and include it too
		wp_enqueue_script('mycustom', get_stylesheet_directory_uri() . '/mycustom.js', array( 'jquery', 'select2' ) ); 

	    
	    
	}


	public function add_hooks(){	
		add_action( 'admin_enqueue_scripts', array($this,'wp_opencritic_enqueue_admin_script'));
		
	}
}