function submitme() {
	var dummy = "";
	var dummyid = "";
	var x = document.getElementById("frm");
	
	for(var i=0; i<x.length; i++) {
		dummy = (x.elements[i].id.substr(0,3)=="job"? x.elements[i].id: dummy);
		
		if((x.elements[i].id.substr(0,1)=="n") || (x.elements[i].id.substr(0,1)=="s")) {
			dummyid = "job" + (x.elements[i].id.substr(0,1)=="n"? x.elements[i].id: x.elements[i].id.substr(1,(x.elements[i].id.length)-1));
		
			document.getElementById(dummyid).value = "";
			for(var j=0; j<document.getElementById(x.elements[i].id).length; j++) {
				document.getElementById(dummyid).value += 
					(document.getElementById(dummyid).value==""? "": "[splitme]") + document.getElementById(x.elements[i].id)[j].value;
			}
		}
	}
	return true;
}


function refresh(me) {
	for(var i=0; i<document.getElementById(me).length; i++) {
		document.getElementById(me)[i].selected = false;
	}
}


function abnormal(me) {
	document.getElementById(me).src= "../images/" + (me=="u"? "u_arrow32b.png": (me=="r"? "r_arrow32b.png": "MB_save32b.png"));
	//alert (document.getElementById(me).src);
}


function normal(me) { 
	document.getElementById(me).src= "../images/" + (me=="u"? "u_arrow32k.png": (me=="r"? "r_arrow32k.png": "MB_save32k.png"));
	//alert (document.getElementById(me).src);
}


function assign(me) {
	if(me=="r") {
		var option = document.createElement("option");
		if((document.getElementById("nd").value!="") && (document.getElementById("usr").value!="")) {
			var usr = document.getElementById("s" + document.getElementById("usr").value);
			var idx = document.getElementById("nd").selectedIndex;

			option.text = document.getElementById("nd")[idx].text;
			option.value = document.getElementById("nd").value;
			
			try { // for IE earlier than version 8
				usr.add(option, usr.options[null]);
			}
			catch (e) {
				usr.add(option, null);
			}
			document.getElementById("nd").remove(document.getElementById("nd").selectedIndex);
		}
	} else {
	
		var x = document.getElementById("frm");
		
		for(var i=0; i<x.length; i++) {
			if(x.elements[i].id.substr(0,1)=="s") {
				for(var j=0; j<document.getElementById(x.elements[i].id).length; j++) {
					if(document.getElementById(x.elements[i].id)[j].selected) {
						var nd = document.getElementById("nd");								
						var option = document.createElement("option");
						option.text = document.getElementById(x.elements[i].id)[j].text;
						option.value = document.getElementById(x.elements[i].id)[j].value;
						
						try { // for IE earlier than version 8
							nd.add(option, nd.options[null]);
						}
						catch (e) {
							nd.add(option, null);
						}
						document.getElementById(x.elements[i].id).remove(document.getElementById(x.elements[i].id)[j]);
					}
				}
			}
		}			
	}
}

/*
function proses(me) {
	window.open("../disposisibawahan/admproses.php?n="+me, "_self");
}


function showpagu() {
	var url = "showpagu.php?prd=" + document.getElementById("tprd").value;
	ajaxpage(url, "showPagu");
}
*/

function tambahpagu(prd) {
//	var url = "tambahpagu.php?prd=" + document.getElementById("tprd").value;
	var url = "tambahpagu.php?prd=" + prd;
	window.open(url,"_self");
}


function detailpagu(pos, prd) {
	var det = 0;
	for(var i=0; i<pos.length; i++) {
		det += (pos.substr(i,1)=="."? 1: 0);
	}
	det++;
//	var url = "subpos" + det + ".php?prd=" + document.getElementById("tprd").value + "&pos=" + pos;
	var url = "subpos.php?prd=" + prd + "&pos=" + pos + "&ke=" + det;
	window.open(url,"_self");
}


function nilaisubpagu(me, tot) {
	var x = document.getElementById("frm");
	var dummy = 0;
	for(var i=0; i<x.length; i++) {
		if(x.elements[i].id.substr(0,1)=="t") {
			var regex = new RegExp("\\,","g");
			res = parseInt(x.elements[i].value.replace(regex,""));
			dummy += (isNaN(res)? 0: res);

			if(dummy > tot) {
				alert('Nilai Melewati Batas Pagu!!!');
				me.value = 0;
				dummy -= (isNaN(res)? 0: res);
				me.focus();
				break;
			} 
		}
	}
	
	document.getElementById("sudah").innerHTML="Pagu yang sudah dirinci = Rp." + dummy;
	document.getElementById("belum").innerHTML="Pagu yang belum dirinci = Rp." + (tot-dummy);
}


function kembali(prd, pos, sub) {
	var url = "pagupos.php?prd=" + prd;
	sub = sub-1;
	if(sub>0) { url = url + ""; }
	window.open(url,"_self");
}


function hapuspagu(pos, prd) {
//	alert(pos);

	if(confirm("Hapus SELURUH Data Pagu dan Sub Pagu " + pos + "?")) {
//		var url = "hapuspagu.php?prd=" + document.getElementById("tprd").value + "&pos=" + pos;
		var url = "hapuspagu.php?prd=" + prd + "&pos=" + pos;
		window.open(url,"_self");
	}
}


