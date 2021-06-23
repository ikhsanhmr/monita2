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

	$num = $_REQUEST["num"];
	echo "
	<div id='kontrak$num'>
		
		<table border='1'>
			<tr>
				<td><input type='button' value='-' onClick='hapuskontrak(\"kontrak$num\")'>&nbsp;Nomor Kontrak</td>
				<td>&nbsp;:&nbsp;</td>
				<td><input required size='49' type='text' name='nkontrak$num' id='nkontrak$num'></td>
			</tr>
			<tr>
				<td>Uraian Kegitatan</td>
				<td>&nbsp;:&nbsp;</td>
				<td><textarea required rows='3' cols='47' name='uraian$num' id='uraian$num'></textarea></td>
			</tr>
			<tr>
				<td>Vendor</td>
				<td>&nbsp;:&nbsp;</td>
				<td><input required size='49' type='text' name='vendor$num' id='vendor$num'></td>
			</tr>
			<tr>
				<td>Tanggal Awal - Akhir (MM/DD/YYYY)</td>
				<td>&nbsp;:&nbsp;</td>
				<td>
					<input required size='22' type='" . ($nice? "date": "text") . "' name='awal$num' id='awal$num'  " . ($nice? "": "onChange='dateCheck(\"awal$num\")' ") . "> - 
					<input required size='22' type='" . ($nice? "date": "text") . "' name='akhir$num' id='akhir$num'  " . ($nice? "": "onChange='dateCheck(\"akhir$num\")' ") . ">
				</td>
			</tr>
			<tr>
				<td>Nilai Kontrak</td>
				<td>&nbsp;:&nbsp;</td>
				<td><input required size='49' type='text' name='nilai$num' id='nilai$num' onChange='totalkontrak()'></td>
			</tr>
			<tr>
				<td colspan='3'></td>
			</tr>
		</table>
	</div>
	";
?>