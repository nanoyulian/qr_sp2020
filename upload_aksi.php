<?php 
include "SimpleXLSX.php";
require_once("fpdf182/fpdf.php");

//direktory tempat menyimpan hasil generate qrcode jika folder belum dibuat maka secara otomatis akan membuat terlebih dahulu
$tempdir = "temp/"; 
if (!file_exists($tempdir))
    mkdir($tempdir);
    
$temp = explode(".", $_FILES["filepegawai"]["name"]);
$newfilename = str_replace(' ','_',$_POST['txt_wilayah']).'-'.round(microtime(true)). '.' . end($temp);
move_uploaded_file($_FILES["filepegawai"]["tmp_name"], "./temp/" . $newfilename);

// beri permisi agar file xls dapat di baca
chmod("./temp/" . $newfilename,0777);

//Isi dari QRCode Saat discan
$wil = preg_replace('/\s+/', '%20', $_POST['txt_wilayah']);
$organisasi = "Badan%20Pusat%20Statistik%20".$wil;
//set pdf
$pdf = new FPDF('P','mm','A4');
$pdf->SetFont('Arial','',8);
$pdf->AddPage();
//baca file import
if ( $xlsx = SimpleXLSX::parse('./temp/'.$newfilename) ) {
    $i = 0;
    $x_pos = 10;
    $y_pos = 10;
    //  6 x 5 = 30 qrcode per halaman
    $num_qr = count($xlsx->rows())-1;
    $num_pages = ceil($num_qr / 30) ; 
    $queue = array();
    $last_row_qr = ceil($num_qr / 5); 
    $cur_row_qr = 1;
    foreach ($xlsx->rows() as $kol) {
          if ($i == 0) {} 
          else {                     
                //Isi Teks dalam QRCode           
                $nama_lengkap = preg_replace('/\s+/', '%20', $kol[1]);
                $tlp = preg_replace('/\s+/', '%20', $kol[4]);
                // jenis mitra (koseka/PS)
                //print_r($kol[3]);
                $jns_mitra = $kol[3]; 
                if ($jns_mitra == '1') $jns_mitra = "KOSEKA";
                else $jns_mitra = "Petugas%20Sensus";

                $isi_teks  = 'NIK%20:%20'.$kol[0].'%0A';
                $isi_teks .= 'Nama%20:%20'.$nama_lengkap.'%0A';
                $isi_teks .= 'Email%20:%20'.$kol[2].'%0A';   
                $isi_teks .= 'Tugas%20:%20'.$jns_mitra.'%0A';   
                $isi_teks .= 'Tlp/HP%20:%20'.$tlp.'%0A'; 
                $isi_teks .=  $organisasi;  
                //gen.qr code
                $pdf->Image("http://localhost/qrcode/qr_generator.php?code=".$isi_teks, $x_pos, $y_pos, 30, 30, "png");  
                $queue[] = $kol[0];
                // tiap 4 qrcode maka baris baru dan print label qr yg sudah disimpan di $queue
                if ($i % 5 == 0) { 
                    $x_pos = 10;
                    $y_pos += 35;      
                    //baris baru              
                    $pdf->Cell(35,30,'',1,1,'C'); 
                    // print label qr
                    for($x=0; $x < count($queue);$x++) {
                      if ($x == 4) $pdf->Cell(35,5,$queue[$x],1,1,'C');
                      else $pdf->Cell(35,5,$queue[$x],1,0,'C');
                    }    
                    $queue = array();
                    $cur_row_qr++;                                     
                } else {                    
                    //Cell(width , height , text , border , end line , [align] )                      
                    // jika qr terakhir      
                    if ($cur_row_qr == $last_row_qr && $i == $num_qr) {
                      //baris baru untuk label qr
                      $pdf->Cell(35,30,'',1,1,'C');
                      for($x=0; $x < count($queue);$x++) {                        
                        $pdf->Cell(35,5,$queue[$x],1,0,'C');
                      }    
                    } else {
                      $pdf->Cell(35,30,'',1,0,'C');
                      $x_pos += 35;   
                    }       
                }        
                // jika sudah 30 qr code maka halaman baru
                if ($i % 30 == 0) {
                  $pdf->AddPage();
                  $x_pos = 10;
                  $y_pos = 10; 
                }     
          }      
          // next row
          $i++;
    }
    $pdf->Output();
} else {
    echo SimpleXLSX::parseError();
}

?>