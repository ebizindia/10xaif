<?php
	//echo date('jS-F');;
	//print_r($this->base_template_data['events_summary']);

	// $today_birthday=0;
	// $today_birthday_count=0;
	// $today_anniversaryday=0;
	// $today_anniversaryday_count=0;
	// $recfirst_fullname='';
	// if($this->base_template_data['loggedindata'][0]['usertype']!="ADMIN"){
	// 	$class="d-none";
	// }
?>
<style>
:root {
	/*--color-a: #fb7f65;
	--color-b: #f90;
	--color-c: #F79D8A;*/
	
	--color-a: #ff795c;
	--color-b: #ff9900;
	--color-c: #fd7152;
}
.dashboard-rounded-square-btn {
  font-size: 1rem !important;
  color: #fff;
}
.contact_image img{
width: 20px;
height: auto;
}
.menu-button {
padding: 1rem 1.5rem;
border: none;
border-radius: 0.5rem;
color: white;
font-size: 1rem;
cursor: pointer;
text-decoration: none;
text-align: center;
min-width: 160px;
transition: opacity 0.2s ease-in-out;
}

.menu-button:hover {
opacity: 0.9;
}
.card_container .card_single{
	border-radius: 0.5rem;
}
.card_container .card_single:nth-child(2n) a {
background-color: var(--color-a);
color: #fff;
letter-spacing: 1.2px;
  font-weight: 600;
}

.card_container .card_single:nth-child(2n+1) a{
background-color: var(--color-b);
color: #fff;
letter-spacing: 1.2px;
  font-weight: 600;
}
/*
.card_container .card_single:nth-child(2n+2) a{
background-color: var(--color-c);
}*/
.card_container .card_single a:hover{
color:#fff;
}	
.menu-container {
display: flex;
gap: 1rem;
flex-wrap: wrap;
justify-content: flex-start;
max-width: 1200px;
}
.card_single a{
	height:auto;
}
.menu-button {
box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}
@media (max-width:767px){
	.contact_image img{
	width: 32px;
	height: auto;
	margin-right: 40px !important;
	}
	.pointer.clickable-cell, .dashboard_mem_cnt a, .Birthdays-Anniversaries {
		font-size: 20px !important;
	}
	.instruction-option{
		font-size: 17px !important;
	}
}

