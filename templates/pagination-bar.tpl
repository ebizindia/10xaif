<?php if($this->body_template_data['fetch_template_data']['tot_pages'] > 1){ ?>
<div class="pagination-bar"  id="pagination-bar-container"  >
	<div class="col-sm-12 textalign-right">

	<?php //if($this->body_template_data['fetch_template_data']['tot_pages'] > 1){
		$link_data = $this->body_template_data['fetch_template_data']['link_data'];
		$page_link = $this->body_template_data['fetch_template_data']['page_link'];
		if(!$page_link)
			$page_link = '#';
		$first_page = 1;

		$previous_page = $this->body_template_data['fetch_template_data']['prev_page'] == '' ? 1 : $this->body_template_data['fetch_template_data']['prev_page'];

		$curr_page = $this->body_template_data['fetch_template_data']['curr_page'];

		$next_page = $this->body_template_data['fetch_template_data']['next_page'] == '' ? $this->body_template_data['fetch_template_data']['last_page'] : $this->body_template_data['fetch_template_data']['next_page'];

		$last_page = $this->body_template_data['fetch_template_data']['last_page'];

	?>
		<ul class="pagination float-right">

	  		<li class="page-item <?php if($curr_page == 1){echo 'disabled';}?>">
		  		<a href="<?php echo str_replace('<<page>>', '1', $page_link);?>" data-page="1" <?php echo $link_data;?> class='page-link'>
					<!-- <i class="fa fa-angle-double-left"  aria-hidden="true" ></i> -->
					<img src="images/double_left_blue.png" class="enabled_page_link" alt="First">
					<img src="images/double_left_disable.png" class="disabled_page_link" alt="First">
		  		</a>
	  		</li>

	  		<li class="page-item <?php if($curr_page == 1){echo 'disabled';}?>">
		  		<a href="<?php echo str_replace('<<page>>', $previous_page, $page_link);?>" data-page="<?php echo $previous_page;?>" <?php echo $link_data;?>  class='page-link' >
					<i class="fa fa-angle-left hidden-768"></i> <span class=" d-sm-inline-block d-md-inline-block">Previous</span>
				</a>
			</li>

			<?php for($pg = $this->body_template_data['fetch_template_data']['start_disp_pageno']; $pg <= $this->body_template_data['fetch_template_data']['end_disp_pageno']; $pg++){?>
			<li class="page-item <?php if($curr_page == $pg ){	echo 'active'
	  	;}else{echo 'hidden-480';}?>">
				<a href="<?php echo str_replace('<<page>>', $pg, $page_link);?>" data-page="<?php echo $pg;?>" <?php echo $link_data;?> class='page-link'  ><?php echo $pg;?></a>
			</li>
			<?php } ?>

	  		<li class="page-item <?php if($curr_page == $last_page){echo 'disabled';}?>">
				<a href="<?php echo str_replace('<<page>>', $next_page, $page_link);?>" data-page="<?php echo $next_page;?>" <?php echo $link_data;?> class='page-link'  >
					<span class=" d-sm-inline-block d-md-inline-block"> Next</span> <i class="fa fa-angle-right hidden-768"></i>
				</a>
	  		</li>

			<li class="page-item <?php if($curr_page == $last_page){echo 'disabled';}?>">
				<a href="<?php echo str_replace('<<page>>', $last_page, $page_link);?>" data-page="<?php echo $last_page;?>" <?php echo $link_data;?>  class='page-link'  >
					<!-- <i class="fa fa-angle-double-right"  ></i> -->
					<img src="images/double_right_blue.png" class="enabled_page_link" alt="Last">
					<img src="images/double_right_disable.png" class="disabled_page_link" alt="Last">
				</a>
			</li>

		</ul>
	<?php //}?>
	</div>
</div>
<?php }?>
