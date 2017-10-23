<?php
//-----------------------------------------------------------------------------
// Handle Incoming Messages
//-----------------------------------------------------------------------------
   require '../../../lib/server/Connect.class.php';

   // Do the darn thing!
   $connect = new Connect([
      'room' => $_GET['room'],
      'socket' => $_GET['socket'],
      'data' => json_decode($_GET['data']),
      'path' => './Activity',
      'speed' => 1/30,
      'methods' => [
         'message' => function($connect, $socket, $data) {
            session_id($socket);
            session_start();
            $data->uid = $_SESSION['uid'];
            error_log($_SESSION['uid']);
            session_write_close();
            $connect->multicast($data);
         }
      ],
      'connect' => function($connect, $socket) {
         session_id($socket);
         session_start();
         $_SESSION['uid'] = time();
         session_write_close();
      },
      'disconnect' => function($connect, $socket, $data) {
         $connect->broadcast($data);
      }
   ]);
?>
