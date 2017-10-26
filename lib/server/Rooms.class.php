<?php
//-----------------------------------------------------------------------------
// Room Functions
//-----------------------------------------------------------------------------
class Rooms {
	private $rooms;
   private $path;

	public function __construct( $path ) {
      $this->path = $path . '/';
		$this->rooms = $this->get_rooms();
		return;
	}

	public function create( $room ) {
      $path = $this->path . $room;
		if ( !file_exists( $path ) ) {
    		mkdir( $path, 0777, true );
		}
	}

	public function socket_move( $socket, $room ) {
		$current_room = $this->socket_find( $socket );

		if( $current_room && $current_room !== $room ) {
         array_splice(
            $this->rooms[$current_room],
            array_search( $this->rooms[$current_room], $socket), 1 );
         array_push( $this->room[$room], $socket );
		}
	}

	public function socket_new( $room_id="", $socket ) {
      array_push( $this->rooms[$room_id], $socket );
      $path = $this->socket_path( $socket );
      error_log('SOCKET NEW socket: ' . $socket . ' room: ' . $room_id . ' path: ' . $path);
      return $path;
   }

	public function socket_close( $socket ) {
      $room = $this->socket_find( $socket );
		if( !empty( $room ) ) {
			$this->rooms[$room] = array_diff($this->rooms[$room], [$socket]);
			return true;
		}

		return false;
   }

   public function socket_path( $socket ) {
      $room_id = $this->socket_find( $socket );
      if( !empty( $room_id ) ) {
         return $this->path . $room_id . "/$socket";
      }
      return '';
   }

	public function socket_find( $socket ) {
		foreach( $this->rooms as $room_id => $room ) {
			if( $this->socket_in_room( $room_id, $socket ) ) {
				return $room_id;
			}
		}
      return '';
	}

   private function socket_in_room( $room_id, $socket ) {
      $sockets_search = array_search( $socket, $this->rooms[$room_id], true );
      return empty( $sockets_search ) ? false : true;
   }

	public function sockets_in_room( $room_id ) {
		return $this->rooms[$room_id];
	}

	private function get_rooms() {
		$room_ids = array_diff(scandir($this->path), array('..', '.', '.DS_Store'));
      $rooms_array = [];
      foreach( $room_ids as $room_id ){
         $sockets_in_room = array_diff(scandir($this->path . $room_id), array('..', '.', '.DS_Store'));
         $rooms_array[$room_id] = $sockets_in_room;
      }
      return $rooms_array;
	}
}
?>
