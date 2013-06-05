<?php

/*
 * User Role Editor plugin: advertisement showing class
 * Author: Vladimir Garagulya
 * email: vladimir@shinephp.com
 * site: http://shinephp.com
 * 
 */

class ure_Advertisement {
	
	private $slots = array(0=>'', 1=>'', 2=>'');
				
	function __construct() {
		
		$used = array(-1);
		$index = $this->rand_unique( $used );		
		$this->slots[$index] = $this->admin_menu_editor();
		
		$used[] = $index;
		$index = $this->rand_unique( $used );
		$this->slots[$index] = $this->manage_wp();
		
		$used[] = $index;
		$index = $this->rand_unique( $used );
		$this->slots[$index] = $this->video_user_manuals();
		
	}
	// end of __construct
	
	
	/**
	 * Returns random number not included into input array
	 * 
	 * @param array $used - array of numbers used already
	 * 
	 * @return int
	 */
	private function rand_unique( $used = array(-1) ) {
		$index = rand(0, 2);
		while (in_array($index, $used)) {
			$index = rand(0, 2);
		}
		
		return $index;
	}
	// return rand_unique()
	
	
	// content of Admin Menu Editor advertisement slot
	private function admin_menu_editor() {
	
		$output = '
			<div style="text-align: center;">
				<a href="http://w-shadow.com/admin-menu-editor-pro/?utm_source=UserRoleEditor&utm_medium=banner&utm_campaign=Plugins " target="_new" >
					<img src="'. URE_PLUGIN_URL . 'images/admin-menu-editor-pro.jpg' .'" alt="Admin Menu Editor Pro" title="Move, rename, hide, add admin menu items, restrict access"/>
				</a>
			</div>  
			';
		
		return $output;
	}
	// end of admin_menu_editor()
	
	
	// content of Manage WP advertisement slot
	private function manage_wp() {
	
		$output = '
			<div style="text-align: center;">
			<a title="ManageWP" href="http://managewp.com/?utm_source=user_role_editor&utm_medium=Banner&utm_content=mwp250_2&utm_campaign=Plugins" target="_new" >
				<img width="250" height="250" alt="ManageWP" src="'. URE_PLUGIN_URL .'images/mwp250_2.png">
			</a>                        
		</div>  
			';

		return $output;
	}
	// end of manage_wp()


	// content of Video User Manuals advertisement slot
	private function video_user_manuals() {
	
		ob_start();
?>			
			<div style="margin-left: 3px; margin-bottom: 3px; text-align: center; background: url(<?php echo URE_PLUGIN_URL . 'images/vum-ebook-250-250.jpg'; ?>) left top no-repeat;">
				<div style="width: 250px; height: 250px; position: relative; ">
					<form accept-charset="utf-8" action="https://app.getresponse.com/add_contact_webform.html" method="post" onsubmit="return quickValidate()" target="_blank">
					<div style="display: none;">
						<input type="hidden" name="webform_id" value="430680" />
					</div>
					<input id="vum_sub_name" type="text" name="name" class="text"  tabindex="500" value="Enter your name" style="border: 0; position: absolute; left:129px;top:91px;height: 18px; width: 90px;background-color: #fff; font-size: 11px;" onfocus="this.value='';" />
					<input class="text" id="vum_sub_email" type="text" name="email" tabindex="501"  value="Email"  style="border: 0; position: absolute; left:129px;top:126px;height: 18px; width: 90px;background-color: #fff; font-size: 11px;" onfocus="this.value='';"  />
					<input name="submit" type="image" alt="submit" tabindex="502" src="<?php echo URE_PLUGIN_URL; ?>images/vum-submit.jpg" width="100" height="25" style="background: none; border: 0;position: absolute; left:121px;top:154px;" />
					<a href="http://www.videousermanuals.com/blog/report/?utm_campaign=plugin-ads&utm_medium=plugin&utm_source=user-role-editor" target="_blank" style="position: absolute; left: 7px;top: 63px;width:102px;height:152px;border:0;text-decoration: none;">&nbsp;</a>
					<a href="http://www.videousermanuals.com/blog/report/?utm_campaign=plugin-ads&utm_medium=plugin&utm_source=user-role-editor" target="_blank" style="position: absolute; left: 41px;top: 219px;width:163px;height:25px;border:0;text-decoration: none;">&nbsp;</a>
					</form>
				</div>
		   <script type="text/javascript">
				function quickValidate() {
					if ((!jQuery('#vum_sub_name').val()) || (jQuery('#vum_sub_name').val() == 'Enter your name') ) 
						{
							alert('Your Name is required');
							return false; 
						}
					if ((!jQuery('#vum_sub_email').val()) || (jQuery('#vum_sub_email').val() == 'Email') ) 
						{
							alert('Your Email is required');
							return false; 
						}
						return true;
				}			
			</script>
			
			</div>  
<?php
		$output = ob_get_contents();
		ob_end_clean();
		
		return $output;
	}
	// end of manage_wp()

	
	/**
	 * Output all existed ads slots
	 */
	public function display() {
	
		foreach ($this->slots as $slot) {
			echo $slot."\n";
		}
		
	}
	// end of display()
	
}
// end of ure_Advertisement
?>
