function frame_receipt(value) {
    content = "";
    content = content.concat("<div class='receipt' >"); // start "receipt id"
    content = content.concat("<br><table width='100%' border='0'>");
    content = content.concat("<thead>");
    content = content.concat("<tr>");
    content = content.concat("<th colspan='4'>");
    content = content.concat("<h4>JAYA JAYA RAGHUVEER SAMARTH</h4>");
    content = content.concat("</th>");
    content = content.concat("</tr>");
    content = content.concat("<tr>");
    content = content.concat("<th colspan='4'>");
    content = content.concat("<h2>SHRI SADGURU SAMARTH NARAYANA ASHRAM</h2>");
    content = content.concat("</th>");
    content = content.concat("</tr>");
    content = content.concat("<tr>");
    content = content.concat("<th colspan='4'>");
    content = content.concat("<h3>SHRI SAMRTHA KAMADHENU GOWSHALA</h3>");
    content = content.concat("</th>");
    content = content.concat("</tr>");
    content = content.concat("<tr>");
    content = content.concat("<th colspan='4'>");
    content = content.concat("<p style='margin: 0 2px2px 0 0;'>\(Regd. No. HRR-IV-00126-2010/11\) </p>");
    content = content.concat("</th>");
    content = content.concat("</tr>");
    content = content.concat("<tr>");
    content = content.concat("<th colspan='4'>");
    content = content.concat("<p style='margin: 0 0 0 0;'> Opp. M.C.H. Colony,Shiv Bagh,Jiyaguda,Puranapul,Hyderabad - 5000 006 </p>");
    content = content.concat("</th>");
    content = content.concat("</tr>");
    content = content.concat("</thead>");
    content = content.concat("<tbody>");
    content = content.concat("<tr>");
    content = content.concat("<td>Received with thanks from Shri. "+ value.uname +"</td>");
    content = content.concat("<td>with ID "+ value.dnr_id +"</td>");
    content = content.concat("<td colspan='2'>on "+ value.ddate + "</td>");
    content = content.concat("</tr>");
    content = content.concat("<tr>");
    content = content.concat("<td><b> the sum of Rupees </b></td>");
    content = content.concat("<td colspan='3'>"+value.pamnt +"</td>");
    content = content.concat("</tr>");
    content = content.concat("<tr>");
    content = content.concat("<td><b> in the form of  </b></td>");
    content = content.concat("<td colspan='3'>"+value.pmode+"</td>");
    content = content.concat("</tr>");
    content = content.concat("<tr><td><b> being the seva for  </b></td>");
    content = content.concat("<td colspan='3' style='text-align:left'>Gow Seva</td>");
    content = content.concat("</tr>");
    content = content.concat("<tr>");
    content = content.concat("<td rowspan='3' style='vertical-align:bottom;'>" + value.pamnt + "</td>");
    content = content.concat("<td rowspan='3' style='vertical-align:bottom;text-align:left'><h4>JAI KAMADHENU</h4></td>");
    content = content.concat("<td rowspan='3' style='vertical-align:bottom;text-align:right;'><b>Authorized Signatory</b></td>");
    content = content.concat("</tr>");
    content = content.concat("<tbody>");
    content = content.concat("</table>");
    return content;
}
jQuery(document).ready(function() {
    /*Ajax utility to handle print a single receipt */
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

                jQuery.each(receipts, function (key,value) {
                    console.log(frame_receipt(value));
                    jQuery(parentobj).append(frame_receipt(value));
                });
                window.print();

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
