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
//	alert(me);
	if(me=="r") {
		var option = document.createElement("option");
		if((document.getElementById("nd").value!="") && (document.getElementById("usr").value!="")) {
			var usr = document.getElementById("s" + document.getElementById("usr").value);
			var idx = document.getElementById("nd").selectedIndex;

			option.text = document.getElementById("nd")[idx].text;
			option.value = document.getElementById("nd").value;
//			alert(option.text + " " + option.value);
			
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
						
//						alert(option.text + " " + option.value);
						
						try { // for IE earlier than version 8
							nd.add(option, nd.options[null]);
						}
						catch (e) {
							nd.add(option, null);
						}
//						document.getElementById(x.elements[i].id).remove(document.getElementById(x.elements[i].id)[j]);
						document.getElementById(x.elements[i].id).remove(document.getElementById(x.elements[i].id).selectedIndex);
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
	
//	me.value = me.value.replace(",", "");
//	alert(me.value + " " + me.value.replace(",", ""));
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

function ndvalidate(me) {
	var hasil = true;
//	alert(me);
	hasil = all_limit();
//	hasil = (me==0? all_limit(): all_limited());

	var j = -1;	
	var mypos = new Array();
	var myval = new Array();

	frm = document.getElementById("tambahrekomendasi");
	lfrm = frm.length;
	for (i=0; i<lfrm; i++) {
		dummy = frm.elements[i].id.substr(0,3);
		if(dummy=="pic"||dummy=="pos"||(dummy=="nil"&&dummy!="nilairekom")) {
			document.getElementById(frm.elements[i].id).style.borderColor = 
				(document.getElementById(frm.elements[i].id).style.borderColor=="red"? "red": "initial");
				
			if(document.getElementById(frm.elements[i].id).value.trim()=="") {
				document.getElementById(frm.elements[i].id).style.borderColor = "red";
				hasil = false;
			}
		}
	}
	return hasil;
}

function nilai_usulan(me) {
	if(me!=undefined){
		var ni = parseFloat(document.getElementById("nilai"+me).value);
		ni = (isNaN(ni)? 0: ni);
		var si = parseFloat(document.getElementById("sisa"+me).value);
		si = (isNaN(si)? 0: si);
		
		if(ni > si) {	
			document.getElementById("nilai"+me).value = "";
			document.getElementById("nilai"+me).style.borderColor = "red";
		}
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


function nilai_terbit(me) {
	var frm = document.getElementById("tambahrekomendasi");
	var lfrm = frm.length;
	var dummy = 0;
	document.getElementById("tunai").value = dummy;
	document.getElementById("disburse").value = dummy;
	
	for (i=0; i<lfrm; i++) {
		if(frm.elements[i].id.substr(0,5)=="nilai") {
			dummy += (isNaN(parseFloat(frm.elements[i].value))? 0: parseFloat(frm.elements[i].value));
		}
	}
	document.getElementById("tunai").value = dummy;
	document.getElementById("disburse").value = dummy;
	document.getElementById("anggaran").value = parseFloat(dummy) + parseFloat(document.getElementById("nontunai").value);
}


function myvalue(me) {
	//poscheck(me);
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
			var dummy = (isNaN(parseFloat(xmlhttp.responseText))? 0: parseFloat(xmlhttp.responseText));
			document.getElementById("sisa"+me).value=dummy; // - mylimit(me);
//			alert("cek " + dummy);
			nilai_usulan(me);
		}
	}
	xmlhttp.open("GET","myvalue.php?q="+mine+"&th="+th,true);
	xmlhttp.send();
}


function mylimit(me) {
	// dynamic pagu var
	var j = -1;
	var myid = new Array();
	var mypos = new Array();
	var myval = new Array();
	var myres = new Array();
	var frm = document.getElementById("tambahrekomendasi");
	var lfrm = frm.length;
	
	for (i=0; i<lfrm; i++) {
		switch(frm.elements[i].id.substr(0,3)) {
			case "pos":
				j++;
				myid[j] = frm.elements[i].id.substr(3,frm.elements[i].id.length-3);
				mypos[j] = frm.elements[i].value;
				break;
			case "sis":
				myres[j] = frm.elements[i].value;
				break;
			case "nil":
				if(frm.elements[i].id!="nilairekom") {
					myval[j] = frm.elements[i].value;
				}
				break;
		}
	}
	
	var caripos = "";
	for(i=0; i<mypos.length; i++) {
		if(me==myid[i]) { caripos = mypos[i]; break; }
		//alert("me : " + me + " " + myid[i] + " " + mypos[i] + " " + myval[i] + " " + myres[i]);
	}
	
	var dinamis = 0;
	for(i=0; i<mypos.length; i++) {
		if(mypos[i]==caripos) { dinamis += (me==myid[i]? 0: parseFloat(myval[i])); }
		//alert("me : " + me + " " + myid[i] + " " + mypos[i] + " " + myval[i] + " " + myres[i]);
	}
	return dinamis;
}


function unitcheck(me) {
	var dummy;
	var dobel = false;
	var frm = document.getElementById("tambahrekomendasi");
	var lfrm = frm.length;
	
	for (i=0; i<lfrm; i++) {
		if(frm.elements[i].id.substr(0,3)=="pic") {
			document.getElementById(frm.elements[i].id).style.borderColor = "initial";
			if(frm.elements[i].id != me) {
				if(frm.elements[i].value == document.getElementById(me).value) {
					dobel = true;
				}
			}
		}
	}
	if(dobel) {
		document.getElementById(me).style.borderColor = "red";
		document.getElementById(me).selectedIndex = 0;
		document.getElementById(me).blur();
	}
}


function poscheck(me) {
	var arrme = me.split(".");
	
	var dummy;
	var dobel = false;
	var frm = document.getElementById("tambahrekomendasi");
	var lfrm = frm.length;
	
	for (i=0; i<lfrm; i++) {
		if(frm.elements[i].id.substr(0,3+arrme[0].length)=="pos"+arrme[0]) {
			document.getElementById(frm.elements[i].id).style.borderColor = "initial";
			if(frm.elements[i].id != "pos"+me) {
				if(frm.elements[i].value == document.getElementById("pos"+me).value) {
					dobel = true;
				}
			}
		}
	}
	if(dobel) {
		document.getElementById("pos"+me).style.borderColor = "red";
		document.getElementById("pos"+me).selectedIndex = 0;
		document.getElementById("pos"+me).blur();
	}
}


function all_limit() {
	var myid = new Array();
	var mypos = new Array();
	var myval = new Array();
	var mylim = new Array();
	var i, k;
	var j = -1;
	var lim = true;
	
	var dummy;
	var frm = document.getElementById("tambahrekomendasi");
	var lfrm = frm.length;
	
	for(i=0; i<lfrm; i++) {
		switch(frm.elements[i].id.substr(0,3)) {
			case "pos":
				j++;
				myid[j] = frm.elements[i].id.substr(3, frm.elements[i].id.length-3);
				mypos[j] = frm.elements[i].value;
				document.getElementById(frm.elements[i].id).style.borderColor = "initial";
				break;
				
			case "sis":
				mylim[j] = frm.elements[i].value;
				document.getElementById(frm.elements[i].id).style.borderColor = "initial";
				break;
				
			case "nil":
				if(frm.elements[i].id!="nilairekom") {
					myval[j] = frm.elements[i].value;
					document.getElementById(frm.elements[i].id).style.borderColor = "initial";
				}
				break;
		}
	}

/*	
	for(i=0; i<mypos.length; i++) {
		k = 0;
		dummy = mypos[i];
		
		var maxpagu = 0;
		for(j=i; j<mypos.length; j++) {
			if(dummy==mypos[j]) {
				maxpagu = (maxpagu < mylim[j]? mylim[j]: maxpagu);
				k += parseFloat(myval[j]);
				
				if(k>maxpagu) {
//					alert(k + " " + maxpagu);
					document.getElementById("nilai"+myid[j]).style.borderColor = "red";
					lim = false;
				}
			}
		}
	}
*/
	var batas;
	for(i=0; i<mypos.length; i++) {
		dummy = mypos[i];
		k = parseFloat(myval[i]);
		batas = parseFloat(mylim[i]);
		
		for(j=0; j<i; j++) {
			if(dummy==mypos[j]) {
				batas = (batas > parseFloat(mylim[j])? batas: mylim[j]);
				k += parseFloat(myval[j]);
				
//				alert(k + " " + batas);
				if(k>batas) {
					//alert(k + " " + batas);
					document.getElementById("nilai"+myid[j]).style.borderColor = "red";
					lim = false;
				}
			}
		}
	}
	return lim;
}

function all_limited() {
	var myid = new Array();
	var mypos = new Array();
	var myval = new Array();
	var mylim = new Array();
	var i, k;
	var j = -1;
	var lim = true;
	
	var dummy;
	var frm = document.getElementById("tambahrekomendasi");
	var lfrm = frm.length;
	
	for(i=0; i<lfrm; i++) {
		switch(frm.elements[i].id.substr(0,3)) {
			case "pos":
				j++;
				myid[j] = frm.elements[i].id.substr(3, frm.elements[i].id.length-3);
				mypos[j] = frm.elements[i].value;
				document.getElementById(frm.elements[i].id).style.borderColor = "initial";
				break;
				
			case "sis":
				mylim[j] = frm.elements[i].value;
				document.getElementById(frm.elements[i].id).style.borderColor = "initial";
				break;
				
			case "nil":
				if(frm.elements[i].id!="nilairekom") {
					myval[j] = frm.elements[i].value;
					document.getElementById(frm.elements[i].id).style.borderColor = "initial";
				}
				break;
		}
	}

	var batas;
	for(i=0; i<mypos.length; i++) {
		dummy = mypos[i];
		k = parseFloat(myval[i]);
		batas = parseFloat(mylim[i]);
		
		for(j=0; j<i; j++) {
			if(dummy==mypos[j]) {
				batas = (batas > parseFloat(mylim[j])? batas: mylim[j]);
				k += parseFloat(myval[j]);
				
				if(k>batas) {
					//alert(k + " " + batas);
					document.getElementById("nilai"+myid[j]).style.borderColor = "red";
					lim = false;
				}
			}
		}
	}
	return lim;
}


function notacheck(me) {
//	alert(me);// + " " + document.getElementById(me).value);
	var mine = document.getElementById("nota"+me).value;
	var parm = "myvalue.php?i="+mine+"&t="+me;
//	alert(parm + " " + me=="");
	
	if (me==undefined) {
//		alert("terhapus");
		document.getElementById("dpic"+me).innerHTML="<select><option value=''>Pilih Pelaksana</option></select>";
		return;
	}
	
	if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp=new XMLHttpRequest();
	} else {// code for IE6, IE5
		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
	
	xmlhttp.onreadystatechange=function() {
		if (xmlhttp.readyState==4 && xmlhttp.status==200) {
			//alert(xmlhttp.responseText);
			document.getElementById("dp"+me).innerHTML=xmlhttp.responseText; 
		}
	}
	xmlhttp.open("GET",parm,true);
	xmlhttp.send();
	
}

function ndcheck(me) {
	var mine = document.getElementById("pic"+me).value;
	var parm = "myvalue1.php?i="+mine+"&t="+me;
//	alert(parm);
	
	if (me==undefined) {
		document.getElementById("pos"+me).value="";
		document.getElementById("nilai"+me).value="";
//		document.getElementById("sisa"+me).value="";
		return;
	}
	
	if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp=new XMLHttpRequest();
	} else {// code for IE6, IE5
		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
	
	xmlhttp.onreadystatechange=function() {
		if (xmlhttp.readyState==4 && xmlhttp.status==200) {
			var dummy = xmlhttp.responseText.split("<data>");
			document.getElementById("pos"+me).value=dummy[0];
			document.getElementById("nilai"+me).value=dummy[1];
			
			var dumm = document.getElementById("tunai").value;
			dumm = (isNaN(dumm) || dumm==""? 0: dumm);
			document.getElementById("tunai").value = parseFloat(dumm) + parseFloat(dummy[1]);
			document.getElementById("disburse").value= document.getElementById("tunai").value;
/*			
			var x = document.getElementById("tambahrekomendasi");
			for(var i=0; i<x.length; i++) {
				if(x.elements[i].id.substr(0,3)=="nil") {
					document.getElementById("anggaran").value = (isNaN(parseFloat(document.getElementById("anggaran").value ))? 0: parseFloat(document.getElementById("anggaran").value )) +
						((isNaN(x.elements[i].value ))? 0: parseFloat(x.elements[i].value ));
				}
			}
*/
		}
	}

	xmlhttp.open("GET",parm,true);
	xmlhttp.send();
}

function anggaranval() {
	var t = document.getElementById("tunai").value;
	var n = document.getElementById("nontunai").value;
	
	document.getElementById("disburse").value = t;
	document.getElementById("anggaran").value = parseFloat(t) + parseFloat(n);
/*
	formatme(document.getElementById("tunai"));
	formatme(document.getElementById("nontunai"));
	formatme(document.getElementById("anggaran"));
	formatme(document.getElementById("disburse"));
*/
}



function dateCheck(me) {
	var mindm = 1;
	var maxd = 31;
	var maxm = 12;
	var miny = 999;
	var maxy = 9999;
	var checked = true;
	
	var dummy = document.getElementById(me).value.split("/");
	for(i=0; i<dummy.length; i++) {
		dummy[i] = parseInt(dummy[i]);
		if(isNaN(dummy[i])) { checked = false;}
	}

	if(dummy.length != 3) { document.getElementById(me).value = ""; } 	
	else {
		// check year
		if(dummy[2]<miny || dummy[2]>maxy) {checked = false;}
		if(dummy[0]<mindm || dummy[0]>maxm) {
			checked = false;
		} else {
			if(dummy[0]<10) {dummy[0] = "0" + dummy[0]};
		}
		
		if(dummy[1]<mindm || dummy[1]>maxd) {
			checked = false;
		} else {
			if(dummy[1] < 10) {
				dummy[1] = "0" + dummy[1];
			} else {
				switch(parseInt(dummy[0])) {
					case 2:
						if(dummy[1] > 29) {checked = false;}
						else {
							if((dummy[1]>28) && (dummy[2]%4 != 0)) {checked = false;}
						}
						break;
															
					case 4:
					case 6:
					case 9:
					case 11:
						if(dummy[1]>30) {checked = false;}
						break;
				}
			}
		}
		document.getElementById(me).value = (checked? dummy[0] + "/" + dummy[1] + "/" + dummy[2]: "");
	}
}

function tambahKontrak() {
	var x = document.getElementById("fkontrak");
	var num  = 0;
	
	for(var i=0; i<x.length; i++) {
		if(x.elements[i].id.substr(0,8)=="nkontrak") {
			num = x.elements[i].id.substr(8, (x.elements[i].id.length)-8);
		}
	}
	num = (isNaN(parseInt(num))? 0: parseInt(num)+1);
	var url = "dynamic.php?num=" + num;
	ajaxpage1(url, "kontrak", false);
}

function hapuskontrak(me) {
	var el = document.getElementById(me);
	el.parentNode.removeChild(el);
}

function totalkontrak() {
	var x = document.getElementById("fkontrak");
	var num  = 0;
	var dummy = 0;
	
	for(var i=0; i<x.length; i++) {
		if(x.elements[i].id.substr(0,5)=="nilai" && x.elements[i].id.length > 5) {
			num = parseInt(x.elements[i].value);
			dummy = parseInt(dummy) + (isNaN(parseInt(num))? 0 : parseInt(num));
		}
	}
	document.getElementById("nilai").value = dummy;
}

var loadedobjects=""
var rootdomain="http://"+window.location.hostname

function ajaxpage1(url, containerid, isreplace){
	var page_request = false
	if (window.XMLHttpRequest) // if Mozilla, Safari etc
		page_request = new XMLHttpRequest()
	else if (window.ActiveXObject){ // if IE
			try {
				page_request = new ActiveXObject("Msxml2.XMLHTTP")
			} 
			
		catch (e){
			try{
				page_request = new ActiveXObject("Microsoft.XMLHTTP")
			}
			catch (e){}
		}
	}
	else
		return false
		
	page_request.onreadystatechange=function(){
		loadpage1(page_request, containerid, isreplace)
	}

	page_request.open('GET', url, true)
	page_request.send(null)
}


function loadpage1(page_request, containerid, isreplace){
	if (page_request.readyState == 4 && (page_request.status==200 || window.location.href.indexOf("http")==-1))
		if(isreplace) document.getElementById(containerid).innerHTML=page_request.responseText
		else document.getElementById(containerid).innerHTML+=page_request.responseText
}
