var loadedobjects=""
var rootdomain="http://"+window.location.hostname

function ajaxpage(url, containerid, addme){
	//alert(url + " " + containerid + " " + addme);
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
		loadpage(page_request, containerid, addme)
	}

	page_request.open('GET', url, true)
	page_request.send(null)
}


function loadpage(page_request, containerid, addme){
	if (page_request.readyState == 4 && (page_request.status==200 || window.location.href.indexOf("http")==-1)) {
//		if(containerid=="mydiv") {
			// get original values of each elements in mydiv
			var frm = document.getElementById("tambahrekomendasi");
			var lfrm = frm.length;
			var myid = new Array();
			var myval = new Array();
			var j = -1;
			
			for (i=0; i<lfrm; i++) {
				if((frm.elements[i].id.substr(0,3)=="pic")||(frm.elements[i].id.substr(0,3)=="pos")) {
					j++;
					myid[j] = frm.elements[i].id;
					myval[j] = document.getElementById(frm.elements[i].id).selectedIndex;
				}
				if((frm.elements[i].id.substr(0,5)=="nilai" && frm.elements[i].id!=="nilairekom")||(frm.elements[i].id.substr(0,4)=="sisa")) {
					j++;
					myid[j] = frm.elements[i].id;
					myval[j] = document.getElementById(frm.elements[i].id).value;
				}
			}
			// end get
//		}

		document.getElementById(containerid).innerHTML= (addme==0? 
			page_request.responseText: 
			document.getElementById(containerid).innerHTML+page_request.responseText
		);
		
//		if(containerid=="mydiv") {
			// return original values of each elements in mydiv
			for(i=0; i<=j; i++) {
				if(myid[i].substr(0,1)=="p") {document.getElementById(myid[i]).selectedIndex = myval[i];
				} else {document.getElementById(myid[i]).value = myval[i]}
			}
			// end return
//		}
	}
}


function loadobjs(){
	if (!document.getElementById)
		return
		
	for (i=0; i<arguments.length; i++){
		var file=arguments[i]
		var fileref=""
	
		if (loadedobjects.indexOf(file)==-1) { //Check to see if this object has not already been added to page before proceeding
			if (file.indexOf(".js")!=-1) { //If object is a js file
				fileref=document.createElement('script')
				fileref.setAttribute("type","text/javascript");
				fileref.setAttribute("src", file);
			}
			else if (file.indexOf(".css")!=-1) { //If object is a css file
				fileref=document.createElement("link")
				fileref.setAttribute("rel", "stylesheet");
				fileref.setAttribute("type", "text/css");
				fileref.setAttribute("href", file);
			}
		}
		
		if (fileref!=""){
			document.getElementsByTagName("head").item(0).appendChild(fileref)
			loadedobjects+=file+" " //Remember this object as being already added to page
		}
	}
}


function tambah() {
//	alert("tambah");
	var ldiv, frm, lfrm, i, dummy;
	
	frm = document.getElementById("tambahrekomendasi");
	lfrm = frm.length;
	ldiv = document.getElementById("mydiv").innerHTML.length;
	
	dummy= 0;
	for (i=0; i<lfrm; i++) {
		if(frm.elements[i].id.substr(0,3)=="pic") {
			dummy = parseInt(frm.elements[i].id.substr(3, frm.elements[i].id.length-3)) + 1;
		}
	}
	ajaxpage("dynamic6.php?t="+dummy, "mydiv", 1); //0 replace, 1 additional
}


function hapus(me) {
	var el = document.getElementById("dpic"+me);
	el.parentNode.removeChild(el);			
}


function tambahpos(me) {
	//alert(me);
	var lanjut = false;
	var frm = document.getElementById("tambahrekomendasi");
	var lfrm = frm.length;
	var n;
	var dummy = 0;
	
	for (i=0; i<lfrm; i++) {
		n = frm.elements[i].id;
		if(n=="pic"+me||lanjut) {
			lanjut = true;
			lanjut = (n.substr(0,3)=="pic" && n.substr(3, n.length-3)!=me? false: true);
			if(!lanjut) { break; }
			
//			if(n.substr(0,3)=="pos") { dummy = n.substr(4, n.length-4); }
//			if(n.substr(0,3)=="pos") { dummy = n.substr(5, n.length-5); }
			if(n.substr(0,3)=="pos") { 
				//alert(n);
				var arr = n.split(".");
				dummy = arr[1]; 
			}
		}
	}
	dummy = parseInt(dummy) + 1;
	ajaxpage("dynamic7.php?t="+me+"&i="+dummy, "dpic"+me, 1); //0 replace, 1 additional
}


function kurangpos(me) {
	me = "dpos" + me;
	var el = document.getElementById(me);
	el.parentNode.removeChild(el);			
}