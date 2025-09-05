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
    .ck-editor__editable_inline {
    min-height: 250px;
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
									<a href="meetings.php" class="btn btn-danger record-list-show-button back-to-list-button rounded" id="back-to-list-button">
									<!--<i class="fa fa-arrow-left"></i>--><img src="images/left-arrow.png" class="custom-button" alt="Left"> Back To List </a>
									
									
									<a href="meetings.php#mode=addUser" class="btn btn-danger record-add-button rounded mobile-bck-to-list" id="back-to-list-button"><!--<i class="fa fa-plus"></i>--><img src="images/left-arrow.png" class="custom-button" alt="Left"></a>
								</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <!--meeting-agenda-input start-->
                    
                    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12 meeting-agenda-input">
                        
                        <form class="form-horizontal" role="form" name='addmeetform' id="addmeetform" action='meetings.php' method='post' onsubmit="return meetfuncs.saveRecDetails(this);" target="form_post_submit_target_window"  data-mode="add-rec" enctype="multipart/form-data" novalidate  >
                        <input type='hidden' name='mode' id='add_edit_mode' value='createrec' />
                        <input type='hidden' name='recordid' id='add_edit_recordid' value='' />
                        <div class="alert alert-warning mt-2 d-none" role="alert" id="msgFrm1">
                            <p style="margin-bottom: 0">Field marked with an asterisk (<span class="required">*</span>) is required.</p>
                        </div>

                        <div class="alert alert-danger d-none">
                            <strong><i class="icon-remove"></i></strong>
                            <span class="alert-message"></span>
                        </div>
                        <div class="alert alert-success d-none">
                            <strong><i class="icon-ok"></i></strong>
                            <span class="alert-message"></span>
                        </div>
                        
                        <div class="form-group">
                          <div class="table-responsive">
                           <table class="table table-striped table-bordered table-hover" style="margin-bottom:0rem;">
                              <thead>
                                <tr>
                                  <th style="width: 15%;">From Date</th>
                                  <th style="width: 15%;">To Date</th>
                                  <th style="width: 10%;">Time</th>
                                  <th>Title</th>
                                  <th>Venue</th>
                                </tr>
                              </thead>
                              <tbody>
                                <tr>
                                  <td>
                                    <input type="date" name="meet_date" id="meet_date" placeholder="Enter Date" class="form-control" style="height:35px;" value="<?php echo date('Y-m-d'); ?>">
                                  </td>
                                  <td>
                                    <input type="date" name="meet_date_to" id="meet_date_to" placeholder="Enter Date" class="form-control" style="height:35px;" value="<?php echo date('Y-m-d'); ?>">
                                  </td>
                                  <td>
                                    <input type="text" name="meet_time" id="meet_time" placeholder="Enter Time" class="form-control">
                                  </td>
                                  <td>
                                    <input type="text" name="meet_title" id="meet_title" placeholder="Enter Title" class="form-control">
                                  </td>
                                  <td>
                                    <input type="text" name="venue" id="venue" placeholder="Enter Venue" class="form-control">
                                  </td>
                                </tr>
                               </tbody>
                            </table>    

                            <table class="table table-striped table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th style="width: 400px;">Session</th>
                                        <th colspan="2">Topic</th>
                                    </tr>
                                </thead>
                                <tbody id="session-rows">
                                    <tr>
                                      <td valign="top" style="vertical-align: top;">
                                        <div class="form-row">
                                          <div class="col">
                                            <input type="date" name="session_meet_date[]" id="session_meet_date_0" placeholder="Enter Date" class="form-control" value="<?php echo date('Y-m-d'); ?>" >
                                          </div>
                                          <div class="col-auto d-flex align-items-center">From</div>
                                          <div class="col">
                                            <input type="text" name="time_from[]" id="time_from_0" placeholder="Start Time" class="form-control">
                                          </div>
                                          <div class="col-auto d-flex align-items-center">to</div>
                                          <div class="col">
                                            <input type="text" name="time_to[]" id="time_to_0" placeholder="End Time" class="form-control">
                                          </div>
                                        </div>
                                      </td>
                                      <td>
                                        <!-- input type="text" name="topic[]" placeholder="Enter Topic" class="form-control" -->
                                        <textarea name="topic[]" id="topic_0"  rows="3" placeholder="Enter Topic" class="form-control"></textarea>
                                      </td>
                                      <td style="width: 60px;"></td> <!-- Reduced width for empty cell -->
                                    </tr>
                                  </tbody>
                                </table>
                            <button type="button" class="btn btn-primary rounded" onclick="meetfuncs.addRow()">Add Session</button>
                          </div>
                        </div>
                        <div class="clearfix"></div>
                        <div id="minutesBlock"  class="d-none">
                        <div class="card-header-heading ">
                            <div class="row">
                                <div class="col-12">
                                    <h4 class="row pg_heading_line_ht">Minutes of Meeting</h4>  
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 text-center mt-2 mb-3">
                                <textarea name="minutes" id="minutes" class="form-control" rows="10"></textarea>
                               
                            </div>
                        </div>
                    </div>
                       
                        <div class="form-actions form-group">
                            <div class="col-md-12 col-sm-12 col-xs-12 text-center">
                                <center>
                                    <button class="btn btn-success rounded" type="submit"  id="record-save-button" style="margin-right: 10px;">
                                        <img src="images/check.png" class="check-button" alt="Check"> <span>Add Meeting</span>
                                    </button>
                                    <input type="hidden" id="rec_id" name="rec_id" value="<?php echo (int)$_GET['id']; ?>">
                                    <!-- button type="button" class="btn btn-info rounded" id="download-agenda-btn" onclick="meetfuncs.downloadAgenda(document.getElementById('add_edit_recordid').value)" style="display: none; margin-left: 10px;">
                                        <img src="images/download.png" class="custom-button" alt="Download"> Download Agenda
                                    </button -->
                                    <a href="" class="btn btn-info rounded" id="download-agenda-btn" onclick="return meetfuncs.downloadAgenda(document.getElementById('add_edit_recordid').value)" style="display: none; margin-left: 10px;" download>
                                        <img src="images/download.png" class="custom-button" alt="Download"> Download Agenda
                                    </a>
                                    
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
				<div class="form-actions form-group d-none" style="display: none;">
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
 <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
                                <!-- script>
                                    // Initialize CKEditor for the textarea
                                    ClassicEditor.create(document.querySelector('#minutes'))
                                        .catch(error => {
                                            console.error('There was a problem initializing the editor:', error);
                                        });
                                </script -->