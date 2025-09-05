<style>
    #add_form_field_msgbody{
        min-height: 270px;
    }
</style>
<div class="row">
    <div id='feedback_form_container' class="col-12 mt-3 mb-2">
		<div class="card">
        <div class="card-body">
        	<div class="card-header-heading">
        	<div class="row">
                <div class="col"><h4 class="row">Send Feedback To Admins</h4></div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
            <form class="form-horizontal" role="form" name='feedbackform' id="feedbackform" action='feedback.php' method='post' onsubmit="return feedbackfuncs.submitFeedback(this);" target="form_post_submit_target_window"  data-mode="send-feedback" novalidate  >
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
            <hr class="my-2 mt-2 mb-4">
            <div class="form-group row">
                <label class="control-label col-xs-12 col-sm-4 col-lg-2" for="add_form_field_msgbody"> Feedback Message <span class="mandatory">*</span></label>
                <div class="col-xs-12 col-sm-8 col-lg-6">
                    <textarea id="add_form_field_msgbody" placeholder="Enter your message here" class="form-control"  name='msg_body' rows="10" cols="100"   autocomplete="off" maxlength="<?php echo $this->body_template_data['feedback_max_chars']; ?>"  ></textarea>
                    <!-- <div class="col-12 mt-2 small-text">Remaining characters <b><span id="remcount"></span></b> </div> -->
                    <div class="form-elem-guide-text default-box" >
                      <span class=" "   >The text entered into this box will be mailed to all the admins.</span>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="form-actions form-group">
                <div class="col-md-12 col-sm-12 col-xs-12 text-center">
                    <center>
                        <button class="btn btn-success rounded-pill" type="submit"  id="record-save-button" style="margin-right: 10px;">
                            <img src="images/check.png" class="check-button" alt="Check"> <span>Send Feedback</span>
                        </button>
                    </center>
                </div>
                <div class="col-md-4 col-sm-2 hidden-xs"></div>
            </div>
            </form>
            </div>
        </div>

	</div>
	</div>
</div>

</div>
