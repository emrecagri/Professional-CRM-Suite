<?php
session_start();

include 'baglan.php';
include 'logtutucu.php';
logtutucu(1, "Oturum kapatıldı.", 0);

session_destroy();
header("location:../giris.php")
 ?>