<? 
    require_once '../config/koneksi.php';
    
    $sql="select * from costcenter where nocostcenter='$_POST[costcenter]'";
    $hasil=mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));
    $cek_costcenter = mysql_num_rows($hasil);
	
    /*
    if($_POST['rp_sisa']<0)
    {
         echo"<script>alert('Maaf, rupiah kontrak yang dientri melebihi sisa rupiah anggaran.',document.location.href='javascript:history.back(0)')</script>";    
    }
    */
   if($cek_costcenter==0)
    {
        $tahuncostcenter= trim($_POST['tahuncostcenter']);
		$hierarchyarea=$_POST['hierarchyarea'];
		$costcenter= trim($_POST['costcenter']);
		$deskripsibisnisarea= trim($_POST['deskripsibisnisarea']);
		$bisnisarea= trim($_POST['bisnisarea']);
		if(!$_POST['uraian']=='')
		{
		$uraian=trim($_POST['uraian']);
		}
        
        $sql = "INSERT INTO costcenter(
                hierarkiarea,
                uraian,
                nocostcenter,
				descriptionbisnis,
                bisnisarea,
                tahunperiode
                )
                VALUES
                (
                '$hierarchyarea'," .
				(isset($uraian)?"'$uraian',":"") .
				"   
                '$costcenter',
				'$deskripsibisnisarea',
				'$bisnisarea',
                '$tahuncostcenter'                                      
                )
                ";
	
   		$hasil=mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli)); 
   		echo "<script>alert($hasil);</script>";
	
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