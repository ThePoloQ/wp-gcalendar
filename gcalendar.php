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

error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once plugin_dir_path( __FILE__ ).'/gcalendar-widget.php';

class gCalendar
{
  public function __construct()
  {
     add_action('widgets_init', function(){register_widget('gCalendar_Widget');});
     add_action( 'init', 'gcalendar_styles' );
  }
}

new gCalendar();

function gcalendar_styles() {
  wp_register_style('gcalendar-css', plugins_url('gcalendar.css',__FILE__ ));
  wp_enqueue_style('gcalendar-css');
}