<?php
$today_bd_count=0;
$today_an_count=0;
$recbd_fullname=array();
$anRecords='';
$recbd = array();
if( array_key_exists('birthday_list_notification', $this->base_template_data) )
	foreach($this->base_template_data['birthday_list_notification'] as $key=>$bdvalues){
		$bdvalues['Date']=$bdvalues['birth_date_dm']['d'].'-'.$bdvalues['birth_date_dm']['M'];
		if($bdvalues['Date']==date("d-M")){
			$today_bd_count++;
		}

	}

	if($today_bd_count!=0){ $recbd= current($this->base_template_data['birthday_list_notification']);
				  $recbd_fullname=$recbd['salutation'].' '.$recbd['first_name'].' '.$recbd['last_name'];
			      if($today_bd_count>1){
					 $restbdrec=$today_bd_count-1;
                     if($restbdrec==1){
						$addbdtext=" ";
					 }
					 else {
						$addbdtext=" ";
					 }
				}
                  else{
					 $addbdtext="'s birthday is today.";
				  }
	}
	if( array_key_exists('anniversaryday_list_notification', $this->base_template_data) )
		$anRecords=$this->base_template_data['anniversaryday_list_notification'][date('jS F')];
	if(is_array($anRecords))
	{
		$today_an_count=count($anRecords);
	}
	if($today_an_count!=0){
		$recAn= current($this->base_template_data['anniversaryday_list_notification']);
				  $recAn_fullname=$recAn[0]['salutation'].' '.$recAn[0]['first_name'].' '.$recAn[0]['last_name'];
			      if($today_an_count>1){
					 $restAn=$today_an_count-1;
                     if($restAn==1){
						$addantext=" ";
					 }
					 else {
						$addantext="  ";
					 }
				}
                  else{
					 $addantext="'s anniversary is today.";
				  }
	}

?>
<div class="navbar navbar-default fixed-top  <?php echo ($this->base_template_data['template_type']=="login")?"d-none":"";?>" id="navbar">
	<script type="text/javascript">
        try{ace.settings.check('navbar' , 'fixed')}catch(e){}
    </script>

    <div class="navbar-container row" id="navbar-container">

            <?php if($this->base_template_data['loggedindata'][0]['id']!=''){?>
            <a class="menu-toggler" id="menu-toggler" href="#"></a>
            <?php } ?>
            <div class="col-lg-6 col-md-6 col-sm h-40" >
				<div class="logo-container" >
					<a href="<?php if($this->base_template_data['loggedindata'][0]['id']==''){echo 'http' . (( $_SERVER['HTTPS'] != '' ? 's' : '' ). '://'.$_SERVER['HTTP_HOST'] ); }else{echo '#';} ?>" class="navbar-brand" style='cursor:pointer !important;' style="padding-left: 0px;">
							<?php /*echo $this->base_template_data['app_disp_name'];*/ ?>
							<img  src="<?php echo CONST_LOGO_IMAGE_NAVBAR; ?>" class="img-responsive" alt="logo" style="max-width: 100px;"/>
							<a href="#menu-toggle" id="menu-toggle"><i class="fa fa-bars"><img src="images/menu-icon.png" alt="menu"></i></i></a>
					</a>
				</div>
            </div>
           
         <div class="col-lg-6 col-md-6 col-sm-12 login_menu_adj">
        	<div class="navbar-header pull-right row" role="navbar">
            <?php
            if( ($this->body_template_data['navbar']['template_type']??'')!='login' && ($this->base_template_data['loggedindata'][0]['id']??'')!='' && ($this->base_template_data['template_type']??'')!='login_header'){

        ?>

                    <ul class="nav">
                        <?php if($today_bd_count!=0 || $today_an_count!=0) { ?>
						<li class="purple">
							<a data-toggle="dropdown" class="dropdown-toggle" href="#">
								<i class="icon-gift icon-animated-bell"></i>
							</a>

							<ul class="pull-right dropdown-navbar dropdown-menu dropdown-caret dropdown-close">
								<li class="dropdown-header">
									Birthday &amp; Anniversary
								</li>
								<?php if($today_bd_count!=0){ ?>
								<li>
									<a class="black" href="contacts.php?do=print#mode=report-contact-birthday" target="_blank">
										<div class="clearfix">
											<span class="pull-left">
												<i class="btn btn-xs btn-success icon-gift"></i>
												<?php echo $recbd_fullname.''.$addbdtext; ?>
											</span>
											<?php if($restbdrec>0)
											{ ?>
											<span class="pull-right badge badge-success">+<?php echo $restbdrec; ?></span>
											<?php } ?>
										</div>
									</a>
								</li>
                                <?php } if($today_an_count!=0){ ?>
								<li>
									<a class="black" href="contacts.php?do=print#mode=report-contact-anniversary" target="_blank">
										<div class="clearfix">
											<span class="pull-left">
												<i class="btn btn-xs btn-pink icon-heart"></i>
												<?php echo $recAn_fullname.''.$addantext; ?>
											</span>
											<?php if($restAn>0)
											{ ?>
											<span class="pull-right badge badge-pink">+<?php echo $restAn; ?></span>
											<?php }							 ?>
										</div>
									</a>
								</li>
								<?php } ?>
							</ul>
						</li>
						<?php } ?>
						<li class="light-blue nav-item dropdown">
                            <a data-toggle="dropdown" href="#" class="nav-link dropdown-toggle">
                                <!-- <img class="nav-user-photo" src="assets/user.<?php echo RESOURCE_VERSION;?>.jpg" alt="Jason's Photo" /> -->
                                <span class="user-info" style="white-space:nowrap; position:relative; top:1.5px; text-overflow:ellipsis; overflow:hidden; display:inline-block;">
                                	<small></small>Hi
                                    <span class="user-name"><?php echo $this->base_template_data['loggedindata'][0]['profile_details']['fname'];?></span>
                                </span>

                                <!-- i class="fa fa-caret-down pull-right"></i -->
                            </a>
                            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            	<!-- <a class="dropdown-item" href="myprofile.php"><i class="icon-cog"></i>
                                        Settings</a>
                                <div class="dropdown-divider"></div> -->
						        <a class="dropdown-item right-menu-icon-profile <?php if($this->base_template_data['self_edit']!==true){ echo 'd-none'; }  ?> " href="<?php echo $this->base_template_data['my_profile_url']; ?>" id=""   ><i class="icon-off"></i>
                                        My Profile</a>
                                <a class="dropdown-item right-menu-icon-logout" href="login.php?mode=logout"><i class="icon-off"></i>
                                        Logout</a>
                                        

						    </div>

                        </li>

                    </ul>




        <?php

        }

        ?>


        </div>
    </div>



    </div>



</div>
