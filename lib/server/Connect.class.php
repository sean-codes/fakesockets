<?php
//-----------------------------------------------------------------------------
// Incoming Message Functions
//-----------------------------------------------------------------------------
class Connect {

	private $socketID;
	private $sockets_folder;
	private $socket;
	private $timeout = 5;
	private $messages;
	private $data;
	private $room;

	public function __construct($socket, $room, $data, $methods){
		$this->data = $data;
		$this->methods = $methods;
		$this->socketID = $socket;
		$this->room = $room;
		$this->sockets_folder = dirname(__FILE__) . "/Sockets/{$this->room}/";
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
		$file_size = 10;

		while($file_size && $maxTimeout > time()){
			clearstatcache();
			$file_size = filesize($this->socket);
			if($file_size > 10){
				$lines = file($this->socket);
				array_shift($lines);
				file_put_contents($this->socket, time());
				$this->output($lines);
				return;
			}
			usleep(1000000);
		}
	}

	private function output( $data ) {
		echo $this->packet( $data );
	}

	public function broadcast( $data ) {
		$sockets = $this->get_sockets();
		foreach($sockets as $socket){
			$this->writeSocket($socket, $data);
		}
	}

	private function get_sockets(){
		$sockets = array_diff(scandir($this->sockets_folder), array('..', '.'));
		foreach($sockets as $key=>$val){
			$sockets[$key] = $this->sockets_folder . $val;
		}
		return $sockets;
	}

	private function writeSocket($socket, $data){
		$file = fopen($socket, 'r+');
		$last_poll = fread($file, 10);
		fseek($file, -0, SEEK_END);
		fwrite($file, PHP_EOL . $this->packet( $data ));
		fclose($file);
		if(time() - $last_poll > 20){
			unlink($socket);
		}
	}

	private function packet($data){
		return json_encode($data);
	}

	private function is_socket_dead($socket){
		return $socket . split('_');
	}

	private function socket_init(){
		$file = fopen($this->socket, 'c');
		fwrite($file, time());
		fclose($file);
	}
}

?>
