SELECT 
	noskk, pos1, pelaksana, nomorskko, nilaianggaran, nilaidisburse, kontrak, bayar 
FROM notadinas_detail d
LEFT JOIN skkoterbit s ON d.noskk = s.nomorskko
LEFT JOIN (
	SELECT nomorskkoi nomorskk, SUM(nilaikontrak) kontrak, SUM(COALESCE(bayar,0)) bayar FROM kontrak k 
	LEFT JOIN (SELECT nokontrak, SUM(COALESCE(nilaibayar,0)) bayar FROM realisasibayar GROUP BY nokontrak) r ON k.nomorkontrak = r.nokontrak 
	GROUP BY nomorskkoi 
) kr ON s.nomorskko = kr.nomorskk
WHERE d.progress >= 7 AND NOT nomorskko IS NULL 
ORDER BY LPAD(pelaksana,2,'0'), pos1, nomorskko