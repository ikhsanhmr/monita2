<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<link href="../css/screen.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="../js/methods.js"></script>
	<script type="text/javascript">
		function validate() {
			var s = parseInt(document.getElementById("sisanya").value);
			var n = parseInt(document.getElementById("nilai").value);
			var b = parseInt(document.getElementById("sisabyr").value);
			
			if(n>s) {
				alert("Nilai kontrak lebih besar dari sisa pagu.\nKontrak tidak bisa disimpan!");
				return false;
			}

			if(n<b) {
				alert("Nilai kontrak lebih kecil dari nilai yang sudah dibayar.\nKontrak tidak bisa disimpan!");
				return false;
			}
		}

		function getskkdata(my) {
			var xmlhttp;
			if (window.XMLHttpRequest) {
				xmlhttp=new XMLHttpRequest();
			} else {
				xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
			}
			
			var url = encodeURI("getskkdata.php?s=" + my);
			xmlhttp.open("GET",url,false);
			xmlhttp.send();
			var rutin_select = document.getElementsByClassName("rutinselect");
			var elecount = rutin_select.length;

			if(xmlhttp.responseText == 'SKKI'){
				
				for(var i=0; i < elecount; i++){
					rutin_select[i].style.display = "none";
				}
			} else {
				for(var i=0; i < elecount; i++){
					rutin_select[i].style.display = "block";
				}
			}
		}
	</script>
</head>

