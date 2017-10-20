

function Connect(options){
	this.serverPath = options.serverPath
	this.messages = options.messages
	console.log(this.messages);
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
        	text += possible[Math.floor(Math.random() * possible.length)];
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
			console.log(this.messages)
			this.messages[parsed.method].call(this, parsed.data);
		}
	}

	this.poll = function(){
		if(this.long)
			this.send({ uid: this.uid, method: 'poll' })
	}

	this.response = new Response()
}
