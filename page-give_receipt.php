<?php 

function handle_give_receipt($donation_id) {
	global $wpdb;
	/*$donation_id = $_REQUEST['donation_id'];*/
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    $tb_donations = $wpdb->prefix.'gs_donations';
    $tb_donors = $wpdb->prefix.'users';
    $tb_donor_meta = $wpdb->prefix.'usermeta';
    $g_udetails = "SELECT * FROM wp_users WHERE ";

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
        //printf("data retrieved ");
        //print_r($donationv);
        //print_r($donorv);
        //print_r($donorm);
?>
        <div class="don_receipt dont_show">    
        <table style="width:100%;" >
            <thead>
                <tr>
                <th colspan="4">
                <h4>JAYA JAYA RAGHUVEER SAMARTH</h4>
                </th>
                </tr>
                <tr>
                <th colspan="4">
                <h2>SHRI SADGURU SAMARTH NARAYANA ASHRAM</h2>
                </th>
                </tr>
                <tr>
                <th colspan="4">
                <h3>SHRI SAMRTHA KAMADHENU GOWSHALA</h3>
                </th>
                </tr>
                <tr>
                <th colspan="4">
                <p style="margin: 0 2px2px 0 0;">(Regd. No. HRR-IV-00126-2010/11) </p>
                </th>
                </tr>
                <tr>
                <th colspan="4">
                <p style="margin: 0 0 0 0;"> Opp. M.C.H. Colony,Shiv Bagh,Jiyaguda,Puranapul,Hyderabad - 5000 006 </p>
                </th>
                </tr>
                <tr>
                <th colspan="4">
                <p style="margin: 0 0 0 0;"> Phone : 040-24825313 Cell: 9296358630, 9949121508 </p>
                </th>
                </tr>
                <tr>
                <th colspan="4">
                <h4 style="margin: 0 0 0 3px"> "Shree Kamadhenu Prarabramhane Namaha"</h4>
                </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        Received with thanks from Shri. <?php printf("<b>%s</b>",$donorv[0]->display_name); ?> 
                    </td>
                    <td >
                        with ID 
                        <?php printf("<b>%s</b>",$donorm[0]->meta_value); ?> 
                    </td>
                    <td colspan="2">
                        on
                        <?php printf("<b>%s</b>",$donationv[0]->DDATE);?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b> the sum of Rupees </b>
                    </td>
                    <td colspan="3"> 
                        <?php printf("%s",$donationv[0]->AMNT);?>
                    </td>
                </tr>
                 <tr>
                    <td>
                        <b> in the form of  </b>
                    </td>
                    <td colspan="3"> 
                        <?php printf("%s",$donationv[0]->PMODE);?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b> being the seva for  </b>
                    </td>
                    <td colspan="3" style="text-align:left"> 
                        <?php printf("Gow Seva");?>
                    </td>
                </tr>
                <tr >
                    <td rowspan="3" style="vertical-align: bottom;">
                            <?php printf("Rs. %s",$donationv[0]->AMNT);?>
                    </td>
                    <td rowspan="3" style="vertical-align:bottom;text-align:left"> 
                        <?php printf("<h4>JAI KAMADHENU</h4>");?>
                    </td>
                    <td rowspan="3" style="vertical-align: bottom;text-align:right"> 
                        <?php printf("<b>Authorized Signatory</b>");?>
                    </td>
                </tr>
              
            </tbody>
        </table>
        </div> <!-- end div.don_receipt-->
<?php
    } else {
        printf("No data retrieved ");
        //print_r($donationv);
        //print_r($donorv);
        //print_r($donorm);
    }
}

