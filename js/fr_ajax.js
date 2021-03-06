function frame_receipt(value) {
    content = "";
    content = content.concat("<p></p>");
    content = content.concat("<div class='receipt'>"); // start "receipt id"
    content = content.concat("<table width='100%' border='0'>");
    content = content.concat("<thead>");
    content = content.concat("<tr>");
    content = content.concat("<th colspan='4'>");
    content = content.concat("<p style='margin:0;padding:0;font-size:medium;'>JAYA JAYA RAGHUVEER SAMARTH</p>");
    content = content.concat("</th>");
    content = content.concat("</tr>");
    content = content.concat("<tr>");
    content = content.concat("<th colspan='4'>");
    content = content.concat("<p style='margin:0;padding:0;font-size:x-large;'>SHRI SADGURU SAMARTH NARAYANA ASHRAM</p>");
    content = content.concat("</th>");
    content = content.concat("</tr>");
    content = content.concat("<tr>");
    content = content.concat("<th colspan='4'>");
    content = content.concat("<p style='margin:0;padding:0;font-size:medium;'>SHRI SAMARTHA KAMADHENU GOWSHALA</p>");
    content = content.concat("</th>");
    content = content.concat("</tr>");
    content = content.concat("<tr>");
    content = content.concat("<th colspan='4'>");
    content = content.concat("<p style='margin: 0; font-size:small;'>\(Regd. No. HRR-IV-00126-2010/11\) </p>");
    content = content.concat("</th>");
    content = content.concat("</tr>");
    content = content.concat("<tr>");
    content = content.concat("<th colspan='4'>");
    content = content.concat("<p style='margin:0 0 10px 0; border-bottom: 1px dashed black;font-size:small;'> Opp. M.C.H. Colony,Shiv Bagh,Jiyaguda,Puranapul,Hyderabad - 5000 006 </p>");
    content = content.concat("</th>");
    content = content.concat("</tr>");
    content = content.concat("</thead>`");
    content = content.concat("<tbody>");
    content = content.concat("<tr>");
    content = content.concat("<td>Received with thanks from Shri. <b>"+ value.uname +"</b></td>");
    content = content.concat("<td>with ID <b>"+ value.dnr_id +"</b></td>");
    content = content.concat("<td colspan='2'>on <b>"+ value.ddate + "</b></td>");
    content = content.concat("</tr>");
    content = content.concat("<tr>");
    content = content.concat("<td> the sum of Rupees :: </td>");
    content = content.concat("<td colspan='3'><b>"+value.pamnt +"</b></td>");
    content = content.concat("</tr>");
    content = content.concat("<tr>");
    content = content.concat("<td> in the form of    :: </td>");
    content = content.concat("<td colspan='3'><b>"+value.pmode+"</b></td>");
    content = content.concat("</tr>");
    content = content.concat("<tr><td> being the seva for :: </td>");
    content = content.concat("<td colspan='3' style='text-align:left'><b>Gow Seva</b></td>");
    content = content.concat("</tr>");
    content = content.concat("<tr>");
    content = content.concat("<td rowspan='3' style='vertical-align:bottom; text-decoration: underline;'><b>SHREE RAMA</b></td>");
    content = content.concat("<td rowspan='3' style='vertical-align:bottom;text-align:left; text-decoration: underline;'><b>JAI KAMADHENU</b></td>");
    content = content.concat("<td rowspan='3' style='vertical-align:bottom;text-align:right; text-decoration: underline;'><b>Authorized Signatory</b></td>");
    content = content.concat("</tr>");
    content = content.concat("<tbody>");
    content = content.concat("</table>");
    content = content.concat("<p></p>");
    content = content.concat("<p></p>");
    content = content.concat("</div>"); // start "receipt id"
    return content;
}

function print_n_receipts(donation_ids) {
	parentobj = jQuery(this).parent().parent();
	jQuery(this).parent().remove();
	jQuery.ajax({
		type:"POST",
		//dataType : "json",
		url: myAjax.ajaxurl,
		data: { action : "my_action", 
			"donors" :  JSON.stringify(donation_ids),
			"operation" : "print_receipts",
		},
		success:function(response){
			console.log("success "+ response);
			receipts = JSON.parse(response);
			var content="";

			var mywindow = window.open('', 'PRINT', 'height=400,width=600');

			mywindow.document.write('<html><head>');
			mywindow.document.write('<style type="text/css"> @media print {');
			mywindow.document.write('div table thead th tr { font-weight : bold ; padding: 0; margin: 0;}');
			mywindow.document.write('.receipt {border-top: 3px solid black; border-bottom: 1px solid black;}');
			mywindow.document.write('.receipt:nth-of-type(3n) {page-break-before:auto; page-break-inside:avoid; page-break-after:always; }');
			mywindow.document.write('.receipt:last-of-type { page-break : avoid ; }');
			mywindow.document.write('} </style>');
			mywindow.document.write('</head><body>');


			jQuery.each(receipts, function (key,value) {
				console.log(frame_receipt(value));
				mywindow.document.write(frame_receipt(value));
			});
			mywindow.document.write('</body></html>');
			mywindow.document.close(); // necessary for IE >= 10
			mywindow.focus(); // necessary for IE >= 10*/

			mywindow.print();
			mywindow.close();
			jQuery(parentobj).append("<h3>successfully printed </h3>");

		},
		error: function(response) {
			console.log("error" + response);
		},
	});

}

