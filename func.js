function change(id,lnk) {
	obj=document.getElementById(id);
	obj.href=lnk;
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

function ChangeMRTGParam() {

		var obj = "", frm;

		frm = document.mrtg_param;

		if ( frm.mrtg_param_value.value == -1 ) {
			obj = document.getElementById("-1");
			obj.style.display = "";
			obj = document.getElementById("-2");
			obj.style.display = "";
			obj = document.getElementById("-3");
			obj.style.display = "";
			obj = document.getElementById("-4");
			obj.style.display = "none";
			return 1;
		} else if ( frm.mrtg_param_value.value == -2 ) {
			obj = document.getElementById("-1");
			obj.style.display = "none";
			obj = document.getElementById("-2");
			obj.style.display = "none";
			obj = document.getElementById("-3");
			obj.style.display = "none";
			obj = document.getElementById("-4");
			obj.style.display = "";
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
		}
}
