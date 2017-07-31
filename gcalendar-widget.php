<?php

require_once plugin_dir_path( __FILE__ ) . '/google-api-php-client/vendor/autoload.php';
require_once plugin_dir_path( __FILE__ ) . '/gcalendar-param.php';

class gCalendar_Widget extends WP_Widget
{
    public function __construct()
    {
        parent::__construct('gcalendar', 'gCalendar', array('description' => 'Display next events'));
    }

    public function widget($args, $instance)
    {
	$gcal_opts = get_option( 'gcalendar_reader_settings');
        $client = new Google_Client();
        $client->setApplicationName($gcal_opts['gcalendar_reader_app_name']);
        $client->setDeveloperKey($gcal_opts['gcalendar_reader_api_key']);

        $service = new Google_Service_Calendar($client);

        $calendarId = $gcal_opts['gcalendar_reader_calendar_id'];

	$gPageToken = get_query_var( 'gPageToken', null );

        $optParams = array(
          'maxResults' => $gcal_opts['gcalendar_reader_max_results'],
          'orderBy' => 'startTime',
          'singleEvents' => TRUE,
          'timeMin' => date('o-m-d').'T00:00:00'.date('P'),
          'pageToken' => $gPageToken,
        );

	
        if (false && $gPageToken){
          $optParams = array(
	    'pageToken' => $gPageToken,
	  );
	}

        $results = $service->events->listEvents($calendarId, $optParams);

        $date_format = get_option( 'date_format' );
        $time_format = get_option( 'time_format' );  

        print '<section class="widget wdiget-gcalendar">';
        $events = $results->getItems();
        if (count($events) == 0) {
          print '<p>'.__('No upcoming events found.','gcalendar')."</p>";
        } else {
          //<span class=\"dashicons dashicons-calendar-alt\"></span>&nbsp;
          print "<h3 class=\"widget-title\" style=\"display: table\">".__('Upcoming Events','gcalendar')."</h3>";
          print "<div class=\"gcalendar-events\">";
          foreach ($events as $event) {
            print '<div class="gcalendar-event">';
            print '<div class="gcalendar-event-date">';

            $dtFormat = DateTime::RFC3339;
            $dFormat = 'Y-m-d';
            $hOffset = get_option('gmt_offset');
            $dtStart = $event->getStart()->getDateTime();
            
            if ($dtStart !== NULL){
              $dStart = DateTime::createFromFormat($dtFormat, $dtStart);    
              $start = date_i18n(get_option('date_format'), $dStart->getTimestamp() + $hOffset * 3600) .' @ '. date_i18n(get_option('time_format'), $dStart->getTimestamp() + $hOffset * 3600,true);
            }else{
              $dStart = DateTime::createFromFormat('Y-m-d', $event->getStart()->getDate());
              $start = date_i18n('d M', $dStart->getTimestamp());
            }

            $dtEnd = $event->getEnd()->getDateTime();
            if ($dtEnd !== NULL){
              $dEnd = DateTime::createFromFormat($dtFormat, $dtEnd );
              $end = date_i18n(get_option('date_format'), $dEnd->getTimestamp() + $hOffset * 3600) .' @ '. date_i18n(get_option('time_format'), $dEnd->getTimestamp() + $hOffset * 3600,true);
            }else{
              $dEnd = DateTime::createFromFormat('Y-m-d', $event->getEnd()->getDate());
              $end = date_i18n('d M', $dEnd->getTimestamp() - 3600*24); //goggle calculate : minus one day
            }
            
            if (strcmp($end,$start) !==0 ){
              if (strcmp(date_i18n('d M', $dStart->getTimestamp()),date_i18n('d M', $dEnd->getTimestamp())) == 0){ //tow hour with the same day
                printf ('<div class="gcalendar-event-date-up">%s</div><div class="gcalendar-event-date-down">%s</div>',date_i18n('d', $dStart->getTimestamp()),date_i18n('M', $dEnd->getTimestamp()));
              }else if ($dtStart === NULL){ //event on all days
                printf ('<div class="gcalendar-event-date-up">%s</div><div class="gcalendar-event-date-down">%s</div>',date_i18n('d M', $dStart->getTimestamp()),date_i18n('d M', $dEnd->getTimestamp() - 3600*24));
              }else{ //tow hour and diff day
                printf ('<div class="gcalendar-event-date-up">%s</div><div class="gcalendar-event-date-down">%s</div>',date_i18n('d M', $dStart->getTimestamp()),date_i18n('d M', $dEnd->getTimestamp()));
              }
            }else{
              printf ('<div class="gcalendar-event-date-up">%s</div><div class="gcalendar-event-date-down">%s</div>',date_i18n('d', $dStart->getTimestamp()),date_i18n('M', $dStart->getTimestamp()));
            }
            
            print '</div>';
            print '<div class="gcalendar-event-desc">';
            print '<div class="gcalendar-event-desc-sum">';
            printf ("<a href=\"%s\" target=\"_blank\" title=\"%s\">%s</a>",$event->getHtmlLink(),esc_attr($event->getSummary()),esc_attr($event->getSummary()));
            print '</div>';
            print '<div class="gcalendar-event-desc-time">';
              if (strcmp($end,$start) !==0 ){
                if (strcmp(date_i18n('d M', $dStart->getTimestamp()),date_i18n('d M', $dEnd->getTimestamp())) == 0){ //tow hour with the same day
                  printf (__('From %s to %s','gcalendar'),date_i18n('H:i', $dStart->getTimestamp() + $hOffset * 3600),date_i18n('H:i', $dEnd->getTimestamp() + $hOffset * 3600));
                }else if ($dtStart !== NULL){ //tow hour and diff day
                  printf (__('From %s to %s','gcalendar'),date_i18n('d M, H:i', $dStart->getTimestamp() + $hOffset * 3600),date_i18n('d M, H:i', $dEnd->getTimestamp() + $hOffset * 3600));
                }
              }
            print '</div>';
            print '</div>';
            print '<div class="gcalendar-event-clear"></div>';
            print '<div class="gcalendar-event-more">';
            printf ("<a href=\"%s\" target=\"_blank\" title=\"%s\"><b>+</b></a>",$event->getHtmlLink(),__('Read more','gcalendar').' : '.esc_attr($event->getSummary()));
            print '</div>';            
            print '</div>';
          }
          print '</div>';
          $pageToken = $results->getNextPageToken();
          $gPrevPageToken = get_query_var( 'gPrevPageToken', null );
          $gPrevPage = "";
          $nextTextAlign = "";
          if ($gPageToken) { $gPrevPage = "&gPrevPageToken=".$gPageToken;}
	  if ($gPrevPageToken || $gPageToken) { $nextTextAlign ="text-align:right;";}

          if ($gPrevPageToken || $pageToken)  { print "<div>"; }
	  if ($gPrevPageToken || $gPageToken) { print "<div style=\"width:50%;float:left;\"><a href=\"?gPageToken=".$gPrevPageToken."\">".__('Previous events','gcalendar')."</a></div>";}
          if ($pageToken) { print "<div style=\"width:50%;float:left;".$nextTextAlign."\"><a href=\"?gPageToken=".$pageToken.$gPrevPage."\">".__('Next events','gcalendar')."</a></div>";}
          if ($gPrevPageToken || $pageToken)  { print "</div>"; }

        }
        print '<div class="gcalendar-cal-link"><a href="https://calendar.google.com/calendar/embed?src='.$gcal_opts['gcalendar_reader_calendar_id'].'" title="'.__('Google Agenda','gcalendar').'" target="_blank">'.__('Agenda','gcalendar').'</a></div>';
        print "</section>";

    }
}