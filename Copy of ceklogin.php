<?php
session_start();
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING | E_DEPRECATED));
require_once("config/method.php");	
require_once("config/incldap.php");
require_once 'config/koneksi.php';
//untuk user manual
$nip = $_POST['UserID'];
//tidak menggunakan password
//$password = $_POST['password'];
$pasword = md5($_POST['pasword']);
$pasword_md5 = md5($pasword);
// query untuk mendapatkan record dari username
$query = "SELECT * FROM user WHERE nip = '$nip'";
$hasil = mysql_query($query);
$data = mysql_fetch_array($hasil);	
//Connect to Active Directory
$ad = ldap_connect($ad_server);
ldap_set_option($ad, LDAP_OPT_PROTOCOL_VERSION, 3);
ldap_set_option($ad, LDAP_OPT_REFERRALS, 0);
	
$u = $_POST["UserID"];
$p = $_POST["pasword"];	
$msg = "1";
$adusn = $u.$ad_usn_postfix; $adpwd = $p;
$bind = ldap_bind($ad, $adusn, $adpwd);
$ldapErr = ldap_errno($ad);
//	echo "$bind - $adusn - $adpwd - $ldapErr<br>";
$AD_employeeNumber = "";
if ($ldapErr==0 && ($u!="" && $p!=""))  { //JIKA LDAP LOGIN BERHASIL 
		//Ambil data nama dari active directory
		$ldap_search_param = "(&"."(sAMAccountName=$u)".")";
		$ldap_search_return = array('displayname','employeenumber','mail','company','department','title');
		
		$search = ldap_search($ad, $ad_dn, $ldap_search_param, $ldap_search_return);
		$entries = ldap_get_entries($ad, $search);
		
		$AD_displayName = substr(str_replace("'","\'",$entries[0]['displayname'][0]),0,150);
		$AD_employeeNumber = substr(str_replace("'","\'",$entries[0]['employeenumber'][0]),0,14);
		$AD_mail = substr(str_replace("'","\'",$entries[0]['mail'][0]),0,150);
		$AD_company = substr(str_replace("'","\'",$entries[0]['company'][0]),0,150);
		$AD_department = substr(str_replace("'","\'",$entries[0]['department'][0]),0,150);
		$AD_title = substr(str_replace("'","\'",$entries[0]['title'][0]),0,150);
		//echo $AD_employeeNumber;

		$AD_mail="$u@pln.co.id";
		error_reporting(E_ALL);
		$db = dbConn();
		$query = "SELECT * FROM user WHERE email = '$AD_mail'";
		$rows = db($db, $query);
		dbClose($db);
		if(count($rows)==0) {
			$msg = "Unregistered User";
		} else {
			session_start();
			foreach($rows as $row) {
				$_SESSION["nip"] = ($row["nip"]==$row["nip1"]? $row["nip"]: $row["nip1"]);
				$_SESSION["cnip"] = $row["nip"];
				$_SESSION["bidang"] = $row["bidang"];
				$_SESSION["kdunit"] = $row["kdunit"];
				$_SESSION["adm"] = $row["adm"];
				$_SESSION["nama"] = $row["nama"];
				$_SESSION["org"] = $row["kodeorg"];
				$_SESSION["roleid"] = $row["roleid"];
				$_SESSION["itreporoleid"] = $row["roleid"];
				$_SESSION['nip'] = ($row['nip']==$row['nip1']? $row['nip']: $row['nip1']);
				$_SESSION['cnip'] = $row['nip'];
				$_SESSION['bidang'] = $row['bidang'];
				$_SESSION['kdunit'] = $row['kdunit'];
				$_SESSION['adm'] = $row['adm'];
				$_SESSION['nama'] = $row['nama'];
				$_SESSION['org'] = $row['kodeorg'];
				$_SESSION['roleid'] = $row['roleid'];
				$_SESSION['itreporoleid'] = $row['roleid'];
			}
		}
		header("location:home.php");
 } 
 elseif ($pasword == $data['pasword']) {// menyimpan username dan level ke dalam session
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
} else {
	echo "<h1>Login gagal</h1>";
	echo "<script>alert('Login gagal'); window.open('index.php', '_self')</script>";
}
?>