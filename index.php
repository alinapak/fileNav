<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Document</title>
   <style>
      #table {
         width: 100%;
         margin-bottom:2rem;
      }

      #table td,
      #table th {
         border: 1px solid #ddd;
         padding: 0.5rem;
         text-align: center;
         background-color: #E8E8E8;
      }

      #table th {
         background-color: #097969;
         color: white;
      }

      #button {
         background-color: #097969;
         padding:0.5rem;
         border-radius:5px;
         display:block;
         margin:0 auto;
         border: none;
      }
      #button a{
         text-decoration: none;
         color:white;
         padding:0.5rem 3rem 0.5rem 3rem;
      }
   </style>
</head>

<body>
   <?php
   print("<h1>Current Directory: " . $_SERVER['REQUEST_URI'] . "</h1>");
   print("<h1> Current directory:" . str_replace("?path=./", "", $_SERVER['REQUEST_URI']) . "</h1>");
   print("<table id='table'><tr><th>Type</th><th>Name</th><th>Action</th></tr>");
   if (isset($_GET["path"])) {
      $path = $_SERVER['REQUEST_URI'];
   } else {
      $path =  $_SERVER['REQUEST_URI'] . '?path=';
   }
   $dir = "./";
   $dirPath = $dir . $_GET["path"];
   $dirArr = scandir($dirPath);
   $dirArr = array_diff($dirArr, array(".", ".."));
   $dirArr = array_values($dirArr);

   foreach ($dirArr as $dirValue) {
      if (is_dir($dirPath . $dirValue)) {
         print("<tr><td>Folder</td><td><a href=" . $path . $dirValue . "/" . ">$dirValue</a></td><td></td></tr>");
      } else if (is_file($dirPath . $dirValue)) {
         print("<tr><td>File</td><td>$dirValue</td><td></td></tr>");
      }
   }
   // print($_GET["path"]);
  
   print("</table>");
$backPath = "?path=".dirname($_GET["path"])."/";
   print("<button id='button'><a href=".$backPath.">Back</a></button>");
   ?>
</body>

</html>