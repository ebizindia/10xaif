<style>
	.sector-search{
		margin-left: 26px;
		padding: 0px;
	}

	@media (min-width:992px){
		.bldgrp-search{
			padding-right: 0px;
		}
		.batch-search{
			padding-left: 0px;
			padding-right: 0px;
		}	
	}

	@media (max-width:991px){
		.sector-search{
			margin-left: 0px;
			padding-right: 15px;
			padding-left: 15px;
		}	
	}

	

</style>
<div id="search_records" class="row ">
	<div class=" col-lg-12 col-sm-12">
		<form class="form-inline search-form" name="search_form" onsubmit="return usersfuncs.doSearch(this);">

			<div class="basic-search-box ">
				<div class="row">
					
						<div class="col-lg-12 col-sm-12">
							<div class="row btn-search-form-top" >
						        <div class="col form-group">
						            <button class="btn btn-primary user-btn-search search_button" style="margin-right: 10px;">
						                <img src="images/search.png" class="custom-button" alt="Search"> Search
						            </button>
						        </div>
						    </div>
							<div class="row">
								<div class="col-xs-12 col-sm-6 col-md-6 col-lg-2 form-group ">
									<label class="" style="font-weight: normal; white-space: nowrap;" for="search-field_name" >Name</label>
									<input type="text" id="search-field_name"  placeholder="Name has" class="form-control srchfld" style="height: 32px;width: 100%;" maxlength="50" data-type="CONTAINS" data-fld="name" autocomplete="off" />
								</div>

								<div class="col-xs-12 col-sm-6 col-md-6 col-lg-2 form-group ">
									<label class="" style="font-weight: normal; white-space: nowrap;" for="search-field_email" >Email</label>
									<input type="text" id="search-field_email"  placeholder="Email Id has" class="form-control srchfld" style="height: 32px;width: 100%;" maxlength="50" data-type="CONTAINS" data-fld="email"  />
								</div>

								<div class="col-xs-12 col-sm-6 col-md-6 col-lg-2 form-group ">
									<label class="" style="font-weight: normal; white-space: nowrap;" for="search-field_mob" >Mobile</label>
									<input type="text" id="search-field_mob"  placeholder="Mobile no. has" class="form-control srchfld" style="height: 32px;width: 100%;" maxlength="50" data-type="CONTAINS" data-fld="mob" />
								</div>


								<div class="col-xs-12 col-sm-6 col-md-6 col-lg-2 form-group ">
									<label class="" style="font-weight: normal; white-space: nowrap;" for="search-field_memno" >Membership No.</label>
									<input type="text" id="search-field_memno"  placeholder="Membership no. has" class="form-control srchfld" style="height: 32px;width: 100%;" maxlength="30" data-type="CONTAINS" data-fld="membership_no" />
								</div>
								
								<!--<div class="col-xs-12 col-sm-6 col-md-6 col-lg-1 form-group batch-search ">
									<label class="" style="font-weight: normal; white-space: nowrap;" for="search-field_batch-no" >Batch</label>
									<input type="text" id="search-field_batch-no"  placeholder="Batch is" class="form-control srchfld" style="height: 32px;width: 100%;" maxlength="50" data-type="EQUAL" data-fld="batch_no" />
								</div>-->
								
								<div class="col-xs-12 col-sm-6 col-md-6 col-lg-2 form-group" > <!-- bldgrp-search " -->
									<label class="" style="font-weight: normal; white-space: nowrap; width: 100%;" for="search-field_blood-grp" >Blood Gr.</label>
									<select class="form-control srchfld" id="search-field_blood-grp" data-type="EQUAL" data-fld="blood_grp" style="width: 100%;" >
	                                    <option value="">-- Any --</option>
	                                	<?php  foreach($this->body_template_data['blood_grps'] as $bg){ ?>
	                                   		<option value="<?php echo htmlentities($bg) ?>"><?php echo htmlentities($bg); ?></option>
	                                	<?php } ?>
	                            	</select>
								</div>

								<div class="col-xs-12 col-sm-6 col-md-6 col-lg-2 form-group" > <!-- sector-search " -->
									<label class="" style="font-weight: normal; white-space: nowrap; width: 100%;" for="search-field_sector" >Sector</label>
									<select class="form-control srchfld" id="search-field_sector" data-type="EQUAL" data-fld="sector_id" style="width: 100%;" >
	                                    <option value="">-- Any --</option>
	                                	<?php  foreach($this->body_template_data['sectors'] as $sec){ ?>
	                                   		<option value="<?php echo $sec['id']; ?>"><?php echo htmlentities($sec['sector']); ?></option>
	                                	<?php } ?>
	                            	</select>
								</div>

								
							</div>
							<div class="row">
								
								<div class="col-xs-12 col-sm-6 col-md-6 col-lg-2 form-group ">
									<label class="" style="font-weight: normal; white-space: nowrap;" for="search-field_residence-city" >Res. City</label>
									<input type="text" id="search-field_residence-city"  placeholder="City name has" class="form-control srchfld" style="height: 32px;width: 100%;" maxlength="50" data-type="CONTAINS" data-fld="residence_city" />
								</div>

								<div class="col-xs-12 col-sm-6 col-md-6 col-lg-2 form-group ">
									<label class="" style="font-weight: normal; white-space: nowrap;" for="search-field_residence-country" >Res. Country</label>
									<input type="text" id="search-field_residence-country"  placeholder="Country name has" class="form-control srchfld" style="height: 32px;width: 100%;" maxlength="50" data-type="CONTAINS" data-fld="residence_country" />
								</div>

								<div class="col-xs-12 col-sm-6 col-md-6 col-lg-2 form-group ">
									<label class="" style="font-weight: normal; white-space: nowrap;" for="search-field_work-company" >Company Name</label>
									<input type="text" id="search-field_work-company"  placeholder="Company name has" class="form-control srchfld" style="height: 32px;width: 100%;" maxlength="50" data-type="CONTAINS" data-fld="work_company" />
								</div>

								<div class="col-xs-12 col-sm-6 col-md-6 col-lg-2 form-group ">
									<label class="" style="font-weight: normal; white-space: nowrap;" for="search-field_work-type" >Business/Prof Details</label>
									<input type="text" id="search-field_work-type"  placeholder="Business/Profession details  has" class="form-control srchfld" style="height: 32px;width: 100%;" maxlength="50" data-fld="work_type" data-type="CONTAINS" />
								</div>

								<div class="col-xs-12 col-sm-6 col-md-6 col-lg-2 form-group ">
									<label class="" style="font-weight: normal; white-space: nowrap; width: 100%;" for="search-field_grp" >Group</label>
									<select class="form-control srchfld" id="search-field_grp" data-type="EQUAL" data-fld="grp_id" style="width: 100%;" >
	                                    <option value="">-- Any --</option>
	                                	<?php  foreach($this->body_template_data['groups'] as $grp){ ?>
	                                   		<option value="<?php echo $grp['id']; ?>"><?php echo htmlentities($grp['grp']); ?></option>
	                                	<?php } ?>
	                            	</select>
								</div>

								<!--<div class="col-xs-12 col-sm-6 col-md-6 col-lg-2 form-group ">
									<label class="" style="font-weight: normal; white-space: nowrap;" for="search-field_joinedafterdt_picker" >Joined After</label>
									<input type="hidden" id="search-field_joining-dt" value="" class="srchfld" data-fld="joining_dt"  data-type="AFTER" >
                               		<input type="text" id="search-field_joinedafterdt_picker" placeholder="Joined after" class="form-control"  value="" maxlength='10' autocomplete="off" readonly style="background-color: transparent !important; cursor: default !important; height: 32px;width: 100%;"   />
								</div>-->
								
								<div class="col-xs-12 col-sm-6 col-md-6 col-lg-2 form-group">
									<label class="mobile_display_none" >&nbsp;</label>
									<button class="btn btn-primary user-btn-search search_button" style="margin-right: 10px;">
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
