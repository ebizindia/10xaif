<div id="sort_records" class="row ">
	<div class=" col-lg-12 col-sm-12">
		<form class="form-inline sort-form" name="sort_form" onsubmit="return usersfuncs.doSort(this);">

			<div class="basic-search-box ">
				<div class="row">
					<div class="col-lg-12 col-sm-12">
						<div class="row">
							<div class="col-sm-12 col-md-12 col-lg-12">
								<div style="width:190px; display:inline-block;margin-bottom:10px;">
									<span style="display:inline;">Sort by</span>&nbsp; 
									<select style="display:inline-block;" class="form-control srchfld" id="orderlist-sorton" style="width: 100%;">
										<option value="name" >Name</option>
										<!--<option value="batch_no">Batch No.</option>-->
										<option value="work_company">Company</option>
										<option value="email">Email ID</option>
										<option value="membership_no">Membership No.</option>
									</select>
								</div>
								<div style="width:140px; display:inline-block;margin-bottom:10px;">
									&nbsp;<input type="radio" id="orderlist_sortorder_ASC" name="sortorder" value="ASC" >&nbsp;<label style="display: inline-block;" for="orderlist_sortorder_ASC"  ><img src="images/a-to-z.png" alt="Sort ascending" class=""></label> &nbsp; 
									&nbsp;&nbsp; <input type="radio" id="orderlist_sortorder_DESC" name="sortorder" value="DESC" >&nbsp;<label style="display: inline-block;" for="orderlist_sortorder_DESC"  ><img src="images/z-to-a.png" alt="Sprt descending" class=""></label>
								</div>
								<div style="display:inline-block;margin-bottom:10px;">
									<button class="btn btn-primary user-btn-sort" id="btn_sort" style="background:#054890;">
									Sort 
								</div>
							</div>
						</div>
					</div>
				</div>

			</div>

		</form>
	</div>
</div>
