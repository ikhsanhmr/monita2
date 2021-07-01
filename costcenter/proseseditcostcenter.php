<? 
    require_once '../config/koneksi.php';
    
    $nocostcenter=trim($_POST['costcenter']);
	
	$sql="select * from costcenter where nocostcenter='$nocostcenter'";
    
	$hasil=mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));
    $cek_costcenter = mysqli_num_rows($hasil);
	

   if($cek_costcenter==0)
    {
        $tahuncostcenter= trim($_POST['tahuncostcenter']);
		$hierarchyarea=$_POST['hierarchyarea'];
		$nomorcostcenterawal=$_POST['nomorcostcenterawal'];
		$deskripsibisnisarea= trim($_POST['deskripsibisnisarea']);
		$bisnisarea= trim($_POST['bisnisarea']);
		if(!$_POST['uraian']=='')
		{
		$uraian=trim($_POST['uraian']);
		}
        

        
		$sql = "UPDATE costcenter set
                hierarkiarea='$hierarchyarea'," .
				(isset($uraian)?"uraian='$uraian',":"") .
				"  
                nocostcenter='$nocostcenter',
				descriptionbisnis='$deskripsibisnisarea',
                bisnisarea='$bisnisarea',
                tahunperiode='$tahuncostcenter'
                WHERE nocostcenter='$nomorcostcenterawal'";
		
   		$hasil=mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli)); 
	
	    echo '
        <script language="javascript">
		  alert("Cost Center '.$costcenter.' Berhasil Ditambah!\r\n");
          window.location.href="index.php"
        </script>
        ';                  

    }

    else
    {
        echo '
        <script language="javascript">
          alert("No Cost Center Sudah Ada!");
          window.location.href="javascript:history.back(0)";
        </script>
        ';    

    }
  
?>