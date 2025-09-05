            
	        <div class="card">
                <div class="card-body">
                    <div class="card-header-heading">
                    <div class="row">
                        <div class="col-6"><h4 id="panel-heading-text" class="pull-left row">View Registration&nbsp;<!--<i class="fa fa-info-circle" data-toggle="tooltip" data-placement="right" title="Add new user."></i>--></div>

                        <div class="col-6 text-right">
        <a href="event-bookings.php#" class="btn btn-danger record-list-show-button back-to-list-button row" id="back-to-list-button">
            <!--<i class="fa fa-arrow-left"></i>--><img src="images/left-arrow.png" class="custom-button" alt="Left"> Back To List </a>

   <a href="event-bookings.php#" class="btn btn-danger record-list-show-button back-to-list-button row mobile-bck-to-list"  id="back-to-list-button"><!--<i class="fa fa-arrow-left"></i>--><img src="images/left-arrow.png" class="custom-button" alt="Left"> </a>

                        </div>
                    </div>
                </div>
                    <div class="row  evregview  ">
                        <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                        <form class="form-horizontal" >
                        
                        <!-- <div class="event_name_container">
                        <h4 id="view_form_field_eventname"  ></h4>
                            <div class="form-group row">
                                <div class="col  event_reg_img" >
                                    <img src=""  id="view_form_field_evdskimg" class="dsk_img" />
                                    <img src=""  id="view_form_field_evmobimg" class="mob_img" />
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col" id="view_form_field_evdescription" >
                                    
                                </div>
                            </div>
                        </div> -->
                        
                        <div class="form-group row " >
                            <div class="col-xs-12 col-sm-12 col-lg-12">
                                <span id="view_form_field_eventname" style="display: inline-block; font-weight: bold; font-size: 16px;"  ></span> 
                            </div>
                        </div>
                        <div class="form-group row " >
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="view_form_field_evperiod"  id="view_form_field_evperiod_label" >Date: </label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <span class="form-control" id="view_form_field_evperiod"  ></span>
                            </div>
                        </div>

                        <div class="form-group row " >
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="view_form_field_evtime"   >Time: </label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <span class="form-control" id="view_form_field_evtime"  ></span>
                            </div>
                        </div>


                        <div class="form-group row " >
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="view_form_field_evvenue">Venue: </label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <span class="form-control" id="view_form_field_evvenue"  ></span>
                            </div>
                        </div>

                        <div class="form-group row " >
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="view_form_field_ppt">Price Per Person: </label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <span class="form-control" id="view_form_field_ppt"  ></span>
                            </div>
                        </div>

                        
                        <h4>Registration Details</h4>
                        <hr class="my-2 mt-2 mb-2">

                        <div class="form-group row " >
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="view_form_field_bookingid">Registration ID: </label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <span class="form-control" id="view_form_field_bookingid"  ></span>
                            </div>
                        </div>

                        <div class="form-group row " >
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="view_form_field_registeredon">Registered On: </label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <span class="form-control" id="view_form_field_registeredon"  ></span>
                            </div>
                        </div>

                        <div class="form-group row " >
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="view_form_field_bookedby">Member: </label>
                            <div class="col-xs-12 col-sm-6 col-lg-4 form-control-height-adjust">
                                <span class="form-control" id="view_form_field_bookedby"  ></span>
                                <span class="form-control" id="view_form_field_membershipno" style="font-size: 12px !important; margin-top: 0px; padding-top: 0px; display: inline-block;"  ></span>
                                <span class="form-control" id="view_form_field_membermobile" style="font-size: 12px !important; margin-top: 0px; padding-top: 0px; display: inline-block;"  ></span>
                                <span class="form-control" id="view_form_field_memberemail" style="font-size: 12px !important; margin-top: 0px; padding-top: 0px; display: inline-block;"  ></span>
                            </div>
                        </div>

                        <div class="form-group row " >
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="view_form_field_regstatus">Registration Status: </label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <span class="form-control" id="view_form_field_regstatus"  ></span>
                            </div>
                        </div>
                        
                        <div class="form-group row " >
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="view_form_field_nooftkt">No. of Persons: </label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <span class="form-control" id="view_form_field_nooftkt"  ></span>
                            </div>
                        </div>

                        <div class="form-group row " >
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="view_form_field_attended">Attended: </label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <span class="form-control" id="view_form_field_attended"  ></span>
                            </div>
                        </div>

                        <div class="form-group row " >
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="view_form_field_noshow">No Show: </label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <span class="form-control" id="view_form_field_noshow"  ></span>
                            </div>
                        </div>
                        

                        <h4>Payment Details</h4>
                        <hr class="my-2 mt-2 mb-2">
                        
                        <div class="form-group row " >
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="view_form_field_paymentstatus">Payment Status: </label>
                            <div class="col-xs-12 col-sm-6 col-lg-4 form-control-height-adjust">
                                <span class="form-control" id="view_form_field_paymentstatus"  ></span>
                                <span class="form-control d-none" id="view_form_field_pmtfailuremsg"  ></span>
                            </div>
                        </div>

                        <!-- <div class="form-group row d-none" >
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="view_form_field_pmtfailuremsg">Failure Reason: </label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <span class="form-control" id="view_form_field_pmtfailuremsg"  ></span>
                            </div>
                        </div> -->

                        <div class="form-group row" >
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="view_form_field_amountpaid">Amount: </label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <span class="form-control" id="view_form_field_amountpaid"  ></span>
                            </div>
                        </div>

                        <div class="form-group row d-none" >
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="view_form_field_pmtfees">Gateway Fees: </label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <span class="form-control" id="view_form_field_pmtfees"  ></span>
                            </div>
                        </div>

                        <div class="form-group row d-none" >
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="view_form_field_pmttax">Gateway Taxes: </label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <span class="form-control" id="view_form_field_pmttax"  ></span>
                            </div>
                        </div>

                        <div class="form-group row " >
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="view_form_field_instrument">Instrument: </label>
                            <div class="col-xs-12 col-sm-6 col-lg-4 form-control-height-adjust">
                                <span class="form-control" id="view_form_field_instrtype"  ></span>
                                <span class="form-control" id="view_form_field_instr"  ></span>
                            </div>
                        </div>

                        <div class="form-group row d-none" >
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="view_form_field_bankref">Bank Reference: </label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <span class="form-control" id="view_form_field_bankref"  ></span>
                            </div>
                        </div>
                        

                        <!-- <div class="form-group row " >
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="view_form_field_paymentmode">Payment Mode: </label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <span class="form-control" id="view_form_field_paymentmode"  ></span>
                            </div>
                        </div> -->
                        
                        
        <div class="clearfix"></div>
                        <div class="form-actions form-group">
                            <!-- <div class="col-md-4 col-sm-2 col-lg-2 hidden-xs"></div> -->
                            <div class="col-md-12 col-sm-12 col-xs-12 text-center">
                                <center>
                                    <a href="event-bookings.php#" class="btn btn-danger" type="button"   id="record-add-cancel-button" data-back-to="" >
                                        <!--<i class="fa fa-remove"></i>--><img src="images/delete-white.png" class="custom-button-extra-small" alt="cancel">
                                        Close
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
