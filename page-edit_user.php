<?php get_header(); ?>

<div class="collumn main">

<?php 
if(have_posts()) {
	while(have_posts()) { 
		the_post();	
	?>								
	<?php
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		debug_print("fname".$_POST["fname"]."lname".$_POST["lname"]."mobile".$_POST["mobile"]."gender ".$_POST["gender"]."dob ".$_POST["dob"]."u_id ".$_POST["u_id"]."user id ".$_POST["id"]."address ".$_POST["u_addr"]);
		$user_id = test_input($_POST["id"]);
		$user_metadata = array(
                    'f_name' => test_input($_POST["fname"]),
                    'l_name'  => test_input($_POST["lname"]),
                    'GENDER'     => test_input($_POST["gender"]),
                    'MOBILE'     => test_input($_POST["mobile"]),
                    'DOB'        => test_input(date('Y-m-d',strtotime($_POST["dob"]))),
                    'UNIQUE_ID'  => test_input($_POST["u_id"]),                   
                    'ADDRESS'    => test_input($_POST["u_addr"]),
                );
		/* populate meta data*/
		//update_user_meta();
		foreach ($user_metadata as $mkey => $mvalue) {
                	# code...
        		update_user_meta($user_id,$mkey,$mvalue);
    	        }
    	echo "<h2>Successfully updated </h2>";
	} 
	else 
	{
		global $wpdb;
		$user_id = $_REQUEST['d_id'];
		$usr_page = get_permalink( get_page_by_title('edit_user'));
	    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	    $tb_donations = $wpdb->prefix.'gs_donations';
	    $user_custom_data = $wpdb->prefix.'usermeta';
	    $sql = "SELECT * FROM $user_custom_data WHERE user_id = $user_id";
	    $g_udetails = "SELECT * FROM wp_users WHERE ID = $user_id";
	    $value="";
	    $default_text="Enter Now";

	    $results = $wpdb->get_row($g_udetails);

	    if(!empty($results)) {
	    	?>
	    	<form id="reg" name="DonorEdit" method="post" action="<?php echo $usr_page ?>">
		    	<?php
			        echo "<table width='50%' border='0'>"; // Adding <table> and <tbody> tag outside foreach loop so that it wont create again and again
					echo "<thead> <strong> Edit Donor Profile </strong></thead>";
					echo "<tbody>";     				        
					$results = $wpdb->get_row("SELECT * FROM wp_users WHERE ID = $user_id");
		        ?>
		        	<tr>
			            <td>
			            	<label>Donor Login : </label>	            
			            </td>	
			            <td>	
			            	<input type="text" value = "<?php echo $results->user_login; ?>" readonly> 
			            </td>
		            </tr>
		        <?php	         
			        $key = 'first_name';
			        $ph = "";
			        $value = get_user_meta($user_id,$key,true);
			        if(empty($value)) {
			        	debug_print("Check the SQL querry");
			        	$value ="";
			        	$ph="$default_text";		        	
			        } 			    	
		        ?>
		        	<tr>
			            <td>
			            	<label>First Name : </label>	            
			            </td>	
			            <td>
			            	<input type="text" name="fname" placeholder="<?php echo $ph; ?>" value = "<?php echo $value; ?>"> 
			            </td>
		            </tr>
		        <?php
			        $key = 'last_name';
			        $ph = "";
			        $value = get_user_meta($user_id,$key,true);
			        if(empty($value)) {
			        	debug_print("Check the SQL querry");
			        	$value ="";
			        	$ph="$default_text";		        	
			        }			        
		        ?>
		        	<tr>
			            <td>
			            	<label>Last Name : </label>	            
			            </td>	
			            <td>
			            	<input type="text" name="lname" placeholder="<?php echo $ph; ?>" value = "<?php echo $value; ?>"> 
			            </td>
		            </tr>
		        <?php
			        $key = 'ADDRESS';
			        $ph = "";
			        $value = get_user_meta($user_id,$key,true);
			        if(empty($value)) {
			        	debug_print("Check the SQL querry");
			        	$value ="";
			        	$ph="$default_text";		        	
			        }			        
		        ?>
		        	<tr>
			            <td>
			            	<label>Address : </label>	            
			            </td>	
			            <td>
			            	<textarea name="u_addr" placeholder="<?php echo $ph; ?>" ><?php echo $value; ?> </textarea> 
			            </td>
		            </tr>

		        <?php
			        $key = 'MOBILE';
			        $ph = "";
			        $value = get_user_meta($user_id,$key,true);
			        if(empty($value)) {
			        	debug_print("Check the SQL querry");
			        	$value ="";
			        	$ph="$default_text";		        	
			        }
		        ?>
		        	<tr>
			            <td>
			            	<label>Mobile : </label>	            
			            </td>	
			            <td>
			            	<input type="tel" name="mobile" placeholder="<?php echo $ph; ?>" value = "<?php echo $value; ?>" > 
			            </td>
		            </tr>
		        <?php
			        $key = "GENDER";
			        $ph = "";
			        $value = get_user_meta($user_id,$key,true);
			        if(empty($value)) {
			        	debug_print("Check the SQL querry");
			        	$value ="";
			        	$ph="$default_text";		        	
			        }
		        ?>
		        	<tr>
			            <td>
			            	<label>Gender : </label>	 
			            </td>	
			            <td>		            	
			            	<select id="gender" name="gender">
						      <option value="MALE"  <?php if(strtoupper($value) == "MALE") { echo "selected"; } ?>>MALE</option>
						      <option value="FEMALE" <?php if(strtoupper($value) == "FEMALE") { echo "selected"; }?>>FEMALE</option>					      
							</select>
			            </td>
		            </tr>
		        <?php
			        $key = "DOB";
			        $ph = "";
			        $value = date('Y-m-d', strtotime(get_user_meta($user_id,$key,true)));
			        if(empty($value)) {
			        	debug_print("Check the SQL querry");
			        	$value ="";
			        	$ph="$default_text";		        	
			        }			        
		        ?>
		        	<tr>
			            <td>
			            	<label>DOB : </label>	 
			            </td>	
			            <td>	           
			            	<input type="date" name="dob" placeholder="<?php echo $ph; ?>" value = "<?php echo $value; ?>" > 
			            </td>
		            </tr>
		        <?php
			        $key = "UNIQUE_ID";
			        $ph = "";
			        $value = get_user_meta($user_id,$key,true);
			        if(empty($value)) {
			        	debug_print("Check the SQL querry");
			        	$value ="";
			        	$ph="$default_text";		        	
			        }
		        ?>
	        	<tr>
		            <td>
		            	<label>UNIQUE Identifaction : </label>	 
		            </td>	
		            <td>	           
		            	<input type="number" name="u_id" placeholder="<?php echo $ph; ?>" value = "<?php echo $value; ?>" > 
		            </td>
	            </tr>
	        
		        <tr>
					<td></td>
					<td>	
						<input name="id" id="id" style="display: none" value="<?php echo $user_id ?>">                    
						<input type="submit" id="reg_btn" name="register-user" value="Update" class="btnRegister">
					</td>
				</tr>
	        </form>
	        <?php
		        echo "</tbody>";				
		        echo "</table>";
	        ?>
	        <?php				

	    } else {
	        echo "no data";
	    }
		 
	}    
		?>									
		   <br>		   
		<?php 
	}	
}
?>

</div> <!-- end collumn main -->
<div class="collumn side">
<?php get_sidebar(); ?>
</div>

</div> <!-- end row -->
</div> <!-- end content -->

<?php get_footer(); ?>


