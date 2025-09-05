<style>
@media screen and (max-width: 575px) {
  .mobile_display_none {
    display: block !important;
  }
}

@media screen and (max-width: 520px) {
  .mobile_display_none {
    display: none !important;
  }
}	
</style>

<div id="search_records" class="row ">
	<div class=" col-lg-12 col-sm-12">
		<form class="form-inline search-form" name="search_form" onsubmit="return memregfuncs.doSearch(this);">

			<div class="basic-search-box ">
				<div class="row">
					
						<div class="col-lg-12 col-sm-12">
							<div class="row">
							<div class="col-xs-12 col-sm-6 col-md-6 col-lg-2 form-group ">
								<label class="" style="font-weight: normal; white-space: nowrap;" for="search-field_name" >Name</label>
								<input type="text" id="search-field_name"  placeholder="Name has" class="form-control srchfld" style="height: 32px;width: 100%;" maxlength="50" data-type="CONTAINS" data-fld="name" />
							</div>
							<div class="col-xs-12 col-sm-6 col-md-6 col-lg-2 form-group ">
								<label class="" style="font-weight: normal; white-space: nowrap; width: 100%;" for="search-field_status" >Status</label>
								<select class="form-control srchfld" id="search-field_status" data-type="EQUAL" data-fld="status" style="width: 100%;" >
                                    <option value="" >-- Any --</option>
                                    <option value="New" >New</option>
                                    <option value="Approved" >Approved</option>
                                    <option value="Disapproved" >Disapproved</option>
                                </select>
							</div>
							<div class="col-xs-12 col-sm-6 col-md-6 col-lg-4">
								<label class="mobile_display_none" >&nbsp;</label>
								<button class="btn btn-primary user-btn-search" style="margin-right: 10px;margin-top:0px !important;">
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
