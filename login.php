<?php
include 'config_db.php';

if (isset($_POST['login'])) {
  $passwordHash = md5($_POST['password']);
  $login = $_POST['login'];
  // Проверка логина на плохие смиволы
  //if (!preg_match("/^\w{3,}$/", $login)) {
//	die('Неправильный логин!');
 // }

   $link = mysql_connect('localhost',$dbuser,$dbpass);
   if (!$link) {
      die('Не удалось соединиться с БД');
   }else{
	 mysql_select_db($dbname, $link) or die(mysql_error());
	  $query="SELECT pass,status FROM ".$dbname.".".$tbname." WHERE login='$login' LIMIT 1"; //AND pass='$passwordHash'
      $res = mysql_query($query, $link) or die(mysql_error());
      // Есть ли пользователь с таким логином?

      if (mysql_num_rows($res) < 1) {
         mysql_close($link);
         die('Такого пользователя нет!');
      } else {
             if (mysql_result($res, 0,0) != $passwordHash) {
                mysql_close($link);
                die('Неверный пароль!');
             } else {
                    if (mysql_result($res, 0,1) != 1) {
                        mysql_close($link);
                        die('Логин не активирован!');
                    } else {
      // Стартуем сессию и записываем логин в суперглобальный массив $_SESSION
      session_start();
      $_SESSION['user'] = $login;
      mysql_close($link);
      // Если определена страница с которой мы пришли,
      // на нее и переадресуем, либо на главную
	  if (isset($_SERVER['HTTP_REFERER'])) {
		if (preg_match("/activation.php/",$_SERVER['HTTP_REFERER'])) {
		  header ("location: index.php");
		} else {
		  header ("location: ".$_SERVER['HTTP_REFERER']);
		}
      } else {
		header ("location: index.php");
	  }
                    }
             }
      }

   }
}
?>

