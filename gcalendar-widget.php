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
        $client->setDeveloperKey(GCAL_API_KEY);

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
        if (count($results->getItems()) == 0) {
          print '<p>'.__('No upcoming events found.','gcalendar')."</p>";
        } else {
          //<span class=\"dashicons dashicons-calendar-alt\"></span>&nbsp;
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
            printf ("<a href=\"%s\" target=\"_blank\" title=\"%s\">%s</a>",$event->getHtmlLink(),$event->getSummary(),$event->getSummary());
            print '</div>';
            print '<div class="gcalendar-event-desc-time">';
              if (strcmp($end,$start) !==0 ){
                if (strcmp(date_i18n('d M', $dStart->getTimestamp()),date_i18n('d M', $dEnd->getTimestamp())) == 0){ //tow hour with the same day
                  printf (__('From %s to %s'),date_i18n('H:i', $dStart->getTimestamp()),date_i18n('H:i', $dEnd->getTimestamp()));
                }else if ($dtStart !== NULL){ //tow hour and diff day
                  printf (__("From %s to %s"),date_i18n('d M, H:i', $dStart->getTimestamp()),date_i18n('d M, H:i', $dEnd->getTimestamp()));
                }
              }
            print '</div>';
            print '</div>';
            print '<div class="gcalendar-event-clear"></div>';
            print '<div class="gcalendar-event-more">';
            printf ("<a href=\"%s\" target=\"_blank\" title=\"%s\"><b>+</b></a>",$event->getHtmlLink(),__('Read more','gcalendar'));
            print '</div>';            
            print '</div>';
          }
          print '</div>';
        }
        print '<div class="gcalendar-cal-link"><a href="https://calendar.google.com/calendar/embed?src='.GCAL_CALENDAR_ID.'" title="'.__('Google Agenda','gcalendar').'" target="_blank">'.__('Agenda','gcalendar').'</a></div>';
        print "</section>";
    }
}