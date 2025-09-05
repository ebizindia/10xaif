<?php 
if($this->body_template_data['mode'] == 'getCatList'){

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
						',$this->body_template_data[$mode_index]['filtertext'],' <a class="clear-filter" onclick="DiscountOffer.clearCatSearch();">Clear Filter</a>
					</div>	
				</div>
			</div>';
	}

	if($this->body_template_data[$mode_index]['records_count']==0){
		echo "<div class='row' ><div class='col-sm-12 col-md-12 col-lg-12 text-danger' align='center'  >There are no offers available at this time. Please check later.</div> </div>";

	}else{
		echo '<div class="row"  > 
				<div class="col-sm-12 col-md-12 col-lg-12"> ';
		for($i_ul=0; $i_ul<$this->body_template_data[$mode_index]['records_count']; $i_ul++){

				require CONST_THEMES_TEMPLATE_INCLUDE_PATH.'discount-offers-cat-row.tpl';

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

.row-adjust{margin-left:0; margin-right:0px;}
.panel-body .offer-item{
margin-bottom:20px;
border: 1px solid #ececec;
box-shadow: 0 0 5px #ccc;
transition:.3s all ease-in-out;
padding-bottom: 10px;
}
.panel-body .offer-item:hover{
border: 1px solid #d9d9d9;
box-shadow: 0 0 15px #8c8c8c;
transition: .3s all ease-in-out;
background: #f5fcff;
}
.offer-url{
padding:5px 10px;
text-decoration:underline;
}
.heading{
font-size: 16px;
padding: 5px 10px;
background:#e4eef9;
margin-bottom: 0px;
font-weight:600;
}
.description {
padding: 5px 10px;
}
.company {
padding: 5px 10px;
  color: #777;
  font-weight: 600;
  display: block;
}
.validity {
padding: 5px 10px;
  font-weight: 600;
  font-style: italic;
  font-size: 14px;
  color: #dc3545 !important;
    display: block;
}
.more-btn{
background: #0a74c4;
color: #fff;
padding: 4px 20px 6px;
transition: .3s all ease-in-out;
margin-bottom: 10px;
display: inline-block;
}
.more-btn:hover{
text-decoration:none;
transition: .3s all ease-in-out;
background: #074d82;
color: #fff;
}

/*
@media screen and (max-width:767px){
.mob-img img {
  max-width: 400px;
  width: 100%;
  }
}
*/


/********************** css for discount-offers row ************************/
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

/********************** css for discount-offers row ************************/




</style>




<div class="row">

		<!--   Cats list   -->
		<div id='cat_list_container' class="col-12 mt-3 mb-2 d-none">
				<div class="card">
		        <div class="card-body">
		        	<div class="card-header-heading">
				        	<div class="row">
			                <div class="col-8 "><h4 class="row pg_heading_line_ht  ">Epic Offers&nbsp;<span><span id="heading_rec_cnt" style="color: #0c0c0cab;" class="d-none" >0</span></h4></div>
			                
				          </div>
		        	</div>
		        
				
							<div class="responsive-block-table for-cat" id="catlistbox" style="clear: both;"  ></div>

			</div>
			</div>
		</div>	


		<!--   -->


    <div id='rec_list_container' class="col-12 mt-3 mb-2 d-none">
		<div class="card">
	        <div class="card-body">
	        	<div class="card-header-heading">
		        	<div class="row">
		                <div class="col-8"><h4 class="row pg_heading_line_ht"><span id="disc_offer_cat"  >Epic Offers</span>&nbsp;(<span id="heading_offers_rec_cnt" style="color: #0c0c0cab;" >0</span>) </h4></div>
		                <div class="col-4 text-right">
	                    <a href="discount-offers.php#" class="btn btn-danger record-list-show-button back-to-list-button row" id="back-to-list-button"><img src="images/left-arrow.png" class="custom-button" alt="Left"> Back To Offer Categories </a>

	                    <a href="discount-offers.php#" class="btn btn-danger record-list-show-button back-to-list-button row mobile-bck-to-list"  id="back-to-list-button"><img src="images/left-arrow.png" class="custom-button" alt="Left"> </a>
                		</div>
		          </div>
		        </div>
		 		<div class="responsive-block-table for-cat">
					<div class="panel-body table-responsive">
						<div id="offers_list"  class="category_block"  >
									
						</div>
						<div class="clearfix"   ></div>
						<div id="more_container" style="clear: both; margin-top: 30px;"  >
							<div class="col-12 text-center d-none"><a href="javascript:void(0);" id="fetch_more" class="more-btn">More ...</a></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>




</div>
<div class="d-none">
	<div class="row">
		<div id="offer_clone" class="category_details_container">
			<div class="category_details">
				<div class="category_details_left">
					<div class="category_details_left_img  mob-img d-none ">
							<div class="category_img_overflow">
								<img src="https://www.wekolkata.com/wp-content/uploads/2023/06/WhatsApp-Image-2023-06-14-at-3.22.57-PM.jpeg" alt="" class="img-fluid">			
							</div>
					</div>
					<div class="black_link mou-link">
						<a href="#" rel="noopener noreferrer"  target="_blank">View MOU</a>
					</div>
				</div>
				<div class="category_details_right  offer_details ">
					<h3 class="title title_elem"   >16th Street Patisserie</h3>
					<div  class=" desc_elem"  >5% on Billing of Rs. 10000/- & 7% on Billing of Rs. 15,000/-</div>
					<div  class="  url_elem d-none"  ><a href='#' rel="noopener noreferrer" target="_blank" >Test Link</a></div>
				</div>
			</div>	
		</div>

		<div id="nooffer_clone" class="text-danger"  >There are no epic offers under this category at this time. Please check later.</div>

		<div  id="category_details_container_row"  class="category_details_container_row"></div>


	</div>
</div>
<?php
}
?>