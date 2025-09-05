var ManageOffer={
	search_params:[],  /* [{search_on:'',search_type:'',search_text:''},{},..] */
	sort_params:[],  /* [{sort_on:'',sort_order:''},{},..] */
	default_sort:{sort_on:'valid_upto',sort_order:'DESC'},
	pagination_data:{},
	status_change_started:0,
	ajax_data_script:'manage-offers.php',
	curr_page_hash:'',
	prev_page_hash:'',
	// title_pattern: /^[A-Z0-9\%&'_ -]+$/i,
	max_valid_upto: '2099-12-31',
	//pp_max_filesize:0,
	
	initiateStatusChange: status_cell =>{
		let temp_text='Activate',
			color='#00a650'; 
		if($(status_cell).find(':nth-child(1)').hasClass('status-live')){
			let temp_text='Deactivate';
			let color='#ff3333'; // red
		}
		$(status_cell).find(':nth-child(1)').html(temp_text);
		$(status_cell).find(':nth-child(1)').css('color',color);
	},
	toggleSearch: ev=>{
		let elem = $(ev.currentTarget);
		elem.toggleClass('search-form-visible', !elem.hasClass('search-form-visible'));
		$('#search_records').closest('.panel-search').toggleClass('d-none', !elem.hasClass('search-form-visible'));
		let search_form_cont = $('#search_records').closest('.panel-search');
		if(search_form_cont.hasClass('d-none'))
			elem.prop('title','Open search panel');
		else{
			elem.prop('title','Close search panel');
			$("#search-field_title").focus();
		}
		if(typeof(Storage) !== "undefined") {
			localStorage.ad_search_toggle = elem.hasClass('search-form-visible') ? 'visible' : '';
		}else{
			Cookies.set('ad_search_toggle', elem.hasClass('search-form-visible') ? 'visible' : '', {path : '/'/*, secure: true*/});
		}
	},

	handleExistingFileRemoval: e=>{
		let el=$(e.currentTarget),
			s=$(e.currentTarget).data('state');
		switch(s){
			case 1: //delete existing
				el.closest('.has-existing-file').addClass('remove-file').find('.remove-file-input').val('1');
				break;
			case 2: //do not delete existing
				el.closest('.has-existing-file').removeClass('remove-file')
					.find('input[type=file]').val('').end()
					.find('.remove-file-input').val('0').end()
					.find('.clear-file-input').addClass('d-none').end();
					;
				break;
			default: //default
				break;
		}
	},
	fileSelected: e =>{
		let p=$(e.currentTarget).closest('.has-existing-file');
		$(e.currentTarget).siblings('.clear-file-input').removeClass('d-none');
		if(!p || p.hasClass('remove-file'))
			return;
		p.addClass('remove-file').find('.remove-file-input').val('1');
	},
	clearFileInput: e=>{
		$(e.currentTarget).addClass('d-none').closest('.existing-file-view').find('input[type=file]').val('');
	},
	/*confirmAndExecuteStatusChange: status_cell=>{
		ManageOffer.status_change_started=1;
		let text=$(status_cell).find(':nth-child(1)').html(),
			new_status=1,
			new_status_text='activate';
		if($(status_cell).find(':nth-child(1)').hasClass('status-live')){
			new_status=0;
			new_status_text='deactivate';
		}

		let row_elem=$(status_cell).parent(),
			row_id=row_elem.attr('id'),
			temp=rowid.split('_'),
			user_id=temp[temp.length-1],
			title=row_elem.find('td:eq(1)').html();
		if(confirm(`Really ${new_status_text} the user "${title}"?`)){
			common_js_funcs.callServer({
					cache:'no-cache',
					dataType:'json',
					async:true,
					type:'post',
					url:ManageOffer.ajax_data_script+"?mode=changeStatus",
					data:"new_status="+new_status+"&rec_id="+user_id,
					successResponseHandler:ManageOffer.handleStatusChangeResponse,
					successResponseHandlerParams:{
							status_cell:status_cell,
							row_elem:row_elem
					}
				});
			let s = parseInt(new_status)==1
			$(status_cell).toggleClass("status-grn", s).toggleClass("status-red", !s);
		}else{
			ManageOffer.status_change_started=0;
			ManageOffer.abortStatusChange(status_cell);
		}
	},
*/
	/*abortStatusChange: status_cell=>{
		if(ManageOffer.status_change_started==0){
			$(status_cell).find(':nth-child(1)').html($(status_cell).find(':nth-child(1)').hasClass('status-live')? 'Active' : 'Inactive');
		}
	},

	handleStatusChangeResponse: (resp,other_params)=>{
		ManageOffer.status_change_started=0;
		if(resp.errorcode!=0){
			ManageOffer.abortStatusChange(other_params.status_cell);
			if(resp.errorcode == 5)
				alert(resp.errormsg)
			else
				alert("Sorry, the status could not be updated.");
		}else{
			let s = $(other_params.status_cell).find(':nth-child(1)').hasClass('status-live');
			$(other_params.status_cell).find(':nth-child(1)').toggleClass('status-live',!s).toggleClass("status-notlive",s);
			other_params.row_elem.toggleClass('inactiverow');
			ManageOffer.abortStatusChange(other_params.status_cell);
		}
	},*/

	getList: options=>{
		let pno=1,
			params=['pno='+encodeURIComponent(options.pno || 1)]
		params.push('search_data='+encodeURIComponent(JSON.stringify(ManageOffer.search_params)));
		params.push('sort_data='+encodeURIComponent(JSON.stringify(ManageOffer.sort_params)));
		params.push('ref='+Math.random());
		$("#common-processing-overlay").removeClass('d-none');
		location.hash=params.join('&');
	},

	d_o_count:0,
	showList: (resp,other_params)=>{
		let list_html=resp[1].list;
		ManageOffer.d_o_count=resp[1]['rec_count'];
		$("#rec_list_container").removeClass('d-none');
		$("#rec_detail_add_edit_container").addClass('d-none');
		$("#common-processing-overlay").addClass('d-none');
		$("#userlistbox").html(list_html);
		if(resp[1].tot_rec_cnt>0)
			$('#heading_rec_cnt').text((resp[1]['rec_count']==resp[1]['tot_rec_cnt'])?`${resp[1]['tot_rec_cnt']}`:`${resp[1]['rec_count'] || 0} of ${resp[1]['tot_rec_cnt']}`);
		else
			$('#heading_rec_cnt').text('0');
		$("#add-record-button").removeClass('d-none');
		$("#refresh-list-button").removeClass('d-none');
		$(".back-to-list-button").addClass('d-none').attr('href',"manage-offers.php#"+ManageOffer.curr_page_hash);
		$("#edit-record-button").addClass('d-none');
		ManageOffer.pagination_data=resp[1].pagination_data;
		ManageOffer.setSortOrderIcon();
	},

	onListRefresh: (resp,other_params)=>{
		$("#common-processing-overlay").addClass('d-none');
		let list_html=resp[1].list;
		$("#userlistbox").html(list_html);
		ManageOffer.pagination_data=resp[1].pagination_data;
		ManageOffer.setSortOrderIcon();
	},

	/*setExportLink: function(show){
		const dnld_elem = $('#export_members');
		if(dnld_elem.length<=0) // the download link element does not exist, the user might not be in ADMIN role
			return;
		let url = '#';
		if(show===true){
			let params = [];
			params.push('mode=export');
			params.push('search_data='+encodeURIComponent(JSON.stringify(this.search_params)));
			params.push('sort_data='+encodeURIComponent(JSON.stringify(this.sort_params)));
			params.push('ref='+Math.random());
			url = `${window.location.origin}${window.location.pathname}?${params.join('&')}`;
			
		}
		dnld_elem.attr('href',url).toggleClass('d-none', show!==true);
	},*/

	expandFilterBox: ()=>{
		document.lead_search_form.reset();
		for(let i=0; i<ManageOffer.search_params.length; i++){
			switch(ManageOffer.search_params[i].search_on){
				case 'title': $("#search-field_title").val(ManageOffer.search_params[i].search_text[0]); break;
			}
		}
		$("#search_box").show();
		$("#apply_filter").hide();
	},

	collapseFilterBox:()=>{
		$("#search_box").hide();
		if($("#filter_status").is(':hidden')){
			$("#apply_filter").show();
			$("#filter_status").hide();
		}else{
			$("#filter_status").show();
			$("#apply_filter").hide();
		}
		return false;
	},

	/*onDateFilterChange: elem=>{
		let date_filter_type=$(elem).val();
		if(date_filter_type=='EQUAL'){
			$("#end_date_container").hide();
			$("#enddate,#startdate").val('');
			$("#start_date_container").show();
		}else if(date_filter_type=='BETWEEN'){
			$("#end_date_container").show();
			$("#enddate").val('');
			$("#startdate").val('')
			$("#start_date_container").show();
		}else{
			$("#enddate").val('');
			$("#startdate").val('')
			$("#end_date_container").hide();
			$("#start_date_container").hide();
		}
	},*/

	resetSearchParamsObj: ()=>{
		ManageOffer.search_params=[];
	},

	setSearchParams: obj=>{
		ManageOffer.search_params.push(obj);
	},

	removeFilter: e =>{
		e.stopPropagation();
		let el = $(e.currentTarget);
		let f = el.data('fld');
		el.parent('.searched_elem').remove();
		/*if(remove_all==='validity_period'){
			$("#search-field_validity_period_start_picker").datepicker('setDate', null);
			$("#search-field_validity_period_start_picker").datepicker('option', 'maxDate', null);
			$("#search-field_validity_period_end_picker").datepicker('setDate', null);
			$("#search-field_validity_period_end_picker").datepicker('option', 'minDate', null);
		}else */if(f==='display_period'){
			$("#search-field_display_period_start_picker").datepicker('setDate', null);
			$("#search-field_display_period_start_picker").datepicker('option', 'maxDate', null);
			$("#search-field_display_period_end_picker").datepicker('setDate', null);
			$("#search-field_display_period_end_picker").datepicker('option', 'minDate', null);
		}else
			$('.panel-search .srchfld[data-fld='+f+']').val('');
		ManageOffer.search_params = ManageOffer.search_params.filter(p=>{
			return p.search_on !== f;
		});		
		ManageOffer.getList({pno:1});
		return false;
	},
	clearSearch: e =>{
		ManageOffer.resetSearchParamsObj();
		document.search_form.reset();
		$("#search-field_validity_period_start_picker").datepicker('setDate', null);
		$("#search-field_validity_period_start_picker").datepicker('option', 'maxDate', null);
		$("#search-field_validity_period_end_picker").datepicker('setDate', null);
		$("#search-field_validity_period_end_picker").datepicker('option', 'minDate', null);
		$("#search-field_display_period_start_picker").datepicker('setDate', null);
		$("#search-field_display_period_start_picker").datepicker('option', 'maxDate', null);
		$("#search-field_display_period_end_picker").datepicker('setDate', null);
		$("#search-field_display_period_end_picker").datepicker('option', 'minDate', null);
		ManageOffer.getList({pno:1});
		return false;
	},

	doSearch: ()=>{
		ManageOffer.resetSearchParamsObj();
		let d_prd = ['',''],
			v_prd = ['',''],
			disp_text='',
			fld = '';
		$('.panel-search .srchfld').each(function(i, el){
			let val = $.trim($(el).val());
			if(!val)
				return true;
			switch($(el).data('fld')){
				// case 'display_period_start':
				// 	d_prd[0]=val;
				// 	break;
				// case 'display_period_end':
				// 	d_prd[1]=val;
				// 	break;
				// case 'validity_period_start':
				// 	v_prd[0]=val;
				// 	break;
				// case 'validity_period_end':
				// 	v_prd[1]=val;
				// 	break;
				case 'category_id':
					disp_text=$(el).find('option:selected').text();
					break;
					
			}

			ManageOffer.setSearchParams({search_on:$(el).data('fld'),search_type:$(el).data('type'),search_text:val, disp_text:disp_text});

		});
		// if(d_prd[0] || d_prd[1]){
		// 	ManageOffer.setSearchParams({search_on:'display_period',search_type:'',search_text:d_prd});
		// }
		// if(v_prd[0] || v_prd[1]){
		// 	ManageOffer.setSearchParams({search_on:'validity_period',search_type:'',search_text:v_prd});
		// }

		ManageOffer.getList({pno:1});
		return false;
	},

	changePage: ev=>{
		ev.preventDefault();
		if(!$(ev.currentTarget).parent().hasClass('disabled')){
			ManageOffer.getList({pno:$(ev.currentTarget).data('page')});
		}
	},

	sortTable: e=>{
		let sort_on=e.currentTarget.id.split('_')[1].replaceAll('-','_'),
			sort_order='ASC',
			pno = 1;

		if($(e.currentTarget).find("i:eq(0)").hasClass('fa-sort-up'))
			sort_order='DESC';
		if(ManageOffer.sort_params[0].sort_on==sort_on){
			if(ManageOffer.pagination_data.curr_page!='undefined' && ManageOffer.pagination_data.curr_page>1){
				pno = ManageOffer.pagination_data.curr_page;
			}
		}
		ManageOffer.sort_params=[];
		ManageOffer.sort_params.push({sort_on:sort_on, sort_order:sort_order});
		ManageOffer.getList({pno:pno});
	},

	setSortOrderIcon: ()=>{
		if(ManageOffer.sort_params.length>0){
			let sort_on = ManageOffer.sort_params[0].sort_on.replaceAll('_','-'),
				sort_order_class='fa-sort-up';
			if(ManageOffer.sort_params[0].sort_order=='DESC')
				sort_order_class='fa-sort-down';
			$("#colheader_"+sort_on).siblings('th.sortable').removeClass('sorted-col').find('i:eq(0)').removeClass('fa-sort-down fa-sort-up').addClass('fa-sort').end().end().addClass('sorted-col').find('i:eq(0)').removeClass('fa-sort-down fa-sort-up').addClass(sort_order_class);
		}
	},

	/*openRecordForViewing: (rec_id)=>{
		if(rec_id=='')
			return false;

		$("#record-save-button").addClass('d-none').attr('disabled', 'disabled');
		$("#common-processing-overlay").removeClass('d-none');
		ManageOffer.openRecord({
			mode:'viewrecord',
			rec_id:rec_id,
			loadingmsg:"Opening the discount offer '"+rec_id+"' for viewing...",
			lead_tab_text:'View Discount Offer Details',coming_from:''});
		 return false;

	},*/

	openRecordForEditing: rec_id=>{
		
		if(rec_id=='')
			return false;
		document.add_do_form.reset();
		$(".form-control").removeClass("error-field");
		$("#record-save-button").removeClass('d-none').attr('disabled', false);
		$("#common-processing-overlay,#msgFrm").removeClass('d-none');
		$("#record-add-cancel-button").attr('href',"manage-offers.php#"+ManageOffer.prev_page_hash);
		ManageOffer.openRecord({
			mode:'edit_record',
			rec_id:rec_id,
			lead_tab_text:"Edit Epic Offer's Details",
			coming_from:''
		});
		return false;
	},

	openRecord: options=>{
		let opts={lead_tab_text:'Discount Offer Details'};
		$.extend(true,opts,options);
		ManageOffer.dep_row_no_max=-1;
		let params={mode:"getRecordDetails",rec_id:opts.rec_id};
		common_js_funcs.callServer({
			cache:'no-cache',
			async:true,
			type:'post',
			dataType:'json',
			url:ManageOffer.ajax_data_script,
			params:params,
			successResponseHandler:ManageOffer.showDO,
			successResponseHandlerParams:{
				mode:opts.mode,
				rec_id:opts.rec_id,
				coming_from:opts.coming_from,
				header_bar_text:opts.lead_tab_text
			}
		});
	},

	showDO: (resp,other_params)=>{
		let container_id='';
		$("#common-processing-overlay").addClass('d-none');
		const rec_id= resp[1].record_details.id ??''; // ad_banners table's id
		
		if(other_params.mode=='edit_record'){
			let coming_from=other_params.coming_from;
			if(rec_id!=''){
				if(resp[1].can_edit===false){
					// User is not authorised to edit this record so send him back to the previous screen
					location.hash=ManageOffer.prev_page_hash;
					return;
				}
				ManageOffer.removeEditRestrictions();
								
				let contobj=$("#rec_detail_add_edit_container");
				$('.alert-danger').addClass('d-none').find('.alert-message').html('');
				$('#msgFrm').removeClass('d-none');
				contobj.find(".form-actions").removeClass('d-none');
				contobj.find("form[name=add_do_form]:eq(0)").data('mode','edit-rec').find('input[name=status]').attr('checked',false).end().get(0).reset();
				contobj.find("#add_edit_mode").val('update');
				contobj.find("#add_edit_rec_id").val(rec_id);
				contobj.find("#add_form_field_categoryid").val(resp[1].record_details.category_id ?? '');
				contobj.find("#add_form_field_title").val(resp[1].record_details.title ?? '');
				contobj.find("#add_form_field_description").val(resp[1].record_details.description ?? '');
				// contobj.find("#add_form_field_company").val(resp[1].record_details.company ?? '');
				contobj.find("#add_form_field_offer_url").val(resp[1].record_details.offer_url??'');
				// contobj.find("#add_form_field_address").val(resp[1].record_details.address??'');
				// contobj.find("#add_form_field_dsk_img, #add_form_field_mob_img").val('');
				contobj.find("#add_form_field_mob_img,#add_form_field_mou").val('');

				// contobj.find('#desktop_image_container').toggleClass('has-existing-file', !!resp[1].record_details.dsk_img_url).find('.remove-file-input').val('0');
				contobj.find('#mobile_image_container').toggleClass('has-existing-file', !!resp[1].record_details.mob_img_url).find('.remove-file-input').val('0');
				contobj.find('#mou_file_container').toggleClass('has-existing-file', !!resp[1].record_details.mou_url).find('.remove-file-input').val('0');
				// contobj.find('#dsk_d_o_img').prop('src', resp[1].record_details.dsk_img_url??'')
				contobj.find('#mob_d_o_img').prop('src', resp[1].record_details.mob_img_url??'')
				contobj.find('#mou_file').prop('href', resp[1].record_details.mou_url??'').text(resp[1].record_details.mou_org_file_name??'MOU Document')
				// contobj.find("#add_form_field_display_start_date_picker").datepicker('setDate', new Date(resp[1].record_details.display_start_date));
				let m = new Date();
				let vu_dt=new Date(resp[1].record_details.valid_upto || ManageOffer.max_valid_upto );
				// let ds_dt=new Date(resp[1].record_details.display_start_date);
				// let de_dt=new Date(resp[1].record_details.display_end_date);
				// contobj.find("#add_form_field_display_start_date_picker").datepicker('option', 'minDate', m>ds_dt?ds_dt:m);
				// contobj.find("#add_form_field_display_start_date_picker").datepicker('option', 'maxDate', null);
				// contobj.find("#add_form_field_display_start_date_picker").datepicker('setDate', ds_dt);
				// contobj.find("#add_form_field_display_end_date_picker").datepicker('option', 'minDate', m>ds_dt?ds_dt:m);
				// contobj.find("#add_form_field_display_end_date_picker").datepicker('setDate', de_dt);
				// contobj.find("#add_form_field_valid_upto_picker").datepicker('option', 'minDate', m>ds_dt?ds_dt:m);
				contobj.find("#add_form_field_valid_upto_picker").datepicker('setDate', vu_dt);
				contobj.find("input[name=status]").attr('checked', false);
				contobj.find('.clear-file-input').addClass('d-none');
				if(resp[1].record_details.active)
					contobj.find("#add_form_field_status_"+resp[1].record_details.active).attr('checked', true);

				let header_text = 'Edit Epic Offer';
				
				contobj.find("#record-add-cancel-button").data('back-to',coming_from);
				contobj.find("#record-save-button>span:eq(0)").html('Save Changes');
				contobj.find("#panel-heading-text").text(header_text);
				contobj.find("#infoMsg").html('Edit Epic Offer <b>' + resp[1].record_details.title +  '</b>');
				ManageOffer.setheaderBarText(header_text);
				ManageOffer.applyEditRestrictions(resp[1].edit_restricted_fields);
				container_id='rec_detail_add_edit_container';
			}else{

				let message="Sorry, the edit window could not be opened (Server error).";
				if(resp[0]==1){
					message="Sorry, the edit window could not be opened (User ID missing).";
				}else if(resp[0]==2){
					message="Sorry, the edit window could not be opened (Server error).";
				}else if(resp[0]==3){
					message="Sorry, the edit window could not be opened (Invalid user ID).";
				}
				alert(message);
				location.hash=ManageOffer.prev_page_hash;
				return;
			}
		}

		if(container_id!=''){
			$(".back-to-list-button").removeClass('d-none');
			$("#refresh-list-button, #add-record-button, #rec_list_container").addClass('d-none');
			if(container_id!='rec_detail_add_edit_container'){
				$("#rec_detail_add_edit_container").addClass('d-none');
				$("#edit-record-button").removeClass('d-none').data('rec_id',other_params.rec_id);
			}else if(container_id!='user_detail_view_container'){
				$("#user_detail_view_container").addClass('d-none');
				$("#edit-record-button").addClass('d-none');
			}
			$("#"+container_id).removeClass('d-none');
			ManageOffer.setheaderBarText(other_params.header_bar_text);
		}
		$("#add_form_field_title").focus();
	},

	applyEditRestrictions: restricted_fields=>{
		const contobj=$("#rec_detail_add_edit_container");
		restricted_fields.forEach(fld=>{
			switch(fld){
				case 'name':
					contobj.find("#add_form_field_name").prop('disabled', restricted_fields.includes('name')).addClass('rstrctedt');
					break;
				case 'active':
					contobj.find("input[name=active]").prop('disabled', restricted_fields.includes('active')).addClass('rstrctedt');
					break;
				case 'dsk_img':
					contobj.find("#add_form_field_dsk_img").prop('disabled', restricted_fields.includes('dsk_img')).addClass('rstrctedt');
					break;
			}
		});
	},

	removeEditRestrictions: ()=>{
		$("#rec_detail_add_edit_container").find("#add_form_field_name, input[name=active], #add_form_field_dsk_img").prop('disabled', false).end()
			.find('.rstrctedt').removeClass('rstrctedt');
	},
	
	backToList: e=>{
		// if(typeof e=='object' && e.hasOwnProperty('data') && e.data.hasOwnProperty('self')){
			// var self=e.data.self;
		// }else{
			// 
		// }
		// $("#back-to-list-button").addClass('d-none');
		// $("#refresh-list-button").removeClass('d-none');
		// $("#add-record-button").removeClass('d-none');
		// $("#edit-record-button").addClass('d-none');
		// $("#rec_list_container").removeClass('d-none');
		// $("#user_detail_view_container").addClass('d-none');
		// $("#rec_detail_add_edit_container").addClass('d-none');
		// ManageOffer.setheaderBarText("Users List");
	},

	refreshList:e=>{
		ManageOffer.getList({pno:ManageOffer.pagination_data.curr_page,successResponseHandler:ManageOffer.onListRefresh});
		return false;
	},

	handleAddDOResponse: resp=>{
		$(".form-control").removeClass("error-field");
		let message_container = '.alert-danger';
		if(resp.error_code==0){
			message_container = '.alert-success';
			$("#record-add-cancel-button>i:eq(0)").next('span').html('Close');
			$("form[name=add_do_form]").find(".error-field").removeClass('error-field').end().get(0).reset();
			$("#add_form_field_status_y").prop('checked',true);
			// $('#dsk_d_o_img,#mob_d_o_img').attr('src',"");
			// $('#add_form_field_dsk_img,#add_form_field_mob_img').val('');
			$('#mob_d_o_img').attr('src',"");
			$('#add_form_field_mob_img').val('');
			$('#mou_file').attr('href',"#");
			$('#add_form_field_mou').val('');
			
			// $("#add_form_field_display_start_date_picker").datepicker('setDate', null);
			// $("#add_form_field_display_end-date_picker").datepicker('setDate', null);
			$("#add_form_field_valid_upto_picker").datepicker('setDate', ManageOffer.max_valid_upto);
			$("#add_form_field_title").focus();
			document.querySelector('.main-content').scrollIntoView(true);
		}else if(resp.error_code==2){
			if(resp.error_fields.length>0){
				// alert(resp.message);
				$(resp.error_fields[0]).focus();
				$(resp.error_fields[0]).addClass("error-field");
			}
		}

		$('#record-save-button, #record-add-cancel-button').removeClass('disabled').attr('disabled',false);
		$("#common-processing-overlay").addClass('d-none');

		if(message_container!=''){
			$(message_container).removeClass('d-none').siblings('.alert').addClass('d-none').end().find('.alert-message').html(resp.message);
			common_js_funcs.scrollTo($('.main-container-inner'));
			$('#msgFrm').addClass('d-none');
		}
	},

	handleUpdateDOResponse: resp=>{
		let mode_container='rec_detail_add_edit_container';
		$(".form-control").removeClass("error-field");
		let message_container = '.alert-danger';
		if(resp.error_code==0){
			message_container = '.alert-success';
			// $('#dsk_d_o_img').prop('src', resp.other_data.dsk_img_url??'').closest('.existing-file-view').removeClass('remove-file').toggleClass('has-existing-file', !!resp.other_data.dsk_img_url).end();
			$('#mob_d_o_img').prop('src', resp.other_data.mob_img_url??'').closest('.existing-file-view').removeClass('remove-file').toggleClass('has-existing-file', !!resp.other_data.mob_img_url).end();
			$('#mou_file').prop('href', resp.other_data.mou_url??'').text(resp.other_data.mou_org_file_name??'MOU Document').closest('.existing-file-view').removeClass('remove-file').toggleClass('has-existing-file', !!resp.other_data.mou_url).end();
			// $('#add_form_field_dsk_img, #add_form_field_mob_img').val('');
			$('#add_form_field_mou, #add_form_field_mob_img').val('');
			$(".clear-file-input").addClass('d-none');
			$("#add_form_field_title").focus();
		}else if(resp.error_code==2){
			// message_container ='';
			if(resp.error_fields.length>0){
				// alert(resp.message);
				setTimeout(()=>{$(resp.error_fields[0]).addClass("error-field").focus(); },0);
			}
		}

		$('#record-save-button, #record-add-cancel-button').removeClass('disabled').attr('disabled',false);
		$("#common-processing-overlay").addClass('d-none');
		if(message_container!=''){
			$(message_container).removeClass('d-none').siblings('.alert').addClass('d-none').end().find('.alert-message').html(resp.message);//.end().delay(3000).fadeOut(800,function(){$(this).css('display','').addClass('d-none');});
			common_js_funcs.scrollTo($('.main-container-inner'));
			$('#msgFrm').addClass('d-none');
		}
	},

	saveDODetails:form_elem=>{
		$('.alert').addClass('d-none');
		let data_mode=$(form_elem).data('mode'),
			res = ManageOffer.validateDODetails({mode:data_mode});
		if(res.error_fields.length>0){
			alert(res.errors[0]);
			setTimeout(()=>{
				$(res.error_fields[0],'#add_do_form').focus();
				common_js_funcs.scrollTo($(res.error_fields[0],'#add_do_form'), {duration:'fast'});
			},0);
			return false;
		}
		$("#common-processing-overlay").removeClass('d-none');
		$('#record-save-button, #record-add-cancel-button').addClass('disabled').attr('disabled',true);
		$('#rec_detail_add_edit_container .error-field').removeClass('error-field');
		return true;
	},

	validateDODetails:opts=>{
		let errors = [], error_fields=[], mode='add-do';
		$(".form-control").removeClass("error-field");
		if(typeof opts=='object' && opts.hasOwnProperty('mode'))
			mode=opts.mode;

		const frm = $('#add_do_form');
			// address=$.trim(frm.find('#add_form_field_address').val()),
			// company=$.trim(frm.find('#add_form_field_company').val()),
	 		// display_start_date=$.trim(frm.find('#add_form_field_display_start_date').val()),
	 		// display_end_date=$.trim(frm.find('#add_form_field_display_end_date').val()),
			// dsk_img_url = frm.find('#dsk_d_o_img').attr('src') || '',
			// dsk_img = frm.find('#add_form_field_dsk_img').val() || '',
			// mob_img_url = frm.find('#mob_d_o_img').attr('src') || '',
			// mob_img = frm.find('#add_form_field_mob_img').val() || '';
		let category=$.trim(frm.find('#add_form_field_categoryid').val()),
			title=$.trim(frm.find('#add_form_field_title').val()),
	 		description=$.trim(frm.find('#add_form_field_description').val()),
			offer_url=$.trim(frm.find('#add_form_field_offer_url').val()),
	 		valid_upto=$.trim(frm.find('#add_form_field_valid_upto').val()),
	 		active =frm.find('input[name=active]:checked').val();
			

		if(!frm.find('#add_form_field_categoryid').hasClass('rstrctedt') && category == ''){
			errors.push('A category is required for the epic offer.');
			error_fields.push('#add_form_field_categoryid');
			$("#add_form_field_categoryid").addClass("error-field");
		}else if(!frm.find('#add_form_field_title').hasClass('rstrctedt') && title == ''){
			errors.push('A title is required for the epic offer.');
			error_fields.push('#add_form_field_title');
			$("#add_form_field_title").addClass("error-field");
		}/*else if(!frm.find('#add_form_field_title').hasClass('rstrctedt') && !ManageOffer.title_pattern.test(title)){
			errors.push('Title contains invalid characters.');
			error_fields.push('#add_form_field_title');
			$("#add_form_field_title").addClass("error-field");
		}*//*else if(!frm.find('#add_form_field_company').hasClass('rstrctedt') && company==''){
			errors.push('Company is required.');
			error_fields.push('#add_form_field_company');
			$("#add_form_field_company").addClass("error-field");
		}*/else if(!frm.find('#add_form_field_description').hasClass('rstrctedt') && description==''){
			errors.push('Epic offer\'s description is required.');
			error_fields.push('#add_form_field_description');
			$("#add_form_field_description").addClass("error-field");
		}else if(!frm.find('#add_form_field_offer_url').hasClass('rstrctedt') && offer_url!='' && !/^https?:\/\//i.test(offer_url)){
			errors.push('Please provide a valid offer url starting with http:// or https://');
			error_fields.push('#add_form_field_offer_url');
			$("#add_form_field_offer_url").addClass("error-field");
		
		}/*else if(!frm.find('#add_form_field_display_start_date_picker').hasClass('rstrctedt') && display_start_date==''){
			errors.push('The display start date is required.');
			error_fields.push('#add_form_field_display_start_date_picker');
			$("#add_form_field_display_start_date_picker").addClass("error-field");
		}else if(!frm.find('#add_form_field_display_end_date_picker').hasClass('rstrctedt') && display_end_date==''){
			errors.push('The display end date is required.');
			error_fields.push('#add_form_field_display_end_date_picker');
			$("#add_form_field_display_end_date_picker").addClass("error-field");
		}*/else if(!frm.find('#add_form_field_valid_upto_picker').hasClass('rstrctedt') && valid_upto==''){
			errors.push('The valid upto date is required.');
			error_fields.push('#add_form_field_valid_upto_picker');
			$("#add_form_field_valid_upto_picker").addClass("error-field");
		}
		return {'errors': errors, 'error_fields': error_fields};
	},

	openAddDOForm: e=>{
		document.add_do_form.reset();
		ManageOffer.removeEditRestrictions();

		ManageOffer.dep_row_no_max=-1;
		$(".form-control").removeClass("error-field");
		$("#refresh-list-button").addClass('d-none');
		$("#add-record-button").addClass('d-none');
		$("#edit-record-button").addClass('d-none');
		$("#rec_list_container").addClass('d-none');
		$("#rec_detail_add_edit_container").removeClass('d-none').find("#panel-heading-text").text('Add Epic Offer').end();
		$('#msgFrm').removeClass('d-none');
			
		$(".back-to-list-button").removeClass('d-none');
		
		$("#rec_detail_add_edit_container").find("#record-save-button>span:eq(0)").html('Add Epic Offer').end().find("#add_edit_mode").val('create').end().find("#add_edit_rec_id").val('').end().find("#record-add-cancel-button").data('back-to','').attr('href',"manage-offers.php#"+ManageOffer.prev_page_hash);
		$("form[name=add_do_form]").data('mode','add-rec').find(".error-field").removeClass('error-field').end().find('input[name=active]').attr('checked',false).end().get(0).reset();

		$("#add_form_field_status_n").prop('checked',false);
		$("#add_form_field_status_y").prop('checked',true);

		// $('#dsk_d_o_img,#mob_d_o_img').attr('src',""); 
		// $('#add_form_field_dsk_img,#add_form_field_mob_img').val(''); // empty the mob image file input
		// $('#desktop_image_container,#mobile_image_container').removeClass('has-existing-file remove-file')
		$('.clear-file-input').addClass('d-none');
		$('#mob_d_o_img').attr('src',""); 
		$('#add_form_field_mob_img').val('');
		$('#mobile_image_container').removeClass('has-existing-file remove-file');

		$('#mou_file').attr('href',""); 
		$('#add_form_field_mou').val('');
		$('#mou_file_container').removeClass('has-existing-file remove-file');

		
		// $("#add_form_field_display_start_date_picker").datepicker('setDate', null);
		// $("#add_form_field_display_start_date_picker").datepicker('option', 'minDate', '-0d');
		// $("#add_form_field_display_start_date_picker").datepicker('option', 'maxDate', null);
		// $("#add_form_field_display_end_date_picker").datepicker('setDate', null);
		// $("#add_form_field_display_end_date_picker").datepicker('option', 'minDate', '-0d');
		// $("#add_form_field_display_end_date_picker").datepicker('option', 'maxDate', null);
		$("#add_form_field_valid_upto_picker").datepicker('setDate', new Date(ManageOffer.max_valid_upto));
		$("#add_form_field_valid_upto_picker").datepicker('option', 'minDate', '-0d');
		$("#add_form_field_valid_upto_picker").datepicker('option', 'maxDate', null);
		ManageOffer.setheaderBarText("");
		$('#add_form_field_title').focus();
		document.querySelector('.main-content').scrollIntoView(true);
		return false;
	},

	/*deleteDO: ev=>{
		var elem = $(ev.currentTarget);
		var id =elem.data('rec_id');
		// alert(id);
		if(confirm('Do you want to delete this user?')){

			var rec_details = {};
			common_js_funcs.callServer({cache:'no-cache',async:false,dataType:'json',type:'post',url:ManageOffer.ajax_data_script,params:{mode:'deleteUser', user_id:id},
				successResponseHandler:function(resp,status,xhrobj){
					if(resp.error_code == 0)
						ManageOffer.handleDeleteResp(resp);
					else
						alert(resp.message);
				},
				successResponseHandlerParams:{}});
			return rec_details;
		}

	},*/
	handleDeleteResp:resp=>{
		// console.log(resp);return false;
		alert(resp.message);
		ManageOffer.refreshList();
	},

	closeAddDOForm:()=>{
		return true;
	},

	setheaderBarText: text=>{
		$("#header-bar-text").find(":first-child").html(text);
	},
	
	onHashChange: e=>{
		let hash=location.hash.replace(/^#/,'');
		if(ManageOffer.curr_page_hash!=ManageOffer.prev_page_hash){
			ManageOffer.prev_page_hash=ManageOffer.curr_page_hash;
		}
		ManageOffer.curr_page_hash=hash;

		let hash_params={mode:''};
		if(hash!=''){
			let hash_params_temp=hash.split('&');
			let hash_params_count= hash_params_temp.length;
			for(let i=0; i<hash_params_count; i++){
				let temp=hash_params_temp[i].split('=');
				hash_params[temp[0]]=decodeURIComponent(temp[1]);
			}
		}

		switch(hash_params.mode.toLowerCase()){
			case 'add':
				$('.alert-success, .alert-danger').addClass('d-none');
				$('#msgFrm').removeClass('d-none');
				ManageOffer.openAddDOForm();
				break;
			case 'edit':
				$('.alert-success, .alert-danger').addClass('d-none');
				$('#msgFrm').removeClass('d-none');
				if(hash_params.hasOwnProperty('rec_id') && hash_params.rec_id!=''){
					ManageOffer.openRecordForEditing(hash_params.rec_id);
				}else{
					location.hash=ManageOffer.prev_page_hash;
				}
				break;
			default:
				$('.alert-success, .alert-danger').addClass('d-none');
				$('#msgFrm').removeClass('d-none');
				let params={mode:'getList',pno:1, search_data:"[]", sort_data:JSON.stringify(ManageOffer.sort_params), list_format:'html'};
				if(hash_params.hasOwnProperty('pno')){
					params['pno']=hash_params.pno
				}
				if(hash_params.hasOwnProperty('search_data')){
					params['search_data']=hash_params.search_data;
				}
				if(hash_params.hasOwnProperty('sort_data')){
					params['sort_data']=hash_params.sort_data;
				}

				ManageOffer.search_params=JSON.parse(params['search_data']);
				ManageOffer.sort_params=JSON.parse(params['sort_data']);

				if(ManageOffer.sort_params.length==0){
					ManageOffer.sort_params.push(ManageOffer.default_sort);
					params['sort_data']=JSON.stringify(ManageOffer.sort_params);
				}
				//if(ManageOffer.search_params.length==0){
				if(!hash_params.hasOwnProperty('search_data')){
					ManageOffer.setSearchParams({search_on:'active',search_type:'EQUAL',search_text:'y'});
					params['search_data']='[{"search_on":"active","search_type":"EQUAL","search_text":"y"}]';
				}
				
				if(ManageOffer.search_params.length>0){
					$.each(ManageOffer.search_params , function(idx,data) {
						switch (data.search_on) {

							case 'title':
								$("#search-field_title").val(data.search_text);
								break;
							case 'description':
								$("#search-field_description").val(data.search_text);
								break;
							case 'category_id':
								$("#search-field_categoryid").val(data.search_text);
								break;
							case 'active':
								$("#search-field_active").val(data.search_text);
								break;
							
							// case 'display_period':
							// 	if(data.search_text[0]){
							// 		$("#search-field_display_period_start_picker").datepicker('setDate', new Date(data.search_text[0]));
							// 		$("#search-field_display_period_end_picker").datepicker('option', 'minDate', $("#search-field_display_period_start_picker").datepicker('getDate'));
							// 	}
							// 	if(data.search_text[1]){
							// 		$("#search-field_display_period_end_picker").datepicker('setDate', new Date(data.search_text[1]));
							// 		$("#search-field_display_period_start_picker").datepicker('option', 'maxDate', $("#search-field_display_period_end_picker").datepicker('getDate'));
							// 	}
							// 	if(!data.search_text[0] && !data.search_text[1]){
							// 		$("#search-field_display_period_start_picker").datepicker('setDate', null);
							// 		$("#search-field_display_period_end_picker").datepicker('setDate', null);
							// 		$("#search-field_display_period_start_picker").datepicker('option', 'maxDate', null);
							// 		$("#search-field_display_period_end_picker").datepicker('option', 'minDate', null);
							// 	}
							// 	break;
						}
					});
					$("#search_field").val(ManageOffer.search_params[0]['search_on'] || '');
				}
				if(ManageOffer.search_params.length>0){
					if(ManageOffer.search_params[0]['search_on'] == 'status')
						$("#search_text").val(ManageOffer.search_params[0]['search_text'][0]=='1'?'Active':'Inactive');
					else
						$("#search_text").val(ManageOffer.search_params[0]['search_text'] || '');
					$("#search_field").val(ManageOffer.search_params[0]['search_on'] || '');
				}
				$("#common-processing-overlay").removeClass('d-none');
				common_js_funcs.callServer({cache:'no-cache',async:true,dataType:'json',type:'post', url:ManageOffer.ajax_data_script,params:params,successResponseHandler:ManageOffer.showList,successResponseHandlerParams:{}});
				let show_srch_form = false;
				if (typeof(Storage) !== "undefined") {
					srch_frm_visible = localStorage.ad_search_toggle;
				}else{
					srch_frm_visible = Cookies.get('ad_search_toggle');
				}
				if(srch_frm_visible && srch_frm_visible == 'visible')
					show_srch_form = true;
				$('.toggle-search').toggleClass('search-form-visible', show_srch_form);
				$('#search_records').closest('.panel-search').toggleClass('d-none', !show_srch_form);
				let search_form_cont = $('#search_records').closest('.panel-search');
				if(search_form_cont.hasClass('d-none'))
					$('.toggle-search').prop('title','Open search panel');
				else{
					$('.toggle-search').prop('title','Close search panel');
					//$("#search-field_title").focus();
				}
				break;
		}
	}
}