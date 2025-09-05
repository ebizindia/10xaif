<tr  id="record_row_<?php echo $this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>"   class="aborted-task responsive-task-cat"  >

	<?php $action_mode = 'edit'; ?>

  <td class="text-center"  >
	<div class="">
		<a href="events.php#mode=<?php echo $action_mode; ?>&recid=<?php echo $this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>"  class="btn btn-xs btn-success user-edit-action record-edit-button"   data-in-mode="list-mode"    data-recid="<?php echo $this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>"   data-rel='tooltip' title="Edit details" >
				<img src="images/edit-white.png" class="custom-button-small" alt="Edit">
			</a>
	</div>


	</td>

  <td data-label   data-in-mode="list-mode"    data-recid="<?php echo $this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>"   data-hash="<?php echo 'mode='.$action_mode.'&recid=',$this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>" class="pointer clickable-cell pseudo-link"    >  
  
  <?php 
  	
  	\eBizIndia\_esc($this->body_template_data[$mode_index]['records'][$i_ul]['name']);

  ?>
  </td>  

  <td data-label  class="pointer clickable-cell"  data-in-mode="list-mode"    data-recid="<?php echo $this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>"   data-hash="<?php echo 'mode='.$action_mode.'&recid=',$this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>" ><?php \eBizIndia\_esc($this->body_template_data[$mode_index]['records'][$i_ul]['venue']); ?></td>

  <td data-label  class="pointer clickable-cell"  data-in-mode="list-mode"    data-recid="<?php echo $this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>"   data-hash="<?php echo 'mode='.$action_mode.'&recid=',$this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>" ><?php echo date('d-M-Y',strtotime($this->body_template_data[$mode_index]['records'][$i_ul]['start_dt']) ); ?></td>

  <td data-label  class="pointer clickable-cell"  data-in-mode="list-mode"    data-recid="<?php echo $this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>"   data-hash="<?php echo 'mode='.$action_mode.'&recid=',$this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>" ><?php echo date('d-M-Y',strtotime($this->body_template_data[$mode_index]['records'][$i_ul]['end_dt']) ); ?></td>

  <?php
		$status_cls='text-danger';
		$status_span_cls='status-notlive';
		$status_text = 'No';
		if($this->body_template_data[$mode_index]['records'][$i_ul]['active']=='y'){
			$status_cls='text-success';
			$status_span_cls='status-live';
			$status_text = 'Yes';
		}


	?>

	<td  data-label class="hidden-480 <?php echo $status_cls;?> pointer clickable-cell"  data-in-mode="list-mode"    data-recid="<?php echo $this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>"   data-hash="<?php echo 'mode='.$action_mode.'&recid=',$this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>"><span class="show_in_mobile" style="color: #212529 !important;"  >Active: </span>
		<span class='<?php echo $status_span_cls; ?> pointer' ><?php \eBizIndia\_esc($status_text); ?></span >
	</td>

	<?php 
		$status_cls='text-danger';
		$status_span_cls='status-notlive';
		$reg_on_text = 'No';
		if($this->body_template_data[$mode_index]['records'][$i_ul]['active']=='y' && $this->body_template_data[$mode_index]['records'][$i_ul]['reg_active']==='y' && $this->body_template_data[$mode_index]['records'][$i_ul]['reg_start_dt']!=''){
			$today = new DateTime();
			$dt1 = new \DateTime($this->body_template_data[$mode_index]['records'][$i_ul]['reg_start_dt'].' 00:00:00');
			if($this->body_template_data[$mode_index]['records'][$i_ul]['reg_end_dt']!=''){
				$dt2 = new \DateTime($this->body_template_data[$mode_index]['records'][$i_ul]['reg_end_dt']. ' 23:59:59');
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

	?>

	<td  data-label class="hidden-480 <?php echo $status_cls;?> pointer clickable-cell"  data-in-mode="list-mode"    data-recid="<?php echo $this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>"   data-hash="<?php echo 'mode='.$action_mode.'&recid=',$this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>"><span class="show_in_mobile"  style="color: #212529 !important;"   >Registration is On: </span>
		<span class='<?php echo $status_span_cls; ?> pointer' ><?php \eBizIndia\_esc($reg_on_text); ?></span >
	</td>
 
</tr>
