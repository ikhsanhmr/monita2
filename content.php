<?php
	session_start();
	if(!isset($_SESSION['nip'])) {
		echo "unauthorized user";
		echo "<script>window.open('index.php', '_parent')</script>";
		exit;
	}
	
	if($_SESSION['roleid']<=3) {
		require_once "config/control.inc.php";
		$link = new mysqli($srv, $usr, $pwd,$db);
		
		if (!$link) {
			echo "Failed to connect to MySQL: " . $mysqli -> connect_error; exit();
		}
		//mysqli_select_db($db);
	
		$sql = "SELECT COUNT(*) jumlah FROM notadinas WHERE " . ($_SESSION["roleid"]==1? "coalesce(progress,0) = 0": "nip = '$_SESSION[nip]' AND coalesce(progress,0) = 2");
		//echo "$sql<br>";
		$result = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli)) or die (mysqli_error());
		while ($row = mysqli_fetch_array($result)) {
			$nd = $row["jumlah"];
		}
		mysqli_free_result($result);
		
		$sql = "SELECT COUNT(*) jumlah FROM kontrak k INNER JOIN skkiterbit i ON k.nomorskkoi = i.nomorskki WHERE SIGNED IS NULL";
		//echo "$sql<br>";
		$result = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli)) or die (mysqli_error());
		while ($row = mysqli_fetch_array($result)) {
			$kk = $row["jumlah"];
		}
		mysqli_free_result($result);
		$mysqli->close();($link);	
		
		echo "Halo $_SESSION[nama],<br><br>";
		echo "Terdapat : <br>";
		echo "- $nd Nota Dinas Baru<br>";
		echo "- $kk Kontrak baru / kontrak yang belum SIGNED<br>";
	}
?>