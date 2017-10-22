<!DOCTYPE HTML>
<html>
   <head>
      <!-- Make it look right -->
      <link rel="stylesheet" href="resources/style.css">
   </head>
   <body>

      <!-- Structure the App -->
      <content>
         <messenger>
            <messages></messages>
            <inputs>
               <input name="name" placeholder="Name">
               <input name="input" placeholder="Type message here! :]">
               <input type="button" value="Send" onclick="sendMessage()">
            </inputs>
         </messenger>
      </content>

      <!-- The App Scripts -->
      <script src='../../lib/client/Connect.js'></script>
      <script src="resources/script.js"></script>
   </body>
</html>
