<style>
.login-container {max-width: 410px;}
.form-control {margin-top: 4px;}
.welcome{font-size:16px;}
.welcome > span{font-weight:600;}
@media screen and (max-width:991px){
	.form-control {
	  margin-top: -5px;
	}
}
</style>

<div class="login-container">
    <div class="logo text-center"><img  src="<?php echo CONST_LOGO_IMAGE_LARGE; ?>" /><?php /*echo $this->base_template_data['app_disp_name'];*/ ?></div>
		<!--<p class="text-center welcome">Welcome to <span>10X AIF</span></p>-->
      <div id="login-box" class="flippable-box flip-visible">
          <div class="login clearfix">

            <h3 class="text-center">Please Login</h3>
            <div>
			<div  id='alert-error' class="alert alert-danger d-none">
				<span id='alert-error-message'>Change a few things up and try submitting again.</span>
			</div>
    </div>
     <div>
			<div  id='alert-success' class="alert alert-success d-none">
				<!-- <i class="fa fa-check"></i>--><img src="images/green-tick.png" class="check-button" alt="Check">  Login successful.
				<span id='alert-success-message'>Redirecting...</span>
			</div>
    </div>
     <div>
			<div  id='alert-logout-success' class="alert alert-success d-none">
				<!-- <i class="fa fa-check"></i>--><img src="images/green-tick.png" class="check-button" alt="Check">
				<span id='alert-common-success-message'>Logout successful.</span>
			</div>
    </div>

            <form class="form-horizontal" name="Login"  onsubmit="return login_funcs.login(this);">
            <input type="hidden" name="remember" value=""/>
            <input type='hidden' name='referurl' value="<?php echo $this->body_template_data['referurl']; ?>" />
			
			<!---->
			<div class="form-group row">
				<label for="staticEmail" class="col-sm-12 col-nd-3 col-lg-3 col-form-label">Email</label>
				<div class="col-sm-12 col-nd-9 col-lg-9">
					<input type="text" name="login_username" id="login_username" class="form-control" placeholder="Email" />
				</div>
			</div>
			<div class="form-group row">
				<label for="inputPassword" class="col-sm-12 col-nd-3 col-lg-3 col-form-label">Password</label>
				<div class="col-sm-12 col-nd-9 col-lg-9">
					<input type="password" name="login_password" id="login_password" class="form-control" placeholder="Password" />
				</div>
			</div>
			<!---->

            <div class="form-group mb-0 row">
              <div class="col-sm-12">
                <div class="checkbox">
                  <label>
                  <?php
                    $checked='';
                    if($this->body_template_data['is_remember_me']==1){
                       $checked='checked';
                    }
                  ?>
                  <input <?php echo $checked; ?>  type="checkbox" name='login_remember' id='login_remember' value='1' autocomplete="off" /> 

                    <span>Remember Me</span></label>
                 </div>
              </div>
            </div>

            <div class="form-group row">
              <div class="col-sm-12 text-center">
                <button type="submit" class="btn btn-primary btn-block" style="padding: 7px 10px;font-size:17px;font-weight:bold;">
                    <i class="fa fa-key"></i>
                    Login
                </button>
                
                <a class="btn btn-link" href="#" onclick="show_box('forgot-box',{callback:login_funcs.onLoginPageBoxesFlip}); return false;" class="forgot-password-link" style="font-size:16px;">
                  <i class="icon-arrow-left"></i>
                  I forgot my password
                </a>
              </div>
              <!-- <div class="col-sm-12 text-center mt-3">
                New user? <strong><a href="<?php echo $this->body_template_data['root_uri']; ?>register/" class="forgot-password-link">Register Now</a></strong>
              </div> -->
            </div>

            </form>

          </div>
        </div>


        <!-- forgot-box -->

        <div id="forgot-box" class="flippable-box">
            <div class="forgot">

                <h3 class="text-center">Reset Password</h3>
				<div  id='alert-error' class="alert alert-danger d-none">
					<span id='alert-error-message'>Change a few things up and try submitting again.</span>
				</div>

				<div  id='alert-success' class="alert alert-success d-none">
					<!-- <i class="fa fa-check"></i>--><img src="images/green-tick.png" class="check-button" alt="Check">  Login successful.
					<span id='alert-success-message'>Redirecting...</span>
				</div>

				<div  id='alert-logout-success' class="alert alert-success d-none">
					<!-- <i class="fa fa-check"></i>--><img src="images/green-tick.png" class="check-button" alt="Check">
					<span id='alert-common-success-message'>Logout successful.</span>
				</div>

                <p class="text-center text-secondary">Enter your email address to receive instructions.</p>

                <form class="form-horizontal" name="retrieve-password" method="post"  onsubmit="return login_funcs.sendRetrievePasswordRequest(this);"  >

                <div class="form-group">
                  <div class="col-sm-12">
                      <input class="form-control" type="email" name="pretrieve_email" id="pretrieve_email"  placeholder="Email ID" />
                  </div>
                </div>

                <div class="form-group">
                  <div class="col-sm-12 text-center">
                      <button type="submit" class="btn btn-block btn-danger">
                        <i class="fa fa-send"></i>
                        Send
                      </button>
                      <br>
                      <a class="btn btn-link" href="#" onclick="show_box('login-box',{callback:login_funcs.onLoginPageBoxesFlip}); return false;" >
                        <i class="fa fa-long-arrow-left"></i> Back to login
                      </a>
                  </div>
                </div>

                </form>


                <div id="form-processing-indicator"   class="loding-spinner d-none"><i class="icon-spinner icon-spin orange bigger-225"></i><br>Please wait...</div>

          </div>
        </div>



        <!-- password reset -->

        <div id="password-reset-box" class="flippable-box">
            <div class="password-reset">

              <h4 class="text-center">Set New Password</h4>
			  <div  id='alert-error' class="alert alert-danger d-none">
				  <span id='alert-error-message'>Change a few things up and try submitting again.</span>
			  </div>

			  <div  id='alert-success' class="alert alert-success d-none">
				  <!-- <i class="fa fa-check"></i>--><img src="images/green-tick.png" class="check-button" alt="Check">  Login successful.
				  <span id='alert-success-message'>Redirecting...</span>
			  </div>

			  <div  id='alert-logout-success' class="alert alert-success d-none">
				  <!-- <i class="fa fa-check"></i>--><img src="images/green-tick.png" class="check-button" alt="Check">
				  <span id='alert-common-success-message'>Logout successful.</span>
			  </div>

              <p class="text-center text-secondary"> Enter your new password: </p>

              <form class="form-horizontal" onsubmit="return login_funcs.setNewPassword(this);"  >
              <input type='hidden' name="resetpswd_email_id" id="resetpswd_email_id"  value='' />
              <input type='hidden' name="resetpswd_key" id="resetpswd_key"  value='' />


                  <div class="form-group">
                    <div class="col-sm-12">
                        <input class="form-control" type="password" placeholder="New password" name="resetpswd_password" id="resetpswd_password"  />
                    </div>
                  </div>

                  <div class="form-group">
                    <div class="col-sm-12">
                      <input class="form-control" type="password" placeholder="Re-enter password" name="resetpswd_passwordre" id="resetpswd_passwordre"/>
                    </div>
                  </div>

                  <div class="form-group">
                    <div class="col-sm-12 text-center">
                      <button type="submit" class="btn btn-block btn-success">
                        Set Password
                        <i class="fa fa-lock"></i>
                      </button>
                      <br>
                      <a href="#" onclick="show_box('login-box',{callback:login_funcs.onLoginPageBoxesFlip}); return false;" class="back-to-login-link">
                          <i class="fa fa-long-arrow-left"></i>
                          Back to login
                      </a>
                    </div>
                  </div>

              </form>

              <div  id="form-processing-indicator" class="loding-spinner d-none"><i class="icon-spinner icon-spin orange bigger-225"></i><br>Please wait...</div>

            </div>
        </div>
        
</div>
