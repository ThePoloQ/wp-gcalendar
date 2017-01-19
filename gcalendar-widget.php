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
        $client = new Google_Client();
        $client->setApplicationName(GCAL_APPLICATION_NAME);
        $client->setDeveloperKey(GCAL_APP_KEY);

        $service = new Google_Service_Calendar($client);

        // Print the next 10 events on the user's calendar.
        $calendarId = GCAL_CALENDAR_ID;
        $optParams = array(
          'maxResults' => GCAL_MAX_RESULTS,
          'orderBy' => 'startTime',
          'singleEvents' => TRUE,
          'timeMin' => date('c'),
        );
        $results = $service->events->listEvents($calendarId, $optParams);

        $date_format = get_option( 'date_format' );
        $time_format = get_option( 'time_format' );  

        print '<section class="widget wdiget-gcalendar">';
        print '<span><a href="https://calendar.google.com/calendar/embed?src='.GCAL_CALENDAR_ID.'" target="_blank">'.__('Agenda','gcalendar').'</a></span>';
        if (count($results->getItems()) == 0) {
          print '<p>'.__('No upcoming events found.','gcalendar')."</p>";
        } else {
          print "<h3 class=\"widget-title\" style=\"display: table\">".__('Upcoming Events','gcalendar')."</h3>";
          print "<div class=\"gcalendar-events\">";
          foreach ($results->getItems() as $event) {
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
              $start = date_i18n(get_option('date_format'), $dStart->getTimestamp());
            }

            $dtEnd = $event->getEnd()->getDateTime();
            if ($dtEnd !== NULL){
              $dEnd = DateTime::createFromFormat($dtFormat, $dtEnd );
              $end = date_i18n(get_option('date_format'), $dEnd->getTimestamp() + $hOffset * 3600) .' @ '. date_i18n(get_option('time_format'), $dEnd->getTimestamp() + $hOffset * 3600,true);
            }else{
              $dEnd = DateTime::createFromFormat('Y-m-d', $event->getEnd()->getDate());
              $end = date_i18n(get_option('date_format'), $dEnd->getTimestamp() - 3600*24);
            }
            
            if ($end != $start)            
              printf (__("From %s to %s",'gcalendar'),$start, $end);
            else
              printf (__("The %s",'gcalendar'),$start, $end);
            
            print '</div>';
            print '<div class="gcalendar-event-desc">';
            printf ("%s",$event->getSummary());
            print '</div>';
            print '<div class="gcalendar-event-more">';
            printf ("<a href=\"%s\" target=\"_blank\">".__('View More ...','gcalendar')."</a>",$event->getHtmlLink());
            print '</div>';
            print '</div>';
          }
          print '</div>';
        }
        print "</section>";
    }
}
