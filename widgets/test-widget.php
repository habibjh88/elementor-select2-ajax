<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor Test Widget.
 *
 * Elementor widget that uses the emojionearea control.
 *
 * @since 1.0.0
 */
class Elementor_Test_Widget extends \Elementor\Widget_Base {

	/**
	 * Get widget name.
	 *
	 * Retrieve test widget name.
	 *
	 * @return string Widget name.
	 * @since 1.0.0
	 * @access public
	 */
	public function get_name() {
		return 'test';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve test widget title.
	 *
	 * @return string Widget title.
	 * @since 1.0.0
	 * @access public
	 */
	public function get_title() {
		return esc_html__( 'Test', 'elementor-select2-ajax' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve test widget icon.
	 *
	 * @return string Widget icon.
	 * @since 1.0.0
	 * @access public
	 */
	public function get_icon() {
		return 'eicon-code';
	}

	/**
	 * Get custom help URL.
	 *
	 * Retrieve a URL where the user can get more information about the widget.
	 *
	 * @return string Widget help URL.
	 * @since 1.0.0
	 * @access public
	 */
	public function get_custom_help_url() {
		return 'https://developers.elementor.com/docs/widgets/';
	}

	/**
	 * Get widget categories.
	 *
	 * Retrieve the list of categories the test widget belongs to.
	 *
	 * @return array Widget categories.
	 * @since 1.0.0
	 * @access public
	 */
	public function get_categories() {
		return [ 'general' ];
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the test widget belongs to.
	 *
	 * @return array Widget keywords.
	 * @since 1.0.0
	 * @access public
	 */
	public function get_keywords() {
		return [ 'test', 'emoji' ];
	}

	function rt_category_list() {
		$all_post = get_posts( [
			'post_type'      => 'post',
			'posts_per_page' => - 1,
			'post_status'    => 'publish',
		] );
		$lists    = [];
		foreach ( $all_post as $p ) {
			$lists[ $p->ID ] = $p->post_title;
		}

		return $lists;
	}

	/**
	 * Register test widget controls.
	 *
	 * Add input fields to allow the user to customize the widget settings.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls() {

		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Content', 'elementor-select2-ajax' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'content',
			[
				'label' => esc_html__( 'Content with Emoji', 'elementor-select2-ajax' ),
				'type'  => 'emojionearea',

			]
		);

		$this->add_control(
			'list',
			[
				'label'       => esc_html__( 'Show Elements', 'elementor-select2-ajax' ),
				'type'        => \Elementor\Controls_Manager::SELECT2,
				'label_block' => true,
				'multiple'    => true,
				'options'     => [
					'title'       => esc_html__( 'Title', 'elementor-select2-ajax' ),
					'description' => esc_html__( 'Description', 'elementor-select2-ajax' ),
					'button'      => esc_html__( 'Button', 'elementor-select2-ajax' ),
				],
			]
		);

		//
//		$this->add_control(
//			'post_lists',
//			[
//				'label'       => __( 'Choose Posts', 'homlisti-core' ),
//				'type'        => \Elementor\Controls_Manager::SELECT2,
//				'multiple'    => true,
//				'options'     => $this->rt_category_list(),
//				'label_block' => true,
//			]
//		);




		$this->add_control(
			'rt_product_gallery_tag', [
				'label'       => __( 'Choose posts', 'textdomain' ),
				'type'        => 'rt-select2',
				'source_name' => 'post_type', //post_type, taxonomy, user
				'source_type' => 'post',
				'multiple'    => true,
			]
		);



		$this->end_controls_section();

	}

	/**
	 * Render test widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		echo $settings['content'];
		var_dump( $settings['rt_product_gallery_tag'] );
	}

}