var connect = new Connect({
	serverPath: './server/server.php',
	room: 'main',
	methods: {
		connect: function(data){
			console.log('connected: ' + data)
			game.connected = true
		},
		newplayer: function(data){
			var newPlayer = new Player(10, 10)
			newPlayer.uid = data.uid
			newPlayer.ghost = true
			newPlayer.x = data.x
			newPlayer.y = data.y
			game.objects.push(newPlayer)
		},
		delplayer: function(data){
			for(var player of game.objects){
				if(player.uid == data){
					player.dead = true
				}
			}
		},
		message: function(data){
			for(var player of game.objects){
				if(player.uid == data.uid){
					player.keys = data.keys
					player.x = data.x
					player.y = data.y
				}
			}
		}
	}
})

var game = {
	ctx: document.querySelector('canvas').getContext('2d'),
	objects: [],
	keys: [],
	init: function(){
		this.listen()
		this.objects.push(new Player(10, 10))

		this.render()
	},
	listen: function(){
		var that = this
		this.ctx.canvas.addEventListener('keydown', function(e){
			that.keys[e.keyCode] = true
		})
		this.ctx.canvas.addEventListener('keyup', function(e){
			that.keys[e.keyCode] = false
		})
	},
	render: function(){
		var that = this
		setTimeout(function(){ that.render() }, 1000/60)

		this.ctx.clearRect(0, 0, this.ctx.canvas.width, this.ctx.canvas.height)
		for(var object of this.objects){
			if(object.dead) continue
			object.step()
			this.ctx.drawImage(object.img, object.x, object.y)
		}
	},
	findObject: function(){

	},
	resetKeys: function(){
		var that = this
		this.keys.forEach(function(e, i){ console.log(e); that.keys[i] = false })
	}
}

var Player = function(x, y){
	this.img = document.querySelector('img')
	this.x = x
	this.y = y
	this.height = 16
	this.width = 16
	this.hspeed = 0
	this.vspeed = 1
	this.network = ''
	this.ghost = false
	this.keys = {}

	this.step = function(){

		keys = this.ghost ? this.keys : {
			up: game.keys[38],
			right: game.keys[39],
			left: game.keys[37]
		}

		if(this.y + this.height + this.vspeed <= game.ctx.canvas.height){
			if(this.vspeed < 6){
				this.vspeed += 0.5
			}
			this.y += this.vspeed
		} else {
			this.vspeed = 0
			if(keys.up){
				this.vspeed = -10
				this.y -= 2;
			}
		}

		if(keys.right && this.x+this.width < game.ctx.canvas.width){
			this.x += 2;
		}

		if(keys.left && this.x > 0){ this.x -= 2; }

		var newNetwork = game.keys[38]+'_'+game.keys[39]+'_'+game.keys[37]
		if(!this.ghost && game.connected && this.network != newNetwork){
			this.network = newNetwork
			connect.send({
				method: 'message',
				data: { keys: keys, x: this.x, y: this.y, uid: this.uid }
			})
		}
	}
}
