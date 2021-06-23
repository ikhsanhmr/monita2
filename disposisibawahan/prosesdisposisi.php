<?php 
    require_once '../config/koneksi.php';
    
    $nd = $_REQUEST['nd']; 
	$prg = $_REQUEST['prg'];	
	$jenis = $_REQUEST['skkoi'];        
	//echo $jenis;
$sql ="
	UPDATE notadinas SET
		progress='$prg'
	WHERE nomornota='$nd'";


$hasil=mysql_query($sql); 
echo "<script type='text/javascript'>window.open('index.php', '_self')</script>";

/*
if($prg=='7')
{

if ($jenis=='SKKO' )
		{	
		$sql = "INSERT INTO skkoterbit(
                nomornota
                )
                VALUES
                (
                '$nd'                                           
                )
                ";
     	
		$hasil=mysql_query($sql); 
		}
		else
		{
		$sql = "INSERT INTO skkiterbit(
                nomornota
                )
                VALUES
                (
                '$nd'                                           
                )
                ";
				
     	$hasil=mysql_query($sql); 	
		
		}
	echo $sql;		
}
*/	
/*	  echo '
        <script language="javascript">
          window.location.href="index.php"
        </script>
        ';                  
*/


   
?>