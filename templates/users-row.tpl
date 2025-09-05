<?php 
	$action_mode = 'view';  // This should be the default mode for both ADMIN and REGULAR members
	if($this->body_template_data['cu_role']==='REGULAR' &&  $this->body_template_data[$mode_index]['records'][$i_ul]['dnd']==='y' && ($this->body_template_data[$mode_index]['cu_id'] !== $this->body_template_data[$mode_index]['records'][$i_ul]['user_acnt_id'] || $this->body_template_data[$mode_index]['self_edit']!==true ) )
		$action_mode = false; // Regualr memebrs are not allowed to view details of a DND marked profile 
	/*elseif( ($this->body_template_data[$mode_index]['cu_id'] == $this->body_template_data[$mode_index]['records'][$i_ul]['user_acnt_id'] && $this->body_template_data[$mode_index]['self_edit']===true) || ($this->body_template_data[$mode_index]['cu_id'] !== $this->body_template_data[$mode_index]['records'][$i_ul]['user_acnt_id'] && $this->body_template_data[$mode_index]['others_edit']===true) ){
				 $action_mode='edit';
	}*/

// echo "<pre>";
// 	print_r($this->body_template_data[$mode_index]['records']);
// 	die();

	$is_member_admin = $this->body_template_data[$mode_index]['records'][$i_ul]['assigned_roles'][0]['role']=='ADMIN'?true:false; // the current member in the iteration
	$status_cls='';
	$admin_class= '';
	if($_is_admin){ // if the logged in user is an admin
		if($this->body_template_data[$mode_index]['records'][$i_ul]['active']=='n'){
			$status_cls='inactive_member';
		}
		


	}
	

	if($is_member_admin){
			$admin_class = 'admin_member';
	}

	$profile_pic = '';

	if($this->body_template_data[$mode_index]['records'][$i_ul]['profile_pic']!=''){
			$profile_pic = CONST_PROFILE_IMG_URL_PATH.$this->body_template_data[$mode_index]['records'][$i_ul]['profile_pic'] ;
	}else{
			if($this->body_template_data[$mode_index]['records'][$i_ul]['gender']==='F')
				$profile_pic = CONST_NOIMAGE_F_FILE ;
			else
				$profile_pic = CONST_NOIMAGE_M_FILE ;
	}


?>



<div class="user_block">
	<!--<div class="col-sm-12 col-md-12 col-lg-3">-->
	<!-- <div class="member_list_block <?php echo $admin_class.'  '.$status_cls; ?> pointer clickable-cell "  data-hash="<?php echo 'mode='.(!$action_mode?'view':$action_mode).'&recid=',$this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>"     > -->
	<div class="member_list_block   <?php echo $admin_class." ".$status_cls; ?> pointer clickable-cell "  data-hash="<?php echo 'mode='.(!$action_mode?'view':$action_mode).'&recid=',$this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>"     >
			<?php
				if($this->body_template_data[$mode_index]['records'][$i_ul]['dnd']==='y'){
			?>
					<div class="flag_user"><img src="images/no-call-big.png" alt="Flag" class="" width="20" title="Marked as DND"  ></div>
			<?php		
				}
			?>
		
			<?php
			if($action_mode=='edit'){
			?>	
				<!-- <div class="member_img">
					<a href="users.php#mode=edit&recid=<?php echo $this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>"   data-in-mode="list-mode"    data-recid="<?php echo $this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>"   data-rel='tooltip' title="Edit details"  >
						<img src="<?php echo $profile_pic; ?>" alt=""  class="prof_img"  >
					</a>	
				</div>
				
				<div class="member_name">
					<a href="users.php#mode=edit&recid=<?php echo $this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>"   data-in-mode="list-mode"    data-recid="<?php echo $this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>"   data-rel='tooltip' title="Edit details"  >
						
						<?php 
							
							echo \eBizIndia\_esc($this->body_template_data[$mode_index]['records'][$i_ul]['name'], true);
							if($this->body_template_data[$mode_index]['records'][$i_ul]['batch_no']!=''){
								echo ' (',\eBizIndia\_esc($this->body_template_data[$mode_index]['records'][$i_ul]['batch_no'], true),')';
							} 
						?>
					</a>
				</div> -->

			<?php
			}else { // $action mode is view or false
			?>
				<div class="member_img">
					<a href="users.php#mode=view&recid=<?php echo $this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>"  data-in-mode="list-mode"    data-recid="<?php echo $this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>"   data-rel='tooltip' title="View details"  class="nopropagate"  >
						<img src="<?php echo $profile_pic; ?>" alt=""  class="prof_img"   >
					</a>
				</div>

				<div class="member_name">
					<a href="users.php#mode=view&recid=<?php echo $this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>"     data-in-mode="list-mode"    data-recid="<?php echo $this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>"   data-rel='tooltip' title="View details"  class="nopropagate"   >
						
						

						<?php 
						
							echo \eBizIndia\_esc($this->body_template_data[$mode_index]['records'][$i_ul]['name'], true); 
							if($this->body_template_data[$mode_index]['records'][$i_ul]['batch_no']!=''){
								
							}
						?>
					</a>
				</div>

			<?php	
			} 
			?>
		
		<div class="member_details company_truncate">
			<div  title="<?php echo $this->body_template_data[$mode_index]['records'][$i_ul]['work_company']; ?>"  ><?php echo \eBizIndia\_esc($this->body_template_data[$mode_index]['records'][$i_ul]['work_company'], true); ?></div>
		</div>
		<?php
			if($action_mode){ // IF action mode is not false then show the contact options
		?>
		<div class="member_details nopropagate" style="width: fit-content; margin: 0 auto; margin-top: 8px;"   >
			<?php
				if(!empty($this->body_template_data[$mode_index]['records'][$i_ul]['email'])){
			?>
				<a href="mailto:<?php echo $this->body_template_data[$mode_index]['records'][$i_ul]['email']; ?>" style="white-space: nowrap;" class="nopropagate"><img src="images/email.png" alt="email" style="text-decoration: none;"></a>	
			<?php
				}
					if(!empty($this->body_template_data[$mode_index]['records'][$i_ul]['mobile'])){
						$whatsapp_num = $this->body_template_data[$mode_index]['records'][$i_ul]['mobile'];
						if(!preg_match("/^[+0]/", $whatsapp_num))
							$whatsapp_num = '+'.$this->body_template_data['country_code'].$whatsapp_num;
			?>

						<a href="https://wa.me/<?php echo $whatsapp_num; ?>" class="nopropagate" target="_blank" rel="noopener"><img src="images/whatsapp.png" alt="whatsapp" style="margin-left:15px;text-decoration: none;"></a>

						<a href="tel:<?php echo $whatsapp_num; ?>" style="white-space: nowrap;width: 100% !important;" class="nopropagate" rel="noopener"><img src="images/phone.png" alt="phone" style="margin-left:15px; text-decoration: none; "></a>
		
		<?php
					}
		?>
		</div>		
		<?php			
				}
		?>													
		<!-- div class="member_details member_details_inline">
			<strong><?php echo \eBizIndia\_esc($this->body_template_data[$mode_index]['records'][$i_ul]['membership_no'],true); ?></strong> 
		</div -->																								
	</div>
</div>

