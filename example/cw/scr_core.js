objects = new Array();

function cube_script(){
	canvas = document.getElementById("canvas");
	ctx = canvas.getContext("2d");
	frame_rate = 1000/60;
	setTimeout(step, frame_rate);
	real_fps = 0;
	other_players = [];
	sprites_init()
}

function create(obj_name, x, y)
{
	//save the number before it changes from adding to the array
	obj_id = objects.length;
	//Add the object to the array
		//Add to the array objects[new id][object name][x][y][step]
	objects[obj_id] = new Array();
	objects[obj_id].object = obj_name;
	objects[obj_id].direction = obj_name.direction;
	objects[obj_id].gravity = obj_name.gravity
	objects[obj_id].x = x;
	objects[obj_id].y = y;	
	objects[obj_id].width = obj_name.width;
	objects[obj_id].height = obj_name.height;	
	
	return obj_id;
}

function destroy(obj)
{   
    objects[obj].object = "destroyed";
	//Remove an object from the array
	//delete from array objects[new id][object name]
	
}

function step()//should be the only event repeated ever! May split in future
{	
    //Run all the objects step events
	ctx.clearRect(0, 0, canvas.width, canvas.height);	
	//for(i=0; i<length of array;i+=1)
    for (var i=0; i < objects.length; i++) 
    {
		if (objects[i].object !== "destroyed")
		{
			//run step event for i
			id = i;
			objects[i].object.step(objects[i].x, objects[i].y, id);
		}
    }	
    setTimeout(step, frame_rate);
    
	//Hell to create realtime frame rate :(
    n_timer = new Date();
	n_rate = n_timer.getSeconds();
	if (typeof rate !== "undefined")
	{
		if(n_rate == rate)
		{
			frames += 1;
		}
		else
		{
			real_fps = frames;
			rate = n_rate;
			frames = 0;
		}
	}
	else 
	{
		rate = 0;
		framerate = 0;
	}

}

function sprites_init(){
	var imgs = document.querySelectorAll('img')
	console.log('Loading Images..')
	for(var i = 0; i < imgs.length; i++){
	
		var spr_name = imgs[i].getAttribute('data-name')
		console.log(spr_name)
		window[spr_name] = {
			img: imgs[i],
			frames: imgs[i].getAttribute('data-frames'),
			width: imgs[i].getAttribute('data-width'),
			height: imgs[i].getAttribute('data-height')
		}
	}
}

function draw_sprite(sprite, frame, x, y){
	var sx = sprite.width * ((frames < 0) ? frames % sprite.frames : 0)
	ctx.drawImage(sprite.img, sx, 0, sprite.width, sprite.height, x, y, sprite.width, sprite.height);
}

function draw_text(str, x, y)
{
	ctx.fillText(str, x, y);
}

function place_meeting(obj2, x, y)
{
	//check whether there is an instance of object 2 at the place of object 1 at x and y
	for (var i=0; i< objects.length; i++)
	{
		if (objects[i].object == obj2 && i !== id)
		{
			var obj1top = y;
			var obj1bottum = y + objects[id].height;
			var obj1left = x;
			var obj1right = x + objects[id].width;
			
			var obj2top = objects[i].y;
			var obj2bottum = objects[i].y + objects[i].height;
			var obj2left = objects[i].x;
			var obj2right = objects[i].x + objects[i].width;				
			
			if ( 	obj1bottum > obj2top &&
					obj1top < obj2bottum &&
					obj1left < obj2right &&
					obj1right > obj2left
				)
			{
				return true;
			}		
		}
	}		
}

key = new Array();
for (var i=0; i < 300; i++) 
{
	key[i] = false;
}
document.onkeydown = function(event)
{
	var keydown = event.which;
	key[keydown] = true;	
	if (keydown == 37 || keydown == 38 || keydown == 39 || keydown == 40 || keydown == 191 || keydown == 8)
	{
		return false;
	}

}
document.onkeyup = function(event)
{
	if (navigator.userAgent.toLowerCase().indexOf('chrome') > -1)
	{
		is_chrome = true;
	}
	
	key[event.which] = false;
    var keyup = event.which;
    if (keyup == 8 && is_chrome == false)
    {
        return false;
    }
}	
