<? 
    require_once '../config/koneksi.php';
    
    $sql="select * from kontrak where no_kontrak='$_POST[nonotadinas]'";
    $hasil=mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));
    $cek_notadinas = mysql_num_rows($hasil);
	
   if($cek_notadinas==0)
    {
        $nonotadinas= trim($_POST['nonotadinas']);
		$tgl_nota=$_POST['tgl_nota'];
		$unit1=explode("-",$_POST['unit1']);
   	 	$kdunit1=$unit1[0];
		$namaunit1=$unit1[1]; 
		$perihal= trim($_POST['perihal']);
		$jenis= trim($_POST['jenis']);
		$nilairekom=trim(str_replace(',','',$_POST['nilairekom']));
		$nip=$_POST['nip'];
       
        $sql = "INSERT INTO notadinas(
                nomornota,
                tanggal,
                unit,
				kdunit,
                perihal,
                skkoi,
                nilaiusulan,
                nipuser
				)
                VALUES
                (
                '$nonotadinas',            
                '$tgl_nota',
				'$namaunit1',
				'$kdunit1',
                '$perihal',
                '$jenis',                
                '$nilairekom',
				'$nip'                                        
                )
                ";
		
   
   		$hasil=mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli)); 
        echo '
        <script language="javascript">
          window.location.href="index.php"
        </script>
        ';                  
  	
    }

    else
    {
        echo '
        <script language="javascript">
          alert("No Nota Dinas Sudah Ada!");
          window.location.href="javascript:history.back(0)";
        </script>
        ';    
 
    }
   
?>