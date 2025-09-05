<style>


</style>
<div class="sidebar" id="sidebar">

        <ul class="sidebar-nav nav nav-list">


			<?php

				$users_menulist=$this->base_template_data['loggedindata'][1];

				$menu_cat_count=count($users_menulist);
				for($i_menucat=0; $i_menucat<$menu_cat_count;  $i_menucat++){
					$menu_count=count($users_menulist[$i_menucat]['menus']);

					if($users_menulist[$i_menucat]['active_menu_count'] > 0 ){

						// In order to turn off this "if" section, here the menucat is being equated to -1 instead of the original value 1. Earlier the Dashboard menu was displayed without a menucategory header but now it has to be displayed with it's menu category header, so this change. The else part displays the menus within their respective categories.
						if($users_menulist[$i_menucat]['menucat_id']==-1){
	                    	for($i_menu=0; $i_menu<1; $i_menu++){
								// echo strtolower($this->base_template_data['module_name']),' == ', $users_menulist[$i_menucat]['menus'][$i_menu]['menupage'];
		                        //echo '<pre>';print_r($users_menulist[$i_menu]);echo '</pre>';

		                        // if($this->base_template_data['loggedindata'][0]['user_settings_array']['menu_mode']=='ADVANCE' || $users_menulist[$i_menucat]['menus'][$i_menu]['included_in_basic_view']){
		                        $menu_title=$users_menulist[$i_menucat]['menus'][$i_menu]['description'];

							?>
								<li data-placement="right" data-html="true" <?php echo $menu_title!='' ? ('title="'.$menu_title.'"') : ''?>  data-container="body" class="<?php echo $users_menulist[$i_menucat]['menus'][$i_menu]['menu_icon'],' ',(strtolower($this->base_template_data['module_name']) == $users_menulist[$i_menucat]['menus'][$i_menu]['menupage'])? 'active' : '';?>" >
									<a href="<?php echo $users_menulist[$i_menucat]['menus'][$i_menu]['menuurl']; ?>" class="dasboard_align">
										<?php echo $users_menulist[$i_menucat]['menus'][$i_menu]['menuname']; ?>
		                                <i class="fa fa-angle-right fa-lg pull-right"></i>
									</a>
								</li>

							<?php
								//}
							}
							?>

	              <?php } else { 
	              		$menu_cat_collapsed = true;
	              		$tmp_menupages = array_column($users_menulist[$i_menucat]['menus'],'menupage');
	              		if(in_array(strtolower($this->base_template_data['module_name']), $tmp_menupages)){
	              			$menu_cat_collapsed = false;
	              		}

	              		$menu_cat_collapsed = false; // remove this line to keep all the menu categories collapsed by default	
	              ?>		

	                    <li class = "<?php echo $users_menulist[$i_menucat]['menu_cat_icon']; ?>" data-placement="right" data-html="true"><a href="#" onclick="return common_js_funcs.sidenavShowHide('<?php echo "nav_cat_". $users_menulist[$i_menucat]['menucat_id']; ?>');"   > <?php echo $users_menulist[$i_menucat]['menucat_name']; ?><i class="fa fa-angle-right fa-lg pull-right cat-inactive"></i><i class="fa fa-angle-down fa-lg pull-right cat-active"></i>
	                    </a>


		                    <ul  <?php if($menu_cat_collapsed===true){echo 'style="display:none"';} ?>   id="<?php echo "nav_cat_".$users_menulist[$i_menucat]['menucat_id']; ?>"
		                        >

							<?php
							for($i_menu=0; $i_menu<$menu_count; $i_menu++){
								// echo strtolower($this->base_template_data['module_name']),' == ', $users_menulist[$i_menucat]['menus'][$i_menu]['menupage'];
		                        //echo '<pre>';print_r($users_menulist[$i_menu]);echo '</pre>';

		                        // if($this->base_template_data['loggedindata'][0]['user_settings_array']['menu_mode']=='ADVANCE' || $users_menulist[$i_menucat]['menus'][$i_menu]['included_in_basic_view']){
		                        $menu_title=$users_menulist[$i_menucat]['menus'][$i_menu]['description'];

							?>
								<li data-placement="right" data-html="true" <?php echo $menu_title!='' ? ('title="'.$menu_title.'"') : ''?>  data-container="body" class="<?php echo $users_menulist[$i_menucat]['menus'][$i_menu]['menu_icon'],' ', (strtolower($this->base_template_data['module_name']) == $users_menulist[$i_menucat]['menus'][$i_menu]['menupage'])? 'active' : '';?>" >
									<a href="<?php echo $users_menulist[$i_menucat]['menus'][$i_menu]['menuurl']; ?>"  target="<?php echo $users_menulist[$i_menucat]['menus'][$i_menu]['target_window']; ?>" >
										<span>&ndash;</span> <span class="submenu-text"><?php echo $users_menulist[$i_menucat]['menus'][$i_menu]['menuname']; ?></span>
									</a>
								</li>

							<?php
								// }
							}

							if($users_menulist[$i_menucat]['menucat_slug']=='utilities'){

							?>
								<li data-placement="right" data-html="true" title="My profile"  data-container="body" class="menu-icon-profile" >
									<a href="<?php echo $this->base_template_data['my_profile_url']; ?>" >
										<span>&ndash;</span> <span class="submenu-text">My Profile</span>
									</a>
								</li>
							<?php	

							}

							?>
							</ul>
						</li>
				<?php
						}

					}

				}
			?>

	</ul>


    </div>
   
    <div class="btm-footer-copy footer_custom" style="padding-right:0px;">
	<div style="text-align: center;display: block;width: 100%;border-bottom: 1px solid #ddd;padding-bottom:5px; margin-bottom:5px;">Licensed to:
		<strong style="color: #074d82;white-space:nowrap;"><?php echo htmlentities(CONST_LICENSED_TO); ?></strong>
	</div>
	&copy; Copyright: <?php echo $copyright_yr_text;?>  
	<div class="clearfix"></div>
	<a href="https://www.ebizindia.com/" target="_blank" rel="noopener">Ebizindia Consulting</a>
	</div>


    