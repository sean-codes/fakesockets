<!DOCTYPE HTML>
<html>
   <head>
      <!-- Include functions to connect to server -->
      <script src='../../lib/client/Connect.js'></script>
   </head>
   <body>
      <script>
         var connect = new Connect({
            serverPath: 'server.php',
            room: '0',
            methods: {
               test: function(data){
                  console.log('testing: ', data)
               }
            }
         })
      </script>
   </body>
</html>
