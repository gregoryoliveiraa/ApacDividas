<?php
session_start();
session_unset();
session_destroy();

header("Location: http://dividas.apac.org.br/login.html");
exit();
?>