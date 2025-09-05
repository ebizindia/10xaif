<?php
/*$ip = \eBizIndia\getRemoteIP();
if($ip!=='49.37.10.113' && $ip!=='2405:201:800b:c8d7:94be:8752:cb6a:2b09'){
	echo '<h4>Hold on, the page is loading...</h4>';
}else*/ if($this->body_template_data['mode'] == 'getSummaryList'){

	$mode_index=$this->body_template_data['mode'];
	$this->body_template_data[$mode_index]['records'];

	if($this->body_template_data[$mode_index]['filtertext']!=''){
			$filtertext='Filter';

			if($this->body_template_data[$mode_index]['filtercount']>1){
				$filtertext='Filters';
			}
		echo '<tr>
				<td align=\'center\' class="label-yellow padding-10 filter-row" colspan="10" id="rearch_data">',$this->body_template_data[$mode_index]['filtertext'],'  <a class="clear-filter" onclick="evbkngfuncs.clearSummarySearch();">Clear Filter</a>
				</td>
			</tr>';
	}

	if($this->body_template_data[$mode_index]['records_count']==0){
		echo "<tr><td  colspan='10' class='text-danger' align='center' >No records found.</td></tr>";

	}else{



		for($i_ul=0; $i_ul<$this->body_template_data[$mode_index]['records_count']; $i_ul++){

				require CONST_THEMES_TEMPLATE_INCLUDE_PATH.'event-bookings-summary-row.tpl';

		} // end of for loop for user list creation

		if($this->body_template_data[$mode_index]['pagination_html']!=''){
			echo "<tr  ><td  colspan='10'  class='pagination-row'  >\n";
				echo $this->body_template_data[$mode_index]['pagination_html'];
			echo "</td></tr>\n";
		}

	}
}else if($this->body_template_data['mode'] == 'getList'){

	$mode_index=$this->body_template_data['mode'];
	$this->body_template_data[$mode_index]['records'];

	if($this->body_template_data[$mode_index]['filtertext']!=''){
			$filtertext='Filter';

			if($this->body_template_data[$mode_index]['filtercount']>1){
				$filtertext='Filters';
			}
		echo '<tr>
				<td align=\'center\' class="label-yellow padding-10 filter-row" colspan="11" id="rearch_data">',$this->body_template_data[$mode_index]['filtertext'],'  <a class="clear-filter" onclick="evbkngfuncs.clearSearch();">Clear Filter</a>
				</td>
			</tr>';
	}

	if($this->body_template_data[$mode_index]['records_count']==0){
		echo "<tr><td  colspan='11' class='text-danger' align='center' >No records found.</td></tr>";

	}else{



		for($i_ul=0; $i_ul<$this->body_template_data[$mode_index]['records_count']; $i_ul++){

				require CONST_THEMES_TEMPLATE_INCLUDE_PATH.'event-bookings-row.tpl';

		} // end of for loop for user list creation

		if($this->body_template_data[$mode_index]['pagination_html']!=''){
			echo "<tr  ><td  colspan='11'  class='pagination-row'  >\n";
				echo $this->body_template_data[$mode_index]['pagination_html'];
			echo "</td></tr>\n";
		}

	}
}else{

?>

<style>
.back-and-search::after{
display:table;
content:"";
clear:both;
}	
	
.back-and-search{width:200px;}
.back-and-search .btns-user-add{display: block; width: 90px;}
.back-and-search .back-to-list{display: block;  float: right;  width: 115px;}
.mobile_only{
	display: none;
}
#recs-list td .mobile_display{
display:block !important;
}

.form-control-height-adjust > .form-control{
	min-height: 10px !important;
	padding-bottom: 0px;
}
.form-control-height-adjust > .form-control:not(:first-child){
	padding-top: 0px;
}

@media screen and (max-width:1220px){
	.fix-sort-icon {
		top: -14px !important;
  		left: 23px;
	}
}

@media screen and (max-width:767px){
	.back-and-search .back-to-list{
	  width:47px;
	  }
	.recs-list-mobile-left{
		text-align: left !important;
	}
	.mobile_only{
		display: inline-block;
	}

}
@media screen and (max-width:760px){
	.responsive-block-table .table-responsive {
		overflow-x: hidden;
	}
}

@media screen and (min-width:1400px){
	.responsive-block-table{
		width: fit-content;
	}
}

</style>
<div class="row">
	<div class="col-12 mt-3 mb-2 d-none"  id='event_bookings_summary_list_container'  >
		<?php require_once CONST_THEMES_TEMPLATE_INCLUDE_PATH.'event-bookings-summary-list.tpl';	?>
	</div>
    <div id='rec_list_container' class="col-12 mt-3 mb-2 d-none">
		<?php require_once CONST_THEMES_TEMPLATE_INCLUDE_PATH.'event-bookings-list.tpl';	?>
	</div>
	<div class="col-12 mt-3 mb-2 d-none"  id='rec_detail_view_container'  >
		<?php require_once CONST_THEMES_TEMPLATE_INCLUDE_PATH.'event-bookings-view.tpl';	?>
	</div>
	
</div>
<?php

}

?>
