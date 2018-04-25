<?php
  require  '../vendor/autoload.php';

  $options = array(
    'cluster' => 'eu',
    'encrypted' => true,
    'notification_host' => 'nativepush-cluster1.pusher.com'
  );

  $app_id = 'fe5f2288c17c6ab6257c';
  $app_key = 'da864772ef221df0ae9c';
  $app_secret = '515684';

  $pusher = new \Pusher\Pusher(
    $app_id,
    $app_key,
    $app_secret,
    $options
  );

  $data = array(
  'apns' => array(
    'aps' => array(
      'alert' => array(
        'body' => 'tada'
      ),
    ),
  ),
  'gcm' => array(
    'notification' => array(
      'title' => 'title',
      'icon' => 'icon'
    ),
  ),
);

$pusher->notify(array("test"), $data);

/*
  $data['message'] = 'vadd world';
  $pusher->trigger('my-channel', 'my-event', $data);
*/
?>


<!DOCTYPE html>
<head>
  <title>Pusher Test</title>
  <script src="https://js.pusher.com/4.1/pusher.min.js"></script>
  <script>

    // Enable pusher logging - don't include this in production
    Pusher.logToConsole = true;

    var pusher = new Pusher('fe5f2288c17c6ab6257c', {
      cluster: 'eu',
      encrypted: true
    });

    var channel = pusher.subscribe('my-channel');
    channel.bind('my-event', function(data) {
      alert(data.message);
    });
  </script>
</head>
