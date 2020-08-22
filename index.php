<?php
//library phpqrcode


 
//direktory tempat menyimpan hasil generate qrcode jika folder belum dibuat maka secara otomatis akan membuat terlebih dahulu
$tempdir = "temp/"; 
if (!file_exists($tempdir))
    mkdir($tempdir);
 
?>
<html>
<head>
    <meta http-equiv="Content-Type" content="isi_teks/html; charset=utf-8" />    
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <link rel="icon" href="dk.png">
    <title>QRCode Petugas SP2020 Generator</title>
</head>
<body>

<form method="post" enctype="multipart/form-data" action="upload_aksi.php">
  <table>
    <tr>
      <td>BPS Kabupaten/Kota : </td>
      <td><input name="txt_wilayah" required="required"> </td>

    </tr>
    <tr>
      <td>Pilih File:</td>
      <td>
        <input name="filepegawai" type="file" required="required"> 
	      <input name="upload" type="submit" value="Import">
      </td>
    </tr>
  </table>
  
</form>
 
</body>
</html>