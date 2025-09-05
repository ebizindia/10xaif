<?php 
$status_css_cls='';
if($this->body_template_data[$mode_index]['records'][$i_ul]['active'] == 'n'){
	$status_css_cls='overdue-task'; //class name not meaningful but exists in css
}else if(date('Y-m-d')>=$this->body_template_data[$mode_index]['records'][$i_ul]['display_start_date'] && date('Y-m-d')<=$this->body_template_data[$mode_index]['records'][$i_ul]['display_end_date']){
	$status_css_cls='completed-task'; //class name not meaningful but exists in css
}
?>
<tr  id="record_row_<?php echo $this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>"   class="responsive-task-cat <?php echo $status_css_cls;?>"  >
	<?php $action_mode = 'edit'; ?>
	<td class="text-center"  >
		<div class="">
			<a href="manage-offers.php#mode=<?php echo $action_mode; ?>&rec_id=<?php echo $this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>"  class="btn btn-xs btn-success user-edit-action record-edit-button" data-in-mode="list-mode" data-rec_id="<?php echo $this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>" data-rel='tooltip' title="Edit details" ><img src="images/edit-white.png" class="custom-button-small" alt="Edit"></a>
		</div>
	</td>
	<td data-label="" data-in-mode="list-mode" data-rec_id="<?php echo $this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>"   data-hash="<?php echo 'mode='.$action_mode.'&rec_id=',$this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>" class="pointer clickable-cell pseudo-link" >
	<?php \eBizIndia\_esc($this->body_template_data[$mode_index]['records'][$i_ul]['title']);?>
	</td>
	<td data-label="" data-in-mode="list-mode" data-rec_id="<?php echo $this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>"   data-hash="<?php echo 'mode='.$action_mode.'&rec_id=',$this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>" class="pointer clickable-cell pseudo-link" >
	<?php \eBizIndia\_esc($this->body_template_data[$mode_index]['records'][$i_ul]['category_name']);?>
	</td>
	
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
	<td  data-label="Active: " class="hidden-480x <?php echo $status_cls;?> pointer clickable-cell"  data-in-mode="list-mode" data-rec_id="<?php echo $this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>"   data-hash="<?php echo 'mode='.$action_mode.'&rec_id=',$this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>">
		<span class='<?php echo $status_span_cls; ?> pointer' ><?php \eBizIndia\_esc($status_text); ?></span >
	</td>

	<td  data-label="MOU: " class="hidden-480x "  data-in-mode="list-mode" >
		<a href="<?php if($this->body_template_data[$mode_index]['records'][$i_ul]['mou']!=''){echo CONST_DISCOUNT_OFFER_MOU_URL_PATH.$this->body_template_data[$mode_index]['records'][$i_ul]['mou'];}else{echo '#';} ?>" target="_blank"  rel="noopener"  ><?php if($this->body_template_data[$mode_index]['records'][$i_ul]['mou']!=''){echo 'Open';} ?></a >&nbsp;
	</td>

</tr>