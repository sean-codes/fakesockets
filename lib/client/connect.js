

function Connect(options){
	this.serverPath = options.serverPath
	this.initialize = function(){
		this.uid = this.createUID();
		this.request = {
			long: new Request({ path: this.serverPath, uid: this.uid, long: true }),
			short: new Request({ path: this.serverPath, uid: this.uid })
		}
		this.request.long.send({ method: 'poll' })
	}

	this.send = function(data){
		this.request.short.send(data)
	}

	this.createUID = function(){
		var text = "";
    var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
    for(var i = 0; i < 6; i++) {
        text += possible.charAt(Math.floor(Math.random() * possible.length));
    }
    return text;
	}

	this.initialize();
}

function Request(options = {}){
	this.connectPath = options.path + '/connect.php';
	this.long = options.long;
	this.uid = options.uid
	this.xhttp = new XMLHttpRequest()

	this.xhttp.onreadystatechange = () => {
		if( this.xhttp.readyState == 4 ){
			this.receiveData(this.xhttp.responseText)
		}
	}

	this.send = function(data){
		this.xhttp.open("GET", this.connectPath + "?uid="+this.uid+"&data=" + JSON.stringify(data), true);
		this.xhttp.send();
	}

	this.receiveData = function(responseText){
		console.log('Receiving Data: ' + this.xhttp.responseText);
		var responseParsed = JSON.parse(this.xhttp.responseText);
		this.response.parse(responseParsed)

		this.poll(responseParsed.length)
	}

	this.poll = function(responseLength){
		if(this.long)
			this.send({ uid: this.uid, method: 'poll', read: responseLength })
	}

	this.response = new Response()
}

function Response(data){
	this.parse = function(data){
		for(var dataPart of data){
			this.in[dataPart.method](dataPart.data)
		}
	}

	this.in = {
		ok: function(data){
			console.log('sent successful')
		},
		uid: function(data){
			console.log(data)
			uid = data
		},
		movement: function(data){
			console.log(data)
			split_message = get_response[2].split("~");
			if ( typeof(other_players[get_response[0]]) == "undefined")
			{
				identifier = create(obj_ghost, split_message[1], split_message[2]);
				other_players[get_response[0]] = [];
				other_players[get_response[0]].oid = identifier;
				objects[identifier].uid = get_response[0];
			}

			var other = objects[other_players[get_response[0]].oid]
			other.direction = split_message[0];
			other.x = Number(split_message[1]);
			other.y = Number(split_message[2]);
			other.up = split_message[4];
			other.down = split_message[6];
			other.left = split_message[3];
			other.right = split_message[5];
			other.gravity = Number(split_message[7]);
		},
		text: function(data){
			console.log(data)
		},
		disconnect: function(data){
			console.log(data)
			if (typeof (other_players[get_response[2]]) !== "undefined"){
				destroy(other_players[get_response[2]].oid);
			}
		}
	}
}
