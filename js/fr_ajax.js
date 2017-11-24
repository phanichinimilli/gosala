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
			   content = "<div class='global_receipt'>"; // start global_receipt
			   jQuery(parentobj).append(content);
                           jQuery.each(receipts, function (key,value) {
				content = "<div class='printer_no_display' style='display:none;visibility:hidden'>"; //start printer_no_display
                           	jQuery(parentobj).append(content);
                           	content = "<br><table width='100%' border='0'>"; // Adding <table> and <tbody> tag outside foreach loop so that it wont create again and again
                           	jQuery(parentobj).append(content);
                           	content = "<thead>";
                           	jQuery(parentobj).append(content);
                           	content = "<h2 style='text-align: center;' > Donation Receipt <h2>";
                           	jQuery(parentobj).append(content);
                           	content = "</thead>";
                           	jQuery(parentobj).append(content);
                           	content = "<tbody>";
                           	jQuery(parentobj).append(content);

				content = "</div>"; //end printer_no_display
                           	jQuery(parentobj).append(content);

                                content = "<div class='printer_display' style='display:none;visibility:hidden'>" ; //start printer_display
                           	jQuery(parentobj).append(content);
                           	content = "<tr><td>Received By </td><td> Sri Samartha Naryana Gosala,Jiyaguda</td></tr>";
                           	jQuery(parentobj).append(content);
                           	content = "<tr><td>Donation Id </td><td>" + value.did + "</td></tr>";
                           	jQuery(parentobj).append(content);
                                content = "</div>" ; //end printer_display
                           	jQuery(parentobj).append(content);


				content = "<div class='printer_no_display' style='display:none'>"; // start printer_no_display
                           	jQuery(parentobj).append(content);
                           	content = "<tr><td>Name </td><td>Shri "+ value.uname + "</td></tr>";
                           	jQuery(parentobj).append(content);
                           	content = "<tr><td>Date </td><td>" + value.ddate + "</td></tr>";
                           	jQuery(parentobj).append(content);
                           	content = "<tr><td>Payment Mode </td><td>" + value.pmode +  "</td></tr>";
                           	jQuery(parentobj).append(content);
                           	content = "<tr><td>Payment status </td><td>" + value.pstat + "</td></tr>";
                           	jQuery(parentobj).append(content);
                           	content = "<tr><td>Paid amount </td><td>" + value.pamnt + "</td></tr>";
                           	jQuery(parentobj).append(content);
                           	content = "</tbody>";
                           	jQuery(parentobj).append(content);
                           	content = "</table>";
                           	jQuery(parentobj).append(content);
				
				content = "</div>"; //end printer_no_display
                           	jQuery(parentobj).append(content);

			   });
			   content = "</div>"; //end global_display 
			   jQuery(parentobj).append(content);
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

//function add_donations() {
// $.post('/wordpress/wp-admin/admin-ajax.php',{'action':'my_action'},function(response) {
// 		//$('#p_excerpt').append(response);
//		console.log("repsonse");
// 	});
//}
//function add_donations(){
//	//alert("The input value has changed. The new value is: ");
//	
//	var checkboxes = document.getElementsByTagName('input');
//	var donors = [];
//	for (var i = 0; i < checkboxes.length; i++) {
//             if (checkboxes[i].type == 'checkbox' && checkboxes[i].checked == true) {
//                 //alert("selected donation id is " + checkboxes[i].value);
//				 donors.push(checkboxes[i].value);
//             }
//        }
//	var xmlhttp=new XMLHttpRequest();
//	xmlhttp.onreadystatechange=function()
//	{     
//		if (xmlhttp.readyState==4 && xmlhttp.status==200)     {       
//			alert(xmlhttp.responseText);
//		}
//	}
//	var query = '';
//	for (var i = 0; i < donors.length; i++) {
//	  if (i > 0) {
//		query += '&';
//	  } // if
//	  query += 'q[' + i + ']=' + donors[i];
//	} // for
//	xmlhttp.open("GET","../page-tmp.php?q="+query,true);
//	xmlhttp.send(); 
//	//alert("The input value has changed. The new value is: ");
//}
