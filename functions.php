<?PHP
$DEBUG_LOG = "FALSE";	
$d_id = "";
function debug_print($info) {
    global $DEBUG_LOG;
    if($DEBUG_LOG == "TRUE") {
        echo $info;    
    }
}
function gosala_script_enqueue() {
    /* To Apply or include css */
    //wp_enqueue_script('java_scrpts1',get_template_directory_uri().'/js/jquery-3.2.1.js',array(),'1.0.0',true);
    wp_enqueue_script('jquery');
    wp_register_script('fr_ajax',get_template_directory_uri().'/js/fr_ajax.js');
    wp_localize_script( 'fr_ajax', 'myAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php')));
    wp_enqueue_script('fr_ajax');

    wp_enqueue_script('java_scrpts',get_template_directory_uri().'/js/donar_entry.js',array(),'1.0.0',true);
    wp_enqueue_style('cstm_style', get_template_directory_uri() . '/css/style.css', array(), '1.0.0', 'all');
    wp_enqueue_style('p_cstm_style', get_template_directory_uri() . '/css/print.css', array(), '1.0.0', 'all');

}

/* include css */
add_action( 'wp_enqueue_scripts', 'gosala_script_enqueue');

/*activate menus in theme*/
function gosala_theme_setup() {
    global $wpdb;
    add_theme_support('menus');
    /*create custom menu in theme*/
    register_nav_menu('primary','Primary navigation menu');
    register_nav_menu('secondary','secondary navigation menu');	

    $users_tb = $wpdb->prefix.'users';
    $tb_donations = $wpdb->prefix.'gs_donations';        
    $charset_collate = $wpdb->get_charset_collate();

    /* create donations table */    
    $sql = "CREATE TABLE $tb_donations (
        DID BIGINT(20) NOT NULL AUTO_INCREMENT,                 
        UID BIGINT(20) unsigned NOT NULL ,
        PMODE tinytext,                 
        STATUS tinytext,
        AMNT decimal(10,2),
            DDATE date ,                              
            PRIMARY KEY (DID),               
            FOREIGN KEY (UID) REFERENCES $users_tb(ID)
        ) $charset_collate;";


require_once( ABSPATH . 'wp-admin/includes/upgrade.php');               
dbDelta( $sql );         

}
add_action('after_setup_theme', 'remove_admin_bar');
 
function remove_admin_bar() {
if (!current_user_can('administrator') && !is_admin()) {
  show_admin_bar(false);
}
}
add_action('init','gosala_theme_setup');
/* Re-direct setup*/
function my_login_redirect( $redirect_to, $request, $user ) {
    //is there a user to check?
    if ( isset( $user->roles ) && is_array( $user->roles ) ) {
        //check for admins
        if ( in_array( 'administrator', $user->roles ) ) {
            // redirect them to the default place
            return $redirect_to;
        } else {
            return home_url();
        }
    } else {
        return $redirect_to;
    }
}
 
add_filter( 'login_redirect', 'my_login_redirect', 10, 3 );

function new_excerpt_more($more) {
    global $post;
    return '<a class="moretag" href="'. get_permalink($post->ID) . '"> &raquo;&raquo;&raquo;</a>';
}
add_filter('excerpt_more', 'new_excerpt_more');

/* local functions */
function get_donor_data($user_id,$key='DONOR_ID') {
    global $wpdb;
    $n_donations = 0;
    $tb_donations = $wpdb->prefix."gs_donations";
    $results = get_user_meta($user_id);
    if(!empty($results)) {
        switch($key) {
        case 'DONOR_ID':
            return $results[$key][0];
        case 'ROLE':
            return $results['wp_user_level'][0];
        case 'NAME':
            return $results['first_name'][0]." ".$results['last_name'][0];
        case 'N_DONATIONS':
            $n_donations = $wpdb->get_var("select COUNT(DID) FROM $tb_donations WHERE UID = $user_id");
            return $n_donations;
        default:
            return $results[$key][0];
        }
    } else {
        debug_print("<br> no user with such user id $user_id ");
    }
}
function get_user_id($input) {
    global $wpdb;
    $tb_donors = $wpdb->prefix."users";
    $tb_donor_data = $wpdb->prefix."usermeta";
    $results = "";
    
    $sql_q = "SELECT user_id FROM $tb_donor_data WHERE meta_value = \"$input\" AND ( meta_key='first_name' OR meta_key='DONOR_ID' )";
    debug_print("sql querry $sql_q");
    $results = $wpdb->get_var($sql_q);
    if(!empty($results)) {
        return $results;
    } else {
        debug_print("<br> no user with such name $input <br> Checking if input is donor id");
    }
}
function lookup_donor($d_uid) {
    global $wpdb;
    $tb_donors = $wpdb->prefix."users";
    $tb_donor_data = $wpdb->prefix."usermeta";
    $results = "";
    $pos = 0;
    //echo "input $d_uid ";
    if($pos =strpos($d_uid,",")) {
	    $mobile_num = substr($d_uid,$pos+1,strlen($d_uid)-2);
        /* Assuming it as email */
	    $sql_q = "SELECT * FROM $tb_donors WHERE user_login = \"$mobile_num\"";
            $results = $wpdb->get_results($sql_q);
	    if(!empty($results)) {
            return $results[0]->ID;
        } else {
	    debug_print(" no such user with info ".$mobile_num);
            return FALSE;
        }
    } else if(is_numeric($d_uid)){
        /* Assuming it as Mobile number */
        $sql_q = "SELECT * FROM $tb_donors WHERE user_login = \"$d_uid\"";
        $results = $wpdb->get_results($sql_q);
        if(!empty($results)) {
            //echo "<br>user id is ".$results[0]->user_login;
            return $results[0]->ID;
        } else {
            return FALSE;
        }
    } else {
        /* Assuming it as Gosala ID */
        $sql_q = "SELECT * FROM $tb_donor_data WHERE meta_key = 'DONOR_ID' AND meta_value = \"$d_uid\"";
        $results = $wpdb->get_results($sql_q);
        if(!empty($results)) {
            //echo "<br>user id is ".$results[0]->user_id;
            return $results[0]->user_id;
        } else {
            return FALSE;
        }

    }
}
function validate_user_login( $uname,$pass) {
    $user = get_user_by( 'login', $uname );
    if ( $user && wp_check_password( $pass, $user->data->user_pass, $user->ID) )
        return TRUE;
    else
        return FALSE;
}
function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}/*function test_input*/

