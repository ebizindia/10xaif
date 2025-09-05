// login js
var login_funcs={
	email_pattern:new RegExp("^\\w+([.']?-*\\w+)*@\\w+([.-]?\\w+)*(\\.\\w{2,4})+$","i"),
	alert_error_elem: $("div#alert-error"),
	alert_success_elem: $("div#alert-success"),
	alert_logout_success_elem: $("div#alert-logout-success"),
	login:function(formelem){
		var self=this;
		$("div#alert-error").addClass('d-none');
		$("div#alert-success").addClass('d-none');
		$("div#alert-logout-success").addClass('d-none');
		var login_username=$(formelem).find("input#login_username").val().trim();
		var login_password=$(formelem).find("input#login_password").val().trim();
		var login_remember=$(formelem).find("input#login_remember:checked").val();
		var login_referurl=$(formelem).find("input[name=referurl]").val();

		if(login_remember!='1')
			login_remember='0';

		var validation_error=0; // 0 | 1
		var validation_error_msg='';

		if(login_username==''){
			validation_error=1;
			validation_error_msg='Enter email ID.';
			$(formelem).find("input#login_username").focus();

		}else if(login_password==''){
			validation_error=1;
			validation_error_msg='Enter password.';
			$(formelem).find("input#login_password").focus();

		}




		if(validation_error==1){
			$("div#alert-success").addClass('d-none');
			$("div#alert-logout-success").addClass('d-none');
			$("div#alert-error").removeClass('d-none');
			$("div#alert-error").find('#alert-error-message').html(validation_error_msg);
		}else{

			var login_url = 'login.php';
			var destination_url = '';

			$.ajax({
				async:false,
				cache:false,
				type:'post',
				dataType:'json',
				url:login_url,
				data:"mode=login&login_username="+encodeURIComponent(login_username)+"&login_password="+encodeURIComponent(login_password)+"&login_remember="+encodeURIComponent(login_remember)+"&referurl="+encodeURIComponent(login_referurl),
				success:function(resp,status){

					if(resp.errorcode==0){
						$("div#alert-error").addClass('d-none');
						if(resp.location){
							$("div#alert-logout-success").addClass('d-none');
							$("div#alert-success").removeClass('d-none');
							if(destination_url!='')
								window.location.href=destination_url;
							else
								window.location.href=resp.location;
						}else{

						}

					}else{
						$("div#alert-success").addClass('d-none');
						$("div#alert-logout-success").addClass('d-none');
						$("div#alert-error").removeClass('d-none');
						$("div#alert-error").find('#alert-error-message').html(resp.msg);
						if(resp.errorcode==1){
							$(formelem).find("input#login_username").focus();
						}else if(resp.errorcode==2){
							$(formelem).find("input#login_username").focus();
						}else if(resp.errorcode==3){
							$(formelem).find("input#login_passowrd").focus().val('');
						}else if(resp.errorcode==4){
							$(formelem).find("input#login_username").focus();
						}else{
							$(formelem).find("input#login_username").focus();
						}
					}

				},
				error:function(obj,error,status){

					if(obj.status == '302'){
						window.location.reload();

					}else{
						alert(error+' | '+status);
						$("div#alert-error").addClass('d-none');
						$("div#alert-success").addClass('d-none');
						$("div#alert-logout-success").addClass('d-none');
					}
				},

			});
		}

		return false;
	},



	handleClientChangeResponse: function(resp){
		if(resp.Error!=0)
			if(resp.Message)
				bootbox.alert({animate:false,message:resp.Message,closeButton: false});
		if(resp.Action){
			if(resp.Action=='Reload')
				window.location = window.location
			else if(resp.Action=='Redirect')
				window.location = resp.URL;
		}
	},

	showLogoutResult:function(resp){

		if(resp.errorcode==1){
			$("div#alert-success").addClass('d-none');
			$("div#alert-logout-success").addClass('d-none');
			$("div#alert-error").removeClass('d-none');
			$("#alert-error-message").html(resp.msg);
		}else{
			$("div#alert-error").addClass('d-none');
			$("div#alert-success").addClass('d-none');
			$("div#alert-logout-success").removeClass('d-none');

		}
		 localStorage.removeItem('my_schedule_search_filter');



	},

	onLoginPageBoxesFlip:function(params_obj){
		$("div#alert-error").addClass('d-none');
		$("div#alert-success").addClass('d-none');
		$("div#alert-logout-success").addClass('d-none');

	},

	sendRetrievePasswordRequest:function(formelem){
		$("div#alert-error").addClass('d-none');
		$("div#alert-success").addClass('d-none');
		$("div#alert-logout-success").addClass('d-none');
		var pretrieve_email=$(formelem).find("input#pretrieve_email").val();

		// show form processing indicator
		$("div#forgot-box div#form-container").addClass('d-none');
		$("div#forgot-box div#form-processing-indicator").removeClass('d-none');

		$.ajax({
			cache:false,
			type:'post',
			dataType:'json',
			url:"login.php",
			data:"mode=sendpasswordresetlink&pretrieve_email="+encodeURIComponent(pretrieve_email),

			success:function(resp,status){
				// console.log(resp);

				$("div#forgot-box div#form-container").removeClass('d-none');
				$("div#forgot-box div#form-processing-indicator").addClass('d-none');
				if(resp.errorcode==0){
					$("div#alert-error").addClass('d-none');
					$("div#alert-success").addClass('d-none');
					$("div#alert-logout-success").removeClass('d-none');
					$("div#alert-logout-success").find('#alert-common-success-message').html(resp.msg);

				}else{
					$("div#alert-success").addClass('d-none');
					$("div#alert-logout-success").addClass('d-none');
					$("div#alert-error").removeClass('d-none');
					$("input#pretrieve_email").focus();
					$("div#alert-error").find('#alert-error-message').html(resp.msg);
				}

			},
			error:function(obj,status,error){
				$("div#forgot-box div#form-container").removeClass('d-none');
				$("div#forgot-box div#form-processing-indicator").addClass('d-none');
				alert(error+' | '+status);
				$("div#alert-error").addClass('d-none');
				$("div#alert-success").addClass('d-none');
				$("div#alert-logout-success").addClass('d-none');
			},

		});


		return false;
	},


	onResetPasswordFormDisplay:function(options){
		options.self.onLoginPageBoxesFlip();
		$("input#resetpswd_email_id").val(options.resp.email_id);
		$("input#resetpswd_key").val(options.resp.k);

	},

	showPasswordResetForm:function(resp){
		var self=this;

		if(resp.errorcode==0){
			show_box('password-reset-box',{callback:self.onResetPasswordFormDisplay,callback_params_obj:{self:self,resp:resp}});

		}else{
			show_box('login-box',{callback:self.onLoginPageBoxesFlip});
			$("div#alert-error").removeClass('d-none');
			$("div#alert-error").find('#alert-error-message').html(resp.msg);

		}
		//resetpswd_password
	},

	showActivationForm:function(resp){
		var self=this;

		if(resp.errorcode==0){
			show_box('activation-box',{callback:self.onResetPasswordFormDisplay,callback_params_obj:{self:self,resp:resp}});

		}else{
			show_box('activation-box',{callback:self.onLoginPageBoxesFlip});
			$("div#alert-error").removeClass('d-none');
			$("div#alert-error").find('#alert-error-message').html(resp.msg);

		}
		//resetpswd_password
	},

	activateAccount:function(formelem){
		$("div#alert-error").addClass('d-none');
		$("div#alert-success").addClass('d-none');
		$("div#alert-logout-success").addClass('d-none');
		var resetpswd_email_id=$(formelem).find("input#resetpswd_email_id").val();
		var resetpswd_key=$(formelem).find("input#resetpswd_key").val();
		var resetpswd_password=$(formelem).find("input#resetpswd_password").val();
		var resetpswd_passwordre=$(formelem).find("input#resetpswd_passwordre").val();
		// show form processing indicator
		$("div#password-reset-box div#form-container").addClass('d-none');
		$("div#password-reset-box div#form-processing-indicator").removeClass('d-none');

		$.ajax({
			cache:false,
			type:'post',
			dataType:'json',
			url:"login.php",
			data:"mode=setpassword&resetpswd_email_id="+encodeURIComponent(resetpswd_email_id)+"&resetpswd_key="+encodeURIComponent(resetpswd_key)+"&resetpswd_password="+encodeURIComponent(resetpswd_password)+"&resetpswd_passwordre="+encodeURIComponent(resetpswd_passwordre),
			success:function(resp,status){

				$("div#password-reset-box div#form-container").removeClass('d-none');
				$("div#password-reset-box div#form-processing-indicator").addClass('d-none');
				if(resp.errorcode==0){
					$("div#alert-error").addClass('d-none');
					$("div#alert-success").addClass('d-none');
					$("div#alert-logout-success").removeClass('d-none');
					$("div#alert-logout-success").find('#alert-common-success-message').html(resp.msg);
					show_box('login-box',{callback:self.onLoginPageBoxesFlip});

				}else{
					$("div#alert-success").addClass('d-none');
					$("div#alert-logout-success").addClass('d-none');
					$("div#alert-error").removeClass('d-none');
					$("div#alert-error").find('#alert-error-message').html(resp.msg);
					if(resp.errorcode==6){
						$(formelem).find("input#resetpswd_password").focus();
					}else if(resp.errorcode==5 || resp.errorcode==61 ){
						$(formelem).find("input#resetpswd_passwordre").focus();
					}

				}

			},
			error:function(obj,status,error){
				$("div#password-reset-box div#form-container").removeClass('d-none');
				$("div#password-reset-box div#form-processing-indicator").addClass('d-none');
				alert(error+' | '+status);
				$("div#alert-error").addClass('d-none');
				$("div#alert-success").addClass('d-none');
				$("div#alert-logout-success").addClass('d-none');
			},

		});


		return false;

	},

	setNewPassword:function(formelem){
		$("div#alert-error").addClass('d-none');
		$("div#alert-success").addClass('d-none');
		$("div#alert-logout-success").addClass('d-none');
		var resetpswd_email_id=$(formelem).find("input#resetpswd_email_id").val();
		var resetpswd_key=$(formelem).find("input#resetpswd_key").val();
		var resetpswd_password=$(formelem).find("input#resetpswd_password").val();
		var resetpswd_passwordre=$(formelem).find("input#resetpswd_passwordre").val();

		// show form processing indicator
		$("div#password-reset-box div#form-container").addClass('d-none');
		$("div#password-reset-box div#form-processing-indicator").removeClass('d-none');

		$.ajax({
			cache:false,
			type:'post',
			dataType:'json',
			url:"login.php",
			data:"mode=setnewpassword&resetpswd_email_id="+encodeURIComponent(resetpswd_email_id)+"&resetpswd_key="+encodeURIComponent(resetpswd_key)+"&resetpswd_password="+encodeURIComponent(resetpswd_password)+"&resetpswd_passwordre="+encodeURIComponent(resetpswd_passwordre),
			success:function(resp,status){

				$("div#password-reset-box div#form-container").removeClass('d-none');
				$("div#password-reset-box div#form-processing-indicator").addClass('d-none');
				if(resp.errorcode==0){
					$("div#alert-error").addClass('d-none');
					$("div#alert-success").addClass('d-none');
					$("div#alert-logout-success").removeClass('d-none');
					$("div#alert-logout-success").find('#alert-common-success-message').html(resp.msg);
					show_box('login-box',{callback:self.onLoginPageBoxesFlip});

				}else{
					$("div#alert-success").addClass('d-none');
					$("div#alert-logout-success").addClass('d-none');
					$("div#alert-error").removeClass('d-none');
					$("div#alert-error").find('#alert-error-message').html(resp.msg);
					if(resp.errorcode==6){
						$(formelem).find("input#resetpswd_password").focus();
					}else if(resp.errorcode==5 || resp.errorcode==61 ){
						$(formelem).find("input#resetpswd_passwordre").focus();
					}

				}

			},
			error:function(obj,status,error){
				$("div#password-reset-box div#form-container").removeClass('d-none');
				$("div#password-reset-box div#form-processing-indicator").addClass('d-none');
				alert(error+' | '+status);
				$("div#alert-error").addClass('d-none');
				$("div#alert-success").addClass('d-none');
				$("div#alert-logout-success").addClass('d-none');
			},

		});


		return false;



	}


}

function show_box(id,options) {
	jQuery('.flippable-box.flip-visible').removeClass('flip-visible');
	jQuery('#'+id).addClass('flip-visible');

	if(typeof options=='object'){
		if(options.hasOwnProperty('callback') && options.callback!=null){
			var callback_params_obj=(options.hasOwnProperty('callback_params_obj') && typeof options.callback_params_obj=='object')?options.callback_params_obj:{};
			options.callback(callback_params_obj);
		}
	}
}
