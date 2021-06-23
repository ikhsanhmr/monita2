SELECT noskk, nilai, nomorskko, nilaianggaran, nilaidisburse FROM (
	SELECT noskk, SUM(nilai1) nilai FROM notadinas_detail WHERE noskk LIKE '%SKK%O%' GROUP BY noskk
) d LEFT JOIN skkoterbit s ON d.noskk = s.nomorskko
WHERE 
CASE WHEN nilaianggaran != nilaidisburse THEN (nilai != nilaianggaran AND nilai != nilaidisburse) ELSE nilai != nilaidisburse END 

SELECT noskk, nilai, nomorskki, nilaianggaran, nilaidisburse FROM (
	SELECT noskk, SUM(nilai1) nilai FROM notadinas_detail WHERE noskk LIKE '%SKK%I%' GROUP BY noskk
) d LEFT JOIN skkiterbit s ON d.noskk = s.nomorskki
WHERE 
CASE WHEN nilaianggaran != nilaidisburse THEN (nilai != nilaianggaran AND nilai != nilaidisburse) ELSE nilai != nilaidisburse END 
