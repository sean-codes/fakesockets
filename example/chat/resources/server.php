<?php
//-----------------------------------------------------------------------------
// Handle Incoming Messages
//-----------------------------------------------------------------------------
   require '../../../lib/server/Connect.class.php';

   // Read Data
   $connect = new Connect([
      'room' => $_GET['room'],
      'socket' => $_GET['socket'],
      'data' => json_decode($_GET['data']),
      'path' => 'Activity',
      'speed' => 1/30,
      'methods' => [
         'message' => function($connect, $socket, $data) {
            $connect->broadcast($data);
         }
      ],
      'connect' => function(){},
      'disconnect' => function(){}
   ]);
?>
