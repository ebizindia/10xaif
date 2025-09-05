<div id="search_records" class="row ">
	<div class=" col-lg-12 col-sm-12">
		<form class="form-inline search-form" name="search_form" onsubmit="return evbkngfuncs.doSearch(this);">

			<div class="basic-search-box ">
				<div class="row">
					
						<div class="col-lg-12 col-sm-12">
							<div class="row">
								<div class="col-xs-12 col-sm-6 col-md-6 col-lg-2 form-group ">
									<label class="" style="font-weight: normal; white-space: nowrap;" for="search-field_bookingid" >Registration ID</label>
									<input type="text" id="search-field_bookingid"  placeholder="Registration ID is" class="form-control srchfld" style="height: 32px;width: 100%;" maxlength="100" data-type="EQUAL" data-fld="booking_id" />
								</div>

								<div class="col-xs-12 col-sm-6 col-md-6 col-lg-2 form-group ">
									<label class="" style="font-weight: normal; white-space: nowrap;" for="search-field_memname" >Member's Name</label>
									<input type="text" id="search-field_memname"  placeholder="member's name has" class="form-control srchfld" style="height: 32px;width: 100%;" maxlength="150" data-type="CONTAINS" data-fld="mem_name"  />
								</div>

								<div class="col-xs-12 col-sm-6 col-md-6 col-lg-2 form-group ">
									<label class="" style="font-weight: normal; white-space: nowrap;" for="search-field_memno" >Membership No.</label>
									<input type="text" id="search-field_menno"  placeholder="Membership no. has" class="form-control srchfld" style="height: 32px;width: 100%;" maxlength="20" data-type="CONTAINS" data-fld="mem_membership_no"  />
								</div>

								<div class="col-xs-12 col-sm-6 col-md-6 col-lg-2 form-group d-none " id="event_name_search_cont"  >
									<label class="" style="font-weight: normal; white-space: nowrap;" for="search-field_name" >Event's Name</label>
									<input type="text" id="search-field_evname"  placeholder="Event's name has" class="form-control srchfld" style="height: 32px;width: 100%;" maxlength="100" data-type="CONTAINS" data-fld="ev_name" />
								</div>

								<div class="col-xs-12 col-sm-6 col-md-6 col-lg-4 form-group "  style="width: 300px !important;" >
									<label class="" style="font-weight: normal; white-space: nowrap;width:100%;display:block;" for="search-field_evregperiodstart" >Registered Between</label>
									<input type="hidden" id="search-field_evregperiodstart" value="" class="srchfld" data-fld="evreg_period_start"  data-type="CONTAINS" >
                               		<input type="text" id="search-field_evregperiodstart_picker" placeholder="Period start" class="form-control"  value="" maxlength='10' autocomplete="off" readonly style="background-color: transparent !important; cursor: default !important;width:100px;display:inline-block; margin-right:10px;"   />

                               		<input type="hidden" id="search-field_evregperiodend" value="" class="srchfld" data-fld="evreg_period_end"  data-type="CONTAINS" >
                               		<input type="text" id="search-field_evregperiodend_picker" placeholder="Period end" class="form-control"  value="" maxlength='10' autocomplete="off" readonly style="background-color: transparent !important; cursor: default !important;width:100px;display:inline-block;"   />
                               		
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
