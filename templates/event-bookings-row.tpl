<?php
$status_css_cls='';
if($this->body_template_data[$mode_index]['records'][$i_ul]['reg_status'] == 'Confirmed'){
	$status_css_cls='completed-task'; //class name not meaningful but exists in css
}else if($this->body_template_data[$mode_index]['records'][$i_ul]['payment_status'] == 'Failed'){
	$status_css_cls='overdue-task'; //class name not meaningful but exists in css
}
?>
<tr  id="record_row_<?php echo $this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>"   class="aborted-task responsive-task-cat <?php echo $status_css_cls; ?>"  >

	<?php $action_mode = 'view'; ?>

  <td class="text-center"  >
	<div class="">
		<a href="event-bookings.php#mode=<?php echo $action_mode; ?>&recid=<?php echo $this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>"  class="btn btn-xs btn-primary user-edit-action record-edit-button"   data-in-mode="list-mode"    data-recid="<?php echo $this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>"   data-rel='tooltip' title="View details" >
				<img src="images/view-icon.png" class="custom-button-small" alt="View">
			</a>
	</div>


	</td>

	<td data-label  class="pointer clickable-cell"  data-in-mode="list-mode"    data-recid="<?php echo $this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>"   data-hash="<?php echo 'mode='.$action_mode.'&recid=',$this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>" nowrap="nowrap" ><?php \eBizIndia\_esc($this->body_template_data[$mode_index]['records'][$i_ul]['booking_id']); ?></td>

	<td data-label  class="pointer clickable-cell"  data-in-mode="list-mode"    data-recid="<?php echo $this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>"   data-hash="<?php echo 'mode='.$action_mode.'&recid=',$this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>" ><?php echo date('d-M-Y',strtotime($this->body_template_data[$mode_index]['records'][$i_ul]['registered_on']) ); ?></td>

	<td data-label   data-in-mode="list-mode"    data-recid="<?php echo $this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>"   data-hash="<?php echo 'mode='.$action_mode.'&recid=',$this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>" class=" "    >  
  
  <?php 
  	
  	\eBizIndia\_esc($this->body_template_data[$mode_index]['records'][$i_ul]['mem_name']);

  	
  ?>
  	<span  style="display: inline-block; font-size: 11px; width: 100%; text-align: left;" class="mobile_display mt-1" >
  		<a href="mailto:<?php echo $this->body_template_data[$mode_index]['records'][$i_ul]['mem_email']; ?>" style="white-space: nowrap;width: 100% !important;"  class="nopropagate" rel="noopener" title="<?php echo $this->body_template_data[$mode_index]['records'][$i_ul]['mem_email']; ?>"  ><img src="images/email.png" alt="phone" style="text-decoration: none;  margin-top: -3px; margin-left: 2px;" ></a>
  <?php

  	if(!empty($this->body_template_data[$mode_index]['records'][$i_ul]['mem_mobile'])){ 
		$whatsapp_num = $this->body_template_data[$mode_index]['records'][$i_ul]['mem_mobile'];
		if(!preg_match("/^[+0]/", $whatsapp_num))
			$whatsapp_num = '+'.$this->body_template_data['country_code'].$whatsapp_num;
				

  ?>
  		<a href="https://wa.me/<?php echo $whatsapp_num; ?>"   class="nopropagate" target="_blank" rel="noopener" title="<?php echo $whatsapp_num; ?>"  ><img src="images/whatsapp.png" alt="whatsapp" style="margin-left:10px;text-decoration: none;margin-top:-4px;" ></a>
  		<a href="tel:<?php echo $whatsapp_num; ?>" style="white-space: nowrap;width: 100% !important;"  class="nopropagate" rel="noopener" title="<?php echo $whatsapp_num; ?>"  ><img src="images/phone.png" alt="phone" style="text-decoration: none;  margin-top: -3px; margin-left: 10px;" ></a>
  <?php
  
  }

  ?>
  	</span>		

  </td>  


  <!-- <td data-label   data-in-mode="list-mode"    data-recid="<?php echo $this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>"   data-hash="<?php echo 'mode='.$action_mode.'&recid=',$this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>" class="pointer clickable-cell pseudo-link"    >  
  
  <?php 
  	
  	\eBizIndia\_esc($this->body_template_data[$mode_index]['records'][$i_ul]['ev_name']);

  	echo ' <span style="display: inline-block; font-size: 11px; width:100%" >( Start Dt.: '.date('d-M-Y',strtotime($this->body_template_data[$mode_index]['records'][$i_ul]['ev_start_dt']) ).' )</span>';

  ?>
  </td>   -->

  <td data-label  class="pointer clickable-cell text-right recs-list-mobile-left"  data-in-mode="list-mode"    data-recid="<?php echo $this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>"   data-hash="<?php echo 'mode='.$action_mode.'&recid=',$this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>" ><?php \eBizIndia\_esc($this->body_template_data[$mode_index]['records'][$i_ul]['no_of_tickets']); ?><span class="mobile_only" style="margin-left: 5px;"  >Persons</span></td>


  <td data-label  class="pointer clickable-cell text-right recs-list-mobile-left"  data-in-mode="list-mode"    data-recid="<?php echo $this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>"   data-hash="<?php echo 'mode='.$action_mode.'&recid=',$this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>" ><?php \eBizIndia\_esc($this->body_template_data[$mode_index]['records'][$i_ul]['attended']??0); ?><span class="mobile_only" style="margin-left: 5px;"  >Attended</span></td>
  
  <?php
  	$no_show = $this->body_template_data[$mode_index]['records'][$i_ul]['no_of_tickets'] - ($this->body_template_data[$mode_index]['records'][$i_ul]['attended']??0);
  ?>

  <td data-label  class="pointer clickable-cell text-right recs-list-mobile-left"  data-in-mode="list-mode"    data-recid="<?php echo $this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>"   data-hash="<?php echo 'mode='.$action_mode.'&recid=',$this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>" ><?php \eBizIndia\_esc($no_show); ?><span class="mobile_only" style="margin-left: 5px;"  >No Show</span></td>
  

  <td data-label  class="pointer clickable-cell recs-list-mobile-left"  data-in-mode="list-mode"    data-recid="<?php echo $this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>"   data-hash="<?php echo 'mode='.$action_mode.'&recid=',$this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>" ><span class="show_in_mobile"  >Registration: </span><?php \eBizIndia\_esc($this->body_template_data[$mode_index]['records'][$i_ul]['reg_status']); ?></td>

  <td data-label  class="pointer clickable-cell recs-list-mobile-left"  data-in-mode="list-mode"    data-recid="<?php echo $this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>"   data-hash="<?php echo 'mode='.$action_mode.'&recid=',$this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>" ><span class="show_in_mobile"  >Payment: </span><?php \eBizIndia\_esc($this->body_template_data[$mode_index]['records'][$i_ul]['payment_status']); ?></td>

  <td data-label  class="pointer clickable-cell text-right recs-list-mobile-left"  data-in-mode="list-mode"    data-recid="<?php echo $this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>"   data-hash="<?php echo 'mode='.$action_mode.'&recid=',$this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>" ><span class="show_in_mobile"  style="color: #212529 !important;"   >Amount paid: </span>&#8377;<?php \eBizIndia\_esc($this->body_template_data[$mode_index]['records'][$i_ul]['total_amount']); ?></td>

  <td data-label  class="pointer clickable-cell"  data-in-mode="list-mode"    data-recid="<?php echo $this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>"   data-hash="<?php echo 'mode='.$action_mode.'&recid=',$this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>" ><?php /*if($this->body_template_data[$mode_index]['records'][$i_ul]['amount_paid']>0){*/ \eBizIndia\_esc($this->body_template_data[$mode_index]['records'][$i_ul]['payment_mode']);/*}else{echo '&nbsp;';}*/ ?></td>
  

  
 
</tr>
