<div class="card">
	<div class="card-body">
		<div class="card-header-heading">
		<div class="row">
	        <div class="col-8 "><h4 class="row pg_heading_line_ht  ">Register For Events&nbsp;</h4></div>
	        <div class="col-4 text-right">
	        	<div class="row btns-user-add" style="float:right;">
	                	<a class="btn btn-primary toggle-search eventsearch" href="javascript:void(0);">
							<img src="images/search-plus.png" class="custom-button fa-search-plus" alt="Search"><img src="images/search-minus.png" class="custom-button fa-search-minus" alt="Search">
						</a>
						
						<a href="event-registrations.php#mode=bookings" class="btn btn-success  record-add-button"  id="add-record-button"><!-- <img src="images/ticket.png" class="custom-button-small" alt="Ticket"  style="width: 35px; height: 21px; max-width: 100%;"    > --> <span class="hide_in_mobile"  >My Tickets</span><span class="show_in_mobile"  >TKTs</span> </a>
					</div>
				</div>
	    </div>
	</div>
	<div class="panel-search d-none pb-2">
			<?php include CONST_THEMES_TEMPLATE_INCLUDE_PATH.'event-available-search-form-basic.tpl'; ?>
		</div>
		<div class="responsive-block-table for-cat"  id="eventslistbox" style="clear:both;" >
			<!-- <div class="panel-body table-responsive">
				<table id="recs-list" class="table table-striped table-bordered table-hover">
					<thead class="thead">
						<tr>
							<th width="70px"><span>Action</span></th>
							<th id="colheader_booking-id" ><span class="pull-left">Booking ID</span></th>
							<th class='sortable' id="colheader_ev-name" ><span class="pull-left">Event</span><i  class='fa fa-sort pull-right' ></i></th>
							<th class='sortable' id="colheader_ev-start-dt"><span class="pull-left">Event Starts From</span><i  class='fa fa-sort pull-right'></i></th>
							<th class='sortable' id="colheader_registered-on"><span class="pull-left">Registered On</span><i  class='fa fa-sort pull-right'></i></th>
							<th id="colheader_no-of-tickets" align="right" class="text-right"  ><span class="pull-right text-right">No. of Tickets</span></th>
							<th  id="colheader_total-amount" align="right" class="text-right"  ><span class="pull-right text-right">Total Amount</span></th>
						</tr>
					</thead>

					<tbody  id='userlistbox' >
					</tbody>
				</table>
			</div> -->
		</div>

	</div>
</div>