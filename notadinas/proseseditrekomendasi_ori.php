<? 
    require_once '../config/koneksi.php';
    
    $nomornota=trim($_POST['nonotadinas']);
	$nomornotaawal=trim($_POST['nomornotaawal']);
	$tanggal=$_POST['tgl_nota'];
	$unit1=explode("-",$_POST['unit1']);
    $kdunit1=$unit1[0];
	$namaunit1=$unit1[1];  
	$perihal=trim($_POST['perihal']);
	$skkoi=trim($_POST['jenis']);
	$nilaiusulan=trim(str_replace(',','',$_POST['nilairekom'])); 
    $pembuat=trim($_POST['pembuat']);
	$progress=trim($_POST['progress']);
    $noskkoi=trim($_POST['noskk']);
	
	if($nomornotaawal == $nomornota){$cek='1';}
	if($nomornotaawal != $nomornota){$cek='2';}
/*	
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
				  where nomornota='$nomornota'";
			//echo $sql;
			$hasil=mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli)) or die (mysqli_error());  
			$result = mysqli_num_rows($hasil);  	 
			 if($result > 0 )
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
*/	
if($cek==2) {
	$sql="select * from notadinas where nomornota='$nomornota'";
	$hasil=mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli)) or die (mysqli_error());  
	$result = mysqli_num_rows($hasil);  	 

	if($result > 0 ) {
		echo '
			<script language="javascript">
			alert("Maaf, Nota Dinas '.$nomornota.' sudah ada!\r\nNota Dinas Tidak Dapat Diedit.");
			document.location.href="javascript:history.back(0)";
			</script>
			';  
		exit;
	}
}
			  
$sql ="
	UPDATE notadinas SET
		nomornota='$nomornota',
		tanggal='$tanggal',
		unit='$namaunit1',
		kdunit='$kdunit1',
		perihal='$perihal',
		skkoi='$skkoi',
		nilaiusulan='$nilaiusulan'
	WHERE nomornota='$nomornotaawal'";


$hasil=mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli)); 

$sql ="
	UPDATE skkoterbit SET
		nomornota='$nomornota'
	WHERE nomornota='$nomornotaawal'";
$hasil=mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli)); 

echo '
	<script language="javascript">
		alert("Nota Dinas '.$nomornota.' Berhasil Diedit!\r\n");
		window.location.href="index.php";                   
	</script>';  
?>