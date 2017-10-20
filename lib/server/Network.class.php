<?php
//-----------------------------------------------------------------------------//
// Network Functions
//-----------------------------------------------------------------------------//
class Network {
	public function instant($method, $data){
		$response[0] = array( 'method' => $method, 'data' => $data );
		echo json_encode($response);
	}

	public function broadcast($client_uid, $id, $goods){
		$activity_files = scandir( dirname(__FILE__) . "/" . "Activity" );
		for($i = 0; $i < count($activity_files); $i++){
			$activity_file_path =  dirname(__FILE__) . "/" . "Activity/" . $activity_files[$i];
			if ($activity_files[$i] !== ".." && $activity_files[$i] !== "."){
				$active = file($activity_file_path);
				//If old needs to get the KO
				if( abs($active[0] - time()) > 45 && stristr($activity_files[$i], ".info") == false ){
					unlink($activity_file_path);
					unlink($activity_file_path . ".info");
					$player = str_split($activity_files[$i]);
					$player = array_slice($player, (count($player) - 5));
					$player = implode($player);
					send_broadcast($client_uid, 4, $player);
				} else {
					if (stristr($activity_files[$i], ".info") == false) {
						$activity_file = fopen( $activity_file_path, "a");
						fwrite($activity_file, "\r\n" . $client_uid . ">" . $id . ">" . $goods);
						fclose($activity_file);
					}
				}
			}
		}
	}

	public function multicast($client_uid, $id, $goods)
	{
		global $client_activity;
		$activity_files = scandir( dirname(__FILE__) . "/" . "Activity" );
		for($i = 0; $i < count($activity_files); $i++)
		{
			$activity_file_path =  dirname(__FILE__) . "/" . "Activity/" . $activity_files[$i];
			if ($activity_files[$i] !== ".." && $activity_files[$i] !== "." && $activity_file_path !== $client_activity)
			{
				$active = file($activity_file_path);
				//If old needs to get the KO
				if( abs($active[0] - time()) > 45 && stristr($activity_file_path, ".info") == false)
				{
					unlink($activity_file_path);
					unlink($activity_file_path . ".info");
					$player = str_split($activity_files[$i]);
					$player = array_slice($player, (count($player) - 5));
					$player = implode($player);
					send_broadcast($client_uid, 4, $player);
				}
				else
				{
					if (stristr($activity_files[$i], ".info") == false)
					{
						$activity_file = fopen( $activity_file_path, "a");
						fwrite($activity_file, "\r\n" . $client_uid . ">" . $id . ">" . $goods);
						fclose($activity_file);
					}
				}
			}
		}
	}
}
?>
