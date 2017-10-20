<?php
//-----------------------------------------------------------------------------
// Handle Incoming Messages
//-----------------------------------------------------------------------------
   require '../../lib/server/Connect.class.php';

   // Read Data
   $uid = $_GET["uid"];
   $data = json_decode($_GET["data"]);

   $connect = new Connect($uid);
   $message = new Messages($connect, $data);

   class Messages {
      // Messages

      private $connect;
      private $data;

      function __construct($connect, $data){
         $this->connect = $connect;
         $this->data = $data;
         error_log('Incoming Data: ' . json_encode($data));
         $this->parse();
      }

      function parse(){
         $method = 'method_' . $this->data->method;
         $this->$method();
      }

      function method_poll(){
         $this->connect->listen();
      }

      function method_data(){
         $packet = Array( 'method'=>'test', 'data'=>'wtf' );
         $this->connect->broadcast($packet);
      }
   }
?>
