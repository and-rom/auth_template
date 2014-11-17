<?php
function do_html_header () {
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
	<meta content="text/html; charset=utf-8" http-equiv="content-type" />
	<title></title>
	<meta name="keywords" content="" />
	<meta name="description" content="" />
	<link rel="stylesheet" href="style.css" type="text/css" media="screen, projection" />
	<!--[if lte IE 6]><link rel="stylesheet" <a href=""></a>="style_ie.css" type="text/css" media="screen, projection" /><![endif]-->
  </head>
  <body>
	<div id="wrapper">
	  <div id="header">
		<div class="login">
		  <?php
			if (isset($_SESSION['user'])) {
		  ?>
		  <p class="form-text">Вы вошли как <strong><?=$_SESSION['user']?></strong>.
		  <a class="form-link" href="logout.php">Выйти.</a>
		  </p>
		  <?php
			} else {
		  ?>
		  <form class="login-form" method="post" action="login.php">
			<fieldset>
			  <legend></legend>
			  <label>Логин:</label><input class="form-login-input" type="text" name="login"/>
			  <label>Пароль:</label><input class="form-login-input" type="password" name="password"/>
			  <input class="form-login-input" type="submit" name="submit" value="Войти"/>
			  <p class="form-text">
				<a class="form-link" href="registration.php">Регистрация.</a>
				<a class="form-link" href="#">Забыли пароль?</a>
			  </p>
			</fieldset>
		  </form>

		  <?php
			}
		  ?>
		</div>
	  </div><!-- #header-->
	  <div id="content">
	  <?
		}
function do_html_footer () {
?>
	</div><!-- #content-->
  </div><!-- #wrapper -->
  <div id="footer">
  </div><!-- #footer -->
  </body>
</html>
<?
}
function do_html_simple_message($message) {
?>
<p align=center><?=$message ?></p>
<?
}
?>
