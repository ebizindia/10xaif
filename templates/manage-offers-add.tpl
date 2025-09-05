<div class="card">
    <div class="card-body">
        <div class="card-header-heading">
            <div class="row">
                <div class="col-6"><h4 id="panel-heading-text" class="pull-left row">Create Epic Offers&nbsp;<!--<i class="fa fa-info-circle" data-toggle="tooltip" data-placement="right" title="Add new discount offer."></i>--><img src="images/info.png" class="info-button" alt="Info"></h4></div>

                <div class="col-6 text-right">
                    <a href="manage-offers.php#" class="btn btn-danger record-list-show-button back-to-list-button row" id="back-to-list-button"><img src="images/left-arrow.png" class="custom-button" alt="Left"> Back To List </a>

                    <a href="manage-offers.php#" class="btn btn-danger record-list-show-button back-to-list-button row mobile-bck-to-list"  id="back-to-list-button"><img src="images/left-arrow.png" class="custom-button" alt="Left"> </a>

                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                <form class="form-horizontal" role="form" name='add_do_form' id="add_do_form" action='manage-offers.php' method='post' onsubmit="return ManageOffer.saveDODetails(this);" target="form_post_submit_target_window"  data-mode="add-rec" enctype="multipart/form-data" novalidate  >
                    <input type='hidden' name='mode' id='add_edit_mode' value='create' />
                    <input type='hidden' name='rec_id' id='add_edit_rec_id' value='' />
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
                        <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_categoryid"> Category <span class="mandatory">*</span></label>
                        <div class="col-xs-12 col-sm-6 col-lg-4">
                            <select class="form-control" id="add_form_field_categoryid" name='category_id' >
                                <option value="">-- Select a category --</option>
                                <?php  foreach($this->body_template_data['disc_offer_cats'] as $doffcat){ ?>
                                   <option value="<?php echo htmlentities($doffcat['id']) ?>"><?php echo \eBizIndia\_esc($doffcat['name'], true); ?></option>
                                <?php } ?>
                            </select>

                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_title"> Title <span class="mandatory">*</span></label>
                        <div class="col-xs-12 col-sm-6 col-lg-4">
                            <input type="text" id="add_form_field_title" placeholder="A title for the Epic Offer" class="form-control"  name='title' value="" maxlength='100'   autocomplete="off"  />
                            <div class="form-elem-guide-text default-box" >
                              <!-- <span class=" "   >Only alphabets, numbers, hyphen, underscore, percent, ampersand, apostrophe and spaces are allowed.</span> -->
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_description"> Description <span class="mandatory">*</span></label>
                        <div class="col-xs-12 col-sm-6 col-lg-4">
                            <textarea id="add_form_field_description" placeholder="Enter description of the epic offer" class="form-control"   name='description' value="" maxlength='500' autocomplete="off"></textarea>
                            <div class="form-elem-guide-text default-box" >
                              <span class=" "   >Enter description of the epic offer.</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_mou"> MOU Document </label>
                        <div class="col-xs-12 col-sm-6 col-lg-4 existing-file-view " id="mou_file_container">
                            <div class="profile_image existing-uploaded-file">
                                <a href="#" alt='Mou document' id="mou_file" class="ad_banner_img" style="max-width: 100%; margin-right: 20px;" target="_blank" rel="noopener" >Mou Document</a>
                                <div class="remove_image ">
                                    <a href="javascript:void(0);" class="remove-existing-file" data-state="1" title="Remove MOU document on save."  ><img src="images/clear1.png" ></a>
                                    <a href="javascript:void(0);" class="keep-existing-file" data-state="2" title="Do not remove MOU doucment on save."><img src="images/undo.png" ></a>
                                </div> 
                                <div class="remove-existing-file-msg" style="font-size: 11px; color: #ff3333;" >The uploaded MOU document has been marked for deletion and will be deleted after you click the "Save" button below.</div>
                                <input type='hidden' name='delete_mou_file' value='0' id="delete_mou_file" class="remove-file-input"/>
                            </div>
                            <input type="file" id="add_form_field_mou" placeholder="Offer's MOU" class="form-control"  name='mou' value="" accept="pdf" style="margin-top: 5px;" />
                            <a href="javascript:void(0);" id="remove_mou_selection" class="clear-file-input">Clear Selection</a>
                            <div class="form-elem-guide-text default-box" >
                              <span class=" "   >Allowed file types: pdf</span>
                              <br> <span>Max file size: 2MB or less</span>
                            </div>
                        </div>
                        
                    </div>
                    <div class="form-group row">
                        <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_offer_url"> URL </label>
                        <div class="col-xs-12 col-sm-6 col-lg-4">
                            <input type="text" id="add_form_field_offer_url" placeholder="URL of the offer website/webpage" class="form-control"  name='offer_url' value="" maxlength='200'   autocomplete="off"  />
                            <div class="form-elem-guide-text default-box" >
                              <span class=" "   >The url of the website/webpage giving this offer.</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_mob_img"> Provider's Logo </label>
                        <div class="col-xs-12 col-sm-6 col-lg-4 existing-file-view " id="mobile_image_container"><!--has-existing-file remove-file-->
                            <div class="profile_image existing-uploaded-file">
                                <img src="" alt='Image' id="mob_d_o_img" class="ad_banner_img" style="max-width: 100%;">
                                <div class="remove_image ">
                                    <a href="javascript:void(0);" class="remove-existing-file" data-state="1" title="Remove mobile pic on save."  ><img src="images/clear1.png" ></a>
                                    <a href="javascript:void(0);" class="keep-existing-file" data-state="2" title="Do not remove mobile pic on save."><img src="images/undo.png" ></a>
                                </div> 
                                <div class="remove-existing-file-msg" style="font-size: 11px; color: #ff3333;" >The offer provider's logo has been marked for deletion and will be deleted after you click the "Save" button below.</div>
                                <input type='hidden' name='delete_mob_img' value='0' id="delete_mob_img" class="remove-file-input"/>
                            </div>
                            <input type="file" id="add_form_field_mob_img" placeholder="Offer provider's logo" class="form-control"  name='mob_img' value="" accept="<?php echo '.'.implode(', .',$this->body_template_data['field_meta']['discount_offer']['file_types']); ?>" style="margin-top: 5px;" />
                            <a href="javascript:void(0);" id="remove_profile_pic_selection" class="clear-file-input">Clear Selection</a>
                            <div class="form-elem-guide-text default-box" >
                              <span class=" "   >Allowed file types: <?php \eBizIndia\_esc(implode(', ',$this->body_template_data['field_meta']['discount_offer']['file_types'])); ?></span>
                              <br> <span>Recommended size: 400px X 400px or less</span>
                            </div>
                        </div>
                        
                    </div>
                    
                    <div class="form-group row d-none">
                        <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_valid_upto_picker"> Valid Up To <span class="mandatory">*</span></label>
                        <div class="col-xs-12 col-sm-6 col-lg-4">
                            <input type="hidden" name="valid_upto" id="add_form_field_valid_upto" value="" >
                            <input type="text" id="add_form_field_valid_upto_picker" placeholder="Valid upto " class="form-control"  value="" maxlength='10' autocomplete="off" readonly />
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
                                    <!-- <i class="fa fa-check"></i>--><img src="images/check.png" class="check-button" alt="Check"> <span>Add Epic Offer</span>
                                </button>
                                <a href="manage-offers.php#" class="btn btn-danger d-none" type="button"   id="record-add-cancel-button" data-back-to=""    onclick="ManageOffer.closeAddForm();">
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



