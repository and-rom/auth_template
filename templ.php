<?php
require_once('templater.php');
session_start();
do_html_header ();

if (isset($_SESSION['user'])) {
?>
<img src="https://www.google.ru/logos/2013/edward_goreys_88th_birthday-1056005.2-hp.jpg" />
<?
} else {
?>
<img src="http://yandex.st/morda-logo/i/logo.png" />
<?
}
do_html_footer();
?>
