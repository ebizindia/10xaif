<div class="card">
<div class="card-body">
	<div class="card-header-heading">
		<div class="row">
	        	<div class="col-6 "><h4 class="row pg_heading_line_ht  ">Registrations&nbsp;<span id="heading_rec_cnt" style="color: #0c0c0cab;" >0</span><a id="export_booking" href="" download="bookings.csv" class="nopropagate ml-1 d-none" ><img src="images/dnld.png" alt="Export members list as CSV" width="22" height="22"></a> </h4></div>
	        	<div class="col-6 text-right back-and-search">
	        		
				<div class="text-right back-to-list">
	        			<a href="event-bookings.php#" class="btn btn-danger record-list-show-button back-to-list-button row" id="back-to-list-button"> <img src="images/left-arrow.png" class="custom-button" alt="Left"> Back To List </a>

	   					<a href="event-bookings.php#" class="btn btn-danger record-list-show-button back-to-list-button row mobile-bck-to-list"  id="back-to-list-button"> <img src="images/left-arrow.png" class="custom-button" alt="Left"> </a>

	                	</div>
	                	<div class="row btns-user-add" style="float:right;">
	                		<a class="btn btn-primary toggle-search" href="javascript:void(0);">
							<img src="images/search-plus.png" class="custom-button fa-search-plus" alt="Search"><img src="images/search-minus.png" class="custom-button fa-search-minus" alt="Search">
						</a>
				</div>
			</div>
	    	</div>
	</div>
	<div class="panel-search d-none pb-2">
		<?php include CONST_THEMES_TEMPLATE_INCLUDE_PATH.'event-bookings-search-form-basic.tpl'; ?>
	</div>
	<div class="d-none pb-2" id="booking_for_event" style="color: #a35f37; font-weight: bold;" >
		
	</div>
	<div class="responsive-block-table for-cat">
		<div class="panel-body table-responsive">
			<table id="recs-list" class="table table-striped table-bordered table-hover">
				<thead class="thead">
					<tr>
						<th width="70px"><span>Action</span></th>
						<th id="colheader_booking-id" nowrap="nowrap" width="150px" ><span class="pull-left">Registration ID</span></th>
						<th class='sortable' id="colheader_registered-on" nowrap="nowrap" style="padding-right: 20px;"  width="150px"   ><span class="pull-left">Registered On</span><i  class='fa fa-sort pull-right fix-sort-icon' ></i></th>
						<th class='sortable' id="colheader_mem-name" style="width: 250px;" ><span class="pull-left">Member</span><i  class='fa fa-sort pull-right' ></i></th>
						<!-- <th class='sortable' id="colheader_ev-name" style="width: 300px;" ><span class="pull-left">Event</span><i  class='fa fa-sort pull-right' ></i></th> -->
						<th id="colheader_no-of-tickets" align="right" class="text-right"  ><span class="pull-right text-right ">Persons</span></th>
						<th id="colheader_attended" align="right" class="text-right"  ><span class="pull-right text-right ">Attended</span></th>
						<th id="colheader_noshow" align="right" class="text-right"  ><span class="pull-right text-right ">No Show</span></th>
						<th  id="colheader_payment-mode" ><span class="pull-left">Registration Status</span></th>
						<th  id="colheader_payment-mode" ><span class="pull-left">Payment Status</span></th>
						<th  id="colheader_amount-paid" align="right" class="text-right"  ><span class="pull-right text-right ">Amount</span></th>
						<th  id="colheader_payment-mode" ><span class="pull-left">Payment Mode</span></th>
					</tr>
				</thead>

				<tbody  id='userlistbox' >
				</tbody>
			</table>
		</div>
	</div>

</div>
</div>