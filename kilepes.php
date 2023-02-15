<?php
session_start();
$log=fopen('log.txt','a');
$txt=$_SESSION["nev"]." kilépett! \n";
fwrite($log, $txt);
fclose($log);
session_unset();
?>