<?php
require_once("templater.php");
do_html_header();
if (!empty($_POST)) {
  echo "<pre>";
  print_r($_POST);
  echo "</pre>";  
  if (!empty($_POST['user_login']) & !empty($_POST['user_pass'])) {
	$dbuser=$_POST['user_login'];
	$dbpass=$_POST['user_pass'];
	if (isset($_POST['cr_us_cb'])) {
	  $cr_us_cb="checked";
	  $query['cr_us']="CREATE USER '".$dbuser."'@'%' IDENTIFIED BY '".$dbpass."'";
	} else {
	  $cr_us_cb="";
	}
	if (!empty($_POST['db_name'])) {
	  $dbname=$_POST['db_name'];
	  if (isset($_POST['cr_db_cb'])) {
		$cr_db_cb="checked";
		$query['cr_db']="CREATE DATABASE IF NOT EXISTS ".$dbname;
	  } else {
		$cr_db_cb="";
	  }
	  if (!empty($_POST['tb_name'])) {
		$tbname=$_POST['tb_name'];
		if (isset($_POST['cr_tb_cb'])) {
		  $cr_tb_cb="checked";
		  $query['cr_tb']="CREATE TABLE ".$dbname.".".$tbname." (id MEDIUMINT(9) NOT NULL AUTO_INCREMENT, login VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, pass VARCHAR(32) CHARACTER SET ucs2 COLLATE ucs2_general_ci NOT NULL, email VARCHAR(64) CHARACTER SET ucs2 COLLATE ucs2_general_ci NOT NULL, status TINYINT(2) NOT NULL, timestamp INT(10) NOT NULL, PRIMARY KEY (id), UNIQUE (login, email)) ENGINE = MyISAM";
		  if (isset($_POST['cr_us_cb'])) {
			$query['cr_us_priv']="GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, REFERENCES, INDEX, ALTER, CREATE VIEW, TRIGGER, SHOW VIEW ON ".$dbname.".".$tbname." TO '".$dbuser."'@'%'WITH GRANT OPTION";
		  }
		} else {
		  $cr_tb_cb="";
		}
		$file=fopen("config_db.php","w");
		$config="<?php\n";
		$config.="\$dbuser=\"".$dbuser."\";\n";
		$config.="\$dbpass=\"".$dbpass."\";\n";
		$config.="\$dbname=\"".$dbname."\";\n";
		$config.="\$tbname=\"".$tbname."\";\n";
		$config.="?>";
		fwrite($file,$config);
		fclose($file);		
		if (isset($query)) {
		  if (!empty($_POST['root_login']) & !empty($_POST['root_pass'])) {
			$root=$_POST['root_login'];
			$pass=$_POST['root_pass'];
			$link = mysql_connect('localhost',$root,$pass);
			if (mysql_error()=='') {
			  foreach ($query as $key => $value) {
				mysql_query($value,$link);
			  }
			  $ok="ok";
			  do_html_simple_message("Настройка успешно завершена.");
			} else {
			  $err['root']="Неудалось подкючиться к БД. Возможно неверно введен лигн или пароль администратора.";
			}
		  } else {
			$err['root']="Отсутствует логин или пароль администратора.";
		  }
		}

	  } else {
		$err['tb']="Отсутствует имя таблицы.";
	  }
	} else {
	  $err['db']="Отсутствует имя БД.";
	}
  } else {
	$err['user']="Отсутствует логин или пароль пользователя.";
  }

} 
if (empty($_POST) | !isset($ok)) {
?>
  <form id="form-setup" method="post" action="<?=$_SERVER['SCRIPT_NAME']?>">
  <fieldset>
    <legend></legend>
	<fieldset>
	  <legend>Администратор БД</legend>
	  <label for="root_login">Логин: </label><input id="root_login" type="text" name="root_login" value="<?=(!empty($root) ? $root : '')?>" />
	  <label for="root_pass">Пароль: </label><input id="root_pass" type="password" name="root_pass" value="<?=(!empty($pass) ? $pass : '')?>" />
	  <p class="form-setup-err"><?=(!empty($err['root']) ? $err['root'] : '&nbsp;')?></p>
	</fieldset>
	<fieldset>
	  <legend>Пользователь БД</legend>
	  <label for="user_login">Логин: </label><input id="user_login" type="text" name="user_login" value="<?=(!empty($dbuser) ? $dbuser : '')?>" />
	  <label for="user_pass">Пароль: </label><input id="user_pass" type="password" name="user_pass" value="<?=(!empty($dbpass) ? $dbpass : '')?>" />
	  <input id="cr_us_cb" type="checkbox" name="cr_us_cb" <?=(!empty($cr_us_cb)  ? ' checked=\"checked\"' : '')?>/><label for="cr_us_cb">Создать</label>
	  <p><?=(!empty($cr_us_cb)  ? $cr_us_cb : '')?><?=(!empty($err['user']) ? $err['user'] : '&nbsp;')?></p>
	</fieldset>
	<fieldset>
	  <legend>База данных</legend>
	  <label for="db_name">Имя БД: </label><input id="db_name" type="text" name="db_name" value="<?=(!empty($dbname) ? $dbname : '')?>" />
	  <input id="cr_db_cb" type="checkbox" name="cr_db_cb" <?=(!empty($cr_db_cb) ? ' checked=\"checked\"' : '')?>/><label for="cr_db_cb">Создать</label>
	  <p><?=(!empty($err['db']) ? $err['db'] : '&nbsp;')?></p>
	</fieldset>
	<fieldset>
	  <legend>Таблица</legend>
	  <label for="tb_name">Имя таблицу: </label><input id="tb_name" type="text" name="tb_name" value="<?=(!empty($tbname) ? $tbname : '')?>" />
	  <input id="cr_tb_cb" type="checkbox" name="cr_tb_cb"  <?=(!empty($cr_tb_cb) ? " checked=\"checked\"" : '')?>/><label for="cr_tb_cb">Создать</label>
	  <p><?=(!empty($err['tb']) ? $err['tb'] : '&nbsp;')?></p>
	</fieldset>
	<input type="submit" name="submit" value="OK"/>
  </fieldset>
  </form>
<?
}
do_html_footer();
?>
