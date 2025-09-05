<?php 
if($this->body_template_data['mode'] == 'getList'){

	$mode_index=$this->body_template_data['mode'];
	$this->body_template_data[$mode_index]['records'];
	$_is_admin = false;
	$colspan=7;
	if($this->body_template_data['cu_role']==='ADMIN'){
		$_is_admin = true;
		$colspan=9;
	}

	if($this->body_template_data[$mode_index]['filtertext']!=''){
			$filtertext='Filter';

			if($this->body_template_data[$mode_index]['filtercount']>1){
				$filtertext='Filters';
			}
		echo '<div class="row"  style="clear:both;"  >
				<div class="col-sm-12 col-md-12 col-lg-12 label-yellow text-center" style="white-space: normal !important;" id="rearch_data"   >
					<div class="filter-block" >
						',$this->body_template_data[$mode_index]['filtertext'],' <a class="clear-filter" onclick="usersfuncs.clearSearch();">Clear Filter</a>
					</div>	
				</div>
			</div>';	
	}

	if($this->body_template_data[$mode_index]['records_count']==0){
		echo "<div class='row' ><div class='col-sm-12 col-md-12 col-lg-12 text-danger' align='center'  >No records found</div> </div>";

	}else{
		echo '<div class="row"  > 
				<div class="col-sm-12 col-md-12 col-lg-12"> ';
		for($i_ul=0; $i_ul<$this->body_template_data[$mode_index]['records_count']; $i_ul++){

				require CONST_THEMES_TEMPLATE_INCLUDE_PATH.'users-row.tpl';

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
	
    .company_truncate {
	  width: 200px;
	  margin: 5px auto;
	  overflow: hidden;
	}

	.company_truncate > div {
	  text-overflow: ellipsis;
	  overflow: hidden;
	  white-space: nowrap;
	}

    .member_list_block{
	background: #f4f4f4;
	padding: 15px;
	border-radius: 10px;
	text-align:center;
	border-bottom: 3px solid #3b82bf;
	margin-bottom:20px;
	transition:.3s ease-in-out;
	}
	.member_list_block:hover{
	background: #edeff4;
	transition:.3s ease-in-out;
	
	}

	.member_list_block.inactive_member{
		border-bottom: 3px solid #b7b5b5 !important;

	}

	.member_list_block.inactive_member *{
		color: #b5b5b5 !important;
	}

	
	.member_list_block.inactive_member .prof_img{
		opacity: 0.6;
	}

	.member_list_block.admin_member{
		border-bottom: 3px solid #73ad16;
	}

	.member_img{    
    width: 120px;
    height: 120px !important;
    margin: auto;
    border-radius: 50%;
    overflow: hidden;
    box-shadow: 0 0 5px #aca6a6b2;
    border: 5px solid #bd5f4b;
    display: flex;
    justify-content: center;
    align-items: center;
    }
    .member_img img{
    width: 100%;
    height: auto;
    } 
	.member_name{
	font-size:16px;
	font-weight:600;
	margin-top:10px;
	}
	.member_details{
	font-size:14px;
	color:#777;
	margin-top:8px;
	font-size:13px;
	}
	.member_details strong{
	color:#757575;
	margin-top:5px;
	font-size:13px;
	font-weight:600;
	}
	.member_details_inline{
	display:inline-block;
	padding: 0 5px;
	}
	.action_btn a{
	padding: 5px 15px;
	border-radius: 5px;
	}

	.flag_user{
		position:absolute;
	}

	.panel-sort{
	    width: 100%;
	    float: left;
	    box-sizing: border-box;
	}

	.user_block{

		width:260px;
		display:inline-block;
		margin:5px;
	}

	.toggle-search, .toggle-sort, .user-btn-search{
		background-color: #054890;
	    border-color: #054890;
	}

	/*details page*/
	.detil_wrap_content{
		display: table;
	}
	.left_cell{
		/*display:table-cell;
		vertical-align:top;*/
		float:left;
	}
	.right_cell{
		/*display:table-cell;
		vertical-align:top;*/
		width:200px;
	}								
	.member_details_block .count-box .left_cell{
	  color: #5d6a75;
	}
	.top_right_button{
		width:200px;
		float: right;
	}
	.top_right_button a{
		float:right;
	}
	
	@media screen and (max-width:850px){
		.top_right_button {
		  width: 73px;
		}
	}
	
	@media screen and (max-width:767px){
		.top_right_button {
		  width: 85px;
		}
	}
	
	.member_details_block{
	clear: both;
	margin-bottom:15px;
	}
	.member_details_block.inactive *{
		color: grey !important;
	}
	.member_details_block.inactive img{
		opacity: .5 !important;
	}
	
	.member_detail_img{
	box-shadow: 0 0 5px #aca6a6b2;
	padding:1px;
	border:1px solid #ccc;
	min-width:auto;
	max-width: 300px;
	height: auto;
	border-radius: 10px;
	}
	.member_detail_name{
	font-size:18px;
	font-weight: 600;
	margin-bottom:5px;
	}
	.background_gray{
	background:#f9f9f9;
	padding:15px;
	border-radius:10px;
	border-bottom: 2px solid #3b82bf;
	}

	.member_details_block.admin_member .background_gray{
		border-bottom: 2px solid #73ad16;
	}
	.member_details_block.inactive .background_gray{
		border-bottom: 2px solid #b7b5b5 !important;
	}
	

	.social_icon{
	margin-bottom:15px;
	}
	.social_icon a{
	display:inline-block;
	}
	.social_icon .middle{
	margin:0 10px;
	}
	.member_detail_info{
	font-size: 18px;
	font-weight: 600;
	margin-bottom: 5px;
	color:#777;
	font-size:14px;
	}
	.member_detail_info strong{
	font-weight: 600;
	font-size: 13px;
	}
	.member_detail_info_normal {
	font-weight: normal;
	color:#777;
	font-size: 13px;
	line-height:25px;
	}
	.member_details_block ul {
	list-style: none;
	padding: 0;
	margin: 0;
	}
	.member_details_block ul li {
	margin-bottom: 10px;
	display: flex;
	align-items: center;
	}
	.member_details_block ul i {
	font-size: 16px;
	margin-right: 5px;
	color: #ffb727;
	line-height: 0;
	}
	.member_details_block  .count-box span.purecounter {
	/*font-size: 36px;
	line-height: 30px;*/
	display: block;
	/*font-weight: 700;*/
	color: #3b434a;
	margin-left: 50px;
	}
	.member_details_block .count-box p {
	padding: 15px 0 0 0;
	margin: 0 0 0 50px;
	font-family: "Raleway", sans-serif;
	font-size: 14px;
	color: #5d6a75;
	}
	.member_details_block .count-box {
	width: 100%;
	}
	.member_details_block .count-box span.purecounter {
	font-size: 20px;
	  line-height: 22px;  
	  font-weight: 700;
	  color: #3b434a;
	  margin-top: 15px;
	  margin-bottom: 0;
	  color:#054890;
	background: #f4f4f4;
	padding:8px;
	}
	.member_details_block .heading{
	font-size: 20px;
	  line-height: 22px;  
	  font-weight: 700;
	  color: #3b434a;
	  margin-bottom: 15px;
	  color:#054890;
	}

	.member_details_block .count-box img{
	width: 32px;
	  height: 32px;
	  box-shadow: none;
	  border: none;
	  position: relative;
	  top: 52px;
	  left: 10px;
	}
	.float_block_content{
	/*clear:both;
	overflow:hidden;
	content:"";*/
	padding:15px;
	vertical-align: text-top;
	}
	.float_block{
	display:inline-block;
	margin-bottom: 15px;
	margin-right: 35px;
	vertical-align: text-top;
	}
	.float_block_left{
	margin-left:30px;
	margin-bottom:50px;
	}
	.float_block_right{
	margin-right:35px;
	}
	
	/*top right button adjustment*/
	@media screen and (max-width:908px){
		.btns-user-add{
			width: 220px;
		}
	}
	@media screen and (max-width:850px){
		.btns-user-add{
			width: auto;
		}
	}
	@media screen and (max-width:447px){
		.btns-user-add a{
			padding:7px;
		}
	}
	@media screen and (max-width:374px){
		.btns-user-add a{
			padding:7px;
			padding: 4px;
			margin-top: 3px;
		}	
	}
	/**********/


	/*details page*/
	.float_block_middle{
		width:320px;
	}
	.float_block_middle .right_cell{
		width:320px;
	}
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


	@media screen and (max-width:1399px){
		.user_block {
			width: 30.5%;
			display: inline-block;
			margin: 1.2% 1.2%;
			height:280px;
			vertical-align: top;
		}
		.member_list_block{
			height:280px;
		}
	}

	@media screen and (max-width:991px){
		.user_block {
			width: 46.5%;
			display: inline-block;
			margin: 1.2% 1.2%;
			height:280px;
			vertical-align: top;
		}
		.member_list_block{
			height:280px;
		}
	}	

	@media screen and (max-width:767px){    /* For details screen */
	    .resp_margin_one{
	        margin-top:15px;
	    }
	    .resp_margin_two{
	        margin-bottom:15px;
	    }
	    
	    
	}

	@media screen and (max-width:600px){
		.user_block {
			width: 98%;
			display: inline-block;
			margin: .5% 1%;
			height:auto;
			vertical-align: top;
		}
		.member_list_block{
			height:auto;
		}

	}	
	
	
	@media screen and (max-width:485px){
		.detail_wrap_content{
			width:100%;
		}
		.float_block{
			width:100%;
		}
		.right_cell {
		  width: auto;
		}
		.member_details_block .count-box {
		  margin-left: -8px;
		}
		.float_block_middle{
			width:100%;
		}
		.float_block_middle .right_cell{
			width:100%;
		}
	}	

	@media screen and (min-width:1400px){
		.user_block {
			width: 22.3%;
			display: inline-block;
			margin: .8% 1.2%;
			vertical-align: top;
			height:290px;
		}
		.member_list_block{
			height:290px;
		}
	}
</style>

<div class="row">
    <div id='user_list_container' class="col-12 mt-3 mb-2">
		<div class="card">
        <div class="card-body">
        	<div class="card-header-heading">
        	<div class="row">
                <div class="col-8 "><h4 class="row pg_heading_line_ht  ">Members&nbsp;<span><span id="heading_rec_cnt" style="color: #0c0c0cab;" >0</span><?php if($this->body_template_data['allow_export']===true){ ?><a id="export_members" href="" download="members.csv" class="nopropagate ml-1 d-none" ><img src="images/dnld.png" alt="Export members list as CSV" width="22" height="22"  ></a><?php } ?></span> </h4></div>
                <div class="col-4 text-right">
                	<div class="row btns-user-add" style="float:right;">
	                	<!-- a class="btn btn-primary toggle-search" href="javascript:void(0);">
							<img src="images/search-plus.png" class="custom-button fa-search-plus" alt="Search"><img src="images/search-minus.png" class="custom-button fa-search-minus" alt="Search">
						</a -->
						<a class="btn btn-primary toggle-sort" href="javascript:void(0);">
							<img src="images/sort1.png" class="custom-button" alt="Sort">
						</a>
						<?php 
							if($this->body_template_data['can_add']===true){
						?>		
								<a href="users.php#mode=addUser" class="btn btn-success  record-add-button"  id="add-record-button"><!--<i class="fa fa-plus"></i>--><img src="images/plus.png" class="custom-button-small" alt="Plus"> <span class="hide_in_mobile"  >Add Member</span> </a>
						
						<?php		
							}
						?>	

					</div>
				</div>
            </div>
        </div>
        <div class="panel-search d-none pb-2">
			<?php include CONST_THEMES_TEMPLATE_INCLUDE_PATH.'users-search-form-basic.tpl'; ?>
		</div>
		<div class="panel-sort d-none pb-2">
			<?php include CONST_THEMES_TEMPLATE_INCLUDE_PATH.'users-sort-form-basic.tpl'; ?>
		</div>
		
		<div class="responsive-block-table for-cat" id="userlistbox" style="clear: both;"  ></div>

	</div>
	</div>
</div>
	<div class="col-12 mt-3 mb-2 d-none"  id='user_detail_view_container'  >
		<?php require_once CONST_THEMES_TEMPLATE_INCLUDE_PATH.'users-view.tpl';	?>
	</div>
	<div class="col-12 mt-3 mb-2 d-none"  id='user_detail_add_edit_container'   >
		<?php require_once CONST_THEMES_TEMPLATE_INCLUDE_PATH.'users-add.tpl';	?>
	</div>


</div>

<!-- Modal for the regular members for sending a contact request to DND members -->
<div class="modal fade" id="reqcontactdialog" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="reqcontactstaticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="width: 600px;"   >
      <div class="modal-header">
        <h5 class="modal-title" id="reqcontactstaticBackdropLabel"></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="reqcontactform" enctype="multipart/form-data" name="reqcontactform" action='<?php echo CONST_CURR_SCRIPT; ?>' method='post' target="form_post_submit_target_window"  >
      	<input type="hidden" name='mode' value='reqcontact' />
      	<input type="hidden" id="reqcontact_mem_id" name='to_mem_id' value='' />
      	<div class="modal-body">
	        <div class="row p-2">
	        	<p class="reqcontacterror  "  ></p>
	        </div>
	      	<div class="form-group row">
				<label class="control-label col-xs-12 col-sm-6 col-lg-4" for="reqcontact_to"> To </label>
				<div class="col-xs-12 col-sm-6 col-lg-8">
					<span id="reqcontact_to" style="display: inline-block; margin-top: 4px;" ></span>
				</div>
			</div>
			
			<div class="form-group row" >
				<label class="control-label col-xs-12 col-sm-6 col-lg-4" for="reqcontact_brands"> Message </label>
				<div class="col-xs-12 col-sm-6 col-lg-8">
					<textarea id="reqcontact_msg" placeholder="Your message" class="form-control" name="msg" maxlength="500"></textarea>
				</div>
			</div>  
      
      </div>
      <div class="modal-footer">
        <button type="submit" name="reqcontactbtn" class="btn btn-success" value="reqcontact"  id="reqcontactbtn" style="margin: 0 auto;"   >Request Contact</button>
      </div>
    	</form>
    </div>
  </div>
</div>

<?php

}

?>