</style>
<div class="row">
	<div class="col-12 mt-3 mb-2">
	<div class="">
		<div class="card">
			<div class="card-body">
				<div class="mt-2">
					
					<?php if($this->base_template_data['loggedindata'][0]['user_settings_array']['page_guide'][$this->base_template_data['module_name']]!=1){ ?>

					<!-- <div class='alert alert-warning'>
						<button type='button' class='btn btn-xs btn-danger pull-right close-guide' data-dismiss='alert' data-module='<?php echo $this->base_template_data['module_name'];?>'>
							<i class='fa fa-remove'></i>
						</button>
						Overall statistics for the system will appear here.
					</div> -->
					<?php } ?>
					<div class='alert alert-info'>
						<h3>Welcome to <?php echo $this->base_template_data['app_disp_name']; ?></h3>
						<p class="instruction-option" style="margin-bottom: 0;">Please choose an option from the menu.</p>
					</div>
					<div class="col-sm-9 padding-none">
						<div class="pull-left">
						</div>
					</div>					
					<div class="mb-3">
						<div>														
							<div class="card_container">
								<div class="card_single">
									<a href="calendar.php" class="action-button btn menu-button issue_button mb-3 dashboard-rounded-square-btn" type="submit" id="record-save-button">
										<!--<img src="images/check.png" class="check-button" alt="Check">--> <span>Calendar</span>
									</a>
								</div>
								<div class="card_single">
									<a href="users.php" class="action-button btn menu-button issue_button mb-3 dashboard-rounded-square-btn" type="submit" id="record-save-button">
										<!--<img src="images/check.png" class="check-button" alt="Check">--> <span>Members</span>
									</a>
								</div>
								
								<div class="card_single">							
									<a href="reflections.php" class="action-button btn menu-button issue_button mb-3 dashboard-rounded-square-btn" type="submit" id="record-save-button">
										<!--<img src="images/check.png" class="check-button" alt="Check">--> <span>Reflections</span>
									</a>
								</div>
								<div class="card_single">							
									<a href="all-goals.php" class="action-button btn menu-button issue_button mb-3 dashboard-rounded-square-btn" type="submit" id="record-save-button">
										<!--<img src="images/check.png" class="check-button" alt="Check">--> <span>Goals</span>
									</a>
								</div>
								<div class="card_single">							
									<a href="view-forum-roles.php" class="action-button btn menu-button issue_button mb-3 dashboard-rounded-square-btn" type="submit" id="record-save-button">
										<!--<img src="images/check.png" class="check-button" alt="Check">--> <span>Forum Roles</span>
									</a>
								</div>
								
								<div class="card_single">							
									<a href="meetings.php" class="action-button btn menu-button issue_button mb-3 dashboard-rounded-square-btn" type="submit" id="record-save-button">
										<!--<img src="images/check.png" class="check-button" alt="Check">--> <span>Meetings</span>
									</a>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div  class="mb-3"  >
					<?php
						if($this->base_template_data['can_access_members']){
					?>	
							<span class="dashboard_mem_cnt"  ><a href="users.php" ><?php if($this->body_template_data['tot_active_members']!==false){ echo 'View '.$this->body_template_data['tot_members'].' members'; }else if($this->body_template_data['tot_members']===0){ echo 'No members yet.';}else{ echo 'Members count is not available.';}?></a></span>
					<?php
						}else{
					?>
							<span class="dashboard_mem_cnt"  ><?php if($this->body_template_data['tot_members']!==false){echo 'View '.number_format($this->body_template_data['tot_members']).' members';}else{ echo 'Members count is not available.';} ?></span>
					<?php		
						}
					?>		

				</div>
				<div class="mb-3"  >
					<?php
						if(count($this->body_template_data['bday_annv_list'])>0){
					?>
							<div class="Birthdays-Anniversaries" style="width: 100%; font-weight: bold;"  >Birthdays &amp; Anniversaries Today</div>
							<div class="responsive-block-table for-cat">
								<div class="panel-body table-responsive birthdays_anniversaries"  >
									<table   border="0" cellpadding="4" cellspacing="0" class="table table-striped table-bordered table-hover"  >
										<thead class="thead">
											<tr>
												<th>Name</th>
												<th>Occasion</th>
												<th>Greet</th>
											</tr>
										</thead>
										<tbody>
											<?php
												foreach ($this->body_template_data['bday_annv_list'] as $mem) {
													$whatsapp_num = $mem['mobile'];
													if($whatsapp_num!='' && !preg_match("/^[+0]/", $whatsapp_num))
														$whatsapp_num = '+'.$this->base_template_data['country_code'].$whatsapp_num;
													if($this->body_template_data['cu_role']!=='ADMIN' && $mem['dnd']==='y'){
														$action_mode = false;	
													}else{
														$action_mode = 'view';
														// if(($this->base_template_data['loggedindata'][0]['profile_details']['id'] === $mem['id'] && $this->base_template_data['self_edit']===true) || ($this->base_template_data['loggedindata'][0]['profile_details']['id'] !== $mem['id'] && $this->base_template_data['others_edit']===true)  )
														// 	$action_mode='edit';
													}

											?>		
													<tr  class="gradient-table-row <?php if($mem['active']==='n'){ echo ' disable_gray_txt ';} ?>"  >
														<td <?php if($action_mode){ ?> class="pointer clickable-cell"  data-url="<?php echo CONST_APP_ABSURL.'/users.php#mode='.$action_mode.'&recid=',$mem['id']; ?>" <?php } ?>  >
															<?php 
															  	if($mem['dnd']==='y' && $this->body_template_data['show_dnd_status']===true){
															?>		
															  	<img src="images/no-call.png" width="16" height="16" alt="DND marked" title="Marked as DND" style="display: inline-block; position: relative; margin-left: 0; margin-right: 0;"  >
															<?php 
															  	}
															\eBizIndia\_esc($mem['name'].(!empty($mem['batch_no'])?' ('.$mem['batch_no'].')':'' )); ?></td>
														<td <?php if($action_mode){ ?> class="pointer clickable-cell" data-url="<?php echo CONST_APP_ABSURL.'/users.php#mode='.$action_mode.'&recid=',$mem['id']; ?>" <?php } ?>  ><?php \eBizIndia\_esc($mem['type']=='both'?'Birthday & Anniversary':($mem['type']=='bday'?'Birthday':'Anniversary') ); ?></td>
														<td  style="white-space: nowrap;" class="" >
															<?php
																if($this->body_template_data['cu_role']==='ADMIN' || $mem['dnd']==='n'){
															?>
																<a href="mailto:<?php echo $mem['email']; ?>" class="nopropagate contact_image" title="Send an email"  ><img src="images/email-big.png" alt="email" style="margin-right: 15px;text-decoration: none;margin-top: -3px;" ></a>
															<?php
																	if(!empty($whatsapp_num)){
															?>		
																	<a href="https://wa.me/<?php echo $whatsapp_num; ?>"   class="nopropagate contact_image" target="_blank" rel="noopener" title="Send WhatsApp message"  ><img src="images/whatsapp-big.png" alt="whatsapp" style="text-decoration: none;margin-top:-4px;margin-right: 15px;" ></a>
																	<a href="tel:<?php echo $whatsapp_num; ?>" style="white-space: nowrap;width: 100% !important;"  class="nopropagate contact_image" rel="noopener" title="Call now"  ><img src="images/phone-big.png" alt="phone" style="text-decoration: none; margin-top: -3px; " ></a>
															<?php
																	}
																}else{
																	echo "&nbsp;";
																}
															?>
														</td>
													</tr>
											<?php		
												}
											?>
										</tbody>
									</table>
								</div>
							</div>
					<?php
						}else{
					?>
							<div style="width: 100%;  font-size: 16px; font-weight: bold;"  >No Birthdays &amp; Anniversaries Today.</div>
					<?php		

						}
					?>
				</div>
			</div>
		</div>
	</div>
</div>
</div>