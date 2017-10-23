<?php 
function handle_give_receipt($donation_id) {
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
			echo do_shortcode("[print_button]");
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


