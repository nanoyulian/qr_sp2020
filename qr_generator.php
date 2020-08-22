<?php
//include the libraries
require_once("phpqrcode/qrlib.php");
//create a QR Code and save it as a png image file named test.png
QRcode::png($_GET['code'])
?>