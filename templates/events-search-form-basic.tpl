<div id="search_records" class="row ">
	<div class=" col-lg-12 col-sm-12">
		<form class="form-inline search-form" name="search_form" onsubmit="return evfuncs.doSearch(this);">

			<div class="basic-search-box ">
				<div class="row">
					
						<div class="col-lg-12 col-sm-12">
							<div class="row">
								<div class="col-xs-12 col-sm-6 col-md-6 col-lg-2 form-group ">
									<label class="" style="font-weight: normal; white-space: nowrap;" for="search-field_name" >Name</label>
									<input type="text" id="search-field_name"  placeholder="Name has" class="form-control srchfld" style="height: 32px;width: 100%;" maxlength="100" data-type="CONTAINS" data-fld="name" />
								</div>

								<div class="col-xs-12 col-sm-6 col-md-6 col-lg-2 form-group ">
									<label class="" style="font-weight: normal; white-space: nowrap;" for="search-field_description" >Description</label>
									<input type="text" id="search-field_description"  placeholder="Description has" class="form-control srchfld" style="height: 32px;width: 100%;" maxlength="300" data-type="CONTAINS" data-fld="description"  />
								</div>

								<div class="col-xs-12 col-sm-6 col-md-6 col-lg-2 form-group ">
									<label class="" style="font-weight: normal; white-space: nowrap;" for="search-field_venue" >Venue</label>
									<input type="text" id="search-field_venue"  placeholder="Venue address has" class="form-control srchfld" style="height: 32px;width: 100%;" maxlength="300" data-type="CONTAINS" data-fld="venue"  />
								</div>

								<div class="col-xs-12 col-sm-6 col-md-6 col-lg-3 form-group "  style="width: 300px !important;" >
									<label class="" style="font-weight: normal; white-space: nowrap;width:100%;display:block;" for="search-field_periodstart" >Falls In Period</label>
									<input type="hidden" id="search-field_periodstart" value="" class="srchfld" data-fld="period_start"  data-type="CONTAINS" >
                               		<input type="text" id="search-field_periodstart_picker" placeholder="Period start" class="form-control"  value="" maxlength='10' autocomplete="off" readonly style="background-color: transparent !important; cursor: default !important;width:100px;display:inline-block; margin-right:10px;"   />

                               		<input type="hidden" id="search-field_periodend" value="" class="srchfld" data-fld="period_end"  data-type="CONTAINS" >
                               		<input type="text" id="search-field_periodend_picker" placeholder="Period end" class="form-control"  value="" maxlength='10' autocomplete="off" readonly style="background-color: transparent !important; cursor: default !important;width:100px;display:inline-block;"   />
                               		
								</div>

								<div class="col-xs-12 col-sm-6 col-md-6 col-lg-1 form-group " style="padding-right: 0px !important;"    >
									<label class="" style="font-weight: normal; white-space: nowrap;" for="search-field_active" >Active</label>
									<select class="form-control srchfld" id="search-field_active" data-type="EQUAL" data-fld="active" style="width: 100%;" >
										<option value=''>-- Any --</option>
										<option value='y' >Yes</option>
										<option value='n' >No</option>
									</select>
								</div>

								<div class="col-xs-12 col-sm-6 col-md-6 col-lg-2 form-group" style="padding-right: 0px !important;"  >
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
