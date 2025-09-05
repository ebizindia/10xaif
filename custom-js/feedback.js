var feedbackfuncs={
	curr_page_hash:'',
	prev_page_hash:'',
	char_limit : 500,
	remCount: function (ev){
		var remChar = feedbackfuncs.char_limit-$(ev.currentTarget).val().length
		$('#remcount').html(remChar);
    },

    submitFeedback:function(formelem){

		const res = feedbackfuncs.validateForm();
		if(res.error_fields.length>0){
			alert(res.errors[0]);
			setTimeout(function(){
				$(res.error_fields[0],'#feedbackform').focus();
			},0);
			return false;

		}

		$("#common-processing-overlay").removeClass('d-none');
		$('#record-save-button').addClass('disabled').attr('disabled',true);
		$('#feedback_form_container .error-field').removeClass('error-field');

		return true;

	},

	validateForm : function () {
		var errors = [], error_fields=[];
		$(".form-control").removeClass("error-field");
		const msg_body  = $.trim($('#add_form_field_msgbody').val());
		if(msg_body == ''){
			errors.push('Please enter your feedback message.');
			error_fields.push('#add_form_field_msgbody');
			$('#add_form_field_msgbody').addClass("error-field");
		}/*else if(msg_body.length > feedbackfuncs.char_limit) {
			errors.push('The messge text has exceeded the allowed the number of characters.');
			error_fields.push('#add_form_field_msgbody');
		}*/
		return {'errors': errors, 'error_fields': error_fields};
		
	},
	sendFeedbackResp:function (resp) {
		$("#common-processing-overlay").addClass('d-none');
		// $("#record-send-button").prop('disabled',false).find("span").html("Send Enquiry");
		if(resp.error_code == 0){
			$("#feedbackform")[0].reset();
			// $('#remcount').html(feedbackfuncs.char_limit);
		}
		$('#add_form_field_msgbody').focus();


		$(".form-control").removeClass("error-field");
		let message_container ='';
		if(resp.error_code==0){
			message_container = '.alert-success';
			$("#feedbackform")[0].reset();
			// $('#remcount').html(feedbackfuncs.char_limit);
		}else if(resp.error_code==2){
			if(resp.error_fields.length>0){
				var msg = resp.message;
				alert(msg);
				$(resp.error_fields[0]).focus();
				$(resp.error_fields[0]).addClass("error-field");
			}

		}else{
			message_container = '.alert-danger';
		}

		$('#record-save-button').removeClass('disabled').attr('disabled',false);
		$("#common-processing-overlay").addClass('d-none');

		if(message_container!=''){
			$(message_container).removeClass('d-none').siblings('.alert').addClass('d-none').end().find('.alert-message').html(resp.message);
			var page_scroll='.main-container-inner';
			common_js_funcs.scrollTo($(page_scroll));
			$('#msgFrm').addClass('d-none');
		}

	
	}

}