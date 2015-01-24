<?php
/**
* Plugin Name: CAHNRSWP Page Settings
* Plugin URI:  http://cahnrs.wsu.edu/communications/
* Description: Adds Redirect, Short Title and Expiration date functions
* Version:     1.0.0.0
* Author:      CAHNRS Communications, Don Pierce
* Author URI:  http://cahnrs.wsu.edu/communications/
* License:     Copyright Washington State University
* License URI: http://copyright.wsu.edu
*/

class CAHNRSWP_PAGESET_Init {
	
	private static $instance = null;
	
	public static function get_instance(){
		
		if( null == self::$instance ) {
			
			self::$instance = new self;
			
		} 
		
		return self::$instance;
		
	} // end get_instance
	
	private function __construct(){
		
		define( 'CAHNRSWPPAGESETURL' , plugin_dir_url( __FILE__ ) ); // PLUGIN BASE URL
		
		define( 'CAHNRSWPPAGESETDIR' , plugin_dir_path( __FILE__ ) ); // DIRECTORY PATH
		
		add_action( 'edit_form_after_title', array( $this , 'cahnrswp_edit_form_after_title' ) );
		
		add_action( 'init', array( $this, 'cahnrswp_init' ), 1 );
		
		add_action( 'save_post', array( $this , 'cahnrswp_save_post' ) );
		
		add_action( 'template_redirect', array( $this , 'cahnrswp_template_redirect' ) );

	    add_filter('the_title', array( $this , 'cahnrswp_short_title' ) );

        add_filter('the_content', array( $this , 'cahnrswp_page_expire' ) );
		
		add_action('the_post', array( $this , 'cahnrswp_page_expire_email' ));
				
		add_filter('the_permalink', array( $this , 'cahnrswp_the_permalink' ) );
		
		
	} // end constructor
	
	public function cahnrswp_init(){
		
	} // end cahnrswp_init
	
	public function cahnrswp_edit_form_after_title(){ 
		
		global $post;
		
		$pageset_model = new CAHNRSWP_PAGESET_model(); 
		
		$pageset_model->set_pageset( $post->ID );
		
		$page_view = new CAHNRSWP_PAGESET_view( $this , $pageset_model );
		
		$page_view->output_editor();
				
	} // end add_editor_form
	
	
	public function cahnrswp_save_post( $post_id ){
		
		$pageset_model = new CAHNRSWP_PAGESET_model(); 
		
		$pageset_model->save_pageset( $post_id );
		
	} // end cahnrswp_save_post
	
	public function cahnrswp_template_redirect(){
		
		 global $post;
		 
		 if( 'page' == $post->post_type && is_singular() ){
			 
			 $meta = \get_post_meta( $post->ID , '_redirect_to' , true );
			 
			 if( $meta ){
				 
				 \wp_redirect( $meta , 302 );
				 
			 } // end if $meta
			 
		 } // end if post_type
		 
	 } // end cahnrswp_template_redirect
	 
	 public function cahnrswp_the_permalink( $link ){
		 
		 global $post;
		 
		 if( 'page' == $post->post_type ) {
			 
			 $meta = get_post_meta( $post->ID , '_redirect_to' , true );
			 
			 if( $meta ){
				 
				 $link = $meta;
				 
			 } // end if $meta
			 
		 } // end if post_type
		 
		 return $link;
		 
	 } // end cahnrswp_the_permalink
	 
	 	
	public function cahnrswp_short_title( $title ){
		
		 global $post;
		 
	if((( 'page' == $post->post_type ) or ( 'post' == $post->post_type )) AND in_the_loop()){
 
		     $meta = get_post_meta( $post->ID , '_short_title' , true );
	
			 if (( $meta != '')) {  
			    
				  $title = $meta;
				  
			 } 
			 else {
				$title = $post->post_title;
			 }
			 
			 // end if $meta
	
		 } // if post_type
		 
			 return $title; 
		 
	} // end cahnrswp_short_title
	
