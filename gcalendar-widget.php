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

        print '<section class="widget wdiget-gcalendar">';
        print '<p><a href="https://calendar.google.com/calendar/embed?src='.GCAL_CALENDAR_ID.'" target="_blank">'.__('Agenda','gcalendar').'</a></p>';
        if (count($results->getItems()) == 0) {
          print '<p>'.__('No upcoming events found.','gcalendar')."</p>";
        } else {
          print "<h3>".__('Upcoming events:','gcalendar')."</h3>";
          foreach ($results->getItems() as $event) {
            $start = $event->start->dateTime;
            if (empty($start)) {
              $start = $event->start->date;
            }
            $end = $event->end->dateTime;
            if (empty($end)) {
              $end = $event->end->date;
            }
            printf(__("From %s to %s",'gcalendar')."<br/>%s<br/><a href=\"%s\" target=\"_blank\">".__('Details','gcalendar')."</a><br/>", $start, $end,$event->getSummary(),$event->getHtmlLink());
          }
        }
        print "</section>";
    }
}