<?php

/********FUNGSI COMBO TANGGAL***********/
function combotgl($awal, $akhir, $var, $terpilih){
  echo "<select name=$var id=$var>";
  for ($i=$awal; $i<=$akhir; $i++){
    if ($i==$terpilih)
      echo "<option value=$i selected>$i</option>";
    else
      echo "<option value=$i>$i</option>";
  }
  echo "</select> ";
}

function fcombotgl($awal, $akhir, $var, $terpilih){
  $nama_thn = "<select name=$var>";
  for ($i=$awal; $i<=$akhir; $i++){
    if ($i==$terpilih)
      $nama_thn.= "<option value=$i selected>$i</option>";
    else
      $nama_thn.= "<option value=$i>$i</option>";
  }
  $nama_thn.= "</select> ";
  return $nama_thn;
}

/********FUNGSI COMBO BULAN***********/
function combobln($var, $terpilih){
  $nama_bln=array(1=> "Januari", "Februari", "Maret", "April", "Mei", 
                      "Juni", "Juli", "Agustus", "September", 
                      "Oktober", "November", "Desember");
  echo "<select name=$var id=$var>";
  for ($bln=1; $bln<=12; $bln++){
      if ($bln==$terpilih)
         echo "<option value=$bln selected>$nama_bln[$bln]</option>";
      else
        echo "<option value=$bln>$nama_bln[$bln]</option>";
  }
  echo "</select> ";
}

function fcombobln($awal, $akhir, $var, $terpilih){
  $nama_bln=array(1=> "Januari", "Februari", "Maret", "April", "Mei", 
                      "Juni", "Juli", "Agustus", "September", 
                      "Oktober", "November", "Desember");
  $str_bulan="<select name=$var>";
  for ($bln=$awal; $bln<=$akhir; $bln++){
      if ($bln==$terpilih)
         $str_bulan.="<option value=$bln selected>$nama_bln[$bln]</option>";
      else
         $str_bulan.="<option value=$bln>$nama_bln[$bln]</option>";
  }
  $str_bulan.="</select>";
  return $str_bulan;
}

/********FUNGSI COMBO JAM***********/
function combojam($awal, $akhir, $var, $terpilih){
  echo "<select name=$var>";
  for ($i=$awal; $i<=$akhir; $i++){
    if ($i==$terpilih)
      echo "<option value=$i selected>$i</option>";
    else
      echo "<option value=$i>$i</option>";
  }
  echo "</select> ";
}

/********FUNGSI COMBO MENIT***********/
function combomenit($awal, $akhir, $var, $terpilih){
  echo "<select name=$var>";
  for ($i=$awal; $i<=$akhir; $i++){
    if ($i==$terpilih)
      echo "<option value=$i selected>$i</option>";
    else
      echo "<option value=$i>$i</option>";
  }
  echo "</select> ";
}

/********FUNGSI COMBO DETIK***********/
function combodetik($awal, $akhir, $var, $terpilih){
  echo "<select name=$var>";
  for ($i=$awal; $i<=$akhir; $i++){
    if ($i==$terpilih)
      echo "<option value=$i selected>$i</option>";
    else
      echo "<option value=$i>$i</option>";
  }
  echo "</select> ";
}

/********FUNGSI MENGIHITUNG SELISIH JAM***********/
function selisih($jam_masuk,$jam_keluar) {
list($h,$m,$s) = explode(":",$jam_masuk);
$dtAwal = mktime($h,$m,$s,"1","1","1");
list($h,$m,$s) = explode(":",$jam_keluar);
$dtAkhir = mktime($h,$m,$s,"1","1","1");
$dtSelisih = $dtAkhir-$dtAwal;
$totalmenit=$dtSelisih/60;
$jam =explode(".",$totalmenit/60);
$sisamenit=($totalmenit/60)-$jam[0];
$sisamenit2=$sisamenit*60;
$jml_jam=$jam[0];
return $jml_jam.":".$sisamenit2.":00";
}


/********FUNGSI COMBO TANGGAL 2***********/
function combotgl2($awal, $akhir, $var, $terpilih){
echo "<select name=$var>";
for ($i=$awal; $i<=$akhir; $i++){
if ($i==$terpilih)
  echo "<option value=$i selected>$i</option>";
else
  echo "<option value=$i>$i</option>";
}
echo "</select> ";
}

/********FUNGSI COMBO BULAN 2***********/
function combobln2($awal, $akhir, $var, $terpilih){
include "./config/library.php";
echo "<select name=$var>";
for ($bln=$awal; $bln<=$akhir; $bln++){
      if ($bln==$terpilih)
         echo "<option value=$bln selected>$nama_bln[$bln]</option>";
      else
        echo "<option value=$bln>$nama_bln[$bln]</option>";
}
echo "</select> ";
}

?>
