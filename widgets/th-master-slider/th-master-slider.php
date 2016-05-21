<?php
/*
Widget Name: Themovation Master Slider
Description: Master Slider widget.
Author: Themovation
Author URI: themovation.com
*/

class Themovation_SO_WB_MasterSlider_Widget extends SiteOrigin_Widget {

	function __construct() {

		parent::__construct(

			'th-master-slider',

			__('Themovation Master Slider', 'themovation-widgets'),

			array(
				'description' => __('Master Slider widget.', 'themovation-widgets'),
				'help'        => '',
			),

			array(
			),

			array(
				'shortcode' => array(
					'type' => 'text',
					'label' => __('Form Shortcode', 'themovation-widgets'),
					'placeholder' => __('[add_shortcode_here]', 'themovation-widgets'),
				),
			),

			plugin_dir_path(__FILE__)
		);
	}

	function get_template_name($instance) {
		return '';
	}

	function get_style_name($instance) {
		return '';
	}
}
siteorigin_widget_register('th-master-slider', __FILE__, 'Themovation_SO_WB_MasterSlider_Widget');
