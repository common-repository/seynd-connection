<div class="wrap main-seynd">
    <div class="divrow">
	<h1 class="wp-heading-inline"><?php echo esc_html( __( 'Seynd Connect', 'seynd_conn' ) ); ?></h1>
	<div class="seynd_notices"></div>
	<?php
	$seynd_connection = get_option('seynd_connection');
	if ($seynd_connection) {
	    //Already connected	    
	    $seynd_main_website_alias = get_option('seynd_main_website_alias');
	    $seynd_URLdomain = get_option('seynd_URLdomain');
	    $seynd_page_type = get_option('seynd_page_type');
	    ?>
	    <div class="card">
		<?php echo $seynd_selected_page; ?>
		<h2 class=""><?php echo esc_html( __( 'Connected to', 'seynd_conn' ) ); ?> "<i><?php echo wp_unslash($seynd_main_website_alias); ?></i>" (<?php echo $seynd_URLdomain; ?>)</h2> 
		<div class="seynd-div-buttons">
		    <button type="button" id="seynd-change-site" class="button"><?php echo esc_html( __( 'Change Site', 'seynd_conn' ) ); ?>
		    </button>
		    <button type="button" id="seynd-btn-disconnect" class="button-primary"><?php echo esc_html( __( 'Disconnect', 'seynd_conn' ) ); ?></button>
		</div>
		<div class="seynd-loader-connected-sites" style="display: none;"></div>

		<form id="seynd-connected-websitelist">
    		    <ul>
    			<li class="seynd-select-pages">			
    			    <label><input type="radio" name="optpage" <?php if ($seynd_page_type == 'all_page') {
		    echo 'checked';
		} ?> value="all_page"><?php echo esc_html( __( 'Full Website', 'seynd_conn' ) ); ?></label>
    			    <label><input type="radio" name="optpage" <?php if ($seynd_page_type == 'selected_page') {
		    echo 'checked';
		} ?> value="selected_page"><?php echo esc_html( __( 'Selected Pages', 'seynd_conn' ) ); ?></label>
    			</li>
    			<li <?php if ($seynd_page_type != 'selected_page') { echo 'style="display: none;"' ?> <?php } ?>class="seynd-connected-select_page_list">				
			    <div id="seyndConnectedEmptyPageDropdown" >Loading Pages...</div>				
    			</li>
    		    </ul>
    		    <span class="seynd-connected-error-msg"></span>
    		</form>
		<button type="button" id="seynd-connected-sites" class="button-primary btn-seyndajax">Save</button>
	    </div>

    	

	<?php
	} else {
	    //Not connected	    
	    ?>
	    <div class="card">
		<div class="div-confirmation">
		    <h2 class="title"><?php echo esc_html( __( 'Do you have Seynd account?', 'seynd_conn' ) ); ?></h2>
		    <span class="colarea">
			<button type="button" id="yes-seynd" class="button button-primary" title="yes"><?php echo esc_html( __( 'Yes', 'seynd_conn' ) ); ?></button>
		    </span>
		    <span class="colarea">
			<button type="button" id="no-seynd" class="button" title="no"><?php echo esc_html( __( 'No', 'seynd_conn' ) ); ?></button>
		    </span>
		</div>
		<div class="div-connect-option" style="display: none;">
		    <h2><?php echo esc_html( __( 'Connnect your Seynd account', 'seynd_conn' ) ); ?></h2>
		    <span class="colarea">
			<button type="button" id="seynd-connect" class="button-primary" title="connect">Connect</button>
		    </span>
		</div>	
	    </div>
<?php } ?>
    </div>	