function generate_duid($user_id) {
    $cdate = getdate();
    $duid = sprintf("GS%02s%02s%02s%04s",$cdate[mday],$cdate[mon],substr($cdate[year],-2),$user_id);
    return $duid;
}

function update_donor_info(array $userdata,array $user_metadata) {    
    /*create an entry for new donor*/   
    $user_id = wp_insert_user($userdata);
    /* On success */
    //printf("unique id generated for user id =%s is %s \n",$user_id,generate_duid($user_id));
    if ( ! is_wp_error( $user_id ) ) {
        debug_print("User created : ". $user_id);

        add_user_meta($user_id,"DONOR_ID",generate_duid($user_id));
        foreach ($user_metadata as $mkey => $mvalue) {
            # code...
            add_user_meta($user_id,$mkey,$mvalue);
        }
    } else {
        echo "Issue is Updating Donor Information";
    }
    return $user_id;
}

function update_donation_entry(array $d_entry) {
    global $wpdb;
    $tb_donations = $wpdb->prefix.'gs_donations';
    $wpdb->insert( 
            $tb_donations, 
            $d_entry                  
            );
    return $wpdb->insert_id;
}

function print_button_shortcode( $atts ){
    return '<a class="print-link" href="javascript:window.print()">Print</a>';
}

add_shortcode( 'print_button', 'print_button_shortcode' );