function formatme(me) {
	var i;
	var j;
	var dummy = "";
	
	
	if(isNaN(me.value)) {
		me.value = 0;
	} else {
		var regex = new RegExp("\\,","g");
		me.value = me.value.replace(regex,"");

		j = 0;
		for(i=me.value.length-1; i>=0; i--) { 
			j++;
			if(me.value.substr(i,1)==".") { j = 0; }
			
			if((j>1) && (j%3==1)) {dummy = ","  + dummy};
			dummy = me.value.substr(i,1) + dummy;
		}
		me.value = dummy;
	}
}


function addme() {
	var x = document.getElementById("frm");
	var dummy = 0;
	for(var i=0; i<x.length; i++) {
		if(x.elements[i].id.substr(0,1)=="t") {
			var regex = new RegExp("\\,","g");
			res = parseInt(x.elements[i].value.replace(regex,""));
			dummy += (isNaN(res)? 0: res);
		}
	}
	document.getElementById("jumlah").value = dummy;
}

function validateContract(skk) {
	var next = true;
	var x = document.getElementById("myForm");
	for (var i=0; i<x.length; i++) {
		if((x.elements[i].id.substr(0,1)=="k") && (x.elements[i].value==null || x.elements[i].value=="")) {
			
			document.getElementById(x.elements[i].id).focus();
			alert("Kontrak Pada No." + (parseInt(i/6)+(i%6)) + " Harus Diisi Terlebih Dahulu!");
			return false;
		}
	}
}

function adduser(str) {
	if (str=="") {
		document.getElementById("txtHint").innerHTML="";
		return;
	}

	if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp=new XMLHttpRequest();
	} else {// code for IE6, IE5
		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
	
	xmlhttp.onreadystatechange=function() {
		if (xmlhttp.readyState==4 && xmlhttp.status==200) {
			document.getElementById("txtHint").innerHTML=xmlhttp.responseText;
		}
	}
	xmlhttp.open("GET","getpelaksana.php",true);
	xmlhttp.send();
}

function ndvalidate() {
	var hasil = true;
	var  x = "";
	var dummy = "";
	var counter = "";

	frm = document.getElementById("tambahrekomendasi");
	lfrm = frm.length;
	for (i=0; i<lfrm; i++) {
		x += frm.elements[i].id + "\n";
		dummy = frm.elements[i].id.substr(0,3);
		
		if(dummy=="pic"||dummy=="pos"||(dummy=="nil"&&dummy!="nilairekom")) {
			document.getElementById(frm.elements[i].id).style.borderColor = "initial";
			if(document.getElementById(frm.elements[i].id).value.trim()=="") {
				document.getElementById(frm.elements[i].id).style.borderColor = "red";
				hasil = false;
			}
		}
/*		
		if(dummy=="pos") {
			document.getElementById(frm.elements[i].id).style.borderColor = "initial";
			if(document.getElementById(frm.elements[i].id).value.trim()=="") {
				document.getElementById(frm.elements[i].id).style.borderColor = "red";
				hasil = false;
			}
		}
/*		
		if(dummy=="pos") {
			counter = frm.elements[i].id.substr(3,frm.elements[i].id.length-3);

			document.getElementById("nilai"+counter).style.borderColor = "initial";
			document.getElementById("pos"+counter).style.borderColor = "initial";
			if(document.getElementById("pos"+counter).value.trim()!=="") {
				if(document.getElementById("nilai"+counter).value.trim()=="") {
					document.getElementById("nilai"+counter).style.borderColor = "red";
					hasil = false;
				} 
			}
			
			if(document.getElementById("nilai"+counter).value.trim()!=="") {
				if(document.getElementById("pos"+counter).value.trim()=="") {
					document.getElementById("pos"+counter).style.borderColor = "red";
					hasil = false;
				} 
			}
		}
*/		
	}
	return hasil;
}

function nilai_usulan(me) {
//	alert(me + " " + document.getElementById("pos"+me).value + " " + document.getElementById("nilai"+me).value + " " + document.getElementById("sisa"+me).value);

	var ni = parseFloat(document.getElementById("nilai"+me).value);
	ni = (isNaN(ni)? 0: ni);
	var si = parseFloat(document.getElementById("sisa"+me).value);
	si = (isNaN(si)? 0: si);
	
	if(ni > si) {
		document.getElementById("nilai"+me).value = "";
		document.getElementById("nilai"+me).style.borderColor = "red";
	}
	
	var frm = document.getElementById("tambahrekomendasi");
	var lfrm = frm.length;
	var dummy = 0;
	document.getElementById("nilairekom").value = dummy;
	for (i=0; i<lfrm; i++) {
		if(frm.elements[i].id.substr(0,5)=="nilai" && frm.elements[i].id!="nilairekom") {
			dummy += (isNaN(parseFloat(frm.elements[i].value))? 0: parseFloat(frm.elements[i].value));
		}
	}
	document.getElementById("nilairekom").value = dummy;
}


function myvalue(me) {
	var mine = document.getElementById("pos"+me).value;
	var th = document.getElementById("tgl_nota").value.substr(0,4);
//	alert(th);
	
	if (me=="") {
		document.getElementById("sisa"+me).innerHTML="";
		return;
	}
	
	if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp=new XMLHttpRequest();
	} else {// code for IE6, IE5
		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
	
	xmlhttp.onreadystatechange=function() {
		if (xmlhttp.readyState==4 && xmlhttp.status==200) {
			document.getElementById("sisa"+me).value=xmlhttp.responseText;
			nilai_usulan(me);
		}
	}
	xmlhttp.open("GET","myvalue.php?q="+mine+"&th="+th,true);
	xmlhttp.send();
}