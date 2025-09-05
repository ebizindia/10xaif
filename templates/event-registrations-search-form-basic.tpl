<div id="search_records" class="row ">
	<div class=" col-lg-12 col-sm-12">
		<form class="form-inline search-form" name="search_form" onsubmit="return evregfuncs.doSearch(this);">

			<div class="basic-search-box ">
				<div class="row">
					
						<div class="col-lg-12 col-sm-12">
							<div class="row">
								<div class="col-xs-12 col-sm-6 col-md-6 col-lg-2 form-group ">
									<label class="" style="font-weight: normal; white-space: nowrap;" for="search-field_bookingid" >Registration ID</label>
									<input type="text" id="search-field_bookingid"  placeholder="Registration ID is" class="form-control srchfld" style="height: 32px;width: 100%;" maxlength="100" data-type="EQUAL" data-fld="booking_id" />
								</div>

								<div class="col-xs-12 col-sm-6 col-md-6 col-lg-2 form-group ">
									<label class="" style="font-weight: normal; white-space: nowrap;" for="search-field_name" >Event's Name</label>
									<input type="text" id="search-field_evname"  placeholder="Event's name has" class="form-control srchfld" style="height: 32px;width: 100%;" maxlength="100" data-type="CONTAINS" data-fld="ev_name" />
								</div>

								<div class="col-xs-12 col-sm-6 col-md-6 col-lg-2 form-group ">
									<label class="" style="font-weight: normal; white-space: nowrap;" for="search-field_description" >Event's Description</label>
									<input type="text" id="search-field_evdescription"  placeholder="Event's description has" class="form-control srchfld" style="height: 32px;width: 100%;" maxlength="300" data-type="CONTAINS" data-fld="ev_description"  />
								</div>

								<div class="col-xs-12 col-sm-6 col-md-6 col-lg-2 form-group ">
									<label class="" style="font-weight: normal; white-space: nowrap;" for="search-field_venue" >Event's Venue</label>
									<input type="text" id="search-field_evvenue"  placeholder="Event's venue address has" class="form-control srchfld" style="height: 32px;width: 100%;" maxlength="300" data-type="CONTAINS" data-fld="ev_venue"  />
								</div>

								<div class="col-xs-12 col-sm-6 col-md-6 col-lg-4 form-group "  style="width: 300px !important;" >
									<label class="" style="font-weight: normal; white-space: nowrap;width:100%;display:block;" for="search-field_evperiodstart" >Event Falls in Period</label>
									<input type="hidden" id="search-field_evperiodstart" value="" class="srchfld" data-fld="ev_period_start"  data-type="CONTAINS" >
                               		<input type="text" id="search-field_evperiodstart_picker" placeholder="Period start" class="form-control"  value="" maxlength='10' autocomplete="off" readonly style="background-color: transparent !important; cursor: default !important;width:100px;display:inline-block; margin-right:10px;"   />

                               		<input type="hidden" id="search-field_evperiodend" value="" class="srchfld" data-fld="ev_period_end"  data-type="CONTAINS" >
                               		<input type="text" id="search-field_evperiodend_picker" placeholder="Period end" class="form-control"  value="" maxlength='10' autocomplete="off" readonly style="background-color: transparent !important; cursor: default !important;width:100px;display:inline-block;"   />
                               		
								</div>

								<div class="col-xs-12 col-sm-6 col-md-6 col-lg-2 form-group">
									<label class="mobile_display_none" >&nbsp;</label>
									<button class="btn btn-primary user-btn-search search_button">
										<img src="images/search.png" class="custom-button" alt="Search"> Search
									</button>
								</div>	

								
							</div>
							

						</div>
				</div>

			</div>

		</form>
	</div>
</div>
