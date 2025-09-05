var noticefuncs={
	curr_page_hash:'',
	prev_page_hash:'',
	char_limit : 500,
	email_msg_editor: null,
	remCount: function (ev){
		var remChar = noticefuncs.char_limit-$(ev.currentTarget).val().length
		$('#remcount').html(remChar);
    },

    submitNotice:function(formelem){

		const res = noticefuncs.validateForm();
		if(res.error_fields.length>0){
			alert(res.errors[0]);
			setTimeout(function(){
				if(res.error_fields[0]==='#add_form_field_msgbody'){
					noticefuncs.email_msg_editor.editing.view.focus();
				}else{
					$(res.error_fields[0],'#feedbackform').focus();
				}
			},0);
			return false;

		}
		const send_wa = $('#add_form_field_sendwamsg').is(':checked');
		const send_test_elem = $('#add_form_field_msgtestnotice');
		let conf_msg = '';
		if(send_test_elem.is(':checked')){
			conf_msg = "You are about to send the notice as a test via email to  "+send_test_elem.data('email');
			const wa_num = send_test_elem.data('wa');
			if(send_wa && wa_num!='')
				conf_msg += " and over Whatsapp to "+wa_num+".";
			conf_msg += "\n\nReally send a test notice now?";
		}else{
			conf_msg = "You are about to send the notice to all the selected members via email";
			if(send_wa)
				conf_msg += " and over Whatsapp";
			conf_msg += ".\n\n Really send the notice now?";
		}
		noticefuncs.email_msg_editor.updateSourceElement();

		if(!confirm(conf_msg))
			return false;

		$("#common-processing-overlay").removeClass('d-none');
		$('#record-save-button').addClass('disabled').attr('disabled',true);
		$('#feedback_form_container .error-field').removeClass('error-field');

		return true;

	},

	validateForm : function () {
		var errors = [], error_fields=[];
		$(".form-control").removeClass("error-field");
		const groups = $('input[name="members[]"]:checked');
		const msg_body  = $.trim(noticefuncs.email_msg_editor.getData()); //$.trim($('#add_form_field_msgbody').val());
		const msg_sub  = $.trim($('#add_form_field_msgsub').val());
		const send_via_wa  = $('#add_form_field_sendwamsg').is(':checked');
		const wa_campaign  = $.trim($('#add_form_field_msgcampaign').val());
		if(groups.length==0){
			errors.push('Please select the recipient members.');
			error_fields.push('input[id^=add_form_field_groups_]:eq(0)');
			$('input[name="groups[]"]').addClass("error-field");
		}else if(msg_sub == ''){
			errors.push('Please enter the subject for the notice.');
			error_fields.push('#add_form_field_msgsub');
			$('#add_form_field_msgsub').addClass("error-field");
		}else if(msg_body == ''){
			errors.push('Please enter the message to be sent.');
			error_fields.push('#add_form_field_msgbody');
			// $('#add_form_field_msgbody').addClass("error-field");
			$('#add_form_field_msgbody').siblings('.ck-editor').addClass("error-field");
			document.getElementById('add_form_field_msgsub').scrollIntoView(true);
		}else if(send_via_wa==true && wa_campaign==''){
			errors.push('Please enter the name of the WhatsApp campaign.');
			error_fields.push('#add_form_field_msgcampaign');
			$('#add_form_field_msgcampaign').addClass("error-field");
		}/*else if(msg_body.length > noticefuncs.char_limit) {
			errors.push('The messge text has exceeded the allowed the number of characters.');
			error_fields.push('#add_form_field_msgbody');
		}*/
		return {'errors': errors, 'error_fields': error_fields};
		
	},
	sendNoticeResp:function (resp) {
		$("#common-processing-overlay").addClass('d-none');
		$(".form-control").removeClass("error-field");
		let message_container ='';
		if(resp.error_code==0){
			message_container = '.alert-success';
			// $('#add_form_field_msgsub,#add_form_field_msgbody, #add_form_field_attachment').val('');
			$('#add_form_field_msgsub, #add_form_field_attachment').val('');
			noticefuncs.email_msg_editor.setData(''); // empty the editor
			$('#add_form_field_msgsub').focus();
		}else if(resp.error_code==2){
			if(resp.error_fields.length>0){
				var msg = resp.message;
				alert(msg);
				$(resp.error_fields[0]).focus();
				$(resp.error_fields[0]).addClass("error-field");
			}

		}else{
			message_container = '.alert-danger';
			$('#add_form_field_msgsub').focus();
		}

		$('#record-save-button').removeClass('disabled').attr('disabled',false);
		$("#common-processing-overlay").addClass('d-none');

		if(message_container!=''){
			$(message_container).removeClass('d-none').siblings('.alert').addClass('d-none').end().find('.alert-message').html(resp.message);
			var page_scroll='.main-container-inner';
			common_js_funcs.scrollTo($(page_scroll));
			$('#msgFrm').addClass('d-none');
		}

	
	},

	removeAttachment: function(e){
		e.preventDefault();
		e.stopPropagation();
		$('#add_form_field_attachment').val('');

	},

	toggleWhatsApp: function(e){
		if($(e.currentTarget).is(':checked')){
			$('#add_form_field_msgcampaign').removeClass('non-editable').attr('disabled', false).parents('.form-group:eq(0)').find('.control-label').css('color','').find('span.mandatory').removeClass('d-none').end().end().end();
			$('#add_form_field_msgreplacements').removeClass('non-editable').attr('disabled', false).parents('.form-group:eq(0)').find('.control-label').css('color','').end().end();

		}else{
			$('#add_form_field_msgcampaign').addClass('non-editable').attr('disabled', true).parents('.form-group:eq(0)').find('.control-label').css('color','#adacac').find('span.mandatory').addClass('d-none').end().end().end();
			$('#add_form_field_msgreplacements').addClass('non-editable').attr('disabled', true).parents('.form-group:eq(0)').find('.control-label').css('color','#adacac').end().end();
		}
	},

	selDeselAllGrps: function(e){
		e.preventDefault();
		e.stopPropagation();
		if(e.currentTarget.id=="selallgrps")
			$('.notice_groups_cont input[type=checkbox]').prop('checked', true);
		else
			$('.notice_groups_cont input[type=checkbox]').prop('checked', false);
	}

}