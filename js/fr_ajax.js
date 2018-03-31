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
    content = content.concat("<td> the sum of Rupees </td>");
    content = content.concat("<td colspan='3'><b>"+value.pamnt +"</b></td>");
    content = content.concat("</tr>");
    content = content.concat("<tr>");
    content = content.concat("<td> in the form of  </td>");
    content = content.concat("<td colspan='3'><b>"+value.pmode+"</b></td>");
    content = content.concat("</tr>");
    content = content.concat("<tr><td> being the seva for  </td>");
    content = content.concat("<td colspan='3' style='text-align:left'><b>Gow Seva</b></td>");
    content = content.concat("</tr>");
    content = content.concat("<tr>");
    content = content.concat("<td rowspan='3' style='vertical-align:bottom;'><b>SHREE RAMA</b></td>");
    content = content.concat("<td rowspan='3' style='vertical-align:bottom;text-align:left'><b>JAI KAMADHENU</b></td>");
    content = content.concat("<td rowspan='3' style='vertical-align:bottom;text-align:right;'><b>Authorized Signatory</b></td>");
    content = content.concat("</tr>");
    content = content.concat("<tbody>");
    content = content.concat("</table>");
    content = content.concat("<p></p>");
    content = content.concat("</div>"); // start "receipt id"
    return content;
}


jQuery(document).ready(function() {
    
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

            /* Not to be displayed */
            jQuery("#u_addr").css('display','none');
            jQuery("#fname").css('display','none');
            jQuery("#lname").css('display','none');
            jQuery("#email").css('display','none');
            jQuery("#gender").css('display','none');
            jQuery("#mob_num").css('display','none');
            jQuery("#u_dob").css('display','none');
            jQuery("#u_unique_id").css('display','none');
            
            /* To be displayed */
            jQuery("#d_uid").css('display','table-row');
            jQuery("#reg_btn").val('Donate');
        } else {
            /* Not to be displayed */
            jQuery("#d_uid").css('display','none');
            /* To be displayed */
            jQuery("#u_addr").css('display','table-row');
            jQuery("#fname").css('display','table-row');
            jQuery("#lname").css('display','table-row');
            jQuery("#email").css('display','table-row');
            jQuery("#gender").css('display','table-row');
            jQuery("#mob_num").css('display','table-row');
            jQuery("#u_dob").css('display','table-row');
            jQuery("#u_unique_id").css('display','table-row');

            jQuery("#reg_btn").val('Join Us');
        }
    });
    /*end */


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
                jQuery(".sucs_msg").append("<h3>Advanced receipts added successfully <br> please check donations page</h3>");
            },
            error: function(response) {
                console.log("error" + response);
            },
        });
    });
});
