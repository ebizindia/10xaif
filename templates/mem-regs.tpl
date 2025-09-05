<?php //print_r($this->body_template_data);
if($this->body_template_data['mode'] == 'getList'){

	$mode_index=$this->body_template_data['mode'];
	$this->body_template_data[$mode_index]['records'];

	if($this->body_template_data[$mode_index]['filtertext']!=''){
			$filtertext='Filter';

			if($this->body_template_data[$mode_index]['filtercount']>1){
				$filtertext='Filters';
			}
		echo '<tr>
				<td align=\'center\' class="label-yellow padding-10 filter-row" colspan="8" id="rearch_data">',$this->body_template_data[$mode_index]['filtertext'],' <a class="clear-filter" onclick="memregfuncs.clearSearch();">Clear Filter</a>
				</td>
			</tr>';
	}

	if($this->body_template_data[$mode_index]['records_count']==0){
		echo "<tr><td  colspan='8' class='text-danger' align='center' >No records found.</td></tr>";

	}else{



		for($i_ul=0; $i_ul<$this->body_template_data[$mode_index]['records_count']; $i_ul++){

				require CONST_THEMES_TEMPLATE_INCLUDE_PATH.'mem-regs-row.tpl';

		} // end of for loop for user list creation

		if($this->body_template_data[$mode_index]['pagination_html']!=''){
			echo "<tr  ><td  colspan='8'  class='pagination-row'  >\n";
				echo $this->body_template_data[$mode_index]['pagination_html'];
			echo "</td></tr>\n";
		}

	}
}else{

?>
<div class="row">
    <div id='user_list_container' class="col-12 mt-3 mb-2">
		<div class="card">
        <div class="card-body">
        	<div class="card-header-heading">
        	<div class="row">
                <div class="col-8 "><h4 class="row pg_heading_line_ht  ">Registrations&nbsp;<span><span id="heading_rec_cnt" style="color: #0c0c0cab;" >0</span><?php if($this->body_template_data['allow_export']===true){ ?><a id="export_members" href="" download="members.csv" class="nopropagate ml-1 d-none" ><img src="images/dnld.png" alt="Export members list as CSV" width="22" height="22"  ></a><?php } ?></span> </h4></div>
                <div class="col-4 text-right">
                	<div class="row btns-user-add" style="float:right;">
	                	<a class="btn btn-primary toggle-search" href="javascript:void(0);">
							<img src="images/search-plus.png" class="custom-button fa-search-plus" alt="Search"><img src="images/search-minus.png" class="custom-button fa-search-minus" alt="Search">
						</a>
					</div>
				</div>
            </div>
        </div>
        <div class="panel-search d-none pb-2">
			<?php include CONST_THEMES_TEMPLATE_INCLUDE_PATH.'mem-regs-search-form-basic.tpl'; ?>
		</div>
		<div class="responsive-block-table for-cat">
		<div class="panel-body table-responsive">
			<table id="users-list" class="table table-striped table-bordered table-hover">
				<thead class="thead">
					<tr>
						<th width="70px"><span>Action</span></th>
						<th class='sortable' id="colheader_name" ><span class="pull-left">Name</span><i  class='fa fa-sort pull-right' ></i></th>
						<th class='sortable' id="colheader_email"><span class="pull-left">Email</span><i  class='fa fa-sort pull-right'></i></th>
						<th class="" id="colheader_mobile"><span class="pull-left">Mobile</span></th>
						<th class="text-right" id="colheader_batch-no"><span class="pull-right">Batch</span></th>
						<th class="sortable" id="colheader_regon"><span class="pull-left">Registered On</span><i  class='fa fa-sort pull-right' ></th>
						<th width="100"  id="colheader_active"><span class="pull-left">Status</span></th>
						<th class="" id="colheader_statusdt"><span class="pull-left">Status Updated On</span></th>
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
	<div class="col-12 mt-3 mb-2 d-none"  id='user_detail_view_container'  >
		<?php require_once CONST_THEMES_TEMPLATE_INCLUDE_PATH.'mem-regs-view.tpl';	?>
	</div>
	<div class="col-12 mt-3 mb-2 d-none"  id='user_detail_add_edit_container'   >
		<?php require_once CONST_THEMES_TEMPLATE_INCLUDE_PATH.'mem-regs-add.tpl';	?>
	</div>


</div>
<?php

}

?>
