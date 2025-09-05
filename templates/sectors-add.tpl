
	        <div class="card">
                <div class="card-body">
                    <div class="card-header-heading">
                    <div class="row">
                        <div class="col-6"><h4 id="panel-heading-text" class="pull-left row">Add Sectors&nbsp;<!--<i class="fa fa-info-circle" data-toggle="tooltip" data-placement="right" title="Add new user."></i>--><img src="images/info.png" class="info-button" alt="Info"></h4></div>

                        <div class="col-6 text-right">
        <a href="sectors.php#" class="btn btn-danger record-list-show-button back-to-list-button row" id="back-to-list-button">
            <!--<i class="fa fa-arrow-left"></i>--><img src="images/left-arrow.png" class="custom-button" alt="Left"> Back To List </a>

   <a href="sectors.php#" class="btn btn-danger record-list-show-button back-to-list-button row mobile-bck-to-list"  id="back-to-list-button"><!--<i class="fa fa-arrow-left"></i>--><img src="images/left-arrow.png" class="custom-button" alt="Left"> </a>

                        </div>
                    </div>
                </div>
                    <div class="row">
                        <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                        <form class="form-horizontal" role="form" name='addrecform' id="addrecform" action='sectors.php' method='post' onsubmit="return sectorfuncs.saveRecDetails(this);" target="form_post_submit_target_window"  data-mode="add-rec" enctype="multipart/form-data" novalidate  >
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
                        
                        <!-- Sector entry box for the add screen -->
                        <div class="form-group row addonly">
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_sector"> Sector <span class="mandatory">*</span></label>
                            <div class="col-xs-12 col-sm-6 col-lg-5">
                                <textarea id="add_form_field_sector" placeholder="One or more sector names, each on a new line..." class="form-control"  name='sector' autocomplete="off" style="height: 200px;"  ></textarea>
                                <div class="form-elem-guide-text default-box" >
                                  <span class=" "   >A sector cannot be more than 100 characters long. To add multiple sectors enter each name on a new line.</span>
                                </div>
                            </div>
                        </div>

                        <!-- Sector Entry box for the edit screen -->
                        <div class="form-group row  editonly d-none">
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="edit_form_field_name"> Sector <span class="mandatory">*</span></label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <input type="text" id="edit_form_field_sector" placeholder="A unique name for the sector" class="form-control"  name='sector' value="" maxlength='100'   autocomplete="off" disabled />
                                <div class="form-elem-guide-text default-box" >
                                  <span class=" "   >Cannot be more than 100 chars long.</span>
                                </div>
                            </div>
                        </div>

                        <!-- Active/Inactive only for the edit screen -->
                        <div class="form-group row  editonly d-none ">
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="edit_form_field_status_y"> Active </label>
                            <div class="form-check form-check-inline pl-3">
                                <label class="form-check-label">
                                    <input id="edit_form_field_status_y" class="form-check-input" type="radio" name="active" value='y' disabled />
                                    Yes
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <label class="form-check-label">
                                    <input  id='edit_form_field_status_n'  name="active" class="form-check-input" type="radio" value='n' disabled />
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
                                        <!-- <i class="fa fa-check"></i>--><img src="images/check.png" class="check-button" alt="Check"> <span>Add Sectors</span>
                                    </button>
                                    <a href="sectors.php#" class="btn btn-danger d-none" type="button"   id="record-add-cancel-button" data-back-to=""    onclick="sectorfuncs.closeAddUserForm();">
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



