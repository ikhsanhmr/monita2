<!DOCTYPE html>
<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	
	<link href="../css/screen.css" rel="stylesheet" type="text/css">
	<link rel="stylesheet" href="assets/css/bootstrap.min.css" type="text/css">
	<link href="../css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" >
	<style type="text/css">
		#signButton{
			background: transparent;
			border: none;
			cursor: pointer;
		}
	</style>
	<script type="text/javascript">
		function viewk(x) {
			var p1 = document.getElementById("p1").value;
			var p2 = document.getElementById("p2").value;
			var b = document.getElementById("bidang").value;
			var p = document.getElementById("pelaksana").value;
			var k = document.getElementById("skk").value;
			var kpos = document.getElementById("kdpos").value;
			//alert(kpos);
			
			if(p1>p2 || p1=="") {
				if(p2!="") {
					var dummy = p1;
					p1 = p2;
					p2 = dummy;
				}
			}
			var url = encodeURI((x==undefined? "ai.php": "aiexcel.php") + "?p1="+p1+"&p2="+p2+"&b="+b+"&p="+p+"&k="+k+"&kpos="+kpos+"&v=1");
			//alert(url);
			window.open(url, "_self");
		}
		
		function signed(c, s, i) {
			//alert("ok");
			// var url = encodeURI("signed.php?" + "c=" + c + "&s=" + s);
			// window.open(url, "_self");
		        $.ajax({
		        	type: "get",
		            url: "signed.php",
		            data: {
		            	"c":c,
		            	"s":s,
		            	"i":i
		            },
		            success: function(data) {
		            	dataresult=JSON.parse(data)
                    if (dataresult[1]=='0') {
                    	$("#k"+dataresult[2]).html("<button id='signButton' onclick='signed("+'dataresult[0]'+", 1, "+'dataresult[2]'+")'><img src='no.png' width='24' height='24' alt='Signed' title='Signed'></img></button>");
                    }else{
                    	$("#k"+dataresult[2]).html("<button id='signButton' onclick='signed("+'dataresult[0]'+", 0, "+'dataresult[2]'+")'><img src='ok.png' width='24' height='24' alt='Unsigned' title='Unsigned'></img></button>");
                    }
                }
		    });
		}
		
		function bayar(k, lvl, id) {
			$.ajax({
	        	type: "get",
	            url: "bayar.php",
	            data: {
	            	"k":k,
	            	"lvl":lvl,
	            	"id":id,
	            	"t":1
	            },
	            success: function(data) {
	            	dataresult=JSON.parse(data);
	            	
	            	if (dataresult[3]){

                    	$("#b"+dataresult[2]).html("Kontrak sudah di inbox bayar.");
	            	}
                }
		    });
		}

	</script>
	
	<?php
//		header("Content-type: application/vnd.ms-excell");
//		header("Content-Disposition: attachment; Filename=ao.xls");

		session_start();
		if(!isset($_SESSION["nip"])) {
			echo "unauthorized user";
			echo "<script>window.open('../index.php', '_parent')</script>";
			exit;
		}
		
		$p1 = isset($_REQUEST["p1"])? $_REQUEST["p1"]: "";
		$p2 = isset($_REQUEST["p2"])? $_REQUEST["p2"]: "";
		$b0 = isset($_REQUEST["b"])? $_REQUEST["b"]: "";
		$p0 = isset($_REQUEST["p"])? $_REQUEST["p"]: "";
		$k0 = isset($_REQUEST["k"])? $_REQUEST["k"]: "";
		$kdpos0 = isset($_REQUEST["kpos"])? $_REQUEST["kpos"]: "";
		$v = isset($_REQUEST["v"])? $_REQUEST["v"]: "";
		
		$b = "<select name='bidang' id='bidang'>" . ($_SESSION["org"]=="" || $_SESSION["org"]==1 || $_SESSION["org"]==3 || $_SESSION["org"]>5? "<option value=''></option>": "");
		$p = "<select name='pelaksana' id='pelaksana'>" . ($_SESSION["org"]>5? "": "<option value=''></option>");

		require_once "../config/koneksi.php";
		$sql = "SELECT * FROM bidang ORDER BY LPAD(id, 2, '0')";
		$result = mysql_query($sql);
		$user = $_SESSION["cnip"];
		
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
			if($row["id"]<6) {
				$b .= ($_SESSION["org"]=="" || $_SESSION["org"]==1 || $_SESSION["org"]==3 || $_SESSION["org"]>5)?
					"<option value='$row[id]' " . ($row["id"]==$b0? "selected": "") . ">$row[namaunit]</option>":
					($row["id"]==$_SESSION["org"]? "<option value='$row[id]' " . ($row["id"]==$b0? "selected": "") . ">$row[namaunit]</option>": "");
			}

			if($user == "8610292Z" || $user == "94171330ZY"){
				$b .= ($row["id"] == 1? "<option value='$row[id]' " . ($row["id"]==$b0? "selected": "") . ">$row[namaunit]</option>": "");
			}
			$p .= ($_SESSION["org"]=="" || $_SESSION["org"]<=5)? 
				"<option value='$row[id]' " . ($row["id"]==$p0? "selected": "") . ">$row[namaunit]</option>":
				($row["id"]==$_SESSION["org"]? "<option value='$row[id]' " . ($row["id"]==$p0? "selected": "") . ">$row[namaunit]</option>": "");
		}
		mysql_free_result($result);
		$b .= "</select>";
		$p .= "</select>";
		
		$sql = "
			SELECT kdindukpos pos, namaindukpos namapos FROM posinduk UNION
			SELECT kdsubpos pos, namasubpos namapos FROM posinduk2 UNION
			SELECT kdsubpos pos, namasubpos namapos FROM posinduk3 UNION
			SELECT kdsubpos pos, namasubpos namapos FROM posinduk4
			ORDER BY pos";
		$result = mysql_query($sql);
		
		$kdpos = "<select name='kdpos' id='kdpos'><option value=''></option>";
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
			$kdpos .= "<option value='$row[pos]' " . ($row["pos"]==$kdpos0? "selected": "") . ">$row[pos] - $row[namapos]</option>";
		}
		$kdpos .= "</select>";
		mysql_free_result($result);
	?>
