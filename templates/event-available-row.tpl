<div class="" id="event_row_<?php echo $this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>"  >
	<div class="Event-Registrations-Container">											
		<div class="Event-Registrations-Table">
			<div class="Event-Registrations-Table-Cel-One">
				<div class="img_placeholder">	
					<img src="<?php echo CONST_EVENT_IMG_URL_PATH.$this->body_template_data[$mode_index]['records'][$i_ul]['mob_img']; ?>" class="dsk_img">
				</div>
			</div>														
			<div class="Event-Registrations-Table-Cel-Two">
				<div class="heading"><?php echo \eBizIndia\_esc($this->body_template_data[$mode_index]['records'][$i_ul]['name'], true); ?></div>
				<div class="desc event_details_txt"><?php echo nl2br(\eBizIndia\_esc($this->body_template_data[$mode_index]['records'][$i_ul]['description'], true)); ?></div>
				<div class="desc"><?php 
						$dt_str = date('d-M-Y', strtotime($this->body_template_data[$mode_index]['records'][$i_ul]['start_dt']));
						if($this->body_template_data[$mode_index]['records'][$i_ul]['start_dt']!=$this->body_template_data[$mode_index]['records'][$i_ul]['end_dt'])
							$dt_str .= ' To ' . date('d-M-Y', strtotime($this->body_template_data[$mode_index]['records'][$i_ul]['end_dt']));
					?>					
				
					<?php echo \eBizIndia\_esc($dt_str, true) ?>
				
					<br>
					<?php echo nl2br(\eBizIndia\_esc($this->body_template_data[$mode_index]['records'][$i_ul]['time_text'], true)); ?>
				</div>
				
				<div class="desc">
					<?php echo nl2br(\eBizIndia\_esc($this->body_template_data[$mode_index]['records'][$i_ul]['venue'], true)); ?>
				</div>
				
				<div class="desc free_event_box">
					<?php 
						$tkt_price = $this->body_template_data[$mode_index]['records'][$i_ul]['tkt_price']>0?'&#8377;'.number_format($this->body_template_data[$mode_index]['records'][$i_ul]['tkt_price']):'Free';
					?>
				</div>
				<!--<div class="text-danger closed">Registration closed.</div>-->
				<div class="float_event_list no_border">
					<div class="events_Register">
						<?php 

							$today = new DateTime();
							if($this->body_template_data[$mode_index]['records'][$i_ul]['reg_active']=='y' && $this->body_template_data[$mode_index]['records'][$i_ul]['reg_start_dt']!=''){
								$dt1 = new \DateTime($this->body_template_data[$mode_index]['records'][$i_ul]['reg_start_dt'].' 00:00:00');
								if($today>=$dt1){
									$dt2='';
									if($this->body_template_data[$mode_index]['records'][$i_ul]['reg_end_dt']!='')
										$dt2 = new \DateTime($this->body_template_data[$mode_index]['records'][$i_ul]['reg_end_dt'].' 23:59:59');
									if($dt2=='' || $today<=$dt2){

									?>
										<a href="event-registrations.php#mode=book&e=<?php echo $this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>" class="btn btn-success" style="border-radius: 4px; display:inline-block;
										margin-left:15px;"    >Register <!-- <img src="images/register.png" class="" alt="Register"  style="width: 41px; height: 41px; max-width: 100%;"   > --></a>

									<?php		

									}else{
									?>
										<span class="text-danger"  >Registration closed.</span>
									<?php	
									}
								}else{
								?>
									<span class="text-danger"  >Registrations opening soon.</span>
								<?php	
								}

							}else{
								?>
									<span class="text-danger"  >Not open for registration.</span>
								<?php
							}
						?>
						
					</div>
					<?php 
					if($this->body_template_data[$mode_index]['records'][$i_ul]['tot_tickets']>0){
					?>	

						<div class="ticket_booked">Booked <span><?php echo $this->body_template_data[$mode_index]['records'][$i_ul]['tot_tickets']; ?></span> ticket<?php if($this->body_template_data[$mode_index]['records'][$i_ul]['tot_tickets']>1){echo 's'; } ?>.</div>
					<?php
					}
					?>
				</div>	
				<!--  T -->
			</div>	
		</div>	
	</div>
	
	
</div>