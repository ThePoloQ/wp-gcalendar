<?php

add_action( 'admin_menu', 'gcalendar_reader_add_admin_menu' );
add_action( 'admin_init', 'gcalendar_reader_settings_init' );

$gcal_default = array ('gcalendar_reader_app_name' => 'googleapplicationame', 'gcalendar_reader_api_key' => 'googleapikey','gcalendar_reader_calendar_id' => 'calendaridentifier@group.calendar.google.com', 'gcalendar_reader_max_results' => 6);

function gcalendar_reader_add_admin_menu(  ) { 

	add_menu_page( 'Google Calendar Reader', 'Google Calendar Reader', 'manage_options', 'gcalendar-reader', 'gcalendar_reader_options_page' );

}

function gcalendar_reader_settings_init(  ) { 

	register_setting( 'pluginPage', 'gcalendar_reader_settings' );

	add_settings_section(
		'gcalendar_reader_pluginPage_section', 
		__( '', 'gcalendar' ), 
		'gcalendar_reader_settings_section_callback', 
		'pluginPage'
	);

	add_settings_field( 
		'gcalendar_reader_app_name', 
		__( 'Google Calendar Application Name', 'gcalendar' ), 
		'gcalendar_reader_app_name_render', 
		'pluginPage', 
		'gcalendar_reader_pluginPage_section' 
	);

	add_settings_field( 
		'gcalendar_reader_api_key', 
		__( 'Google Calendar API key', 'gcalendar' ), 
		'gcalendar_reader_api_key_render', 
		'pluginPage', 
		'gcalendar_reader_pluginPage_section' 
	);

	add_settings_field( 
		'gcalendar_reader_calendar_id', 
		__( 'Google Calendar ID', 'gcalendar' ), 
		'gcalendar_reader_calendar_id_render', 
		'pluginPage', 
		'gcalendar_reader_pluginPage_section' 
	);

	add_settings_field( 
		'gcalendar_reader_max_results', 
		__( 'Max results', 'gcalendar' ), 
		'gcalendar_reader_max_results_render', 
		'pluginPage', 
		'gcalendar_reader_pluginPage_section' 
	);


}


function gcalendar_reader_app_name_render(  ) { 

	global $gcal_default;

	$options = get_option( 'gcalendar_reader_settings',$gcal_default );
	?>
	<input type='text' name='gcalendar_reader_settings[gcalendar_reader_app_name]' size='35' value='<?php echo $options['gcalendar_reader_app_name']; ?>'><br/>
	<i><u>Value</u>: google_application_name</i>
	<?php

}


function gcalendar_reader_api_key_render(  ) { 

	global $gcal_default;

	$options = get_option( 'gcalendar_reader_settings',$gcal_default );
	?>
	<input type='text' name='gcalendar_reader_settings[gcalendar_reader_api_key]' size='35' value='<?php echo $options['gcalendar_reader_api_key']; ?>'><br/>
	<i><u>Value</u>: gcalendar_reader_api_key</i>
	<?php

}


function gcalendar_reader_calendar_id_render(  ) { 

	global $gcal_default;

	$options = get_option( 'gcalendar_reader_settings',$gcal_default );
	?>
	<input type='text' name='gcalendar_reader_settings[gcalendar_reader_calendar_id]' size='35' value='<?php echo $options['gcalendar_reader_calendar_id']; ?>'><br/>
	<i><u>Value</u>: calendaridentifier@group.calendar.google.com</i>
	<?php

}


function gcalendar_reader_max_results_render(  ) { 

	global $gcal_default;

	$options = get_option( 'gcalendar_reader_settings',$gcal_default );
	?>
	<input type='text' name='gcalendar_reader_settings[gcalendar_reader_max_results]' size='2' value='<?php echo $options['gcalendar_reader_max_results']; ?>'><br/>
	<i><u>Value</u>: 5</i>
	<?php

}


function gcalendar_reader_settings_section_callback(  ) { 

	echo __( '', 'gcalendar' );

}


function gcalendar_reader_options_page(  ) { 

	?>
	<form action='options.php' method='post'>

		<h2>Google Calendar Reader</h2>

		<?php
		settings_fields( 'pluginPage' );
		do_settings_sections( 'pluginPage' );
		submit_button();
		?>

	</form>
	<?php

}
