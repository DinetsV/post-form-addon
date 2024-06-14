<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

require_once( __DIR__ . '/../classes/post-form.php' );

class Elementor_Post_Form_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'post_form_widget';
	}

	public function get_title() {
		return esc_html__( 'Post Form', 'post-form-addon' );
	}

	public function get_icon() {
		return 'eicon-form-horizontal';
	}

	public function get_categories() {
		return [ 'basic' ];
	}

	public function get_keywords() {
		return [ 'post', 'form' ];
	}

	public function get_script_depends() {
		return [ 'tinymce-script', 'post-form-script' ];
	}

	public function get_style_depends() {
		return [ 'post-from-style' ];
	}

	protected function register_controls() {
		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Content', 'post-form-addon' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'show_categories',
			[
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label' => esc_html__( 'Show existing category in form', 'post-form-addon' ),
				'label_on' => esc_html__( 'Yes', 'post-form-addon' ),
				'label_off' => esc_html__( 'No', 'post-form-addon' ),
				'return_value' => 'true',
				'default' => 'true',
			]
		);
		$this->add_control(
			'title',
			[
				'type' => \Elementor\Controls_Manager::TEXT,
				'label' => esc_html__( 'Title', 'post-form-addon' ),
				'placeholder' => esc_html__( 'Enter form title', 'post-form-addon' ),
			]
		);
		$this->add_control(
			'description',
			[
				'type' => \Elementor\Controls_Manager::TEXTAREA,
				'label' => esc_html__( 'Description', 'post-form-addon' ),
				'placeholder' => esc_html__( 'Enter form description', 'post-form-addon' ),
			]
		);
		$this->add_control(
			'not_registered',
			[
				'type' => \Elementor\Controls_Manager::TEXTAREA,
				'label' => esc_html__( 'Information', 'post-form-addon' ),
				'placeholder' => esc_html__( 'Enter information for not register user', 'post-form-addon' ),
			]
		);
    }

	protected function render() {
		$settings = $this->get_settings_for_display();
        echo Post_Form::get_render_form($settings);
	}

	protected function content_template() {
		echo Post_Form::get_template_form();
	}
}