</head>

<body>
	<?php
		$parm = "";
//		$parm .= ($p1==""? "": " and SUBSTR(tanggalskki, 1, 7) >= '$p1'");
//		$parm .= ($p2==""? "": " and SUBSTR(tanggalskki, 1, 7) <= '$p2'");
		$parm .= ($p1==""? "": " and YEAR(tanggalskki) = " . substr($p1,0,4) . " AND MONTH(tanggalskki) >= " . substr($p1,-2));
		$parm .= ($p2==""? "": " and YEAR(tanggalskki) = " . substr($p2,0,4) . " AND MONTH(tanggalskki) <= " . substr($p2,-2));

		//$parm .= ($b0==""? "": " and (g.id = '$b0' or pelaksana = '$b0')");

		if ($user == "93162829ZY"){

			$parm .= ($b0==""? " AND d.pos1 IN (Select akses From akses_pos Where nip = '$user') ": " and (g.id = '$b0' or pelaksana = '$b0' or d.pos1 IN (Select akses From akses_pos Where nip = '$user'))");

		}else{

			$parm .= ($b0==""? "": " and (g.id = '$b0' or pelaksana = '$b0')");
		}

		$parm .= ($p0==""? "": " and pelaksana = '$p0'");
		$parm .= ($k0==""? "": " and nomorskki = '$k0'");
		$parm .= ($kdpos0==""? "": " and pos1 = '$kdpos0'");
		//echo "parm : $parm<br>";
		echo "
			<h2>Laporan Monitoring Penyerapan Anggaran Investasi</h2>
			<table>
				<tr>
					<th>Periode (yyyy-mm)</th>
					<td>:</td>
					<td><input type='month' name='p1' id='p1' value='$p1'> - <input type='month' name='p2' id='p2' value='$p2'></td>
				</tr>

				<tr>
					<th>Bidang</th>
					<td>:</td>
					<td>$b</td>
				</tr>
				<tr>
					<th>Pelaksana</th>
					<td>:</td>
					<td>$p</td>
				</tr>
				<tr>
					<th>No SKK</th>
					<td>:</td>
					<td><input type='text' name='skk' id='skk' size='49' value='$k0'></td>
				</tr>
				<tr>
					<th>POS</th>
					<td>:</td>
					<td>$kdpos</td>
				</tr>
				<tr>
					<td colspan='3' align='right'>
						<input type='button' value='View' onclick='viewk()'>
						<input type='button' value='Excel' onclick='viewk(1)'>
					</td>
				</tr>
			</table>";
		
		if($v!="") {
			$sql = "
				SELECT 
					n.nomornota, nipuser, g.id userid, 
					pelaksana, b.namaunit, pos1, nilai1, namapos,
					nomorwbs, nomorprk, nomorscore, nomorskki noskk, s.uraian uraians, tanggalskki, nilaianggaran anggaran, nilaidisburse disburse, nilaiwbs wbs, 
					inputdt, nomorkontrak, k.nodokumen nodokumen, vendor, k.uraian uraiank, tglawal, tglakhir, nilaikontrak kontrak, k.signed, bayar, k.kid , k.file_path, 
					(CASE WHEN ((ka.nmrkontrak IS NULL) OR (ka.nmrkontrak IS NOT NULL and (signlevel = 1 and actiontype = 0) OR (signlevel = 4 and actiontype = 1))) AND (nilaikontrak - IFNULL(bayar, 0) > 0) THEN 0 ELSE 1 END) kontrakapproved, ka.*
				FROM notadinas n
				LEFT JOIN bidang g ON n.nipuser = g.nick 
				LEFT JOIN notadinas_detail d ON n.nomornota = d.nomornota
				LEFT JOIN bidang b ON d.pelaksana = b.id  
				LEFT JOIN skkiterbit s ON d.noskk = s.nomorskki
				LEFT JOIN (
					SELECT kdindukpos pos, namaindukpos namapos FROM posinduk UNION
					SELECT kdsubpos pos, namasubpos namapos FROM posinduk2 UNION
					SELECT kdsubpos pos, namasubpos namapos FROM posinduk3 UNION
					SELECT kdsubpos pos, namasubpos namapos FROM posinduk4
				) p ON d.pos1 = p.pos
				LEFT JOIN kontrak k ON d.noskk = k.nomorskkoi AND d.pos1 = k.pos 
				LEFT JOIN (SELECT nokontrak, SUM(nilaibayar) bayar FROM realisasibayar GROUP BY nokontrak) r ON k.nomorkontrak = r.nokontrak LEFT JOIN
				(
					SELECT	t1.nomorkontrak as nmrkontrak, signlevel, actiontype
					FROM	kontrak_approval t1
					WHERE	t1.id = (SELECT	t2.id
									 FROM	kontrak_approval t2
									 WHERE	TRIM(t2.nomorkontrak) = TRIM(t1.nomorkontrak)          
									 ORDER BY t2.id DESC
									 LIMIT	1)
				) ka  ON TRIM(k.nomorkontrak) = TRIM(ka.nmrkontrak)
				WHERE d.progress >= 7 AND NOT nomorskki IS NULL 
				$parm
				ORDER BY nomorskki, LPAD(pelaksana, 2, '0'), k.pos, k.inputdt DESC, nomorkontrak";
			// echo $sql;
			// print_r($_SESSION);
			// return;
			//$hasil = "
			echo "
			<table border='1' id='dataTables'  cellspacing='0' width='100%'>
			<thead>
				<tr>
					<th rowspan='3' scope='col'>No Urut</th>
					<th colspan='11' scope='col'>skki Terbit</th>
					<th colspan='7' scope='col'>Kontrak</th>
					<th rowspan='3' scope='col'>Realisasi Pembayaran (Rp.)</th>
					<th colspan='2' scope='col'>Sisa</th>
					<th rowspan='3' scope='col'>Tgl Entry</th>
					<th rowspan='3' scope='col'>Attachment</th>
					<th rowspan='3' scope='col'>Bayar</th>
					<th rowspan='3' scope='col'>Status</th>
				</tr>
				<tr>
					<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>WBS <br /> PRK <br /> Basket / Fungsi</td>
					<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>Nomor</td>
					<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>Uraian</td>
					<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>Tgl Terbit</td>
					<td align='center' colspan='2' style='background-color:rgb(127,255,127)'>Nilai Ketetapan</td>
					<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>WBS (Rp.)</td>
					<td align='center' colspan='3' style='background-color:rgb(127,255,127)'>POS</td>
					<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>Pelaksana</td>
					<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>Nomor</td>
					<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>No. SAP</td>
					<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>Vendor</td>
					<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>Uraian</td>
					<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>Tgl Mulai</td>
					<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>Tgl Selesai</td>
					<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>Nilai</td>
					<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>SKKI (Disburse - Kontrak)</td>
					<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>Kontrak (Kontrak - Bayar)</td>
				</tr>
				<tr>
					<td style='background-color:rgb(127,255,127)'>Anggaran (Rp.)</td>
					<td style='background-color:rgb(127,255,127)'>Disburse (Rp.)</td>
					<td style='background-color:rgb(127,255,127)'>Kode</td>
					<td style='background-color:rgb(127,255,127)'>Ket</td>
					<td style='background-color:rgb(127,255,127)'>Nilai</td>
				</tr>
			</thead>
			<tbody>";
			$result = mysql_query($sql);
			
			$no = 0;
			$dummy = "";
			$dummypos = "";
			$hasil0 = "";
			$pnk = 0;
			$pnb = 0;
			$snk = 0;
			$snb = 0;
			$npost = 0;

			$angt = 0;
			$disbt = 0;
			$wbst = 0;
			$post = 0;
			$kont = 0;
			$bayt = 0;
			
			$hasilk = "";
			while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
				$cskk = ($dummy == $row["noskk"]? true: false);

				$statusbayar = "";

				if($row["kontrakapproved"] == 1){
					
					if(($row["signlevel"] == 0 && $row["actiontype"] == 1) || ($row["signlevel"] > 1 && $row["actiontype"] == 0)){

						$statusbayar = "Kontrak sudah di inbox bayar.";

					} elseif ($row["signlevel"] == 1 && $row["actiontype"] == 1){

						$statusbayar = "Menunggu Persetujuan Manager.";

					} elseif ($row["signlevel"] == 2 && $row["actiontype"] == 1){

						$statusbayar = "Menunggu Persetujuan Anggaran.";

					} elseif ($row["signlevel"] == 3 && $row["actiontype"] == 1){

						$statusbayar = "Menunggu Persetujuan Keuangan.";

					} elseif ($row["kontrak"] > 0 && $row["kontrak"] - $row["bayar"] <= 0){

						$statusbayar = "Kontrak Sudah Dibayar.";
					}
				}
				
				if($dummy != $row["noskk"] || $dummypos != $row["pos1"]) {
					if($no>0) {
						$hasilp .= "
								<td align='right'>".number_format($pnk)."</td>
								<td align='right'>".number_format($pnb)."</td>
								<td align='right'>".number_format($npos-$pnk)."</td>
								<td align='right'>".number_format($pnk-$pnb)."</td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
							</tr>
						";
						
						$hasil0 .= $hasilp . $hasilk;
						$pnk = 0;
						$pnb = 0;
						$hasilk = "";
					}

					$hasilp = "
						<tr>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td align='right'></td>
							<td align='right'></td>
							<td align='right'></td>
							<td>$row[pos1]</td>
							<td>$row[namapos]</td>
							<td align='right'>".number_format($row["nilai1"])."</td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>";
							
							$npos = $row["nilai1"];
							//if($dummy!=$row["noskk"])
							$npost = ($dummy==$row["noskk"]? $npost+$row["nilai1"]: $npost);
							$post += $row["nilai1"];
/*							
							<td align='right'>".number_format($row["nilai1"])."</td>
							<td align='right'>".number_format($row["nilai1"])."</td>
							<td align='right'>".number_format($row["nilai1"])."</td>
							<td align='right'>".number_format($row["nilai1"])."</td>
							<td></td>
						</tr>
					";
*/
				}
				
				if($dummy != $row["noskk"]) {
					if($no>0) {
						$hasils .= "
								<td align='right'>".number_format($npost)."</td>
								<td>$nu</td>
								<td></td>
								<td align='right'>$row[nodokumen]</td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td align='right'>".number_format($snk)."</td>
								<td align='right'>".number_format($snb)."</td>
								<td align='right'>".number_format($disb-$snk)."</td>
								<td align='right'>".number_format($snk-$snb)."</td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
							</tr>
						";
						
						//$hasil .= $hasils . $hasil0;
						echo $hasils . $hasil0;
						
						//$hasil = "";
						$hasil0 = "";
						$snk = 0;
						$snb = 0;
					}

					$no++;
					$npost = $row["nilai1"];
					$nu = $row["namaunit"];
					$disb = $row["disburse"];
					
					$angt += $row["anggaran"];
					$disbt += $row["disburse"];
					$wbst += $row["wbs"];
					
					$hasils = "
						<tr>
							<td>$no</td>
							<td style='white-space: nowrap;'>" . $row["nomorwbs"] . ($row["nomorwbs"]!="" && $row["nomorprk"]!=""? " <br /> ": "") . $row["nomorprk"] . ($row["nomorscore"]==""? "": "<br /> $row[nomorscore]") . "</td>
							<td>$row[noskk]</td>
							<td>$row[uraians]</td>
							<td>$row[tanggalskki]</td>
							<td align='right'>".number_format($row["anggaran"])."</td>
							<td align='right'>".number_format($row["disburse"])."</td>
							<td align='right'>".$row["wbs"]."</td>
							<td></td>
							<td></td>";
/*							
							<td align='right'>".number_format($npost)."</td>
							<td>$row[namaunit]</td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td align='right'>".number_format($npos$row["nilai1"])."</td>
							<td align='right'>".number_format($row["nilai1"])."</td>
							<td align='right'>".number_format($row["nilai1"])."</td>
							<td align='right'>".number_format($row["nilai1"])."</td>
							<td>$row[inputdt]</td>
						</tr>
					";
*/
				}
				
				
				$dummypos = $row["pos1"];
				$dummy = $row["noskk"];
				
				$snk += $row["kontrak"];
				$snb += $row["bayar"];
				
				$pnk += $row["kontrak"];
				$pnb += $row["bayar"];
				
				$kont += $row["kontrak"];
				$bayt += $row["bayar"];
				
				$hasilk .= "
					<tr>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td align='right'></td>
						<td align='right'></td>
						<td align='right'></td>
						<td></td>
						<td></td>
						<td align='right'></td>
						<td></td>
						<td>$row[nomorkontrak]</td>
						<td>$row[nodokumen]</td>
						<td>$row[vendor]</td>
						<td>$row[uraiank]</td>
						<td>$row[tglawal]</td>
						<td>$row[tglakhir]</td>
						<td align='right'>".number_format($row["kontrak"])."</td>
						<td align='right'>".number_format($row["bayar"])."</td>
						<td></td>
						<td align='right'>".number_format($row["kontrak"] - $row["bayar"])."</td>
						<td>$row[inputdt]</td>
						<td>".
						($row["file_path"]==""? "": "<br><a href='../$row[file_path]' target='_blank'>Download</a>") ."</td>
						<td>". 
							($row["kontrakapproved"]== 1? $statusbayar : 
								(
									$_SESSION["org"]=="" || $_SESSION["nip"]=="KEU" ? 
										""
									: "<div id='b$row[kid]'><button id='bayarButton' onclick='bayar(\"$row[nomorkontrak]\", 0, \"$row[kid]\")'>BAYAR</button></div>"
								)
							) .
						"</td>
						<td>". 
							($row["nomorkontrak"]==""? "": 
								(
									$_SESSION["roleid"] <= 3 ? 
										($row["signed"]==""? 
											"<div id='k$row[kid]'><button id='signButton' onclick='signed(\"$row[nomorkontrak]\", 1, \"$row[kid]\")'><img src='no.png' width='24' height='24' alt='Signed' title='Signed'></img></button></div>": 
											"<div id='k$row[kid]'><button id='signButton' onclick='signed(\"$row[nomorkontrak]\", 0, \"$row[kid]\")'><img src='ok.png' width='24' height='24' alt='Unsigned' title='Unsigned'></img></button></div>"
										)
									: ($row["signed"]==""? "": "Signed")
								)
							) .
						"</td>						
					</tr>
				";
			}
			mysql_free_result($result);
			
			$hasilp .= "
					<td align='right'>".number_format($pnk)."</td>
					<td align='right'>".number_format($pnb)."</td>
					<td align='right'>".number_format($npos-$pnk)."</td>
					<td align='right'>".number_format($pnk-$pnb)."</td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
			";
			
			$hasil0 .= $hasilp . $hasilk;
			$pnk = 0;
			$pnb = 0;
			$hasilk = "";

			$hasils .= "
					<td align='right'>".number_format($npost)."</td>
					<td>$nu</td>
					<td></td>
					<td align='right'>$row[nodokumen]</td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td align='right'>".number_format($snk)."</td>
					<td align='right'>".number_format($snb)."</td>
					<td align='right'>".number_format($disb-$snk)."</td>
					<td align='right'>".number_format($snk-$snb)."</td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
			";
			
			//$hasil .= $hasils . $hasil0;
			echo $hasils . $hasil0;
			
			//$hasil = "";
			$hasil0 = "";
			$snk = 0;
			$snb = 0;
			
			//$hasil .= "</table>";
			echo "
				<tfoot>
				<tr>
					<td colspan='5'>Total</td>
					<td align='right'>" . number_format($angt) . "</td>
					<td align='right'>" . number_format($disbt) . "</td>
					<td align='right'>" . number_format($wbst) . "</td>
					<td></td>
					<td></td>
					<td align='right'>" . number_format($post) . "</td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td align='right'>" . number_format($kont) . "</td>
					<td align='right'>" . number_format($bayt) . "</td>
					<td align='right'>" . number_format($disbt-$kont) . "</td>
					<td align='right'>" . number_format($kont-$bayt) . "</td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
				</tfoot>";
			  echo "</tbody>";	
			echo "</table>";

		}
		mysql_close($kon);
		
		//echo $hasil;
	?>
</body>
  <script type="text/javascript" src="../js/jquery-1.12.0.min.js"></script>
  <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
<script>
		$(document).ready(function() {
			$('#dataTables').DataTable(
			{
				"bPaginate": false,
				"ordering": false
			});
		} );
	</script>
</html>
