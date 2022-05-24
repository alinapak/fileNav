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
   </style>
</head>

<body>
   <?php
      print("<h1>Current Directory: " . $_SERVER['REQUEST_URI'] . "</h1>");
      print("<h1> Current directory:" . str_replace("?path=", " ", $_SERVER['REQUEST_URI']) . "</h1>");
      print("<table id='table'><tr><th>Type</th><th>Name</th><th>Action</th></tr>");
      $dir = "./";
      $dirPath = $dir . $_GET["path"];
      $dirArr = scandir($dirPath);
      $dirArr = array_diff($dirArr, array(".", ".."));
      $dirArr = array_values($dirArr);

      foreach ($dirArr as $dirValue) {
         if (is_dir($dirValue)) {
            $newPath =  $_SERVER['REQUEST_URI'] . '?path=' . $dirValue . "/";
            print("<tr><td>Folder</td>");
            print("<td><a href=" . $newPath . ">$dirValue</a></td></tr>");
            // print("</tr>");
         } else if (is_file($dirValue)) {
            print("<tr><td>File</td><td>$dirValue</td></tr>");
         }
      }
      // print($_GET["path"]);
      print("</table>");
   ?>
</body>

</html>