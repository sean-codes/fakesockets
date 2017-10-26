<?php
//-----------------------------------------------------------------------------
// Incoming Message Functions
//-----------------------------------------------------------------------------
require 'Rooms.class.php';

class Connect {

	public $room;
	public $rooms;
	private $socket;
	private $path_rooms;
	private $path_room;
	private $timeout = 5;
	private $socketTimeout = 10;
	private $connect;
	private $disconnect;
	private $methods;
	private $packet;
	private $speed;

	public function __construct($settings) {
		$this->path = $settings['path'];
		$this->rooms = new Rooms($this->path);
		$this->rooms->create( $this->room );

		$this->disconnect = $settings['disconnect'];
		$this->connect = $settings['connect'];
		$this->methods = $settings['methods'];
		$this->socket = $settings['socket'];
		$this->packet = $settings['packet'];
		$this->room = $settings['room'];
		$this->speed = $settings['speed'];

		$this->socket_open();
		$this->parse();
		return;
	}

	private function parse() {
		if(empty($this->packet)) {
			$this->data_listen();
		} else {
			$method = $this->packet->method;
			$this->methods[$method]($this, $this->socket, $this->packet->data);
		}
	}

	public function data_listen() {
		$path_socket = $this->rooms->socket_path($this->socket);
		$maxTimeout = $this->timeout + time();
		while($this->data_check( $path_socket ) && $maxTimeout > time()){
			usleep(1000000*$this->speed);
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

	public function broadcast( $socket, $data ) {
		$socket_room = $this->rooms->socket_find( $socket );

		error_log( 'Broadcast: ' . $socket . ' room: ' . $socket_room );
		$sockets = $this->rooms->sockets_in_room( $socket_room );
		foreach( $sockets as $socket ){
			$this->socket_write( $socket, $data );
		}
	}

	public function multicast( $socket, $data ) {
		$socket_room = $this->rooms->socket_find( $socket );
		error_log( 'multicast: ' . $socket . ' room: ' . $socket_room );
		$sockets = $this->rooms->sockets_in_room( $socket_room );

		foreach( $sockets as $send_socket ){
			if( $socket != $send_socket ){
				$this->socket_write( $send_socket, $data );
			}
		}
	}

	public function unicast( $socket, $data ) {
		error_log('unicasting: ' . json_encode($data));
		$this->socket_write( $socket, $data );
	}

	private function socket_write( $socket, $data ) {
		$socket_path = $this->rooms->socket_path( $socket );
		if( empty( $socket_path ) || !file_exists( $socket_path ) ) return;
		// Open file and read last poll
		$file_socket = fopen($socket_path, 'r+');
		flock($file_socket, LOCK_EX);
		$socket_last_poll = time() - fread($file_socket, 10);

		// Send
		fseek($file_socket, 0, SEEK_END);
		fwrite($file_socket, PHP_EOL . json_encode( $data ));
		fclose($file_socket);

		// Close if old socket
		if($socket_last_poll > $this->socketTimeout){ $this->socket_close( $socket ); }
	}

	private function socket_open() {
		$socket_path = $this->rooms->socket_path( $this->socket );
		$connect = false;

		if( empty( $socket_path ) ) {
			$socket_path = $this->rooms->socket_new( $this->room, $this->socket);
			$connect = true;
		}

		$file = fopen($socket_path, 'c');
		fwrite($file, time());
		fclose($file);

		if($connect){
			call_user_func_array($this->connect, [ $this, $this->socket ]);
		}
	}

	private function socket_change_room( $socket, $room_new ) {
		$path_old = $this->rooms->socket_path( $socket );
		$this->rooms->socket_move( $socket, $room );
		$path_new = $this->rooms->socket_path( $socket );
		rename($path_old, $path_new);
	}

	private function socket_close( $socket ) {
		unlink( $this->rooms->socket_path( $socket ));
		call_user_func_array($this->disconnect, [ $this, $socket ]);
		if( $this->rooms->socket_close( $socket ) ) {
			error_log('Disconnect Successful: ' . $socket);
		}
	}
}
?>
