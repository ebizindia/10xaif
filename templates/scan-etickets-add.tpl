<div class="row"   >
    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12" id="scantkt-form-section" >
        <div class="alert alert-danger d-none">
            <strong><i class="icon-remove"></i></strong>
            <span class="alert-message"></span>
        </div>
        <div class="alert alert-success d-none">
            <strong><i class="icon-ok"></i></strong>
            <span class="alert-message"></span>
        </div>
        <div class="alert alert-warning d-none">
            <strong><i class="icon-ok"></i></strong>
            <span class="alert-message"></span>
        </div>
        <form class="form-horizontal d-none" role="form" name='addrecform' id="addrecform" action='<?php echo CONST_CURR_SCRIPT ?>' method='post' onsubmit="return scantktfuncs.saveRecDetails(this);" target="form_post_submit_target_window" data-mode="add-rec" enctype="multipart/form-data" novalidate >
            <input type='hidden' name='mode' id='add_edit_mode' value='enterevent' />
            <input type='hidden' name='tkt_code' id='add_form_field_tc' value='' />
            <!-- <input type='hidden' name='ref_amt' id='add_form_field_refamt' value='' />
            <input type='hidden' name='vcard_id' id='add_form_field_vcardid' value='' /> -->
                                

            <div class="form-group" id="tkt_details_cont"   >
                <div class="card-block-one mt-1">
                    <h4 style="font-weight:600;font-size:16px;background:#e7f1f7;padding:10px;" id="event_name" colspan="2">Test Event 2</h4>
                    <table class="vendor-payment" style="width: 50%; border-collapse: collapse;" cellspacing="0" cellpadding="0">
                    <tbody>
                        <tr>
                            <td>Registrant: </td>
                            <td id="mem_name"></td>
                        </tr>
                        <tr>
                            <td>Registration&nbsp;ID: </td>
                            <td id="booking_id">YI/24-25/00020</td>
                        </tr>
                        <tr>
                            <td>Persons&nbsp;Allowed: </td>
                            <td id="guests_allowed">1</td>
                        </tr>
                        <tr>
                            <td>No. of Persons:</td>
                            <td>
                                <input type="text" name="no_of_guests" id="add_form_field_noofguests" value="1" inputmode="numeric" autocomplete="off" class="form-control" style="width:150px;" maxlength='2' >
                            </td>
                        </tr>
                    </tbody>
                </table>  
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="form-actions form-group no-border">
                <div class="col-md-12 col-sm-12 col-xs-12 text-center">
                    <center>
                        <button class="action-button btn btn-success issue_button" type="submit" id="record-save-button" style="margin-right: 10px;margin-bottom:15px;">
                        <img src="images/check.png" class="check-button" alt="Check"> <span>Allow Entry</span>
                        </button>
                        <a href='#' class="action-button btn btn-danger" id="record-cancel-button" style="font-size:18px;display:inline-block;margin-bottom:15px;" >
                            <img src="images/cross.png" class="cross-button" alt="cross"> <span>Cancel</span>
                        </a>
                    </center>
                </div>
                <div class="col-md-4 col-sm-2 hidden-xs"></div>
            </div>

        </form>
    </div>

    <div  class="col-md-12 col-sm-12 col-xs-12 col-lg-12" id="scanner_screen_cont" style="display:none;" >
        <div id="scanner-container" >
            <div id="qr-reader" style="width: 100%;"></div>
            <!-- <button id="stopScan">Close Scanner</button> -->
        </div>
    </div>

</div>