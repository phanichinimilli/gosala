jQuery(document).ready(function() {
        jQuery("#p_all_don").click ( function () {
                var donor_arr = [];
                var donor_ids = [];
                //donor_ids = jQuery(".donations td a").text();
                donor_arr = jQuery("*#donations_a_id");
                console.log(donor_arr);
                console.log("donor ids "+donor_arr[0].text);
                for (var i = 0; i < donor_arr.length; i++) {
                donor_ids.push(donor_arr[i].text);
                }
                console.log(donor_ids);
                parentobj = jQuery(this).parent().parent();
                jQuery(this).parent().remove();
                jQuery.ajax({
type:"POST",
//dataType : "json",
url: myAjax.ajaxurl,
data: { action : "my_action", 
"donors" :  JSON.stringify(donor_ids),
"operation" : "print_receipts",
},
success:function(response){
console.log("success "+ response);
receipts = JSON.parse(response);
var content="";

jQuery.each(receipts, function (key,value) {
        content = "";
        content = content.concat("<div class='receipt' >"); // start "receipt id"
        content = content.concat("<br><table width='100%' border='0'>");
        content = content.concat("<thead>");
        content = content.concat("<h2 style='text-align: center;' > Donation Receipt <h2>");
        content = content.concat("</thead>");
        content = content.concat("<tbody>");
        content = content.concat("<tr><td>Received By </td><td> Sri Samartha Naryana Gosala,Jiyaguda</td></tr>");
        content = content.concat("<tr><td>Donation Id </td><td>" + value.did + "</td></tr>");
        content = content.concat("<tr><td>Name </td><td>Shri "+ value.uname + "</td></tr>");
        content = content.concat("<tr><td>Date </td><td>" + value.ddate + "</td></tr>");
        content = content.concat("<tr><td>Payment Mode </td><td>" + value.pmode +  "</td></tr>");
        content = content.concat("<tr><td>Payment status </td><td>" + value.pstat + "</td></tr>");
        content = content.concat("<tr><td>Paid amount </td><td>" + value.pamnt + "</td></tr>");
        content = content.concat("</tbody>");
        content = content.concat("</table>");
        content = content.concat("</div>");
        jQuery(parentobj).append(content);
        //jQuery(parentobj).print();
});
        window.print();

},
error: function(response) {
           console.log("error" + response);
       },
       });

});
		jQuery("#p_button").click ( function () {
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
				},
				error: function(response) {
					console.log("error" + response);
				},
			});
		});
});
