
	        <div class="card">
                <div class="card-body">
                    <div class="card-header-heading">
                    <div class="row">
                        <div class="col-6"><h4 id="panel-heading-text" class="pull-left row">Create Ad Banner&nbsp;<!--<i class="fa fa-info-circle" data-toggle="tooltip" data-placement="right" title="Add new user."></i>--><img src="images/info.png" class="info-button" alt="Info"></h4></div>

                        <div class="col-6 text-right">
        <a href="ad-banners.php#" class="btn btn-danger record-list-show-button back-to-list-button row" id="back-to-list-button">
            <!--<i class="fa fa-arrow-left"></i>--><img src="images/left-arrow.png" class="custom-button" alt="Left"> Back To List </a>

   <a href="ad-banners.php#" class="btn btn-danger record-list-show-button back-to-list-button row mobile-bck-to-list"  id="back-to-list-button"><!--<i class="fa fa-arrow-left"></i>--><img src="images/left-arrow.png" class="custom-button" alt="Left"> </a>

                        </div>
                    </div>
                </div>
                    <div class="row">
                        <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                        <form class="form-horizontal" role="form" name='addrecform' id="addrecform" action='ad-banners.php' method='post' onsubmit="return adfuncs.saveRecDetails(this);" target="form_post_submit_target_window"  data-mode="add-rec" enctype="multipart/form-data" novalidate  >
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
                                <input type="text" id="add_form_field_name" placeholder="A name for the Ad Banner" class="form-control"  name='name' value="" maxlength='100'   autocomplete="off"  />
                                <div class="form-elem-guide-text default-box" >
                                  <span class=" "   >Only alphabets, numbers, hyphen, underscore and spaces are allowed.</span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_targetlink"> Target URL <span class="mandatory">*</span></label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <input type="text" id="add_form_field_targetlink" placeholder="https://www.sponsor.com/page/path" class="form-control"   name='target_link' value="" maxlength='255' autocomplete="off" />
                                <div class="form-elem-guide-text default-box" >
                                  <span class=" "   >Enter the Ad's target URL.</span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_dskimg"> Image (Desktop Screen) <span class="mandatory">*</span></label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <div class="ad_banner_image">
                                    <img src="" alt='Desktop banner image' id="dsk_banner_img" class="ad_banner_img" style="max-width: 100%;"  >
                                </div>
                                <input type="file" id="add_form_field_dskimg" placeholder="Desktop screen's image" class="form-control"  name='dsk_img' value="" accept="<?php echo '.'.implode(', .',$this->body_template_data['field_meta']['ad_banner']['file_types']); ?>" style="margin-top: 5px;" />
                                <div class="form-elem-guide-text default-box" >
                                  <span class=" "   >Allowed file types: <?php \eBizIndia\_esc(implode(', ',$this->body_template_data['field_meta']['ad_banner']['file_types'])); ?></span>
                                  <br><span>Recommended size: 1200px X 100px or less</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_mobimg"> Image (Mobile Screen) <span class="mandatory">*</span></label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <div class="ad_banner_image">
                                    <img src="" alt='Mobile banner image' id="mob_banner_img" class="ad_banner_img"  style="max-width: 100%;"   >
                                </div>
                                <input type="file" id="add_form_field_mobimg" placeholder="Mobile screen's image" class="form-control"  name='mob_img' value="" accept="<?php echo '.'.implode(', .',$this->body_template_data['field_meta']['ad_banner']['file_types']); ?>" style="margin-top: 5px;" />
                                <div class="form-elem-guide-text default-box" >
                                  <span class=" "   >Allowed file types: <?php \eBizIndia\_esc(implode(', ',$this->body_template_data['field_meta']['ad_banner']['file_types'])); ?></span>
                                  <br><span>Recommended size: 600px X 50px or less</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_startdt_picker"> Start Date <span class="mandatory">*</span></label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <input type="hidden" name="start_dt"   id="add_form_field_startdt" value="" >
                                <input type="text" id="add_form_field_startdt_picker" placeholder="Start displaying from " class="form-control"  value="" maxlength='10' autocomplete="off" readonly />
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_enddt_picker"> End Date <span class="mandatory">*</span></label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <input type="hidden" name="end_dt"   id="add_form_field_enddt" value="" >
                                <input type="text" id="add_form_field_enddt_picker" placeholder="Stop displaying after" class="form-control"  value="" maxlength='10' autocomplete="off" readonly />
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
                                        <!-- <i class="fa fa-check"></i>--><img src="images/check.png" class="check-button" alt="Check"> <span>Create Ad Banner</span>
                                    </button>
                                    <a href="ad-banners.php#" class="btn btn-danger d-none" type="button"   id="record-add-cancel-button" data-back-to=""    onclick="adfuncs.closeAddUserForm();">
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



