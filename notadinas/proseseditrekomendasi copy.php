<? 
    require_once '../config/koneksi.php';
    
    $nomornota=$_POST['nonotadinas'];
	$nomornotaawal=$_POST['nomornotaawal'];
	$tanggal=$_POST['tgl_nota'];
	$bidang=explode("-",$_POST['bidang']);
    $kdunit=$bidang[0];
	$namaunit=$bidang[1];    
    $perihal=$_POST['perihal'];
	$skkoi=$_POST['jenis'];
	$nilaiusulan=str_replace(',','',$_POST['nilairekom']); 
    $pembuat=$_POST['pembuat'];
	$progress=$_POST['progress'];
    $noskkoi=$_POST['noskk'];
	
	if($nomornotaawal == $nomornota)
	{
		$cek='1';
	}
	if($nomornotaawal != $nomornota)
	{
		$cek='2';
	}

	if($cek=='1'){
		
		$sql ="UPDATE notadinas SET
       nomornota='$nomornota',
       tanggal='$tanggal',
       unit='$namaunit',
	   kdunit='$kdunit',
       perihal='$perihal',
       skkoi='$skkoi',
       nilaiusulan='$nilaiusulan'
       WHERE nomornota='$nomornotaawal'";
	 

$hasil=mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli)); 

                echo '
                <script language="javascript">
                  alert("Nota Dinas '.$nomornota.' Berhasil Diedit!\r\n");
                  window.location.href="index.php";                   
                </script>
                ';   
		
		}
	if($cek=='2'){
				  $sql="select *
				  from notadinas
				  where nomornota='$nomornota'
				  and tanggal='$tanggal'";
			//echo $sql;
			$hasil=mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli)) or die (mysqli_error());    
			while ($row = mysqli_fetch_array($hasil)) {
			  $nomornota=$row['nomornota'];  
			  $tanggal=$row['tanggal'];
			}
		
			 
			 if(($nomornotaawal=$nomornota) )
			 {
			 echo '
			 <script language="javascript">
			 alert("Maaf, Nota Dinas '.$nomornota.' sudah ada!\r\nNota Dinas Tidak Dapat Diedit.");
			 document.location.href="javascript:history.back(0)";
			 </script>
			 ';  
			  }
			
			  else
			  {              
		$sql ="UPDATE notadinas SET
			   nomornota='$nomornota',
			   tanggal='$tanggal',
			   unit='$namaunit',
			   kdunit='$kdunit',
			   perihal='$perihal',
			   skkoi='$skkoi',
			   nilaiusulan='$nilaiusulan'
			   WHERE nomornota='$nomornotaawal'";
			 
		
		$hasil=mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli)); 
		
						echo '
						<script language="javascript">
						  alert("Nota Dinas '.$nomornota.' Berhasil Diedit!\r\n");
						  window.location.href="index.php";                   
						</script>
						';                      
		
					  }  
		
		}
	
	
	
    
  
        

?>