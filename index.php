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
         margin-bottom: 2rem;
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
         padding: 0.5rem;
         border-radius: 5px;
         display: block;
         margin: 0 auto;
         border: none;
      }

      #button a {
         text-decoration: none;
         color: white;
         padding: 0.5rem 3rem 0.5rem 3rem;
      }

      #form {
         margin-top: 1rem;
         display: flex;
         flex-direction: column;
         gap: 1rem;
      }

      input[type=text] {
         width: 50%;
         padding: 0.5rem;
         margin: 0 auto;
         display: block;
         border: 1px solid #ccc;
         border-radius: 5px;
         box-sizing: border-box;
      }

      input[type=password] {
         width: 50%;
         padding: 0.5rem;
         margin: 0 auto;
         display: block;
         border: 1px solid #ccc;
         border-radius: 5px;
         box-sizing: border-box;
      }

      input[type=submit] {
         width: 50%;
         background-color: #097969;
         color: white;
         padding: 0.5rem;
         margin: 0 auto;
         display: block;
         border: none;
         border-radius: 5px;
         cursor: pointer;
      }

      #logrequire {
         text-align: center;
         color: #097969;
      }

      #loginmsg {
         text-align: center;
         color: #C70039;
      }

      #log-out {
         text-align: right;
         padding: 1rem;
      }

      #log-out a {
         color: #097969;
      }
   </style>
</head>

<body>
   <?php
      session_start();

      if (isset($_GET['action']) and $_GET['action'] == 'logout') {
         session_destroy();
         session_start();
      }

      $message = '';
      if (isset($_POST['login']) && !empty($_POST['username']) && !empty($_POST['password'])) {
         if ($_POST['username'] == 'Username' && $_POST['password'] == '12345') {
            $_SESSION['logged_in'] = true;
            $_SESSION['username'] = $_POST['username'];
         } else {
            $message = 'Wrong username or password';
         }
      }
      ?>
      <h1 id="logrequire" <?php isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true
                              ? print("style = 'display: none'")
                              : print("style = 'display: block'") ?>>
         Please log in to look at PHP file browser </h1>
      <form action="" method="post" <?php isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true
                                       ? print("style = 'display: none'")
                                       : print("style = 'display: block'") ?>>
         <input type="text" name="username" placeholder="Username" required></br>
         <input type="password" name="password" placeholder="12345" required>
         <h4 id="loginmsg"><?php echo $message; ?></h4>
         <input type="submit" name="login" value="Login" formaction="./index.php">
      </form>

      <?php
      if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true) {
         $logOut = './?action=logout';
         print("<h3 id='log-out'><a href=" . $logOut . "> Logout</a></h3>");
         print("<h1>Current Directory: " . $_SERVER['REQUEST_URI'] . "</h1>");
         print("<h1> Current directory:" . str_replace("?path=./", "", $_SERVER['REQUEST_URI']) . "</h1>");
         print("<table id='table'><tr><th>Type</th><th>Name</th><th>Action</th></tr>");

         if (isset($_GET["path"])) {
            $path = $_SERVER['REQUEST_URI'];
         } else {
            $path =  $_SERVER['REQUEST_URI'] . '?path=';
         }
         if (isset($_POST['createDir']) && (!file_exists($_POST['createDir']))) {
            mkdir("./" . $_GET["path"] . "/" . $_POST['createDir']);
         }

         $dir = "./";
         $dirPath = $dir . $_GET["path"];
         $dirArr = scandir($dirPath);
         $dirArr = array_diff($dirArr, array(".", ".."));
         $dirArr = array_values($dirArr);

         foreach ($dirArr as $dirValue) {
            if (is_dir($dirPath . $dirValue)) {
               print("<tr><td>Folder</td><td><a href=" . $path . $dirValue . "/" . ">" . $dirValue . "</a></td><td></td></tr>");
            } else if (is_file($dirPath . $dirValue)) {
               print("<tr><td>File</td><td>$dirValue</td><td></td></tr>");
            }
         }

         print("</table>");
         $backPath = "?path=" . dirname($_GET["path"]) . "/";
         print("<button id='button'><a href=" . $backPath . ">Back</a></button>");
         print("<form id='form' method='POST' action=''><input type='text' name='createDir' id='input'><input type='submit' value='Create'></form>");
      }
   ?>
</body>

</html>