function give_receipt($donation_id) {
    global $wpdb;
    /*$donation_id = $_REQUEST['donation_id'];*/
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    $tb_donations = $wpdb->prefix.'gs_donations';
    $sql = "SELECT * FROM $tb_donations WHERE DID = $donation_id";
    $g_udetails = "SELECT * FROM wp_users WHERE ";

    $results = $wpdb->get_results($sql);

    if(!empty($results)) {
        echo "<br><table width='100%' border='0'>"; // Adding <table> and <tbody> tag outside foreach loop so that it wont create again and again
?>
            <thead>
            <h2 style="text-align: center;" > Donation Receipt <h2>
            </thead>
<?php
        echo "<tbody>";     

        foreach($results as $row){ 
            // Adding rows of table inside foreach loop
            echo "<tr><td>Received By </td><td> Sri Samartha Naryana Gosala,Jiyaguda</td></tr>"; 
            echo "<tr><td>Donation Id </td><td>" . $row->DID . "</td></tr>"; 
            $tusr = $wpdb->get_results($g_udetails."ID = $row->UID"); 
            foreach ($tusr as $urow) {
                echo "<tr><td>Name </td><td>Shri " . $urow->display_name . "</td></tr>";
            }                                               
            echo "<tr><td>Date </td><td>" . $row->DDATE . "</td></tr>"; 
            echo "<tr><td>Payment Mode </td><td>" . $row->PMODE . "</td></tr>"; 
            echo "<tr><td>Payment status </td><td>" . $row->STATUS . "</td></tr>"; 
            echo "<tr><td>Paid amount </td><td>" . $row->AMNT . "</td></tr>";                            

        }
        echo "</tbody>";				
        echo "</table>";


    } else {
        echo "no data";
    }

}
function user_interaction($type) {	
    $update_donor = $add_donation = "FALSE";
    $fname = $lname = $email = $pswd = $gender = $amount = $user_name = $mobile= $dob = $unique_id=$address="";
    $new_donor = $pswd_error = $email_error = $genderErr = $dtype = $dtypeErr =$uname_error = $mobileErr= $dobErr = $unique_idErr=$addressErr="";
    $tb_donations = "table_donations";	
    $user_id = "";
    $uid="";
    $gr_page = get_permalink( get_page_by_title( 'give_receipt' ) );
    global $d_id;

    if(isset($_POST["p_receipt"])) {
        debug_print("arguments".$_POST["args"]);
        get_receipt($_POST["args"]);
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $new_donor = test_input($_POST["new_donor"]);

        debug_print("data submitted donortype $new_donor");

        if($new_donor == "New") {
            debug_print (" welcome to gosala");

            /*First name validation*/
            if(!empty($_POST["firstName"])) {
                $fname = test_input($_POST["firstName"]);
            } else {
                $fname_error ="first name empty";
            }
            /*last name validation*/
            if(!empty($_POST["lastName"])) {
                $lname = test_input($_POST["lastName"]);
            } else {
                $lname_error ="lastname name empty";
            }
            /*password validation*/
            if(!is_user_logged_in()) {
                if(!empty($_POST["password"]) && !empty($_POST["confirm_password"])) {
                    $pswd1 = test_input($_POST["password"]);
                    $pswd2 = test_input($_POST["confirm_password"]);
                    if($pswd1 != $pswd2) {								
                        $pswd_error = "Passwords are not matching";
                    } else {
                        $pswd = $pswd1;
                    }
                } else {
                    $pswd_error = "Passwords are not matching";
                }
            }
            /*email address validation*/
            if(!empty($_POST["userEmail"])){
                if (!filter_var(test_input($_POST["userEmail"]), FILTER_VALIDATE_EMAIL)) {
                    $email_error = "Invalid email format"; 
                } else {
                    $email = test_input($_POST["userEmail"]);
                }
            } else {
                $email_error = "Email address is mandatory";
            }

            /*Gender validation*/
            if (!empty($_POST["gender"])) {
                $gender = test_input($_POST["gender"]);
            } else {    						
                $genderErr = "Gender is required";
            }

            /*Address validation*/
            if (!empty($_POST["u_addr_id"])) {
                $address = test_input($_POST["u_addr_id"]);
            } else {                            
                $addressErr = "Address is required";
            }
            /*Mobile Number validation*/
            if (!empty($_POST["mob_num_id"])) {
                $mobile = test_input($_POST["mob_num_id"]);
            } else {                            
                $mobileErr = "Mobile Number is required";
            }

            /*Date of Birth validation*/
            if (!empty($_POST["u_dob_id"])) {
                $dob = test_input($_POST["u_dob_id"]);
            } else {                            
                $dobErr = "DOB is required";
            }

            /*Date of Birth validation*/
            if (!empty($_POST["u_unique_id"])) {
                $unique_id = test_input($_POST["u_unique_id"]);
            } else {                            
                $unique_idErr = "Unique ID is required";
            }

            /*Donation type validation*/
            if (!empty($_POST["don_type_id"])) {
                $dtype = test_input($_POST["don_type_id"]);
            } else {    						
                $dtypeErr = "Donation type required";
            }

            /*donation amount validation*/
            if (!empty($_POST["d_amount_id"])) {
                $amount = test_input($_POST["d_amount_id"]);
            } else {
                $amount_error = "Donation is optional";
            }
            if(empty($pswd_error) && empty($email_error) && empty($genderErr) && empty($dtypeErr) && empty($dobErr) && empty($unique_idErr)) {
                $update_donor = "TRUE";
                if(empty($amount_error)) {
                    $add_donation = "TRUE";
                    debug_print("$dob $unique_id");
                }  				
            } else {
                debug_print("pswd_error = $pswd_error ,");
                debug_print ("email_error = $email_error ,");
                debug_print ("genderErr = $genderErr ,");
                debug_print ("amount_error = $amount_error ,");
                debug_print ("donation type = $dtypeErr ,");
                debug_print ("dob error = $dobErr ,");
                debug_print ("unique id error = $unique_idErr ,");
            }

        } else if($new_donor=="Anonymous") {
            debug_print( "Anonymous::  donations are added under admin");
            if(!empty($_POST["firstName"])) {
                $fname = test_input($_POST["firstName"]);
            } else {
                $fname_error ="first name empty ".$_POST["firstName"];
            }

            /*last name validation*/
            if(!empty($_POST["lastName"])) {
                $lname = test_input($_POST["lastName"]);
            } else {
                $lname_error ="lastname name empty ".$_POST["lastName"];
            }

            if (!empty($_POST["don_type"])) {
                $dtype = test_input($_POST["don_type"]);
            } else {                            
                $dtypeErr = "Donation type required ".$_POST["don_type"];
            }

            if (!empty($_POST["d_amount"])) {
                $amount = test_input($_POST["d_amount"]);
            } else {
                $amount_error = "Donation is optional ".$_POST["d_amount"];
            }

            $add_donation = "TRUE";
        } else if($new_donor=="Existing") {
            debug_print("Donation by existing user");			

            if(!empty($_POST["donor_id"])) {
                $d_uid = test_input($_POST["donor_id"]);                
            } else {
                $d_uid_error = "Please enter your username or mobile or gosala id";
            }

            if (($uid = lookup_donor($d_uid)) != FALSE ) {
                $add_donation = "TRUE";
                if (!empty($_POST["don_type_id"])) {
                    $dtype = test_input($_POST["don_type_id"]);
                } else {                            
                    $dtypeErr = "Donation type required";
                }

                if (!empty($_POST["d_amount_id"])) {
                    $amount = test_input($_POST["d_amount_id"]);
                } else {
                    $amount_error = "Donation is optional";
                }
            } else {
                debug_print("<h2> Username and passwords doesnt match </h2>");
                debug_print("<h2> Please enter correct credentials </h2>");
            }
        }

	/* using mobile number as both username and password */
	$user_name = $pswd = $mobile;

        debug_print("usr ". $user_name."passwrd ". $pswd);

        if($update_donor == "TRUE") {
            /*create an entry for new donor*/
            debug_print(" add new donor $fname "."$lname "."$pswd "."$email "."$gender ");

            $userdata = array(
                'user_login'     => $user_name,
                'user_pass'      => $pswd,  // When creating an user, `user_pass` is expected.
                'user_email'     => $email,
                'role'           => 'subscriber',               
                'first_name'     => $fname,
                'last_name'      => $lname,
                'description'    => "employee",
                'display_name'   => $fname,
            );

            $user_metadata = array(
                'GENDER'     => $gender,
                'MOBILE'     => $mobile,
                'DOB'        => date('Y-m-d',strtotime($dob)),
                'UNIQUE_ID'  => $unique_id,
                'ADDRESS'    => $address,
            );    
            $user_id = update_donor_info($userdata,$user_metadata);
            echo "<h3> Congratulations <strong>$fname</strong></h3>";

            /**/
        } /*$update_donor == "TRUE"*/
        if($add_donation == "TRUE") {

            /*create_table($tb_donations);*/
            global $wpdb;        
            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
            debug_print("add new donation for user email =$email, tb_donations =$tb_donations");

            $d_status ="";
            if($dtype == "OFFLINE") {
                $d_status = "PENDING";
            } else {
                $d_status = "DONE";
            }

            if($update_donor != "FALSE") { 
                //$user_id = get_user_by(login,"$user_name");
                //$uid = $user_id->ID;
                //} else {
                $uid = $user_id;
            }
            debug_print("user id ".$user_id);
            $ddate = current_time( 'mysql' );				
            $donation_e = array ( 
                'UID' => $uid,
                'STATUS' => $d_status,
                'PMODE'  => $dtype,
                'AMNT'   => $amount,    
                'DDATE'  => $ddate                   
            );

            $d_id = update_donation_entry($donation_e);
            echo "<h3> Thank you for Contribution <strong>$amount</strong></h3>";

?>

	<button id="prnt_sngl_rcpt" value="<?php echo $d_id?>"> Get Receipt </button>
            <!--
            <a href="<?php echo $gr_page ?>?donation_id=<?php echo $d_id ?>" > <button type="button">Get Receipt</button> </a>
            -->
<?php

        } /*end $add_donation == "TRUE"*/
        ?> <!--  <script>window.location = "<?php echo home_url('/donate_redirect/');?>"</script> -->  <?php
    } else {
?>

            <h2><strong>Donor Information</strong></h2>	
            <form id="reg" name="frmRegistration" method="post" action=""<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"">
            <table border="0" width="500" align="center" class="demo-table">

            <tr>
            <td><label for="donortype">Donor type</label></td>
            <td>

            <!-- <select id="donortype_jx" name="new_donor" onchange="display_apt_ele(this.value)"> -->
            <select id="donortype_jx" name="new_donor" >
            <option value="None" selected>- None -</option>
            <option value="New">Register</option>
            <option value="Existing">Existing user</option>
            <!--
            <option value="Anonymous">Anonymous donation</option> 
            -->
            </select>
            </td>
            </tr>

            <tr id="fname" style="display:none">
                <td>First Name</td>
                <td>
                    <input type="text" class="demoInputBox" id="firstName" name="firstName" 
                           value="<?php if(isset($_POST['firstName'])) echo $_POST['firstName']; ?>" 
                           size ="30" required > <sup>*</sup>
                </td>
            </tr>
            <tr id="lname" style="display:none">
                <td>Last Name</td>
                <td>
                    <input type="text" class="demoInputBox" id="lastName" name="lastName"
                           value="<?php if(isset($_POST['lastName'])) echo $_POST['lastName']; ?>" 
                           size ="30" required > <sup>*</sup>
                </td>
            </tr>
            <tr id="d_uid" style="display:none">
                <td>Donor ID</td>
                <td>
                    <input type="text" class="demoInputBox" id="donor_id" name="donor_id"
                           placeholder="Donor Id or mobile or email" 
                           value="<?php if(isset($_POST['donor_id'])) echo $_POST['donor_id']; ?>"
                           size ="30" > <sup>*</sup>
		    <div id="donorList"></div>  
                </td>
            </tr>

            <tr id="email" style="display:none">
                <td>Email</td>
                <td>
                    <input type="email" class="demoInputBox" id="userEmail" name="userEmail" 
                           placeholder="E-mail address" 
                           value="<?php if(isset($_POST['userEmail'])) echo $_POST['userEmail']; ?>" 
                           size ="30">
                </td>
            </tr>					
            <?php if(!is_user_logged_in()) { ?>
            <!-- Displayed only for non-registered users -->
            <tr id="password" style="display:none">
                <td>Password</td>
                <td><input type="password" class="demoInputBox" id="password" name="password" value="" size ="30"></td>
            </tr>
            <tr id="cpassword" style="display:none">
                <td>Confirm Password</td>
                <td><input type="password" class="demoInputBox" id="confirm_password" name="confirm_password" value="" size ="30"></td>
            </tr>
<?php } ?>
            <tr id="gender" style="display:none">
                <td>Gender</td>
                <td>
                    <input type="radio" name="gender" 
                    value="Male" <?php if(isset($_POST['gender']) && $_POST['gender']=="Male") { ?>checked<?php  } ?> > Male
                
		    <input type="radio" name="gender" value="Female" <?php if(isset($_POST['gender']) && $_POST['gender']=="Female") { ?>checked<?php  } ?> > Female
                </td>
            </tr>  
            <tr id="mob_num" style="display:none">
    		<td>Mobile Number</td>
    		<td> <input type="tel" class="demoInputBox" id="mob_num_id" name="mob_num_id" value="" placeholder="" size ="10" maxlength="10" minlength="10" required > <sup>*</sup></td>
	    </tr>
	    <tr id="u_dob" style="display:none">
		<td>Date of Birth</td>
		<td><input type="date" class="demoInputBox" id="u_dob_id" name="u_dob_id" value="" required><sup>*</sup></td>
	    </tr>
	    <tr id="u_addr" style="display:none">
		<td>Address</td>
		<td> <textarea rows="4" cols="50" class="demoInputBox" id="u_addr_id" name="u_addr_id" value="" placeholder="Your address" > </textarea> </td>
	    </tr>

	    <tr id="u_unique" style="display:none">
		<td>Identification ID</td>
		<td> <input type="number" class="demoInputBox" id="u_unique_id" name="u_unique_id" placeholder="Aadhar card No" size ="16" required >  <sup>*</sup></td>
	    </tr>

	    <tr id="u_dtype" style="display:none">
		<td><label for="donationtype">Donate as</label></td>
		<td>							
		    <select name="don_type_id" id="don_type_id" onchange="handle_donation(this.value)">
			    <option value="OFFLINE">Advance</option>
			    <option value="CHEQUE">cheque</option>
			    <option value="CASH" selected>cash</option>
			    <option value="ONLINE">online</option>						      
			    <option value="NO_DONATION">Donate later</option>
		    </select>
	        </td>
	    </tr>
	    <tr id="damount" style="display:none">
		<td>Donation amount</td>
		<td> <input type="text" class="demoInputBox"  id="d_amount_id" name="d_amount_id" value="<?php if(isset($_POST['d_amount_id'])) { echo $_POST['d_amount_id'];} ?>" size ="30"> </td>
	    </tr>
	    <tr id="u_buttons" style="display:none">
	    <td></td>
	    <td>
		<input type="reset" value ="Clear">
		<input type="submit" id="reg_btn" name="register-user" value="Join Us" class="btnRegister">
	    </td>
	    </tr>
	    <tr>
	    <td></td>
	    <td></td>
	    </tr>
    </table>
    </form>
<?php
    }			
}
add_shortcode('u_interact', 'user_interaction');

