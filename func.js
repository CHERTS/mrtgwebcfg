function change(id,lnk) {
	obj=document.getElementById(id);
	obj.href=lnk;
}

function ChangeHost() {
	var frm;

	frm = document.add_form;

	if ( frm.place_rid.value == -1 ) {
		frm.place_your.style.display = "block";
		return 1;
	}
	frm.place_your.style.display = "none";
}

function ChangeIPHost() {

		var obj = "", frm;

		frm = document.snmp_param;

		if ( frm.hid.value == -1 ) {
			obj = document.getElementById("-1");
			obj.style.display = ( obj.style.display != "none" ) ? "none" : "";
			obj = document.getElementById("-2");
			obj.style.display = ( obj.style.display != "none" ) ? "none" : "";
			return 1;
		} else {
			obj = document.getElementById("-1");
			obj.style.display = "none";
			obj = document.getElementById("-2");
			obj.style.display = "none";
			obj = document.getElementById("-3");
			obj.style.display = "none";
			obj = document.getElementById("-4");
			obj.style.display = "none";
			obj = document.getElementById("-5");
			obj.style.display = "none";
		}
}

function ChangeSNMP() {

		var obj = "", frm;

		frm = document.snmp_param;

		if ( frm.ver_snmp.value == -3 ) {
			obj = document.getElementById("-3");
			obj.style.display = "";
			obj = document.getElementById("-4");
			obj.style.display = "none";
			return 1;
		} else if ( frm.ver_snmp.value == -4 ) {
			obj = document.getElementById("-4");
			obj.style.display = "";
			obj = document.getElementById("-3");
			obj.style.display = "none";
			return 1;
		} else if ( frm.ver_snmp.value == -5 ) {
			obj = document.getElementById("-3");
			obj.style.display = "";
			obj = document.getElementById("-4");
			obj.style.display = "";
			obj = document.getElementById("-5");
			obj.style.display = "";
			return 1;
		} else {
			obj = document.getElementById("-3");
			obj.style.display = "none";
			obj = document.getElementById("-4");
			obj.style.display = "none";
			obj = document.getElementById("-5");
			obj.style.display = "none";
		}
}
