var html = {
   name: document.querySelector('messenger inputs [name=name]'),
   input: document.querySelector('messenger inputs [name=input]'),
   messages: document.querySelector('messages')
}

var connect = new Connect({
   serverPath: './resources/server.php',
   room: 'main',
   methods: {
      message: function(data){
         html.messages.innerHTML += `<message><name>${data.name}</name><text>: ${data.input}</text></message>`
         html.messages.scrollTop = html.messages.scrollHeight
      }
   }
})

html.input.addEventListener('keyup', function(e){
   if(e.keyCode == 13){ send() }
})

function send(){
   data = { name: html.name.value || 'noname', input: html.input.value}
   connect.send({ method: "message", data: data })
   html.input.value = ''
}
