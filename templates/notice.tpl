<style>
.checkbox-whatsapp .heading{padding-right:10px;display:inline-block; font-weight:bold;}
.checkbox-whatsapp .check_block{display:inline-block;}
.checkbox-whatsapp input[type='checkbox']{position:relative; top: 5px;}

/********form check box group*****/
.checkbox_group{
    display: block;
    float: left;
    white-space: nowrap;
    margin-bottom: 10px;
    margin-right: 5px;
}
.checkbox_group::after{
    content: "";
    clear: both;
    display: table;
}

.checkbox_group input[type="checkbox"]{
    display: block;
    float: left;
}

.checkbox_group label{
    display: block;
    float: left;
    margin-right: 10px;
    margin-top: -6px;
}

.notice_groups_cont{
    height: 100px;
    border: 1px solid #ced4da;
    padding: 10px;
    overflow: auto;
}
/********form check box group*****/

/***** CKEditor *****/

.ck-editor__editable_inline:not(.ck-comment__input *) {
    min-height: 400px;
}
.ck-editor__editable_inline.ck-read-only {
    background-color: #f1f1f1 !important;
}

/********************/

/******** WA section  **************/

#add_form_field_msgreplacements{
    min-height: 300px;
}

/********************************/


</style>
<div class="row">
    <div id='feedback_form_container' class="col-12 mt-3 mb-2">
        <div class="card">
        <div class="card-body">
            <div class="card-header-heading">
            <div class="row">
                <div class="col"><h4 class="row">Send Email To Members</h4></div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
            <form class="form-horizontal" role="form" name='feedbackform' id="feedbackform" action='notice.php' method='post' onsubmit="return noticefuncs.submitNotice(this);" target="form_post_submit_target_window"  data-mode="sendnotice" novalidate  enctype="multipart/form-data" >
            <input type='hidden' name='mode' id='send_feedback' value='sendnotice' />
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
            <div class="form-group row">
                <label class="control-label col-xs-12 col-sm-4 col-lg-2" for="add_form_field_msgtestnotice"> Send Test Email </label>
                <div class="col-xs-12 col-sm-8 col-lg-6">
                    <input id="add_form_field_msgtestnotice" class="form-control dnd_chkbox" type="checkbox" name='msg_test_notice' value="1" autocomplete="off" checked data-email="<?php echo $this->base_template_data['loggedindata'][0]['profile_details']['email']; ?>" data-wa="<?php echo $this->base_template_data['loggedindata'][0]['profile_details']['mobile']; ?>"  />
                    <div class="form-elem-guide-text default-box" >
                        <span class=" ">Test Email will be sent to your email and mobile.</span>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
            <hr class="my-2 mt-2 mb-4">

            <div class="form-group row   notice_chkbox ">
                <label class="control-label col-xs-12 col-sm-4 col-lg-2" for="add_form_field_groups"> To <span class="mandatory">*</span></label>
                <div class="col-xs-12 col-sm-8 col-lg-6" style="/*border: 1px solid #ced4da; margin-left: 15px; height: 120px; overflow-y: auto;*/"  >
                    <span><a href="#" id="selallgrps" class="togglegrpsel" >Select All</a></span><span style="margin-left: 15px;"  ><a href="#" id="deselallgrps" class="togglegrpsel" >Deselect All</a></span>
                    <div  class="notice_groups_cont"  >
                     <?php 
                          $i = 1;
                          foreach ($this->body_template_data['members'] as $mem) {
                        ?>
                          <span class="checkbox_group">
                            <input type='checkbox' value="<?php echo $mem['id']; ?>" 
                                   name="members[]" 
                                   class="form-control" 
                                   id="add_form_field_member_<?php echo $mem['id']; ?>" 
                                   checked="checked">
                            
                            <label class="control-label" for="add_form_field_member_<?php echo $mem['id']; ?>">
                              <?php echo \eBizIndia\_esc($mem['name']); ?>
                            </label>
                          </span>
                    <?php } ?>

                    <!--    
                    <?php 
                        $i=1;
                        foreach ($this->body_template_data['groups'] as $grp) {
                    ?>
                                <span class="checkbox_group">
                                    <input type='checkbox' value="<?php echo $grp['id']; ?>" name="groups[]" class="form-control" id="add_form_field_groups_<?php echo $grp['id']; ?>" checked="checked"  ><label  class="control-label" for="add_form_field_groups_<?php echo $grp['id']; ?>" ><?php \eBizIndia\_esc($grp['grp']); ?></label>
                                </span>
                           
                    <?php        
                        }
                    ?>
                    -->
                    </div>
                        <!-- <br><input type='checkbox' value="Others" name="groups[]" class="form-control" id="add_form_field_groups_0"  ><label  class="control-label" for="add_form_field_groups_0" >Others (<span style="font-size: 10px;"  >No groups assigned</span>)</label>     -->
                </div>
            </div> 
            

            <div class="form-group row">
                <label class="control-label col-xs-12 col-sm-4 col-lg-2" for="add_form_field_msgsub"> Subject <span class="mandatory">*</span></label>
                <div class="col-xs-12 col-sm-8 col-lg-6">
                    <input type="text" id="add_form_field_msgsub" placeholder="Subject" class="form-control"  name='msg_sub' rows="10" cols="100"   autocomplete="off" maxlength="250"  >
                </div>
            </div>
            <div class="form-group row">
                <label class="control-label col-xs-12 col-sm-4 col-lg-2" for="add_form_field_msgbody"> Message <span class="mandatory">*</span></label>
                <div class="col-xs-12 col-sm-8 col-lg-6">
                    <textarea id="add_form_field_msgbody" placeholder="Enter your message here" class="form-control"  name='msg_body' rows="10" cols="100"   autocomplete="off"  ></textarea>
                    <!-- <div class="col-12 mt-2 small-text">Remaining characters <b><span id="remcount"></span></b> </div> -->
                    <!-- <div class="form-elem-guide-text default-box" >
                      <span class=" "   >The text entered into this box will be mailed to all the active members of the types selected above.</span>
                    </div> -->
                    <div class="form-elem-guide-text default-box" >
                      <span class=" "   >The content entered into this box will be mailed to the recipients.</span>
                      <span class=" "   >The below given placeholders (the text which beings with a "$") if used in the email message will be automatically replaced with the corresponding values from the member's profile when the email will be triggered.<br>
                      <span style="font-style:normal;"   ><?php echo implode(', ', array_keys(CONST_NOTICE_EMAIL_VARS)); ?></span>  
                      <?php 
                        // foreach (CONST_NOTICE_EMAIL_VARS as $ph=>$fld) {
                        //     echo '<span style="font-style:normal;"   >'.\eBizIndia\_esc($ph.' => '.$fld, true), '</span><br>';    
                        // }
                      ?>  
                      </span>
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <label class="control-label col-xs-12 col-sm-4 col-lg-2" for="add_form_field_attachment"> Attachment </label>
                <div class="col-xs-12 col-sm-8 col-lg-6">
                    <input type="file" id="add_form_field_attachment" placeholder="Select a file to attach" class="form-control"  name='attachment' autocomplete="off" accept="<?php echo '.'.implode(', .',$this->body_template_data['attachment_types']); ?>"  ></textarea>
                    <a href="#" id="remove_attachment" >Clear Selection</a>
                    
                    <div class="form-elem-guide-text default-box" >
                      <span class=" "   >You may select a file to send with the email. Allowed file types: <?php echo implode(', ',$this->body_template_data['attachment_types']); ?></span>
                    </div>
                </div>
            </div>
            <?php if(ENABLE_WHATSAPP_MSG == 1){?>
            <div class="row">
                <!-- <div class="col-12"><strong>WhatsApp</strong></div> -->
                <div class="col-12 checkbox-whatsapp"><div class="heading">WhatsApp</div>               
                    <div class="check_block">( <input id="add_form_field_sendwamsg" class="form-control dnd_chkbox" type="checkbox" name="send_via_wa" value="1" autocomplete="off" checked="checked"> <label for="add_form_field_sendwamsg" style="cursor: pointer;"  >Send over WhatsApp too</label> )</div>
                </div>
            </div>
            <hr class="my-2">
            <div class="form-group row">
                <label class="control-label col-xs-12 col-sm-4 col-lg-2" for="add_form_field_msgcampaign"> Campaign <span class="mandatory">*</span></label>
                <div class="col-xs-12 col-sm-8 col-lg-6">
                    <input type="text" id="add_form_field_msgcampaign" placeholder="WhatsApp campaign name" class="form-control"  name='msg_campaign' autocomplete="off" maxlength="250"  >
                </div>
            </div>
            <div class="form-group row">
                <label class="control-label col-xs-12 col-sm-4 col-lg-2" for="add_form_field_msgreplacements"> Replace vars with </label>
                <div class="col-xs-12 col-sm-8 col-lg-6">
                    <textarea id="add_form_field_msgreplacements" placeholder="Enter replacements here" class="form-control"  name='msg_replacements' rows="10" cols="100"   autocomplete="off" maxlength="<?php echo $this->body_template_data['feedback_max_chars']; ?>"  ></textarea>
                    <div class="form-elem-guide-text default-box" >
                        <span class=" ">Valid vars - $fname, $lname, $email, $password, $membership_no, $batch_no, or fixed text.</span>
                    </div>
                </div>
            </div>
            <?php }?>
            <div class="clearfix"></div>
            <div class="form-actions form-group">
                <div class="col-md-12 col-sm-12 col-xs-12 text-center">
                    <center>
                        <button class="btn btn-success rounded" type="submit"  id="record-save-button" style="margin-right: 10px;">
                            <img src="images/check.png" class="check-button" alt="Check"> <span>Send Email</span>
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