function donor_retreival($type) {

    debug_print("welcome to Donations retrieval");

    $usr_page = get_permalink( get_page_by_title('edit_user'));
    $s_expression="";
    $retrieve_now=FALSE;
    $retrieve_donors=FALSE;
    if ($_SERVER["REQUEST_METHOD"] != "POST") {
?>   
        <h2><strong>Get Donor Information</strong></h2> 
        <form id="reg" name="Donor_info" method="post" action=""<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"">
        <table border="0" width="500" align="center" class="demo-table" style="table-layout:fixed;">
        <tbody>

        <tr>
            <td><label for="donor_srch_jx" > Select a value to <h2>Search Donors</h2> </label></td>
            <td>                            
                <select id="donor_srch_jx" name="s_criteria" >
                <option value="donor_all">ALL DONORS</option>                	
                <option value="d_info">Donor Info</option>
                </select>
            </td>
        </tr>

        <tr id="sexp" style="display:none">
            <td>Search expression</td>
            <td>                
            <input id="sexp_t" type="text"  name="s_expression" placeholder=" name or address or donor id" >
            </td>
        </tr>

        <tr>
            <td colspan=2 style="text-align:center">
                <input type="submit" name="s_donor" value="Search" >
            </td>
        </tr>

        </tbody>
        </table>
        </form>

<?php
    } else {
        global $wpdb;
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        $tb_donations = $wpdb->prefix.'gs_donations';
        $tb_donors = $wpdb->prefix.'users';
        $tb_donor_data = $wpdb->prefix.'usermeta';

        debug_print("data submitted ");
        if(!empty($_POST["s_criteria"])) {
            $s_criteria= test_input($_POST["s_criteria"]);
            debug_print("search criteria =$s_criteria");


            if($s_criteria == 'd_info'){
                /* lists all the donations with donor name */

                $donor_name = test_input($_POST["s_expression"]);
                debug_print("get donations with donor info:  $donor_name");

                if(!empty($donor_name)) {
                    $sql_q = "SELECT * FROM $tb_donors LEFT JOIN $tb_donor_data ON $tb_donors.ID = $tb_donor_data.user_id WHERE ( meta_key='ADDRESS' AND meta_value LIKE '%$donor_name%' ) OR ( meta_key='first_name' AND meta_value LIKE '%$donor_name%' ) OR ( meta_key='DONOR_ID' AND meta_value LIKE '%$donor_name%' )";
                    $retrieve_donors=TRUE;
                } else {
                    debug_print("please correct the user name not available");
                    $retrieve_donors=FALSE;
                }                

            } else if($s_criteria == 'donor_all'){
                /* lists all the donors in database */
                $retrieve_now=FALSE;
                $retrieve_donors=TRUE;
                $addr = $_POST["u_addr"];
                $sql_q = "SELECT * FROM $tb_donors "; 
            } else {
                echo "No Data";
            }
            /* Retrieve Donor Information*/
            if($retrieve_donors) {
                debug_print(" querry : $sql_q ");
                $results = $wpdb->get_results($sql_q);
                if(!empty($results)) {
?>
                    <p class ="sucs_msg">  </p>

                    <div class="no-printdonor" >
		    <p>
			<button id="print_button_jx" >print</button>
			<button id="del_button_jx" >Delete</button>
			<button id="p_button_jx" >Advance Receipt</button>
		    	<label> Custom amount </label> <input type="text" id="d_amount" value="1100" size ="10">
		    </p>
<?php
                    //echo do_shortcode("[print_button]");
?>
                    </div> <!-- end no_print_donor-->

                    <table width='100%' border='0'>
                    <thead>
                    <tr>
                        <td>
                        <input type="checkbox" value="<?php if(isset($_POST['select_all'])) { echo "ALL"; } else { echo "NONE";} ?>" 
                        id="select_all" 
                        name="select_all" 
                        onchange="handle_donor_selection(this)" >
                        ALL
                        </input>
                        </td>
                        <td>ID</td>
                        <td>Name</td>
                        <td>Donations</td>
                    </tr>
                    </thead>
<?php
                    echo "<tbody>";
                    foreach($results as $row){      
                        $donor_role = get_donor_data($row->ID,'ROLE');
                        echo "<tr>";                           // Adding rows of table inside foreach loop
                        /* Pick a donor  */
                        echo "<td>";
?>
                        <input type="checkbox" class="drow"  name=<?php echo $row->ID ?> value=<?php echo $row->ID ?> >
<?php
                        echo "</td>";
                        /* Donor ID */
                        echo "<td>";
                        if ($donor_role != 0) {
                            $donor_id = "ADMIN".$row->ID;
                        } else {
                            $donor_id = get_donor_data($row->ID,'DONOR_ID');
                        }
                        //echo "$donor_id"; ?>
			<a href="<?php echo $usr_page?>?d_id=<?php echo $row->ID ?>" > <?php echo $donor_id ?> </a>
			<?php
                        echo "</td>";
                        /* Donor name */
                        echo "<td>";
                        $donor_name = get_donor_data($row->ID,'NAME');
                        echo "$donor_name";
                        echo "</td>";
                        /* Number of donations done  */
                        echo "<td>";
                        $n_donations = get_donor_data($row->ID,'N_DONATIONS');
                        echo "$n_donations";
                        echo "</td>";
                        echo "</tr>";

                    }				
?>
<?php
                    echo "</tbody>";
                    echo "</table>";
                    //echo "</form>";
                } else {
                    echo "<h2>No Donor Information</h2>";
                }
            }

        } else {
            $fname_error =" search expression empty";
        }		
    }    
}

