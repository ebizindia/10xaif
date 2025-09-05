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
                        
                        <form class="form-horizontal" role="form" name='adduserform' id="adduserform" action='feedback.php' method='post' onsubmit="return feedbackfuncs.submitFeedback(this);" target="form_post_submit_target_window"  data-mode="send-feedback" novalidate  >
                        <input type='hidden' name='mode' id='send_feedback' value='sendfeedback' />
                        <div class="alert alert-warning mt-2" role="alert" id="msgFrm">
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
                            <div style="overflow-x:auto;" class="w-100">
                                <table class="table table-striped table-bordered table-hover" style="width: 100%; border-collapse: collapse;" cellspacing="0" cellpadding="0">
                                    <thead>
                                        <tr>
                                            <th style="width: 22%;">Date</th>
                                            <th>Time</th>
                                            <th>Venue</th>
                                        </tr>
                                    </thead>
                                    <tbody id="session-rows">
                                        <tr>
                                            <td>
                                                <input type="date" id="date" placeholder="Enter Date" class="form-control hasDatepicker" value="" style="height:35px;" >
                                            </td>
                                            <td>                                               
												<input type="text" id="" placeholder="Enter Time" class="form-control" value="" > 
                                            </td>
                                            <td>
                                                <input type="text" id="" placeholder="Enter Venue" class="form-control" value="" >
                                            </td>
                                        </tr>
                                        <tr>
                                             <td colspan="3">&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <th>Session</th>
                                            <th colspan="3">Topic</th>
                                        </tr>
                                        <tr>
                                            <td>                                               
												<input type="text" id="" placeholder="Enter Time" class="form-control" value="" > 
                                            </td>
                                             <td><input type="text" id="" placeholder="" class="form-control" value=""></td>
                                             <td></td> <!-- No delete button for the first row -->
                                        </tr>
                                        
                                    </tbody>
                                </table>
                                <button type="button" class="btn btn-primary rounded" onclick="usersfuncs.addRow()">Add Session</button>

                            </div>
                        </div>
                        <div class="clearfix"></div>

                        <div class="card-header-heading ">
                            <div class="row">
                                <div class="col-12">
                                    <h4 class="row pg_heading_line_ht">Minutes of Meeting</h4>  
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 text-center mt-2 mb-3">
                                <textarea  name="myeditor" placeholder="Product Details" class="form-control" rows="12" cols="50"></textarea>
                            </div>
                        </div>

                        <div class="form-actions form-group">
                            <div class="col-md-12 col-sm-12 col-xs-12 text-center">
                                <center>
                                    <button class="btn btn-success rounded" type="submit"  id="record-save-button" style="margin-right: 10px;">
                                        <img src="images/check.png" class="check-button" alt="Check"> <span>Add Meetings</span>
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
<script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>
<!-- script type="text/javascript" src="ckeditor/ckeditor.js"></script -->
<script>
    window.onload = function() {
       CKEDITOR.replace('myeditor');
    };
</script>