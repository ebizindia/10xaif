<tr  id="record_row_<?php echo $this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>"   class="responsive-task-cat "  >

	<?php $action_mode = 'edit'; ?>

  <td class="text-center"  >
	<div class="">
		<a href="sectors.php#mode=<?php echo $action_mode; ?>&recid=<?php echo $this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>"  class="btn btn-xs btn-success user-edit-action record-edit-button"   data-in-mode="list-mode"    data-recid="<?php echo $this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>"   data-rel='tooltip' title="Edit details" >
				<img src="images/edit-white.png" class="custom-button-small" alt="Edit">
			</a>

		<a href="#"  class="btn btn-xs btn-danger record-delete-button ml-3"   data-in-mode="list-mode"    data-recid="<?php echo $this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>"    data-sector="<?php echo \eBizIndia\_esc($this->body_template_data[$mode_index]['records'][$i_ul]['sector'], true); ?>"   data-rel='tooltip' title="Delete sector" >
				<img src="images/delete-white.png" class="custom-button-small" alt="Delete">
			</a>	
	</div>


	</td>

  <td data-label="Name: " data-in-mode="list-mode"    data-recid="<?php echo $this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>"   data-hash="<?php echo 'mode='.$action_mode.'&recid=',$this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>" class="pointer clickable-cell pseudo-link"    >
  
  <?php 
  	
  	\eBizIndia\_esc($this->body_template_data[$mode_index]['records'][$i_ul]['sector']);

  ?>
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

	<td  data-label="Active: " class="hidden-480 <?php echo $status_cls;?> pointer clickable-cell"  data-in-mode="list-mode"    data-recid="<?php echo $this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>"   data-hash="<?php echo 'mode='.$action_mode.'&recid=',$this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>">
		<span class='<?php echo $status_span_cls; ?> pointer' ><?php \eBizIndia\_esc($status_text); ?></span >
	</td>
 
</tr>
