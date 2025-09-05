
	        <div class="card">
                <div class="card-body">
                    <div class="card-header-heading">
                    <div class="row">
                        <div class="col-6"><h4 id="panel-heading-text" class="pull-left row">Register<img src="images/info.png" class="info-button" alt="Info"></h4></div>

                        <div class="col-6 text-right">
        <a href="event-registrations.php#" class="btn btn-danger record-list-show-button back-to-list-button row" id="back-to-list-button">
            <img src="images/left-arrow.png" class="custom-button" alt="Left"> Back To Events List </a>

   <a href="event-registrations.php#" class="btn btn-danger record-list-show-button back-to-list-button row mobile-bck-to-list"  id="back-to-list-button"><img src="images/left-arrow.png" class="custom-button" alt="Left"> </a>

                        </div>
                    </div> 
                </div>

                    <div class="row mb-3 d-none">
                        <div class="col" >
                            <select class="form-control" id="event_selector" style="height: 35px !important;
    border: 2px solid #919191 !important;" >
                                    <option value="">-- Select an event to register --</option>
                                <?php  foreach($this->body_template_data['events'] as $ev){ ?>
                                   <option value="<?php echo htmlentities($ev['id']) ?>"><?php echo htmlentities($ev['name']); ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="row d-none" id="booking_success_msg"  >
                         <div class="col booking_success_msg" >
                                <h4>Thank You!</h4>
                                <span class="msg-text"  ></span><br><br>
                                <!-- <span style="font-style: italic; font-size: 11px; color: #cc4b00; margin-top: 10px;"  >You may do further bookings by selecting the desired event from the above list.</span> -->
                         </div>   
                    </div>
                    <div class="row  d-none"  id="event_details_n_reg_section"  >
                        <div class="col" >
                            <form class="form-horizontal" role="form" name='addrecform' id="addrecform" action='event-registrations.php' method='post' onsubmit="return evregfuncs.saveRecDetails(this);" target="form_post_submit_target_window"  data-mode="add-rec" enctype="multipart/form-data" novalidate  >
                                <input type='hidden' name='mode' id='add_edit_mode' value='createrec' />
                                <input type='hidden' name='recordid' id='add_edit_recordid' value='' />
                                <input type='hidden' name='event_id' id='add_edit_eventid' value='' />
                                <input type='hidden' name='offer' id='add_edit_offer' value='' />
                                <!-- <div class="alert alert-warning mt-2" role="alert" id="msgFrm">
                                <p style="margin-bottom: 0">All fields marked with an asterisk (<span class="required">*</span>) are required.</p>
                                </div> -->

                                <div class="alert alert-danger d-none">
                                    <strong><i class="icon-remove"></i></strong>
                                    <span class="alert-message"></span>
                                </div>
                                <div class="alert alert-success d-none">
                                    <strong><i class="icon-ok"></i></strong>
                                    <span class="alert-message"></span>
                                </div>
                                <div class="event_name_container">  
                                    <h4 id="add_form_field_eventname"  ></h4>
                                    <div class="form-group row">
                                        <div class="col  event_reg_img" >
                                            <img src=""  id="add_form_field_evdskimg" class="dsk_img" />
                                            <img src=""  id="add_form_field_evmobimg" class="mob_img" />
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col" id="add_form_field_evdescription" >
                                            
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row " >
                                    <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_evperiod" id="add_form_field_evperiod_label"  >Date: </label>
                                    <div class="col-xs-12 col-sm-6 col-lg-4">
                                        <span class="form-control" id="add_form_field_evperiod"  ></span>
                                    </div>
                                </div>

                                <div class="form-group row " >
                                    <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_evtime">Time: </label>
                                    <div class="col-xs-12 col-sm-6 col-lg-4">
                                        <span class="form-control" id="add_form_field_evtime"  ></span>
                                    </div>
                                </div>

                                <div class="form-group row " >
                                    <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_evvenue">Venue: </label>
                                    <div class="col-xs-12 col-sm-6 col-lg-4">
                                        <span class="form-control" id="add_form_field_evvenue"  ></span>
                                    </div>
                                </div>

                                <div class="form-group row " >
                                    <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_ppt">Price Per Person: </label>
                                    <div class="col-xs-12 col-sm-6 col-lg-4">
                                        <span class="form-control" id="add_form_field_ppt"  ></span>
                                        <!-- <span class="form-control"  > ( Including GST) </span> -->
                                    </div>
                                </div>


                                <!-- <div class="form-group row " >
                                    <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_evregperiod">Registration Allowed Till: </label>
                                    <div class="col-xs-12 col-sm-6 col-lg-4">
                                        <span class="" id="add_form_field_evregperiod"  ></span>
                                    </div>
                                </div> -->


                                <h4>Registration Details</h4>
                                <hr class="my-2 mt-2 mb-2">

                                <div  id="flds_for_ev_registration" class="d-none"  >

                                    <div class="form-group row">
                                        <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_nooftickets">No. of Persons: <span class="mandatory">*</span></label>
                                        <div class="col-xs-12 col-sm-6 col-lg-4">
                                            <input type="text" id="add_form_field_nooftickets" placeholder="No. of persons" class="form-control"  name='no_of_tickets' value="" maxlength='3' inputmode="numeric"  autocomplete="off" data-max="0" data-ppt="0"    />
                                            <div class="form-elem-guide-text default-box" >
                                              <span class=" "   >Maximum persons allowed: <span id="max_tkts_available_for_booking"  ></span> </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group row " >
                                        <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_baseamount">Base Amount: </label>
                                        <div class="col-xs-12 col-sm-6 col-lg-4">
                                            <span class="form-control" id="add_form_field_baseamount"  ></span>
                                        </div>
                                    </div>


                                    <div class="form-group row d-none" ><!--to hide the GST line for MYM-->
                                        <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_gstamount">GST Amount: </label>
                                        <div class="col-xs-12 col-sm-6 col-lg-4">
                                            <span class="form-control" id="add_form_field_gstamount"  ></span>
                                        </div>
                                    </div>

                                    <div class="form-group row " >
                                        <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_convfee">Convenience Fee: </label>
                                        <div class="col-xs-12 col-sm-6 col-lg-4">
                                            <span class="form-control" id="add_form_field_convfee"  ></span>
                                        </div>
                                    </div>


                                    <div class="form-group row " >
                                        <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_roffdiscount">R/Off Discount: </label>
                                        <div class="col-xs-12 col-sm-6 col-lg-4">
                                            <span class="form-control" id="add_form_field_roffdiscount"  ></span>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group row " >
                                        <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_totalamount">Amount Payable: </label>
                                        <div class="col-xs-12 col-sm-6 col-lg-4">
                                            <span class="form-control" id="add_form_field_totalamount"  ></span>
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
                                                <!-- <a href="event-registrations.php#" class="btn btn-danger d-none" type="button"   id="record-add-cancel-button" data-back-to=""    onclick="evregfuncs.closeAddUserForm();">
                                                    <img src="images/cancel-black.png" class="custom-button-extra-small" alt="cancel">
                                                    Cancel
                                                </a> -->
                                                
                                                <div  id="payment_btn_cont" class="d-none" >
                                                    <a href="#"  rel="im-checkout" data-behaviour="remote" data-style="no-style" data-text="Pay & Register"   ></a>
                                                </div>
                                                <script src="https://d2xwmjc4uy2hr5.cloudfront.net/im-embed/im-embed.min.js"></script>
                                            </center>
                                        </div>
                                        <div class="col-md-4 col-sm-2 hidden-xs"></div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>



