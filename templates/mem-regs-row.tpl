<?php 
$status_css_cls='';
if(strtolower($this->body_template_data[$mode_index]['records'][$i_ul]['status']) == 'approved'){
	$status_css_cls='completed-task'; //class name not meaningful but exists in css
}else if(strtolower($this->body_template_data[$mode_index]['records'][$i_ul]['status']) == 'disapproved'){
	$status_css_cls='overdue-task'; //class name not meaningful but exists in css
}else if(strtolower($this->body_template_data[$mode_index]['records'][$i_ul]['payment_status']) == 'paid'){
	$status_css_cls='paid-reg'; //class name not meaningful but exists in css
}
?>
<tr  id="record_row_<?php echo $this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>"   class="responsive-task-cat <?php echo $status_css_cls;?>" >

	<?php $action_mode = 'view'; ?>

  <td class="text-center"  >
	<div class="">
		<?php 
			if( $this->body_template_data[$mode_index]['others_edit']===true &&  ($this->body_template_data[$mode_index]['records'][$i_ul]['status']==='New' || $this->body_template_data[$mode_index]['records'][$i_ul]['status']==='Disapproved') ){
				 $action_mode='edit';
		?>
			<a href="mem-regs.php#mode=edit&recid=<?php echo $this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>"  class="btn btn-xs btn-success user-edit-action record-edit-button"   data-in-mode="list-mode"    data-recid="<?php echo $this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>"   data-rel='tooltip' title="Edit details" >
				<img src="images/edit-white.png" class="custom-button-small" alt="Edit">
			</a>
		<?php 
			}else{
		?>
			<a href="mem-regs.php#mode=view&recid=<?php echo $this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>"  class="btn btn-xs btn-primary user-edit-action record-edit-button"   data-in-mode="list-mode"    data-recid="<?php echo $this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>"   data-rel='tooltip' title="View details" >
				<img src="images/view-icon.png" class="custom-button-small" alt="View">
			</a>
		<?php		

			}
		?>
	</div>


</td>

  <td data-label   data-in-mode="list-mode"    data-recid="<?php echo $this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>"   data-hash="<?php echo 'mode='.$action_mode.'&recid=',$this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>" class="pointer clickable-cell pseudo-link"    >
  <?php 
  	if($this->body_template_data[$mode_index]['records'][$i_ul]['dnd']==='y'){
  ?>		
  	<img src="images/red-flag.png" width="16" height="16" alt="DND marked" title="Marked as DND" style="display: inline-block; position: relative; margin-left: 0; margin-right: 0;"  >
  <?php 
  	}

  	echo $this->body_template_data[$mode_index]['records'][$i_ul]['name'].($this->body_template_data[$mode_index]['records'][$i_ul]['batch_no']!=''? '<span class="show_in_mobile" > ('.\eBizIndia\_esc($this->body_template_data[$mode_index]['records'][$i_ul]['batch_no'], true).')</span>':'' );

  ?>
  </td>

  <?php if(!empty($this->body_template_data[$mode_index]['records'][$i_ul]['email'])){ ?>
  <td data-label  class="pointer clickable-cell"  data-in-mode="list-mode"    data-recid="<?php echo $this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>"    ><a href="mailto:<?php echo $this->body_template_data[$mode_index]['records'][$i_ul]['email']; ?>" style="white-space: nowrap;" class="nopropagate"  ><img src="images/email.png" alt="email" style="margin-right: 3px; text-decoration: none;" ><?php echo $this->body_template_data[$mode_index]['records'][$i_ul]['email']; ?></a></td>
<?php }else{ ?>
	<td data-label  class="pointer clickable-cell"  data-in-mode="list-mode"    data-recid="<?php echo $this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>"   data-hash="<?php echo 'mode='.$action_mode.'&recid=',$this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>" ><?php echo $this->body_template_data[$mode_index]['records'][$i_ul]['email']; ?></td>
<?php } ?>	
	

	<?php 
		
		if(!empty($this->body_template_data[$mode_index]['records'][$i_ul]['mobile'])){ 
				$whatsapp_num = $this->body_template_data[$mode_index]['records'][$i_ul]['mobile'];
				if(!preg_match("/^[+0]/", $whatsapp_num))
					$whatsapp_num = '+'.$this->body_template_data['country_code'].$whatsapp_num;
	?>
  	<td data-label  class="pointer clickable-cell"  data-in-mode="list-mode"    data-recid="<?php echo $this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>" style="white-space: nowrap !important; "  ><a href="https://wa.me/<?php echo $whatsapp_num; ?>"   class="nopropagate" target="_blank" rel="noopener"  ><img src="images/whatsapp.png" alt="whatsapp" style="margin-right:10px;text-decoration: none;margin-top:-4px;" ></a>
  		<a href="tel:<?php echo $whatsapp_num; ?>" style="white-space: nowrap;width: 100% !important;"  class="nopropagate" rel="noopener"  ><?php \eBizIndia\_esc($this->body_template_data[$mode_index]['records'][$i_ul]['mobile']); ?><img src="images/phone.png" alt="phone" style="text-decoration: none;  margin-top: -3px; margin-left: 2px;" ></a></td>
  <?php }else{ ?>
  	<td data-label  class="pointer clickable-cell"  data-in-mode="list-mode"    data-recid="<?php echo $this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>"   data-hash="<?php echo 'mode='.$action_mode.'&recid=',$this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>" >&nbsp;</td>
  <?php } ?>	

  <td data-label  class="pointer clickable-cell text-right"  data-in-mode="list-mode"    data-recid="<?php echo $this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>"   data-hash="<?php echo 'mode='.$action_mode.'&recid=',$this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>" ><?php echo $this->body_template_data[$mode_index]['records'][$i_ul]['batch_no']; ?></td>

  <td data-label  class="pointer clickable-cell"  data-in-mode="list-mode"    data-recid="<?php echo $this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>"   data-hash="<?php echo 'mode='.$action_mode.'&recid=',$this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>" ><?php echo date('d-m-Y',strtotime($this->body_template_data[$mode_index]['records'][$i_ul]['reg_on']) ); ?></td>

  <?php
		$status_cls='text-primary';
		if($this->body_template_data[$mode_index]['records'][$i_ul]['status']=='Approved'){
			$status_cls='text-success';
		}else if($this->body_template_data[$mode_index]['records'][$i_ul]['status']=='Disapproved'){
			$status_cls='text-danger';
		}
		
	?>
 		<td  data-label class="hidden-480 <?php echo $status_cls;?> pointer clickable-cell"  data-in-mode="list-mode"    data-recid="<?php echo $this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>"   data-hash="<?php echo 'mode='.$action_mode.'&recid=',$this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>"><?php \eBizIndia\_esc($this->body_template_data[$mode_index]['records'][$i_ul]['status']); ?>
		</td>

	<?php
		$status_updated_on = '';
		if($this->body_template_data[$mode_index]['records'][$i_ul]['status']==='Approved'){
			$status_updated_on = date('d-m-Y',strtotime($this->body_template_data[$mode_index]['records'][$i_ul]['approved_on']) );
		}else if($this->body_template_data[$mode_index]['records'][$i_ul]['status']==='Disapproved'){
			$status_updated_on = date('d-m-Y',strtotime($this->body_template_data[$mode_index]['records'][$i_ul]['disapproved_on']) );
		}
	?>	

		<td data-label  class="pointer clickable-cell"  data-in-mode="list-mode"    data-recid="<?php echo $this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>"   data-hash="<?php echo 'mode='.$action_mode.'&recid=',$this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>" ><?php echo $status_updated_on; ?></td>

</tr>
