/*if (window.print) {
  document.write('<form> '
  + '<input type=button name=print value="Print Page" '
  + 'onClick="javascript:window.print()"></form>');
 }*/

function display_apt_ele(val) {
/*alert("The input value has changed. The new value is: " + val);*/

	if(val == "New")	{
		document.getElementById('uname').style.display='none';
		document.getElementById('fname').style.display='table-row';	
		document.getElementById('lname').style.display='table-row';
		document.getElementById('email').style.display='table-row';
		document.getElementById('gender').style.display='table-row';
		document.getElementById('password').style.display='table-row';
		document.getElementById('cpassword').style.display='table-row';
		document.getElementById('reg_btn').value = 'Register Now';
		document.getElementById('mob_num').style.display='table-row';
		document.getElementById('u_dob').style.display='table-row';
		document.getElementById('u_unique_id').style.display='table-row';
	} else if (val == "Existing") {
		document.getElementById('uname').style.display='table-row';
		document.getElementById('fname').style.display='none';	
		document.getElementById('lname').style.display='none';
		document.getElementById('email').style.display='none';
		document.getElementById('gender').style.display='none';		
		document.getElementById('password').style.display='table-row';
		document.getElementById('cpassword').style.display='none';
		document.getElementById('reg_btn').value = 'Donate Now';
		document.getElementById('mob_num').style.display='none';
		document.getElementById('u_dob').style.display='none';
		document.getElementById('u_unique_id').style.display='none';
		
	} else if (val == "Anonymous") {
		document.getElementById('fname').style.display='table-row';	
		document.getElementById('lname').style.display='table-row';
		document.getElementById('gender').style.display='table-row';
		document.getElementById('uname').style.display='none';
		document.getElementById('email').style.display='none';
		document.getElementById('password').style.display='none';
		document.getElementById('cpassword').style.display='none';
		document.getElementById('reg_btn').value = 'Donate Now';
		document.getElementById('mob_num').style.display='none';
		document.getElementById('u_dob').style.display='none';
		document.getElementById('u_unique_id').style.display='none';
	}

}

function handle_donation(val) {	
	if(val == "NO_DONATION") {
		document.getElementById('damount').style.display='none';	
	} else {
		document.getElementById('damount').style.display='table-row';
	}
}

function handle_search(val) {	
	if(val == "d_name") {	
		
		document.getElementById('sexp_l').style.display='table-cell';
		document.getElementById('sexp_t').style.display='table-cell';
		document.getElementById('sexp_fd').style.display='none';
		document.getElementById('sexp_td').style.display='none';	
		document.getElementById('fdate').style.display='none';	
		document.getElementById('todate').style.display='none';	
		
	} else if(val == "dates"){
		
		document.getElementById('sexp_l').style.display='table-cell';
		document.getElementById('sexp_t').style.display='none';
		document.getElementById('sexp_fd').style.display='table-cell';
		document.getElementById('sexp_td').style.display='table-cell';
		
		document.getElementById('fdate').style.display='table-cell';	
		document.getElementById('todate').style.display='table-cell';				
	} else {
		
		document.getElementById('sexp_l').style.display='none';
		document.getElementById('sexp_t').style.display='none';
		document.getElementById('sexp_fd').style.display='none';
		document.getElementById('sexp_td').style.display='none';
		document.getElementById('fdate').style.display='none';	
		document.getElementById('todate').style.display='none';
			
		
	}
}