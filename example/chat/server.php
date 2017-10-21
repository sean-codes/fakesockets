<?php
//-----------------------------------------------------------------------------
// Handle Incoming Messages
//-----------------------------------------------------------------------------
   require '../../lib/server/Connect.class.php';

   // Read Data
   $uid = $_GET["uid"];
   $room = $_GET["room"];
   $data = json_decode($_GET["data"]);

   $connect = new Connect($uid, $room, $data, array(
      'test'=> function($connect, $data){
         $packet = Array( 'method'=>'test', 'data'=>$data );
         $connect->broadcast($packet);
      }
   ));
?>
