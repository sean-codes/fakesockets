<?php
//-----------------------------------------------------------------------------
// Incoming Message Functions 
//-----------------------------------------------------------------------------
require '_Net.class.php';

class In {
	
	private $net;
	private $uid;
	private $data;
	
	public function __construct($uid, $data){
		$this->net = new Net();
		// Set the Data
		$this->data = $data;
		$this->uid = $uid;
		$this->method = $data->method;
		
		// Call the method
		$this->method();
	}
	
	public function uid($uid){
		$uid = $this->net->init_player($uid);
		$this ->net->instant('uid', $uid);
	}

	public function poll(){
		//Rid of known messages
		if(file_exists($client_activity)){
			$known_activity = file($client_activity);
			$known_activity = array_slice($known_activity, $message + 1);
			$activity_file = fopen($client_activity, "w");
			if(count($known_activity) > 0){
				fwrite($activity_file, time() . "\r\n");
				for($i = 0; $i < count($known_activity); $i++){
					fwrite($activity_file, $known_activity[$i]);
				}
			} else {
				fwrite($activity_file, time());
			}
		}
		else
		{
			$activity_file = fopen($client_activity, "w");
			fwrite($activity_file, time());
		}
		fclose($activity_file);
		//Poll!
		$poll_time = time();
		do
		{
			$unknown_activity = file($client_activity);
		}
		while(count($unknown_activity) == 1 && abs($poll_time - time()) < 25);
		//Send messages!
		$response = '';
		for($i = 1; $i < count($unknown_activity); $i++)
		{
			$response = $response . rtrim($unknown_activity[$i], "\r\n") . "#";
		}
		send_response(1, $response);
	}

	public function data(){
		$file = fopen($path_activity . $uid, "w");
		$info = explode("~", $message);
		for($i = 0; $i < count($info); $i++)
		{
			fwrite($file, $info[$i] . "\r\n");
		}
		fclose($file);

		$this -> $net -> send_multicast($client_uid, 3, $message);
		$this -> $net -> end_broadcast($client_uid, 2, $message);
	}
}

?>
