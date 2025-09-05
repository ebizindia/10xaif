<?php 
if($this->body_template_data['mode'] == 'getList'){

	$mode_index=$this->body_template_data['mode'];
	$this->body_template_data[$mode_index]['records'];

	if($this->body_template_data[$mode_index]['filtertext']!=''){
			$filtertext='Filter';

			if($this->body_template_data[$mode_index]['filtercount']>1){
				$filtertext='Filters';
			}
		echo '<tr>
				<td align=\'center\' class="label-yellow padding-10 filter-row" colspan="6" id="rearch_data">',$this->body_template_data[$mode_index]['filtertext'],'  <a class="clear-filter" onclick="adfuncs.clearSearch();">Clear Filter</a>
				</td>
			</tr>';
	}

	if($this->body_template_data[$mode_index]['records_count']==0){
		echo "<tr><td  colspan='6' class='text-danger' align='center' >No records found.</td></tr>";

	}else{



		for($i_ul=0; $i_ul<$this->body_template_data[$mode_index]['records_count']; $i_ul++){

				require CONST_THEMES_TEMPLATE_INCLUDE_PATH.'ad-banners-row.tpl';

		} // end of for loop for user list creation

		if($this->body_template_data[$mode_index]['pagination_html']!=''){
			echo "<tr  ><td  colspan='6'  class='pagination-row'  >\n";
				echo $this->body_template_data[$mode_index]['pagination_html'];
			echo "</td></tr>\n";
		}

	}
}else{

?>
<div class="row">
    <div id='rec_list_container' class="col-12 mt-3 mb-2">
		<div class="card">
        <div class="card-body">
        	<div class="card-header-heading">
        	<div class="row">
                <div class="col-8 "><h4 class="row pg_heading_line_ht  ">Ad Banners&nbsp;<span id="heading_rec_cnt" style="color: #0c0c0cab;" >0</span> </h4></div>
                <div class="col-4 text-right">
                	<div class="row btns-user-add" style="float:right;">
	                	<a class="btn btn-primary toggle-search" href="javascript:void(0);">
							<img src="images/search-plus.png" class="custom-button fa-search-plus" alt="Search"><img src="images/search-minus.png" class="custom-button fa-search-minus" alt="Search">
						</a>
						<?php 
							if($this->body_template_data['can_add']===true){
						?>		
								<a href="ad-banners.php#mode=addrec" class="btn btn-success  record-add-button"  id="add-record-button"><img src="images/plus.png" class="custom-button-small" alt="Plus"> <span class="hide_in_mobile"  >Create Ad Banner</span> </a>
						
						<?php		
							}
						?>	
					</div>
				</div>
            </div>
        </div>
        <div class="panel-search d-none pb-2">
			<?php include CONST_THEMES_TEMPLATE_INCLUDE_PATH.'ad-banners-search-form-basic.tpl'; ?>
		</div>
		<div class="responsive-block-table for-cat">
		<div class="panel-body table-responsive">
			<table id="recs-list" class="table table-striped table-bordered table-hover">
				<thead class="thead">
					<tr>
						<th width="70px"><span>Action</span></th>
						<th class='sortable' id="colheader_name" ><span class="pull-left">Name</span><i  class='fa fa-sort pull-right' ></i></th>
						<th class='sortable' id="colheader_start-dt"><span class="pull-left">Start Date</span><i  class='fa fa-sort pull-right'></i></th>
						<th class="sortable" id="colheader_end-dt"><span class="pull-left">End Date</span><i  class='fa fa-sort pull-right'></i></th>
						<th class='' id="colheader_target-link" ><span class="pull-left">Target URL</span></th>
						<th width="100"  id="colheader_active"><span class="pull-left">Active</span></th>
					</tr>
				</thead>

				<tbody  id='userlistbox' >
				</tbody>
			</table>
		</div>
	</div>

	</div>
	</div>
</div>
	<div class="col-12 mt-3 mb-2 d-none"  id='rec_detail_add_edit_container'   >
		<?php require_once CONST_THEMES_TEMPLATE_INCLUDE_PATH.'ad-banners-add.tpl';	?>
	</div>


</div>
<?php

}

?>
