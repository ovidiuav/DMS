<?php
/**
 * Plugin installer class
 *
 * Install PageLines plugins and looks after them.
 *
 * @since 2.0.b3
 */

 class PagelinesExtensions {
 	

 	function __construct() {

		add_action('admin_head', array(&$this, 'extension_js'));
		add_action('wp_ajax_pagelines_ajax_extension_install', array(&$this, 'extension_install'));
		add_action('wp_ajax_pagelines_ajax_extension_activate', array(&$this, 'extension_activate'));
		add_action('wp_ajax_pagelines_ajax_extension_deactivate', array(&$this, 'extension_deactivate'));
 	}


 	function extension_activate() {
 		$file =  $_POST['extend_url'];
 		activate_plugin( $file );
 		echo 'Activation complete! ';
 		die();
 	}

 	function extension_deactivate() {
 		$file =  $_POST['extend_url'];
 		deactivate_plugins( array($file) );
 		echo 'Deactivation complete! ';
 		die();
 	}


	function extension_themes() {
		return 'Fetch from api list of themes and show ajax buttons...';		
	}

	function extension_plugins() {

		plprint( pagelines_register_plugins(), 'pagelines_register_plugins');
		
		$api = wp_remote_get( 'http://api.pagelines.com/plugins/' );

		$plugins = json_decode( $api['body'] );

	
		if ( is_object($plugins) ) {
			$rn = 2;
			$count = $rn;
			$output = '';
			
			foreach( $plugins as $key => $plugin ) {
				
				$start_row = ($count % $rn == 0) ? true : false;
				$end_row = ( ($count+1) % $rn == 0 || $plugin == end($plugins)) ? true : false;
				$cl = ($end_row) ? 'pplast' : '';
			
				/**
				 * 
				 * Remember this form is a hack up!
				 * TODO here we need to check if our plugin is installed already, and change the form to ajax activate/deactivate
				 *
				 */
				$install_js_call = sprintf('onClick="extendInstall(\'%s\', \'%s\', \'%s\')"', $key, 'plugin', $plugin->url);
				$activate_js_call = sprintf('onClick="extendActivate(\'%s\', \'%s\', \'%s\')"', $key, 'plugin', $plugin->file);
				$deactivate_js_call = sprintf('onClick="extendDeactivate(\'%s\', \'%s\', \'%s\')"', $key, 'plugin', $plugin->file);

				switch ( $this->plugin_check_status( WP_PLUGIN_DIR . $plugin->file ) ) {

					case 'active':
						$button = OptEngine::superlink('Deactivate Plugin', '', '', '', $deactivate_js_call);
						break;
					
					case 'notactive':
						$button = OptEngine::superlink('Activate Plugin', '', '', '', $activate_js_call);
						break;
					
					default:
						// were not installed, show the form!
						$button = OptEngine::superlink('Install Plugin', '', '', '', $install_js_call);
						break;
						
				}
				
				// Output
				
				//if($start_row) $output .= sprintf('<div class="pprow">');
				
				$buttons = sprintf('<div class="pane-buttons">%s</div>', $button);
				
				$title = sprintf('<div class="pane-head"><div class="pane-head-pad"><h3 class="pane-title">%s</h3><div class="pane-sub">%s</div></div></div>', $plugin->name, 'Version ' . $plugin->version);
				
				$body = sprintf('<div class="pane-desc"><div class="pane-desc-pad">%s<div class="pane-dets">by <a href="%s">%s</a></div></div></div>', $plugin->text, $plugin->author_url, $plugin->author );	
				
				$output .= sprintf('<div class="plpane pane-plugin %s"><div class="plpane-hl fix"><div class="plpane-pad fix">%s %s %s</div></div></div>', $cl, $title, $body, $buttons);
				
				$output .= sprintf('<div id="response%s" class="install_response"><div class="rp"></div></div>', $key);
				
				//if($end_row) $output .= sprintf('</div>');
				$count++;
			}
		}
		return $output;
	}
	
	/**
	 * 
	 * Add Javascript to header (hook in contructor)
	 * 
	 */
	function extension_js(){ ?>
		
		<script type="text/javascript">/*<![CDATA[*/

		function extendInstall(key, type, url){
			
				var data = {
					action: 'pagelines_ajax_extension_install',
					extend_type: type,
					extend_url: url
				};

				var saveText = jQuery('#response'+key);
				jQuery.ajax({
					type: 'POST',
					url: ajaxurl,
					data: data,
					beforeSend: function(){

						saveText.html('Installing').slideDown();
						
						// add some dots while saving.
						interval = window.setInterval(function(){
							var text = saveText.text();
							if (text.length < 13){	saveText.text(text + '.'); }
							else { saveText.text('Installing'); } 
						}, 400);

					},
				  	success: function( response ){
					
						window.clearInterval(interval); // clear dots...
						
						saveText.html(response).delay(6500).slideUp();
						

					}
				});
			
		}

		function extendActivate(key, type, url){
			
				var data = {
					action: 'pagelines_ajax_extension_activate',
					extend_type: type,
					extend_url: url
				};

				var saveText = jQuery('#response'+key);
				jQuery.ajax({
					type: 'POST',
					url: ajaxurl,
					data: data,
					beforeSend: function(){

						saveText.html('Activating').slideDown();
						
						// add some dots while saving.
						interval = window.setInterval(function(){
							var text = saveText.text();
							if (text.length < 13){	saveText.text(text + '.'); }
							else { saveText.text('Activated'); } 
						}, 400);

					},
				  	success: function( response ){
					
						window.clearInterval(interval); // clear dots...
						
						saveText.html(response).delay(6500).slideUp();
						

					}
				});
			
		}

		function extendDeactivate(key, type, url){
			
				var data = {
					action: 'pagelines_ajax_extension_deactivate',
					extend_type: type,
					extend_url: url
				};

				var saveText = jQuery('#response'+key);
				jQuery.ajax({
					type: 'POST',
					url: ajaxurl,
					data: data,
					beforeSend: function(){

						saveText.html('Deactivating').slideDown();
						
						// add some dots while saving.
						interval = window.setInterval(function(){
							var text = saveText.text();
							if (text.length < 13){	saveText.text(text + '.'); }
							else { saveText.text('Deactivated'); } 
						}, 400);

					},
				  	success: function( response ){
					
						window.clearInterval(interval); // clear dots...
						
						saveText.html(response).delay(6500).slideUp();
						

					}
				});
			
		}
		/*]]>*/</script>
		
<?php }

	/**
	 * 
	 * Extension AJAX callback
	 * 
	 */
	function extension_install(  ) {
		
		// 1. Libraries
			include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		
		// 2. Variable Setup
			$type =  $_POST['extend_type'];
			$url =  $_POST['extend_url'];
		
		// 3. Do our thing...
			$upgrader = ( $type == 'theme' ) ? new Theme_Upgrader() : new Plugin_Upgrader();

			@$upgrader->install($url);
	
			if ( is_wp_error($upgrader->skin->result ) )
				$error = $upgrader->skin->result->get_error_message();
		
		// 4. Output
			$out = ( !isset($error) ) ? true : 'error'; // nothing needs to be returned, just echo'd
	
			die(); // needed at the end of ajax callbacks
	}

	function plugin_check_status( $file ) {
		
		if ( !file_exists( $file ) )
			return ;
			 
		if (in_array( str_replace( '.php', '', basename($file) ), pagelines_register_plugins() ) )
			return 'active';
		else
			return 'notactive';
	}

 } // end PagelinesExtensions class