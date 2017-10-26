<?php
//-----------------------------------------------------------------------------
// Handle Incoming Messages
//-----------------------------------------------------------------------------
   require '../../../lib/server/Connect.class.php';

   // Do the darn thing!
   $connect = new Connect([
      'room' => $_GET['room'],
      'socket' => $_GET['socket'],
      'packet' => json_decode($_GET['packet']),
      'path' => './Activity',
      'speed' => 1/30,
      'methods' => [
         'message' => function($connect, $socket, $data) {
            error_log('Message Socket: ' . json_encode($data) );
            session_id($socket);
            session_start();
            $data->uid = $_SESSION['uid'];
            $_SESSION['x'] = $data->x;
            $_SESSION['y'] = $data->y;
            session_write_close();
            $connect->multicast($socket, ['method'=>'message', 'data'=>$data]);
         }
      ],
      'connect' => function($connect, $socket) {
         $uid = time();
         session_id($socket);
         session_start();
         $_SESSION['uid'] = $uid;
         $_SESSION['x'] = 0;
         $_SESSION['y'] = 0;
         session_write_close();
         $data = [ 'uid'=> $uid, 'x'=>0, 'y'=>0 ];

         $connect->unicast($socket, [ 'method' => 'connect', 'data' => $uid ]);
         $connect->multicast($socket, [ 'method' => 'newplayer', 'data' => $data ]);

         $room = $connect->rooms->socket_find( $socket );
         $otherplayers = $connect->rooms->sockets_in_room( $room );
         foreach( $otherplayers as $other ) {
            error_log('updating other players: ' . $other);
            session_id($other);
            session_start();
            $data = [ 'uid'=>$_SESSION['uid'], 'x'=>$_SESSION['x'], 'y'=>$_SESSION['y'] ];
            session_write_close();

            if( $other !== $socket ) {
               $connect->unicast($socket, [ 'method' => 'newplayer', 'data' => $data ]);
            }
         }
      },
      'disconnect' => function($connect, $socket) {
         error_log('Disconnect Socket');
         session_id($socket);
         session_start();
         $uid = $_SESSION['uid'];
         session_write_close();
         $connect->multicast($socket, [ 'method' => 'delplayer', 'data' => $uid ]);
      }
   ]);
?>