		public function cahnrswp_page_expire( $content ){
						
		 global $post;
		 		 
		 if(( 'page' == $post->post_type ) or ( 'post' == $post->post_type )){
			 
		    $meta = get_post_meta( $post->ID , '_page_expire' , true );
			 
		    $metadate = date("Y-m-d", (int)$meta);
			
            $todayDate = date("Y-m-d");// current date
		 
			 if ( strtotime($todayDate) > $meta ) {  
			    
				  $content = '<div style="clear:both; background-color:#cccccc; border:1px solid #000000; color:#000000; padding:7px; margin:0.5em auto 0.5em auto;">This page contains old information<p /></div>'.$content;	
				  
			 } 
			 else {
	
			    $content;
			 }
			 
			 // end if 
			 
		    return $content;
		
		 } // if post_type
		 
	} // end cahnrswp_page_expire
	
		public function cahnrswp_page_expire_email( ){
						
		 global $post;
		 		 
		 if(( 'page' == $post->post_type ) or ( 'post' == $post->post_type ) AND in_the_loop()){
			 
		     $meta = get_post_meta( $post->ID , '_page_expire' , true );
			 
	//	  echo "eMeta is :".$meta."<br />" ;
	
		    $emetadate = date("Y-m-d", (int)$meta);
			
            $etodayDate = date("Y-m-d");// current date
		 	
		 
			 if ( strtotime($etodayDate) > $meta ) {  
   		  
		//       echo "Expired page. Email will be sent!!!";
		  
			   $site_url = get_bloginfo('wpurl');
                $page_link = get_permalink();
                $to      = get_bloginfo('admin_email');
                $subject = "Page has expired on site: ".$site_url;
            	$message = "Page has expired at: ".$page_link;
             	wp_mail($to, $subject, $message);			
				  
			 } 
			 else {
	
	//		    echo "In no email sent.";
			 }
			 
			 // end if 
			 
		    return;
		
		 } // if post_type
		 
	} // end cahnrswp_page_expire_email
	
	
		
} // end class CAHNRSWP_PAGESET

class CAHNRSWP_PAGESET_Model {
	
	public $post_date;
	
	public $redirect;
	
	
	public function __construct(){
	}
	
	public function set_pageset( $post_id = false ) {
		
		$date = \get_post_meta( $post_id , '_post_date', true );
		
		$this->post_date = ( $date )? date( 'm', $date ).'/'.date( 'd', $date ).'/'.date( 'y', $date ) : $date;
		
		$this->redirect = \get_post_meta( $post_id , '_redirect_to', true );
		
		$this->short_title = \get_post_meta( $post_id , '_short_title', true );
		
		$edate = \get_post_meta( $post_id , '_page_expire', true );

		$this->page_expire = ( $edate )? date( 'm', $edate ).'/'.date( 'd', $edate ).'/'.date( 'y', $edate ) : $edate;
			
	} // end set pageset
	
	public function save_pageset( $post_id ){
		
		if ( ! isset( $_POST['pageset_nonce'] ) ) return;
		
		if ( ! wp_verify_nonce( $_POST['pageset_nonce'], 'submit_pageset' ) ) return;
		
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
		
		if ( ! current_user_can( 'edit_post', $post_id ) ) return;
		
		$fields = array(
			'_post_date'   => 'text',
			'_redirect_to' => 'text',
			'_short_title' => 'text',
			'_page_expire' => 'text',
		);
		
		foreach( $fields as $f_key => $f_data ){
			
			if( isset( $_POST[ $f_key ] ) ){
				
				$instance = sanitize_text_field( $_POST[ $f_key ] );
				if( '_page_expire' == $f_key ){ 
				
					$instance = strtotime( $instance );
					
				}
				
				update_post_meta( $post_id , $f_key , $instance );
				
			} // end if
			
		} // end foreach
		
	} // end save_pageset

} // end class CAHNRSWP_PAGESET_Model

class CAHNRSWP_PAGESET_View {
	
	private $control;
	private $model;
	public $view;
	
	public function __construct( $control , $model ){
		
		$this->control = $control;
		$this->model = $model;
		
	} // end __construct
	
	public function output_editor(){
					
		include CAHNRSWPPAGESETDIR . 'inc/editor.php';
		
		}
	
} // end class CAHNRSWP_PAGESET_View

$cahnrswp_PAGESET = CAHNRSWP_PAGESET_Init::get_instance();