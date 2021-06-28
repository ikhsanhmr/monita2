<?
  session_start(); 
  require_once '../config/koneksi.php';
  $costcenter=base64_decode($_GET['nocostcenter']);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<link href="../css/screen.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="../js/jquery-1.10.2.min.js"></script>
<link rel="stylesheet" type="text/css" href="../css/jquery.datepick.css"> 
<script type="text/javascript" src="../js/jquery.datepick.js"></script>
<script type="text/javascript" src="../js/jquery.validate.pack.js"></script>
<script type="text/javascript"> 

$(document).ready(function() {
	$("#tambahcostcenter").validate({
		rules: {
			tahuncostcenter: "required",
			hierarchyarea: "required",
			costcenter: "required",
			deskripsibisnisarea: "required",
			bisnisarea: "required",
		},
		messages: {
			tahuncostcenter: "Tahun Harus Diisi",	
			hierarchyarea: "Hierarchy Area Harus Diisi",  
			costcenter: "Cost Center Harus Diisi",
			deskripsibisnisarea: "Description Business Area Harus Diisi",	
			bisnisarea: "Uraian Harus diisi"            		
		}
	});  
	  
 }); 
</script>
</head>
<?php
$sql="select * 
        from costcenter 
        where nocostcenter='$costcenter'";
	$hasil=mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli)) or die (mysql_error());    
	while ($row = mysqli_fetch_array($hasil)) {
	$hierarkiarea=$row['hierarkiarea'];
    $uraian=$row['uraian'];
    $nocostcenterawal=$row['nocostcenter'];  
    $descriptionbisnis=$row['descriptionbisnis'];
	$bisnisarea=$row['bisnisarea'];
    $tahunperiode=$row['tahunperiode'];  
	}
?>
<body>
<h2>Edit Cost Center</h2>
<form name='editcostcenter' method=POST id='editcostcenter' action="proseseditcostcenter.php" autocomplete="off">
<input name="nomorcostcenterawal" id="nomorcostcenterawal" maxlength="64" size="50" type="hidden" value="<? echo $nocostcenterawal;?>"/>
  <table>
		<tr>
            <td>Tahun Periode</td>                
            <td><select id="tahuncostcenter" name="tahuncostcenter">
			<option value="">Pilih Tahun</option>>
            <?php 
			for($i=2013; $i<=date('Y')+1; $i++) 
				echo "<option value='$i'". ($i==$tahunperiode?"selected":"") . " >$i</option>"; 
			?>
			</select>
            </td>
        </tr> 	  
           <tr>
            <td>Hierarchy Area</td>                
            <td><input name="hierarchyarea" id="hierarchyarea" maxlength="64" size="50" type="text" value="<? echo $hierarkiarea;?>"/>
            </td>
        </tr>   
        <tr>
            <td>Uraian</td>                  
            <td><input name="uraian" id="uraian" type="text" size="50" value="<? echo $uraian;?>"/></td>
            </tr>  
        <tr>
            <td>Cost Center</td>                    
            <td>            
            <input name="costcenter" id="costcenter" maxlength="64" size="50" type="text" value="<? echo $nocostcenterawal;?>"/>
            </td>
        </tr>               	                                   
      <tr>
            <td>Description Business Area</td>                    
            <td>            
            <input name="deskripsibisnisarea" id="deskripsibisnisarea" maxlength="64" size="50" type="text" value="<? echo $descriptionbisnis;?>"/>
            </td>
        </tr>  
        <tr>
            <td>Business Area</td>                    
            <td>            
            <input name="bisnisarea" id="bisnisarea" maxlength="64" size="50" type="text" value="<? echo $bisnisarea;?>"/>
            </td>
        </tr>   
      <tr>
            <td colspan="2">
                <input type="submit" value="Simpan">
                <input type="button" value="Batal" onclick=self.history.back()>
            </td>
        </tr>
    </table>
</form>
</body>
</html>