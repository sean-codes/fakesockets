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
	private $messages;
	private $data;

	public function __construct($socket, $data, $methods){
		$this->data = $data;
		$this->methods = $methods;
		$this->socketID = $socket;
		$this->sockets_folder = dirname(__FILE__) . "/Sockets/";
		$this->socket =  $this->sockets_folder . $this->socketID;
		$this->socket_init();
		$this->parse();
		return;
	}

	private function parse(){
		if(empty($this->data)){
			$this->listen();
		} else {
			$method = $this->data->method;
			error_log($method);
			$this->methods[$method]($this, $this->data);
		}
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

	public function broadcast($data){
		$sockets = $this->get_sockets();
		foreach($sockets as $socket){
			$this->writeSocket($socket, $data);
		}
		error_log('broadcasting');
	}

	private function get_sockets(){
		$sockets = array_diff(scandir($this->sockets_folder), array('..', '.'));
		foreach($sockets as $key=>$val){ $sockets[$key] = $this->sockets_folder . $val; }
		return $sockets;
	}

	private function writeSocket($socket, $data){
		error_log('writing to socket: ' . $socket);
		$file = fopen($socket, 'a');
		fwrite($file, $this->packet( $data ));
		fclose($file);
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
