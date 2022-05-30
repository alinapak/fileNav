<!DOCTYPE html>
<html lang="en">

<head>
   <link rel="stylesheet" href="./style.css">
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Document</title>
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
      <h4 class='message'><?php echo $message; ?></h4>
      <input type="submit" name="login" value="Login" formaction="./index.php">
   </form>

   <?php
      if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true) {
         $logOut = './?action=logout';
         print("<h3 id='log-out'><a href=" . $logOut . "> <i class='fa-solid fa-arrow-right-from-bracket'></i> Quit</a></h3>");
         print("<h1>Current Directory: " . $_SERVER['REQUEST_URI'] . "</h1>");
         print("<h1> Current directory:" . str_replace("?path=./", "", $_SERVER['REQUEST_URI']) . "</h1>");

         if (isset($_POST['download'])) {
            $file = './' . $_POST['download'];
            $fileToDownloadEscaped = str_replace("&nbsp;", " ", htmlentities($file, 0, 'utf-8'));
            print($fileToDownloadEscaped);
            ob_clean();
            ob_start();
            header('Content-Disposition: attachment; filename=' . basename($fileToDownloadEscaped));
            header('Content-Length: ' . filesize($fileToDownloadEscaped)); // kiek baitų browseriui laukti, jei 0 - failas neveiks nors bus sukurtas
            ob_end_flush();
            readfile($fileToDownloadEscaped);
            exit;
         }
         $error = "";
         $success = "";
         if (isset($_FILES['file'])) {
            $file_name = $_FILES['file']['name'];
            $file_size = $_FILES['file']['size'];
            $file_tmp = $_FILES['file']['tmp_name'];
            $file_name_arr = explode('.', $_FILES['file']['name']);
            $file_sup = strtolower(end($file_name_arr));
            $supports = array("jpeg", "jpg", "png", "txt");
            if (in_array($file_sup, $supports) === false) {
               $error = 'Upload is not allowed, please choose a JPEG,PNG or TXT file.';
            }
            if ($file_size >= 2097152) {
               $error = 'File size must be smaller or equal to 2 MB';
            }
            if (empty($error) == true) {
               move_uploaded_file($file_tmp, "./" . $_GET["path"] . $file_name);
               $success = "File successfully uploaded";
            } else {
               $error;
            }
         }
         print("
            <form id='upload' action ='' method ='post' enctype = 'multipart/form-data'>
               <input type ='file' name ='file' />
               <input type ='submit' value= 'Upload'/>
            </form>
            <h2 class='message'>$error</h2>
            <h2 class='message'>$success</h2>
         ");
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
               $_POST["download"] = $dirValue;
               print("<tr><td>File</td><td>$dirValue</td>
                  <td><form action=" . $path . $dirValue . " method='POST'>
                  <button id='download' name='download' value='$dirValue'>Download</button>
                  </form>
                  </td></tr>");
            }
         }

         print("</table>");
         $backPath = "?path=" . dirname($_GET["path"]) . "/";
         print("<button id='button'><a href=" . $backPath . ">Back</a></button>");
         print("<form id='form' method='post' action=''><input type='text' name='createDir'placeholder='Type new directory name here' ><input type='submit' value='Create'></form>");
      }
   ?>
   <script src="https://kit.fontawesome.com/255176e611.js" crossorigin="anonymous"></script>
</body>

</html>