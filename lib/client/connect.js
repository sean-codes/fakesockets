function Connect(options){
	this.serverPath = options.serverPath
	this.methods = options.methods
	this.room = options.room
	this.initialize = function(){
		this.uid = this.createUID();
		this.request = {
			long: new Request({ path: this.serverPath, connect: this, long: true }),
			short: new Request({ path: this.serverPath, connect: this })
		}
		this.request.long.poll()
	}

	this.send = function(data){
		this.request.short.send(data)
	}

	this.createUID = function(){
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
	console.log(options.connect)
	this.xhttp.onreadystatechange = () => {
		if( this.xhttp.readyState == 4 ){
			this.receiveData(this.xhttp.responseText)
		}
	}

	this.send = function(data){
		if(!data) data = {}
		this.xhttp.open("GET", this.buildRequestURL(data), true);
		this.xhttp.send();
	}

	this.buildRequestURL = function(data){
		return this.connectPath
			+ "?uid=" + this.connect.uid
			+ "&room=" + this.connect.room
			+ "&data=" + JSON.stringify(data)
	}

	this.receiveData = function(responseText){
		console.log('Received Data: ' + this.xhttp.responseText);
		if(this.xhttp.responseText.trim().length){
			var responseParsed = JSON.parse(this.xhttp.responseText);
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
