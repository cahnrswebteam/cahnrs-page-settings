<?php if( get_post_type() == 'page' || get_post_type() == 'post' ) {?>


<?php wp_nonce_field('submit_pageset','pageset_nonce'); ?>

<div style="clear: both; background-color: #cccccc; border: 1px solid #000000; color: #999999; padding: 7px; margin: 0.5em auto 0.5em auto;">
<b>Page Settings</b><p />

<div class="pg_input_wrap" style="width: 80%; display: inline-block; ">
	<label>Short Title</label><br />
	<input value="<?php echo $this->model->short_title; ?>" type="text" name="_short_title" style="width: 90%;" />
</div>


<div class="pg_input_wrap" style="width: 80%; display: inline-block; ">
	<br /><label>Link To: ( Redirect )</label><br />
	<input value="<?php echo $this->model->redirect; ?>" type="text" name="_redirect_to" style="width: 90%;" />
</div>

<p></p>
<?php
$datemodified = get_the_modified_date( $d );
$datemodifiedOneYear = strtotime(date("Y-m-d", strtotime($datemodified)) . "+1 year");
//$datemodifiedOneYear = strtotime(date("m-d-Y", strtotime($datemodified)) . "+1 day");
$datemodifiedOneYearDisplay = date("F, j, Y",$datemodifiedOneYear);

if ($this->model->page_expire == '') {
    $this->model->page_expire = ( $datemodifiedOneYear )? date( 'm', $datemodifiedOneYear ).'/'.date( 'd', $datemodifiedOneYear ).'/'.date( 'y', $datemodifiedOneYear ) : $datemodifiedOneYear;
}
?>

<div class="pg_input_wrap" style="width: 20%; display: inline-block; ">
	<label>Page Expiration Date</label><br />
	<input value="<?php echo $this->model->page_expire; ?>" type="text" name="_page_expire" style="width: 45%;" />
</div>
<p></p>
<div align="left"><b>Modified:</b> <?php the_modified_date('F j, Y'); ?></div>

</div>
<?php 


//$todayDate = date("Y-m-d");// current date

//if (strtotime($todayDate) > $datemodifiedOneYear) {
//	echo "This needs updating. It has old information. ";
//	$site_url = get_bloginfo('wpurl');
//	$to      = get_bloginfo('admin_email');
//	$subject = "Page has expired: ";
//	$message = 'Page has expired!!!. Fix it.';
//	wp_mail($to, $subject, $message);
//}
 



?>


 <?php } ?>