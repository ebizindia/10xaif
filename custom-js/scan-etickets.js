var scantktfuncs={
	scanned_card_input:null,
	scanner:null,
	scanner_initialized: false,
	ajax_data_script:'scan-etickets.php',
	curr_page_hash:'',
	prev_page_hash:'',
	
	saveRecDetails:function(formelem){
    	const res = scantktfuncs.validateForm();
		if(res.errors.length>0){
			$('.alert-danger').removeClass('d-none').siblings('.alert').addClass('d-none').end().find('.alert-message').html(res.errors[0]);
			var page_scroll='.main-container-inner';
			common_js_funcs.scrollTo($(page_scroll));
			scantktfuncs.flashScreen('error');
			setTimeout(function(){
				$(res.error_fields[0],'#addrecform').focus();
			},0);
			return false;
		}
		$('.alert-danger,.alert-success, .alert-warning').addClass('d-none');
		$("#common-processing-overlay").removeClass('d-none');
		$('#record-save-button,#record-cancel-button').addClass('disabled').attr('disabled',true);
		$('#addrecform .error-field').removeClass('error-field');

		return true;

	},

	validateForm : function () {
		let errors = [], error_fields=[];
		$('#record-save-button').blur();
		$(".form-control", "#addrecform").removeClass("error-field");
					
		const tkt_code  = $.trim($('#add_form_field_tc').val());
		const guests_allowed  = $.trim($('#add_form_field_noofguests').data('allowed'));
		const no_of_guests  = $.trim($('#add_form_field_noofguests').val());
		if(tkt_code==''){
			errors.push(`Please cancel the operation and rescan the QR code.`);
		}else if(no_of_guests==''){
			errors.push(`Please enter the number of persons to be allowed entry.`);
			error_fields.push('#add_form_field_noofguests');
			$('#add_form_field_noofguests').addClass("error-field");
		}else if(no_of_guests>guests_allowed){
			errors.push(`The number of persons entered exceeds the max no. of persons allowed.`);
			error_fields.push('#add_form_field_noofguests');
			$('#add_form_field_noofguests').addClass("error-field");
		}
		return {'errors': errors, 'error_fields': error_fields};
		
	},
	handleSaveRecResp:function (resp) {
		$("#common-processing-overlay").addClass('d-none');
		$("#addrecform .error-field").removeClass("error-field");
		let message_container ='';
		if(resp.error_code==0){
			message_container = '.alert-success';
			$(message_container).removeClass('d-none').siblings('.alert').addClass('d-none').end().find('.alert-message').html(resp.message);
			var page_scroll='.main-container-inner';
			common_js_funcs.scrollTo($(page_scroll));
			$('#msgFrm').addClass('d-none');
			scantktfuncs.flashScreen('success');
			setTimeout(()=>{
				scantktfuncs.resetForm();
			},2500);
		}else {
			if(resp.error_fields?.length>0){
				$(resp.error_fields[0]).focus();
				$(resp.error_fields[0]).addClass("error-field");
			}
			message_container = '.alert-danger';
			$(message_container).removeClass('d-none').siblings('.alert').addClass('d-none').end().find('.alert-message').html(resp.message);
			var page_scroll='.main-container-inner';
			common_js_funcs.scrollTo($(page_scroll));
			$('#record-save-button,#record-cancel-button').removeClass('disabled').attr('disabled',false);
			scantktfuncs.flashScreen('error');
		}

		

		// if(message_container!=''){
		// 	$(message_container).removeClass('d-none').siblings('.alert').addClass('d-none').end().find('.alert-message').html(resp.message);
		// 	var page_scroll='.main-container-inner';
		// 	common_js_funcs.scrollTo($(page_scroll));
		// 	$('#msgFrm').addClass('d-none');
		// }
		
	},


	resetForm: function(){
		document.addrecform?.reset();
		$('#add_form_field_tc').val('');
		$('#add_form_field_noofguests').data('allowed','');
		$('#record-save-button,#record-cancel-button').removeClass('disabled').attr('disabled',false).blur();
		$('#addrecform').addClass('d-none');
		$("#addrecform .error-field").removeClass("error-field");
		$('.alert-danger,.alert-success, .alert-warning').addClass('d-none');
		$('#tkt_scan_form_container .card-body').removeClass('highlight-error highlight-success');
		scantktfuncs.scanner_initialized = false;
		scantktfuncs.initScanner();
	},


	getTktDetails: function(qr_code, callback){
		$("#common-processing-overlay").removeClass('d-none');
		common_js_funcs.callServer({cache:'no-cache',async:false,dataType:'json',type:'post',url:scantktfuncs.ajax_data_script,params:{mode:'gettktdtls', qr_code:qr_code},
		successResponseHandler:callback,
		successResponseHandlerParams:{}});
	},

	showTktDetails: function(tkt_details, extra){
		$("#common-processing-overlay").addClass('d-none');
		// $('.alert-danger,.alert-success,.alert-warning').addClass('d-none');
		if(tkt_details.error_code==0){
			$('#addrecform').removeClass('d-none');
			$('#add_form_field_tc').val(tkt_details?.details?.qr_code);
			$('#event_name').text(tkt_details?.details?.ev_name);
			$('#mem_name').text(tkt_details?.details?.mem_name);
			$('#booking_id').text(tkt_details?.details?.booking_id);
			const max_guests_allowed = tkt_details?.details?.no_of_tickets??0;
			const guests_attended = tkt_details?.details?.attended??0;
			const more_guests_allowed = max_guests_allowed - guests_attended;
			$('#guests_allowed').text(more_guests_allowed==max_guests_allowed?more_guests_allowed:`${more_guests_allowed} of ${max_guests_allowed}`);
			$('#add_form_field_noofguests').val('1').data('allowed',more_guests_allowed).focus();

			$('#record-save-button, #record-cancel-button').removeClass('disabled').attr('disabled', false);
			$('#cancel_entry').removeClass('disabled').attr('disabled', false);
			
		}else{
			$('#addrecform').addClass('d-none');
			$('#add_form_field_tc').val('');
			$('.alert-danger').removeClass('d-none').siblings('.alert').addClass('d-none').end().find('.alert-message').html(tkt_details.message);
			var page_scroll='.main-container-inner';
			common_js_funcs.scrollTo($(page_scroll));
			scantktfuncs.flashScreen('error');
			setTimeout(()=>{
				scantktfuncs.resetForm();
			},2500);
		}
	},


	initScanner: function(){
		const scanner_cont = document.getElementById('scanner_screen_cont');
		scantktfuncs.scanned_card_input = $('#add_form_field_tc');
	    scantktfuncs.scanned_card_input.val('');
	    // document.getElementById('scantkt-form-section').style.display='none';
	    scantktfuncs.scanner_initialized = true;
		scantktfuncs.scanner = new QRScan(scantktfuncs.onScanInitFailure, scantktfuncs.onScanSuccess, scantktfuncs.onScanFailure, scanner_cont);
	    scantktfuncs.scanner.startQrScanner();
	},
	
	onEntryCancel: function(ev){
		ev.preventDefault();
		ev.stopPropagation();
		$("#common-processing-overlay").removeClass('d-none');
		setTimeout(()=>{
			scantktfuncs.resetForm();
			$("#common-processing-overlay").addClass('d-none');

		},0);
	},


	onScanSuccess: function(decodedText, decodedResult) {
        // Set the decoded text (ID) into the input field
        scantktfuncs.scanned_card_input.val(decodedText);//.trigger('change');
        scantktfuncs.scanner.stopQrScanner();
        scantktfuncs.scanner = null;
        // document.getElementById('scantkt-form-section').style.display='';
        // $('#add_form_field_amount', '#addrecform').focus();
        scantktfuncs.getTktDetails(decodedText, scantktfuncs.showTktDetails);
    },

    onScanFailure: function(error) {
        // console.log(`QR Code scanning error: ${error}`);
        // document.getElementById('bookingForm').style.display='';
    },

    onScanInitFailure: function(error) {
    	scantktfuncs.scanner_initialized = false;
    	$('.alert-danger').removeClass('d-none').siblings('.alert').addClass('d-none').end().find('.alert-message').html(`Scanner initialisation error: ${error}`);
		var page_scroll='.main-container-inner';
		common_js_funcs.scrollTo($(page_scroll));
		scantktfuncs.flashScreen('error');
		// scantktfuncs.getTktDetails('B1VK1', scantktfuncs.showTktDetails);
		// scantktfuncs.getTktDetails('HBGPB', scantktfuncs.showTktDetails);

    },

    onCancelScan: function(ev){
    	// document.getElementById('scantkt-form-section').style.display='';	
    	if(scantktfuncs.scanner_initialized==true){
    		scantktfuncs.scanner.stopQrScanner();
    	}
    },

    onAmountBoxFocus:function(ev){
    	$(ev.currentTarget).select();
    },

    flashScreen: function(type, callback=null, callback_params={}){
    	$('#tkt_scan_form_container .card-body').removeClass('highlight-error highlight-success');
    	setTimeout(()=>{
    		$('#tkt_scan_form_container .card-body').addClass('highlight-'+type);

    	},0);
    	if(callback){
	    	setTimeout(()=>{
	    		callback(callback_params);
	    	}, 1000); //half a sec
    	}
    }


}

// $('#add_form_field_cardcode').on('change', e=>{
// 	if(e.target.value!='')
// 		scantktfuncs.getTktDetails(e.target.value, scantktfuncs.showTktDetails);
// });