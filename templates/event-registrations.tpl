<?php
/*$ip = \eBizIndia\getRemoteIP();
if($ip!='2405:201:800b:c8d7:7fcf:762b:6608:e407' && $ip!='49.37.35.178'){
	echo '<h3>Under maintenance...</h3>';
}else */if($this->body_template_data['mode'] == 'getBookings'){

	$mode_index=$this->body_template_data['mode'];
	$this->body_template_data[$mode_index]['records'];

	if($this->body_template_data[$mode_index]['filtertext']!=''){
			$filtertext='Filter';

			if($this->body_template_data[$mode_index]['filtercount']>1){
				$filtertext='Filters';
			}
		echo '<tr>
				<td align=\'center\' class="label-yellow padding-10 filter-row" colspan="7" id="rearch_data">',$this->body_template_data[$mode_index]['filtertext'],'  <a class="clear-filter" onclick="evregfuncs.clearSearch();">Clear Filter</a>
				</td>
			</tr>';
	}

	if($this->body_template_data[$mode_index]['records_count']==0){
		echo "<tr><td  colspan='7' class='text-danger' align='center' >No records found.</td></tr>";

	}else{



		for($i_ul=0; $i_ul<$this->body_template_data[$mode_index]['records_count']; $i_ul++){

				require CONST_THEMES_TEMPLATE_INCLUDE_PATH.'event-registrations-row.tpl';

		} // end of for loop for user list creation

		if($this->body_template_data[$mode_index]['pagination_html']!=''){
			echo "<tr  ><td  colspan='7'  class='pagination-row'  >\n";
				echo $this->body_template_data[$mode_index]['pagination_html'];
			echo "</td></tr>\n";
		}

	}
}elseif($this->body_template_data['mode'] == 'getList'){

	$mode_index=$this->body_template_data['mode'];
	$this->body_template_data[$mode_index]['records'];

	if($this->body_template_data[$mode_index]['filtertext']!=''){
			$filtertext='Filter';

			if($this->body_template_data[$mode_index]['filtercount']>1){
				$filtertext='Filters';
			}
		echo '<div class="row">
				<div class="col-sm-12 col-md-12 col-lg-12 label-yellow text-center" style="white-space: normal !important;" id="rearch_data"   >
					<div class="filter-block" >
						',$this->body_template_data[$mode_index]['filtertext'],' <a class="clear-filter" onclick="evregfuncs.clearEventSearch();">Clear Filter</a>
					</div>	
				</div>
			</div>';
	}

	if($this->body_template_data[$mode_index]['records_count']==0){
		echo "<div class='row' ><div class='col-sm-12 col-md-12 col-lg-12 text-danger' align='center'  >No events were found.</div> </div>";

	}else{
		echo '<div class="row"  > 
				<div class="col-sm-12 col-md-12 col-lg-12"> ';
		for($i_ul=0; $i_ul<$this->body_template_data[$mode_index]['records_count']; $i_ul++){

				require CONST_THEMES_TEMPLATE_INCLUDE_PATH.'event-available-row.tpl';

		} // end of for loop for user list creation
		echo ' </div> 
		</div>';


		if($this->body_template_data[$mode_index]['pagination_html']!=''){
			echo "<div class='row'  ><div  class='col-sm-12 col-md-12 col-lg-12 pagination-row'  >\n";
				echo $this->body_template_data[$mode_index]['pagination_html'];
			echo "</div></div>\n";
		}

	}
}else{

?>
<style>
	.basic-search-box{  /* Overriding the class defined in custom.css */
		border: 1px solid #e5e5e5;
	    padding: 5px;
	    border-radius: 5px;
	    background: #fbfbfb;
	}

	.filter-block {
	  background: #fbfbfb;
	  padding: 5px;
	  border-radius: 5px;
	  margin: 0px 0 20px;
	  border: 1px solid #e5e5e5;
	}


	/*List-of-events*/

	.list_of_events_background{
		background:#f2f2f2;
		padding:5px;
		margin-bottom:25px;
	}
	.table_leave_application {
	  width: 100%;
	  border-collapse: collapse;
	}
	.table_leave_application td {
	  border: 1px solid #d2d1d1;
	  padding: 5px;
	  background: #fff;
	  vertical-align: top;
	}
	.events_right_heading{
	font-weight:600;
	}
	.events_left{
	/*background: #eee;*/
	  padding: 5px;
	  float:left;
	  font-weight:600;
	width:20%;
	/*margin-bottom:5px;*/
	}
	.events_right{
	background:#f9f9f9;
	  padding: 5px;
	width:100%;
	/*margin-bottom: 5px;*/
	}
	.float_event_list{
	border-bottom:1px solid #d0d0d0;
	}
	.float_event_list::after{
	display:table;
	content:"";
	clear:both;
	}
	.free_event{
	color:#ff3300;
	}
	.events_Register{
	  margin-bottom: 5px;
	  clear: both;	  
	}
	.events_left img{
	max-width:100%;
	}
	.events_right img{
	max-width:100%;
	}


	.table_leave_application .float_event_list:last-child{
	border-bottom:0px solid #c7c7c7;
	}
	.ticket_booked {
	  /*float: right;
	  margin-top: 0px;	  
	  text-align: right;
	  width: 100%;
	  clear: both;*/
	  padding-left: 15px;
	  font-style: italic;
	  font-size: 13px;
	  margin-bottom: 10px;	
	}
	.ticket_booked span{
	  color:#ff3300;
	  font-weight:600;
	}
	.no_border{
	border:0px solid #fff !important;
	}
	@media screen and (max-width:580px){
		.event_name_responsive{
		float:none;
		width:100% !important;
		}	
	}


	/*List-of-events*/

	@media screen and (max-width:576px){
		.control-label.col-xs-12.col-sm-6.col-lg-2 {
		  width: 43%;
		  line-height: 25px;
		}
		.col-xs-12.col-sm-6.col-lg-4 {
		  width: 55%;
		  line-height: 25px;
		}
	}

	@media screen and (min-width:761px) and (max-width:850px){
		.show_in_mobile {
			display: inline;
		}
	}

/********************** css for event registration row ************************/
.EventRegistrations{
display:table;
width:100%;
border-collapse:collapse;
}
.Event-Registrations-Table-Cel-One{
display:table-cell;
width:400px;
vertical-align:top;
text-align:left;
}

.Event-Registrations-Table-Cel-Two{
display:table-cell;
vertical-align:top;
background:#fff;
width:100%;
}
.img_placeholder{
width: 400px;
  max-width: 400px;
  max-height: 400px;
  height: auto;
  overflow: hidden;
}

.Event-Registrations-Table-Cel-Two .heading {
font-weight: 600;
font-size: 17px;
line-height: 22px;
color: #2f2f2f;
background: #e6f1ff;
padding: 5px 5px 5px 15px;
padding-left: 15px;
}
/*
.Event-Registrations-Table-Cel-Two .event_details_txt{
	color:#2277b7 !important;
}
*/

.Event-Registrations-Table-Cel-Two .desc {
font-weight: normal;
font-size:17px;
line-height:20px;
color:#2f2f2f;
margin-top: 10px;
padding:0 0 0 15px;
}

.Event-Registrations-Table-Cel-Two .text-danger {
    font-weight: normal;
    font-size: 17px;
    line-height: 20px;
    color: #2f2f2f;
    margin-top: 0px;
    padding: 0 0 0 15px;
}

.Event-Registrations-Container {
border: 2px solid #7d7d7d;    
margin-bottom:15px;
}

.Event-Registrations-Table-Cel-Two .checkmark{
margin-right:10px;
}

.Event-Registrations-Table-Cel-Two .free_event_box{
color: #dc3545;
font-size: 18px;
font-weight: 700;
padding: 5px;
width: 100px;
margin: 5px 0 0 10px;
}


@media screen and (max-width:991px){
	.Event-Registrations-Table-Cel-One {
		display: block;
		width: 100%;
	}
	.Event-Registrations-Table-Cel-Two {
		display: block;
		width: 100%;
	}
	
	.Event-Registrations-Table-Cel-Two .closed{	   
	margin-bottom:15px;
	}
	.img_placeholder {
	width: 400px;
    max-height: auto;
    margin: 0 auto;
    overflow: hidden;
	}
	.img_placeholder img {
		max-width: 100% !important;
		width: 400px;
	}
	
}

@media screen and (max-width:414px){
	.img_placeholder {
	width: 100%;    
    text-align: center;
	}
	.img_placeholder img {
		max-width: 100% !important;
		width: 390px;
	}
}

@media screen and (max-width:400px){
	.img_placeholder {
		width: 100%;
	}
	.img_placeholder img {
		max-width: 100% !important;
		width: 353px;
	}
}


@media screen and (max-width:375px){
	.img_placeholder {	
	}
	.img_placeholder img {
		max-width: 100% !important;
		width: 353px;
	}
}

@media screen and (max-width:350px){
	.btns-user-add .btn{
	padding: .375rem .58rem !important;
	}
}

/********************** css for event registration row ************************/

</style>

<div class="row">
    <div id='event_available_list_container' class="col-12 mt-3 mb-2">
		<?php require_once CONST_THEMES_TEMPLATE_INCLUDE_PATH.'event-available-list.tpl';	?>
	</div>
    <div id='rec_list_container' class="col-12 mt-3 mb-2 d-none">
		<?php require_once CONST_THEMES_TEMPLATE_INCLUDE_PATH.'event-registrations-list.tpl';	?>
	</div>
	<div class="col-12 mt-3 mb-2 d-none"  id='rec_detail_view_container'  >
		<?php require_once CONST_THEMES_TEMPLATE_INCLUDE_PATH.'event-registrations-view.tpl';	?>
	</div>
	<div class="col-12 mt-3 mb-2 d-none"  id='rec_detail_add_edit_container'   >
		<?php require_once CONST_THEMES_TEMPLATE_INCLUDE_PATH.'event-registrations-add.tpl';	?>
	</div>
	<div class="col-12 mt-3 mb-2 d-none"  id='reg_thanks_container'   >
		<?php require_once CONST_THEMES_TEMPLATE_INCLUDE_PATH.'event-registrations-thanks.tpl';	?>
	</div>
	


</div>

<script >
	window.onload = function(){
		$(document).ready(function(){
			$('#event_available_list_container').on(common_js_funcs.click_event,'.searched_elem .remove_filter' ,evregfuncs.clearEventSearch);
		});	
	}

</script>

<?php

}

?>
