<?php
	session_start(); 
	$nip=$_SESSION['cnip'];
	$bidang=$_SESSION['bidang'];
	$kdunit=$_SESSION['kdunit'];
	$nama=$_SESSION['nama'];
	$adm=$_SESSION['adm'];
	$org=$_SESSION['org'];
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
	$query=mysqli_fetch_assoc($sql);
	$numid=$query['bayarid']+1;

	$lvl = $_REQUEST['lvl'];

	$numrow=1;
		foreach($sheet as $row) {
			if ($numrow>1) {
				$nokontrak = $row['A'];
				$nodokumen = $row['B'];
				$nilaibayar = $row['C'];
				$tglbayar = $row['D'];
				$pmn = $row['E'];
				$catatan = $row['F'];

				if ($nokontrak=="") {
					$nokontrak="na";
				}
				if ($nodokumen=="") {
					$nodokumen="na";
				}
				$datetime=date("Y-m-d H:i:s");
				$selectkontrak=mysqli_query("select * from kontrak where trim(nomorkontrak)='".trim($nokontrak)."'");
				$exekontrak=mysqli_fetch_assoc($selectkontrak);

				$kontrakdt=substr($exekontrak['inputdt'], 0, 10);

				$selectbayar=mysqli_query("SELECT SUM(nilaibayar) bayar FROM realisasibayar where trim(nokontrak)='".trim($exekontrak[nomorkontrak])."'");
				$exebayar=mysqli_fetch_assoc($selectbayar);

				$kontrakapproval=mysqli_query("SELECT t1.nomorkontrak nmrkontrak, signed, signdt, signlevel, actiontype, nilaitagihan, catatan, catatanreject FROM kontrak_approval t1 WHERE t1.id = (SELECT t2.id FROM kontrak_approval t2 WHERE TRIM(t2.nomorkontrak) = TRIM(t1.nomorkontrak) ORDER BY t2.signdt DESC LIMIT 1) and trim(t1.nomorkontrak) = '".trim($nokontrak)."'");

				$exeapproval=mysqli_fetch_assoc($kontrakapproval);

				$sisa_anggaran=$exekontrak['nilaikontrak'];


				if($exeapproval['signlevel'] >= 0 && $exeapproval['signlevel'] < 4 && $exeapproval['actiontype'] == 1){
					$list[]=array(
						'nokontrak' => $exekontrak['nomorkontrak'], 
						'nodokumen' => $nodokumen, 
						'nilaibayar' => $nilaibayar,
						'tglbayar' => $tglbayar,
						//'level' => $lvl."||".$exeapproval['signlevel'],
						'message' => 'Kontrak sedang di proses',
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
						//'level' => $lvl."||".$exeapproval['signlevel'],
						'message' => 'Nilai Kontrak melebihi sisa',
						'action' => 1
					);
					continue;
				}

				if ($exekontrak==false) {
					$list[]= array(
						'nokontrak' => $exekontrak['nomorkontrak'], 
						'nodokumen' => $nodokumen, 
						'nilaibayar' => $nilaibayar,
						'tglbayar' => $tglbayar,
						//'level' => $lvl."||".$exeapproval['signlevel'],
						'message' => 'No Kontrak dan No Dokumen Tidak Ditemukan',
						'action' => 1
					);
					continue;
				}
				
				mysqli_query("INSERT INTO kontrak_approval (nomorkontrak, actiontype, signdt, signed, signlevel, nilaitagihan, catatan) VALUES ('$exekontrak[nomorkontrak]', 1, sysdate(), '$nip', 0, '$nilaibayar', '$catatan')");

				// echo $kontrakdt;
				// echo "<br>".number_format($nilaibayar)."";
				// echo "<br>".number_format($exekontrak['nilaikontrak'])."";
				// echo "<br>".number_format($exebayar['bayar'])."";
				// echo "<hr>";

				if($lvl == 1){
					if(!empty($nodokumen)){
						mysqli_query("update kontrak set nodokumen='$nodokumen' where nomorkontrak='$exekontrak[nomorkontrak]'");
					}
				}

				$list[]=array(
						'nokontrak' => $exekontrak['nomorkontrak'], 
						'nodokumen' => $nodokumen, 
						'nilaibayar' => $nilaibayar, 
						'tglbayar' => $tglbayar,
						//'level' => $lvl."||".$exeapproval['signlevel'],
						'message' => 'Berhasil ditambahkan',
						'action' => 0
					);
				
				
					// print_r($exekontrak);
			}
			$numid++; 
			$numrow++;
		}

		// echo "<script>window.open('index.php', '_self'); alert($list);</script>";
		echo "<a href='index.php'>Back</a><hr>";
			foreach ($list as $key => $value) {
				
				echo "".($key+1)." - ".$value['nokontrak']." - ".$value['message']."<hr>";
			}
		echo "<a href='index.php'>Back</a>";
?>