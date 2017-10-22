var connect = new Connect({
	serverPath: './server/server.php',
	room: '0',
	methods: {
		message: function(data){
			html.messages.innerHTML += `<message><name>${data.name}</name><text>: ${data.input}</text></message>`
			html.messages.scrollTop = html.messages.scrollHeight
		}
	}
})


var game = {
	ctx: document.querySelector('canvas').getContext('2d'),
	objects: [],
	keys: [],
	init: function(){
		this.listen()
		this.objects.push(new player(10, 10))

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
			object.step()
			this.ctx.drawImage(object.img, object.x, object.y)
		}
	},
	resetKeys: function(){
		var that = this
		this.keys.forEach(function(e, i){ console.log(e); that.keys[i] = false })
	}
}

var player = function(x, y){
	this.img = document.querySelector('img')
	this.x = x
	this.y = y
	this.height = 16
	this.width = 16
	this.hspeed = 0
	this.vspeed = 1
	this.network = ''

	this.step = function(){
		if(this.y + this.height + this.vspeed <= game.ctx.canvas.height){
			if(this.vspeed < 6){
				this.vspeed += 0.5
			}
			this.y += this.vspeed
		} else {
			this.vspeed = 0
			if(game.keys[38]){
				this.vspeed = -10
				this.y -= 2;
			}
		}

		if(game.keys[39] && this.x+this.width < game.ctx.canvas.width){
			this.x += 2;
		}

		if(game.keys[37] && this.x > 0){ this.x -= 2; }

		var newNetwork = game.keys[38]+'_'+game.keys[39]+'_'+game.keys[37]
		if(newNetwork !== this.network){
			this.network = newNetwork
		}
		console.log(newNetwork)
	}
}
