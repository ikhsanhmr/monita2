/*  fungsi untuk menghilangkan format */
  function clearnumberformat(angka)
	{
	  var nilai="";
	  nilai=replaceAll(angka,",","");
	  return nilai.replace(/^\s\s*/, '').replace(/\s\s*$/, ''); 
	}

	
	function replaceAll( str, from, to ) 
  {
    var idx = str.indexOf( from );

    while ( idx > -1 ) {
        str = str.replace( from, to );
        idx = str.indexOf( from );
    }

    return str;
  }
  

/*  fungsi untuk format angka */	
	function setnumberformat(angka)
  {
		var rupiah  = "";
		var panjang = angka.length;
		angka = angka + '';
		
		while (panjang > 3)
		{
			rupiah = "," + angka.substring(panjang-3) + rupiah;
			panjang = panjang-3;
			angka = angka.substring(0,panjang);			
		}
	
		rupiah1 = angka+rupiah;
		
		return rupiah1;
  }
           