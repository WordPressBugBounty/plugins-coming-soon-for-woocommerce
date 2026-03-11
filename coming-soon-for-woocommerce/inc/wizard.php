<?php
/**
 * Wizard to guide new users, 5 star rating stuff and news/notices system
 *
 * @package Coming Soon Badges for WooCommerce
 * @since 1.0.8
 * @version 1.1.1
 */

defined( 'ABSPATH' ) || exit;

class Coming_Soon_WC_Wizard {
		
	public function __construct() {
		
		// Note: admin_init is too early for get_current_screen() !
		add_action( 'admin_enqueue_scripts', array ( $this, 'admin_init' ) );
	}
	
	function admin_init() {

		global $Coming_Soon_WC;

		// We should show now / later / hide wizard forever?
		if( wp_verify_nonce('_nonce', 'wc-coming-soon-wizard' ) )
		{
			if( isset($_GET['wc-coming-soon-wizard']) ) {
				
				$when = sanitize_key( $_GET['wc-coming-soon-wizard'] );
				
				if ( in_array( $when, array( 'now', 'later', 'off' ), true ) ) {
					$Coming_Soon_WC->update_wizard_opts('wizard', $when);
				}
			}
		}

		// We're on config page? hide wizard forever
		if( $Coming_Soon_WC->its_csw_page_options() ) {
			$Coming_Soon_WC->update_wizard_opts('wizard', 'off');
		};

		// We should show later / hide five stars forever? (failed AJAX)
		if( wp_verify_nonce('_nonce', 'wc-coming-soon-wizard' ) )
		{
			if (isset($_GET['wc-coming-soon-stars']) ) {

				$when = sanitize_key( $_GET['wc-coming-soon-stars'] );
				
				if ( in_array( $when, array( 'later', 'off' ), true ) ) {
					$Coming_Soon_WC->update_wizard_opts('five-stars', $when);
				}
			}
		}
		

		// Is wizard pending to show?
		if( $Coming_Soon_WC->get_option('show_wizard') < time() && version_compare($Coming_Soon_WC->get_option('first_version'), '1.0.8', '>=') )
		{

			add_action('admin_notices', array ($this, 'show_wizard') );
		}
		elseif ( $Coming_Soon_WC->get_option('five_stars') < time() )
		{
			// add_action('admin_notices', array ($this, 'five_stars') );
		}
	}

	function show_wizard() {

		global $Coming_Soon_WC;

		if( ! current_user_can('manage_options') || !$Coming_Soon_WC->is_wc() ) return;
		
		$nonce = wp_create_nonce('wc-coming-soon-wizard');

		echo '<div class="notice wc-coming-soon-wizard must wc-coming-soon-wizard-notice-0">'
			. '<h3>'. esc_html__('Welcome to Coming Soon Badge for WooCommerce:', 'coming-soon-for-woocommerce') . '</h3>'
			. '<p>' . esc_html__('Add easily a coming soon badges to WooCommerce products.', 'coming-soon-for-woocommerce') . '</p>'
		  . '<p><a href="' . esc_url( admin_url('admin.php?page=coming-soon-wc-opts') ) . '" class="button-primary">' . esc_html__('Configure now', 'coming-soon-for-woocommerce') . '</a> &nbsp;'
			. '<a href="' . esc_url( add_query_arg( array('wc-coming-soon-wizard' => 'later', '_nonce' => $nonce) ) ) . '" class="button" data-ajax="wizard" data-param="later" data-nonce="'.esc_attr($nonce).'">' . esc_html__('Remind later', 'coming-soon-for-woocommerce') . '</a> &nbsp;'
			. '<a href="' . esc_url( add_query_arg( array('wc-coming-soon-wizard' => 'off',   '_nonce' => $nonce) ) ) . '" class="button" data-ajax="wizard" data-param="off" data-nonce="'.esc_attr($nonce).'">' . esc_html__('Thanks, I know how to use it', 'coming-soon-for-woocommerce') . '</a></p>'
		  . '</div>';
	}

	function five_stars() {

		global $Coming_Soon_WC;

		if( ! current_user_can('manage_options') || !$Coming_Soon_WC->is_wc() ) return;

		$nonce = wp_create_nonce('wc-coming-soon-wizard');

		echo '<div class="notice wc-coming-soon-wizard wc-coming-soon-five-stars">'
			//. '<a class="notice-dismiss" href="#">' . esc_html__('Dismiss') . '</a>'
			. '<h3>'. esc_html__('Do you like Coming Soon Badge for WooCommerce?', 'coming-soon-for-woocommerce') . '</h3>'
			. '<p>' . esc_html__('We are very pleased that you by now have been using our plugin a few days.', 'coming-soon-for-woocommerce') . '</p><p>' . 
			wp_kses( __('Please, rate <strong>Coming Soon Badge for WooCommerce</strong> on WordPress repository, it will help us a lot :)', 'coming-soon-for-woocommerce'),
						 array('strong'=>array())
			) . '</p>'
			. '<p><a href="' . esc_url('https://wordpress.org/support/plugin/coming-soon-for-woocommerce/reviews/?#new-post') . '" class="button-primary" target="_blank" data-ajax="five-stars" data-param="off">' . esc_html__('Rate the plugin', 'coming-soon-for-woocommerce') . '</a> &nbsp;'
			  . '<a href="' . esc_url( add_query_arg( array('wc-coming-soon-stars' => 'later', '_nonce' => $nonce) ) ) . '" class="button" data-ajax="five-stars" data-param="later" data-nonce="'.esc_attr($nonce).'">' . esc_html__('Remind later', 'coming-soon-for-woocommerce') . '</a> &nbsp;'
			  . '<a href="' . esc_url( add_query_arg( array('wc-coming-soon-stars' => 'off',   '_nonce' => $nonce) ) ) . '" class="button" data-ajax="five-stars" data-param="off" data-nonce="'.esc_attr($nonce).'">' . esc_html__('Don\'t show again', 'coming-soon-for-woocommerce') . '</a>'

			  . '</p></div>';
	}

}

$Coming_Soon_WC_Wizard = new Coming_Soon_WC_Wizard();