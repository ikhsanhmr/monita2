<!DOCTYPE html>
<head>
	<link href="css/screen2.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="js/methods.js"></script>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>User Management</title>
</head>

<body>

<?php
session_start();
if(!isset($_SESSION['cnip'])) {
	echo "<script>window.open('.', '_self')</script>";
	exit;
}

echo "
	<h2>User Management</h2>
	<form name='frm' id='frm'>
	<table>
		<tr>
			<td>&nbsp;User&nbsp;</td>
			<td>&nbsp;:&nbsp;</td>
			<td>&nbsp;<input type='text' name='nip' id='nip' value='$_SESSION[cnip]' readonly>&nbsp;</td>
		</tr>
		<tr>
			<td>&nbsp;Password Lama&nbsp;</td>
			<td>&nbsp;:&nbsp;</td>
			<td>&nbsp;<input type='password' name='opwd' id='opwd'>&nbsp;</td>
		</tr>
		<tr>
			<td>&nbsp;Password Baru&nbsp;</td>
			<td>&nbsp;:&nbsp;</td>
			<td>&nbsp;<input type='password' name='pwd' id='pwd' value=''>&nbsp;</td>
		</tr>
		<tr>
			<td>&nbsp;Ketik Ulang Password&nbsp;</td>
			<td>&nbsp;:&nbsp;</td>
			<td>&nbsp;<input type='password' name='rpwd' id='rpwd' value=''>&nbsp;</td>
		</tr>
		<tr>
			<td colspan='3' align='right'>&nbsp;<input type='button' value='Save' onclick='savepwd()'>&nbsp;</td>
		</tr>
	</table>
	</form>
";
?>

</body>

</html> 