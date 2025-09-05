
	        <div class="card">
                <div class="card-body">
                    <div class="card-header-heading">
                    <div class="row">
                        <div class="col-6"><h4 id="panel-heading-text" class="pull-left row">Add Member&nbsp;<!--<i class="fa fa-info-circle" data-toggle="tooltip" data-placement="right" title="Add new user."></i>--><img src="images/info.png" class="info-button" alt="Info"></h4></div>

                        <div class="col-6 text-right">
        <a href="users.php#" class="btn btn-danger record-list-show-button back-to-list-button row" id="back-to-list-button">
            <!--<i class="fa fa-arrow-left"></i>--><img src="images/left-arrow.png" class="custom-button" alt="Left"> Back To List </a>

   <a href="users.php#" class="btn btn-danger record-list-show-button back-to-list-button row mobile-bck-to-list"  id="back-to-list-button"><!--<i class="fa fa-arrow-left"></i>--><img src="images/left-arrow.png" class="custom-button" alt="Left"> </a>

                        </div>
                    </div>
                </div>
                    <!-- <div class="alert alert-info alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><strong>Info!</strong> <span id="infoMsg">Enter the new user's information below. On successful submission, a system generated default password is automatically set for this new user. A user's "Role" determines what they can use in the portal.</span>
                    </div> -->

                    <div class="row">
                        <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                        <form class="form-horizontal" role="form" name='adduserform' id="adduserform" action='users.php' method='post' onsubmit="return usersfuncs.saveUserDetails(this);" target="form_post_submit_target_window"  data-mode="add-user" enctype="multipart/form-data" novalidate  >
                        <input type='hidden' name='mode' id='add_edit_mode' value='createUser' />
                        <input type='hidden' name='recordid' id='add_edit_recordid' value='' />
                        <!-- <input type='hidden' name='user_type' id='add_edit_usertype' value='' />
                        <input type = 'hidden' name='MAX_FILE_SIZE'  value="<?php echo $this->body_template_data['pp_max_filesize_in_bytes']; ?>" /> -->
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
                        <h6>Basic Information</h6>
                        <hr class="my-2 mt-2 mb-4">
                        <div class="form-group row d-none">
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_dnd"> DND </label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <input type="checkbox" id="add_form_field_dnd" placeholder="DND" class="form-control dnd_chkbox"  name='dnd' value="y" />
                                <div class="form-elem-guide-text default-box" >
                                  <span class=" "   >Tick to hide the contact information.</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_profilepic"> Profile Pic. </label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <div class="profile_image">
                                    <img src="" alt='profile_pic' id="profile_pic_img" class="profile_pic_img"  >
                                    <div class="remove_image d-none" ><a href="#" id="remove_profile_pic" title="Mark the profile pic for deletion."  ><img src="images/clear1.png" ></a><a href="#" id="undo_remove_profile_pic"  class="d-none" title="Remove the delete marker from the profile pic to keep it after saving the modifications."  ><img src="images/undo.png" ></a></div>
                                    <input type='hidden' name='delete_profile_pic' value='0' id="delete_profile_pic" />
                                </div>
                                <div  id="img_del_marked_msg" class="d-none" style="font-size: 11px; color: #ff3333;" >The profile pic has been marked for deletion and will be deleted after you click the "Save" button below.</div>
                                <input type="file" id="add_form_field_profilepic" placeholder="Profile Pic" class="form-control"  name='profile_pic' value="" accept="<?php echo '.'.implode(', .',$this->body_template_data['profile_pic_file_types']); ?>" style="margin-top: 5px;" />
                                <a href="#" id="remove_profile_pic_selection" >Clear Selection</a>
                                <div class="form-elem-guide-text default-box" >
                                  <span class=" "   >Allowed file types: <?php echo implode(', ',$this->body_template_data['profile_pic_file_types']); ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_title"> Salutation <span class="mandatory">*</span></label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <select class="form-control" id="add_form_field_title"  name='title'>
                                        <option value="">-- Select salutation --</option>
                                    <?php  foreach($this->body_template_data['salutation'] as $title){ ?>
                                       <option value="<?php echo htmlentities($title) ?>"><?php echo htmlentities($title); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_fname"> First Name <span class="mandatory">*</span></label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <input type="text" id="add_form_field_fname" placeholder="First name" class="form-control"  name='fname' value="" maxlength='30'   autocomplete="off"  />
                                <div class="form-elem-guide-text default-box" >
                                  <span class=" "   >Only alphabets, hyphen, and spaces are allowed.</span>
                                </div>
                            </div>
                        </div>
                         <div class="form-group row">
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_mname"> Middle Name </label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <input type="text" id="add_form_field_mname" placeholder="Middle name" class="form-control"  name='mname' value="" maxlength='30'  autocomplete="off"   />
                                <div class="form-elem-guide-text default-box" >
                                    <span class=" "   >Only alphabets, hyphen, and spaces are allowed.</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_lname"> Surname <span class="mandatory">*</span></label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <input type="text" id="add_form_field_lname" placeholder="Surname" class="form-control"  name='lname' value="" maxlength='60'   autocomplete="off"  />
                                <div class="form-elem-guide-text default-box" >
                                    <span class=" "   >Only alphabets, hyphen, and spaces are allowed.</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_email"> Primary Email <span class="mandatory">*</span></label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <input type="text" id="add_form_field_email" placeholder="Member's email id" class="form-control"   name='email' value="" maxlength='255' autocomplete="off" style="padding-right: 25px;" /> 
                                <img src="images/email.png" alt="email" class="email-icon-form-input" data-url="" >
                                <div class="form-elem-guide-text default-box" >
                                  <span class=" "   >Member's email id. Will be used as username  for login.</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_secondary_email"> Secondary Email </label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <input type="text" id="add_form_field_secondary_email" placeholder="Member's secondary email id" class="form-control"   name='secondary_email' value="" maxlength='255' autocomplete="off" style="padding-right: 25px;" /> 
                                <img src="images/email.png" alt="Secondary Email" class="email-icon-form-input" data-url="" >
                                
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_mobile"> Mobile Number <span class="mandatory">*</span></label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <input type="text" id="add_form_field_mobile" placeholder="Mobile number" class="form-control"   name='mobile' value="" maxlength='15' autocomplete="off" style="padding-right: 52px;"  />
                                <img src="images/whatsapp.png" alt="whatsapp" class="wa-icon-form-input" data-url=""  data-target="_blank" >
                                <img src="images/phone.png" alt="whatsapp" class="tel-icon-form-input" data-url=""  data-target="_blank" >
                                <div class="form-elem-guide-text default-box" >
                                  <span class=" "   >Only digits with an optional "+" at the beginning are allowed.</span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_mobile2"> Alternate Mobile </label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <input type="text" id="add_form_field_mobile2" placeholder="Alternate mobile number" class="form-control"   name='mobile2' value="" maxlength='15' autocomplete="off" style="padding-right: 52px;"  />
                                <div class="form-elem-guide-text default-box" >
                                  <span class=" "   >Only digits with an optional "+" at the beginning are allowed.</span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row d-none">
                            <label class="control-label col-xs-12 col-sm-4 col-md-3 col-lg-2" for="add_form_field_eduqual"> Educational Qualification </label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <input type="text" id="add_form_field_eduqual" placeholder="Educational qualification" class="form-control" name="edu_qual" value="" maxlength="100" autocomplete="off" >
                            </div>
                        </div>

                        <div class="form-group row d-none">
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_fbaccnt"> Facebook Profile </label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <input type="text" id="add_form_field_fbaccnt" placeholder="https://www.facebook.com/id" class="form-control"   name='fb_accnt' value="" maxlength='150' autocomplete="off" style="padding-right: 52px;"  />
                                <div class="form-elem-guide-text default-box" >
                                  <span class=" "   >Enter the Facebook profile's full URL, if any.</span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row d-none">
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_xaccnt"> Twitter (X) Profile </label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <input type="text" id="add_form_field_xaccnt" placeholder="https://www.twitter.com/id" class="form-control"   name='x_accnt' value="" maxlength='150' autocomplete="off" style="padding-right: 52px;"  />
                                <div class="form-elem-guide-text default-box" >
                                  <span class=" "   >Enter the Twiiier profile's full URL, if any.</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group row d-none">
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_linkedinaccnt"> LinkedIn Profile </label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <input type="text" id="add_form_field_linkedinaccnt" placeholder="https://www.linkedin.com/in/id" " class="form-control"   name='linkedin_accnt' value="" maxlength='150' autocomplete="off" style="padding-right: 52px;"  />
                                <div class="form-elem-guide-text default-box" >
                                  <span class=" "   >Enter the linkedin profile's full URL, if any.</span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row d-none">
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_website"> Website </label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <input type="text" id="add_form_field_website" placeholder="https://yourwebsite.com" class="form-control"   name='website' value="" maxlength='150' autocomplete="off" style="padding-right: 52px;"  />
                                <div class="form-elem-guide-text default-box" >
                                  <span class=" "   >Enter the website's full URL, if any.</span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_gender_M"> Gender <span class="mandatory">*</span></label>

                            <?php  foreach ( \eBizIndia\enums\Gender::cases() as $case){ ?>
                                <div class="form-check form-check-inline pl-3">
                                    <label class="form-check-label">
                                        <input id="add_form_field_gender_<?php echo $case->value; ?>" class="form-check-input" type="radio" name="gender" value='<?php echo $case->value; ?>' />
                                        <?php echo \eBizIndia\_esc($case->label()); ?>
                                    </label>
                                </div>
                            <?php } ?>

                        </div>

                        <div class="form-group row d-none">
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_bloodgrp"> Blood Group </label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <select class="form-control" id="add_form_field_bloodgrp"  name='blood_grp'>
                                        <option value="">-- Select blood group --</option>
                                    <?php  foreach($this->body_template_data['blood_grps'] as $bg){ ?>
                                       <option value="<?php echo htmlentities($bg) ?>"><?php echo htmlentities($bg); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <!--<div class="form-group row">
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_batchno"> Batch No. <span class="mandatory">*</span></label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <input type="text" id="add_form_field_batchno" placeholder="Batch No." class="form-control"  name='batch_no' value="" maxlength='4' inputmode="numeric"  autocomplete="off"  />
                                <div class="form-elem-guide-text default-box" >
                                  <span class=" "   >Your class X year - between <?php echo $this->body_template_data['field_meta']['batch_no']['min']; ?> and <?php echo $this->body_template_data['field_meta']['batch_no']['max']; ?>.</span>
                                </div>
                            </div>
                        </div>-->

                        <div class="form-group row">
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_dob_picker"> Date Of Birth <span class="mandatory">*</span></label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <input type="hidden" name="dob"   id="add_form_field_dob" value="" >
                                <input type="text" id="add_form_field_dob_picker" placeholder="Date of birth" class="form-control"  value="" maxlength='10' autocomplete="off" readonly style="background-color: transparent !important; cursor: default !important;"  />
                            </div>
                        </div>


                        <div class="form-group row">
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_annv_picker"> Anniversary Date </label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <input type="hidden" name="annv"   id="add_form_field_annv" value="" >
                                <input type="text" id="add_form_field_annv_picker" placeholder="Wedding anniversary date" class="form-control"  value="" maxlength='10' autocomplete="off" readonly style="background-color: transparent !important; cursor: default !important;"  />
                            </div>
                        </div>
                        
                          <h4>Family Information</h4>
                        <hr class="my-2 mt-2 mb-4">
                        
                        <div class="form-group row">
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_spouse_fname"> Spouse </label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <input type="text" id="add_form_field_spouse_fname" placeholder="Spouse" class="form-control" name='spouse_name' value="" maxlength='30' autocomplete="off" />
                                <div class="form-elem-guide-text default-box">
                                    <span class="">Only alphabets, hyphen, and spaces are allowed.</span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="spgendM"> Gender</label>

                            <?php  foreach ( \eBizIndia\enums\Gender::cases() as $case){ ?>
                                <div class="form-check form-check-inline pl-3">
                                    <label class="form-check-label">
                                        <input id="spgend<?php echo $case->value; ?>" class="form-check-input" type="radio" name="spouse_gender" value='<?php echo $case->value; ?>' />
                                        <?php echo \eBizIndia\_esc($case->label()); ?>
                                    </label>
                                </div>
                            <?php } ?>

                        </div>

                        <div class="form-group row">
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="spousdob"> DOB </label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <input type="hidden" name="spouse_dob" id="add_form_field_spouse_dob" value="" />
                                <input type="text" id="spousdob" name="spouse_dobb" placeholder="Date of birth" class="form-control"  value="" maxlength='10' autocomplete="off" readonly style="background-color: transparent !important; cursor: default !important;"  />
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_spouse_whatsapp"> Mobile Number </label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <input type="text" id="add_form_field_spouse_whatsapp" placeholder="Mobile number" class="form-control" name='spouse_whatapp' value="" maxlength='15' autocomplete="off" />
                                <!-- <img src="images/whatsapp.png" alt="whatsapp" class="wa-icon-form-input"> -->
                                <div class="form-elem-guide-text default-box">
                                    <span class="">Only digits with an optional "+" at the beginning are allowed.</span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_spouse_email"> Email </label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <input type="text" id="add_form_field_spouse_email" placeholder="Email" class="form-control" name='spouse_email' value="" maxlength='255' autocomplete="off" />
                                <div class="form-elem-guide-text default-box">
                                    <span class="">Must be a valid email address.</span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row d-none">
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_spouse_profession">  Profession </label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <textarea id="add_form_field_spouse_profession" placeholder="Profession" class="form-control" name="spouse_profession" maxlength="255"></textarea>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_children"> Children </label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <textarea id="add_form_field_children" placeholder="Children details" class="form-control" name="spouse_children" maxlength="255"></textarea>
                            </div>
                        </div>



                        <!-- <div class="form-group row">
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_maritalstatus"> Marital status </label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <select class="form-control" id="add_form_field_maritalstatus" name='marital_status' >
                                        <option value="">-- Select marital status --</option>
                                        <?php  foreach ( \eBizIndia\enums\MaritalStatus::cases() as $case){ ?>
                                           <option value="<?php echo $case->value; ?>"><?php echo \eBizIndia\_esc($case->label()); ?></option>
                                        <?php } ?>
                                </select>
                            </div>
                        </div> -->

                        <h4>Residence Address</h4>
                        <hr class="my-2 mt-2 mb-4">
                        
                        <div class="form-group row">
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_resaddrline1"> Address Line 1 </label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <input type="text" id="add_form_field_resaddrline1" placeholder="Residence address line 1" class="form-control"  name='residence_addrline1' value="" maxlength='255'  />
                            </div>
                        </div> 
                        
                        <div class="form-group row">
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_resaddrline2"> Address Line 2</label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <input type="text" id="add_form_field_resaddrline2" placeholder="Residence address line 2" class="form-control"  name='residence_addrline2' value="" maxlength='255'  />
                            </div>
                        </div> 
                        
                        <div class="form-group row">
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_resaddrline3"> Address Line 3</label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <input type="text" id="add_form_field_resaddrline3" placeholder="Residence address line 3" class="form-control"  name='residence_addrline3' value="" maxlength='255'  />
                            </div>
                        </div> 
						<div class="form-group row">
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_rescity"> City</label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <input type="text" id="add_form_field_rescity" placeholder="Residence city" class="form-control"  name='residence_city' value="" maxlength='60'  />
                                <div class="form-elem-guide-text default-box" >
                                  <span class=" "   >Type a few characters and then select from the list.</span>
                                </div>
                            </div>
                        </div> 
                        <div class="form-group row">
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_respin"> PIN </label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <input type="text" id="add_form_field_respin" placeholder="Residence PIN" class="form-control"  name='residence_pin' value="" maxlength='20'  />
                            </div>
                        </div> 
                        <div class="form-group row">
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_resstate"> State </label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <input type="text" id="add_form_field_resstate" placeholder="Residence state" class="form-control"  name='residence_state' value="" maxlength='70'  />
                                <div class="form-elem-guide-text default-box" >
                                  <span class=" "   >Type a few characters and then select from the list.</span>
                                </div>
                            </div>
                        </div> 

                        <div class="form-group row">
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_rescountry"> Country</label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <input type="text" id="add_form_field_rescountry" placeholder="Residence country" class="form-control"  name='residence_country' value="India" maxlength='60'  />
                                <div class="form-elem-guide-text default-box" >
                                  <span class=" "   >Type a few characters and then select from the list.</span>
                                </div>
                            </div>
                        </div> 
                        <div class="form-group row">
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_resphone"> Phone</label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <input type="text" id="add_form_field_resphone" placeholder="Residence phone" class="form-control"  name='residence_phone' value="" maxlength='255'  />
                            </div>
                        </div> 
                        <div class="form-group row">
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_resfax"> Fax</label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <input type="text" id="add_form_field_resfax" placeholder="Residence fax" class="form-control"  name='residence_fax' value="" maxlength='255'  />
                            </div>
                        </div>
                        
                        <h4>Work Details</h4>
                        <hr class="my-2 mt-2 mb-4">

                        <div class="form-group row d-none">
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_sector"> Sector</label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <select class="form-control" id="add_form_field_sector" name='sector' >
                                        <option value="">-- Select a sector --</option>
                                        <?php  foreach ( $this->body_template_data['sectors'] as $sec){ ?>
                                           <option value="<?php echo $sec['id']; ?>"  data-active="<?php echo $sec['active']; ?>"  ><?php echo \eBizIndia\_esc($sec['sector']); ?></option>
                                        <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_worktype"> Prime Business</label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <input type="text" id="add_form_field_worktype" placeholder="Your main line of business" class="form-control"  name='work_type' value="" maxlength='100'  />
                                
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_worktype"> Secondary Business </label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <input type="text" id="add_form_field_workind" placeholder="Your secondary line of business" class="form-control"  name='work_ind' value="" maxlength='100'  />
                                <!-- <div class="form-elem-guide-text default-box" >
                                  <span class=" "   >Type a few characters and select from the list shown. Or enter a new value.</span>
                                </div> -->
                            </div>
                        </div> 

                        <!-- <div class="form-group row">
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_workind"> Industry </label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <input type="text" id="add_form_field_workind" placeholder="Work industry" class="form-control"  name='work_ind' value="" maxlength='255'  />
                                <div class="form-elem-guide-text default-box" >
                                  <span class=" "   >Type a few characters and select from the list shown. Or enter a new industry.</span>
                                </div>
                            </div>
                        </div> --> 


                        <div class="form-group row">
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_workcompany"> Company Name </label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <input type="text" id="add_form_field_workcompany" placeholder="Company name" class="form-control"  name='work_company' value="" maxlength='255'  />
                            </div>
                        </div> 

                        <div class="form-group row d-none">
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_designation"> Designation </label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <input type="text" id="add_form_field_designation" placeholder="Designation in company" class="form-control"  name='designation' value="" maxlength='100'  />
                            </div>
                        </div> 
                        
                        <div class="form-group row">
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_workaddrline1"> Address Line 1</label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <input type="text" id="add_form_field_workaddrline1" placeholder="Work address line 1" class="form-control"  name='work_addrline1' value="" maxlength='255'  />
                            </div>
                        </div> 
                        
                        <div class="form-group row">
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_workaddrline2"> Address Line 2</label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <input type="text" id="add_form_field_workaddrline2" placeholder="Work address line 2" class="form-control"  name='work_addrline2' value="" maxlength='255'  />
                            </div>
                        </div> 
                        
                        <div class="form-group row">
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_workaddrline3"> Address Line 3</label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <input type="text" id="add_form_field_workaddrline3" placeholder="Work address line 3" class="form-control"  name='work_addrline3' value="" maxlength='255'  />
                            </div>
                        </div> 
						<div class="form-group row">
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_workcity"> City </label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <input type="text" id="add_form_field_workcity" placeholder="Work city" class="form-control"  name='work_city' value="" maxlength='60'  />
                                <div class="form-elem-guide-text default-box" >
                                  <span class=" "   >Type a few characters and then select from the list.</span>
                                </div>
                            </div>
                        </div> 
                        <div class="form-group row">
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_workpin"> PIN</label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <input type="text" id="add_form_field_workpin" placeholder="Work PIN" class="form-control"  name='work_pin' value="" maxlength='20'  />
                            </div>
                        </div> 
                        <div class="form-group row">
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_workstate"> State </label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <input type="text" id="add_form_field_workstate" placeholder="Work state" class="form-control"  name='work_state' value="" maxlength='70'  />
                                <div class="form-elem-guide-text default-box" >
                                  <span class=" "   >Type a few characters and then select from the list.</span>
                                </div>
                            </div>
                        </div> 

                        <div class="form-group row">
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_workcountry"> Country</label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <input type="text" id="add_form_field_workcountry" placeholder="Work country" class="form-control"  name='work_country' value="India" maxlength='60'  />
                                <div class="form-elem-guide-text default-box" >
                                  <span class=" "   >Type a few characters and then select from the list.</span>
                                </div>
                            </div>
                        </div> 
                        <div class="form-group row">
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_workphone"> Phone(D)</label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <input type="text" id="add_form_field_workphone" placeholder="Phone" class="form-control"  name='work_phone' value="" maxlength='255'  />
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_workphoneepabx"> Phone(epabx)</label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <input type="text" id="add_form_field_workphoneepabx" placeholder="Phone" class="form-control"  name='work_phoneepabx' value="" maxlength='255'  />
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_workfax"> Fax</label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <input type="text" id="add_form_field_workfax" placeholder="Fax" class="form-control"  name='work_fax' value="" maxlength='255'  />
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_workaSecretaryName"> Secretary Name</label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <input type="text" id="add_form_field_workSecretaryName" placeholder="Secretary name" class="form-control"  name='work_secretary_name' value="" maxlength='255'  />
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_workSecretaryMobile"> Secretary Mobile</label>
                             <div class="col-xs-12 col-sm-6 col-lg-4">
                            <input type="text" id="add_form_field_workSecretaryMobile" placeholder="Secretary mobile" class="form-control" name='work_secretary_mobile' value="" maxlength='255' autocomplete="off" />
                            </div>
                       </div>

                        <div class="form-group row">
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_workaSecretaryEmail"> Secretary Email</label>
                             <div class="col-xs-12 col-sm-6 col-lg-4">
                            <input type="text" id="add_form_field_workSecretaryEmail" placeholder="Secretary email" class="form-control" name='work_secretary_email' value="" maxlength='255' autocomplete="off" />
                            </div>
                        </div> 


                        <!-- <div class="form-group row">
                            <label class="control-label col-xs-12 col-sm-4 col-md-3 col-lg-2" for="add_form_field_hashtags"> Hashtags</label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <input type="text" id="add_form_field_hashtags" placeholder="Eg. #nature, #art" class="form-control" name="hashtags" value="" maxlength="100">
                            </div>
                        </div> -->

<!-- 
                        <h4 class="pmtdtls"  >Payment Details</h4>
                        <hr class="my-2 mt-2 mb-4 pmtdls">
                        <div class="form-group row pmtdtls ">
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_membershipfee"> Membership Fee</label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <input type="text" id="add_form_field_membershipfee" placeholder="Membership fee" class="form-control"  name='membership_fee' value="" maxlength='5' inputmode="numeric" data-minamt="<?php echo CONST_MEM_REG_FEE; ?>"  />
                                <\!--<div class="form-elem-guide-text default-box" >
                                  <span class=" "   >Inclusive of GST</span>
                                </div>--\>
                            </div>
                        </div>

                        <div class="form-group row pmtdtls">
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_paymentstatus"> Payment Status </label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <select class="form-control" id="add_form_field_paymentstatus" name='payment_status' >
                                    <option value="Pending">Pending</option>
                                    <option value="Paid">Paid</option>
                                    <\!-- <option value="Failed">Failed</option> --\>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row pmtdtls">
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_paidon_picker"> Paid On </label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <input type="hidden" name="paid_on"   id="add_form_field_paidon" value="" >
                                <input type="text" id="add_form_field_paidon_picker" placeholder="Paid on date" class="form-control"  value="" maxlength='10' autocomplete="off" readonly style="background-color: transparent !important; cursor: default !important;"  />
                            </div>
                        </div>

                        <\!-- <div class="form-group row pmtdtls">
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_pmtfailmsg"> Failure Reason </label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <span class="form-control non-editable" id="add_form_field_pmtfailmsg"  ></span>
                            </div>
                        </div> --\>

                        <div class="form-group row pmtdtls">
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_paymentmode"> Payment Mode <span class="mandatory d-none">*</span></label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <select class="form-control" id="add_form_field_paymentmode" name='payment_mode' >
                                    <option value="">-- Select  --</option>
                                    <option value="Online">Online</option>
                                    <option value="Cash">Cash</option>
                                    <option value="Cheque">Cheque</option>
                                    <option value="Draft">Draft</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row pmtdtls">
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_instrumenttype"> Instrument Type</label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <input type="text" id="add_form_field_instrumenttype" placeholder="CARD, UPI, QR, etc." class="form-control"  name='payment_instrument_type' value="" maxlength='25'   />
                            </div>
                        </div>

                        <div class="form-group row pmtdtls">
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_instrument"> Instrument</label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <input type="text" id="add_form_field_instrument" placeholder="Card type, etc." class="form-control"  name='payment_instrument' value="" maxlength='255'   />
                            </div>
                        </div>

                        <div class="form-group row pmtdtls">
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_bnkref"> Bank Reference</label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <input type="text" id="add_form_field_bnkref" placeholder="Bank reference ID" class="form-control"  name='payment_txn_ref' value="" maxlength='30'   />
                            </div>
                        </div>

                        -->
                        
                        <h4>Settings</h4>
                        <hr class="my-2 mt-2 mb-4">

                        <!-- <div class="form-group row d-none">
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_expdt_picker"> Expiry Date <span class="mandatory">*</span></label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <input type="hidden" name="exp_dt"   id="add_form_field_expdt" value="" >
                                <input type="text" id="add_form_field_expdt_picker" placeholder="Membership expiry date" class="form-control"  value="" maxlength='10' autocomplete="off" readonly   />
                            </div>
                        </div>

                        <div class="form-group row d-none" id="joining_dt_row"  >
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_joiningdt"> Joining Date </label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <span class="form-control non-editable" id="add_form_field_joiningdt"  ></span>
                            </div>
                        </div> -->

                        <div class="form-group row d-none">
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_joiningdt_picker"> Joining Date </label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <input type="hidden" name="joining_dt"   id="add_form_field_joiningdt" value="" >
                                <input type="text" id="add_form_field_joiningdt_picker" placeholder="Member's joining date" class="form-control"  value="" maxlength='10' autocomplete="off" readonly   />
                            </div>
                        </div>

                        <!-- div class="form-group row">
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_memno"> Membership No. </label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <input type="text" id="add_form_field_memno" placeholder="Membership No." class="form-control text-uppercase"  name='membership_no' value="" maxlength='30'  />
                                <div class="form-elem-guide-text default-box" >
                                  <span class=" "   >A unique member identifier.</span>
                                </div>
                            </div>
                        </div --> 

                        <div class="form-group row">
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_memtype"> Groups </label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <div style="position: relative;"  class="drop_custom">
                                    <select class="form-control" name='groups[]' id="add_form_field_memtype" multiple >
                                        <!-- <option value='' >-- Select membership type --</option> -->
                                        <?php 
                                            foreach ($this->body_template_data['groups'] as $grp) {
                                        ?>
                                                <option value="<?php echo $grp['id']; ?>"  ><?php \eBizIndia\_esc($grp['grp']); ?></option>
                                        <?php        
                                            }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-elem-guide-text default-box" >
                                  <span class=" "   >Type a few characters and then select from the list. Or enter a new group and press tab to select.</span>
                                </div>
                            </div>
                        </div> 
                        <div class="form-group row">
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_desiginassoc"> Designation </label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <input type="text" id="add_form_field_desiginassoc" placeholder="Designation in association" class="form-control"  name='desig_in_assoc' value="" maxlength='100'  />
                                <div class="form-elem-guide-text default-box" >
                                  <span class=" "   >Type a few characters and select from the list shown. Or enter a new designation.</span>
                                </div>
                            </div>
                        </div>   
                         <div class="form-group row">
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_role_REGULAR"> Role <span class="mandatory">*</span> </label>

                            <?php  foreach ( \eBizIndia\enums\Role::cases() as $case){ ?>
                                <div class="form-check form-check-inline pl-3">
                                    <label class="form-check-label">
                                        <input id="add_form_field_role_<?php echo $case->value; ?>" class="form-check-input" type="radio" name="role" value='<?php echo $case->value; ?>' />
                                        <?php echo \eBizIndia\_esc($case->label()); ?>
                                    </label>
                                </div>
                            <?php } ?>

                        </div>
                        <div class="form-group row">
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_password"> Password </label>
                            <div class="col-xs-12 col-sm-6 col-lg-4">
                                <input type="text" id="add_form_field_password" placeholder="Login password" class="form-control"   name='password' value="" maxlength='20' />
                                <div class="form-elem-guide-text default-box"  >
                                    <span class="" id="add_password_msg">Required for login purposes.<br>Leave the box empty to set a random password.</span>
                                    <span class=" d-none" id="edit_password_msg"  >Required for login purposes.<br>Leave the box empty to keep the old password.</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_status_y"> Status </label>
                            <div class="form-check form-check-inline pl-3">
                                <label class="form-check-label">
                                    <input id="add_form_field_status_y" class="form-check-input" type="radio" name="status" value='y' />
                                    Active
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <label class="form-check-label">
                                    <input  id='add_form_field_status_n'  name="status" class="form-check-input" type="radio" value='n' />
                                    Inactive
                                </label>
                            </div>
                        </div>

                        <?php
                            if($this->body_template_data['is_admin']){
                        ?>
                                <div class="form-group row" id="remarks_box"   >
                                    <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="add_form_field_remarks">Remarks</label>
                                    <div class="col-xs-12 col-sm-6 col-lg-4">
                                        <textarea id="add_form_field_remarks" placeholder="Enter any important, extra information, to be recorded with this member's profile." class="form-control"  name='remarks'   ></textarea>
                                    </div>
                                </div> 
                        <?php
                            }
                        ?>
                        

                        <!-- <div class="form-group row">
                            <label class="col-lg-2 col-md-4 col-sm-12 col-form-label"></label>
                            <div class="col-sm-7 col-lg-6">
                                <div class='alert alert-info default-box'  style="width: 370px;" >
                                    <p style="margin-bottom: 0">User will be created with a default password <b><?php echo $this->body_template_data['default_pswd'];?></b></p>
                                </div>
                            </div>
                        </div> -->


                        


                        <div class="clearfix"></div>
                        <div class="form-actions form-group">
                            <!-- <div class="col-md-4 col-sm-2 col-lg-2 hidden-xs"></div> -->
                            <div class="col-md-12 col-sm-12 col-xs-12 text-center">
                                <center>
                                    <button class="btn btn-success rounded" type="submit"  id="record-save-button" style="margin-right: 10px;">
                                        <!-- <i class="fa fa-check"></i>--><img src="images/check.png" class="check-button" alt="Check"> <span>Add Member</span>
                                    </button>
                                    <a href="users.php#" class="btn btn-danger d-none" type="button"   id="record-add-cancel-button" data-back-to=""    onclick="usersfuncs.closeAddUserForm();">
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



