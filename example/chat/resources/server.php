<?php
//-----------------------------------------------------------------------------
// Handle Incoming Messages
//-----------------------------------------------------------------------------
   require '../../../lib/server/Connect.class.php';

   // Read Data
   $connect = new Connect([
      'room' => $_GET['room'],
      'socket' => $_GET['socket'],
      'packet' => json_decode($_GET['packet']),
      'path' => 'Activity',
      'speed' => 1/30,
      'methods' => [
         'message' => function($connect, $socket, $data) {
            $connect->broadcast($socket, ['method'=>'message', 'data'=>$data]);
         }
      ],
      'connect' => function(){},
      'disconnect' => function(){}
   ]);
?>
