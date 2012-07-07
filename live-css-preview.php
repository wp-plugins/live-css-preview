<?php
/*
Plugin Name: Live CSS Preview
Plugin URI: http://dojodigital.com
Description: Adds a textarea to the new Customize page that allows theme editors to write, preview & implement css code in real time.
Version: 1.0.0
Author: Randall Runnels
Author URI: http://dojodigital.com
*/
 
if ( !class_exists( 'DojoDigitalLiveCSSPreview' ) ) {
	
    class DojoDigitalLiveCSSPreview {
        
    	private $slug = 'dojodigital_live_css';
    	private $optionName; // String to use for option_name in options table
    	private $sectionSlug; 
    	private $optionFull; 
    	
    	
        function __construct(){
        	
        	global $wp_version;
        	
        	if ( $wp_version >= 3.4 ) {
        	
	        	$this->optionName = $this->slug . '_data';
	        	$this->optionFull = $this->optionName . '[' . $this->slug . ']';
	        	$this->sectionSlug = $this->slug . '_section';
	        	
				add_action( 'wp_head', array( $this, 'insert_placeholder' ), 2000 );
				add_action( 'customize_register', array( $this, 'register_field' ) );
				add_filter( 'body_class', array( $this, 'body_class_override' ) );
			
			} else {
				add_action( 'admin_notices', array( $this, 'insufficient_version_alert' ) );
			}
			
        } // __construct()
		
		
		/**
		 * Alert the admin that the version of WordPress is insufficient
		 * 
		 * return void
		 */
		 public function insufficient_version_alert(){
		 	echo '<div class="error">
			       <p><strong>Sorry!</strong> The Live CSS Preview plugin requires WordPress Version 3.4 or greater. Please upgrade you WordPress install or deactivate the plugin.</p>
			    </div>';
		 } // insufficient_version_alert()
		
		
        /**
		 * Inserts a style tag as a placeholder into wp_head.
		 *
		 * @access public
		 * @return void
		 */
		public function insert_placeholder(){
		
			echo '<style type="text/css" id="' . $this->slug . '">';
		
			$opt = get_option( $this->optionName );
			
			if( isset( $opt[ $this->slug ] ) ) echo $opt[ $this->slug ];
			
			echo '</style>';
		
		} // insert_placeholder()
		
		
		/**
		 * Registers the Customize API and builds the live css section & field.
		 *
		 * @param object $wp_customize
		 * @return void
		 */
		public function register_field( $wp_customize ) {
		
			// This must be called here! Any later and the WP_Customize_Control may not be available.
			require_once( 'wp-customize-ext.class.php' );
		
			$wp_customize->add_section( $this->sectionSlug, array(
				'title'       	=> __( 'Live CSS', $this->slug ),
				'priority'     	=> 160
			) );
							
			$wp_customize->add_setting( $this->optionFull, array(
				'default'        => '',
				'type'           => 'option',
				'capability'     => 'edit_theme_options',
				'transport'		=> 'postMessage'
			) );
		
			
			$wp_customize->add_control( new DDLCP_Customize_Textarea_Control( $wp_customize, $this->slug, array(
				'label'			=> __( 'CSS Code', $this->slug ),
				'section' 		=> $this->sectionSlug,
				'settings' 		=> $this->optionFull,
				'description'	=> '<strong>NOTE:</strong> the TAB key works differently in browsers! Use spaces instead.'
			) ) );
			
			
			if( $wp_customize->is_preview() && ! is_admin() ){
			
				add_action( 'customize_preview_init', array( $this, 'load_jquery' ) );
				add_action( 'wp_footer', array( $this, 'preview_scripts' ), 21 );
				
			}
			
		} // register_field() 		
		
		
		/**
		 * Prints out the javascript necessary to show previews in real time.
		 * @todo enqueue jquery 
		 *
		 * @access public
		 * @return void
		 */
		public function preview_scripts() { ?>
		
<script type="text/javascript">
( function( $ ){

	// Bind the Live CSS
	wp.customize('<?php echo $this->optionFull; ?>', function( value ) {
		value.bind(function(to) {
			$('#<?php echo $this->slug; ?>').html( to );
		});
	});
	
} )( jQuery )
</script>

		<?php } // preview_scripts()
		
		
		/**
		 * Make sure jQuery is running for the preview.
		 *
		 * @return void
		 */
		 function load_jquery(){
		 	
		 	wp_enqueue_script( 'jquery' );
					 
		 } // load_jquery()
		
		
		/**
		 * Adds the class "livecss" to the body tag to help overcome specicifity issues.
		 *
		 * @param array $classes
		 * @return array
		 */
		 public function body_class_override( $classes ){
		 	
		 	$classes[] = 'livecss';
		 	return $classes;
		 	
		 } // body_class_override()
      
      
    } // DojoDigitalLiveCSSPreview
    
    $DojoDigitalLiveCSSPreview = new DojoDigitalLiveCSSPreview();
    
} // !class_exists( 'DojoDigitalLiveCSSPreview' )
