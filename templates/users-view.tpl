<style>
.family_block span{
    display:inline-block;
    vertical-align: text-top;
        position: relative;
    top: -2px;
}
.family_block p{
padding: 0px !important;
}
.address_block span{
    display:block;
}
.company_details_block span{
    display:block;
}

.contact_image{
    width: 30px;
    height: auto;
}

.count-box-close p {
    padding: 0px !important;
}

#req_contact_btn{
    background-color: #074d82; border-color: #074d82;
}
#req_contact_btn:hover{
    background-color: #074d82c4; border-color: #074d82c4;
}
.social-center {
    display: flex;
    flex-direction: column; /* Ensures elements stack vertically */
    align-items: flex-start; /* Aligns all items to the left */
}

.social-center a {
    display: flex;
    align-items: center;
    gap: 8px; /* Ensures space between icon and text */
    text-decoration: none; /* Removes underline */
    width: 100%; /* Makes sure links stretch properly */
}
.d-none { display:none !important; }

</style>

<div class="card">
    <div class="card-body">
        <div class="card-header-heading">
            <div class="row">
                <div class="col-6">
                    <h4 id="panel-heading-text" class="pull-left row">View Profile&nbsp;</h4>
                </div>
                <div class="col-6 text-right back-and-search">
                    <div class="top_right_button">
                        <div class="text-right back-to-list">
                            <a href="users.php#" class="btn btn-danger back-to-list-button rounded row" id="back-to-list-button">
                                <img src="images/left-arrow.png" class="custom-button" alt="Left">
                                <span class="hide_in_mobile">Back To List</span>
                            </a>
                        </div>
                        <div class="row btns-user-add" style="float:left; height: 34px;" id="vu_pg_edit_btn_cont">
                            <a href="users.php#mode=edit&recid=" class="btn btn-success rounded">
                                <img src="images/edit-white.png" class="custom-button-small" alt="Plus">
                                <span class="hide_in_mobile">Edit Profile</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- New design -->
        <div class="member_details_block">
            <div class="background_gray">
                <div class="row">
                    <div class="float_block_content">
                        <div class="heading">
                            <span id="view_form_field_name">Mr. Arun Agrawal</span>
                            <span id=""></span>
                            <span id="view_form_field_dnd" class="d-none">
                                <img src="images/no-call-big.png" alt="Flag" width="20" title="Marked as DND" style="margin-top: -4px;">
                            </span>
                        </div>
                        <div class="float_block float_block_right">
                            <img class="img-fluid member_detail_img" id="view_form_field_profilepic" src="" alt="">
                        </div>

                        <div class="float_block float_block_middle">
                            <ul class="resp_margin_one float_block_right d-none">
                                <!-- li><strong>Membership #&nbsp;</strong><span id="view_form_field_memno">&nbsp;</span></li -->
                                <li>
                                    <div class="detail_wrap_content">
                                        <div class="left_cell"><strong>Education:&nbsp;</strong></div>
                                        <div id="view_form_field_eduqual" class="right_cell"></div>
                                    </div>
                                </li>
                                <li class="d-none"><strong>Blood Group:&nbsp;</strong><span id="view_form_field_bloodgrp">&nbsp;</span></li>
                            </ul>
                            <div class="resp_margin_two social-center">
                                <a href="" class="nopropagate d-flex align-items-center" id="view_form_field_email" style="white-space: nowrap;">
                                    <img src="images/mail-big.png" alt="email" width="30">&nbsp;
                                    <span id="view_form_field_email_text"></span>
                                </a>
                                <a href="" class="nopropagate d-flex align-items-center mt-2" id="view_form_field_secondary_email" style="white-space: nowrap;">
                                    <img src="images/mail-big.png" alt="secondary email" width="30">&nbsp;
                                    <span id="view_form_field_secondary_email_text"></span>
                                </a>
                                <!-- a href="" class="nopropagate contact_image d-flex align-items-center mt-2" target="_blank" rel="noopener noreferrer nofollow" id="view_form_field_mobile">
                                    <img src="images/whatsapp-big.png" alt="whatsapp" width="30">&nbsp;
                                    <span id="view_form_field_mobile_text"></span>
                                </a -->
                                <a href="" class="nopropagate d-flex align-items-center mt-2" rel="noopener noreferrer nofollow" id="view_form_field_mobile_tel">
                                    <img src="images/telephone.png" alt="phone" width="30">&nbsp;
                                    <span id="view_form_field_mobile_tel_text"></span>
                                </a>
                                <a href=""  class="nopropagate d-flex align-items-center mt-2" rel="noopener noreferrer nofollow" id="view_form_field_mobile2">
                                    <img src="images/telephone.png" alt="2nd phone">&nbsp;
                                    <span id="view_form_field_mobile2_text"></span>
                                </a>
                            </div>
                            <div class="resp_margin_two req-contact-btn-cont">
                                <a href="" class="btn text-white nopropagate" rel="noopener noreferrer nofollow" id="req_contact_btn" data-recid='' data-mnm=''>
                                    Request Contact
                                </a>
                            </div>
                        </div>

                        <div class="float_block">
                            <ul>
                                <li><strong>Date Of Birth:&nbsp;</strong><span id="view_form_field_dob">&nbsp;</span></li>
                                <li><strong>Anniversary Date:&nbsp;</strong><span id="view_form_field_annv">&nbsp;</span></li>
                            </ul>
                            <div class="member_details social_icon d-none">
                                <a href="#" id="view_form_field_fbaccnt" target="_blank" rel="noopener noreferrer nofollow">
                                    <img class="img-fluid" src="images/facebook.png" alt="Facebook profile">
                                </a>
                                <a href="#" id="view_form_field_xaccnt" target="_blank" rel="noopener noreferrer nofollow">
                                    <img class="img-fluid middle" src="images/twitter.png" alt="Twitter profile">
                                </a>
                                <a href="#" id="view_form_field_linkedinaccnt" target="_blank" rel="noopener noreferrer nofollow">
                                    <img class="img-fluid middle" src="images/linkedin.png" alt="LinkedIn profile">
                                </a>
                                <a href="#" id="view_form_field_website" target="_blank" rel="noopener noreferrer nofollow">
                                    <img class="img-fluid" src="images/website.png" alt="Website">
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Family Section -->
            <div class="row">
                <div class="col-lg-12 d-flex flex-column align-items-stretch">
                    <div class="row">
                        <div class="col-md-6 col-lg-4">
                            <div class="count-box count-box-address" style="margin_top:20px;">
                                <img class="img-fluid member_detail_img" src="images/family.png" alt="">
                                <span class="purecounter">Family</span>
                               <div class="family_block">
									<p style="margin-top:15px;"><strong>Spouse</strong></p>
									<p>Name: 
									<span id="view_form_field_spouse_name">John Doe</span></p>
									<p>Gender: 
									<span id="spgender">Male</span></p>									
									<p>DOB:
									<span id="spdob">01/01/1990</span></p>									
									<p>Mobile:
									<span id="spwapp">+1234567890</span></p>									
									<p>Email:
									<span id="spemail">example@example.com</span></p>									
									<p class="d-none">Profession:
									<span id="spproff">Engineer</span></p>									
									<p style="margin-top:15px;"><strong>Children</strong></p>
									<p><span id="view_form_field_children">
										Ayush 7 years<br>
										Sangita 3 years
									</span></p>
								</div>
                            </div>
                        </div>

                       

                        <!-- Work Details Section -->
                        <div class="col-md-6 col-lg-4">
                            <div class="count-box count-box-address" style="margin_top:15px;">
                                <img class="img-fluid member_detail_img" src="images/work.png" alt="">
                                <span class="purecounter">Work Details</span>
                                <!-- p  id="view_form_field_worktype">&nbsp;</p -->
                                <p class="d-none">
                                    <strong>Sector:</strong></br>
                                    <span id="view_form_field_sector">&nbsp;</span>
                                </p>
                                <p>
                                    <strong>Prime Business:</strong></br>
                                    <span id="view_form_field_prime_business">Prime Business</span>
                                </p>
                                <p>
                                    <strong>Secondary Business :</strong></br>
                                    <span id="view_form_field_secondary_business">Secondary Business</span>
                                </p>
                                <p class="company_details_block">
                                    <strong>Company:</strong>
								<span id="view_form_field_designation">&nbsp;</span>
                                    <span id="view_form_field_workcompany">&nbsp;</span>
                                    <span id="view_form_field_workaddrline1">&nbsp;</span>
                                    <span id="view_form_field_workaddrline2">&nbsp;</span>
                                    <span id="view_form_field_workaddrline3">&nbsp;</span>
                                    <span id="view_form_field_workcity">City, PIN</span>
                                    <span id="view_form_field_workstate">State, Country</span>
                                    <span id="view_form_field_workphone">&nbsp;</span>
                                    <span id="view_form_field_workphoneepabx">&nbsp;</span>
                                    <span id="view_form_field_workfax">&nbsp;</span>
                                </p>
                                <p >
                                    <strong>Secretary:</strong></br>
                                    <span id="view_form_field_workSecretaryName">&nbsp;</span><br>
                                    <span id="view_form_field_workSecretaryMobile">&nbsp;</span><br>
                                    <span id="view_form_field_workSecretaryEmail">&nbsp;</span>
                                    
                                </p>
                            </div>
                        </div>						
						<!-- Other Info Section -->
                         <!-- Residence Section -->
                        <div class="col-md-6 col-lg-4">
                            <div class="count-box count-box-address" style="margin_top:20px;">
                                <img class="img-fluid member_detail_img" src="images/location.png" alt="">
                                <span class="purecounter">Residence</span>
                                <p class="address_block">
                                    <span id="view_form_field_resaddrline1">Address Line</span>
                                    <span id="view_form_field_resaddrline2">Address Line</span>
                                    <span id="view_form_field_resaddrline3">Address Line</span>
                                    <span id="view_form_field_rescity">City, PIN</span>
                                    <span id="view_form_field_resstate">State, Country</span>
                                    <span id="view_form_field_resphone">&nbsp;</span>
                                    <span id="view_form_field_resfax">&nbsp;</span>
                                </p>
                            </div>
                        </div>
					</div>	
					<div class="row d-none">
						<div class="col-md-6 col-lg-4">
							<div class="count-box count-box-close" style="margin_top:20px;">
								<img class="img-fluid member_detail_img" src="images/settings.png" alt="">
								<span class="purecounter">Other Info</span>
                                <!-- <p  >Expiry Date:&nbsp;<span id="view_form_field_expirydt"></span></p> -->
								<p style="margin-top: 15px;" >Gender:&nbsp;<span id="view_form_field_gender"></span></p>
								<p>Designation:&nbsp;<span id="view_form_field_desiginassoc"></span></p>
								<p>Groups:&nbsp;<span id="view_form_field_memtype"></span></p>
								<p>Role:&nbsp;<span id="view_form_field_role"></span></p>
								<p>Joining Date:&nbsp;<span id="view_form_field_joiningdt"></span></p>
								<p>Remarks:&nbsp;<span id="view_form_field_remarks"></span></p>
							</div>
						</div>

						<!--div class="col-md-6 col-lg-4">
							<div class="count-box count-box-close" style="margin_top:20px;" id="payment_info_view">
								<img class="img-fluid member_detail_img" src="images/payment.png" alt="">
								<span class="purecounter">Membership Fee</span>
								<p style="margin-top: 15px;">Status:&nbsp;<span id="view_form_field_paymentstatus"></span></p>
								<p>Mode:&nbsp;<span id="view_form_field_paymentmode"></span></p>
								<p>Amount:&nbsp;<span id="view_form_field_membershipfee"></span></p>
								<p>Paid On:&nbsp;<span id="view_form_field_paidon"></span></p>
								<p>Bank Ref.:&nbsp;<span id="view_form_field_bnkref"></span></p>
								<p>Instrument Type:&nbsp;<span id="view_form_field_instrumenttype"></span></p>
								<p>Instrument:&nbsp;<span id="view_form_field_instrument"></span></p>
							</div>
						</div -->
					</div>
					<!---->	
                </div>
            </div>
        </div>
    </div>
</div>