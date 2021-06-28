<?php
	session_start(); 
	$nip=$_SESSION['nip'];
	$bidang=$_SESSION['bidang'];
	$kdunit=$_SESSION['kdunit'];
	$nama=$_SESSION['nama'];
	$adm=$_SESSION['adm'];
	if($nip=="") {exit;}

	require_once '../config/koneksi.php';

	require_once '../lib/PHPExcel/PHPExcel.php';

	$file_uploaded=$_FILES['upload'];
	$name_uploaded=$_FILES['upload']['name'];
	$type_uploaded=$_FILES['upload']['type'];
	$tmp_uploaded=$_FILES['upload']['tmp_name'];

	move_uploaded_file($tmp_uploaded, '../files/excel/'.$name_uploaded.'');
	$excelreader = new PHPExcel_Reader_Excel2007();
	$loadexcel = $excelreader->load('../files/excel/'.$name_uploaded);
	$sheet = $loadexcel->getActiveSheet()->toArray(null, true, true ,true);
	$list=array();
	$sql=mysqli_query("select bayarid from realisasibayar order by bayarid desc");
	$query=mysql_fetch_assoc($sql);
	$numid=$query['bayarid']+1;
	$numrow=1;
		foreach($sheet as $row) {
			if ($numrow>1) {
				$nokontrak = $row['A'];
				$nodokumen = $row['B'];
				$nilaibayar = $row['C'];
				$tglbayar = $row['D'];
				$pmn = $row['E'];

				if ($nokontrak=="") {
					$nokontrak="na";
				}
				if ($nodokumen=="") {
					$nodokumen="na";
				}
				$datetime=date("Y-m-d H:i:s");
				$selectkontrak=mysqli_query("select * from kontrak where nomorkontrak='$nokontrak' OR nodokumen='$nodokumen'");
				$exekontrak=mysql_fetch_assoc($selectkontrak);

				$kontrakdt=substr($exekontrak['inputdt'], 0, 10);
				$selectbayar=mysqli_query("SELECT SUM(nilaibayar) bayar FROM realisasibayar where nokontrak='$exekontrak[nomorkontrak]' OR nodokrep='$exekontrak[nodokumen]'");
				$exebayar=mysql_fetch_assoc($selectbayar);

				$sisa_anggaran=$exekontrak['nilaikontrak'];
				
				// echo $kontrakdt;
				// echo "<br>".number_format($nilaibayar)."";
				// echo "<br>".number_format($exekontrak['nilaikontrak'])."";
				// echo "<br>".number_format($exebayar['bayar'])."";
				// echo "<hr>";

				if ($exekontrak==false) {
					$list[]= array(
						'nokontrak' => $exekontrak['nomorkontrak'], 
						'nodokumen' => $nodokumen, 
						'nilaibayar' => $nilaibayar,
						'tglbayar' => $tglbayar,
						'message' => 'No Kontrak dan No Dokumen Tidak Ditemukan',
						'action' => 1
					);
					continue;
				}
				
				if (($exekontrak['nilaikontrak']-$exebayar['bayar'])<$nilaibayar) {
					$list[]=array(
						'nokontrak' => $exekontrak['nomorkontrak'], 
						'nodokumen' => $nodokumen, 
						'nilaibayar' => $nilaibayar,
						'tglbayar' => $tglbayar,
						'message' => 'Nilai Kontrak melebihi sisa',
						'action' => 1
					);
					continue;
				}

				$list[]=array(
						'nokontrak' => $exekontrak['nomorkontrak'], 
						'nodokumen' => $nodokumen, 
						'nilaibayar' => $nilaibayar, 
						'tglbayar' => $tglbayar,
						'message' => 'Berhasil ditambahkan',
						'action' => 0
					);


				mysqli_query("insert into realisasibayar(nokontrak,nodokrep,nilaibayar,tglbayar,bayarid,pmn) values('$exekontrak[nomorkontrak]','$nodokumen','$nilaibayar','$tglbayar','$numid','$pmn')");
				mysqli_query("update kontrak set signed='$nip',signeddt='$datetime' where nomorkontrak='$exekontrak[nomorkontrak]' OR nomordokumen='$nodokumen'");
				
					// print_r($exekontrak);
			}
			$numid++; 
			$numrow++;
		}

		// echo "<script>window.open('index.php', '_self'); alert($list);</script>";
		echo "<a href='index.php'>Back</a><hr>";
			foreach ($list as $key => $value) {
				if ($value['action']==1) {
				mysqli_query("insert into frealisasibayar(nokontrak, nodokumen, nilaibayar, uploaddate, message) values ('$value[nokontrak]','$value[nodokumen]', '$value[nilaibayar]','$value[tglbayar]', '$value[message]')");
				}
				echo "".($key+1)." - ".$value['nokontrak']."-".$value['message']."<hr>";
			}
		echo "<a href='index.php'>Back</a>";
?>