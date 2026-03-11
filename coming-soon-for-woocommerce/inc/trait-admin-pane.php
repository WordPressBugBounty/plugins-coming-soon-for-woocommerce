<?php
/**
 * Admin config pane 
 *
 * @package Coming Soon Badges for WooCommerce
 * @version 1.1.1
 */
 
defined( 'ABSPATH' ) || exit;

trait Coming_Soon_WC_Admin_Pane
{
	/**
	 * Trait constructor called from main class
	 *
	 * @since 1.0
	 */
	function contruct_admin_pane_trait()
	{
		// Admin-side interface: configuration
		add_action( 'admin_menu', array ($this, 'admin_submenu'), 80 );
	}

	/**
	 * Add submenu link on the WooCommerce admin menu option
	 *
	 * @since 1.0.0
	 *
	 */
	function admin_submenu() {
		add_submenu_page( 'woocommerce', esc_html__('Coming Soon for WooCommerce configuration', 'coming-soon-for-woocommerce'), esc_html__('Coming Soon Badges', 'coming-soon-for-woocommerce'), 'manage_options', 'coming-soon-wc-opts', array ($this, 'admin_pane'), 20 );
	}

	/**
	 * The options pane plugin
	 *
	 * @since 1.0.0
	 *
	 */
	function admin_pane()
	{
		?>
		<div class="wrap">
		<?php

		$prefs_ok_save = false;

		$tab = 'loop';
		if (isset($_GET['tab']) && $_GET['tab'] == 'product') $tab = 'product';
		if (isset($_GET['tab']) && $_GET['tab'] == 'rate-us') $tab = 'rate-us';

		if ( isset( $_POST['coming_soon_wc_action'] ) && $_POST['coming_soon_wc_action'] == 'save_opts' 
			 && check_admin_referer( 'save_opts', 'coming_soon_wc_opts' . $tab ) ) {


			$current_options = $this->get_options();


			// sanitize & save loop badge options
			if ( isset( $_POST['badge_loop_style'] ) ) {

				// use only slug chars to sanitize the loop style option
				$current_options['badge_'.$tab.'_style'] = sanitize_title( wp_unslash($_POST['badge_loop_style']) );
				
				// strip all tags to sanitize text
				$current_options['badge_'.$tab.'_text'] = wp_kses( isset($_POST['coming-soon-wc_text']) ? wp_unslash($_POST['coming-soon-wc_text']) : __("COMING SOON", 'coming-soon-for-woocommerce'), 'strip' );

				// loop all badge options, and sanitize with only slug tags (colors will lose prefix # )
				$loop_badge = array ( 'font-size', 'font-weight', 'color', 'background', 'width', 'height', 
									  'padding-top', 'padding-bottom', 'padding-left', 'padding-right', 
									  'align-hor', 'margin-hor', 'align-ver', 'margin-ver', 'border-radius',
									  'background-size', 'custom-img');
				
				foreach ( $loop_badge as $field ) {
					
					if ( isset( $_POST[ 'coming-soon-wc_' . $field ] ) ) {
						$current_options['badge_'.$tab.'_opts'][$field] = sanitize_title(wp_unslash($_POST[ 'coming-soon-wc_' . $field ]));
					}
				}
				
				// Checkbox can be set or unset
				$current_options['badge_'.$tab.'_opts']['custom_css'] = isset ( $_POST['coming-soon-wc_custom_css'] ) ? '1' : '';

				// Media ID
				$current_options['badge_'.$tab.'_opts']['custom-img'] = isset ( $_POST['coming-soon-wc_custom-img'] ) ? intval ( $_POST['coming-soon-wc_custom-img'] ) : '';

				$prefs_ok_save = true;
			}

		}

		if ($prefs_ok_save) {

			$this->set_options( $current_options );

			?>
			<div class="updated below-h2">
				<p><?php esc_html_e('Your preferences has been saved.', 'coming-soon-for-woocommerce'); ?></p>
			</div>
			<?php
		}

		$current_options = $this->get_options();
		?>
			<h1 class="wp-heading-inline"><?php esc_html_e("Coming Soon Badges for WooCommerce configuration", 'coming-soon-for-woocommerce'); ?></h1>
			<nav class="nav-tab-wrapper woo-nav-tab-wrapper">
				<a href="<?php echo esc_url( admin_url('admin.php?page=coming-soon-wc-opts') ); ?>" class="nav-tab <?php echo esc_attr( $tab == 'loop' ? 'nav-tab-active' : ''); ?>"><?php esc_html_e("Badge on loop", 'coming-soon-for-woocommerce'); ?></a>
				<a href="<?php echo esc_url( admin_url('admin.php?page=coming-soon-wc-opts&tab=product') ); ?>" class="nav-tab <?php echo esc_attr( $tab == 'product' ? 'nav-tab-active' : ''); ?>"><?php esc_html_e("Badge on product page", 'coming-soon-for-woocommerce'); ?></a>
				<a href="<?php echo esc_url( admin_url('admin.php?page=coming-soon-wc-opts&tab=rate-us') ); ?>" class="nav-tab <?php echo esc_attr( $tab == 'rate-us' ? 'nav-tab-active' : ''); ?>"><span class="cswc-tab-icons"><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span></span></a>
			</nav>
			<?php 
			if ( $tab == 'loop' || $tab == 'product') {
				?>
				<form method="post" action="<?php echo esc_url( admin_url('admin.php?page=coming-soon-wc-opts' . ($tab == 'product' ? '&tab=product' : '') ) ); ?>" novalidate="novalidate">
					<?php wp_nonce_field( 'save_opts','coming_soon_wc_opts' . $tab ); ?>
					<input type="hidden" name="coming_soon_wc_action" value="save_opts" />
					<?php				
					if ( $tab == 'loop' ) { 
						?>
						<h2 class="title"><?php esc_html_e("Badge on loop", 'coming-soon-for-woocommerce'); ?></h2>
						<p><?php esc_html_e("Here you can set the badge over coming soon products on loops: shop homepage, categories, tags, results, etc.", 'coming-soon-for-woocommerce'); ?></p>
						<?php
					} elseif ( $tab == 'product' ) { 
						?>
						<h2 class="title"><?php esc_html_e("Badge on product page", 'coming-soon-for-woocommerce'); ?></h2>
						<p><?php esc_html_e("Here you can set the badge over the product image on the product page.", 'coming-soon-for-woocommerce'); ?></p>
						<?php 
					} 
					?>
					<table class="form-table" role="presentation">
						<tbody>
							<tr id="coming_soon_wc_loop_icon_style">
								<th scope="row"><label for="taxonomy"><?php esc_html_e("Badge style", 'coming-soon-for-woocommerce'); ?></label></th>
								<td>
									<div class="coming-soon-wc-columns">
										<div class="col">
											<div class="badge_sel_preview">
												<span class="coming-soon-wc-preview circle"><?php esc_html_e("CIRCLE TEXT", 'coming-soon-for-woocommerce'); ?></span>
											</div>
											<fieldset><input type="radio" name="badge_loop_style" value="circle-text" <?php if ($current_options['badge_'.$tab.'_style'] == 'circle-text') echo 'checked'; ?> /></fieldset>
										</div>
										<div class="col">
											<div class="badge_sel_preview">
												<span class="coming-soon-wc-preview square"><?php esc_html_e("SQUARE TEXT", 'coming-soon-for-woocommerce'); ?></span>
											</div>
											<fieldset><input type="radio" name="badge_loop_style" value="square-text" <?php if ($current_options['badge_'.$tab.'_style'] == 'square-text') echo 'checked'; ?> /></fieldset>
										</div>
										<div class="col">
											<div class="badge_sel_preview">
												<span class="coming-soon-wc-preview long"><?php esc_html_e("LINE TEXT", 'coming-soon-for-woocommerce'); ?></span>
											</div>
											<fieldset><input type="radio" name="badge_loop_style" value="line-text" <?php if ($current_options['badge_'.$tab.'_style'] == 'line-text') echo 'checked'; ?> /></fieldset>
										</div>
										<?php
										for ($i=1; $i < 9; $i++) {
											?>
											<div class="col">
												<div class="badge_sel_preview">
													<span class="coming-soon-wc-preview"><img src="<?php echo esc_url( COMING_SOON_WC_URL . "assets/img/coming_soon_$i.png" ); ?>" width="250" height="250" /></span>
												</div>
												<fieldset><input type="radio" name="badge_loop_style" value="<?php echo esc_attr("image-$i"); ?>" <?php if ($current_options['badge_'.$tab.'_style'] == 'image-'.$i) echo esc_attr('checked'); ?> /></fieldset>
											</div>
											<?php
										}
										?>
										<div class="col">
											<div class="badge_sel_preview has_icon">
												<span class="dashicons dashicons-format-image"></span>
												<span class="coming-soon-wc-preview long"><?php esc_html_e("CUSTOM IMAGE", 'coming-soon-for-woocommerce'); ?></span>
											</div>
											<fieldset><input type="radio" name="badge_loop_style" value="custom-image" <?php if ($current_options['badge_'.$tab.'_style'] == 'custom-image') echo 'checked'; ?> /></fieldset>
										</div>
										<div class="col">
											<div class="badge_sel_preview">
												<span class="coming-soon-wc-preview off "><strong><?php esc_html_e("OFF", 'coming-soon-for-woocommerce'); ?></strong><br /><?php esc_html_e("(NO BADGE)", 'coming-soon-for-woocommerce'); ?></span>
											</div>
											<fieldset><input type="radio" name="badge_loop_style" value="off" <?php if ($current_options['badge_'.$tab.'_style'] == 'off') echo 'checked'; ?> /></fieldset>
										</div>
									</div>
								</td>
							</tr>
						</tbody>
					</table>
					<div id="coming_soon_wc_preview_row">
						<div id="coming_soon_wc_options_col">
							<table class="form-table" id="coming_soon_wc_text" role="presentation" style="width:auto">
								<tbody>
									<tr>
										<th scope="row"><label for="taxonomy"><?php esc_html_e("Text:", 'coming-soon-for-woocommerce'); ?></label></th>
										<td><input type="text" name="coming-soon-wc_text" placeholder="<?php esc_attr_e("COMING SOON", 'coming-soon-for-woocommerce'); ?>" value="<?php echo esc_attr($current_options['badge_'.$tab.'_text']); ?>" /></td>
									</tr>
								</tbody>
							</table>
							<table class="form-table" id="coming_soon_wc_styles" role="presentation">
								<tbody>
									<?php 
									$bl_opts = $current_options['badge_'.$tab.'_opts'];
									?>
									<tr class="coming_soon_wc_only_text">
										<th scope="row"><label for="coming-soon-wc_font-size"><?php esc_html_e("Font size:", 'coming-soon-for-woocommerce'); ?></label></th>
										<td><input name="coming-soon-wc_font-size" type="number" step="1" min="1" value="<?php echo esc_attr($bl_opts['font-size']); ?>" class="small-text" data-unit="px" placeholder="<?php echo $tab == 'product' ? '28' : '14'; ?>"> px</td>
										<th scope="row"><label for="coming-soon-wc_font-weight"><?php esc_html_e("Font weight:", 'coming-soon-for-woocommerce'); ?></label></th>
										<td><input name="coming-soon-wc_font-weight" type="number" step="100" min="100" value="<?php echo esc_attr($bl_opts['font-weight']); ?>" class="small-text" placeholder="600"></td>
									</tr>
									<tr class="coming_soon_wc_only_text">
										<th scope="row"><label for="coming-soon-wc_color"><?php esc_html_e("Text color:", 'coming-soon-for-woocommerce'); ?></label></th>
										<td class="coming-soon-wc-second_col"><input type="text" name="coming-soon-wc_color" value="#<?php echo esc_attr($bl_opts['color']); ?>" class="coming-soon-wc-color-picker" data-default="#FFFFFF" /></td>
										<th scope="row"><label for="coming-soon-wc_background"><?php esc_html_e("Background color:", 'coming-soon-for-woocommerce'); ?></label></th>
										<td><input type="text" name="coming-soon-wc_background" value="#<?php echo esc_attr($bl_opts['background']); ?>" class="coming-soon-wc-color-picker" data-default="#555555" /></td>
									</tr>
									<tr class="coming_soon_wc_only_text">
										<th scope="row"><label for="coming-soon-wc_width"><?php esc_html_e("Width:", 'coming-soon-for-woocommerce'); ?></label></th>
										<td><input name="coming-soon-wc_width" type="number" step="1" min="1" value="<?php echo esc_attr($bl_opts['width']); ?>" class="small-text" data-unit="px" placeholder="<?php echo $tab == 'product' ? '140' : '70'; ?>"> px</td>
										<th scope="row" class="show_circle-text show_square-text hide_line-text"><label for="coming-soon-wc_height"><?php esc_html_e("Height:", 'coming-soon-for-woocommerce'); ?></label></th>
										<td class="show_circle-text show_square-text hide_line-text"><input name="coming-soon-wc_height" type="number" step="1" min="1" value="<?php echo esc_attr($bl_opts['height']); ?>" class="small-text" data-unit="px" placeholder="<?php echo $tab == 'product' ? '140' : '70'; ?>"> px</td>
									</tr>
									<tr class="coming_soon_wc_only_text">
										<th scope="row"><label for="coming-soon-wc_padding-top"><?php esc_html_e("Padding top:", 'coming-soon-for-woocommerce'); ?></label></th>
										<td><input name="coming-soon-wc_padding-top" type="number" step="1" min="0" value="<?php echo esc_attr($bl_opts['padding-top']); ?>" class="small-text" data-unit="px" placeholder="5"> px</td>
										<th scope="row"><label for="coming-soon-wc_padding-bottom"><?php esc_html_e("Padding bottom:", 'coming-soon-for-woocommerce'); ?></label></th>
										<td><input name="coming-soon-wc_padding-bottom" type="number" step="1" min="0" value="<?php echo esc_attr($bl_opts['padding-bottom']); ?>" class="small-text" data-unit="px" placeholder="5"> px</td>
									</tr>
									<tr class="coming_soon_wc_only_text">
										<th scope="row"><label for="coming-soon-wc_padding-left"><?php esc_html_e("Padding left:", 'coming-soon-for-woocommerce'); ?></label></th>
										<td><input name="coming-soon-wc_padding-left" type="number" step="1" min="0" value="<?php echo esc_attr($bl_opts['padding-left']); ?>" class="small-text" data-unit="px" placeholder="5"> px</td>
										<th scope="row"><label for="coming-soon-wc_padding-right"><?php esc_html_e("Padding right:", 'coming-soon-for-woocommerce'); ?></label></th>
										<td><input name="coming-soon-wc_padding-right" type="number" step="1" min="0" value="<?php echo esc_attr($bl_opts['padding-right']); ?>" class="small-text" data-unit="px" placeholder="5"> px</td>
									</tr>
									<tr class="coming_soon_wc_only_image">
										<th scope="row"><label for="coming-soon-wc_background-size"><?php esc_html_e("Background size:", 'coming-soon-for-woocommerce'); ?></label></th>
										<td><select name="coming-soon-wc_background-size">
											<option value="contain" <?php if ($bl_opts['background-size']=='contain') echo 'selected'; ?>>Contain</option>
											<option value="cover" <?php if ($bl_opts['background-size']=='cover') echo 'selected'; ?>>Cover</option>
											<option value="auto" <?php if ($bl_opts['background-size']=='auto') echo 'selected'; ?>><?php esc_html_e("Image size", 'coming-soon-for-woocommerce'); ?></option>
											<?php
											for ($s = 10; $s<=100; $s = $s+10) {
												echo '<option value="' . esc_attr($s) . '" ' . esc_attr( $bl_opts['background-size']==$s ? 'selected' : '' ) . '>' . esc_html($s . '%') . '</option>';
											}
											for ($s = 125; $s<=250; $s = $s+25) {
												echo '<option value="' . esc_attr($s) . '" ' . esc_attr( $bl_opts['background-size']==$s ? 'selected' : '' ) . '>' . esc_html($s . '%') . '</option>';
											}
											?>
										</select></td>
									</tr>
									<tr>
										<th scope="row"><label for="coming-soon-wc_align-hor"><?php esc_html_e("Horizontal align:", 'coming-soon-for-woocommerce'); ?></label></th>
										<td><select name="coming-soon-wc_align-hor">
											<option value="left" <?php if ($bl_opts['align-hor']=='left') echo 'selected'; ?>><?php esc_html_e("Left", 'coming-soon-for-woocommerce'); ?></option>
											<option value="center" <?php if ($bl_opts['align-hor']=='center') echo 'selected'; ?>><?php esc_html_e("Center", 'coming-soon-for-woocommerce'); ?></option>
											<option value="right" <?php if ($bl_opts['align-hor']=='right') echo 'selected'; ?>><?php esc_html_e("Right", 'coming-soon-for-woocommerce'); ?></option>
										</select></td>
										<th scope="row" class="coming-soon-wc_margin_hor_field"><label for="coming-soon-wc_margin-hor"><?php esc_html_e("Horizontal margin:", 'coming-soon-for-woocommerce'); ?></label></th>
										<td class="coming-soon-wc_margin_hor_field"><input name="coming-soon-wc_margin-hor" type="number" step="1" value="<?php echo esc_attr($bl_opts['margin-hor']); ?>" class="small-text" placeholder="<?php echo $tab == 'product' ? '20' : '10'; ?>"> px</td>
									</tr>
									<tr>
										<th scope="row"><label for="coming-soon-wc_align-ver"><?php esc_html_e("Vertical align:", 'coming-soon-for-woocommerce'); ?></label></th>
										<td><select name="coming-soon-wc_align-ver">
											<option value="top" <?php if ($bl_opts['align-ver']=='top') echo 'selected'; ?>><?php esc_html_e("Top", 'coming-soon-for-woocommerce'); ?></option>
											<option value="middle" <?php if ($bl_opts['align-ver']=='middle') echo 'selected'; ?>><?php esc_html_e("Middle", 'coming-soon-for-woocommerce'); ?></option>
											<option value="bottom" <?php if ($bl_opts['align-ver']=='bottom') echo 'selected'; ?>><?php esc_html_e("Bottom", 'coming-soon-for-woocommerce'); ?></option>
										</select></td>
										<th scope="row" class="coming-soon-wc_margin_ver_field"><label for="coming-soon-wc_margin-ver"><?php esc_html_e("Vertical margin:", 'coming-soon-for-woocommerce'); ?></label></th>
										<td class="coming-soon-wc_margin_ver_field"><input name="coming-soon-wc_margin-ver" type="number" step="1" value="<?php echo esc_attr($bl_opts['margin-ver']); ?>" class="small-text" placeholder="<?php echo $tab == 'product' ? '20' : '10'; ?>"> px</td>
									</tr>
									<tr class="coming_soon_wc_only_text hide_circle-text show_square-text show_line-text">
										<th scope="row"><label for="coming-soon-wc_border-radius"><?php esc_html_e("Border radius:", 'coming-soon-for-woocommerce'); ?></label></th>
										<td><input name="coming-soon-wc_border-radius" type="number" step="1" min="0" value="<?php echo esc_attr($bl_opts['border-radius']); ?>" class="small-text" placeholder="<?php echo $tab == 'product' ? '10' : '5'; ?>" data-unit="px"> px</td>
									</tr>
									<tr>
										<td colspan="4">
											<p><input type="button" class="button" id="coming-soon-wc_reset_loop" value="<?php esc_html_e('Reset badge loop values', 'coming-soon-for-woocommerce'); ?>"></p>
										</td>
									</tr>
								</tbody>
							</table>
							<div id="coming-soon-wc_loop_custom_css">
								<p><input type="checkbox" name="coming-soon-wc_custom_css" value="1" <?php if ($bl_opts['custom_css']=='1') echo 'checked'; ?>> <?php esc_html_e('Thanks, but I will create my own CSS styles', 'coming-soon-for-woocommerce'); ?></p>
								<div id="coming-soon-wc_css_helper">
									<p><?php esc_html_e('Use this classnames:', 'coming-soon-for-woocommerce'); ?></p>
									<code>
										.coming_soon_wc_loop_wrapper {<br />
										}<br />
										.coming_soon_wc_loop_wrapper .coming_soon_text {<br />
										}<br>
										.coming_soon_wc_loop_wrapper .coming_soon_img {<br />
										}<br />
										.coming_soon_wc_product_wrapper {<br />
										}<br>
										.coming_soon_wc_product_wrapper .coming_soon_text {<br />
										}<br />
										.coming_soon_wc_product_wrapper .coming_soon_img {<br />
										}
										</code>
								</div>
							</div>
							<table class="form-table" id="coming-soon-wc_loop_custom_image" role="presentation">
								<tbody>
									<tr>
										<th scope="row"><label for="taxonomy"><?php esc_html_e("Custom image", 'coming-soon-for-woocommerce'); ?></label></th>
										<td id="coming_soon_wc_img_loop_preview">
											<?php
											$image_id = intval ( $bl_opts['custom-img'] );
											if ( $image = wp_get_attachment_image_src( $image_id ) ) {
												echo '<img src="' . esc_url($image[0]) . '" />';
											}
											?>
										</td>
										<td>
											<a href="#" class="button" id="coming_soon_wc_img_upload_preview"><?php esc_html_e('Upload image', 'coming-soon-for-woocommerce'); ?></a>
											<a href="#" class="button" id="coming_soon_wc_img_remove_preview" <?php if( !$image ) echo 'style="display: none"'; ?>><?php esc_html_e('Remove image', 'coming-soon-for-woocommerce'); ?></a>
											<input type="hidden" name="coming-soon-wc_custom-img" value="<?php echo esc_attr($image_id); ?>">
										</td>
									</tr>
								</tbody>
							</table>
						</div>
						<div id="coming_soon_wc_preview_col">
							<div id="coming_soon_wc_loop_preview_wrap" class="tab-<?php echo esc_attr($tab); ?>">
								<div id="coming_soon_wc_loop_preview">
									<p><?php esc_attr_e("BADGE PREVIEW", 'coming-soon-for-woocommerce'); ?></p>
									<div class="badge_sel_preview">
										<span class="coming-soon-wc-img-preview"></span>
										<span class="coming-soon-wc-preview">...</span>
									</div>
								</div>
							</div>
						</div>
					</div>
					<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php esc_html_e('Save changes', 'coming-soon-for-woocommerce'); ?>"></p>
				</form>
				<?php
			} elseif ( $tab == 'rate-us' ) {
				
				require( COMING_SOON_WC_PATH . 'inc/cswc-five-stars.php');
			}
			?>
			<div class="cswc-discover">
				<?php require( COMING_SOON_WC_PATH . 'inc/cswc-discover.php'); ?>
			</div>
		</div>			
		<?php
	}
}
