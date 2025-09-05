<?php if($this->base_template_data['user_id']>0) { ?>
<div class="page-content">	
	<h4 class="header red">
		<i class="icon-warning-sign red"></i>
		Looks like you took a wrong turn
	</h4>
	<div class="space-6"></div>
	<div id='form-container'>
		<p>
		You may have followed an old link, typed in the address incorrectly, or are just trying to make us feel bad. <br/>
		Please choose a link from the menu on the left.
		</p>
	</div>
	<div  id="form-processing-indicator" class="loding-spinner d-none">
		<i class="icon-spinner icon-spin orange bigger-225"></i><br>
		Please wait...
	</div>
</div>
	<?php } else { ?>
	<div class="row">
		<div class="col-sm-10 col-sm-offset-1">
			<div class="login-container">

				<div class="space-12"></div>

				<div class="position-relative">
					<div class="box-404-border">
						<div class="widget-body border-none">
							<div class="widget-main">
								<h4 class="header red margin-none">
									<i class="icon-warning-sign red"></i>
									Looks like you took a wrong turn
								</h4>
								<div class="space-6"></div>
								<div id='form-container'>
									<p>
									You may have followed an old link, typed in the address incorrectly, or are just trying to make us feel bad. 
									</p>
								</div>
								
							</div>
							<div class="toolbar center white">
								If you are a user, <a class="white" href="#" onclick="show_box('login-box',{callback:login_funcs.onLoginPageBoxesFlip}); return false;" class="back-to-login-link">
								please login
									<i class="icon-arrow-right"></i>
								</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php }?>

