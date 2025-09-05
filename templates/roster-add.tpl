<style>
    #add_form_field_msgbody{
        min-height: 270px;
    }
	.time-input{
		width:15%;
	}
	.time-input input[type="text"]{
		width: 72px;
		float: left;
	}
	.time-input select{
		width: 60px;
	  float: left;
	  margin-left: 10px;
	}	
	.start-end-time{
		width: 30%;
		white-space:nowrap;
	}
	
	.start-end-time label{
		float: left;
	  white-space: nowrap;
	  margin-right: 10px;
	  margin-top: 4px;
	}
	.start-end-time input[type="text"]{
		width: 72px;
		float: left;
	}
	.delete-row{
		font-size: 25px;
		font-weight: bold;
		height: 30px;
		line-height: 0px;
		padding: 2px 11px 6px;
	}
	@media (max-width: 767px) {
	.btn.back-to-list-button {
		display: none;
		margin-right: -15px;
	  }
	}
</style>
<div class="row">
    <div id='feedback_form_container' class="col-12 mt-1 mb-2">
        <div class="card">
            <div class="card-body">                 
                <div class="card-header-heading">
                    <div class="row">
                        <div class="col-8"><h4 class="row pg_heading_line_ht" id="panel-heading-text">Add New Meeting</h4></div>
                        <div class="col-4">
                            <div class="row">                                
                                <div style="text-align:right;width: 100%;">
									<a href="meeting.php#" class="btn btn-danger record-list-show-button back-to-list-button rounded" id="back-to-list-button">
									<!--<i class="fa fa-arrow-left"></i>--><img src="images/left-arrow.png" class="custom-button" alt="Left"> Back To List </a>
									
									
									<a href="meeting.php#mode=addUser" class="btn btn-danger record-add-button rounded mobile-bck-to-list" id="back-to-list-button"><!--<i class="fa fa-plus"></i>--><img src="images/left-arrow.png" class="custom-button" alt="Left"></a>
								</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <!--meeting-agenda-input start-->
                    
                    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12 meeting-agenda-input">
                        
                        
					<form class="form-horizontal" role="form" name='addrosterform' id="addrosterform" action='roster.php' method='post' onsubmit="return feedbackfuncs.submitFeedback(this);" target="form_post_submit_target_window"  data-mode="send-feedback" novalidate  >
						<input type="hidden" name="mode" id="add_edit_mode" value="updateUser">
						<input type="hidden" name="recordid" id="add_edit_recordid" value="189">
						
						<div class="alert alert-warning mt-2" role="alert" id="msgFrm">
							<p style="margin-bottom: 0">All fields marked with an asterisk (<span class="required">*</span>) are required.</p>
						</div>
						<div class="alert alert-danger d-none">
							<strong><i class="icon-remove"></i></strong>
							<span class="alert-message"></span>
						</div>
						<div class="alert alert-success d-none">
							<strong><i class="icon-ok"></i></strong>
							<span class="alert-message"></span>
						</div>
						
						<hr class="mt-2 mb-4">									
						<div class="form-group row">
							<label class="control-label col-xs-12 col-sm-6 col-lg-2" for="Year"> Year </label>
							<div class="col-xs-12 col-sm-6 col-lg-4">
								<select class="form-control" name="financial-year[]">
									<option value="2022-23">2022-23</option>
									<option value="2023-24">2023-24</option>
									<option value="2024-25" selected>2024-25</option>
									<option value="2025-26">2025-26</option>
									<option>
								</select>	
							</div>
						</div>
						
						<div class="form-group row">
							<label class="control-label col-xs-12 col-sm-6 col-lg-2" for="Moderator"> Moderator </label>
							<div class="col-xs-12 col-sm-6 col-lg-4">
								<select class="form-control" name="members[]">
                                    <option value="Mr Sudeep Chitlangia">Mr Sudeep Chitlangia</option>  
									<option value="Mr Aditya Chamaria">Mr Aditya Chamaria</option>
									<option value="Mr Akshay Poddar">Mr Akshay Poddar</option>
									<option value="Arun Agrawal">Arun Agrawal</option>	
                                    <option value="Mr Ravi Todi">Mr Ravi Todi</option>
                                    <option value="Mrs Sanaya Mehta Vyas">Mrs Sanaya Mehta Vyas</option>
                                    <option value="Mr Songit Kumar Bagrodia">Mr Songit Kumar Bagrodia</option>
								  </select>
							</div>
						</div>
						
						<div class="form-group row">
							<label class="control-label col-xs-12 col-sm-6 col-lg-2" for="Moderator-Elect"> Moderator Elect </label>
							<div class="col-xs-12 col-sm-6 col-lg-4">
								<select class="form-control" name="members[]">
                                    <option value="Mr Sudeep Chitlangia">Mr Sudeep Chitlangia</option>  
                                    <option value="Mr Aditya Chamaria">Mr Aditya Chamaria</option>
                                    <option value="Mr Akshay Poddar">Mr Akshay Poddar</option>
                                    <option value="Arun Agrawal">Arun Agrawal</option>  
                                    <option value="Mr Ravi Todi">Mr Ravi Todi</option>
                                    <option value="Mrs Sanaya Mehta Vyas">Mrs Sanaya Mehta Vyas</option>
                                    <option value="Mr Songit Kumar Bagrodia">Mr Songit Kumar Bagrodia</option>
                                  </select>
							</div>
						</div>
						
						<div class="form-group row">
							<label class="control-label col-xs-12 col-sm-6 col-lg-2" for="Moderator-Elect-Elect"> Moderator Elect Elect </label>
							<div class="col-xs-12 col-sm-6 col-lg-4">
								<select class="form-control" name="members[]">
                                    <option value="Mr Sudeep Chitlangia">Mr Sudeep Chitlangia</option>  
                                    <option value="Mr Aditya Chamaria">Mr Aditya Chamaria</option>
                                    <option value="Mr Akshay Poddar">Mr Akshay Poddar</option>
                                    <option value="Arun Agrawal">Arun Agrawal</option>  
                                    <option value="Mr Ravi Todi">Mr Ravi Todi</option>
                                    <option value="Mrs Sanaya Mehta Vyas">Mrs Sanaya Mehta Vyas</option>
                                    <option value="Mr Songit Kumar Bagrodia">Mr Songit Kumar Bagrodia</option>
                                  </select>
							</div>
						</div>
						
						<div class="form-group row">
							<label class="control-label col-xs-12 col-sm-6 col-lg-2" for="Treasurer"> Treasurer </label>
							<div class="col-xs-12 col-sm-6 col-lg-4">
								<select class="form-control" name="members[]">
                                    <option value="Mr Sudeep Chitlangia">Mr Sudeep Chitlangia</option>  
                                    <option value="Mr Aditya Chamaria">Mr Aditya Chamaria</option>
                                    <option value="Mr Akshay Poddar">Mr Akshay Poddar</option>
                                    <option value="Arun Agrawal">Arun Agrawal</option>  
                                    <option value="Mr Ravi Todi">Mr Ravi Todi</option>
                                    <option value="Mrs Sanaya Mehta Vyas">Mrs Sanaya Mehta Vyas</option>
                                    <option value="Mr Songit Kumar Bagrodia">Mr Songit Kumar Bagrodia</option>
                                  </select>
							</div>
						</div>
						
						<div class="form-group row">
							<label class="control-label col-xs-12 col-sm-6 col-lg-2" for="Process-Keeper"> Process Observer / Time Keeper </label>
							<div class="col-xs-12 col-sm-6 col-lg-4">
								<select class="form-control" name="members[]">
                                    <option value="Mr Sudeep Chitlangia">Mr Sudeep Chitlangia</option>  
                                    <option value="Mr Aditya Chamaria">Mr Aditya Chamaria</option>
                                    <option value="Mr Akshay Poddar">Mr Akshay Poddar</option>
                                    <option value="Arun Agrawal">Arun Agrawal</option>  
                                    <option value="Mr Ravi Todi">Mr Ravi Todi</option>
                                    <option value="Mrs Sanaya Mehta Vyas">Mrs Sanaya Mehta Vyas</option>
                                    <option value="Mr Songit Kumar Bagrodia">Mr Songit Kumar Bagrodia</option>
                                  </select>
							</div>
						</div>
						
						<div class="form-group row">
							<label class="control-label col-xs-12 col-sm-6 col-lg-2" for="Meeting-Booster"> Meeting Booster </label>
							<div class="col-xs-12 col-sm-6 col-lg-4">
								<select class="form-control" name="members[]">
                                    <option value="Mr Sudeep Chitlangia">Mr Sudeep Chitlangia</option>  
                                    <option value="Mr Aditya Chamaria">Mr Aditya Chamaria</option>
                                    <option value="Mr Akshay Poddar">Mr Akshay Poddar</option>
                                    <option value="Arun Agrawal">Arun Agrawal</option>  
                                    <option value="Mr Ravi Todi">Mr Ravi Todi</option>
                                    <option value="Mrs Sanaya Mehta Vyas">Mrs Sanaya Mehta Vyas</option>
                                    <option value="Mr Songit Kumar Bagrodia">Mr Songit Kumar Bagrodia</option>
                                  </select>
							</div>
						</div>
						
						<div class="form-group row">
							<label class="control-label col-xs-12 col-sm-6 col-lg-2" for="Social-Coordinator"> Social Coordinator </label>
							<div class="col-xs-12 col-sm-6 col-lg-4">
								<select class="form-control" name="members[]">
                                    <option value="Mr Sudeep Chitlangia">Mr Sudeep Chitlangia</option>  
                                    <option value="Mr Aditya Chamaria">Mr Aditya Chamaria</option>
                                    <option value="Mr Akshay Poddar">Mr Akshay Poddar</option>
                                    <option value="Arun Agrawal">Arun Agrawal</option>  
                                    <option value="Mr Ravi Todi">Mr Ravi Todi</option>
                                    <option value="Mrs Sanaya Mehta Vyas">Mrs Sanaya Mehta Vyas</option>
                                    <option value="Mr Songit Kumar Bagrodia">Mr Songit Kumar Bagrodia</option>
                                  </select>
							</div>
						</div>
						
						<div class="form-group row">
							<label class="control-label col-xs-12 col-sm-6 col-lg-2" for="Retreat-Chiar"> Retreat Chiar </label>
							<div class="col-xs-12 col-sm-6 col-lg-4">
								<select class="form-control" name="members[]">
                                    <option value="Mr Sudeep Chitlangia">Mr Sudeep Chitlangia</option>  
                                    <option value="Mr Aditya Chamaria">Mr Aditya Chamaria</option>
                                    <option value="Mr Akshay Poddar">Mr Akshay Poddar</option>
                                    <option value="Arun Agrawal">Arun Agrawal</option>  
                                    <option value="Mr Ravi Todi">Mr Ravi Todi</option>
                                    <option value="Mrs Sanaya Mehta Vyas">Mrs Sanaya Mehta Vyas</option>
                                    <option value="Mr Songit Kumar Bagrodia">Mr Songit Kumar Bagrodia</option>
                                  </select>
							</div>
						</div>
						
						<div class="form-group row">
							<label class="control-label col-xs-12 col-sm-6 col-lg-2" for="Member-Goals"> Member Goals </label>
							<div class="col-xs-12 col-sm-6 col-lg-4">
								<select class="form-control" name="members[]">
                                    <option value="Mr Sudeep Chitlangia">Mr Sudeep Chitlangia</option>  
                                    <option value="Mr Aditya Chamaria">Mr Aditya Chamaria</option>
                                    <option value="Mr Akshay Poddar">Mr Akshay Poddar</option>
                                    <option value="Arun Agrawal">Arun Agrawal</option>  
                                    <option value="Mr Ravi Todi">Mr Ravi Todi</option>
                                    <option value="Mrs Sanaya Mehta Vyas">Mrs Sanaya Mehta Vyas</option>
                                    <option value="Mr Songit Kumar Bagrodia">Mr Songit Kumar Bagrodia</option>
                                  </select>
							</div>
						</div>
						
						<div class="form-group row">
							<label class="control-label col-xs-12 col-sm-6 col-lg-2" for="Parking-Lot"> Parking Lot </label>
							<div class="col-xs-12 col-sm-6 col-lg-4">
								<select class="form-control" name="members[]">
                                    <option value="Mr Sudeep Chitlangia">Mr Sudeep Chitlangia</option>  
                                    <option value="Mr Aditya Chamaria">Mr Aditya Chamaria</option>
                                    <option value="Mr Akshay Poddar">Mr Akshay Poddar</option>
                                    <option value="Arun Agrawal">Arun Agrawal</option>  
                                    <option value="Mr Ravi Todi">Mr Ravi Todi</option>
                                    <option value="Mrs Sanaya Mehta Vyas">Mrs Sanaya Mehta Vyas</option>
                                    <option value="Mr Songit Kumar Bagrodia">Mr Songit Kumar Bagrodia</option>
                                  </select>
							</div>
						</div>
						
						<div class="form-group row">
							<label class="control-label col-xs-12 col-sm-6 col-lg-2" for="Communication"> Communication </label>
							<div class="col-xs-12 col-sm-6 col-lg-4">
								<select class="form-control" name="members[]">
                                    <option value="Mr Sudeep Chitlangia">Mr Sudeep Chitlangia</option>  
                                    <option value="Mr Aditya Chamaria">Mr Aditya Chamaria</option>
                                    <option value="Mr Akshay Poddar">Mr Akshay Poddar</option>
                                    <option value="Arun Agrawal">Arun Agrawal</option>  
                                    <option value="Mr Ravi Todi">Mr Ravi Todi</option>
                                    <option value="Mrs Sanaya Mehta Vyas">Mrs Sanaya Mehta Vyas</option>
                                    <option value="Mr Songit Kumar Bagrodia">Mr Songit Kumar Bagrodia</option>
                                  </select>
							</div>
						</div>
						
						<div class="form-group row">
							<label class="control-label col-xs-12 col-sm-6 col-lg-2" for="Time-Keeper"> Time Keeper </label>
							<div class="col-xs-12 col-sm-6 col-lg-4">
								<select class="form-control" name="members[]">
                                    <option value="Mr Sudeep Chitlangia">Mr Sudeep Chitlangia</option>  
                                    <option value="Mr Aditya Chamaria">Mr Aditya Chamaria</option>
                                    <option value="Mr Akshay Poddar">Mr Akshay Poddar</option>
                                    <option value="Arun Agrawal">Arun Agrawal</option>  
                                    <option value="Mr Ravi Todi">Mr Ravi Todi</option>
                                    <option value="Mrs Sanaya Mehta Vyas">Mrs Sanaya Mehta Vyas</option>
                                    <option value="Mr Songit Kumar Bagrodia">Mr Songit Kumar Bagrodia</option>
                                  </select>
							</div>
						</div>
						
						<div class="form-group row">
							<label class="control-label col-xs-12 col-sm-6 col-lg-2" for="Secretary"> Secretary </label>
							<div class="col-xs-12 col-sm-6 col-lg-4">
								<select class="form-control" name="members[]">
                                    <option value="Mr Sudeep Chitlangia">Mr Sudeep Chitlangia</option>  
                                    <option value="Mr Aditya Chamaria">Mr Aditya Chamaria</option>
                                    <option value="Mr Akshay Poddar">Mr Akshay Poddar</option>
                                    <option value="Arun Agrawal">Arun Agrawal</option>  
                                    <option value="Mr Ravi Todi">Mr Ravi Todi</option>
                                    <option value="Mrs Sanaya Mehta Vyas">Mrs Sanaya Mehta Vyas</option>
                                    <option value="Mr Songit Kumar Bagrodia">Mr Songit Kumar Bagrodia</option>
                                  </select>
							</div>
						</div>
						
						<div class="form-group row">
							<label class="control-label col-xs-12 col-sm-6 col-lg-2" for="Chapter-Integration"> Chapter Integration </label>
							<div class="col-xs-12 col-sm-6 col-lg-4">
								<select class="form-control" name="members[]">
                                    <option value="Mr Sudeep Chitlangia">Mr Sudeep Chitlangia</option>  
                                    <option value="Mr Aditya Chamaria">Mr Aditya Chamaria</option>
                                    <option value="Mr Akshay Poddar">Mr Akshay Poddar</option>
                                    <option value="Arun Agrawal">Arun Agrawal</option>  
                                    <option value="Mr Ravi Todi">Mr Ravi Todi</option>
                                    <option value="Mrs Sanaya Mehta Vyas">Mrs Sanaya Mehta Vyas</option>
                                    <option value="Mr Songit Kumar Bagrodia">Mr Songit Kumar Bagrodia</option>
                                  </select>
							</div>
						</div>
						
						
						<div class="clearfix"></div>
						<div class="form-actions form-group">
                            <div class="col-md-12 col-sm-12 col-xs-12 text-center">
                                <center>
                                    <button class="btn btn-success rounded" type="submit"  id="record-save-button" style="margin-right: 10px;">
                                        <img src="images/check.png" class="check-button" alt="Check"> <span>Add Roster</span>
                                    </button>
                                </center>
                            </div>
                            <div class="col-md-4 col-sm-2 hidden-xs"></div>
                        </div>
					</form>	
						
						
						
						
						
						
                    </div>          
                    <!--meeting-agenda-input end-->
                    
                    <!--meeting-agenda-list-view end-->
                    <div class="clearfix"></div>                        
                </div>
				
				
				
				<div class="clearfix"></div>
				<div class="form-actions form-group d-none">
					<div class="col-md-12 col-sm-12 col-xs-12 text-center">
						<center>
							<button class="btn btn-success rounded" type="submit"  id="record-save-button" style="margin-right: 10px;">
								<img src="images/check.png" class="check-button" alt="Check"> <span>Save</span>
							</button>
						</center>
					</div>
				</div>  
            </div>
        </div>
    </div>
</div>
