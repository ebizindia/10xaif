<?php 
	$img = '';

	if($this->body_template_data[$mode_index]['records'][$i_ul]['img']!=''){
		$img = CONST_DISCOUNT_OFFER_IMG_URL_PATH.$this->body_template_data[$mode_index]['records'][$i_ul]['img'] ;
	}else{
		$img = CONST_DOFF_NOIMAGE_FILE ;
	}

?>
<div class="user_block">
	<div class="grid_view_list_block pointer clickable-cell " data-hash="mode=get-offers&cid=<?php echo urlencode($this->body_template_data[$mode_index]['records'][$i_ul]['id']); ?>" >
		<div class="grid_view_img">
			<a href="#mode=get-offers&cid=<?php echo urlencode($this->body_template_data[$mode_index]['records'][$i_ul]['id']); ?>" data-in-mode="list-mode" data-recid="<?php echo \eBizIndia\_esc($this->body_template_data[$mode_index]['records'][$i_ul]['id'], true); ?>" data-rel="tooltip" title="View Offers" class="nopropagate">
				<table>
					<tr>
						<td class="image_container">
							<img src="<?php echo $img; ?>" alt="" class="prof_img">
						</td>
					</tr>
					<tr>
						<td class="grid_view_name"><h3><?php echo \eBizIndia\_esc($this->body_template_data[$mode_index]['records'][$i_ul]['name'], true); ?></h3></td>
					</tr>
				</table>
			</a>
		</div>
	</div>
</div>

