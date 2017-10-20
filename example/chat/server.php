<?php
//-----------------------------------------------------------------------------
// Handle Incoming Messages
//-----------------------------------------------------------------------------
   require '../../lib/server/Connect.class.php';

   // Read Data
   $uid = $_GET["uid"];
   error_log($_GET["data"]);
   $data = json_decode($_GET["data"]);

   $connect = new Connect($uid, $data, array(
      'test'=> function($connect, $data){
         $packet = Array( 'method'=>'test', 'data'=>$data );
         $connect->broadcast($packet);
      }
   ));
?>