function handle_pending_donation($donation_id) {	
	global $wpdb;	
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    $gr_page = get_permalink( get_page_by_title( 'give_receipt' ) );
    echo "link".$gr_page;
    $tb_donations = $wpdb->prefix.'gs_donations';
    $sql = "SELECT * FROM $tb_donations WHERE DID = $donation_id";
    $g_udetails = "SELECT * FROM wp_users WHERE ";

    $results = $wpdb->get_results($sql);

    if(!empty($results)) {
    	?>
    	<form method="post" action="<?php echo "$gr_page";?>">
	    	<?php
	        echo "<br><table width='100%' border='0'>"; // Adding <table> and <tbody> tag outside foreach loop so that it wont create again and again
	        ?>
			<thead>
			<h2 style="text-align: center;" > Modify Donation <h2>
			</thead>
			<?php
			echo "<tbody>";     
	        
	        foreach($results as $row){ 
	            // Adding rows of table inside foreach loop
	            echo "<tr><td>Received By </td><td> Sri Samartha Naryana Gosala,Jiyaguda</td></tr>"; 
	            echo "<tr><td>Donation Id </td><td>" . $row->DID . "</td></tr>"; 
	            ?>
	            <tr>
	            	<td>
	            		Select a different donation date
	            	</td>
	            	<td>
	            		<input type="date" id="d_date" name="d_date" value="<?php echo "$row->DDATE" ?>">
	            	</td>
	            </tr>
	            
	            <tr>
	            	<td>
	            		Select a payment mode
	            	</td>
	            	<td>
	            		 <select id="p_mode" name="p_mode">
					      <option value="OFFLINE" selected>offline</option>
					      <option value="CHEQUE">cheque</option>
					      <option value="CASH">cash</option>
					      <option value="ONLINE">online</option>						      				      
						</select>
	            	</td>
	            </tr>
	            <tr>
	            	<td>
	            		Select a different Amount
	            	</td>
	            	<td>
	            		<input type="text" id="d_amnt" name="d_amnt" value="<?php echo "$row->AMNT" ?>">
	            	</td>
	            </tr>
	            <tr>
	            	<td>            	
	            	</td>
	            	<td>
	            		<input name="d_id" id="d_id" style="display: none" value="<?php echo $row->DID ?>">
	            		<input type="submit"  value="Update" >
	            	</td>
	            </tr>

	            <?php                                                                                   
	            
	        }
	        echo "</tbody>";				
	        echo "</table>";
	        ?>
        </form>
        <?php
			

    } else {
        echo "no data";
    }

}

?>

<?php get_header(); ?>

<div class="collumn main">

<?php 
if(have_posts()) {
	while(have_posts()) { 
		the_post();				
				
		if(isset($_REQUEST['donation_id'])){
			handle_give_receipt($_REQUEST['donation_id']);
			//echo do_shortcode("[print_button]");
		} else if(isset($_REQUEST['PMODE'])){
			handle_pending_donation($_REQUEST['PMODE']);
		} else {
			if (!empty($_POST["d_id"])) {
                $d_id = test_input($_POST["d_id"]);
                echo "donation id $d_id";
            }
            if (!empty($_POST["d_date"])) {
                $d_date = test_input($_POST["d_date"]);
                echo "donation id $d_date";
            }
            if (!empty($_POST["p_mode"])) {
                $p_mode = test_input($_POST["p_mode"]);
                echo "donation id $p_mode";
                if($p_mode != "OFFLINE") {
                	$p_status = "DONE";
            	} else {
            		$p_status = "PENDING";
            	}
            }
            
             if (!empty($_POST["d_amnt"])) {
                $d_amnt = test_input($_POST["d_amnt"]);
                echo "donation id $d_amnt";
            }

			/*$tmp = date('Y-m-d', strtotime($d_date));*/
            
			global $wpdb;
    		$tb_donations = $wpdb->prefix.'gs_donations';
    		$result = $wpdb->query( "	UPDATE $tb_donations 
							SET DDATE = \"$d_date\" ,
							STATUS = \"$p_status\" ,
							PMODE = \"$p_mode\" ,
							AMNT = \"$d_amnt\"
							WHERE DID = $d_id												
						");
    		if(!empty($result)) {
    			echo "<h3> Successfully updated </h3>";	
    		} else {
    			echo "<h3> Not Updated , please check the data</h3>";	
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


