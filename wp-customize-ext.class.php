<?php 

/**
 * Extends the WP_Customize_Control class to allow textfields
 * @todo double check this class whenever WP updates...
 */
 class DDLCP_Customize_Textarea_Control extends WP_Customize_Control {
 
	public $type = 'textarea';	
	
	public function __construct( $manager, $id, $args = array() ) {
	
		if( isset( $args['description'] ) ) { $this->description = $args['description']; }
		parent::__construct( $manager, $id, $args );
		
	}

	public function render_content() {
		?>
		<label>
			<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<?php if( isset( $this->description ) ) : ?>
				<p class="description"><?php echo $this->description; ?></p>
			<?php endif; ?>
			<textarea <?php $this->link(); ?> id="<?php echo $this->id; ?>" style="width:100%;min-height:200px;"><?php $this->value(); ?></textarea>
		</label>
		<?php
	}
	
} // DDLCP_Customize_Textarea_Control()