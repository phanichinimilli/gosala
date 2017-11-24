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
                    jQuery.each(receipts, function (key,value) {
                            jQuery(parentobj).append(value.did);
                    });
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
