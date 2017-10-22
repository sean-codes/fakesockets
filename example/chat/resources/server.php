<?php
//-----------------------------------------------------------------------------
// Handle Incoming Messages
//-----------------------------------------------------------------------------
   require '../../../lib/server/Connect.class.php';

   // Read Data
   $uid = $_GET["uid"];
   $room = $_GET["room"];
   $data = json_decode($_GET["data"]);
   $path = 'Activity';

   // Do the darn thing!
   $connect = new Connect($uid, $room, $data, $path, array(
      'message'=> function($connect, $data){
         $connect->broadcast($data);
      }
   ));
?>
