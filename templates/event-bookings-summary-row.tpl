<tr  id="summary_row_<?php echo $this->body_template_data[$mode_index]['records'][$i_ul]['ev_id']; ?>"   class=" responsive-task-cat"  >

	<?php $action_mode = 'bookings'; ?>

  <td class="text-center"  >
	<div class="">
		<a href="event-bookings.php#mode=<?php echo $action_mode; ?>&recid=<?php echo $this->body_template_data[$mode_index]['records'][$i_ul]['ev_id']; ?>"  class="btn btn-xs btn-primary user-edit-action record-edit-button"   data-in-mode="list-mode"    data-recid="<?php echo $this->body_template_data[$mode_index]['records'][$i_ul]['ev_id']; ?>"   data-rel='tooltip' title="View bookings" >
				<img src="images/view-icon.png" class="custom-button-small" alt="View">
			</a>
	</div>


	</td>

	

  <td data-label   data-in-mode="list-mode"    data-recid="<?php echo $this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>"   data-hash="<?php echo 'mode='.$action_mode.'&recid=',$this->body_template_data[$mode_index]['records'][$i_ul]['ev_id']; ?>" class="pointer clickable-cell pseudo-link"    ><?php \eBizIndia\_esc($this->body_template_data[$mode_index]['records'][$i_ul]['ev_name']); ?></td>

  <?php
  	$ev_dates = date('d-M-Y',strtotime($this->body_template_data[$mode_index]['records'][$i_ul]['ev_start_dt']));
  	if($this->body_template_data[$mode_index]['records'][$i_ul]['ev_start_dt']!=$this->body_template_data[$mode_index]['records'][$i_ul]['ev_end_dt'])
  		$ev_dates .= ' to '. date('d-M-Y',strtotime($this->body_template_data[$mode_index]['records'][$i_ul]['ev_end_dt']));

  ?>

  <td data-label  class="pointer clickable-cell"  data-in-mode="list-mode"    data-recid="<?php echo $this->body_template_data[$mode_index]['records'][$i_ul]['ev_id']; ?>"   data-hash="<?php echo 'mode='.$action_mode.'&recid=',$this->body_template_data[$mode_index]['records'][$i_ul]['ev_id']; ?>" ><?php \eBizIndia\_esc($ev_dates); ?></td>  
  
  <td data-label  class="pointer clickable-cell text-right recs-list-mobile-left"  data-in-mode="list-mode"    data-recid="<?php echo $this->body_template_data[$mode_index]['records'][$i_ul]['ev_id']; ?>"   data-hash="<?php echo 'mode='.$action_mode.'&recid=',$this->body_template_data[$mode_index]['records'][$i_ul]['ev_id']; ?>" ><?php \eBizIndia\_esc($this->body_template_data[$mode_index]['records'][$i_ul]['bookings']); ?><span class="mobile_only" style="margin-left: 5px;"  >Registrations</span></td>

  <td data-label  class="pointer clickable-cell text-right recs-list-mobile-left"  data-in-mode="list-mode"    data-recid="<?php echo $this->body_template_data[$mode_index]['records'][$i_ul]['ev_id']; ?>"   data-hash="<?php echo 'mode='.$action_mode.'&recid=',$this->body_template_data[$mode_index]['records'][$i_ul]['ev_id']; ?>" ><?php \eBizIndia\_esc($this->body_template_data[$mode_index]['records'][$i_ul]['tot_tickets']>0?$this->body_template_data[$mode_index]['records'][$i_ul]['tot_tickets']:0); ?><span class="mobile_only" style="margin-left: 5px;"  >Persons</span></td>

  <td data-label  class="pointer clickable-cell text-right recs-list-mobile-left"  data-in-mode="list-mode"    data-recid="<?php echo $this->body_template_data[$mode_index]['records'][$i_ul]['ev_id']; ?>"   data-hash="<?php echo 'mode='.$action_mode.'&recid=',$this->body_template_data[$mode_index]['records'][$i_ul]['ev_id']; ?>" ><?php \eBizIndia\_esc($this->body_template_data[$mode_index]['records'][$i_ul]['attended']>0?$this->body_template_data[$mode_index]['records'][$i_ul]['attended']:0); ?><span class="mobile_only" style="margin-left: 5px;"  >Attended</span></td>

  <?php
  	$no_show = $this->body_template_data[$mode_index]['records'][$i_ul]['tot_tickets'] - $this->body_template_data[$mode_index]['records'][$i_ul]['attended'];
  ?>

  <td data-label  class="pointer clickable-cell text-right recs-list-mobile-left"  data-in-mode="list-mode"    data-recid="<?php echo $this->body_template_data[$mode_index]['records'][$i_ul]['ev_id']; ?>"   data-hash="<?php echo 'mode='.$action_mode.'&recid=',$this->body_template_data[$mode_index]['records'][$i_ul]['ev_id']; ?>" ><?php \eBizIndia\_esc($no_show); ?><span class="mobile_only" style="margin-left: 5px;"  >No Show</span></td>

  <td data-label  class="pointer clickable-cell text-right recs-list-mobile-left"  data-in-mode="list-mode"    data-recid="<?php echo $this->body_template_data[$mode_index]['records'][$i_ul]['ev_id']; ?>"   data-hash="<?php echo 'mode='.$action_mode.'&recid=',$this->body_template_data[$mode_index]['records'][$i_ul]['ev_id']; ?>" >&#8377;<?php \eBizIndia\_esc($this->body_template_data[$mode_index]['records'][$i_ul]['tot_amount']>0?number_format($this->body_template_data[$mode_index]['records'][$i_ul]['tot_amount']):0); ?></td>

  <?php 
		$status_cls='text-danger';
		$status_span_cls='status-notlive';
		$reg_on_text = 'No';
		if($this->body_template_data[$mode_index]['records'][$i_ul]['ev_active']=='y' && $this->body_template_data[$mode_index]['records'][$i_ul]['ev_reg_active']==='y' && $this->body_template_data[$mode_index]['records'][$i_ul]['ev_reg_start_dt']!=''){
			$today = (new DateTime())->setTime(23,59,59);
			$dt1 = (new \DateTime($this->body_template_data[$mode_index]['records'][$i_ul]['ev_reg_start_dt']))->setTime(23,59,59);
			if($this->body_template_data[$mode_index]['records'][$i_ul]['ev_reg_end_dt']!=''){
				$dt2 = (new \DateTime($this->body_template_data[$mode_index]['records'][$i_ul]['ev_reg_end_dt']))->setTime(23,59,59);
				if($today>=$dt1 && $today<=$dt2){
					$reg_on_text = 'Yes';
					$status_cls='text-success';
					$status_span_cls='status-live';
				}
			}else if($today>=$dt1){
				$reg_on_text = 'Yes';
				$status_cls='text-success';
				$status_span_cls='status-live';
			}

		} 

		$evactive_cls='text-danger';
		$evactive_span_cls='status-notlive';
		$ev_active_text = 'No';
		if($this->body_template_data[$mode_index]['records'][$i_ul]['ev_active']=='y'){
			$evactive_cls='text-success';
			$evactive_span_cls='status-live';
			$ev_active_text = 'Yes';
		}

	?>


  <td  data-label class="hidden-480 <?php echo $status_cls;?> pointer clickable-cell"  data-in-mode="list-mode"    data-recid="<?php echo $this->body_template_data[$mode_index]['records'][$i_ul]['ev_id']; ?>"   data-hash="<?php echo 'mode='.$action_mode.'&recid=',$this->body_template_data[$mode_index]['records'][$i_ul]['ev_id']; ?>"><span class="show_in_mobile"  style="color: #212529 !important;"   >Registration is On: </span>
		<span class='<?php echo $status_span_cls; ?> pointer' ><?php \eBizIndia\_esc($reg_on_text); ?></span >
	</td>
  
  <td  data-label class="hidden-480 <?php echo $evactive_cls;?> pointer clickable-cell"  data-in-mode="list-mode"    data-recid="<?php echo $this->body_template_data[$mode_index]['records'][$i_ul]['ev_id']; ?>"   data-hash="<?php echo 'mode='.$action_mode.'&recid=',$this->body_template_data[$mode_index]['records'][$i_ul]['ev_id']; ?>"><span class="show_in_mobile"  style="color: #212529 !important;"   >Event Active: </span>
		<span class='<?php echo $evactive_span_cls; ?> pointer' ><?php \eBizIndia\_esc($ev_active_text); ?></span >
	</td>
  


</tr>
