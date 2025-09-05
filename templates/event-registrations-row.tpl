<tr  id="record_row_<?php echo $this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>"   class="aborted-task responsive-task-cat"  >

	<?php $action_mode = 'view'; ?>

  <td class="text-center"  >
	<div class="">
		<a href="event-registrations.php#mode=<?php echo $action_mode; ?>&recid=<?php echo $this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>"  class="btn btn-xs btn-primary user-edit-action record-edit-button"   data-in-mode="list-mode"    data-recid="<?php echo $this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>"   data-rel='tooltip' title="View details" >
				<img src="images/view-icon.png" class="custom-button-small" alt="View">
			</a>
	</div>


	</td>

	<td data-label  class="pointer clickable-cell"  data-in-mode="list-mode"    data-recid="<?php echo $this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>"   data-hash="<?php echo 'mode='.$action_mode.'&recid=',$this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>" ><?php \eBizIndia\_esc($this->body_template_data[$mode_index]['records'][$i_ul]['booking_id']); ?></td>

  <td data-label   data-in-mode="list-mode"    data-recid="<?php echo $this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>"   data-hash="<?php echo 'mode='.$action_mode.'&recid=',$this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>" class="pointer clickable-cell pseudo-link"    >  
  
  <?php 
  	
  	\eBizIndia\_esc($this->body_template_data[$mode_index]['records'][$i_ul]['ev_name']);

  ?>
  </td>  

  <td data-label  class="pointer clickable-cell"  data-in-mode="list-mode"    data-recid="<?php echo $this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>"   data-hash="<?php echo 'mode='.$action_mode.'&recid=',$this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>" ><?php echo date('d-M-Y',strtotime($this->body_template_data[$mode_index]['records'][$i_ul]['ev_start_dt']) ); ?></td>

  <td data-label  class="pointer clickable-cell"  data-in-mode="list-mode"    data-recid="<?php echo $this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>"   data-hash="<?php echo 'mode='.$action_mode.'&recid=',$this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>" ><?php echo date('d-M-Y',strtotime($this->body_template_data[$mode_index]['records'][$i_ul]['registered_on']) ); ?></td>

  <td data-label  class="pointer clickable-cell text-right"  data-in-mode="list-mode"    data-recid="<?php echo $this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>"   data-hash="<?php echo 'mode='.$action_mode.'&recid=',$this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>" ><span class="show_in_mobile"  >Persons: </span><?php \eBizIndia\_esc($this->body_template_data[$mode_index]['records'][$i_ul]['no_of_tickets']); ?></td>

  <td data-label  class="pointer clickable-cell text-right"  data-in-mode="list-mode"    data-recid="<?php echo $this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>"   data-hash="<?php echo 'mode='.$action_mode.'&recid=',$this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>" ><span class="show_in_mobile"  >Total Amount: </span><?php echo $this->body_template_data[$mode_index]['records'][$i_ul]['total_amount']<=0?'<span class="free" >Free</span>':'&#8377;'.\eBizIndia\_esc($this->body_template_data[$mode_index]['records'][$i_ul]['total_amount'], true); ?></td>

  
 
</tr>
