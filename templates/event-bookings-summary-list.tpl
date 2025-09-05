<div class="card">
<div class="card-body">
	<div class="card-header-heading">
		<div class="row">
	        	<div class="col-8 "><h4 class="row pg_heading_line_ht  ">Event Registrations&nbsp;<!-- <span id="summary_heading_rec_cnt" style="color: #0c0c0cab;" >0</span> --> </h4></div>
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
		<?php include CONST_THEMES_TEMPLATE_INCLUDE_PATH.'event-bookings-summary-search-form-basic.tpl'; ?>
	</div>
	<div class="responsive-block-table for-cat">
		<div class="panel-body table-responsive">
			<table id="summary-recs-list" class="table table-striped table-bordered table-hover">
				<thead class="thead">
					<tr>
						<th width="70px"><span>Action</span></th>
						<th class='sortable' id="colheader_ev-name" style="width: 300px;" ><span class="pull-left">Event</span><i  class='fa fa-sort pull-right' ></i></th>
						<th id="colheader_ev-start-dt" class="sortable"  ><span class="pull-left">Date(s)</span><i  class='fa fa-sort pull-right' ></i></th>
						<th id="colheader_bookings" align="right" class="text-right"  ><span class="pull-right text-right">Registrations</span></th>
						<th id="colheader_tot-tickets" align="right" class="text-right"  ><span class="pull-right text-right">Persons</span></th>
						<th id="colheader_tot-attended" align="right" class="text-right"  ><span class="pull-right text-right">Attended</span></th>
						<th id="colheader_tot-noshow" align="right" class="text-right"  ><span class="pull-right text-right">No Show</span></th>
						<th  id="colheader_total-amount" align="right" class="text-right"  ><span class="pull-right text-right">Total Amount</span></th>
						<th id="colheader_reg-active"  ><span class="pull-left">Registration Is On</span></th>
						<th id="colheader_ev-active"  ><span class="pull-left">Event Active</span></th>
					</tr>
				</thead>

				<tbody  id='summaryreclistbox' >
				</tbody>
			</table>
		</div>
	</div>

</div>
</div>