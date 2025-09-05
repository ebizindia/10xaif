<style >
   
    #add_form_field_ebenddt_picker + img
    {
        position: absolute;
        z-index: 9;
        right: -1px;
        top: 22px;
    }     


</style>
	        <div class="card">
                <div class="card-body">
                    <div class="card-header-heading">
                    <div class="row">
                        <div class="col-6"><h4 id="panel-heading-text" class="pull-left row">Create Event&nbsp;<!--<i class="fa fa-info-circle" data-toggle="tooltip" data-placement="right" title="Add new user."></i>--><img src="images/info.png" class="info-button" alt="Info"></h4></div>

                        <div class="col-6 text-right">
        <a href="events.php#" class="btn btn-danger record-list-show-button back-to-list-button row" id="back-to-list-button">
            <!--<i class="fa fa-arrow-left"></i>--><img src="images/left-arrow.png" class="custom-button" alt="Left"> Back To List </a>

   <a href="events.php#" class="btn btn-danger record-list-show-button back-to-list-button row mobile-bck-to-list"  id="back-to-list-button"><!--<i class="fa fa-arrow-left"></i>--><img src="images/left-arrow.png" class="custom-button" alt="Left"> </a>

                        </div>
                    </div>
                </div>
                    <div class="row">
                        <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                        <form class="form-horizontal" role="form" name='addrecform' id="addrecform" action='events.php' method='post' onsubmit="return evfuncs.saveRecDetails(this);" target="form_post_submit_target_window"  data-mode="add-rec" enctype="multipart/form-data" novalidate  >
                        <input type='hidden' name='mode' id='add_edit_mode' value='createrec' />
                        <input type='hidden' name='recordid' id='add_edit_recordid' value='' />
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
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_name"> Name <span class="mandatory">*</span></label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <input type="text" id="add_form_field_name" placeholder="Name of the event" class="form-control"  name='name' value="" maxlength='150'   autocomplete="off"  />
                                <div class="form-elem-guide-text default-box" >
                                  <span class=" "   >Only alphabets, numbers, hyphen, underscore and spaces are allowed.</span>
                                </div>
                                <div id="event_booking_link_cont" class="d-none"  >
                                    <span><a href="#"  >Click here to copy the registration link.</a></span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_description"> Description <span class="mandatory">*</span></label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <textarea id="add_form_field_description" placeholder="Describe the event" class="form-control"   name='description' autocomplete="off" rows="5" cols="50" ></textarea>
                                <div class="form-elem-guide-text default-box" >
                                  <span class=" "   >Write some text describing the event.</span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_venue"> Venue <span class="mandatory">*</span></label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <textarea id="add_form_field_venue" placeholder="Event's venue" class="form-control"   name='venue' autocomplete="off" rows="5" cols="50" ></textarea>
                                <div class="form-elem-guide-text default-box" >
                                  <span class=" "   >Address and exact location of the event.</span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_dskimg"> Image (Desktop Screen) <span class="mandatory">*</span></label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <div class="ad_banner_image">
                                    <img src="" alt='Desktop banner image' id="dsk_banner_img" class="ad_banner_img" style="max-width: 100%;"  >
                                </div>
                                <input type="file" id="add_form_field_dskimg" placeholder="Desktop screen's image" class="form-control"  name='dsk_img' value="" accept="<?php echo '.'.implode(', .',$this->body_template_data['field_meta']['event']['file_types']); ?>" style="margin-top: 5px;" />
                                <div class="form-elem-guide-text default-box" >
                                  <span class=" "   >Allowed file types: <?php \eBizIndia\_esc(implode(', ',$this->body_template_data['field_meta']['event']['file_types'])); ?></span>
                                  <br><span>Recommended size: 600px X 600px or less</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_mobimg"> Image (Mobile Screen) <span class="mandatory">*</span></label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <div class="ad_banner_image">
                                    <img src="" alt='Mobile banner image' id="mob_banner_img" class="ad_banner_img"  style="max-width: 100%;"   >
                                </div>
                                <input type="file" id="add_form_field_mobimg" placeholder="Mobile screen's image" class="form-control"  name='mob_img' value="" accept="<?php echo '.'.implode(', .',$this->body_template_data['field_meta']['event']['file_types']); ?>" style="margin-top: 5px;" />
                                <div class="form-elem-guide-text default-box" >
                                  <span class=" "   >Allowed file types: <?php \eBizIndia\_esc(implode(', ',$this->body_template_data['field_meta']['event']['file_types'])); ?></span>
                                  <br><span>Recommended size: 400px X 400px or less</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_startdt_picker"> Start Date <span class="mandatory">*</span></label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <input type="hidden" name="start_dt"   id="add_form_field_startdt" value="" >
                                <input type="text" id="add_form_field_startdt_picker" placeholder="Event starts on " class="form-control"  value="" maxlength='10' autocomplete="off" readonly />
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_enddt_picker"> End Date <span class="mandatory">*</span></label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <input type="hidden" name="end_dt"   id="add_form_field_enddt" value="" >
                                <input type="text" id="add_form_field_enddt_picker" placeholder="Event ends on" class="form-control"  value="" maxlength='10' autocomplete="off" readonly />
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_timetext"> Time <span class="mandatory">*</span></label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <input type="text" id="add_form_field_timetext" placeholder="Event time info" class="form-control"  name='time_text' value="" maxlength='100'   autocomplete="off"  />
                                <div class="form-elem-guide-text default-box" >
                                  <span class=" "   >Write some text to give information about the time of the event.</span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_maxtktperperson"> Max Persons <span class="mandatory">*</span></label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <input type="text" id="add_form_field_maxtktperperson" placeholder="Maximum persons per member" class="form-control"  name='max_tkt_per_person' value="" maxlength='3' inputmode="numeric"  autocomplete="off"  />
                                <div class="form-elem-guide-text default-box" >
                                  <span class=" "   >Maximum persons allowed per member.</span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_tktprice"> Regular Price (&#8377;) <span class="mandatory">*</span></label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <input type="text" id="add_form_field_tktprice" placeholder="Regular price per person" class="form-control"  name='tkt_price' value="" maxlength='5' inputmode="numeric"  autocomplete="off"  />
                                <div class="form-elem-guide-text default-box" >
                                  <span class=" "   >Price per person in Rupee. Enter 0 to allow free registrations.</span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_ebtktpricechk"> Early&nbsp;Bird&nbsp;Price&nbsp;(₹)&nbsp;</label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <div  style="margin-top: -8px;"  >
                                    <input type="checkbox" id="add_form_field_ebtktpricechk" class="form-control dnd_chkbox" name="early_bird" value="y" style="display: inline-block;">
                                    <input type="text" id="add_form_field_ebtktprice" placeholder="Early bird price per person" class="form-control" name="early_bird_tkt_price" value="" maxlength="5" inputmode="numeric" autocomplete="off" style="width:170px; display: inline-block; vertical-align: text-bottom; margin-left: 10px; margin-top: 7px; position: absolute;">
                                    <div class="form-elem-guide-text default-box">
                                        <span class=" " >Early bird price per person in Rupee. Tick the small box to enter the price in the price box</span>
                                    </div>
                                </div>
                                <div  id="early_bird_pricing_rules" class="d-none"  >   
                                    <div class="mt-3">
                                        <label style="display: inline-block;">
                                            <input type="checkbox" id="add_form_field_ebenddtchk" class="form-control dnd_chkbox" style="display: inline-block;"><span style="display: inline-block; padding-left:10px;position:relative; top:-10px;padding-right:10px;cursor:pointer;">Offer valid till</span>
                                        </label>                                                    
                                        <div style="display:inline-block;position:relative;">   
                                            <input type="hidden" name="early_bird_end_dt" id="add_form_field_ebenddt" value="">
                                            <input type="text" id="add_form_field_ebenddt_picker" placeholder="Early bird offer ends on" class="form-control" value="" maxlength="10" autocomplete="off" readonly="" style="width: 170px;position: relative;top: -10px;"><!-- <img class="ui-datepicker-trigger" src="./Manage Events _ Yi Members&#39; Directory_files/calendar.png" alt="..." title="..." style="margin-top:-33px;margin-right: 6px;"> -->
                                        </div>
                                    </div>
                                    <div class="mt-1">
                                        <label style="display: inline-block;">
                                        <input type="checkbox" id="add_form_field_ebmaxcntchk" class="form-control dnd_chkbox" >
                                        <span style="display:inline-block; padding-left:5px;position: relative; top: -10px;padding-right:5px;cursor:pointer;">Applies to the first</span>
                                        </label>
                                        <input type="text" id="add_form_field_ebmaxcnt" placeholder="Early bird" class="form-control" name="early_bird_max_cnt" value="" maxlength="5" inputmode="numeric" autocomplete="off" style="width: 60px; display:inline-block;  vertical-align: text-bottom;margin-right:5px;"><span style="display: inline-block; position: relative;  top: -10px;"> persons.</span>
                                    </div>
                                    <div class="form-elem-guide-text default-box" id="eb_applicability_text">
                                        <span class=" ">Offer applies to the first 100 registrations done within 1st January 2025.</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- <div class="form-group row">
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_ebtktprice"> Early Bird Price (&#8377;) <span class="mandatory">*</span></label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                
                                    <input type="checkbox" id="add_form_field_ebenddtchk" class="form-control dnd_chkbox"  name='early_bird_end_dt' value="y" />
                                
                                    <input type="text" id="add_form_field_ebtktprice" placeholder="Early bird price per person" class="form-control"  name='tkt_price' value="" maxlength='5' inputmode="numeric"  autocomplete="off"  />
                                <div class="form-elem-guide-text default-box" >
                                  <span class=" "   >Early bird price per person in Rupee.</span>
                                </div>

                                <div class="form-group row">
                                    <label>
                                        <input type="checkbox" id="add_form_field_ebenddtchk" class="form-control dnd_chkbox"  name='early_bird_end_dt' value="y" />
                                         Offer valid till 
                                    </label>
                                    <div class="col">
                                        <input type="hidden" name="early_bird_end_dt" id="add_form_field_ebenddt" value="" >
                                        <input type="text" id="add_form_field_ebenddt_picker" placeholder="Early bird offer ends on" class="form-control"  value="" maxlength='10' autocomplete="off" readonly />
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label>
                                        <input type="checkbox" id="add_form_field_ebcntchk" class="form-control dnd_chkbox"  name='early_bird_max_cnt' value="y" />
                                        Offer applies to the first  
                                    </label>
                                    <div class="col">
                                         <input type="text" id="add_form_field_ebmaxcnt" placeholder="Early bird" class="form-control"  name='early_bird_max_cnt' value="0" maxlength='5' inputmode="numeric"  autocomplete="off"  />
                                    </div>
                                    registrations.
                                </div>
                                <div class="form-elem-guide-text default-box"  id="eb_applicability_text"  >
                                  <span class=" "   >Offer applies to the first 100 registrations done within 1st January 2025.</span>
                                </div>
                            </div>
                        </div> -->

                        <div class="form-group row d-none"><!--to hide the GST line for MYM-->
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_gstperc"> GST Rate (%) <span class="mandatory">*</span></label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <input type="text" id="add_form_field_gstperc" placeholder="GST rate %" class="form-control"  name='gst_perc' value="0" maxlength='5' inputmode="numeric"  autocomplete="off"  />
                                <div class="form-elem-guide-text default-box" >
                                  <span class=" "   >GST rate as percentage. Enter 0 for none.</span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_convfee"> Convenience Fee (&#8377;) <span class="mandatory">*</span></label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <input type="text" id="add_form_field_convfee" placeholder="Convenience fee per person" class="form-control"  name='conv_fee' value="" maxlength='5' inputmode="numeric"  autocomplete="off"  />
                                <div class="form-elem-guide-text default-box" >
                                  <span class=" "   >Convenience fee per person. Enter 0 for none.</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_regstartdt_picker"> Registration Starts On </label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <input type="hidden" name="reg_start_dt"   id="add_form_field_regstartdt" value="" >
                                <input type="text" id="add_form_field_regstartdt_picker" placeholder="Registration starts from " class="form-control"  value="" maxlength='10' autocomplete="off" readonly />
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_regenddt_picker"> Registration Ends On </label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <input type="hidden" name="reg_end_dt"   id="add_form_field_regenddt" value="" >
                                <input type="text" id="add_form_field_regenddt_picker" placeholder="Registration ends on " class="form-control"  value="" maxlength='10' autocomplete="off" readonly />
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_regactive_y"> Registration Allowed? </label>
                            <div class="form-check form-check-inline pl-3">
                                <label class="form-check-label">
                                    <input id="add_form_field_regactive_y" class="form-check-input" type="radio" name="reg_active" value='y' />
                                    Yes
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <label class="form-check-label">
                                    <input  id='add_form_field_regactive_n'  name="reg_active" class="form-check-input" type="radio" value='n' />
                                    No
                                </label>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_status_y"> Active </label>
                            <div class="form-check form-check-inline pl-3">
                                <label class="form-check-label">
                                    <input id="add_form_field_status_y" class="form-check-input" type="radio" name="active" value='y' />
                                    Yes
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <label class="form-check-label">
                                    <input  id='add_form_field_status_n'  name="active" class="form-check-input" type="radio" value='n' />
                                    No
                                </label>
                            </div>
                        </div> 
                        
        <div class="clearfix"></div>
                        <div class="form-actions form-group">
                            <!-- <div class="col-md-4 col-sm-2 col-lg-2 hidden-xs"></div> -->
                            <div class="col-md-12 col-sm-12 col-xs-12 text-center">
                                <center>
                                    <button class="btn btn-success rounded-pill" type="submit"  id="record-save-button" style="margin-right: 10px;">
                                        <!-- <i class="fa fa-check"></i>--><img src="images/check.png" class="check-button" alt="Check"> <span>Create Event</span>
                                    </button>
                                    <a href="events.php#" class="btn btn-danger d-none" type="button"   id="record-add-cancel-button" data-back-to=""    onclick="evfuncs.closeAddUserForm();">
                                        <!--<i class="fa fa-remove"></i>--><img src="images/cancel-black.png" class="custom-button-extra-small" alt="cancel">
                                        Cancel
                                    </a>
                                </center>
                            </div>
                            <div class="col-md-4 col-sm-2 hidden-xs"></div>
                        </div>
                        </form>
                        </div>
                    </div>
                </div>
            </div>



