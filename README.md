# Wordpress Plugin to read a Google Calendar

## 1. Prerequisites

### 1. Google API PHP Client

[Download](https://github.com/google/google-api-php-client/) and install google-api-php-client in plugin root path

[Releases](https://github.com/google/google-api-php-client/releases)

### 2. Google Calendar API

Make your calendar public : [Guide](https://docs.simplecalendar.io/make-google-calendar-public/)

Get the calendar ID : [Guide](https://docs.simplecalendar.io/find-google-calendar-id/)

Generate a Google Calendar API KEY : [Guide](https://docs.simplecalendar.io/google-api-key/)

## 2. Edit parameters

```
File: gcalendar-param.php
```

```php
define('GCAL_APPLICATION_NAME', 'APP_NAME');
define('GCAL_APP_KEY', ' GOOGLE_APP_KEY');
define('GCAL_CALENDAR_ID','GOOGLE_ID@group.calendar.google.com');
```