<body>
	<?php
		$u_agent = $_SERVER['HTTP_USER_AGENT'];
		if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent)) { 
			$bname = 'Internet Explorer'; 
			$ub = "MSIE"; 
		} 
		elseif(preg_match('/Firefox/i',$u_agent)) { 
			$bname = 'Mozilla Firefox'; 
			$ub = "Firefox"; 
		} 
		elseif(preg_match('/Chrome/i',$u_agent)) { 
			$bname = 'Google Chrome'; 
			$ub = "Chrome"; 
		} 
		elseif(preg_match('/Safari/i',$u_agent)) { 
			$bname = 'Apple Safari'; 
			$ub = "Safari"; 
		} 
		elseif(preg_match('/Opera/i',$u_agent)) { 
			$bname = 'Opera'; 
			$ub = "Opera"; 
		} 
		elseif(preg_match('/Netscape/i',$u_agent)) { 
			$bname = 'Netscape'; 
			$ub = "Netscape"; 
		} 
		$nice = (($ub=="Chrome" || $ub=="Opera" || $ub=="Chrome")? true: false);
	
		error_reporting(0);  session_start(); 
		require_once '../config/koneksi.php';
		$nip    =$_SESSION['nip'];
		$bidang =$_SESSION['bidang'];
		$kdunit =$_SESSION['kdunit'];
		$nama   =$_SESSION['nama'];
		$adm    =$_SESSION['adm'];
		@$skk    		= 'null';
		@$nomorkontrak  = 'null';
		@$pos    		= 'null';
		@$uraian 		= 'null';
		@$vendor 		= 'null';
		@$awal   		= 'null';
		@$akhir  		= 'null';
		@$nilai  		= 'null';
		@$isrutin  		= null;
		if($nip=="") {exit;}
		
		$kon = (isset($_REQUEST["kon"])? $_REQUEST["kon"]: "");
		if($kon!="") {
			//echo $kon;
			$sql = "select 	a.*, COALESCE(b.bayar, 0) as bayar
					from 	kontrak a left join
							(SELECT nokontrak, SUM(nilaibayar) bayar FROM realisasibayar GROUP BY nokontrak) b ON a.nomorkontrak = b.nokontrak where kid LIKE '%$kon%'";
			mysqli_set_charset("UTF8");
			//echo "$sql";
			$result = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));
			
			while ($row = mysqli_fetch_array($result)) {
				@$skk			= $row["nomorskkoi"];
				@$nomorkontrak	= $row["nomorkontrak"];
				@$pos			= $row["pos"];
				@$uraian		= $row["uraian"];
				@$vendor		= $row["vendor"];
				@$awal			= $row["tglawal"];
				//$awal			= substr($awal,5,2) . "/" . substr($awal,-2) . "/" . substr($awal,0,4);
				@$akhir   		= $row["tglakhir"];
				//$akhir 		= substr($akhir,5,2) . "/" . substr($akhir,-2) . "/" . substr($akhir,0,4);
				@$nilai   		= $row["nilaikontrak"];
				@$nodokumen   	= $row["nodokumen"];
				@$tgltagih   	= date("Y-m-d", strtotime($row["tgltagih"]));
				@$sisabyr 		= $row["bayar"];
				@$isrutin 		= $row["isrutin"];
				//echo "$skk - $pos - $uraian - $vendor - $awal - $akhir<br>";
			}
		
			mysqli_free_result($result);
			//$mysqli->close();($link);	  			
		} else {
			@$skk = $_REQUEST["skk"];
			@$pos = $_REQUEST["pos"];		
		}

			$sqlsisa = "
			SELECT d.*, kontrak, namapos, n.skkoi FROM notadinas_detail d
			LEFT JOIN (
				SELECT nomorskkoi, pos, SUM(nilaikontrak) kontrak FROM kontrak GROUP BY nomorskkoi, pos
			) k ON d.noskk = k.nomorskkoi AND d.pos1 = k.pos
			LEFT JOIN (
				SELECT kdindukpos kdpos, namaindukpos namapos FROM posinduk UNION ALL 
				SELECT kdsubpos kdpos, namasubpos namapos FROM posinduk2 UNION ALL 
				SELECT kdsubpos kdpos, namasubpos namapos FROM posinduk3 UNION ALL 
				SELECT kdsubpos kdpos, namasubpos namapos FROM posinduk4
			) p ON d.pos1 = p.kdpos LEFT JOIN
			notadinas n ON d.nomornota = n.nomornota
			WHERE 
				noskk = '$skk' 
				AND pos1 = '$pos'";
		
		$nilaisisa = 0;
		$kontraksisa = 0;
		$sisa = 0;
		$namasisa = "";
		$skktype = "";
		
		$resultsisa = mysqli_query($sqlsisa);	
		while ($rowsisa = mysqli_fetch_array($resultsisa)) {
			$nilaisisa = $rowsisa["nilai1"];
			$kontraksisa = $rowsisa["kontrak"];
			$sisa = $nilaisisa - $kontraksisa + $nilai;
			$namasisa = $rowsisa["namapos"];
			$skktype = $rowsisa["skkoi"];
		}

		$sql = "SELECT * FROM kontrak_type ORDER BY nama ASC";
		//echo "$sql";
		$result = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));
		
		$kontrak_type = "";
		while ($row = mysqli_fetch_array($result)) {
			$kontrak_type .= "<option value='$row[id]' ".($isrutin == $row["id"] ? "selected" : "").">$row[nama]</option>";
		}
		
		mysqli_free_result($result);
		
		echo "
			<h2>" . ($kon!=""? "EDIT ": "INPUT ") . "KONTRAK</h2>
			<form name='fkontrak' id='fkontrak' method='post' enctype='multipart/form-data' action='simpan.php' onsubmit='return validate()'>
				<table border='1'>
					<tr>
						<td>Nomor SKKI/O</td>
						<td>&nbsp;:&nbsp;</td>
						<td>
							<input type='hidden' name='edit' id='edit' value='$kon'>
							<input size='52' readonly type='text' name='skk' id='skk' value='$skk'>
						</td>
					</tr>
					<tr>
						<td>POS</td>
						<td>&nbsp;:&nbsp;</td>
						<td><input size='52' readonly type='text' name='pos' id='pos' value='$pos'></td>
						$namasisa <br>Disburse Anggaran = " . number_format($nilaisisa) . " Terkontrak = " . number_format($kontraksisa) . " Sisa = " . number_format($sisa) . "<input type='hidden' name='sisanya' id='sisanya' value='$sisa'><input type='hidden' name='sisabyr' id='sisabyr' value='$sisabyr'>
					</tr>
					<tr>
						<td>Nilai Kontrak</td>
						<td>&nbsp;:&nbsp;</td>
						<td><input size='52' readonly type='text' onChange='viewposskk(this.value, \"subpos\"); name='nilai' id='nilai' value='$nilai'></td>
					</tr>
					<tr>
						<td><div align='center'>Kontrak<br> <!-- <input type='button' value='+' onClick='tambahKontrak()'> --> </div></td>
						<td>&nbsp;:&nbsp;</td>
						<td>
							<div id='kontrak'>
								<table border='1'>
									<tr>
										<td colspan='2'><input type='checkbox' name='peti0' id='peti0'>Kontrak Petty Cash</td>
										<td>
											<select class='rutinselect' style='".($skktype == "SKKO" ? "" : "display:none;")."' name='rutin0' data-id='0'>
												$kontrak_type
											</select>

											<span id='rutininfo0' style='display:none;'>Manbil, Manbuild, Yantek, Yanti, PTL - IPP, Rumah Sakit, Apotik, Dokter, dll</span>
										</td>
									</tr>
									<tr>
										<td>Nomor Kontrak</td>
										<td>&nbsp;:&nbsp;</td>
										<td><input required size='49' type='text' name='nkontrak0' id='nkontrak0' value='$nomorkontrak'" . ($kon==""? "": " readonly") . "></td>
									</tr>
									<tr>
										<td>Uraian Kegitatan</td>
										<td>&nbsp;:&nbsp;</td>
										<td><textarea required rows='3' cols='47' name='uraian0' id='uraian0'>" . ($kon==""? "": $uraian) . "</textarea></td>
									</tr>
									<tr>
										<td>Vendor</td>
										<td>&nbsp;:&nbsp;</td>
										<td><input required size='49' type='text' name='vendor0' id='vendor0' value='" . ($kon==""? "": $vendor) . "'></td>
									</tr>
									<tr>
										<td>Nomor Dokumen</td>
										<td>&nbsp;:&nbsp;</td>
										<td><input required size='49' type='text' name='nodokumen0' id='nodokumen0' required value='$nodokumen'></td>
									</tr>
									<tr>
										<td>Tanggal Awal - Akhir (MM/DD/YYYY)</td>
										<td>&nbsp;:&nbsp;</td>
										<td>
											<input required size='22' type='" . ($nice? "date": "text") . "' name='awal0' id='awal0' " . ($nice? "": "onChange='dateCheck(\"awal0\")' ") . "value='$awal'> - 
											<input required size='22' type='" . ($nice? "date": "text") . "' name='akhir0' id='akhir0' " . ($nice? "": "onChange='dateCheck(\"akhir0\")' ") . "value='$akhir'>
										</td>
									</tr>
									<tr>
										<td>Bulan Tagih</td>
										<td>&nbsp;:&nbsp;</td>
										<td>
											<input required size='22' type='" . ($nice? "date": "text") . "' name='tgltagih0' id='tgltagih0'" . ($nice? "": "onChange='dateCheck(\"tgltagih0\")' ") . " value='$tgltagih'></td>
									</tr>
									<tr>
										<td>Nilai Kontrak</td>
										<td>&nbsp;:&nbsp;</td>
										<td><input required size='49' type='text' name='nilai0' id='nilai0' value='" . ($kon==""? "": $nilai) . "' onChange='totalkontrak()'></td>
									</tr>

									<tr>
										<td>Upload Kontrak</td>
										<td>&nbsp;:&nbsp;</td>
										<td><input type='file' name='uploadkontrak' id='uploadkontrak' accept='application/pdf' /></td>
									</tr>
									<tr>
										<td colspan='3'></td>
									</tr>
								</table>
							</div>
						</td>
					</tr>
					<tr>
						<td colspan='3' align='right'>
							<input type='submit' value='Simpan'>&nbsp;
							<input type='button' value='Batal' onclick='window.open(\"index.php\", \"_self\")'>
						</td>
					</tr>
				</table>
			</form>
		";
	?>
</body>
<script src="../js/jquery-1.12.0.min.js"></script>
<script src="../js/numeral.min.js"></script>

<script>

	$(document).ready(function() {

		$("#fkontrak").on("change", ".rutinselect", function(){

			var dis = $(this);
			var id = dis.data("id");
			var selected = dis.val();

			if(selected == 1){
				$("#rutininfo"+id).show();
			}else{
				$("#rutininfo"+id).hide();
			}
		}).change();

	} );

</script>
</html>