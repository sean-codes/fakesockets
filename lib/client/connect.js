function Connect(options){
	this.serverPath = options.serverPath
	this.methods = options.methods
	this.room = options.room
	this.initialize = function(){
		this.socket = this.createSocketID();
		this.request = {
			long: new Request({ path: this.serverPath, connect: this, long: true }),
			short: new Request({ path: this.serverPath, connect: this })
		}
		this.request.long.poll()
	}

	this.send = function(data){
		this.request.short.send(data)
	}

	this.createSocketID = function(){
		var text = "";
    	var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
    	for(var i = 0; i < 6; i++) {
        	text += possible[Math.floor(Math.random() * possible.length)];
    	}
    	return text;
	}

	this.initialize();
}

function Request(options = {}){
	this.connectPath = options.path + '/connect.php'
	this.connect = options.connect
	this.long = options.long;

	this.xhttp = new XMLHttpRequest()
	this.xhttp.onreadystatechange = () => {
		if( this.xhttp.readyState == 4 ){
			this.receiveData(this.xhttp.responseText)
		}
	}

	this.send = function(data){
		if(!data) data = ''
		this.xhttp.open("GET", this.buildRequestURL(data), true);
		this.xhttp.send();
	}

	this.buildRequestURL = function(data){
		return this.connectPath
			+ "?socket=" + this.connect.socket
			+ "&room=" + this.connect.room
			+ "&packet=" + JSON.stringify(data)
	}

	this.receiveData = function(responseText){
		if(responseText.trim().length){
			console.log('Recieving Data: ' + responseText);
			var responseParsed = JSON.parse(responseText);
			this.parse(responseParsed)
		}
		this.poll()
	}

	this.parse = function(data){
		for(var dataPart of data){
			var parsed = JSON.parse(dataPart)
			this.connect.methods[parsed.method](parsed.data);
		}
	}

	this.poll = function(){
		if(this.long) this.send()
	}
}
