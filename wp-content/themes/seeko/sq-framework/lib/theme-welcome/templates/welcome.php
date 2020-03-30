<div class="clear"></div>
<div class="theme-panel">

	<div class="cd-tabs">

		<div class="page-title d-flex justify-between">
			<div class="page-title-wrap">
				<?php echo esc_html( $this->config['theme_name'] ); ?>
				<div class="theme-version"><?php esc_html_e( "Version", 'seeko' ); ?> <?php echo esc_html( SVQ_THEME_VERSION ); ?></div>
			</div>
			<div class="title-image">
				<img width="100" src="<?php echo SVQ_PANEL_URI . '/assets/welcome.png'; ?>" alt="<?php esc_attr_e( 'Welcome image', 'seeko' ); ?>">
			</div>
		</div>

		<nav>
			<ul class="cd-tabs-navigation">
				<li><a data-content="welcome" class="selected" href="#welcome"><?php esc_html_e( 'Dashboard', 'seeko' ); ?></a></li>
				<li><a data-content="demo-data" href="#demo-data"><?php esc_html_e( 'Import Demo Data', 'seeko' ); ?></a></li>
				<li><a data-content="addons" href="#addons"><!--<span class="count-tab">3</span> --><?php esc_html_e( 'Theme Add-ons', 'seeko' ); ?></a></li>
				<li><a data-content="extras" href="#extras"> <?php esc_html_e( 'Status/Extras', 'seeko' ); ?></a></li>
			</ul> <!-- cd-tabs-navigation -->
		</nav>

		<ul class="cd-tabs-content">
			<li data-content="welcome" class="selected">

				<div class="sq-row">

					<div class="sq-col-6">
						<h2 class="sq-lead-title"><?php esc_html_e('Welcome to SEEKO theme', 'seeko' ); ?></h2>
						<hr>
						<div class="">
							<h3><?php esc_html_e('Initial setup steps', 'seeko' ); ?></h3>
							<ul>
								<li>
									<h4>
										<?php if ( is_child_theme() ) {
											echo '<span class="dashicons dashicons-yes" style="color: green;font-size: 22px;"></span>';
											esc_html_e( "Child theme is active, awesome.", "seeko" );
										} else {
											echo '<span class="dashicons dashicons-warning" style="color: orange;"></span>';
											esc_html_e( "Child theme is inactive. Enable it from Theme Add-ons tab.", "seeko" );
										}
										?>
									</h4>
									<?php esc_html_e( "It is a good practice to keep main theme files intact.", "seeko" ); ?>
								</li>
								<li>
									<h4><span class="dashicons dashicons-schedule"></span> Import demo content</h4>
									<?php esc_html_e( "Import pre-made layouts to get started right away.", "seeko" );?>
								</li>
								<li>
									<h4><span class="dashicons dashicons-store"></span> Activate theme Add-ons</h4>
									<?php esc_html_e( "Check out useful add-ons that work well with Seeko.", "seeko" );?>
								</li>

								<?php if ( class_exists('Envato_Market') ) : ?>

									<li>
										<h4><span class="dashicons dashicons-rss"></span> <?php esc_html_e( "Automatic Updates", "seeko" );?></h4>
										<?php echo wp_kses_post( __( "You can receive automatic updates from Themeforest, where our theme is exclusively sold.
										<br>Just enter your Marketplace token at the link below.", "seeko" ) );?>
										<br>
										<?php printf( '<a href="%s" target="_blank" class="button button-primary button-large">' . esc_html__( 'Setup updates', 'seeko' ) . '</a>', esc_url( admin_url('admin.php?page=envato-market' ) ) );?>

									</li>

								<?php else: ?>

									<li>
										<h4><span class="dashicons dashicons-rss"></span> <?php esc_html_e( "Automatic Updates are OFF", "seeko" );?></h4>
										<?php esc_html_e( "Automatic updates are disabled. Please enable Envato market plugin to receive automatic updates.", "seeko" );?>
										<br><?php printf( '<a href="%s" target="_blank" class="button button-primary button-large">' . esc_html__( 'Enable auto updates', 'seeko' ) . '</a>', esc_url( admin_url('admin.php?page=install-required-plugins') ) );?>
									</li>

								<?php endif; ?>

								<li><h4><span class="dashicons dashicons-admin-appearance"></span> Customize theme</h4>
									<a href="<?php echo esc_url( admin_url('customize.php') );?>" target="_blank">Click here</a>
									to change theme settings
								</li>
							</ul>
							<br>
							<h3>Useful links:</h3>
							<ul>
								<li><a href="https://my.seventhqueen.com/" target="_blank">Support site</a></li>
								<li><a href="https://my.seventhqueen.com/docs/seeko/welcome" target="_blank">Documentation</a></li>
								<li><a href="https://www.youtube.com/watch?v=e1fLpukxW5s&list=PLLLQcfCN3GmOcITcMDOdx_IC8VKizIekD" target="_blank">Youtube channel</a></li>
								<li><a href="https://www.facebook.com/groups/seekowp/" target="_blank">Facebook Community</a></li>
							</ul>
						</div>

					</div>

					<div class="sq-col-6">
						<h2 class="sq-lead-title"><?php esc_html_e( 'Theme License Activation', 'seeko' ); ?></h2>
						<hr>
						<?php if ( ! SVQ_FW::instance()->is_active() ) : ?>
						<p>Register your license on this site to automatically install and update theme add-ons theme add-ons.</p>

						<p><strong>Get done with it in two easy steps:</strong></p>
						<ul class="decimal-style">
							<li>Enter your Purchase code from Themeforest.
								<a target="_blank" href="https://my.seventhqueen.com/docs/seeko/find-theme-purchase-code">
									How to get it?
								</a>
							</li>
							<li>Hit REGISTER button.</li>
						</ul>

						<?php else : ?>
						 <h3>
							 <span class="sq-sstatus-col-icon dashicons-before dashicons-yes"></span>
							 Congratulations, theme license is active on your site!
						 </h3>
						<?php endif; ?>

						<?php

						$tf_code = get_option( 'envato_purchase_code_' . SQ_Panel::getInstance()->config['item_id'], '' );
						?>
						<form action="<?php echo esc_url( admin_url('themes.php?page=' . SQ_Panel::getInstance()->config['slug'] ) ); ?>#registration" class="sq-panel-register-form">
							<div class="sq-panel-form-field">
								<label for="tf_purchase_code">Themeforest Purchase Code</label>
								<input type="password" id="tf_purchase_code" name="tf_purchase_code"
								       class="sq-panel-register-form-api"
								       value="<?php echo esc_attr( $tf_code ); ?>"
								       placeholder="Purchase Code">
							</div>

							<div class="response-area hidden"></div>

							<?php wp_nonce_field( 'sq_theme_registration', 'sq_nonce' ); ?>
							<input type="submit" class="sq-panel-register-form-submit sq-panel-action sq-action-green sq-action-md" value="REGISTER">
						</form>

					</div>
				</div>


			</li>

			<li data-content="addons">

				<?php if ( SVQ_FW::instance()->is_active() ) : ?>
				<h3 class="sq-lead-title">Install Add-ons</h3>
				<div class="wq-lead-text">
					<p>Enhance your site with our recommended extensions. They are not mandatory and you can use any
						plugins you like</p>
				</div>

				<div class="sq-extensions-list">
					<?php
					foreach ( SQ_Addons_Manager()->plugins as $plugin ) : ?>

						<?php
						$plugin_status = SQ_Addons_Manager()->get_plugin_status( $plugin['slug'] );

						$button = '<a class="sq-extension-button"' .
						          ' data-action="' . esc_attr( $plugin_status['action'] ) . '"' .
						          ' data-status="' . esc_attr( $plugin_status['status'] ) . '"' .
						          ' data-nonce="' . wp_create_nonce( 'sq_plugins_nonce' ) . '"' .
						          ' href="#"' .
						          ' data-slug="' . esc_attr( $plugin['slug'] ) . '">' .
						          esc_html( $plugin_status['action_text'] ) .
						          '</a>';
						?>
						<div class="sq-extension <?php echo esc_attr( $plugin_status['status'] ); ?>"
						     id="ext-<?php echo esc_attr( $plugin['slug'] ); ?>">
							<div class="sq-extension-inner">
								<div class="sq-extension-info">
									<h4 class="sq-extension-title"><?php echo esc_html( $plugin['name'] ); ?></h4>
									<span
										class="sq-extension-status"><?php echo esc_html( $plugin_status['status_text'] ); ?></span>
									<p class="sq-extension-desc"><?php echo wp_kses_data( isset( $plugin['description'] ) ? $plugin['description'] : '' ); ?></p>
									<p class="sq-extension-extra">
										<cite><?php echo esc_html( ( isset( $plugin['required'] ) && $plugin['required'] == true ) ? 'REQUIRED' : 'Optional' ); ?></cite>
									</p>
									<p class="sq-extension-ajax-text"></p>
								</div>
								<div class="sq-extension-actions">
									<?php
									echo $button; // WPCS: XSS OK.
									?>
								</div>
							</div>

						</div>
					<?php endforeach; ?>
				</div>

				<?php else: ?>
					<h3>You need to enter your license key in order to proceed!.</h3>
					<p>Demo data import and add-ons are provided only to registered clients!</p>
					<p>Don't have a license? <a target="_blank" href="<?php echo esc_url( SQ_Panel::getInstance()->config['purchase_link'] ); ?>">GET Seeko</a></p>

				<?php endif; ?>

			</li>

			<li data-content="demo-data">

				<?php
				$kleoImport = SVQImport::getInstance();
				$kleoImport->show_message();

				if ( SVQ_FW::instance()->is_active() ) : ?>
					<h3>Importing demo content is easy.</h3>
					<p>Our special Theme Core Plugin will be installed when proceeding with the import. The process is automatic, relax ;)</p>
					<form class="kleo-import-form" action="" method="post" onSubmit="if(!confirm('Really import the data?')){return false;}">

						<input type="hidden" name="kleo_import_nonce" value="<?php echo wp_create_nonce( 'import_nonce' ); ?>" />
						<?php
						$kleoImport->generate_boxes_html();
						?>
					</form>

				<?php else :?>

					<h3>You need to enter your license key in order to proceed!.</h3>
					<p>Demo data import and add-ons are provided only to registered clients!</p>
					<p>Don't have a license? <a target="_blank" href="<?php echo esc_url( SQ_Panel::getInstance()->config['purchase_link'] ); ?>">Get Seeko</a></p>

				<?php endif; ?>

				
			</li>

			<li data-content="extras">
				<div class="sq-row">
					<div class="sq-col-12">

						<h3>Server status:</h3>
						<div class="sq-sstatus-wrapper">

							<?php
							$statuses = array();

							//writable directory
							global $kleo_config;
							$icon = 'dashicons-yes';
							$color_class = 'sq-sstatus-ok';
							$message = 'Uploads folder is writable';

							if ( ! is_writable( trailingslashit( SVQ_FW::get_config( 'upload_dir_path' ) ) ) ) {
								$icon = 'dashicons-warning';
								$color_class = 'sq-sstatus-notok';
								$message = 'Uploads folder is not writable. Please check with your hosting provider.';
							}

							$statuses[] = array(
								'name' => 'File permissions',
								'title' => 'Checks if you can write the uploads folder',
								'icon' => $icon,
								'color_class' => $color_class,
								'message' => $message

							);

							//php version
							$icon = 'dashicons-yes';
							$color_class = 'sq-sstatus-ok';
							$php_version = phpversion();
							$message = 'v. ' . $php_version;

							if ( version_compare($php_version, '5.3', '<') ) {
								$icon = 'dashicons-warning';
								$color_class = 'sq-sstatus-notok';
								$message = ' - You are using an outdated PHP version. A version greater than 5.6 is recommended.';
							}

							$statuses[] = array(
								'name' => 'PHP version',
								'title' => 'Server PHP version',
								'icon' => $icon,
								'color_class' => $color_class,
								'message' => $message

							);


							//Memory limit
							$icon = 'dashicons-yes';
							$color_class = 'sq-sstatus-ok';
							$memory = wp_convert_hr_to_bytes( ini_get('memory_limit') );
							$message = size_format( $memory );

							if ( $memory < 64000000 ) {
								$icon = 'dashicons-warning';
								$color_class = 'sq-sstatus-notok';
								$message .= ' - We recommend setting memory at <strong>128MB</strong>. <br /> To import all demo sample data, <strong>256MB</strong> of memory limit is required. <br /> See how to <a href="http://seventhqueen.com/blog/code-snippets/increase-php-memory-limit-in-wordpress.html" target="_blank">increase memory allocated to PHP.</a>';
							}

							$statuses[] = array(
								'name' => 'PHP Memory limit',
								'title' => 'The maximum amount of memory (RAM) that your site can use at one time',
								'icon' => $icon,
								'color_class' => $color_class,
								'message' => $message

							);


							//max execution time
							$message = '';
							$icon = 'dashicons-yes';
							$color_class = 'sq-sstatus-ok';

							$time_limit = @ini_get('max_execution_time');
							$message = $time_limit;

							if ( $time_limit < 180 && $time_limit != 0 ) {
								$icon = 'dashicons-warning';
								$color_class = 'sq-sstatus-notok';
								$message .= ' - We recommend setting max execution time to at least 180 for importing the sample data. See: <a href="http://codex.wordpress.org/Common_WordPress_Errors#Maximum_execution_time_exceeded" target="_blank">Increasing max execution to PHP</a>';
							}

							$statuses[] = array(
								'name' => 'PHP Time limit',
								'title' => 'The amount of time (in seconds) that your site will spend on a single operation before timing out',
								'icon' => $icon,
								'color_class' => $color_class,
								'message' => $message
							);

							//max input vars
							$icon = 'dashicons-yes';
							$color_class = 'sq-sstatus-ok';
							$input_vars = ini_get('max_input_vars');

							$message = $input_vars;

							if ( $input_vars < 1000 ) {
								$icon = 'dashicons-warning';
								$color_class = 'sq-sstatus-notok';
								$message .= ' - Max input vars limitation will truncate POST data such as menus. See: Increasing max input vars limit. See more info here <a href="'.esc_url('http://sevenspark.com/docs/ubermenu-3/faqs/menu-item-limit').'" target="_blank">See increasing max input vars limit.</a>';
							}

							$statuses[] = array(
								'name' => 'PHP Max Input Vars',
								'title' => 'The maximum number of variables your server can use for a single function to avoid overloads',
								'icon' => $icon,
								'color_class' => $color_class,
								'message' => $message
							);

							//zipArchive
							$message = 'Installed';
							$icon = 'dashicons-yes';
							$color_class = 'sq-sstatus-ok';

							if ( ! class_exists('ZipArchive') ) {
								$icon = 'dashicons-warning';
								$color_class = 'sq-sstatus-notok';
								$message = 'Not installed - ZipArchive is required for importing content. Please contact your server administrator and ask them to enable it.';
							}

							$statuses[] = array(
								'name' => 'ZipArchive',
								'title' => 'ZipArchive is required for importing demos and WordPress content',
								'icon' => $icon,
								'color_class' => $color_class,
								'message' => $message
							);

							// WP DEBUG MODE
							$message = 'OK - DEBUG is OFF';
							$icon = 'dashicons-yes';
							$color_class = 'sq-sstatus-ok';

							if ( defined('WP_DEBUG') && WP_DEBUG === TRUE ) {
								$icon = 'dashicons-warning';
								$color_class = 'sq-sstatus-notok';
								$message = 'DEBUG is ON - We recommend disabling WordPress debugging on your live site.';
							}

							$statuses[] = array(
								'name' => 'WP Debug',
								'title' => 'Displays whether or not WordPress is in Debug Mode. We recommend disabling debug mode for a live site',
								'icon' => $icon,
								'color_class' => $color_class,
								'message' => $message
							);

							?>

							<?php foreach ( $statuses as $status ) : ?>

								<div class="sq-sstatus-row">
									<div class="sq-sstatus-col sq-sstatus-col-name"><?php echo esc_attr( $status['name'] ); ?></div>
									<div class="sq-sstatus-col"><span class="sq-sstatus-col-icon tooltip-me dashicons-before <?php echo esc_attr( $status['icon'] ); ?>" title="<?php echo esc_attr( $status['title'] );?>"></span></div>
									<div class="sq-sstatus-col sq-sstatus-col-value <?php echo esc_attr( $status['color_class'] ); ?>"><?php echo wp_kses_post( $status['message'] ); ?></div>
								</div>

							<?php endforeach; ?>

						</div>
					</div>
		</ul> <!-- cd-tabs-content -->
	</div> <!-- cd-tabs -->
	
</div>