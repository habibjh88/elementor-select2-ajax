<?php


// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class RTSelect2 extends \Elementor\Base_Data_Control {
	public function get_type() {
		return 'rt-select2';
	}

	public function enqueue() {
		wp_register_script( 'rt-select2', plugins_url( '/assets/js/rtajaxselect.js', dirname( __FILE__ ) ), [ 'jquery-elementor-select2' ], '1.0.0' );

		wp_localize_script(
			'rt-select2',
			'rtSelect2Obj',
			[
				'ajaxurl'     => esc_url( admin_url( 'admin-ajax.php' ) ),
				'search_text' => esc_html__( 'Search', 'textdomain' ),
			]
		);
		wp_enqueue_script( 'rt-select2' );
		?>
        <style>
            .rt-select2-main-wrapper ul.select2-selection__rendered li:last-child {
                pointer-events: none;
            }

            .rt-select2-main-wrapper ul.select2-selection__rendered li:last-child::before {
                background: var(--e-a-btn-bg);
                content: '+';
                padding: 1px 5px;
                position: relative;
                top: 1px;
                margin-right: 5px;
                color: #fff;
            }

            .rt-select2-main-wrapper ul.select2-selection__rendered li[title=search] {
                display: none;
            }
        </style>
		<?php
	}

	protected function get_default_settings() {
		return [
			'multiple'             => false,
			'label_block'          => true,
			'source_name'          => 'post_type',
			'source_type'          => 'post',
			'minimum_input_length' => 1,
		];
	}

	public function content_template() {
		$control_uid = $this->get_control_uid();
		?>

        <# var controlUID = '<?php echo esc_html( $control_uid ); ?>'; #>
        <# var currentID = elementor.panel.currentView.currentPageView.model.attributes.settings.attributes[data.name]; #>
        <div class="elementor-control-field rt-select2-main-wrapper">
            <# if ( data.label ) { #>
            <label for="<?php echo esc_attr( $control_uid ); ?>" class="elementor-control-title">{{{data.label }}}</label>
            <# } #>
            <div class="elementor-control-input-wrapper elementor-control-unit-5">
                <# var multiple = ( data.multiple ) ? 'multiple' : ''; #>
                <select id="<?php echo esc_attr( $control_uid ); ?>" {{ multiple }} class="rt-select2" data-setting="{{ data.name }}"></select>
            </div>
        </div>
        <#
        ( function( $ ) {
        $( document.body ).trigger( 'rt_select2_event',{currentID:data.controlValue,data:data,controlUID:controlUID,multiple:data.multiple} );
        }( jQuery ) );
        #>
		<?php
	}
}
