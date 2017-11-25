<?php
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

    /* create donations table */    
    $users_tb = $wpdb->prefix.'users';
    $tb_donations = $wpdb->prefix.'gs_donations';        
    $charset_collate = $wpdb->get_charset_collate();

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
    wp_enqueue_script('java_scrpts',get_template_directory_uri().'/js/donar_entry.js',array(),'1.0.0',true);

}

add_action('init','gosala_theme_setup');

function new_excerpt_more($more) {
    global $post;
    return '<a class="moretag" href="'. get_permalink($post->ID) . '"> &raquo;&raquo;&raquo;</a>';
}
add_filter('excerpt_more', 'new_excerpt_more');

/* local functions */
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

function update_donor_info(array $userdata,array $user_metadata) {    
    /*create an entry for new donor*/   
    $user_id = wp_insert_user( $userdata ) ;

    //On success
    if ( ! is_wp_error( $user_id ) ) {
        debug_print("User created : ". $user_id);
    }

    foreach ($user_metadata as $mkey => $mvalue) {
# code...
        add_user_meta($user_id,$mkey,$mvalue);
    }
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
    $fname = $lname = $email = $pswd = $gender = $amount = $user_name = $mobile= $dob = $unique_id="";
    $new_donor = $pswd_error = $email_error = $genderErr = $dtype = $dtypeErr =$uname_error = $mobileErr= $dobErr = $unique_idErr="";
    $tb_donations = "table_donations";	
    $user_id = "";
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

            /*Mobile Number validation*/
            if (!empty($_POST["mob_num"])) {
                $mobile = test_input($_POST["mob_num"]);
            } else {                            
                $mobileErr = "Mobile Number is required";
            }

            /*Date of Birth validation*/
            if (!empty($_POST["u_dob"])) {
                $dob = test_input($_POST["u_dob"]);
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
            if (!empty($_POST["don_type"])) {
                $dtype = test_input($_POST["don_type"]);
            } else {    						
                $dtypeErr = "Donation type required";
            }

            /*donation amount validation*/
            if (!empty($_POST["d_amount"])) {
                $amount = test_input($_POST["d_amount"]);
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
                debug_print("pswd_error $pswd_error");
                debug_print ("email_error $email_error");
                debug_print ("genderErr $genderErr");
                debug_print ("amount_error $amount_error");
                debug_print ("donation type error $dtypeErr");
            }

        } else if($new_donor=="Anonymous") {
            debug_print( "Anonymous::  donations are added under admin");
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

            if (!empty($_POST["don_type"])) {
                $dtype = test_input($_POST["don_type"]);
            } else {                            
                $dtypeErr = "Donation type required";
            }

            if (!empty($_POST["d_amount"])) {
                $amount = test_input($_POST["d_amount"]);
            } else {
                $amount_error = "Donation is optional";
            }

            $add_donation = "TRUE";
        } else if($new_donor=="Existing") {
            debug_print("Donation by existing user");			

            if(!empty($_POST["password"])) {
                $pswd = test_input($_POST["password"]);                
            } else {
                $pswd_error = "Please enter your password";
            }

            if(!empty($_POST["user_login"])) {
                $user_name = test_input($_POST["user_login"]);                
            } else {
                $uname_error = "Please enter your username";
            }

            if (validate_user_login($user_name,$pswd) == TRUE) {
                $add_donation = "TRUE";
                if (!empty($_POST["don_type"])) {
                    $dtype = test_input($_POST["don_type"]);
                } else {                            
                    $dtypeErr = "Donation type required";
                }

                if (!empty($_POST["d_amount"])) {
                    $amount = test_input($_POST["d_amount"]);
                } else {
                    $amount_error = "Donation is optional";
                }
            } else {
                debug_print("<h2> Username and passwords doesnt match </h2>");
                debug_print("<h2> Please enter correct credentials </h2>");
            }
        }

        debug_print("usr ". $user_name."passwrd ". $pswd);

        if($update_donor == "TRUE") {
            /*create an entry for new donor*/
            debug_print(" add new donor $fname "."$lname "."$pswd "."$email "."$gender ");

            $userdata = array(
                    'user_login'     => $email,
                    'user_pass'      => $pswd,  // When creating an user, `user_pass` is expected.
                    'user_email'     => $email,                    
                    );

            $user_metadata = array(
                    'f_name' => $fname,
                    'l_name'  => $lname,
                    'GENDER'     => $gender,
                    'MOBILE'     => $mobile,
                    'DOB'        => date('Y-m-d',strtotime($dob)),
                    'UNIQUE_ID'  => $unique_id,                    
                    );    
            $user_id = update_donor_info($userdata,$user_metadata);
            echo "<h3> Congratulations <strong>$fname</strong></h3> <br>";

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

            if($update_donor == "FALSE") { 
                $user_id = get_user_by(login,"$user_name");
            } else {
                $user_id = get_user_by(login,"$email");
            }
            debug_print("user id ".$user_id->ID);
            $ddate = current_time( 'mysql' );				
            $donation_e = array ( 
                    'UID' => $user_id->ID,
                    'STATUS' => $d_status,
                    'PMODE'  => $dtype,
                    'AMNT'   => $amount,    
                    'DDATE'  => $ddate                   
                    );

            $d_id = update_donation_entry($donation_e);
            echo "<h3> Thank you for Contribution <strong>$amount</strong></h3>";

            ?>
                <a href="<?php echo $gr_page ?>?donation_id=<?php echo $d_id ?>" > <button type="button">Get Receipt</button> </a>
                <?php

        } /*end $add_donation == "TRUE"*/
        ?> <!--  <script>window.location = "<?php echo home_url('/donate_redirect/');?>"</script> -->  <?php
    } else {
        //echo " data to be entered";
        /* end else if ($_SERVER["REQUEST_METHOD"] == "POST") {*/
        ?>

            <h2><strong>Donor Information</strong></h2>	
            <form id="reg" name="frmRegistration" method="post" action=""<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"">
            <table border="0" width="500" align="center" class="demo-table">

            <tr>
            <td><label for="donortype">Donor type</label></td>
            <td>

            <select id="donortype" name="new_donor" onchange="display_apt_ele(this.value)">
            <option value="New">Register</option>
            <option value="Existing">Existing user</option>
            <option value="Anonymous">Anonymous donation</option>
            </select>
            </td>
            </tr>

            <tr id="fname">
            <td>First Name</td>
            <td><input type="text" class="demoInputBox" name="firstName" value="<?php if(isset($_POST['firstName'])) echo $_POST['firstName']; ?>" size ="30"></td>
            </tr>
            <tr id="lname">
            <td>Last Name</td>
            <td><input type="text" class="demoInputBox" name="lastName" value="<?php if(isset($_POST['lastName'])) echo $_POST['lastName']; ?>" size ="30"></td>
            </tr>
            <tr id="email">
            <td>Email</td>
            <td><input type="email" class="demoInputBox" name="userEmail" value="<?php if(isset($_POST['userEmail'])) echo $_POST['userEmail']; ?>" size ="30"></td>
            </tr>					
            <tr id="uname" style="display: none;">
            <td>User Name</td>
            <td><input type="text" class="demoInputBox" name="user_login" value="<?php if(isset($_POST['user_login'])) echo $_POST['user_login']; ?>" size ="30"></td>
            </tr>
            <tr id="password">
            <td>Password</td>
            <td><input type="password" class="demoInputBox" name="password" value="" size ="30"></td>
            </tr>
            <tr id="cpassword">
            <td>Confirm Password</td>
            <td><input type="password" class="demoInputBox" name="confirm_password" value="" size ="30"></td>
            </tr>

            <tr id="gender">
            <td>Gender</td>
            <td>
            <input type="radio" name="gender" value="Male" <?php if(isset($_POST['gender']) && $_POST['gender']=="Male") { ?>checked<?php  } ?>> Male
            <input type="radio" name="gender" value="Female" <?php if(isset($_POST['gender']) && $_POST['gender']=="Female") { ?>checked<?php  } ?>> Female
            </td>
            </tr>
            <tr id="mob_num">
            <td>Mobile Number</td>
            <td><input type="tel" class="demoInputBox" name="mob_num" value="" size ="10" maxlength="10" minlength="10"></td>
            </tr>
            <tr id="u_dob">
            <td>Date of Birth</td>
            <td><input type="date" class="demoInputBox" name="u_dob" value="" ></td>
            </tr>
            <tr id="u_unique_id">
            <td>Identification ID</td>
            <td><input type="number" class="demoInputBox" name="u_unique_id" value="Aadhar card No" size ="16"></td>
            </tr>
            <tr>
            <td><label for="donationtype">Donate as</label></td>
            <td>							
            <select id="donationtype" name="don_type" onchange="handle_donation(this.value)">						      
            <option value="OFFLINE">offline</option>
            <option value="CHEQUE">cheque</option>
            <option value="CASH">cash</option>
            <option value="ONLINE">online</option>						      
            <option value="NO_DONATION">Donate later</option>
            </select>
            </td>
            </tr>
            <tr id="damount">
            <td>Donation amount</td>
            <td><input type="text" class="demoInputBox"  name="d_amount" value="<?php if(isset($_POST['d_amount'])) { echo $_POST['d_amount'];} ?>" size ="30"></td>
            </tr>
            <tr>
            <td></td>
            <td>
            <input type="reset" value ="Clear">
            <input type="submit" id="reg_btn" name="register-user" value="Register Now" class="btnRegister">
            </td>
            </tr>
            <tr>
            <td></td>
            <td>

            </td>
            </tr>
            </table>
            </form>
            <?php
    }			
    }
    add_shortcode('u_interact', 'user_interaction');


    function donate_retreival($type) {

        debug_print("welcome to Donations retrieval");

        $s_expression="";
        $retrieve_now=FALSE;
        $retrieve_donors=FALSE;
        if ($_SERVER["REQUEST_METHOD"] != "POST") {
            ?>   

                <h2><strong>Get Donor Information</strong></h2> 
                <form id="reg" name="Donor_info" method="post" action=""<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"">
                <table border="0" width="500" align="center" class="demo-table">

                <tr>
                <td><label for="Search_on" > Select a value to <h2>search Donations</h2> </label></td>
                <td>                            
                <select id="Search_on" name="s_criteria" onchange="handle_search(this.value)">
                <option value="show_all">ALL DONATIONS</option>                
                <option value="d_name">Name</option>
                <option value="dates">Dates</option>                         
                <option value="donor_all">ALL DONORS</option>                	
                </select>
                </td>
                </tr>
                <tr >
                <td id="sexp_l" style="display: none">Search expression</td>
                <td>                
                <input id="sexp_t" style="display: none" type="text"  name="s_expression" placeholder="Enter now" size ="30" style="display: none">
                <input id="sexp_fd" style="display: none" type="date"  name="from_date" id="fdate" style="display: none">
                <input id="sexp_td" style="display: none"type="date"  name="to_date" id="todate" style="display: none">
                </td>
                </tr>

                <tr>
                <td></td>
                <td>
                <input type="submit" name="s_donor" value="Search" >
                </td>
                </tr>
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
                debug_print("search criteria =$s_criteria");


                if($s_criteria == 'd_name'){
                    /* lists all the donations with donor name */

                    $donor_name = test_input($_POST["s_expression"]);
                    debug_print("get donations with user name $donor_name");

                    /* get donor id from donors table wp_users using donor_name */
                    $g_udetails = "SELECT * FROM wp_users WHERE ";
                    $donor_info = $wpdb->get_row($g_udetails."user_login LIKE '%$donor_name%'". " OR "." display_name LIKE '%$donor_name%'"); 
                    if(!empty($donor_info)) {
                        debug_print("$donor_info->user_login id = $donor_info->ID");
                        $sql_q = "SELECT * FROM $tb_donations WHERE UID = $donor_info->ID";
                        $retrieve_now=TRUE;
                    } else {
                        debug_print("please correct the user name not available");
                        $retrieve_now=FALSE;                    
                    }                


                } else if($s_criteria == 'dates') {
                    /* lists all the donations between two dates */
                    $retrieve_now=TRUE;
                    $from_date = test_input($_POST["from_date"]);
                    $to_date = test_input($_POST["to_date"]);
                    debug_print("from $from_date to $to_date");
                    $sql_q = "SELECT * FROM $tb_donations WHERE DDATE >= \"$from_date\" AND DDATE <= \"$to_date\"";

                } else if($s_criteria == 'donor_all'){
                    /* lists all the donors in database */
                    $retrieve_now=FALSE;
                    $retrieve_donors=TRUE;
                    $sql_q = "SELECT * FROM $tb_donors";
                } else if($s_criteria == 'show_all') {
                    /* lists all the donations in database */
                    $retrieve_now=TRUE;
                    $sql_q = "SELECT * FROM $tb_donations";

                } else {
                    echo "No Data";
                }
                /* Retrieve Donor Information*/
                if($retrieve_donors) {
                    $results = $wpdb->get_results($sql_q);
                    if(!empty($results)) {
                        //echo "<form>";
                        echo "<table width='100%' border='0'>"; // Adding <table> and <tbody> tag outside foreach loop so that it wont create again and again
                        echo "<tbody>";
                        ?>
                            <tr>
                            <td>
                            <input type="checkbox" value ="<?php if(isset($_POST['select_all'])) { echo "ALL"; } else {echo "NONE";} ?>" 
                            id="select_all" name="select_all" onchange="handle_donor_selection(this)"> 
                            ALL 
                            </input>
                            </td>
                            </tr>
                            <?php
                            echo "<tr>" ;				
                        echo "<td><b>Select</b></td><td><b>DONOR ID</b></td>" ."<td><b>DONOR Name</b></td>"."<td><b>DONATIONS</b></td>" ;
                        echo "</tr>" ;
                        foreach($results as $row){                      
                            echo "<tr>";                           // Adding rows of table inside foreach loop
                            /* Pick a donor  */
                            echo "<td>";
                            ?>
                                <input type="checkbox" class="drow"  name=<?php echo $row->ID ?> value=<?php echo $row->ID ?> >
                                <?php
                                echo "</td>";
                            /* Donor ID */
                            echo "<td>";
                            echo "$row->ID";
                            echo "</td>";
                            /* Donor name */
                            echo "<td>";
                            echo "$row->display_name";
                            echo "</td>";
                            /* Number of donations done  */
                            echo "<td>";
                            echo "n_donations";
                            echo "</td>";

                            echo "</tr>";

                        }				
                        ?>
                            <tr>
                            <td>
                            <button id="p_button" > add donation </button>
                            </td>
                            <td>
                            Custom amount <input type="text" id="d_amount" value="1100" size ="10">
                            </td>
                            <div class ="return">
                            <p> place holder </p>
                            </div>
                            </tr>
                            <?php
                            echo "</tbody>";
                        echo "</table>";
                        //echo "</form>";
                    } else {
                        echo "<h2>No Donor Information</h2>";
                    }
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
                            <button id="p_all_don" > print all </button>
                            <?php
                            echo "</thead>";

                        echo "<tbody>";     
                        echo "<tr>" ;
                        echo "<td><b>DID</b></td>" ."<td><b>DONOR Name</b></td>"."<td><b>DDATE</b></td>"."<td><b>PMODE</b></td>"."<td><b>STATUS</b></td>"."<td><b>AMNT</b></td>" ;
                        echo "</tr>" ;
                        foreach($results as $row){                      
                            echo "<tr>";                           // Adding rows of table inside foreach loop
                            ?>
                                <div class="donations" value = "<?php echo $row->DID ?>">
                                <td> 
                                <a href="<?php echo $gr_page?>?donation_id=<?php echo $row->DID ?>"  id="donations_a_id">  
                                <?php echo $row->DID ?> 
                                </a>
                                </td>
                                </div>

                                <?php                         
                                $tusr = $wpdb->get_row("SELECT * FROM wp_users WHERE "." ID = $row->UID");                 
                            ?>
                                <td>
                                <a href="<?php echo $usr_page?>?d_id=<?php echo $row->UID ?>" > <?php echo $tusr->display_name ?> </a>                   
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

    add_shortcode('d_retreival', 'donate_retreival');

    add_action('wp_ajax_my_action','ajx_add_donations');
    add_action('wp_ajax_nopriv_my_action','ajx_dummy_donations');

    function ajx_add_donations () {

        global $wpdb;
        if ($_REQUEST["operation"]== 'add_off_don') {
            print_r($_REQUEST["donors"]);
            $adv_don=json_decode(stripslashes($_REQUEST['donors']),true);
            print_r($adv_don);

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
        } else if($_REQUEST["operation"] == 'print_receipts'){
            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
            $tb_donations = $wpdb->prefix.'gs_donations';

            $g_udetails = "SELECT * FROM wp_users WHERE ";
            //print_r($_REQUEST["donors"]);
            $donor_ids = json_decode(stripslashes($_REQUEST['donors']),true);
            //print_r($donor_ids[0]);
            $dreceipt_arr = array();
            //$sql = "SELECT * FROM $tb_donations WHERE DID = 1";
            //$results = $wpdb->get_results($sql);
            //print_r($results);
            //exit();
            foreach ($donor_ids as $did) {
                $sql = "SELECT * FROM $tb_donations WHERE DID = $did";
                $results = $wpdb->get_results($sql);
                //print_r($results);
                //exit();
                if(!empty($results)) {
                    foreach ($results as $drow) {
                        $usrs = $wpdb->get_results($g_udetails."ID = $drow->UID");
                        /*
                           did = $row->DID
                           user name = $urow->display_name
                           donation date = $row->DDATE
                           payment mode = $row->PMODE
                           payment status = $row->STATUS
                           paid amount = $row->AMNT

                         */
                        foreach ($usrs as $urow) {
                            $tmp = array (
                                    "did" => $drow->DID,
                                    "uname" => $urow->display_name,
                                    "ddate" => $drow->DDATE,
                                    "pmode" => $drow->PMODE,
                                    "pstat" => $drow->STATUS,
                                    "pamnt" => $drow->AMNT
                                    );
                            array_push($dreceipt_arr,($tmp));
                            //print_r($dreceipt_arr);
                            //exit();
                        }
                    }
                }
            }
            //print_r("not a valid operation ");
            //echo json_encode("print all the donations in the page");
            //print_r( json_encode(array (
            //            "data" => $dreceipt_arr[0],
            //            )));
            //print_r($dreceipt_arr);
            echo json_encode($dreceipt_arr,JSON_FORCE_OBJECT);
            //give_receipt(1);
        }
        wp_die();
    }


    function ajx_dummy_donations() {
        echo "dummy function ";
    }
    ?>

