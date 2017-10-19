<!DOCTYPE HTML>

<html>
	<head>
			<title>Cube-Script</title>
			<link rel="stylesheet" type="text/css" href="css.css" />
		
			<!-- Load Objects and Sprites -->
			<?php include 'scr_load.php'; ?>
	</head>
	<body>

			<!--Create canvas-->
			<canvas width="500" height="200" id="canvas"></canvas>

			<script type="text/javascript">
					cube_script();   
					//Create objects
					x = 0;
					while( x < canvas.width){
							create(obj_block, x, canvas.height - obj_block.height);
							x += obj_block.width;
					}
					create(obj_player, 20, 20);  
					create(obj_console, 0, 0);
			</script>
	</body>
</html>
