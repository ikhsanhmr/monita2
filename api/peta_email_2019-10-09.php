<?php
    
    require_once __DIR__ . "/../lib/Mail/Mail.php";
    require_once __DIR__ . "/../lib/Mail/Mail/mime.php";
    require_once __DIR__ . "/../config/koneksi.php";
    require_once __DIR__ . "/email_attch.php";
    
    $sql = "SELECT * FROM bidang WHERE id != 3 ORDER BY LPAD(id, 2, '0')";
	$query_result = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));
	
	$p = "";
    $nick = "";
    
	while ($row = mysqli_fetch_array($query_result)) {

        $arr_fileAtt = array();

        $p = $row["namaunit"];
        $p0 = $row["id"];
        $nick = $row["nick"];

        $arr_fileAtt[0] = generateLapPeta($p, $p0, $nick);
        $arr_fileAtt[1] = generateLapAOPerJenisKontrak($p, $p0, $nick);
        
        $htmlcontent = '
            <p>
                <span style="font-family: verdana, geneva;">Dengan Hormat,</span>
            </p>
            <p style="text-align: justify;">
                <span style="font-family: verdana, geneva;">Disampaikan realisasi penyerapan anggaran untuk pelaksanaan SKKI dan SKKO. </span> 
                <span style="font-family: verdana, geneva;">Per Sub Pos di Unit masing-masing,&nbsp; mohon dapat di Evaluasi realisasi tekontrak dan terbayar. </span> 
                <span style="font-size: 11pt; font-family: verdana, geneva;">Jika ada data yang kurang cocok agar dikomunikasikan dengan PIC Anggaran atau PIC Keuangan.</span>
            </p>
            <p style="text-align: justify;">
                <span style="font-size: 11pt; font-family: verdana, geneva;">Rincian Kontrak dapat dilihat pada :</span>
            </p>
            <ul>
                <li style="text-align: justify;"><span style="font-size: 11pt; font-family: verdana, geneva;">Laporan Penyerapan AO</span></li>
                <li style="text-align: justify;"><span style="font-size: 11pt; font-family: verdana, geneva;">Laporan Penyerapan AI</span></li>
            </ul>
            <p style="text-align: justify;">&nbsp;</p>
            <p style="text-align: center;">
                <span style="font-size: 11pt; font-family: verdana, geneva;">PIC ANGGARAN DAN KEUANGAN</span>
            </p>
            <p style="text-align: center;">
                <span style="font-size: 11pt; font-family: verdana, geneva;">APLIKASI MONITA</span>
            </p>
            <table style="border-collapse: collapse; width: 100%; height: 270px;" border="1">
                <thead>
                    <tr style="height: 18px;">
                        <th style="width: 5%; text-align: center; height: 18px; background-color: #40ff00;"><span style="color: #ffffff;">No</span></th>
                        <th style="width: 10%; text-align: center; height: 18px; background-color: #40ff00;"><span style="color: #ffffff;">Kode Unit</span></th>
                        <th style="width: 20%; text-align: center; height: 18px; background-color: #40ff00;"><span style="color: #ffffff;">Unit</span></th>
                        <th style="width: 33%; text-align: center; height: 18px; background-color: #40ff00;"><span style="color: #ffffff;">PIC Anggaran</span></th>
                        <th style="width: 32%; text-align: center; height: 18px; background-color: #40ff00;"><span style="color: #ffffff;">PIC Keuangan</span></th>
                    </tr>
                </thead>
                <tbody>
                    <tr style="height: 36px;">
                        <td style="width: 4.83816%; text-align: center; height: 36px;"><span style="font-size: 8pt;">1</span></td>
                        <td style="width: 9.94883%; text-align: center; height: 36px;"><span style="font-size: 8pt;">6201</span></td>
                        <td style="width: 20.0001%; text-align: center; height: 36px;"><span style="font-size: 8pt;">Kanwil Investasi + UP2K</span></td>
                        <td style="width: 40.7836%; text-align: center; height: 36px;"><span style="font-size: 8pt;">Santi Patra</span></td>
                        <td style="width: 25.7923%; text-align: center; height: 36px;"><span style="font-family: verdana, geneva; font-size: 8pt;">M. Amin Syafriedi, Lastri Lusyanti</span></td>
                    </tr>
                    <tr style="height: 18px;">
                        <td style="width: 4.83816%; text-align: center; height: 18px;">&nbsp;</td>
                        <td style="width: 9.94883%; text-align: center; height: 18px;"><span style="font-size: 8pt;">6201</span></td>
                        <td style="width: 20.0001%; text-align: center; height: 18px;"><span style="font-size: 8pt;">Kanwil Operasi</span></td>
                        <td style="width: 40.7836%; text-align: center; height: 18px;">
                            <ul style="list-style-type: circle;">
                                <li class="m_7929123103862784736MsoListParagraph" style="text-align: left;"><span style="font-size: 8pt; font-family: verdana, geneva;">Biaya Admi Niaga dan Umum: </span><span style="font-size: 8pt; font-family: verdana, geneva;">Sofia Komala Sari</span></li>
                                <li class="m_7929123103862784736MsoListParagraph" style="text-align: left;"><span style="font-size: 8pt; font-family: verdana, geneva;">PTL, Sewa Kit, BBM KIT, Har Instalasi dan KIT: Rita Anggraini</span></li>
                                <li class="m_7929123103862784736MsoListParagraph" style="text-align: left;"><u></u><u></u><span style="font-size: 8pt; font-family: verdana, geneva;">Har Non Instalasi: Inda Ardini<u></u><u></u></span></li>
                                <li class="m_7929123103862784736MsoListParagraph" style="text-align: left;"><span style="font-size: 8pt;"><span style="font-family: verdana, geneva;">Biaya Kepegawaian: Dardanella</span></span></li>
                            </ul>
                        </td>
                        <td style="width: 25.7923%; text-align: center; height: 18px;"><span style="font-size: 8pt;">Suwarseno, Syafrides Hendri, dan <span style="font-family: verdana, geneva;">Lastri Lusyanti</span></span></td>
                    </tr>
                    <tr style="height: 18px;">
                        <td style="width: 4.83816%; text-align: center; height: 18px;"><span style="font-size: 8pt;">2</span></td>
                        <td style="width: 9.94883%; text-align: center; height: 18px;"><span style="font-size: 8pt;">6211</span></td>
                        <td style="width: 20.0001%; text-align: center; height: 18px;"><span style="font-size: 8pt;">Pematang Siantar</span></td>
                        <td style="width: 40.7836%; text-align: center; height: 18px;"><span style="font-size: 8pt;">Santi Patra</span></td>
                        <td style="width: 25.7923%; text-align: center; height: 18px;"><span style="font-size: 8pt;">Syafrides Hendri</span></td>
                    </tr>
                    <tr style="height: 18px;">
                        <td style="width: 4.83816%; text-align: center; height: 18px;"><span style="font-size: 8pt;">3</span></td>
                        <td style="width: 9.94883%; text-align: center; height: 18px;"><span style="font-size: 8pt;">6212</span></td>
                        <td style="width: 20.0001%; text-align: center; height: 18px;"><span style="font-size: 8pt;">Sibolga</span></td>
                        <td style="width: 40.7836%; text-align: center; height: 18px;"><span style="font-size: 8pt;">Santi Patra</span></td>
                        <td style="width: 25.7923%; text-align: center; height: 18px;"><span style="font-size: 8pt;">Lidya Kristy M. Pane</span></td>
                    </tr>
                    <tr style="height: 18px;">
                        <td style="width: 4.83816%; text-align: center; height: 18px;"><span style="font-size: 8pt;">4</span></td>
                        <td style="width: 9.94883%; text-align: center; height: 18px;"><span style="font-size: 8pt;">6213</span></td>
                        <td style="width: 20.0001%; text-align: center; height: 18px;"><span style="font-size: 8pt;">Binjai</span></td>
                        <td style="width: 40.7836%; text-align: center; height: 18px;"><span style="font-size: 8pt;">Rita Anggraini</span></td>
                        <td style="width: 25.7923%; text-align: center; height: 18px;"><span style="font-size: 8pt;">Juwita CY Siburian</span></td>
                    </tr>
                    <tr style="height: 18px;">
                        <td style="width: 4.83816%; text-align: center; height: 18px;"><span style="font-size: 8pt;">5</span></td>
                        <td style="width: 9.94883%; text-align: center; height: 18px;"><span style="font-size: 8pt;">6214</span></td>
                        <td style="width: 20.0001%; text-align: center; height: 18px;"><span style="font-size: 8pt;">Medan</span></td>
                        <td style="width: 40.7836%; text-align: center; height: 18px;"><span style="font-size: 8pt;">Sofia Komala Sari</span></td>
                        <td style="width: 25.7923%; text-align: center; height: 18px;"><span style="font-size: 8pt;">Devi Fitriani</span></td>
                    </tr>
                    <tr style="height: 18px;">
                        <td style="width: 4.83816%; text-align: center; height: 18px;"><span style="font-size: 8pt;">6</span></td>
                        <td style="width: 9.94883%; text-align: center; height: 18px;"><span style="font-size: 8pt;">6215</span></td>
                        <td style="width: 20.0001%; text-align: center; height: 18px;"><span style="font-size: 8pt;">Padang Sidempuan</span></td>
                        <td style="width: 40.7836%; text-align: center; height: 18px;"><span style="font-size: 8pt;">Rita Anggraini</span></td>
                        <td style="width: 25.7923%; text-align: center; height: 18px;"><span style="font-size: 8pt;">Devi Fitriani</span></td>
                    </tr>
                    <tr style="height: 18px;">
                        <td style="width: 4.83816%; text-align: center; height: 18px;"><span style="font-size: 8pt;">7</span></td>
                        <td style="width: 9.94883%; text-align: center; height: 18px;"><span style="font-size: 8pt;">6216</span></td>
                        <td style="width: 20.0001%; text-align: center; height: 18px;"><span style="font-size: 8pt;">Rantau Prapat</span></td>
                        <td style="width: 40.7836%; text-align: center; height: 18px;"><span style="font-size: 8pt;">Rita Anggraini</span></td>
                        <td style="width: 25.7923%; text-align: center; height: 18px;"><span style="font-size: 8pt;">Yudhi Priguna</span></td>
                    </tr>
                    <tr style="height: 18px;">
                        <td style="width: 4.83816%; text-align: center; height: 18px;"><span style="font-size: 8pt;">8</span></td>
                        <td style="width: 9.94883%; text-align: center; height: 18px;"><span style="font-size: 8pt;">6217</span></td>
                        <td style="width: 20.0001%; text-align: center; height: 18px;"><span style="font-size: 8pt;">Lubuk Pakam</span></td>
                        <td style="width: 40.7836%; text-align: center; height: 18px;"><span style="font-size: 8pt;">Inda Ardini</span></td>
                        <td style="width: 25.7923%; text-align: center; height: 18px;"><span style="font-size: 8pt;">Lidya Kristy M. Pane</span></td>
                    </tr>
                    <tr style="height: 18px;">
                        <td style="width: 4.83816%; text-align: center; height: 18px;"><span style="font-size: 8pt;">9</span></td>
                        <td style="width: 9.94883%; text-align: center; height: 18px;"><span style="font-size: 8pt;">6218</span></td>
                        <td style="width: 20.0001%; text-align: center; height: 18px;"><span style="font-size: 8pt;">Nias</span></td>
                        <td style="width: 40.7836%; text-align: center; height: 18px;"><span style="font-size: 8pt;">Inda Ardini</span></td>
                        <td style="width: 25.7923%; text-align: center; height: 18px;"><span style="font-size: 8pt;">Juwita CY Siburian</span></td>
                    </tr>
                    <tr style="height: 18px;">
                        <td style="width: 4.83816%; text-align: center; height: 18px;"><span style="font-size: 8pt;">10</span></td>
                        <td style="width: 9.94883%; text-align: center; height: 18px;"><span style="font-size: 8pt;">6219</span></td>
                        <td style="width: 20.0001%; text-align: center; height: 18px;"><span style="font-size: 8pt;">Medan Utara</span></td>
                        <td style="width: 40.7836%; text-align: center; height: 18px;"><span style="font-size: 8pt;">Dardanella</span></td>
                        <td style="width: 25.7923%; text-align: center; height: 18px;"><span style="font-size: 8pt;">Devi Fitriani</span></td>
                    </tr>
                    <tr style="height: 18px;">
                        <td style="width: 4.83816%; text-align: center; height: 18px;"><span style="font-size: 8pt;">11</span></td>
                        <td style="width: 9.94883%; text-align: center; height: 18px;"><span style="font-size: 8pt;">6220</span></td>
                        <td style="width: 20.0001%; text-align: center; height: 18px;"><span style="font-size: 8pt;">Bukit Barisan</span></td>
                        <td style="width: 40.7836%; text-align: center; height: 18px;"><span style="font-size: 8pt;">Inda Ardini</span></td>
                        <td style="width: 25.7923%; text-align: center; height: 18px;"><span style="font-size: 8pt;">Juwita CY Siburian</span></td>
                    </tr>
                    <tr style="height: 18px;">
                        <td style="width: 4.83816%; text-align: center; height: 18px;"><span style="font-size: 8pt;">12</span></td>
                        <td style="width: 9.94883%; text-align: center; height: 18px;"><span style="font-size: 8pt;">6256</span></td>
                        <td style="width: 20.0001%; text-align: center; height: 18px;"><span style="font-size: 8pt;">APD</span></td>
                        <td style="width: 40.7836%; text-align: center; height: 18px;"><span style="font-size: 8pt;">Sofia Komala Sari</span></td>
                        <td style="width: 25.7923%; text-align: center; height: 18px;"><span style="font-size: 8pt;">Syafrides Hendri</span></td>
                    </tr>
                </tbody>
            </table>
            <p>
                <span style="font-size: 11pt; font-family: verdana, geneva;">Demikianlah atas&nbsp; perhatiannya di Ucapkan terimakasih</span>
            </p>
            <p>&nbsp;</p>
            <p>
                <span style="font-size: 11pt; font-family: verdana, geneva;">Salam</span>
            </p>
            <p>
                <span style="font-size: 11pt; font-family: verdana, geneva;">Fadlul Fathoni</span>
            </p>
            <p>
                <span style="font-size: 11pt; font-family: verdana, geneva;">Support Monita</span>
            </p>
            <p>
                <span style="font-size: 11pt; font-family: verdana, geneva;">No. HP: 0896-4464-1591</span>
            </p>
        ';

        $textcontent = '
            Dengan Hormat, \n

            Disampaikan realisasi penyerapan anggaran untuk pelaksanaan SKKI dan SKKO. Per Sub Pos di Unit masing-masing,  mohon dapat di Evaluasi realisasi tekontrak dan terbayar. Jika ada data yang kurang cocok agar dikomunikasikan dengan PIC Anggaran atau PIC Keuangan. \n

            1. Kanwil Investasi + UP2K \n
                \t PIC Anggaran: Santi Patra \n
                \t PIC Keuangan: M. Amin Syafriedi, Lastri Lusyanti \n\n

            2. Kanwil Operasi \n
                \t PIC Anggaran:   \n
                \t \t Biaya Admi Niaga dan Umum: Sofia Komala Sari \n
                \t \t PTL, Sewa Kit, BBM KIT, Har Instalasi dan KIT: Rita Anggraini \n
                \t \t Har Non Instalasi: Inda Ardini \n
                \t \t Biaya Kepegawaian: Dardanella \n\n

                \t PIC Keuangan: Suwarseno, Syafrides Hendri, dan Lastri Lusyanti \n\n

            3. UP3 Pematang Siantar \n
                \t PIC Anggaran: Santi Patra \n
                \t PIC Keuangan: Syafrides Hendri \n\n

            4. UP3 Sibolga \n
                \t PIC Anggaran: Santi Patra  \n
                \t PIC Keuangan: Lidya Kristy M. Pane \n\n

            5. UP3 Binjai \n
                \t PIC Anggaran: Rita Anggraini \n
                \t PIC Keuangan: Juwita CY Siburian \n\n

            6. UP3 Medan \n
                \t PIC Anggaran: Sofia Komala Sari  \n
                \t PIC Keuangan: Devi Fitriani \n\n

            7. UP3 Padang Sidempuan \n
                \t PIC Anggaran: Rita Anggraini \n
                \t PIC Keuangan: Devi Fitriani \n\n

            8. UP3 Rantau Prapat \n
                \t PIC Anggaran: Rita Anggraini  \n
                \t PIC Keuangan: Yudhi Priguna \n\n

            9. UP3 Lubuk Pakam \n
                \t PIC Anggaran: Inda Ardini \n
                \t PIC Keuangan: Lidya Kristy M. Pane \n\n

            10. UP3 Nias \n
                \t PIC Anggaran: Inda Ardini \n
                \t PIC Keuangan: Juwita CY Siburian \n\n

            11. UP3 Medan Utara \n
                \t PIC Anggaran: Dardanella  \n
                \t PIC Keuangan: Devi Fitriani \n\n

            12. UP3 Bukit Barisan \n
                \t PIC Anggaran: Inda Ardini \n
                \t PIC Keuangan: Juwita CY Siburian \n\n

            13. APD \n
                \t PIC Anggaran: Sofia Komala Sari  \n
                \t PIC Keuangan: Syafrides Hendri \n\n

            Demikianlah atas  perhatiannya di Ucapkan terimakasih \n\n
            Salam \n
            Fadlul Fathoni \n
            Support Monita \n
            No. HP: 0896-4464-1591 \n
        ';

        $from       = "Monita <helpdesk.monita@gmail.com>";
        $to         = "";
        $cc         = "Yusri <yusri3@pln.co.id>, Fadlul <fadlul.fathoni@gmail.com>";
        $subject    = "Aplikasi Monita - Informasi Peta Pagu per SKK Terbit " . date("d-m-Y");

        $sql_user = "";

        if ($p0 > 5) {

            $sql_user   = "SELECT * FROM user WHERE kodeorg = $p0";

        }else{
            
            $sql_user   = "SELECT * FROM user WHERE kodeorg = $p0 AND roleid IN (7,13,16,19)";
        }
        $rslt_user  = mysqli_query($sql_user);

        while ($row_user = mysqli_fetch_array($rslt_user)) {

			
            if (!empty($row_user['email'])){

                if (empty($to)){

					$to .= str_replace('.', ' ', str_replace(',', ' ', $row_user['nama'])) . " <" . $row_user['email'] . ">";

				}else{

					$to .= ", " . str_replace('.', ' ', str_replace(',', ' ', $row_user['nama'])) . " <" . $row_user['email'] . ">";
				}
            }
        }
		
		mysqli_free_result($rslt_user);
		
		$sql_user = "";
		
		if ($p0 < 6){
			
			$sql_user   = "select * from user where roleid IN (02,03) or nip IN ('7194002A','8409029A','8509035A','8106282Z')";
        
		}else{
			
			$sql_user   = "select * from akses_bidang a inner join user b on a.nip = b.nip where a.akses = $p0 and roleid IN (02,03,04,05)";
		}
			
		
		$rslt_user  = mysqli_query($sql_user);

        while ($row_user = mysqli_fetch_array($rslt_user)) {

			
            if (!empty($row_user['email'])){

                if (empty($to)){

					$to .= str_replace('.', ' ', str_replace(',', ' ', $row_user['nama'])) . " <" . $row_user['email'] . ">";

				}else{

					$to .= ", " . str_replace('.', ' ', str_replace(',', ' ', $row_user['nama'])) . " <" . $row_user['email'] . ">";
				}
				
				$cc .= ", " . str_replace('.', ' ', str_replace(',', ' ', $row_user['nama'])) . " <" . $row_user['email'] . ">";
            }
        }
		
		if (empty($to)){

			$to .= "Haris Jhon Horas <haris.john@pln.co.id>, Ricky Panggabean <ricky.pangabean@pln.co.id>, Yusri <yusri3@pln.co.id>, Fadlul <fadlul.fathoni@gmail.com>";

		}else{

			$to .= ", Haris Jhon Horas <haris.john@pln.co.id>, Ricky Panggabean <ricky.pangabean@pln.co.id>, Yusri <yusri3@pln.co.id>, Fadlul <fadlul.fathoni@gmail.com>";
		}

        

        $headers = array(
            'From'      => $from,
            'To'        => $to,
            'Cc'        => $cc,
            'Subject'   => $subject
        );
        
        $smtp = Mail::factory('smtp', array(
                'host' => 'ssl://smtp.gmail.com',
                'port' => '465',
                'auth' => true,
                'username' => 'helpdesk.monita@gmail.com',
                'password' => 'P@55w0rdM0nit4'
            ));

        $mime = new Mail_mime();

        $mime->setTXTBody($textcontent);
        $mime->setHTMLBody($htmlcontent);

        foreach($arr_fileAtt as $value){

            $mime->addAttachment($value);
        }
        
        $body = $mime->get();
        $hdrs = $mime->headers($headers);

        $mail = $smtp->send($to, $hdrs, $body);

        if (PEAR::isError($mail)) {
            echo('<p>' . $mail->getMessage() . '</p> <br /> ');
            echo('<p>' . $to . '</p> <br /> ');
        } else {
            echo('<p>Message successfully sent!</p>');
        }
	}
	mysqli_free_result($query_result);
	
	$mysqli->close();($kon);

?>