jQuery(document).ready(function() {
   
    /* Live search of donors */
    jQuery("#donor_id").keyup(function() {
        console.log("selected "+this.value);
	var d_key = this.value;
	if (d_key.length > 0) {
        jQuery.ajax({
            type:"POST",
            //dataType : "json",
            url: myAjax.ajaxurl,
            data: { action : "my_action", 
                "donor_key" :  JSON.stringify(d_key),
                "operation" : "search_donor",
            },
            success:function(response){
	    console.log("successfully recieved donor list "+ response );
	    if (response.length > 0 ) {
	    donor_l = JSON.parse(response);
	    
	    content = "";
	    content = content.concat('<ul>');
	    if (donor_l.length > 0) {
	    jQuery.each(donor_l, function (key,user_arr) {
		    if (user_arr.meta_key == 'first_name') {
		    	console.log("name = "+ user_arr.meta_value + "mobile = "+ user_arr.mobile);
		    	content = content.concat('<li>'+user_arr.name + ','+ user_arr.mobile +'</li>');
		    } else {
		    	console.log("name = "+ user_arr.meta_value);
		    	content = content.concat('<li>'+user_arr.meta_value+'</li>');
		    }
		    });
	            content = content.concat('</ul>');

	            jQuery("#donorList").fadeIn();
	            jQuery("#donorList").html(content);
	    } else {
		    jQuery("#donorList").fadeOut();
	    }
            } else {
		    jQuery("#donorList").fadeOut();
	    }
	    },
            error: function(response) {
                console.log("donor retrieval error" + response);
            },
        });
	} else {
	    jQuery("#donorList").fadeOut();
	}

    });
    jQuery(document).on('click', 'li', function(){ 
    	console.log(" clicked "+jQuery(this).text());
		    jQuery('#donor_id').val(jQuery(this).text());  
		    jQuery('#donorList').fadeOut();  
    });  
    /* Ajax utility to handle donor search selection */
    jQuery("#donor_srch_jx").change(function() {
        console.log("selected "+this.value);
        if(this.value == "donor_all") {
            jQuery("#sexp").css('display','none');
        } else {
            jQuery("#sexp").css('display','table-row');
        }
    });
    /* Ajax utility to handle donation search selection */
    jQuery("#donation_srch_jx").change(function() {
        console.log("selected "+this.value);
        if(this.value == "show_all") {
            jQuery("#sexp").css('display','none');
            jQuery("#dates").css('display','none');
        } else if(this.value == "d_name") {
            jQuery("#sexp").css('display','table-row');
            jQuery("#dates").css('display','none');
        } else if(this.value == 'dates') {
            console.log("datesss ");
            jQuery("#sexp").css('display','none');
            jQuery("#dates").css('display','table-row');
        }
    });
    
    /* Ajax utility to handle print a single receipt */
    jQuery("#select_all_jx").click(function () {
        console.log("selected checkall "+this.checked);
        if(this.checked) {
            /* select all checkboxes */
            jQuery("*#donation_sel").attr("checked",'checked');
        } else {
            /* de-select all checkboxes */
            jQuery("*#donation_sel").removeAttr('checked');
        }
    });
    /*Ajax utility to Handle Front end layout changes of Donor registration*/
	jQuery("#donortype_jx").change(function () {
		console.log("selected "+this.value);
		if( this.value == "Existing") {
			/* for existing user*/
			
			/* required and option feilds */
			jQuery("#donor_id").attr('required',true);
			jQuery("#d_amount_id").attr('required',true);
			jQuery("#don_type_id").attr('required',true);
			jQuery("#firstName").attr('required',false);
			jQuery("#lastName").attr('required',false);
			jQuery("#mob_num_id").attr('required',false);
			jQuery("#u_dob_id").attr('required',false);
			jQuery("#u_unique_id").attr('required',false);
			
			/* Not to be displayed */
			jQuery("#u_addr").css('display','none');
			jQuery("#fname").css('display','none');
			jQuery("#lname").css('display','none');
			jQuery("#email").css('display','none');
			jQuery("#gender").css('display','none');
			jQuery("#mob_num").css('display','none');
			jQuery("#u_dob").css('display','none');
			jQuery("#u_unique").css('display','none');

			
			/* To be displayed */
			jQuery("#d_uid").css('display','table-row');
			jQuery("#u_dtype").css('display','table-row');
			jQuery("#damount").css('display','table-row');
			jQuery("#don_type_id").val('CASH');
			jQuery("#reg_btn").val('Donate');
			jQuery("#u_buttons").css('display','table-row');
			
		} else if (this.value == "New") {
			
			/* required and option feilds */
			jQuery("#donor_id").attr('required',false);
			jQuery("#firstName").attr('required',true);
			jQuery("#lastName").attr('required',true);
			jQuery("#mob_num_id").attr('required',true);
			jQuery("#u_dob_id").attr('required',true);
			jQuery("#u_unique_id").attr('required',true);
			
			/* Not to be displayed */
			jQuery("#d_uid").css('display','none');
			/* To be displayed */
			jQuery("#fname").css('display','table-row');
			jQuery("#lname").css('display','table-row');
			jQuery("#email").css('display','table-row');
			jQuery("#gender").css('display','table-row');
			jQuery("#mob_num").css('display','table-row');
			jQuery("#u_dob").css('display','table-row');
			jQuery("#u_addr").css('display','table-row');
			jQuery("#u_unique").css('display','table-row');
			jQuery("#u_dtype").css('display','table-row');
			jQuery("#damount").css('display','table-row');
			jQuery("#u_buttons").css('display','table-row');
			
			//jQuery("#don_type_id").val('CASH');
			jQuery("#reg_btn").val('Join Us');
			
		} else {
			/* required and option feilds */
			jQuery("#donor_id").attr('required',false);
			jQuery("#firstName").attr('required',false);
			jQuery("#lastName").attr('required',false);
			jQuery("#mob_num_id").attr('required',false);
			jQuery("#u_dob_id").attr('required',false);
			jQuery("#u_unique_id").attr('required',false);
		
			/* Not to be displayed */
			jQuery("#fname").css('display','none');
			jQuery("#lname").css('display','none');
			jQuery("#email").css('display','none');
			jQuery("#gender").css('display','none');
			jQuery("#mob_num").css('display','none');
			jQuery("#u_dob").css('display','none');
			jQuery("#u_addr").css('display','none');
			jQuery("#d_uid").attr('required',false);
			jQuery("#u_unique").css('display','none');
			jQuery("#d_uid").css('display','none');
			jQuery("#u_dtype").css('display','none');
			jQuery("#damount").css('display','none');
			jQuery("#don_type_id").val('CASH');
			jQuery("#u_buttons").css('display','none');
			
		}
	});
    /*end */

    /*Ajax utility to print single donations receipts*/
    jQuery("#prnt_sngl_rcpt").click ( function () {
        var donor_arr = [];
        var donation_ids = [];
        donation_ids.push(jQuery(this).val());
        console.log(donation_ids);
        print_n_receipts(donation_ids);
    });

    /*Ajax utility to print all the donations receipts*/
    jQuery("#p_all_don_jx").click ( function () {
        var donor_arr = [];
        var donation_ids = [];
        jQuery('*#donation_sel').each(function() {
            if(this.checked ) {
                donation_ids.push(jQuery(this).val());
            }
        });
        console.log(donation_ids);
        print_n_receipts(donation_ids);
        
    });
    /* Ajax utilitiy to print current donor list */
    jQuery("#print_button_jx").click ( function () {
        console.log("print button got clicked");
	    window.print();
    });
    /* Ajax utilitiy to delete selected donor list */
    jQuery("#del_button_jx").click ( function () {
        console.log("delete button got clicked");
        var checkboxes = jQuery('.drow:checked');
        var donor_ids = [];
        for (var i = 0; i < checkboxes.length; i++) {
            if (checkboxes[i].type == 'checkbox' && checkboxes[i].checked == true && checkboxes[i].value) {
                donor_ids.push(checkboxes[i].value);
            }
	}
         console.log(JSON.stringify(donor_ids));
        jQuery.ajax({
            type:"POST",
            //dataType : "json",
            url: myAjax.ajaxurl,
            data: { action : "my_action", 
                "donors" :  JSON.stringify(donor_ids),
                "operation" : "delete_donor",
            },
            success:function(response){
                console.log("success "+ response );
                jQuery(".sucs_msg").html("<h3>Donors deleted successfully</h3>");
            },
            error: function(response) {
                console.log("error" + response);
            },
        });
    });
    /* Ajax utilitiy to add advanced receipts */
    jQuery("#p_button_jx").click ( function () {
        console.log("button got clicked");

        var checkboxes = jQuery('.drow:checked');
        var donor_ids = [];
        for (var i = 0; i < checkboxes.length; i++) {
            if (checkboxes[i].type == 'checkbox' && checkboxes[i].checked == true && checkboxes[i].value) {
                donor_ids.push(checkboxes[i].value);
            }
        }
        d_amount = jQuery("#d_amount").val();
        var donor_obj = {
            "id" : donor_ids,
            "amnt" : d_amount,
        };
        console.log(JSON.stringify(donor_obj));
        jQuery.ajax({
            type:"POST",
            //dataType : "json",
            url: myAjax.ajaxurl,
            data: { action : "my_action", 
                "donors" :  JSON.stringify(donor_obj),
                "operation" : "add_off_don",
            },
            success:function(response){
                console.log("success "+ response );
                jQuery(".sucs_msg").html("<h3>Advanced receipts added successfully <br> please check donations page</h3>");
            },
            error: function(response) {
                console.log("error" + response);
            },
        });
    });
});
