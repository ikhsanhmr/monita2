<?php

// memulai session
session_start();
require_once 'config/koneksi.php';

$nip = $_POST['nip'];
//tidak menggunakan password
//$password = $_POST['password'];
$pasword = md5($_POST['pasword']);
$pasword_md5 = md5($pasword);

// query untuk mendapatkan record dari username
$query = "SELECT * FROM user WHERE nip = '$nip'";
$hasil = mysql_query($query);
$data = mysql_fetch_array($hasil);

// cek kesesuaian password
if ($pasword == $data['pasword'])
{
    // menyimpan username dan level ke dalam session
    $_SESSION['nip'] = ($data['nip']==$data['nip1']? $data['nip']: $data['nip1']);
    $_SESSION['cnip'] = $data['nip'];
	$_SESSION['bidang'] = $data['bidang'];
	$_SESSION['kdunit'] = $data['kdunit'];
    $_SESSION['adm'] = $data['adm'];
    $_SESSION['nama'] = $data['nama'];
    $_SESSION['org'] = $data['kodeorg'];
    $_SESSION['roleid'] = $data['roleid'];

    // tampilkan menu
    header("location:home.php");

}
else echo "<h1>Login gagal</h1>";
echo "<script>alert('Login gagal'); window.open('index.php', '_self')</script>";

?>