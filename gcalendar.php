<?php
/*
Plugin Name: Google Calendar Reader
Plugin URI: https://github.com/ThePoloQ/wp-gcalendar
Description: Read a google calendar
Version: 0.1
Author: PoloQ
Author URI: https://github.com/ThePoloQ
License: GPL3
Text Domain: gcalendar
*/

/*
error_reporting(E_ALL);
ini_set('display_errors', 1);
*/

include_once plugin_dir_path( __FILE__ ).'/gcalendar-widget.php';

class gCalendar
{
  public function __construct()
  {
     add_action('widgets_init', function(){register_widget('gCalendar_Widget');});
     add_action( 'wp_enqueue_scripts', 'gcalendar_styles',11 );
     add_action( 'plugins_loaded', 'gcalendar_load_textdomain' );
     add_filter( 'query_vars', 'gcalendar_add_query_vars_filter' );
  }
}

new gCalendar();

function gcalendar_styles() {
  wp_register_style('gcalendar-css', plugins_url('gcalendar.css',__FILE__ ));
  wp_enqueue_style('gcalendar-css');
}


function gcalendar_load_textdomain() {
  load_plugin_textdomain( 'gcalendar', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' ); 
}

function gcalendar_add_query_vars_filter( $vars ){
  $vars[] = "gPageToken";
  $vars[] = "gPrevPageToken";
  return $vars;
}