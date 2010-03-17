// teachPress standard.js

// for jumpmenu
function teachpress_jumpMenu(targ,selObj,restore){
  eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
  if (restore) selObj.selectedIndex=0;
}
// for adding new tags
function teachpress_inserttag(tag) {
	if (document.getElementsByName("tags")[0].value == "") {
		document.getElementsByName("tags")[0].value = tag;
	}
	else {
		document.getElementsByName("tags")[0].value = document.getElementsByName("tags")[0].value+', '+tag;
		document.getElementsByName("tags")[0].value = document.getElementsByName("tags")[0].value;
	}	
}
// for show/hide buttons
function teachpress_showhide(where) {
     if (document.getElementById(where).style.display != "block") {
      document.getElementById(where).style.display = "block";
     }
     else {
      document.getElementById(where).style.display = "none";
     }
}
// validate forms
function teachpress_validateForm() {
  if (document.getElementById){
    var i,p,q,nm,test,num,min,max,errors='',args=teachpress_validateForm.arguments;
    for (i=0; i<(args.length-2); i+=3) { test=args[i+2]; val=document.getElementById(args[i]);
      if (val) { nm=val.name; if ((val=val.value)!="") {
        if (test.indexOf('isEmail')!=-1) { p=val.indexOf('@');
          if (p<1 || p==(val.length-1)) errors+='* '+nm+' must contain an e-mail address.\n';
        } else if (test!='R') { num = parseFloat(val);
          if (isNaN(val)) errors+='* '+nm+' must contain a number.\n';
          if (test.indexOf('inRange') != -1) { p=test.indexOf(':');
            min=test.substring(8,p); max=test.substring(p+1);
            if (num<min || max<num) errors+='* '+nm+' must contain a number between '+min+' and '+max+'.\n';
      } } } else if (test.charAt(0) == 'R') errors += '* '+nm+' is required.\n'; }
    } if (errors) alert('Sorry, but you must relieve the following error(s):\n'+errors);
    document.teachpress_returnValue = (errors == '');
} }