add_shortcode('donor_info', 'donor_retreival');


function donation_retreival($type) {

    debug_print("welcome to Donations retrieval");

    $s_expression="";
    $retrieve_now=FALSE;
    $retrieve_donors=FALSE;
    $user_id="";
    if ($_SERVER["REQUEST_METHOD"] != "POST") {
?>   
        <h2><strong>Get Donation Information</strong></h2> 
        <form id="reg" name="Donor_info" method="post" action=""<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"">
        
        <table border="0" width="500" align="center" class="demo-table" style="table-layout:fixed;" >
        <tbody>
        <tr>
            <td><label for="donation_srch_jx" > Select a value to <h2>search Donations</h2> </label></td>
            <td >                            
                <select id="donation_srch_jx" name="s_criteria" >
                    <option value="show_all">ALL DONATIONS</option>                
                    <option value="d_name">Name</option>
                    <option value="dates">Dates</option>                         
                </select>
            </td>
        </tr>
        
        <tr id="sexp" style="display:none;">
        <td>                
        <b>value</b>
        </td>
        <td>                
        <input id="sexp_t" type="text"  name="s_expression" placeholder="Donor Id or Name" size ="30">
        </td>
        </tr>
        
        <tr id="dates" style="display:none;">
        <td>
        <b>From </b> <input id="sexp_fd"  type="date"  name="from_date" >
        </td>
        <td>
        <b>To </b> <input id="sexp_td"  type="date"  name="to_date"   >
        </td>
        </tr>

        <tr>
        <td style="text-align:center">
        <input type="submit" name="s_donor" value="Search" >
        </td>
        </tr>
        </tbody>
        </table>
        </form>
<?php
    } else {
        global $wpdb;
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        $tb_donations = $wpdb->prefix.'gs_donations';
        $tb_donors = $wpdb->prefix.'users';
        $tb_donor_data = $wpdb->prefix.'usermeta';

        debug_print("data submitted ");
        if(!empty($_POST["s_criteria"])) {
            $s_criteria= test_input($_POST["s_criteria"]);
            
            debug_print("search criteria =$s_criteria");

            if($s_criteria == 'd_name'){
                /* lists all the donations with donor name */

                $donor_name = test_input($_POST["s_expression"]);
                debug_print("get donations with user name $donor_name");
                $user_id = get_user_id($donor_name);

                /* get donor id from donors table wp_users using donor_name */
                if(!empty($user_id)) {
                    debug_print("user id $user_id");
                    $sql_q = "SELECT * FROM $tb_donations WHERE UID = $user_id";
                    $retrieve_now=TRUE;
                } else {
                    debug_print("please correct the user name not available");
                    echo "Donor name <b> $donor_name </b> is not found in our database <br> Please correct it";
                    $retrieve_now=FALSE;                    
                }                

            } else if($s_criteria == 'dates') {
                /* lists all the donations between two dates */
                $retrieve_now=TRUE;
                $from_date = test_input($_POST["from_date"]);
                $to_date = test_input($_POST["to_date"]);
                debug_print("from $from_date to $to_date");
                $sql_q = "SELECT * FROM $tb_donations WHERE DDATE >= \"$from_date\" AND DDATE <= \"$to_date\"";
            } else if($s_criteria == 'show_all') {
                /* lists all the donations in database */
                $retrieve_now=TRUE;
                $sql_q = "SELECT * FROM $tb_donations";

            } else {
                echo "No Data";
            }

            /*retrieve Donoations data*/
            if ($retrieve_now) {                 

                $gr_page = get_permalink( get_page_by_title( 'give_receipt' ) );
                $usr_page = get_permalink( get_page_by_title('edit_user'));
                debug_print("$usr_page");
                $c_page = get_permalink( get_page_by_title( 'contributions'));
                $results = $wpdb->get_results($sql_q);
                /* Number of rows per page */
                $nr_page = 5;
                $ocount = 0;
                $nrows = ceil(count($results)/$nr_page);
                debug_print("number of records $nrows"." $c_page"."$usr_page"); 

                if(isset($_GET['p_indx']) && $_GET['p_indx'] != 0) {
                    $offset = $nr_page * $_GET['p_indx'] - $nr_page;           
                } else if(!isset($_GET['p_indx'])) {
                    $offset = 0;            
                }

                /*if Pagination of record are supported*/
                    /*if (count($results) < $nr_page) {
                      $results = $wpdb->get_results($sql_q);
                      } else {
                      $results = $wpdb->get_results($sql_q." LIMIT $offset, $nr_page");
                    }*/
                $results = $wpdb->get_results($sql_q);


                if(!empty($results)) {
                    echo "<table width='100%' border='0'>"; // Adding <table> and <tbody> tag outside foreach loop so that it wont create again and again
                    echo "<thead>";
?>
                            <button id="p_all_don_jx" > print all </button>
<?php
                    echo "</thead>";

                    echo "<tbody>";     
                    echo "<tr>" ;
                    echo "<td><b>DID</b><input type='checkbox' id='select_all_jx'></td>" ;
                    echo "<td><b>DONOR Name</b></td>"."<td><b>DDATE</b></td>"."<td><b>PMODE</b></td>"."<td><b>STATUS</b></td>"."<td><b>AMNT</b></td>" ;
                    echo "</tr>" ;
                    foreach($results as $row){                      
                        echo "<tr>";                           // Adding rows of table inside foreach loop
?>
                        <div class="donations" value = "<?php echo $row->DID ?>">
                        <td> 
                        <!-- <a href="<?php echo $gr_page?>?donation_id=<?php echo $row->DID ?>"  id="donations_a_id">  -->
                        <input type="checkbox" id="donation_sel" value="<?php echo $row->DID ?>" >  
                        <?php echo $row->DID ?> 
                        </td>
                        </div>

<?php                         
                        $tusr = $wpdb->get_row("SELECT * FROM wp_users WHERE "." ID = $row->UID");                 
?>
                                <td>
                                <?php echo $tusr->display_name ?>                  
                                </td>
<?php                

                        echo "<td>" . $row->DDATE . "</td>";                                            
                        echo "<td>" . $row->PMODE . "</td>";
                        if ($row->STATUS == "PENDING") {                                        
?>
                                    <td>
                                    <a href="<?php echo $gr_page?>?PMODE=<?php echo $row->DID ?>" > <?php echo $row->STATUS ?> </a>
                                    </td>
<?php
                        } else {
                            echo "<td>" . $row->STATUS . "</td>";
                        }
                        echo "<td>" . $row->AMNT . "</td>";                                     
                        echo "</tr>";
?>
<?php
                    }
                    echo "</tbody>";
                    echo "</table><br>"; 
                    /*for($count = 1;$count<=$nrows; $count++) { */
?>
                            <!--   <a href="<?php echo $c_page?>?p_indx=<?php echo $count ?>" > <?php echo $count ?> </a> -->
<?php  
                    /*}*/

                } else {
                    echo "<h2> No records matching criteria </h2>";
                }   
            }
        } else {
            $fname_error =" search expression empty";
        }		
    }    
}

