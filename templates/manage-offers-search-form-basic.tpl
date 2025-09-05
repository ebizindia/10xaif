<div id="search_records" class="row ">
	<div class=" col-lg-12 col-sm-12">
		<form class="form-inline search-form" name="search_form" onsubmit="return ManageOffer.doSearch(this);">
			<div class="basic-search-box ">
				<div class="row">
					<div class="col-lg-12 col-sm-12">
						<div class="row">
							<div class="col-xs-12 col-sm-6 col-md-6 col-lg-2 form-group ">
								<label class="" style="font-weight: normal; white-space: nowrap;" for="search-field_title" >Name</label>
								<input type="text" id="search-field_title"  placeholder="Title has" class="form-control srchfld" style="height: 32px;width: 100%;" maxlength="100" data-type="CONTAINS" data-fld="title" />
							</div>

							<div class="col-xs-12 col-sm-6 col-md-6 col-lg-2 form-group ">
								<label class="" style="font-weight: normal; white-space: nowrap;" for="search-field_description" >Description</label>
								<input type="text" id="search-field_description"  placeholder="Description has" class="form-control srchfld" style="height: 32px;width: 100%;" maxlength="300" data-type="CONTAINS" data-fld="description"  />
							</div>

							<!-- <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4 form-group "  style="width: 300px !important;" >
								<label class="" style="font-weight: normal; white-space: nowrap;width:100%;display:block;" for="search-field_display_period_start" >Display Date </label>
								<input type="hidden" id="search-field_display_period_start" value="" class="srchfld" data-fld="display_period_start"  data-type="CONTAINS" >
	                       		<input type="text" id="search-field_display_period_start_picker" placeholder="Display starts" class="form-control"  value="" maxlength='10' autocomplete="off" readonly style="background-color: transparent !important; cursor: default !important;width:100px;display:inline-block; margin-right:10px;"/>
	                       		<input type="hidden" id="search-field_display_period_end" value="" class="srchfld" data-fld="display_period_end"  data-type="CONTAINS" >
	                       		<input type="text" id="search-field_display_period_end_picker" placeholder="Display ends" class="form-control"  value="" maxlength='10' autocomplete="off" readonly style="background-color: transparent !important; cursor: default !important;width:100px;display:inline-block;"/>
							</div> -->

							<!-- <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4 form-group" style="width: 300px !important;">
								<label class="" style="font-weight: normal; white-space: nowrap;width:100%;display:block;" for="search-field_validity_period_start" >Valid Upto </label>
								<input type="hidden" id="search-field_validity_period_start" value="" class="srchfld" data-fld="validity_period_start"  data-type="CONTAINS" >
	                       		<input type="text" id="search-field_validity_period_start_picker" placeholder="From" class="form-control"  value="" maxlength='10' autocomplete="off" readonly style="background-color: transparent !important; cursor: default !important;width:100px;display:inline-block; margin-right:10px;"   />
	                       		<input type="hidden" id="search-field_validity_period_end" value="" class="srchfld" data-fld="validity_period_end"  data-type="CONTAINS" >
	                       		<input type="text" id="search-field_validity_period_end_picker" placeholder="Upto" class="form-control"  value="" maxlength='10' autocomplete="off" readonly style="background-color: transparent !important; cursor: default !important;width:100px;display:inline-block;"   />
							</div> -->

							<div class="col-xs-12 col-sm-6 col-md-6 col-lg-2 form-group ">
								<label class="" style="font-weight: normal; white-space: nowrap;" for="search-field_categoryid" >Category</label>
								<select class="form-control srchfld" id="search-field_categoryid" data-type="EQUAL" data-fld="category_id" style="width: 100%;">
									<option value="">-- Any --</option>
									<?php  foreach($this->body_template_data['disc_offer_cats'] as $doffcat){ ?>
	                                   <option value="<?php echo htmlentities($doffcat['id']) ?>"><?php echo \eBizIndia\_esc($doffcat['name'], true); ?></option>
	                                <?php } ?>
								</select>
							</div>

							<div class="col-xs-12 col-sm-6 col-md-6 col-lg-2 form-group ">
								<label class="" style="font-weight: normal; white-space: nowrap;" for="search-field_status" >Active</label>
								<select class="form-control srchfld" id="search-field_active" data-type="EQUAL" data-fld="active" style="width: 100%;">
									<option value="">-- Any --</option>
									<option value="y">Yes</option>
									<option value="n">No</option>
								</select>
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
