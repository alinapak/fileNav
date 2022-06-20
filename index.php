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
   <h1 class="header" <?php isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true
                           ? print("style = 'display: none'")
                           : print("style = 'display: block'") ?>>Please log in to look at PHP file browser
   </h1>
   <form action="" method="POST" <?php isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true
                                    ? print("style = 'display: none'")
                                    : print("style = 'display: block'") ?>>
      <input type="text" name="username" placeholder="Username" required></br>
      <input type="password" name="password" placeholder="12345" required>
      <h4 class='message'><?php echo $message; ?></h4>
      <input type="submit" name="login" value="Login" formaction="./">
   </form>

   <?php
      if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true) {
         $logOut = './?action=logout';
         print("<h3 id='log-out'><a href=" . $logOut . "> <i class='fa-solid fa-arrow-right-from-bracket'></i> Quit</a></h3>");
         print("<h1 class='header'>Welcome to file browser!</h1>");
         // rawurlencode() function returns a string in which all non-alphanumeric characters except -_.~ have been replaced with a percent (%) sign followed by two hex digits.
         print("<h2 class='header'> Current directory:" . rawurldecode(str_replace("?path=./", "", $_SERVER['REQUEST_URI'])) . "</h2>");
         if (isset($_POST['download'])) {
            $strExplode = explode(" ", $_POST['download']);
            array_splice($strExplode, 0, 1);
            $pathEnd = implode(" ", $strExplode);
            $file = './' . $_GET["path"] . " " . $pathEnd;
            $fileToDownloadEscaped = str_replace("&nbsp;", " ", htmlentities($file, 0, 'utf-8'));
            ob_clean();
            ob_start();
            header('Content-Disposition: attachment; filename=' . basename($fileToDownloadEscaped));
            header('Content-Length: ' . filesize($fileToDownloadEscaped));
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
         $deleteMsg = '';
         $warning = '';
         if (isset($_POST['delete']) && $_POST['delete'] !== 'index.php' && $_POST['delete'] !== 'style.css' && $_POST['delete'] !== 'README.md') {
            $file = './' . $_GET['path'] . $_POST['delete'];
            if (file_exists($file)) {
               unlink($file);
            }
         } else if (isset($_POST['delete']) && ($_POST['delete'] === 'index.php' || $_POST['delete'] === 'style.css' || $_POST['delete'] === 'README.md')) {
            $deleteMsg = 'This file can not be deleted!';
         }
         print("<table id='table'>
                        <tr>
                           <th>Type</th>
                           <th>Name</th>
                           <th>Action</th>
                        </tr>");
         if (isset($_GET["path"])) {
            $path = $_SERVER['REQUEST_URI'];
         } else {
            $path =  $_SERVER['REQUEST_URI'] . '?path=';
         }
         if (isset($_GET["path"])) {
            if (isset($_POST['createDir']) && !file_exists("./" . $_GET["path"] . "/" . $_POST['createDir'])) {
               mkdir("./" . $_GET["path"] . "/" . $_POST['createDir']);
            } else if (isset($_POST['createDir']) && file_exists("./" . $_GET["path"] . "/" . $_POST['createDir'])) {
               $warning = 'Directory named "' . $_POST['createDir'] . '" already exists';
            }
         }
         else {
            if (isset($_POST['createDir']) && !file_exists("./" . $_POST['createDir'])) {
               mkdir("./" . $_POST['createDir']);
            } else if (isset($_POST['createDir']) && file_exists("./" . $_POST['createDir'])) {
               $warning = 'Directory named "' . $_POST['createDir'] . '" already exists';
            }
         }
         $dir = "./";
         if (isset($_GET["path"])) {
            $dirPath = $dir . $_GET["path"];
            $dirArr = scandir($dirPath);
            $dirArr = array_diff($dirArr, array(".", ".."));
            $dirArr = array_values($dirArr);

            foreach ($dirArr as $dirValue) {
               if (is_dir($dirPath . $dirValue)) {
                  print("<tr>
                                 <td>Folder</td>
                                 <td><a href=" . $path . $dirValue . "/" . ">" . $dirValue . "</a></td>
                                 <td></td>
                              </tr>");
               } else if (is_file($dirPath . $dirValue)) {
                  print("<tr>
                                 <td>File</td>
                                 <td>$dirValue</td>
                                 <td>
                                    <form class='form' action=" . $path . $dirValue . " method='POST'>
                                       <button id='download' name='download' value='$dirValue'>Download</button>
                                    </form>
                                    <form class='form' method='POST' action=''>
                                       <button id='delete' name='delete' value='$dirValue'>Delete</button>
                                    </form>
                                 </td>
                              </tr>");
               }
            }
            print("</table>");
            print("<p class='message'> $deleteMsg</p>");
            print("<p class='message'> $warning</p>");
            $backPath = "?path=" . dirname($_GET["path"]) . "/";
            print("<div id='bar'>");
            print("<a href=" . $backPath . ">Back</a>");
            print("
                        <form id='upload' action ='' method ='post' enctype = 'multipart/form-data'>
                           <input type ='file' name ='file' />
                           <input type ='submit' value= 'Upload'/>
                           <p class='message'>$error</p>
                           <p class='message'>$success</p>
                        </form>
                     ");
            print("<form id='form' method='POST' action=''>
                           <input type='text' name='createDir'placeholder='Type new directory name here' required>
                           <input type='submit' value='Create'>
                        </form>");
            print("</div>");
         } else {
            $dirPath = $dir;
            $dirArr = scandir($dirPath);
            $dirArr = array_diff($dirArr, array(".", ".."));
            $dirArr = array_values($dirArr);

            foreach ($dirArr as $dirValue) {
               if (is_dir($dirPath . $dirValue)) {
                  print("<tr>
                                 <td>Folder</td>
                                 <td><a href=" . $path . $dirValue . "/" . ">" . $dirValue . "</a></td>
                                 <td></td>
                              </tr>");
               } else if (is_file($dirPath . $dirValue)) {
                  print("<tr>
                                 <td>File</td>
                                 <td>$dirValue</td>
                                 <td>
                                    <form class='form' action=" . $path . $dirValue . " method='POST'>
                                       <button id='download' name='download' value='$dirValue'>Download</button>
                                    </form>
                                    <form class='form' method='POST' action=''>
                                       <button id='delete' name='delete' value='$dirValue'>Delete</button>
                                    </form>
                                 </td>
                              </tr>");
               }
            }
            print("</table>");
            print("<p class='message'> $deleteMsg</p>");
            print("<p class='message'> $warning</p>");
            print("<div id='bar'>");
            print("<a href=''>Back</a>");
            print("
                        <form id='upload' action ='' method ='post' enctype = 'multipart/form-data'>
                           <input type ='file' name ='file' />
                           <input type ='submit' value= 'Upload'/>
                           <p class='message'>$error</p>
                           <p class='message'>$success</p>
                        </form>
                     ");
            print("<form id='form' method='POST' action=''>
                           <input type='text' name='createDir'placeholder='Type new directory name here' required>
                           <input type='submit' value='Create'>
                        </form>");
            print("</div>");
         }
      }
   ?>
   <script src="https://kit.fontawesome.com/255176e611.js" crossorigin="anonymous"></script>
</body>

</html>