add_shortcode('donation_info', 'donation_retreival');

add_action('wp_ajax_my_action','ajx_add_donations');
add_action('wp_ajax_nopriv_my_action','ajx_dummy_donations');

/* local function Add advanced receipts to donations database */
function handle_ajx_add_ar_db($adv_don) {
    global $wpdb;
    $dtype = 'OFFLINE';
    $d_status = 'PENDING';
    $ddate = current_time( 'mysql' );
    foreach ( $adv_don['id'] as $did) {
        $donation_e = array (
            'UID' => $did,
            'STATUS' => $d_status,
            'PMODE'  => $dtype,
            'AMNT'   => $adv_don['amnt'],
            'DDATE'  => $ddate
        );

        $d_id = update_donation_entry($donation_e);
    }
    echo json_encode("<h3> Thank you for Contribution <strong> donor id = ".$adv_don['id'][0]." amnt = ". $adv_don['amnt'] ." ndonations = ".count($adv_don['id'])."</strong></h3>");

}
/* Handle donation details that are to be printed on receipt */
function handle_ajx_get_don_receipt_data($donor_ids) {
    global $wpdb;
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    $tb_donations  = $wpdb->prefix.'gs_donations';
    $tb_donors     = $wpdb->prefix.'users';
    $tb_donor_meta = $wpdb->prefix.'usermeta';
    $dreceipt_arr = array();
    
    foreach ($donor_ids as $donation_id) {
        /* Donation details */
        $sql = "SELECT * FROM $tb_donations WHERE DID = $donation_id";
        $donationv = $wpdb->get_results($sql);
        /* Donor details */
        $sql = "SELECT display_name FROM $tb_donors WHERE ID = ".$donationv[0]->UID;
        $donorv = $wpdb->get_results($sql);
        /* Get donor unique id ,meta*/
        $sql = "SELECT meta_value from $tb_donor_meta WHERE meta_key = \"DONOR_ID\" AND user_id = ".$donationv[0]->UID;
        $donorm = $wpdb->get_results($sql);

        if(!empty($donationv) && !empty($donorv) && !empty($donorm)) {
                        /*
                           did = $row->DID
                           user name = $urow->display_name
                           donation date = $row->DDATE
                           payment mode = $row->PMODE
                           payment status = $row->STATUS
                           paid amount = $row->AMNT

			 */
		$payment_mode = "";
		if ($donationv[0]->PMODE != "OFFLINE") {
			$payment_mode = $donationv[0]->PMODE;
		} else {
			$payment_mode = "ADVANCE";
		}
		array_push($dreceipt_arr,array (
                    "did"   => $donation_id,
                    "uname" => $donorv[0]->display_name,
                    "ddate" => $donationv[0]->DDATE,
                    "pmode" => $payment_mode,
                    "pstat" => $donationv[0]->STATUS,
                    "pamnt" => $donationv[0]->AMNT,
                    "dnr_id" => $donorm[0]->meta_value,

                ));
                //print_r($dreceipt_arr);
        }
    }
    return $dreceipt_arr;
}

