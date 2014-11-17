<?php
include 'config_db.php';

session_start();
require_once('templater.php');
  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $rLogin = trim($_POST['rLogin']);
  $rPass  = trim($_POST['rPass']);
  $rPass2 = trim($_POST['rPass2']);
  $rEmail = trim($_POST['rEmail']);
  if ($rLogin == '') {
	$err['login']="Поле 'Логин' не заполнено<br />\n";
	// Логин может состоять из букв, цифр и подчеркивания
  }elseif (!preg_match("/^\w{3,}$/", $rLogin)) {
	$err['login']=die("В поле 'Логин' введены недопустимые символы<br />\n");
  }
  if ($rEmail == '') {
	$err['login']=die("Поле 'E-mail' не заполнено<br />\n");
	// Проверяем e-mail на корректность
  }elseif (!preg_match("/^[a-zA-Z0-9_\.\-]+@([a-zA-Z0-9\-]+\.)+[a-zA-Z]{2,6}$/", $rEmail)) {
	$err['email']=die("Указанный 'E-mail' имеет недопустимый формат<br />\n");
  }
  if ($rPass == '' || $rPass2 == '') {
	$err['pass1']=die("Поле 'Пароль' не заполнено<br />\n");
  }elseif($rPass !== $rPass2) {
	$err['pass2']=die("Поля 'Пароль' и 'Повтор пароля' не совпадают<br />\n");
   // Пароль может состоять из букв, цифр и подчеркивания
  }elseif(!preg_match("/^\w{3,}$/", $rPass)) {
	$err['pass']=die("В поле 'Пароль' введены недопустимые символы<br />\n");
  }
  // В базе данных у нас будет храниться md5-хеш пароля
  $mdPassword = md5($rPass);
  // А также временная метка
  $time = time();// + 7*24*60*60;

  // Устанавливаем соединение с бд(не забудьте подставить ваши значения сервер-логин-пароль)
  $link = mysql_connect('localhost', $dbuser, $dbpass);
  if (!$link) {
	$err['common']=die("Не могу соединиться с базой данных");
  }else {
	// Выбираем базу данных
	mysql_select_db($dbname, $link);
	// Записываем в базу (не используем addslashes - экранировать нечего)
	mysql_query("INSERT INTO ".$tbname." (login, pass, email, timestamp)
                   VALUES ('$rLogin','$mdPassword','$rEmail',$time)",$link);
	if (mysql_error($link) != "") {
	  die("Пользователь с таким логином уже существует, выберите другой<br />\n");
	}
	// Получаем Id, под которым юзер добавился в базу
	$id = mysql_result(mysql_query("SELECT LAST_INSERT_ID()", $link), 0);
	// Составляем "keystring" для активации
	$key = md5(substr($rEmail, 0 ,2).$id.substr($rLogin, 0 ,2));
	$date = date("d.m.Y",$time);
	// Компонуем письмо
	$title = 'Потвеждение регистрации на сайте Somwhere.net';
	$headers  = "Content-type: text/plain; charset=windows-1251\r\n";
	$headers .= "From: Администрация Somwhere.net \r\n";
	$subject = '=?koi8-r?B?'.base64_encode(convert_cyr_string($title, "w","k")).'?=';
	$host=$_SERVER['HTTP_HOST'];
	$letter = <<< LTR
  Здравствуйте!
  бла-бла
   
Ваши регистрационные данные:
  логин: $rLogin
  пароль: $rPass
  
   Для активации аккаунта вам следует пройти по ссылке:
   http://$host/activation.php?login=$rLogin&key=$key
   
   Данная ссылка будет доступна в течении 5 дней.
   
   $date
LTR;
   // Отправляем письмо
   if (!mail($rEmail, $subject, $letter, $headers)) {
	 // Если письмо не отправилось, удаляем юзера из базы
	 mysql_query("DELETE FROM users WHERE login='".$rLogin."' LIMIT 1", $link);
	 //echo "http://".$_SERVER['HTTP_HOST']."/activation.php?login=$rLogin&key=$key";
	 echo "При регистрации возникла ошибка. Попробуйте позже.";
   } else {
	 echo 'Регистрация прошла успешно. На указанный Вами электронный адрес было отправлено письмо с подтверждением регистрации. Регистрацию необходимо подтвердить в течении 5 дней!';
   }
   mysql_close($link);
  }
}
do_html_header();
?>
        <form id="reg-form" method="post" action="">
		  <fieldset>
			<legend>Регистрация</legend>
			<p>
			  <label>Логин:</label>
			  <input class="regform-intut" type="text" name="rLogin"/>
			</p>
			<p>
			  <label>Пароль:</label>
			  <input class="regform-intut" type="password" name="rPass"/>
			</p>
			<p>
			  <label>Повторите пароль:</label>
			  <input class="regform-intut" type="password" name="rPass2"/>
			</p>
			<p>
			  <label>E-mail:</label>
			  <input class="regform-intut" type="text" name="rEmail"/>
			</p>
			<p>
			  <input class="noresize submit" type="reset" name="reset"/>
			  <input class="noresize" type="submit" name="submit"/>
			</p>
		  </fieldset>
        </form>

<?php
  do_html_footer();
?>
