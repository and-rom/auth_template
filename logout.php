<?php
include 'config_db.php';

   session_start();
   if (isset($_SESSION['user'])) {
   // удаляем элемент "user"
      unset($_SESSION['user']);
      session_destroy();
      setcookie("PHPSESSID","",time()-3600,"/");
   }
   if (isset($_SERVER['HTTP_REFERER'])) {
      header ("location: ".$_SERVER['HTTP_REFERER']);
   }else {
      header ("location: index.php");
   }
?>
