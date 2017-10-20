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
            messages: {
               test: function(connect){
                  console.log('testing: ', data)
               }
            }
         })
      </script>
   </body>
</html>
