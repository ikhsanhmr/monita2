<?php
  session_start();
  require_once '../config/koneksi.php';
    
  if(isset($_POST['queryString'])) 
  {        
      $queryString = $_POST['queryString'];
      if(strlen($queryString) >0) 
      {
      
        $sql="SELECT * FROM costcenter 
			  where nocostcenter like '".$queryString."%' limit 10";
		
		$hasil=mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli)) or die (mysql_error());  
		if(mysql_num_rows($hasil) >0)
		{
		while ($row = mysqli_fetch_array($hasil)) {
             //     echo "<li onClick='fill($arr[nocostcenter]')>$row[nocostcenter]  Hierarchy Area $row[hierarkiarea]<br></li>";
				//                  echo "<li onClick=\"alert('$row[nocostcenter]')\">$row[nocostcenter]  Hierarchy Area $row[hierarkiarea]<br></li>"
			echo "<li 
			onClick=\"document.getElementById('nocostcenter').value='$row[nocostcenter]'\">
			$row[nocostcenter]  Hierarchy Area $row[hierarkiarea]<br></li>";
				//   echo '<li onClick="fill(\''.$arr[no_skk].'|'.$arr[pos].'|'.$arr[subpos1].'\');">'.$arr[no_skk].' pos '.$arr[pos].' subpos '.$arr[subpos1].'<br>'.'</li>';
		}
		}

          else
          {
              echo 'Maaf, data tidak ditemukan!';
          }          
		  
      }
             
  }
  else
  {
			   echo 'There should be no direct access to this script!';      
  }  	 
?>         