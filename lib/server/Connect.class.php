<?php
//-----------------------------------------------------------------------------
// Incoming Message Functions
//-----------------------------------------------------------------------------
class Connect {

	private $socket;
	private $path_rooms;
	private $path_room;
	private $timeout = 5;
	private $messages;
	private $data;
	private $room;

	public function __construct($socket, $room, $data, $path, $methods) {
		$this->path = $path;
		$this->path_rooms = "$path/";
		$this->path_room = $this->path_rooms . $room . '/';
		$this->socket = $socket;
		$this->methods = $methods;
		$this->room = $room;
		$this->data = $data;

		$this->room_create($this->path_room);
		$this->socket_init();
		$this->parse();
		return;
	}

	private function parse() {
		if(empty($this->data)) {
			$this->data_listen();
		} else {
			$method = $this->data->method;
			$this->methods[$method]($this, $this->data);
		}
	}

	public function data_listen() {
		$path_socket = $this->path_room . $this->socket;
		$maxTimeout = $this->timeout + time();
		while($this->data_check( $path_socket ) && $maxTimeout > time()){
			usleep(1000000);
		}
	}

	private function data_check( $path_socket ) {
		clearstatcache();
		if(!file_exists($path_socket)){ return; }
		$file_size = filesize($path_socket);
		if($file_size > 10){
			$this->output_data( $path_socket );
			return false;
		}
		return true;
	}

	private function output_data( $path_socket ) {
		$lines = array_slice(file($path_socket), 1);
		echo json_encode( $lines );
		file_put_contents($path_socket, time());
	}

	public function broadcast( $data ) {
		$sockets = $this->get_sockets($this->room);
		foreach($sockets as $socket){
			$this->socket_write($socket, $data);
		}
	}

	private function socket_write($socket, $data) {
		// Open file and read last poll
		$file_socket = fopen($this->path_room . $socket, 'r+');
		$socket_last_poll = time() - fread($file_socket, 10);

		// Send
		fseek($file_socket, 0, SEEK_END);
		fwrite($file_socket, PHP_EOL . json_encode( $data ));
		fclose($file_socket);

		// Close if old socket
		if($socket_last_poll > 20){ $this->socket_close($this->room, $socket); }
	}

	private function socket_init() {
		$this->room_set();
		$file = fopen($this->path_room . $this->socket, 'c');
		fwrite($file, time());
		fclose($file);
	}

	private function room_create($path_room) {
		if (!file_exists($path_room)) {
    		mkdir($path_room, 0777, true);
		}
	}

	private function room_set() {
		foreach($rooms = $this->get_rooms() as $room){
			if($room != $this->room){
				$sockets_in_room = $this->get_sockets($room);
				$socket_search = array_search($this->socket, $sockets_in_room);
				if($socket_search){
					$this->socket_move($room, $this->room, $this->socket);
					return;
				}
			}
		}
	}

	private function socket_move($room_old, $room_new, $socket) {
		$path_old = $this->path_rooms . "$room_old/$socket";
		$path_new = $this->path_rooms . "$room_new/$socket";
		rename($path_old, $path_new);
	}

	private function socket_close($room, $socket) {
		unlink($this->path_rooms . $room . '/' . $socket);
	}

	private function get_rooms() {
		return array_diff(scandir($this->path_rooms), array('..', '.'));
	}

	private function get_sockets($room) {
		return array_diff(scandir($this->path_rooms . $room), array('..', '.'));
	}
}
?>
