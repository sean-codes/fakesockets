<?php
//-----------------------------------------------------------------------------
// Incoming Message Functions
//-----------------------------------------------------------------------------
require 'Network.class.php';

class Connect {

	private $socketID;
	private $sockets_folder;
	private $socket;
	private $timeout = 5;

	public function __construct($socket){
		$this->network = new Network();
		$this->socketID = $socket;
		$this->sockets_folder = dirname(__FILE__) . "/Sockets/";
		$this->socket =  $this->sockets_folder . $this->socketID;

		$this->socket_init();
		return;
	}

	public function listen(){
		error_log('Starting Listen...' . $this->socket);
		$maxTimeout = $this->timeout + time();
		$file_size = 0;

		while(!$file_size && $maxTimeout > time()){
			error_log('Listening...' . $this->socket . ' File Size: ' . $file_size);
			clearstatcache();
			$file_size = filesize($this->socket);
			if($file_size > 0){
				$lines = file($this->socket);
				file_put_contents($this->socket, '');
				$this->output($lines);
				error_log('Read/Clear...');
				return;
			}
			usleep(1000000);
		}

		error_log('restate');
	}

	private function output($data){
		echo $this->packet( $data );
	}

	private function broadcast($data){
		$sockets = $this->get_sockets();
		foreach($sockets as $socket){
			$this->writeSocket($socket, $data);
		}
		$line = $this->packet( $data );
	}

	private function writeSocket($socket, $data){
		$file = fopen($socket, $test);
	}

	private function packet($data){
		return json_encode($data);
	}

	private function socket_init(){
		if(!file_exists($this->socket)){
			error_log('Creating Socket: ' . $this->socket);
			file_put_contents($this->socket, '');
		}
	}

	private function generate_socket(){
		$possible_keys = "1234567890qwertyuiopasdfghjklzxcvbnm";
		$key_length = 5;
		$key = "";
		for($i = 0; $i < $key_length; $i++){
			$key = $key . substr($possible_keys, mt_rand(0, strlen($possible_keys)-1), 1);
		}
		return $key;
	}
}

?>