/* Ajax handler for logged in users*/
function ajx_add_donations () {

    global $wpdb;

    if ($_REQUEST["operation"]== 'add_off_don') {
        /* Handle adding advanced receipts in bulk to database*/
        //print_r($_REQUEST["donors"]);
        $adv_don=json_decode(stripslashes($_REQUEST['donors']),true);
        //print_r($adv_don);
        handle_ajx_add_ar_db($adv_don);
    } else if($_REQUEST["operation"] == 'print_receipts'){
        /* Handle print all donations inb the page */
        $donor_ids = json_decode(stripslashes($_REQUEST['donors']),true);
        echo json_encode(handle_ajx_get_don_receipt_data($donor_ids));
    } else if($_REQUEST["operation"] == 'delete_donor'){
        $del_donor_ids = json_decode(stripslashes($_REQUEST['donors']),true);
	print_r($_REQUEST["donors"]);

	$error_txt = "";
	foreach ($del_donor_ids as $donor_id) {
		$results = get_user_meta($donor_id);
		if (!empty($results)) {
			// Delete user's meta
			foreach ($meta as $key => $val) {
				delete_user_meta($donor_id, $key);
			}
			require_once(ABSPATH.'wp-admin/includes/user.php');
			$tb_donations  = $wpdb->prefix.'gs_donations';
			$sql_querry = "DELETE FROM $tb_donations WHERE UID = ".$donor_id;
			$wpdb->get_results($sql_querry);
			if(wp_delete_user($donor_id)) {
				$error_txt = "success:: deleted donor id = $donor_id ".$results['first_name'][0]." ".$results['last_name'][0];
			} else {
				$error_txt = "error:: in donor deletion $donor_id";
			}
		} else {
				$error_txt = "error:: in donor deletion $donor_id";
		}
		printf("$error_txt");
	}
    } else if($_REQUEST["operation"] == 'search_donor'){
	    $donor_arr = array();
	    $sql_q ="";
	    $tb_donors  = $wpdb->prefix.'users';
	    $tb_donor_data = $wpdb->prefix.'usermeta';
	    $donor_data = json_decode(stripslashes($_REQUEST['donor_key']),true);
	    //printf("key = ".$donor_data);
	    if (!empty($donor_data)) {
		    $sql_q = "SELECT * FROM $tb_donors LEFT JOIN $tb_donor_data ON $tb_donors.ID = $tb_donor_data.user_id WHERE ( meta_key='MOBILE' AND meta_value LIKE '$donor_data%' ) OR ( meta_key='first_name' AND meta_value LIKE '$donor_data%' ) OR ( meta_key='DONOR_ID' AND meta_value LIKE '$donor_data%' )";
		    $donor_list = $wpdb->get_results($sql_q);
		    if( !empty($donor_list)) {
			    //print_r(json_encode($donor_list));
			    foreach ($donor_list as $drow) {
				    array_push($donor_arr,array (
					    "meta_key" => $drow->meta_key,
					    "meta_value" => $drow->meta_value,
					    "name" => $drow->display_name,
					    "mobile" => $drow->user_login,
				    ));
			    }
			    //print_r($donor_arr);
			    echo json_encode($donor_arr);
		    } else {
			    //echo json_encode(printf("error :no donors matching info::  $donor_data , sql: $sql_q");
			    return 0;
		    }
	    }
    }
    wp_die();
}

/* Ajax handler for visitors(not logged-in users ) */
function ajx_dummy_donations() {
    echo "dummy function ";
}
    ?>