</div>
<!-- Modal -->
<div id="seyndConnModal" Title="Seynd Connection" style="display: none;">
    <div id="seyndDialog" title="Dialog Form">
    <?php if($seynd_connection){
	    	$seynd_URLdomain = get_option('seynd_URLdomain');
	    	$seynd_token = get_option('seynd_token');
	    	$seynd_sites = get_option('seynd_sites_list');		    
    	?>
    <form id="seyndUpdateform" method="POST" autocomplete="off">
	    <ul>
	    	<li class="seynd-select-site">
			<label>Select your site: </label>
			<input type="hidden" name="seynd_token" value="<?php echo $seynd_token; ?>">
			<select id="seyndUpdateSitesDropdown" class="select">
				<?php foreach ($seynd_sites as $key => $site) {
					$sel_html = '';					
					if(esc_url_raw($site['url']) == $seynd_URLdomain){
						$sel_html = 'selected';
					}
					echo '<option value="'.$site['sitedomain']['name'].'" '.$sel_html.' data-url="'.$site['url'].'">'.wp_unslash($site['name']).'</option>';
				} ?>
			</select>
			<a href="javascript:void(0);" id="seynd_btnRefresh_site" alt="refresh sites">Syc Sites</a>
		    </li>
		<span class="seynd-error-msg"></span>
	    </ul>
	    <input class="button-primary btnUpdateSites" name="submit" type="submit" value="Save">
	    <div class="seynd-loader" style="display: none;"></div>
	</form>
	<?php }else{ ?>	
	<form id="seyndConnectionform" method="POST" autocomplete="off">
	    <ul>
		<li><label for="uname"><?php echo esc_html( __( 'Username', 'seynd_conn' ) ); ?><span> *</span>: </label>
		    <input required="required" name="username" type="text" class="input" autocomplete="off">
		</li>
		<li><label for="password"><?php echo esc_html( __( 'Password', 'seynd_conn' ) ); ?><span> *</span>: </label>
		    <input required="required" name="password" type="password" class="input" autocomplete="off">
		</li>
		<span class="seynd-error-msg"></span>
	    </ul>

	    <!--button type="button" id="sendNotifications" class="btn btn-primary btn-md hide">Connect</button-->
	    <input class="button-primary btn-seyndajax" name="submit" type="submit" value="Login">
	    <div class="seynd-loader" style="display: none;"></div>
	</form>
	<?php } ?>
	<div id="setSeyndSite" style="display: none;">
	    <form id="seynd-websitelist">
		<ul>
		    <li class="seynd-select-site">
		    <input type="hidden" id="seynd-token">
		    <input type="hidden" id="seynd-sites-list">
			<label><?php echo esc_html( __( 'Select your site:', 'seynd_conn' ) ); ?></label>
			<select id="seyndEmptyDropdown" class="select"></select>
		    </li>
		    <li class="seynd-select-pages">			
			<label><input type="radio" name="optpage" value="all_page" checked><?php echo esc_html( __( 'Full Website', 'seynd_conn' ) ); ?></label>
			<label><input type="radio" name="optpage" value="selected_page"><?php echo esc_html( __( 'Selected Pages', 'seynd_conn' ) ); ?></label>
		    </li>
		    <li class="select_page_list" style="display: none">				
			<div id="seyndEmptyPageDropdown"></div>
		    </li>		    
		    <span class="seynd-error-msg"></span>
		</ul>
	    </form>
	    <button type="button" id="btnSetSites" class="button-primary btn-seyndajax">Save</button>
	    <div class="seynd-loader" style="display: none;"></div>
	</div>

    </div>
</div>
<!-- Modal END-->

<!-- Modal -->
<div id="seyndConnNoAccountModal" Title="Seynd Connection" style="display: none;">
    <div id="dialog" title="Dialog Form">	 	
	<div class="seynd-logo"> <img src="<?php echo plugin_dir_url( dirname( __FILE__ ) ) . 'images/seynd-blue-logo.png'; ?>" alt="seynd logo"></div>
	<h3><?php echo esc_html( __( 'Easiest, Most Automated Web Push Notifications', 'seynd_conn' ) ); ?></h3>
	<p><?php echo esc_html( __( 'No complicated and confusing packages to choose from. ALL the features you’ll ever need or want in only ONE plan!', 'seynd_conn' ) ); ?></p>
	<div class="seynd-signup-free"> <button type="button" id="btn-no-seynd" class="button-primary"><?php echo esc_html( __( 'Sign Up FREE', 'seynd_conn' ) ); ?></button></div>
    </div>
</div>
<!-- Modal END-->