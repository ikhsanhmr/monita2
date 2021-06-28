<?php

    session_start();
    $nip = $_SESSION['nip'];
    if($nip=="") {
        exit;
    }

    require_once "../config/koneksi.php";
    
    $user = $_SESSION["cnip"];

    $p1 = isset($_REQUEST["p1"])? $_REQUEST["p1"]: "";
    $p2 = isset($_REQUEST["p2"])? $_REQUEST["p2"]: "";
    $b0 = isset($_REQUEST["b"])? $_REQUEST["b"]: "";
    $p0 = isset($_REQUEST["p"])? $_REQUEST["p"]: "";
    $k0 = isset($_REQUEST["k"])? $_REQUEST["k"]: "";
    $kdpos0 = isset($_REQUEST["kpos"])? $_REQUEST["kpos"]: "";
    $draw = isset($_REQUEST["draw"])? $_REQUEST["draw"]: "";
    $start = isset($_REQUEST["start"])? $_REQUEST["start"]: "";
    $length = isset($_REQUEST["length"])? $_REQUEST["length"]: "";

    $parm = "";
    // $parm2 = "";
    // $parm .= ($p1==""? "": " and SUBSTR(tanggalskki, 1, 7) >= '$p1'");
    // $parm .= ($p2==""? "": " and SUBSTR(tanggalskki, 1, 7) <= '$p2'");
    $parm .= ($p1==""? "": " and YEAR(tanggalskki) = " . substr($p1,0,4) . " AND MONTH(tanggalskki) >= " . substr($p1,-2));
    $parm .= ($p2==""? "": " and YEAR(tanggalskki) = " . substr($p2,0,4) . " AND MONTH(tanggalskki) <= " . substr($p2,-2));
    
    // $parm2 .= ($p1==""? "": " and YEAR(tanggalskki) = " . substr($p1,0,4) . " AND MONTH(tanggalskki) >= " . substr($p1,-2));
    // $parm2 .= ($p2==""? "": " and YEAR(tanggalskki) = " . substr($p2,0,4) . " AND MONTH(tanggalskki) <= " . substr($p2,-2));
    // $parm3 .= ($p1==""? "": " and YEAR(inputdt) >= " . substr($p1,0,4));
    // $parm3 .= ($p2==""? "": " and YEAR(inputdt) <= " . substr($p2,0,4));

    //$parm .= ($b0==""? "": " and (g.id = '$b0' or pelaksana = '$b0')");

    if ($user == "93162829ZY"){

        // $parm .= ($b0==""? " AND d.pos1 IN (Select akses From akses_pos Where nip = '$user') ": " and (g.id = '$b0' or pelaksana = '$b0' or d.pos1 IN (Select akses From akses_pos Where nip = '$user'))");
        $parm .= ($b0==""? " AND c.pos1 IN (Select akses From akses_pos Where nip = '$user') ": " and (nipuser = '$b0' or b.nick = '$b0' or c.pos1 IN (Select akses From akses_pos Where nip = '$user'))");

    }else{

        // $parm .= ($b0==""? "": " and (g.id = '$b0' or pelaksana = '$b0')");
        $parm .= ($b0==""? "": " and (nipuser = '$b0' or b.nick = '$b0')");
    }

    $parm .= ($p0==""? "": " and pelaksana = '$p0'");
    $parm .= ($k0==""? "": " and nomorskki = '$k0'");
    $parm .= ($kdpos0==""? "": " and pos1 = '$kdpos0'");

    $limit = ($start != "" && $length != "" ? " LIMIT $start , $length": "");

    $sql = "
        SELECT 	n.nomornota, nipuser, pelaksana, b.namaunit, pos1, nilai1, namapos, nomorwbs, nomorprk, nomorscore,
                nomorskki noskk, s.uraian uraians, DATE_FORMAT(tanggalskki, '%d-%m-%Y') as tanggalskki, 
                nilaianggaran anggaran, nilaidisburse disburse, nilaiwbs wbs, pdp.file_path as pdp_file_path, s.sl1,
                DATE_FORMAT(inputdt, '%d-%m-%Y %H:%i:%s') as inputdt, k.nomorkontrak, vendor, k.kid, s.sl3, 
                k.nodokumen nodokumen, k.uraian uraiank, DATE_FORMAT(tglawal, '%d-%m-%Y') as tglawal, k.file_path, 
                nilaikontrak kontrak, DATE_FORMAT(tglakhir, '%d-%m-%Y') as tglakhir, k.signed, bayar, ka.*, s.jtm, 
                rb.no_rab, rb.nilai_rp, pdp.jtmaset, pdp.jtmrp, pdp.gdaset, pdp.gdrp, pdp.jtraset, pdp.jtrrp, s.gd, 
                pdp.sl1aset, pdp.sl1rp, pdp.sl3aset, pdp.sl3rp, pdp.keypointaset, pdp.keypointrp, s.keypoint, s.jtr,
                s.nilaianggaranjtr, s.nilaianggaransl1, DATE_FORMAT(kaang.signdt, '%d-%m-%Y %H:%i:%s') as app_ang, 
                s.nilaianggaransl3, s.nilaianggarankp, DATE_FORMAT(kakeu.signdt, '%d-%m-%Y %H:%i:%s') as app_keu, 
                s.nilaianggaranjtm, s.nilaianggarangd, DATE_FORMAT(kapel.signdt, '%d-%m-%Y %H:%i:%s') as app_pel, 
                (CASE WHEN ((ka.nmrkontrak IS NULL) OR (ka.nmrkontrak IS NOT NULL and (signlevel = 1 and actiontype = 0) OR (signlevel = 4 and actiontype = 1))) AND (nilaikontrak - IFNULL(bayar, 0) > 0) THEN 0 ELSE 1 END) kontrakapproved
        FROM 	notadinas n	LEFT JOIN  
                notadinas_detail d ON n.nomornota = d.nomornota	LEFT JOIN 
                bidang b ON d.pelaksana = b.id LEFT JOIN 
                skkiterbit s ON d.noskk = s.nomorskki LEFT JOIN (
                    SELECT kdindukpos pos, namaindukpos namapos FROM posinduk UNION
                    SELECT kdsubpos pos, namasubpos namapos FROM posinduk2 UNION
                    SELECT kdsubpos pos, namasubpos namapos FROM posinduk3 UNION
                    SELECT kdsubpos pos, namasubpos namapos FROM posinduk4
                ) p ON d.pos1 = p.pos LEFT JOIN 
                kontrak k ON d.noskk = k.nomorskkoi AND d.pos1 = k.pos LEFT JOIN 
                (
                    SELECT nokontrak, SUM(nilaibayar) bayar 
                    FROM realisasibayar 
                    GROUP BY nokontrak
                ) r ON k.nomorkontrak = r.nokontrak LEFT JOIN
                (
                    SELECT	t.nomorkontrak as nmrkontrak, signlevel, actiontype
                    FROM	kontrak_approval t INNER JOIN 
                            (
                                SELECT nomorkontrak, MAX( id ) AS lastid
                                FROM kontrak_approval
                                GROUP BY nomorkontrak
                            )tm ON t.nomorkontrak = tm.nomorkontrak AND t.id = tm.lastid
                ) ka  ON TRIM(k.nomorkontrak) = TRIM(ka.nmrkontrak) LEFT JOIN 
                rab rb ON k.no_rab = rb.no_rab LEFT JOIN 
                asetpdp pdp ON k.nomorkontrak = pdp.nomorkontrak LEFT JOIN
                (
                    SELECT	nomorkontrak, max(signdt) as signdt
                    FROM	kontrak_approval
                    Where 	signlevel <= 2 and actiontype = 1
                    GROUP BY nomorkontrak
                ) kapel  ON TRIM(k.nomorkontrak) = TRIM(kapel.nomorkontrak) LEFT JOIN
                (
                    SELECT	nomorkontrak, max(signdt) as signdt
                    FROM	kontrak_approval
                    Where	signlevel = 3 and actiontype = 1
                    GROUP BY nomorkontrak
                ) kaang  ON TRIM(k.nomorkontrak) = TRIM(kaang.nomorkontrak) LEFT JOIN
                (
                    SELECT	nomorkontrak, max(signdt) as signdt
                    FROM	kontrak_approval
                    Where	signlevel = 4 and actiontype = 1
                    GROUP BY nomorkontrak
                ) kakeu  ON TRIM(k.nomorkontrak) = TRIM(kakeu.nomorkontrak)
                WHERE d.progress >= 7 AND NOT nomorskki IS NULL 
                $parm
                ORDER BY nomorskki, LPAD(pelaksana, 2, '0'), k.pos, k.inputdt DESC, k.nomorkontrak
                
    ";

    $allresult = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));

    $result = mysqli_query($sql.$limit);

    $no = 0;
    $dummy = "";
    $dummypos = "";
    $hasil0 = "";
    $pnk = 0;
    $pnb = 0;
    $prab = 0;
    $pjtmaset = 0;
    $pjtmrp = 0;
    $pgdaset = 0;
    $pgdrp = 0;
    $pjtraset = 0;
    $pjtrrp = 0;
    $psl1aset = 0;
    $psl1rp = 0;
    $psl3aset = 0;
    $psl3rp = 0;
    $pkeypointaset = 0;
    $pkeypointrp = 0;

    $snk = 0;
    $snb = 0;
    $srab = 0;
    $sjtmaset = 0;
    $sjtmrp = 0;
    $sgdaset = 0;
    $sgdrp = 0;
    $sjtraset = 0;
    $sjtrrp = 0;
    $ssl1aset = 0;
    $ssl1rp = 0;
    $ssl3aset = 0;
    $ssl3rp = 0;
    $skeypointaset = 0;
    $skeypointrp = 0;

    $npost = 0;

    $angt = 0;
    $disbt = 0;
    $wbst = 0;
    $post = 0;
    $kont = 0;
    $bayt = 0;

    $ang_jtm_assett = 0;
    $ang_gd_assett = 0;
    $ang_jtr_assett = 0;
    $ang_sl1_assett = 0;
    $ang_sl3_assett = 0;
    $ang_kp_assett = 0;

    $ang_jtm_rpt = 0;
    $ang_gd_rpt = 0;
    $ang_jtr_rpt = 0;
    $ang_sl1_rpt = 0;
    $ang_sl3_rpt = 0;
    $ang_kp_rpt = 0;

    $rea_jtm_assett = 0;
    $rea_gd_assett = 0;
    $rea_jtr_assett = 0;
    $rea_sl1_assett = 0;
    $rea_sl3_assett = 0;
    $rea_kp_assett = 0;

    $rea_jtm_rpt = 0;
    $rea_gd_rpt = 0;
    $rea_jtr_rpt = 0;
    $rea_sl1_rpt = 0;
    $rea_sl3_rpt = 0;
    $rea_kp_rpt = 0;
    
    $hasilk = "";
    $urutanrow = 0;
    $parent_skki = 0;
    $parent_pos = 0;
    $data_master = array();
    $empty_kontrak = array(
        "no_rab"        => "",
        "nilai_rp"      => "",
        "nomorkontrak"  => "",
        "nodokumen"     => "",
        "vendor"        => "",
        "uraiank"       => "",
        "tglawal"       => "",
        "tglakhir"      => "",
        "kontrak"       => "",
        "bayar"         => "",
        "sisabayar"     => "",
        "jtmaset"       => "",
        "jtmrp"         => "",
        "gdaset"        => "",
        "gdrp"          => "",
        "jtraset"       => "",
        "jtrrp"         => "",
        "sl1aset"       => "",
        "sl1rp"         => "",
        "sl3aset"       => "",
        "sl3rp"         => "",
        "keypointaset"  => "",
        "keypointrp"    => "",
        "inputdt"       => "",
        "app_pel"       => "",
        "app_ang"       => "",
        "app_keu"       => "",
        "download"      => "",
        "action1"       => "",
        "action2"       => ""
    );

    $empty_pos = array(
        "pos1"          => "",
        "namapos"       => "",
        "nilai1"        => "",
        "sisadisburse"  => ""
    );

    $empty_skki = array(
        "no_urut"           => "",
        "nowbs"             => "",
        "nomornota"         => "",
        "noskk"             => "",
        "uraians"           => "",
        "tanggalskki"       => "",
        "anggaran"          => "",
        "disburse"          => "",
        "wbs"               => "",
        "namaunit"          => "",
        "jtm"               => "",
        "nilaianggaranjtm"  => "",
        "gd"                => "",
        "nilaianggarangd"   => "",
        "jtr"               => "",
        "nilaianggaranjtr"  => "",
        "sl1"               => "",
        "nilaianggaransl1"  => "",
        "sl3"               => "",
        "nilaianggaransl3"  => "",
        "keypoint"          => "",
        "nilaianggarankp"   => ""
    );

    $total_record = mysql_num_rows($allresult);

    while ($row = mysqli_fetch_array($result)) {
        
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
        
        if($dummy != $row["noskk"]) {
            
            if($urutanrow > 0){
                
                /* push data sebelum masuk SKKI baru */
                $data_kontrak = array(
                    "no_rab"        => "",
                    "nilai_rp"      => (empty($srab)? "" : number_format($srab)),
                    "nomorkontrak"  => "",
                    "nodokumen"     => "",
                    "vendor"        => "",
                    "uraiank"       => "",
                    "tglawal"       => "",
                    "tglakhir"      => "",
                    "kontrak"       => number_format($snk),
                    "bayar"         => number_format($snb),
                    "sisabayar"     => number_format($snk - $snb),
                    "jtmaset"       => number_format($sjtmaset),
                    "jtmrp"         => number_format($sjtmrp),
                    "gdaset"        => number_format($sgdaset),
                    "gdrp"          => number_format($sgdrp),
                    "jtraset"       => number_format($sjtraset),
                    "jtrrp"         => number_format($sjtrrp),
                    "sl1aset"       => number_format($ssl1aset),
                    "sl1rp"         => number_format($ssl1rp),
                    "sl3aset"       => number_format($ssl3aset),
                    "sl3rp"         => number_format($ssl3rp),
                    "keypointaset"  => number_format($skeypointaset),
                    "keypointrp"    => number_format($skeypointrp),
                    "inputdt"       => "",
                    "app_pel"       => "",
                    "app_ang"       => "",
                    "app_keu"       => "",
                    "download"      => "",
                    "action1"       => "",
                    "action2"       => ""
                );

                $data_pos = array(
                    "pos1"          => "",
                    "namapos"       => "",
                    "nilai1"        => number_format($npost),
                    "sisadisburse"  => number_format($disb-$snk)
                );

                $data_master[$parent_skki]["pos"] = $data_pos;
                $data_master[$parent_skki]["kontrak"] = $data_kontrak;
            }

            /* simpan nomor index untuk SKKI */
            $parent_skki = $urutanrow;

            $no++;
            $npost = $row["nilai1"];
            $nu = $row["namaunit"];
            $disb = $row["disburse"];

            /* input data SKKI */
            $data_skki = array(
                "no_urut"           => $no,
                "nowbs"             => $row["nomorwbs"] . ($row["nomorwbs"]!="" && $row["nomorprk"]!=""? " <br /> ": "") . $row["nomorprk"] . ($row["nomorscore"]==""? "": "<br /> $row[nomorscore]"),
                "nomornota"         => $row["nomornota"],
                "noskk"             => $row["noskk"],
                "uraians"           => $row["uraians"],
                "tanggalskki"       => $row["tanggalskki"],
                "anggaran"          => number_format($row["anggaran"]),
                "disburse"          => number_format($row["disburse"]),
                "wbs"               => $row["wbs"],
                "namaunit"          => $nu,
                "jtm"               => number_format($row["jtm"]),
                "nilaianggaranjtm"  => number_format($row["nilaianggaranjtm"]),
                "gd"                => number_format($row["gd"]),
                "nilaianggarangd"   => number_format($row["nilaianggarangd"]),
                "jtr"               => number_format($row["jtr"]),
                "nilaianggaranjtr"  => number_format($row["nilaianggaranjtr"]),
                "sl1"               => number_format($row["sl1"]),
                "nilaianggaransl1"  => number_format($row["nilaianggaransl1"]),
                "sl3"               => number_format($row["sl3"]),
                "nilaianggaransl3"  => number_format($row["nilaianggaransl3"]),
                "keypoint"          => number_format($row["keypoint"]),
                "nilaianggarankp"   => number_format($row["nilaianggarankp"])
            );
            
            /* push data SKKI */
            $data_master[$parent_skki] = array(
                "skki"      => $data_skki,
                "pos"       => $empty_pos,
                "kontrak"   => $empty_kontrak
            );

            $urutanrow++;

            /* reset data */
            $snk = 0;
            $snb = 0;
            $srab = 0;
            $sjtmaset = 0;
            $sjtmrp = 0;
            $sgdaset = 0;
            $sgdrp = 0;
            $sjtraset = 0;
            $sjtrrp = 0;
            $ssl1aset = 0;
            $ssl1rp = 0;
            $ssl3aset = 0;
            $ssl3rp = 0;
            $skeypointaset = 0;
            $skeypointrp = 0;
            
            $angt += $row["anggaran"];
            $disbt += $row["disburse"];
            $wbst += $row["wbs"];

            $ang_jtm_assett += $row["jtm"];
            $ang_gd_assett += $row["gd"];
            $ang_jtr_assett += $row["jtr"];
            $ang_sl1_assett += $row["sl1"];
            $ang_sl3_assett += $row["sl3"];
            $ang_kp_assett += $row["keypoint"];

            $ang_jtm_rpt += $row["nilaianggaranjtm"];
            $ang_gd_rpt += $row["nilaianggarangd"];
            $ang_jtr_rpt += $row["nilaianggaranjtr"];
            $ang_sl1_rpt += $row["nilaianggaransl1"];
            $ang_sl3_rpt += $row["nilaianggaransl3"];
            $ang_kp_rpt += $row["nilaianggarankp"];
        }

        if($dummy != $row["noskk"] || $dummypos != $row["pos1"]) {

            if($parent_pos > 0){
                
                /* push data sebelum masuk pos per SKKI baru */
                $data_kontrak = array(
                    "no_rab"        => "",
                    "nilai_rp"      => (empty($prab)? "" : number_format($prab)),
                    "nomorkontrak"  => "",
                    "nodokumen"     => "",
                    "vendor"        => "",
                    "uraiank"       => "",
                    "tglawal"       => "",
                    "tglakhir"      => "",
                    "kontrak"       => number_format($pnk),
                    "bayar"         => number_format($pnb),
                    "sisabayar"     => number_format($pnk - $pnb),
                    "jtmaset"       => number_format($pjtmaset),
                    "jtmrp"         => number_format($pjtmrp),
                    "gdaset"        => number_format($pgdaset),
                    "gdrp"          => number_format($pgdrp),
                    "jtraset"       => number_format($pjtraset),
                    "jtrrp"         => number_format($pjtrrp),
                    "sl1aset"       => number_format($psl1aset),
                    "sl1rp"         => number_format($psl1rp),
                    "sl3aset"       => number_format($psl3aset),
                    "sl3rp"         => number_format($psl3rp),
                    "keypointaset"  => number_format($pkeypointaset),
                    "keypointrp"    => number_format($pkeypointrp),
                    "inputdt"       => "",
                    "app_pel"       => "",
                    "app_ang"       => "",
                    "app_keu"       => "",
                    "download"      => "",
                    "action1"       => "",
                    "action2"       => "",
                );

                $data_master[$parent_pos]["kontrak"] = $data_kontrak;
            }

            /* simpan nomor index untuk pos per SKKI */
            $parent_pos = $urutanrow;

            /* input data pos per SKKI */
            $data_pos = array(
                "pos1"          => $row["pos1"],
                "namapos"       => $row["namapos"],
                "nilai1"        => number_format($row["nilai1"]),
                "sisadisburse"  => number_format(0)
            );

            /* push data SKKI */
            $data_master[$parent_pos] = array(
                "skki"      => $empty_skki,
                "pos"       => $data_pos,
                "kontrak"   => $empty_kontrak
            );

            $urutanrow++;
            
            $pnk = 0;
            $pnb = 0;
            $prab = 0;
            $pjtmaset = 0;
            $pjtmrp = 0;
            $pgdaset = 0;
            $pgdrp = 0;
            $pjtraset = 0;
            $pjtrrp = 0;
            $psl1aset = 0;
            $psl1rp = 0;
            $psl3aset = 0;
            $psl3rp = 0;
            $pkeypointaset = 0;
            $pkeypointrp = 0;

            $npos = $row["nilai1"];
            //if($dummy!=$row["noskk"])
            $npost = ($dummy==$row["noskk"]? $npost+$row["nilai1"]: $npost);
            $post += $row["nilai1"];
        }
        
        $dummypos = $row["pos1"];
        $dummy = $row["noskk"];
        
        $snk += $row["kontrak"];
        $snb += $row["bayar"];
        $srab += $row["nilai_rp"];
        $sjtmaset += $row["jtmaset"];
        $sjtmrp += $row["jtmrp"];
        $sgdaset += $row["gdaset"];
        $sgdrp += $row["gdrp"];
        $sjtraset += $row["jtraset"];
        $sjtrrp += $row["jtrrp"];
        $ssl1aset += $row["sl1aset"];
        $ssl1rp += $row["sl1rp"];
        $ssl3aset += $row["sl3aset"];
        $ssl3rp += $row["sl3rp"];
        $skeypointaset += $row["keypointaset"];
        $skeypointrp += $row["keypointrp"];
        
        $pnk += $row["kontrak"];
        $pnb += $row["bayar"];
        $prab += $row["nilai_rp"];
        $pjtmaset += $row["jtmaset"];
        $pjtmrp += $row["jtmrp"];
        $pgdaset += $row["gdaset"];
        $pgdrp += $row["gdrp"];
        $pjtraset += $row["jtraset"];
        $pjtrrp += $row["jtrrp"];
        $psl1aset += $row["sl1aset"];
        $psl1rp += $row["sl1rp"];
        $psl3aset += $row["sl3aset"];
        $psl3rp += $row["sl3rp"];
        $pkeypointaset += $row["keypointaset"];
        $pkeypointrp += $row["keypointrp"];
        
        $kont += $row["kontrak"];
        $bayt += $row["bayar"];
        
        $rea_jtm_assett += $row["jtmaset"];
        $rea_gd_assett += $row["gdaset"];
        $rea_jtr_assett += $row["jtraset"];
        $rea_sl1_assett += $row["sl1aset"];
        $rea_sl3_assett += $row["sl3aset"];
        $rea_kp_assett += $row["keypointaset"];

        $rea_jtm_rpt += $row["jtmrp"];
        $rea_gd_rpt += $row["gdrp"];
        $rea_jtr_rpt += $row["jtrrp"];
        $rea_sl1_rpt += $row["sl1rp"];
        $rea_sl3_rpt += $row["sl3rp"];
        $rea_kp_rpt += $row["keypointrp"];

        $download = ($row["file_path"]==""? "": "<br><a href='../$row[file_path]' target='_blank'>Download Kontrak</a>"). ($row["pdp_file_path"]==""? "": "<br><a href='../$row[pdp_file_path]' target='_blank'>Download Realisasi Fisik</a>");
        
        $action1 = ($row["kontrakapproved"]== 1? $statusbayar : 
                        (
                            $_SESSION["org"]=="" || $_SESSION["nip"]=="KEU" ? 
                                ""
                            : "<div id='b$row[kid]'><button id='bayarButton' onclick='bayar(\"$row[nomorkontrak]\", 0, \"$row[kid]\")'>BAYAR</button></div>"
                        )
                    );

        $action2 = ($row["nomorkontrak"]==""? "": 
                        (
                            $_SESSION["roleid"] <= 3 ? 
                                ($row["signed"]==""? 
                                    "<div id='k$row[kid]'><button id='signButton' onclick='signed(\"$row[nomorkontrak]\", 1, \"$row[kid]\")'><img src='no.png' width='24' height='24' alt='Signed' title='Signed'></img></button></div>": 
                                    "<div id='k$row[kid]'><button id='signButton' onclick='signed(\"$row[nomorkontrak]\", 0, \"$row[kid]\")'><img src='ok.png' width='24' height='24' alt='Unsigned' title='Unsigned'></img></button></div>"
                                )
                            : ($row["signed"]==""? "": "Signed")
                        )
                    );

        $data_kontrak = array(
            "no_rab"        => $row["no_rab"],
            "nilai_rp"      => (empty($row["no_rab"]) ? "" : number_format($row["nilai_rp"])),
            "nomorkontrak"  => $row["nomorkontrak"],
            "nodokumen"     => $row["nodokumen"],
            "vendor"        => $row["vendor"],
            "uraiank"       => $row["uraiank"],
            "tglawal"       => $row["tglawal"],
            "tglakhir"      => $row["tglakhir"],
            "kontrak"       => number_format($row["kontrak"]),
            "bayar"         => number_format($row["bayar"]),
            "sisabayar"     => number_format($row["kontrak"] - $row["bayar"]),
            "jtmaset"       => number_format($row["jtmaset"]),
            "jtmrp"         => number_format($row["jtmrp"]),
            "gdaset"        => number_format($row["gdaset"]),
            "gdrp"          => number_format($row["gdrp"]),
            "jtraset"       => number_format($row["jtraset"]),
            "jtrrp"         => number_format($row["jtrrp"]),
            "sl1aset"       => number_format($row["sl1aset"]),
            "sl1rp"         => number_format($row["sl1rp"]),
            "sl3aset"       => number_format($row["sl3aset"]),
            "sl3rp"         => number_format($row["sl3rp"]),
            "keypointaset"  => number_format($row["keypointaset"]),
            "keypointrp"    => number_format($row["keypointrp"]),
            "inputdt"       => $row["inputdt"],
            "app_pel"       => $row["app_pel"],
            "app_ang"       => $row["app_ang"],
            "app_keu"       => $row["app_keu"],
            "download"      => $download,
            "action1"       => $action1,
            "action2"       => $action2,
        );

        /* push data kontrak */
        $data_master[$urutanrow] = array(
            "skki"      => $empty_skki,
            "pos"       => $empty_pos,
            "kontrak"   => $data_kontrak
        );

        $urutanrow++;
    }
    mysqli_free_result($result);


    $data_kontrak = array(
        "no_rab"        => "",
        "nilai_rp"      => (empty($srab)? "" : number_format($srab)),
        "nomorkontrak"  => "",
        "nodokumen"     => "",
        "vendor"        => "",
        "uraiank"       => "",
        "tglawal"       => "",
        "tglakhir"      => "",
        "kontrak"       => number_format($snk),
        "bayar"         => number_format($snb),
        "sisabayar"     => number_format($snk - $snb),
        "jtmaset"       => number_format($sjtmaset),
        "jtmrp"         => number_format($sjtmrp),
        "gdaset"        => number_format($sgdaset),
        "gdrp"          => number_format($sgdrp),
        "jtraset"       => number_format($sjtraset),
        "jtrrp"         => number_format($sjtrrp),
        "sl1aset"       => number_format($ssl1aset),
        "sl1rp"         => number_format($ssl1rp),
        "sl3aset"       => number_format($ssl3aset),
        "sl3rp"         => number_format($ssl3rp),
        "keypointaset"  => number_format($skeypointaset),
        "keypointrp"    => number_format($skeypointrp),
        "inputdt"       => "",
        "app_pel"       => "",
        "app_ang"       => "",
        "app_keu"       => "",
        "download"      => "",
        "action1"       => "",
        "action2"       => ""
    );

    $data_pos = array(
        "pos1"          => "",
        "namapos"       => "",
        "nilai1"        => number_format($npost),
        "sisadisburse"  => number_format($disb-$snk)
    );

    $data_master[$parent_skki]["pos"] = $data_pos;
    $data_master[$parent_skki]["kontrak"] = $data_kontrak;

    /* push data sebelum masuk pos per SKKI baru */
    $data_kontrak = array(
        "no_rab"        => "",
        "nilai_rp"      => (empty($prab)? "" : number_format($prab)),
        "nomorkontrak"  => "",
        "nodokumen"     => "",
        "vendor"        => "",
        "uraiank"       => "",
        "tglawal"       => "",
        "tglakhir"      => "",
        "kontrak"       => number_format($pnk),
        "bayar"         => number_format($pnb),
        "sisabayar"     => number_format($pnk - $pnb),
        "jtmaset"       => number_format($pjtmaset),
        "jtmrp"         => number_format($pjtmrp),
        "gdaset"        => number_format($pgdaset),
        "gdrp"          => number_format($pgdrp),
        "jtraset"       => number_format($pjtraset),
        "jtrrp"         => number_format($pjtrrp),
        "sl1aset"       => number_format($psl1aset),
        "sl1rp"         => number_format($psl1rp),
        "sl3aset"       => number_format($psl3aset),
        "sl3rp"         => number_format($psl3rp),
        "keypointaset"  => number_format($pkeypointaset),
        "keypointrp"    => number_format($pkeypointrp),
        "inputdt"       => "",
        "app_pel"       => "",
        "app_ang"       => "",
        "app_keu"       => "",
        "download"      => "",
        "action1"       => "",
        "action2"       => "",
    );

    $data_master[$parent_pos]["kontrak"] = $data_kontrak;

    echo json_encode(array(
        "draw"              => $draw,
        "recordsTotal"      => $total_record,
        "recordsFiltered"   => $total_record,
        "data"              => $data_master,
        "parent_skki"       => $urutanrow
    ));
?>