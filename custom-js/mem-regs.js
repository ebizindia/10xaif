var memregfuncs={
	searchparams:[],  /* [{searchon:'',searchtype:'',searchtext:''},{},..] */
	sortparams:[],  /* [{sorton:'',sortorder:''},{},..] */
	default_sort:{sorton:'reg_on',sortorder:'DESC'},
	paginationdata:{},
	defaultleadtabtext:'Registrations List',
	filtersapplied:[],
	statuschangestarted:0,
	ajax_data_script:'mem-regs.php',
	curr_page_hash:'',
	prev_page_hash:'',
	// email_pattern:new RegExp("^\\w+([.']?-*\\w+)*@\\w+([.-]?\\w+)*(\\.\\w{2,4})+$","i"),
	email_pattern:/^([^\@;:"'#()=,&]+|"[^\;:"',&]+")@(\w+([\.-]?\w+)*(\.\w{2,})+|[\[]\d{0,3}(\.\d{0,3}){3}[\]])$/,
	mobile_pattern: /^[+]?\d{10,15}$/,
	memno_pattern: /^[A-Z0-9][A-Z0-9-]*$/i,
	name_pattern: /^[A-Z -]+$/i,
	batchno_pattern: /^\d{1,4}$/,
	batch_no_min:1940, // will be feeded from the config file
	batch_no_max:2024, // will be feeded from the config file
	salutaions:[],
	user_roles:[],
	all_user_roles:[],
	deps_list:[],
	user_levels:{},
	dep_rowno_max:-1,
	pp_max_filesize:0,
	default_list:true,

	initiateStatusChange:function(statuscell){
		var self=memregfuncs;

		var currtext=$(statuscell).find(':nth-child(1)').html();
		if($(statuscell).find(':nth-child(1)').hasClass('status-live')){
			var temptext='Deactivate';
			var color='#ff3333'; // red
		}else{
			var temptext='Activate';
			var color='#00a650'; // green
		}

		$(statuscell).find(':nth-child(1)').html(temptext);
		$(statuscell).find(':nth-child(1)').css('color',color);


	},
	toggleSearch: function(ev){
		var elem = $(ev.currentTarget);
		elem.toggleClass('search-form-visible', !elem.hasClass('search-form-visible'));
		$('#search_records').closest('.panel-search').toggleClass('d-none', !elem.hasClass('search-form-visible'));
		var search_form_cont = $('#search_records').closest('.panel-search');
		if(search_form_cont.hasClass('d-none'))
			elem.prop('title','Open search panel');
		else{
			elem.prop('title','Close search panel');
			$("#search-field_fullname").focus();
		}
		if (typeof(Storage) !== "undefined") {
			localStorage.memreg_search_toggle = elem.hasClass('search-form-visible') ? 'visible' : '';
		} else {
			Cookies.set('memreg_search_toggle', elem.hasClass('search-form-visible') ? 'visible' : '', {path : '/'/*, secure: true*/});
		}
	},

	confirmAndExecuteStatusChange:function(statuscell){
		var self=memregfuncs;

		self.statuschangestarted=1;
		var text=$(statuscell).find(':nth-child(1)').html();
		if($(statuscell).find(':nth-child(1)').hasClass('status-live')){
			var newstatus=0;
			var newstatustext='deactivate';
		}else{
			var newstatus=1;
			var newstatustext='activate';
		}

		var rowelem=$(statuscell).parent();
		var rowid=rowelem.attr('id');
		var temp=rowid.split('_');
		var userid=temp[temp.length-1];

		var fullname=rowelem.find('td:eq(1)').html();
		if(confirm("Really "+newstatustext+" the user \""+fullname+"\"?")){
			var options={cache:'no-cache',dataType:'json',async:true,type:'post',url:memregfuncs.ajax_data_script+"?mode=changeStatus",data:"newstatus="+newstatus+"&recordid="+userid,successResponseHandler:memregfuncs.handleStatusChangeResponse,successResponseHandlerParams:{statuscell:statuscell,rowelem:rowelem}};
			common_js_funcs.callServer(options);
			$(statuscell).removeClass("status-grn");
			$(statuscell).removeClass("status-red");
			if(parseInt(newstatus)==1){
				$(statuscell).addClass("status-grn");
			}else{
				$(statuscell).addClass("status-red");
			}
		}else{
			memregfuncs.statuschangestarted=0;
			memregfuncs.abortStatusChange(statuscell);

		}
		/*bootbox.dialog({
				animate:false,
				message: "Really "+newstatustext+" the user \""+fullname+"\"?",
				closeButton: false,
				onEscape:function(){return  false;},
				buttons:{
					"No": 	{
						"label": "No",
						"callback":function(ev){
							memregfuncs.statuschangestarted=0;
							memregfuncs.abortStatusChange(statuscell);
						}
					},
					"Yes":	{
						"label": "Yes",
						"className": "btn-danger btn-primary",
						"callback": function(ev){

							var options={cache:'no-cache',dataType:'json',async:true,type:'post',url:memregfuncs.ajax_data_script+"?mode=changeStatus",data:"newstatus="+newstatus+"&recordid="+userid,successResponseHandler:memregfuncs.handleStatusChangeResponse,successResponseHandlerParams:{statuscell:statuscell,rowelem:rowelem}};
							common_js_funcs.callServer(options);
						}
					}

				}

		});*/




	},

	abortStatusChange:function(statuscell){
		var self=memregfuncs;

		if(self.statuschangestarted==0){
			$(statuscell).find(':nth-child(1)').css('color','');
			if($(statuscell).find(':nth-child(1)').hasClass('status-live')){
				var temptext='Active';

			}else{
				var temptext='Inactive';

			}
			$(statuscell).find(':nth-child(1)').html(temptext);
		}
	},


	handleStatusChangeResponse:function(resp,otherparams){
		var self=memregfuncs;

		self.statuschangestarted=0;
		if(resp.errorcode!=0){

			self.abortStatusChange(otherparams.statuscell);
			if(resp.errorcode == 5)
				alert(resp.errormsg)
			else
				alert("Sorry, the status could not be updated.");

		}else{
			if($(otherparams.statuscell).find(':nth-child(1)').hasClass('status-live')){
				$(otherparams.statuscell).find(':nth-child(1)').removeClass('status-live').addClass("status-notlive");
			}else{
				$(otherparams.statuscell).find(':nth-child(1)').removeClass('status-notlive').addClass("status-live");
			}
			otherparams.rowelem.toggleClass('inactiverow');
			self.abortStatusChange(otherparams.statuscell);
		}

	},

	getList:function(options){
		var self=this;
		var pno=1;
		var params=[];
		if('pno' in options){
			params.push('pno='+encodeURIComponent(options.pno));
		}else{
			params.push('pno=1');
		}

		params.push('searchdata='+encodeURIComponent(JSON.stringify(self.searchparams)));
		params.push('sortdata='+encodeURIComponent(JSON.stringify(self.sortparams)));

		params.push('ref='+Math.random());

		$("#common-processing-overlay").removeClass('d-none');

		location.hash=params.join('&');


	},


	user_count:0,
	showList:function(resp,otherparams){
		//console.log(resp);
		var self=memregfuncs;
		var listhtml=resp[1].list;
		self.user_count=resp[1]['reccount'];
		$("#user_list_container").removeClass('d-none');
		$("#user_detail_view_container").addClass('d-none');
		$("#user_detail_add_edit_container").addClass('d-none');
		$("#common-processing-overlay").addClass('d-none');
		// $('#search_field').select2({minimumResultsForSearch: -1});
		$("#userlistbox").html(listhtml);
		
		if(resp[1].tot_rec_cnt>0){
			$('#heading_rec_cnt').text((resp[1]['reccount']==resp[1]['tot_rec_cnt'])?`(${resp[1]['tot_rec_cnt']})`:`(${resp[1]['reccount'] || 0} of ${resp[1]['tot_rec_cnt']})`);
			// memregfuncs.setExportLink(resp[1]['reccount']>0?true:false);
			memregfuncs.setExportLink(false); // Apparently export is not needed
		}else{
			$('#heading_rec_cnt').text('(0)');
			memregfuncs.setExportLink(false);
		}

		$("#add-record-button").removeClass('d-none');
		$("#refresh-list-button").removeClass('d-none');
		$(".back-to-list-button").addClass('d-none').attr('href',"mem-regs.php#"+memregfuncs.curr_page_hash);
		$("#edit-record-button").addClass('d-none');
		self.paginationdata=resp[1].paginationdata;

		self.setSortOrderIcon();


	},


	onListRefresh:function(resp,otherparams){
		var self=memregfuncs;
		$("#common-processing-overlay").addClass('d-none');
		var listhtml=resp[1].list;
		$("#userlistbox").html(listhtml);
		self.paginationdata=resp[1].paginationdata;
		self.setSortOrderIcon();
	},

	setExportLink: function(show){
		const dnld_elem = $('#export_members');
		if(dnld_elem.length<=0) // the download link element does not exist, the user might not be in ADMIN role
			return;
		let url = '#';
		if(show===true){
			let params = [];
			params.push('mode=export');
			params.push('searchdata='+encodeURIComponent(JSON.stringify(this.searchparams)));
			params.push('sortdata='+encodeURIComponent(JSON.stringify(this.sortparams)));
			params.push('ref='+Math.random());
			url = `${window.location.origin}${window.location.pathname}?${params.join('&')}`;
			
		}
		dnld_elem.attr('href',url).toggleClass('d-none', show!==true);
	},


	expandFilterBox:function(){
		var self=memregfuncs;
		document.leadsearchform.reset();
		for(var i=0; i<self.searchparams.length; i++){
			switch(self.searchparams[i].searchon){
				case 'name': $("#fullname").val(self.searchparams[i].searchtext[0]); break;

				case 'email': $("#email").val(self.searchparams[i].searchtext[0]); break;

				case 'usertype': $("#usertype").val(self.searchparams[i].searchtext[0]); break;


			}

		}
		$("#searchbox").show();
		$("#applyfilter").hide();

	},


	collapseFilterBox:function(){
		var self=memregfuncs;
		$("#searchbox").hide();
		if($("#filterstatus").is(':hidden')){
			$("#applyfilter").show();
			$("#filterstatus").hide();
		}else{
			$("#filterstatus").show();
			$("#applyfilter").hide();

		}
		return false;
	},

	onDateFilterChange:function(elem){
		var date_filtertype=$(elem).val();
		if(date_filtertype=='EQUAL'){
			$("#enddateboxcont").hide();
			$("#enddate").val('');
			$("#startdate").val('')
			$("#startdateboxcont").show();

		}else if(date_filtertype=='BETWEEN'){
			$("#enddateboxcont").show();
			$("#enddate").val('');
			$("#startdate").val('')
			$("#startdateboxcont").show();
		}else{
			$("#enddate").val('');
			$("#startdate").val('')
			$("#enddateboxcont").hide();
			$("#startdateboxcont").hide();
		}

	},


	resetSearchParamsObj:function(){
		var self=memregfuncs;
		self.searchparams=[];
	},

	setSearchParams:function(obj){
		var self=memregfuncs;
		self.searchparams.push(obj);

	},

	clearSearch:function(e){
		let remove_all = true;
		if(e){
			e.stopPropagation();
			elem = e.currentTarget;
			if($(elem).hasClass('remove_filter')){
				remove_all = $(elem).data('fld');
				$(elem).parent('.searched_elem').remove();
				$('.panel-search .srchfld[data-fld='+remove_all+']').val('');
			}
		}

		var self=memregfuncs;
		if(remove_all===true){
			self.resetSearchParamsObj();
			document.search_form.reset();
		}else{
			self.searchparams = self.searchparams.filter(fltr=>{
				return fltr.searchon !== remove_all;
			});
		}
		var options={pno:1};
		self.getList(options);
		return false;
	},


	doSearch:function(){

		memregfuncs.resetSearchParamsObj();

		$('.panel-search .srchfld').each(function(i, el){
			let val = $.trim($(el).val());
			if(val!=''){
				fld = $(el).data('fld');
				memregfuncs.setSearchParams({searchon:$(el).data('fld'),searchtype:$(el).data('type'),searchtext:val});
			}
		});

		if(memregfuncs.searchparams.length<=0)
			return false;

		let options={pno:1};
		memregfuncs.getList(options);
		return false;
	},


	changePage:function(ev){
		ev.preventDefault();
		if(!$(ev.currentTarget).parent().hasClass('disabled')){
			var self=memregfuncs;
			var pno=$(ev.currentTarget).data('page');
			self.getList({pno:pno});
			// return false;
		}

	},



	sortTable:function(e){
		var self=e.data.self;

		var elemid=e.currentTarget.id;
		var elemidparts=elemid.split('_');
		var sorton=elemidparts[1];
		var sortorder='ASC';

		if(sorton == 'regon')
			sorton = 'reg_on';

		if($(e.currentTarget).find("i:eq(0)").hasClass('fa-sort-up')){
			sortorder='DESC';
		}

		var pno = 1;
		// if(self.sortparams[0].sorton==sorton){
		// 	if(self.paginationdata.curr_page!='undefined' && self.paginationdata.curr_page>1){
		// 		pno = self.paginationdata.curr_page;
		// 	}
		// } Page number should be reset if the sorting feature is used

		memregfuncs.sortparams=[];
		memregfuncs.sortparams.push({sorton:sorton, sortorder:sortorder});
		var options={pno:pno};
		memregfuncs.getList(options);

	},



	setSortOrderIcon:function(){
		var self=memregfuncs;
		if(self.sortparams.length>0){
			var sorton = self.sortparams[0].sorton == 'reg_on'?'regon':self.sortparams[0].sorton;
			var colheaderelemid='colheader_'+sorton;

			if(self.sortparams[0].sortorder=='DESC'){
				var sort_order_class='fa-sort-down';
			}else{
				var sort_order_class='fa-sort-up';
			}
			$("#"+colheaderelemid).siblings('th.sortable').removeClass('sorted-col').find('i:eq(0)').removeClass('fa-sort-down fa-sort-up').addClass('fa-sort').end().end().addClass('sorted-col').find('i:eq(0)').removeClass('fa-sort-down fa-sort-up').addClass(sort_order_class);


		}
	},



	openRecordForViewing:function(recordid){
		var self=memregfuncs;
		if(recordid=='')
			return false;

		$("#record-save-button").addClass('d-none').attr('disabled', 'disabled');
		$("#common-processing-overlay").removeClass('d-none');
		var coming_from='';
		var options={mode:'viewrecord',recordid:recordid,loadingmsg:"Opening the lead '"+recordid+"' for viewing...",leadtabtext:'View Member Details',coming_from:coming_from}
		self.openRecord(options);
		 return false;

	},

	openRecordForEditing:function(recordid){
		var self=memregfuncs;
		if(recordid=='')
			return false;

		document.adduserform.reset();
		$(".form-control").removeClass("error-field");
		$("#record-save-button").removeClass('d-none').attr('disabled', false);
		// $("#add_form_field_role").next('.select2-container').removeClass("error-field");
		$("#common-processing-overlay").removeClass('d-none');
		$("#record-add-cancel-button").attr('href',"mem-regs.php#"+memregfuncs.prev_page_hash);
		$('#msgFrm').removeClass('d-none');
		var coming_from='';//elem.data('in-mode');
		var options={mode:'editrecord',recordid:recordid,leadtabtext:'Edit Member\'s Details',coming_from:coming_from}
		self.openRecord(options);
		return false;

	},


	openRecord:function(options){
		var self=memregfuncs;
		var opts={leadtabtext:'Lead Details'};
		$.extend(true,opts,options);

		memregfuncs.dep_rowno_max=-1;

		var params={mode:"getRecordDetails",recordid:opts.recordid};
		var options={cache:'no-cache',async:true,type:'post',dataType:'json',url:self.ajax_data_script,params:params,successResponseHandler:self.showLeadDetailsWindow,successResponseHandlerParams:{self:self,mode:opts.mode,recordid:opts.recordid,coming_from:opts.coming_from,header_bar_text:opts.leadtabtext}};
		common_js_funcs.callServer(options);

	},


	showLeadDetailsWindow:function(resp,otherparams){
		const self=otherparams.self;
		let container_id='';
		$("#common-processing-overlay").addClass('d-none');
		const user_id= resp[1].record_details.id ??''; // member table's id
		const login_acnt_id = resp[1].record_details.user_acnt_id ??''; // users table's id

		if(otherparams.mode=='editrecord'){
			var coming_from=otherparams.coming_from;


			if(user_id!=''){

				if(resp[1].can_edit===false){
					// User is not authorised to edit this record so send him back to the previous screen
					location.hash=memregfuncs.prev_page_hash;
					return;
				}

				memregfuncs.removeEditRestrictions();

				let title = resp[1].record_details.title || '';
				let fname = resp[1].record_details.fname || '';
				let mname = resp[1].record_details.mname || '';
				let lname = resp[1].record_details.lname || '';
				let name = resp[1].record_details.name || '';
				let email = resp[1].record_details.email || '';
				let mobile = resp[1].record_details.mobile || '';
				let wa_num = mobile;
				if(wa_num!=''){
					if(!/^[+0]/.test(wa_num))
						wa_num = `+${country_code}${wa_num}`;
				}
				let mobile2 = resp[1].record_details.mobile2 || '';
				let gender = resp[1].record_details.gender || '';
				let edu_qual = resp[1].record_details.edu_qual || '';
				let linkedin_accnt = resp[1].record_details.linkedin_accnt || '';
				let x_accnt = resp[1].record_details.x_accnt || '';
				let fb_accnt = resp[1].record_details.fb_accnt || '';
				let website = resp[1].record_details.website || '';
				let blood_grp = resp[1].record_details.blood_grp || '';
				let batch_no = resp[1].record_details.batch_no || '';
				let profile_pic = resp[1].record_details.profile_pic || '';
				let profile_pic_url = resp[1].record_details.profile_pic_url || '';
				let dob = resp[1].record_details.dob || '';
				let annv = resp[1].record_details.annv || '';
				// let marital_status = resp[1].record_details.marital_status || '';
				let residence_city = resp[1].record_details.residence_city || '';
				let residence_state = resp[1].record_details.residence_state || '';
				let residence_country = resp[1].record_details.residence_country || '';
				let residence_pin = resp[1].record_details.residence_pin || '';
				let residence_addrline1 = resp[1].record_details.residence_addrline1 || '';
				let residence_addrline2 = resp[1].record_details.residence_addrline2 || '';
				let residence_addrline3 = resp[1].record_details.residence_addrline3 || '';
				let assigned_sector_ids = resp[1].record_details.assigned_sector_ids || '';
				let work_type = resp[1].record_details.work_type || '';
				// let work_ind = resp[1].record_details.work_ind || '';
				let work_company = resp[1].record_details.work_company || '';
				let designation = resp[1].record_details.designation || '';
				let work_city = resp[1].record_details.work_city || '';
				let work_state = resp[1].record_details.work_state || '';
				let work_country = resp[1].record_details.work_country || '';
				let work_pin = resp[1].record_details.work_pin || '';
				let work_addrline1 = resp[1].record_details.work_addrline1 || '';
				let work_addrline2 = resp[1].record_details.work_addrline2 || '';
				let work_addrline3 = resp[1].record_details.work_addrline3 || '';
				let status = resp[1].record_details.status ?? '';
				let dnd = resp[1].record_details.dnd ?? '';
				// let hashtags = resp[1].record_details.hashtags || '';
				let ref1_name = resp[1].record_details.ref1_name || '';
				let ref1_batch = resp[1].record_details.ref1_batch || '';
				let ref1_mobile = resp[1].record_details.ref1_mobile || '';
				let ref1_wa_num = ref1_mobile;
				if(ref1_wa_num!=''){
					if(!/^[+0]/.test(ref1_wa_num))
						ref1_wa_num = `+${country_code}${ref1_wa_num}`;
				}
				let ref2_name = resp[1].record_details.ref2_name || '';
				let ref2_batch = resp[1].record_details.ref2_batch || '';
				let ref2_mobile = resp[1].record_details.ref2_mobile || '';
				let ref2_wa_num = ref2_mobile;
				if(ref2_wa_num!=''){
					if(!/^[+0]/.test(ref2_wa_num))
						ref2_wa_num = `+${country_code}${ref2_wa_num}`;
				}
				let status_remarks = resp[1].record_details.status_remarks ?? '';

				let payment_status = resp[1].record_details.payment_status ?? '';
				let payment_mode = resp[1].record_details.payment_mode ?? '';
				let membership_fee = resp[1].record_details.membership_fee ?? '';
				let pmt_failure_msg = resp[1].record_details?.payment_details?.pmt_failure_msg ?? '';
				let payment_txn_ref = resp[1].record_details.payment_txn_ref ?? '';
				let payment_instrument_type = resp[1].record_details.payment_instrument_type ?? '';
				let payment_instrument = resp[1].record_details.payment_instrument ?? '';
				let paid_on = resp[1].record_details.paid_on_dt ?? '';
				
				var contobj=$("#user_detail_add_edit_container");

				$('.alert-danger').addClass('d-none').find('.alert-message').html('');
				$('#msgFrm').removeClass('d-none');
				contobj.find(".form-actions").removeClass('d-none');

				contobj.find("form[name=adduserform]:eq(0)").data('mode','edit-user').find('input[name=status]').attr('checked',false).end().get(0).reset();

				contobj.find("#add_edit_mode").val('updateUser');
				contobj.find("#add_edit_recordid").val(user_id);
				contobj.find("#add_form_field_title").val(title);
				contobj.find("#add_form_field_fname").val(fname);
				contobj.find("#add_form_field_mname").val(mname);
				contobj.find("#add_form_field_lname").val(lname);
				contobj.find("#add_form_field_email").val(email).siblings('.email-icon-form-input').data('url',`mailto:${email}`).toggleClass('d-none', email=='');
				contobj.find("#add_form_field_mobile").val(mobile).siblings('.wa-icon-form-input').data('url',`https://wa.me/${wa_num}`).toggleClass('d-none', wa_num=='').end().siblings('.tel-icon-form-input').data('url',`tel:${wa_num}`).toggleClass('d-none', wa_num=='');
				contobj.find("#add_form_field_mobile2").val(mobile2)
				contobj.find("#add_form_field_eduqual").val(edu_qual);
				contobj.find("#add_form_field_linkedinaccnt").val(linkedin_accnt)
				contobj.find("#add_form_field_xaccnt").val(x_accnt)
				contobj.find("#add_form_field_fbaccnt").val(fb_accnt)
				contobj.find("#add_form_field_website").val(website)
				contobj.find("input[name=gender]").prop('checked', false);
				if(gender!=='')
					contobj.find("#add_form_field_gender_"+gender).prop('checked', true);
				contobj.find("#add_form_field_bloodgrp").val(blood_grp);
				contobj.find("#add_form_field_batchno").val(batch_no);
				contobj.find("#add_form_field_profilepic").val('');
				contobj.find("#profile_pic_img").attr('src', profile_pic_url).css('opacity','').end().find('.profile_image').removeClass('d-none').end().find('.profile_image >.remove_image').toggleClass('d-none', profile_pic=='').find('#remove_profile_pic').removeClass('d-none').end().find('#undo_remove_profile_pic').addClass('d-none').end().end().find('#img_del_marked_msg').addClass('d-none');
				contobj.find('#delete_profile_pic').val('0');

				
				if(profile_pic!='' && resp[1].record_details.profile_pic_org_width < resp[1].record_details.profile_pic_max_width)
					contobj.find("#profile_pic_img").attr('width', resp[1].record_details.profile_pic_org_width);
				else
					contobj.find("#profile_pic_img").attr('width','');

				contobj.find("#add_form_field_dob_picker").datepicker('setDate', dob!=''?new Date(dob):null);
				contobj.find("#add_form_field_annv_picker").datepicker('setDate', annv!=''?new Date(annv):null);
				// contobj.find("#add_form_field_maritalstatus").val(marital_status);
				contobj.find("#add_form_field_rescity").val(residence_city);
				contobj.find("#add_form_field_resstate").val(residence_state);
				contobj.find("#add_form_field_rescountry").val(residence_country);
				contobj.find("#add_form_field_respin").val(residence_pin);
				contobj.find("#add_form_field_resaddrline1").val(residence_addrline1);
				contobj.find("#add_form_field_resaddrline2").val(residence_addrline2);
				contobj.find("#add_form_field_resaddrline3").val(residence_addrline3);
				contobj.find("#add_form_field_sector").val(assigned_sector_ids[0]);
				contobj.find("#add_form_field_worktype").val(work_type);
				// contobj.find("#add_form_field_workind").val(work_ind);
				contobj.find("#add_form_field_workcompany").val(work_company);
				contobj.find("#add_form_field_designation").val(designation);
				contobj.find("#add_form_field_workcity").val(work_city);
				contobj.find("#add_form_field_workstate").val(work_state);
				contobj.find("#add_form_field_workcountry").val(work_country);
				contobj.find("#add_form_field_workpin").val(work_pin);
				contobj.find("#add_form_field_workaddrline1").val(work_addrline1);
				contobj.find("#add_form_field_workaddrline2").val(work_addrline2);
				contobj.find("#add_form_field_workaddrline3").val(work_addrline3);
				// contobj.find("#add_form_field_hashtags").val(hashtags);
				contobj.find("#add_form_field_status").val(status);
				contobj.find("#current_status").val(status);
				if(status=='Approved'){
					contobj.find("#add_form_field_status").find('option:not(":selected")').attr('disabled', true);
					$('#approval_info, #disapproval_info, #record-save-button').addClass('d-none');
					$('#record-save-button').attr('disabled', true);
					$('#status_remarks_box').removeClass('d-none').find('#add_form_field_statusremarks').val(status_remarks).attr('disabled', false).end();
					contobj.find('input:not(.non-editable), select:not(.non-editable), textarea:not(.non-editable)').addClass('non-editable');
				}else if(status=='Disapproved'){
					contobj.find("#add_form_field_status").find('option[value=New]').attr('disabled', true).end().find('option[value=Disapproved]').attr('disabled', false).end();
					$('#approval_info').addClass('d-none');
					$('#disapproval_info').addClass('d-none');
					$('#status_remarks_box').removeClass('d-none').find('#add_form_field_statusremarks').val(status_remarks).attr('disabled', false).end();
					contobj.find('input.non-editable, select.non-editable, textarea.non-editable').removeClass('non-editable');
					$('#record-save-button').removeClass('disabled').attr('disabled', false);
				}else{
					contobj.find("#add_form_field_status>option").attr('disabled', false);
					$('#approval_info').addClass('d-none');
					$('#disapproval_info').addClass('d-none');
					$('#status_remarks_box').addClass('d-none').find('#add_form_field_statusremarks').val('').attr('disabled', true).end();
					contobj.find('input.non-editable, select.non-editable, textarea.non-editable').removeClass('non-editable');
					$('#record-save-button').removeClass('disabled').attr('disabled', false);
				}

				contobj.find("#add_form_field_dnd").attr('checked', dnd=='y');
				
				contobj.find("#add_form_field_ref1name").text(ref1_name);
				contobj.find("#add_form_field_ref1batch").text(ref1_batch);
				contobj.find("#add_form_field_ref1mobile").text(ref1_mobile).siblings('.wa-icon-form-input').data('url',`https://wa.me/${ref1_wa_num}`).toggleClass('d-none', ref1_mobile=='').end().siblings('.tel-icon-form-input').data('url',`tel:${ref1_wa_num}`).toggleClass('d-none', ref1_mobile=='');

				contobj.find("#add_form_field_ref2name").text(ref2_name);
				contobj.find("#add_form_field_ref2batch").text(ref2_batch);
				contobj.find("#add_form_field_ref2mobile").text(ref2_mobile).siblings('.wa-icon-form-input').data('url',`https://wa.me/${ref2_wa_num}`).toggleClass('d-none', ref2_mobile=='').end().siblings('.tel-icon-form-input').data('url',`tel:${ref2_wa_num}`).toggleClass('d-none', ref2_mobile=='');
				
				contobj.find('#add_form_field_membershipfee').val(membership_fee);
				contobj.find('#add_form_field_paymentstatus').val(payment_status);
				contobj.find('#add_form_field_pmtfailmsg').text(pmt_failure_msg).parents('.pmtdtls:eq(0)').toggleClass('d-none', payment_status!=='Failed');
				contobj.find('#add_form_field_paymentmode').val(payment_mode);
				contobj.find('#add_form_field_instrumenttype').val(payment_instrument_type);
				contobj.find('#add_form_field_instrument').val(payment_instrument);
				contobj.find('#add_form_field_bnkref').val(payment_txn_ref);
				contobj.find("#add_form_field_paidon_picker").datepicker('setDate', paid_on == '' ? null : new Date(paid_on));
				
				$('#add_form_field_paidon_picker').datepicker(payment_status == 'Paid' ? 'disable' : 'enable').css('background-color', 'transparent');
				contobj.find('.pmtdtls input[type=text]').toggleClass('non-editable', payment_status=='Paid').end().find('.pmtdtls select').toggleClass('non-editable', payment_status=='Paid');
				
				// contobj.find("#add_form_field_status_"+status).attr('checked',true).parents('div.form-group').hide();
				// contobj.find("#add_form_field_login_access_"+login_access).attr('checked',true).parents('div.form-group').hide();
				let header_text = 'Update Registration';
				// if(resp[1].cuid == login_acnt_id){ // cuid has the users table's id
				// 	contobj.find("#add_form_field_status_"+status).parents('div.form-group').hide().end().end();
				// 	header_text = 'Edit Your Profile';
				// }else{
					contobj.find("#add_form_field_status_"+status).parents('div.form-group').show().end().end();
				// }


				$('#record-save-button').removeClass('btn-danger').addClass('btn-success');	

				contobj.find("#record-add-cancel-button").data('back-to',coming_from);
				contobj.find("#record-save-button>span:eq(0)").html('Save Changes');
				contobj.find("#panel-heading-text").text(header_text);
				contobj.find("#infoMsg").html('Edit Registration Request For <b>' + title + ' ' + name +  '</b>');
				memregfuncs.setheaderBarText(header_text);

				memregfuncs.applyEditRestrictions(resp[1].edit_restricted_fields);
				container_id='user_detail_add_edit_container';


			}else{

				var message="Sorry, the edit window could not be opened (Server error).";
				if(resp[0]==1){
					message="Sorry, the edit window could not be opened (User ID missing).";
				}else if(resp[0]==2){
					message="Sorry, the edit window could not be opened (Server error).";
				}else if(resp[0]==3){
					message="Sorry, the edit window could not be opened (Invalid user ID).";
				}

				alert(message);
				location.hash=memregfuncs.prev_page_hash;
				return;

			}

		}else if(otherparams.mode=='viewrecord'){

			if(user_id!=''){
				
				let title = resp[1].record_details.title || '';
				let name = resp[1].record_details.name || '';
				let email = resp[1].record_details.email || '';
				let mobile = resp[1].record_details.mobile || '';
				let wa_num = mobile;
				if(wa_num!=''){
					if(!/^[+0]/.test(wa_num))
						wa_num = `+${country_code}${wa_num}`;
				}
				let mobile2 = resp[1].record_details.mobile2 || '';
				let edu_qual = resp[1].record_details.edu_qual || '';
				let linkedin_accnt = resp[1].record_details.linkedin_accnt || '';
				let x_accnt = resp[1].record_details.x_accnt || '';
				let fb_accnt = resp[1].record_details.fb_accnt || '';
				let website = resp[1].record_details.website || '';
				let gender = resp[1].record_details.gender_view || '';
				let blood_grp = resp[1].record_details.blood_grp_view || '';
				let batch_no = resp[1].record_details.batch_no || '';
				let profile_pic_url = resp[1].record_details.profile_pic_url || '';
				let dob = resp[1].record_details.dob_view || '';
				let annv = resp[1].record_details.annv_view || '';
				// let marital_status = resp[1].record_details.marital_status_view || '';
				let residence_city = resp[1].record_details.residence_city || '';
				let residence_state = resp[1].record_details.residence_state || '';
				let residence_country = resp[1].record_details.residence_country || '';
				let residence_pin = resp[1].record_details.residence_pin || '';
				let residence_addrline1 = resp[1].record_details.residence_addrline1 || '';
				let residence_addrline2 = resp[1].record_details.residence_addrline2 || '';
				let residence_addrline3 = resp[1].record_details.residence_addrline3 || '';
				let sectors = resp[1].record_details.sector_names || '';
				let work_type = resp[1].record_details.work_type || '';
				// let work_ind = resp[1].record_details.work_ind || '';
				let work_company = resp[1].record_details.work_company || '';
				let designation = resp[1].record_details.designation || '';
				let work_city = resp[1].record_details.work_city || '';
				let work_state = resp[1].record_details.work_state || '';
				let work_country = resp[1].record_details.work_country || '';
				let work_pin = resp[1].record_details.work_pin || '';
				let work_addrline1 = resp[1].record_details.work_addrline1 || '';
				let work_addrline2 = resp[1].record_details.work_addrline2 || '';
				let work_addrline3 = resp[1].record_details.work_addrline3 || '';
				// let hashtags = resp[1].record_details.hashtags || '';
				let status = resp[1].record_details.status || '';
				let dnd = resp[1].record_details.dnd_view || '';
				let ref1_name = resp[1].record_details.ref1_name || '';
				let ref1_batch = resp[1].record_details.ref1_batch || '';
				let ref1_mobile = resp[1].record_details.ref1_mobile || '';
				let ref1_wa_num = ref1_mobile;
				if(ref1_wa_num!=''){
					if(!/^[+0]/.test(ref1_wa_num))
						ref1_wa_num = `+${country_code}${ref1_wa_num}`;
				}
				let ref2_name = resp[1].record_details.ref2_name || '';
				let ref2_batch = resp[1].record_details.ref2_batch || '';
				let ref2_mobile = resp[1].record_details.ref2_mobile || '';
				let ref2_wa_num = ref2_mobile;
				if(ref2_wa_num!=''){
					if(!/^[+0]/.test(ref2_wa_num))
						ref2_wa_num = `+${country_code}${ref2_wa_num}`;
				}
				let status_remarks = resp[1].record_details.status_remarks_view ?? '';

				let payment_status = resp[1].record_details.payment_status ?? '';
				let payment_mode = resp[1].record_details.payment_mode ?? '';
				let membership_fee = resp[1].record_details.membership_fee ?? '';
				let pmt_failure_msg = resp[1].record_details?.payment_details?.pmt_failure_msg ?? '';
				let payment_txn_ref = resp[1].record_details.payment_txn_ref ?? '';
				let payment_instrument_type = resp[1].record_details.payment_instrument_type ?? '';
				let payment_instrument = resp[1].record_details.payment_instrument ?? '';
				let paid_on = resp[1].record_details.paid_on_dt_view ?? '';

				var contobj=$("#user_detail_view_container");
				contobj.find("#view_form_field_title").text(title);
				contobj.find("#view_form_field_name").text(name);
				contobj.find("#view_form_field_email").text(email).siblings('.email-icon-form-input').data('url',`mailto:${email}`).toggleClass('d-none', email=='');
				contobj.find("#view_form_field_mobile").text(mobile).siblings('.wa-icon-form-input').data('url',`https://wa.me/${wa_num}`).toggleClass('d-none', mobile=='').end().siblings('.tel-icon-form-input').data('url',`tel:${wa_num}`).toggleClass('d-none', mobile=='');
				contobj.find("#view_form_field_mobile2").text(mobile2);
				contobj.find("#view_form_field_eduqual").text(edu_qual);
				contobj.find("#view_form_field_linkedinaccnt").text(linkedin_accnt);
				contobj.find("#view_form_field_xaccnt").text(x_accnt);
				contobj.find("#view_form_field_fbaccnt").text(fb_accnt);
				contobj.find("#view_form_field_website").text(website);
				contobj.find("#view_form_field_gender").text(gender);
				contobj.find("#view_form_field_bloodgrp").text(blood_grp);
				contobj.find("#view_form_field_batchno").text(batch_no);
				contobj.find("#view_form_field_profilepic").attr('src', profile_pic_url);
				contobj.find("#view_form_field_dob").text(dob);
				contobj.find("#view_form_field_annv").text(annv);
				// contobj.find("#view_form_field_maritalstatus").text(marital_status);
				contobj.find("#view_form_field_rescity").text(residence_city);
				contobj.find("#view_form_field_resstate").text(residence_state);
				contobj.find("#view_form_field_rescountry").text(residence_country);
				contobj.find("#view_form_field_respin").text(residence_pin);
				contobj.find("#view_form_field_resaddrline1").text(residence_addrline1);
				contobj.find("#view_form_field_resaddrline2").text(residence_addrline2);
				contobj.find("#view_form_field_resaddrline3").text(residence_addrline3);
				contobj.find("#view_form_field_sector").text(sectors.join(', '));
				contobj.find("#view_form_field_worktype").text(work_type);
				// contobj.find("#view_form_field_workind").text(work_ind);
				contobj.find("#view_form_field_workcompany").text(work_company);
				contobj.find("#view_form_field_designation").text(designation);
				contobj.find("#view_form_field_workcity").text(work_city);
				contobj.find("#view_form_field_workstate").text(work_state);
				contobj.find("#view_form_field_workcountry").text(work_country);
				contobj.find("#view_form_field_workpin").text(work_pin);
				contobj.find("#view_form_field_workaddrline1").text(work_addrline1);
				contobj.find("#view_form_field_workaddrline2").text(work_addrline2);
				contobj.find("#view_form_field_workaddrline3").text(work_addrline3);
				// contobj.find("#view_form_field_hashtags").text(hashtags);
				contobj.find("#view_form_field_status").text(status);
				contobj.find("#view_form_field_statusremarks").html(status_remarks).toggleClass('d-none', status=='New');
				contobj.find("#view_form_field_dnd").text(dnd).parents('.dnd-section').toggleClass('d-none', !resp[1].show_dnd).end();
				contobj.find("#view_form_field_ref1name").text(ref1_name);
				contobj.find("#view_form_field_ref1batch").text(ref1_batch);
				contobj.find("#view_form_field_ref1mobile").text(ref1_mobile).siblings('.wa-icon-form-input').data('url',`https://wa.me/${ref1_wa_num}`).toggleClass('d-none', ref1_mobile=='').end().siblings('.tel-icon-form-input').data('url',`tel:${ref1_wa_num}`).toggleClass('d-none', ref1_mobile=='');;
				contobj.find("#view_form_field_ref2name").text(ref2_name);
				contobj.find("#view_form_field_ref2batch").text(ref2_batch);
				contobj.find("#view_form_field_ref2mobile").text(ref2_mobile).siblings('.wa-icon-form-input').data('url',`https://wa.me/${ref2_wa_num}`).toggleClass('d-none', ref2_mobile=='').end().siblings('.tel-icon-form-input').data('url',`tel:${ref2_wa_num}`).toggleClass('d-none', ref2_mobile=='');
				
				contobj.find('#view_form_field_membershipfee').text(new Intl.NumberFormat("en-IN", { style: "currency", currency: "INR", "maximumFractionDigits":0 }).format(membership_fee));
				contobj.find('#view_form_field_paymentstatus').text(payment_status);
				contobj.find('#view_form_field_pmtfailmsg').text(pmt_failure_msg).parents('.form-group:eq(0)').toggleClass('d-none', payment_status!=='Failed');
				contobj.find('#view_form_field_paymentmode').text(payment_mode);
				contobj.find('#view_form_field_instrumenttype').text(payment_instrument_type);
				contobj.find('#view_form_field_instrument').text(payment_instrument);
				contobj.find('#view_form_field_bnkref').text(payment_txn_ref);
				contobj.find("#view_form_field_paidon").text(paid_on);

				container_id='user_detail_view_container';
			}else{
				var message="Sorry, the view window could not be opened (Server error).";
				if(resp[0]==1){
					message="Sorry, the view window could not be opened (User ID missing).";
				}else if(resp[0]==2){
					message="Sorry, the view window could not be opened (Server error).";
				}else if(resp[0]==3){
					message="Sorry, the view window could not be opened (Invalid user ID).";
				}

				alert(message);
				location.hash=memregfuncs.prev_page_hash;
				return;
			}

		}
		if(container_id!=''){
			$(".back-to-list-button").removeClass('d-none');
			$("#refresh-list-button").addClass('d-none');
			$("#add-record-button").addClass('d-none');
			$("#user_list_container").addClass('d-none');

			if(container_id!='user_detail_add_edit_container'){
				$("#user_detail_add_edit_container").addClass('d-none');
				$("#edit-record-button").removeClass('d-none').data('recid',otherparams.recordid);
			}else if(container_id!='user_detail_view_container'){
				$("#user_detail_view_container").addClass('d-none');
				$("#edit-record-button").addClass('d-none');
			}

			$("#"+container_id).removeClass('d-none');
			self.setheaderBarText(otherparams.header_bar_text);

		}

		$("#add_form_field_dnd").focus();

	},

	applyEditRestrictions: function(restricted_fields){
		const contobj=$("#user_detail_add_edit_container");
		restricted_fields.forEach(fld=>{
			switch(fld){
				case 'batch_no':
					contobj.find("#add_form_field_batchno").prop('disabled', restricted_fields.includes('batch_no')).addClass('rstrctedt');
					break;
				case 'status':
					contobj.find("input[name=status]").prop('disabled', restricted_fields.includes('status')).addClass('rstrctedt');
					break;
				case 'profile_pic':
					contobj.find("#add_form_field_profilepic").prop('disabled', restricted_fields.includes('profile_pic'));
					contobj.find(".profile_image").addClass('d-none');
					contobj.find("#remove_profile_pic_selection").addClass('d-none');
					break;
			}

		});
	},

	removeEditRestrictions: function(){
		const contobj=$("#user_detail_add_edit_container");
		contobj.find("#add_form_field_memno, #add_form_field_batchno, #add_form_field_memtype, #add_form_field_desiginassoc, input[name=role], input[name=status], #add_form_field_profilepic").prop('disabled', false).end()
			.find(".profile_image, #remove_profile_pic_selection").removeClass('d-none').end();
		contobj.find('.rstrctedt').removeClass('rstrctedt');	
	},


	onRegStatusChange: function(e){
		let org_status = $('#current_status').val();
		let status = $(e.currentTarget).val();
		$("#add_form_field_statusremarks").removeClass("error-field");
		if(status=='Approved'){
			$('#approval_info').removeClass('d-none');
			$('#disapproval_info').addClass('d-none');
			$('#status_remarks_box').removeClass('d-none').find('#add_form_field_statusremarks').attr('disabled', false).focus().end();
			$('#record-save-button').find('span').text('Approve').end().removeClass('btn-danger').addClass('btn-success');
		}else if(status=='Disapproved'){
			$('#approval_info').addClass('d-none');
			$('#disapproval_info').removeClass('d-none');
			$('#status_remarks_box').removeClass('d-none').find('#add_form_field_statusremarks').attr('disabled', false).focus().end();
			if(org_status!='Disapproved'){
				$('#record-save-button').find('span').text('Disapprove').end().removeClass('btn-success').addClass('btn-danger');
			}else{
				$('#record-save-button').find('span').text('Save Changes').end().removeClass('btn-danger').addClass('btn-success');	
			}
		}else{
			$('#approval_info, #disapproval_info').addClass('d-none');
			$('#status_remarks_box').addClass('d-none').find('#add_form_field_statusremarks').attr('disabled', true).end();
			$('#record-save-button').find('span').text('Save Changes').end().removeClass('btn-danger').addClass('btn-success');
		}

	},


	refreshList:function(e){
		if(typeof e=='object' && e.hasOwnProperty('data') && e.data.hasOwnProperty('self')){
			var self=e.data.self;
		}else{
			var self=memregfuncs;
		}

		var currpage=self.paginationdata.curr_page;

		var options={pno:currpage,successResponseHandler:self.onListRefresh};
		self.getList(options);
		return false;

	},


	handleUpdateUserResponse:function(resp){
		var self=memregfuncs;

		var mode_container='user_detail_add_edit_container';
		$(".form-control").removeClass("error-field");

		if(resp.error_code==0 || resp.error_code>=11){
			// var back_to_mode=$("#record-add-cancel-button").data('back-to');
			// // alert('aftersave: '+back_to_mode);
			// mode_container=(back_to_mode!='list-mode')?'user_detail_view_container':'user_list_container';
			var message_container = '.alert-success';

			
			let email = $("#add_form_field_email").val().trim();
			let mobile = $("#add_form_field_mobile").val().trim();
			// mobile = (resp.other_data.mobile!='')?resp.other_data.mobile:mobile;
			// $("#add_form_field_mobile").val(mobile);
			let mailto = `mailto:${email}`;
			let tel = '';
			let wame = '';
			if(mobile!=''){
				if(!/^[+0]/.test(mobile))
					mobile = `+${country_code}${mobile}`;
				wame = `https://wa.me/${mobile}`;	
				tel = `tel:${mobile}`;
			}
			
			$("#add_form_field_email").siblings('.email-icon-form-input').data('url',mailto).toggleClass('d-none',mailto=='');
			$("#add_form_field_mobile").siblings('.wa-icon-form-input').data('url',wame).toggleClass('d-none', wame=='').end().siblings('.tel-icon-form-input').data('url',tel).toggleClass('d-none', tel=='');

			// Update the profile image if required
			if(resp.other_data.profile_pic_deleted && resp.other_data.profile_pic_deleted==1){
				$('#delete_profile_pic').val('0');
				$('#user_detail_add_edit_container .profile_image .remove_image').addClass('d-none');
				$('#profile_pic_img').attr('src', resp.other_data.placeholder_image).css('opacity','');
			}else if(resp.other_data.profile_pic_url && resp.other_data.profile_pic_url!=''){
				if(resp.other_data.profile_pic_org_width < resp.other_data.profile_pic_max_width)
					$('#profile_pic_img').attr({width:resp.other_data.profile_pic_org_width, src:resp.other_data.profile_pic_url}).css('opacity','');
				else
					$('#profile_pic_img').attr({width:'', src:resp.other_data.profile_pic_url}).css('opacity','');
				$('#user_detail_add_edit_container .profile_image .remove_image').removeClass('d-none').find('#remove_profile_pic').removeClass('d-none').end().find('#undo_remove_profile_pic').addClass('d-none').end();
			}else if(resp.other_data.placeholder_image && resp.other_data.placeholder_image!=''){
				$('#profile_pic_img').attr('src', resp.other_data.placeholder_image).css('opacity','');
			}
			$('#img_del_marked_msg').addClass('d-none');
			$('#delete_profile_pic').val('0');
			$('#add_form_field_profilepic, #add_form_field_password').val('');
				
			
			$("#add_form_field_dnd").focus();

			let old_status = $('#current_status').val();
			let new_status = $('#add_form_field_status').val();
			if(resp.error_code==0){
				if(new_status=='Approved'){
					$('#record-save-button').addClass('d-none disabled').attr('disabled',true);
					$('#add_form_field_status').find('option[value=New], option[value=Disapproved]').attr('disabled', true);
					$('#adduserform').find('input:not(.non-editable), select:not(.non-editable), textarea:not(.non-editable)').addClass('non-editable');
				}else if(new_status=='Disapproved'){
					$('#record-save-button').removeClass('btn-danger').addClass('btn-success').find('span').text('Save Changes');
					$('#add_form_field_status').find('option[value=New]').attr('disabled', true);
				}
				$('#current_status').val(new_status);

			}else{
				if(new_status!==old_status){
					// The status was changed, but was not updated, so reset the status selector to the old status
					$('#add_form_field_status').val(old_status).trigger('change');
				}
			}

			$('#user_detail_add_edit_container').find('.pmtdtls input[type=text]').toggleClass('non-editable', resp.other_data.payment_status=='Paid').end().find('.pmtdtls select').toggleClass('non-editable', resp.other_data.payment_status=='Paid');
			
		}else if(resp.error_code==2){
			// data validation errors

			var message_container ='';

			if(resp.error_fields.length>0){
				alert(resp.message);
				setTimeout(()=>{$(resp.error_fields[0]).addClass("error-field").focus(); },0);

			}
			$('#record-save-button, #record-add-cancel-button').removeClass('disabled').attr('disabled',false);

		}else{
			var message_container = '.alert-danger';
			let old_status = $('#current_status').val();
			let new_status = $('#add_form_field_status').val();
			if(new_status!==old_status){
				// The status was changed, but was not updated, so reset the status selector to the old status
				$('#add_form_field_status').val(old_status).trigger('change');
			}
			$('#record-save-button, #record-add-cancel-button').removeClass('disabled').attr('disabled',false);
		}

		$('#record-save-button, #record-add-cancel-button').removeClass('disabled').attr('disabled',false);
		$("#common-processing-overlay").addClass('d-none');
		if(message_container!=''){
			$(message_container).removeClass('d-none').siblings('.alert').addClass('d-none').end().find('.alert-message').html(resp.message);//.end().delay(3000).fadeOut(800,function(){$(this).css('display','').addClass('d-none');});
			var page_scroll='.main-container-inner';
			common_js_funcs.scrollTo($(page_scroll));
			$('#msgFrm').addClass('d-none');
		}

	},

	saveUserDetails:function(formelem){

		var self=memregfuncs;
		var data_mode=$(formelem).data('mode');

		var res = self.validateUserDetails({mode:data_mode});
		if(res.error_fields.length>0){

			if((res.errors[0] || '')!='' )
				alert(res.errors[0]);
			setTimeout(function(){
				$(res.error_fields[0],'#adduserform').focus();
			},0);
			return false;

		}

		$("#common-processing-overlay").removeClass('d-none');
		$('#record-save-button, #record-add-cancel-button').addClass('disabled').attr('disabled',true);
		$('#user_detail_add_edit_container .error-field').removeClass('error-field');

		return true;

	},


	validateUserDetails:function(opts){
		var errors = [], error_fields=[];
		// return {'errors': errors, 'error_fields': error_fields}; // for testing php validation
		let mode='add-user';
		// var pp_max_filesize=memregfuncs.pp_max_filesize;
		$(".form-control").removeClass("error-field");
		// $("#add_form_field_role").next('.select2-container').removeClass("error-field");
		if(typeof opts=='object' && opts.hasOwnProperty('mode'))
			mode=opts.mode;

		const frm = $('#adduserform');
		let title=$.trim(frm.find('#add_form_field_title').val()).replace(/(<([^>]+)>)/ig,"");
		let fname=$.trim(frm.find('#add_form_field_fname').val()).replace(/(<([^>]+)>)/ig,"");
		let mname=$.trim(frm.find('#add_form_field_mname').val()).replace(/(<([^>]+)>)/ig,"");
		let lname=$.trim(frm.find('#add_form_field_lname').val()).replace(/(<([^>]+)>)/ig,"");
		let email=$.trim(frm.find('#add_form_field_email').val());
		let mobile=$.trim(frm.find('#add_form_field_mobile').val());
		let mobile2=$.trim(frm.find('#add_form_field_mobile2').val());
		let gender=$.trim(frm.find('input[name=gender]:checked').val());
		let batch_no=$.trim(frm.find('#add_form_field_batchno').val());
		let dob=$.trim(frm.find('#add_form_field_dob').val());
		let res_city=$.trim(frm.find('#add_form_field_rescity').val());
		let res_pin=$.trim(frm.find('#add_form_field_respin').val());
		let res_addrline1 =$.trim(frm.find('#add_form_field_resaddrline1').val());
		let sector = $.trim(frm.find('#add_form_field_sector').val());
		let fb_accnt=$.trim(frm.find('#add_form_field_fbaccnt').val());
		let website=$.trim(frm.find('#add_form_field_website').val());
		let x_accnt=$.trim(frm.find('#add_form_field_xaccnt').val());
		let linkedin_accnt=$.trim(frm.find('#add_form_field_linkedinaccnt').val());
		let status = $.trim($('#add_form_field_status').val());
		let remarks = $.trim($('#add_form_field_statusremarks').val());
		let payment_status = $.trim($('#add_form_field_paymentstatus').val());
		let payment_mode = $.trim($('#add_form_field_paymentmode').val());
		let bank_ref = $.trim($('#add_form_field_bnkref').val());
		let paid_on = $.trim($('#add_form_field_paidon').val());
		let payment_active = !$('#add_form_field_paymentstatus').hasClass('non-editable');
		

		if(!frm.find('#add_form_field_title').hasClass('rstrctedt') && title == ''){
			errors.push('Salutation is required.');
			error_fields.push('#add_form_field_title');
			$("#add_form_field_title").addClass("error-field");

		}else if(!frm.find('#add_form_field_title').hasClass('rstrctedt') && memregfuncs.salutaions.indexOf(title)==-1){
			errors.push('Salutation should be one of these: '+memregfuncs.salutaions.join(', '));
			error_fields.push('#add_form_field_title');
			$("#add_form_field_title").addClass("error-field");

		}else if(!frm.find('#add_form_field_fname').hasClass('rstrctedt') && fname == ''){
			errors.push('First name is required.');
			error_fields.push('#add_form_field_fname');
			$("#add_form_field_fname").addClass("error-field");

		}else if(!memregfuncs.name_pattern.test(fname)){
			errors.push('The first name has invalid characters.');
			error_fields.push('#add_form_field_fname');
			$("#add_form_field_fname").addClass("error-field");

		}else if(mname!='' && !memregfuncs.name_pattern.test(mname)){
			errors.push('The middle name has invalid characters.');
			error_fields.push('#add_form_field_mname');
			$("#add_form_field_mname").addClass("error-field");

		}else if(!frm.find('#add_form_field_lname').hasClass('rstrctedt') && lname==''){
			errors.push('Surname is required.');
			error_fields.push('#add_form_field_lname');
			$("#add_form_field_lname").addClass("error-field");

		}else if(lname!='' && !memregfuncs.name_pattern.test(lname)){
			errors.push('The surname has invalid characters.');
			error_fields.push('#add_form_field_lname');
			$("#add_form_field_lname").addClass("error-field");

		}else if(!frm.find('#add_form_field_email').hasClass('rstrctedt') && email == ''){
			errors.push('A unique email address is required.');
			error_fields.push('#add_form_field_email');
			$("#add_form_field_email").addClass("error-field");
		}else if(!frm.find('#add_form_field_email').hasClass('rstrctedt') && !memregfuncs.email_pattern.test(email)){
			errors.push('The provided email address is invalid.');
			error_fields.push('#add_form_field_email');
			$("#add_form_field_email").addClass("error-field");
		}else if(!frm.find('#add_form_field_email').hasClass('rstrctedt') && email.length>255){
			errors.push('The email address is too long.');
			error_fields.push('#add_form_field_email');
			$("#add_form_field_email").addClass("error-field");
		}else if(!frm.find('#add_form_field_mobile').hasClass('rstrctedt') && mobile==''){
			errors.push('The WhatsApp number is required.');
			error_fields.push('#add_form_field_mobile');
			$("#add_form_field_mobile").addClass("error-field");
		}else if(!memregfuncs.mobile_pattern.test(mobile)){
			errors.push('The WhatsApp number contains invalid characters or is longer than expected.');
			error_fields.push('#add_form_field_mobile');
			$("#add_form_field_mobile").addClass("error-field");
		}else if(mobile2!='' && !memregfuncs.mobile_pattern.test(mobile2)){
			errors.push('The alternate mobile number contains invalid characters or is longer than expected.');
			error_fields.push('#add_form_field_mobile2');
			$("#add_form_field_mobile2").addClass("error-field");
		}else if(fb_accnt!='' && !/^https?:\/\//i.test(fb_accnt)){
			errors.push('Please provide a valid facebook page url starting with https://');
			error_fields.push('#add_form_field_fbaccnt');
			$("#add_form_field_fbaccnt").addClass("error-field");
		}else if(x_accnt!='' && !/^https?:\/\//i.test(x_accnt)){
			errors.push('Please provide a valid twitter profile url starting with https://');
			error_fields.push('#add_form_field_xaccnt');
			$("#add_form_field_xaccnt").addClass("error-field");
		}else if(linkedin_accnt!='' && !/^https?:\/\//i.test(linkedin_accnt)){
			errors.push('Please provide a valid linkedin profile url starting with https://');
			error_fields.push('#add_form_field_linkedinaccnt');
			$("#add_form_field_linkedinaccnt").addClass("error-field");
		}else if(website!='' && !/^https?:\/\//i.test(website)){
			errors.push('Please provide a valid URL of your website, starting with https:// or http://');
			error_fields.push('#add_form_field_website');
			$("#add_form_field_website").addClass("error-field");
		}else if(gender==''){
			errors.push('The gender is required.');
			error_fields.push('#add_form_field_gender_M');
			$("#add_form_field_gender_M").addClass("error-field");
		}else if(gender!='M' && gender!='F'){
			errors.push('The gender is invalid.');
			error_fields.push('#add_form_field_gender_M');
			$("#add_form_field_gender_M").addClass("error-field");
		}else if(!frm.find('#add_form_field_batchno').hasClass('rstrctedt') && batch_no==''){
			errors.push('The batch no is required.');
			error_fields.push('#add_form_field_batchno');
			$("#add_form_field_batchno").addClass("error-field");
		}else if(batch_no!='' && !memregfuncs.batchno_pattern.test(batch_no)){
			errors.push(`The batch no should be your class X year - between ${memregfuncs.batch_no_min} and ${memregfuncs.batch_no_max}.`);
			error_fields.push('#add_form_field_batchno');
			$("#add_form_field_batchno").addClass("error-field");
		}else if(batch_no<memregfuncs.batch_no_min || batch_no>memregfuncs.batch_no_max){
			errors.push(`The batch no should be your class X year - between ${memregfuncs.batch_no_min} and ${memregfuncs.batch_no_max}.`);
			error_fields.push('#add_form_field_batchno');
			$("#add_form_field_batchno").addClass("error-field");
		}else if(dob==''){
			errors.push('The date of birth is required.');
			error_fields.push('#add_form_field_dob_picker');
			$("#add_form_field_dob_picker").addClass("error-field");
		}else if(res_city==''){
			errors.push('The residence city is required.');
			error_fields.push('#add_form_field_rescity');
			$("#add_form_field_rescity").addClass("error-field");
		}else if(res_pin==''){
			errors.push('The residence PIN is required.');
			error_fields.push('#add_form_field_respin');
			$("#add_form_field_respin").addClass("error-field");
		}else if(res_addrline1==''){
			errors.push('The residence address line 1 is required.');
			error_fields.push('#add_form_field_resaddrline1');
			$("#add_form_field_resaddrline1").addClass("error-field");
		}else if(sector==''){
			errors.push('The sector is required.');
			error_fields.push('#add_form_field_sector');
			$("#add_form_field_sector").addClass("error-field");
		}else if (payment_active == true && payment_status !== 'Paid' && (paid_on !== '' || payment_mode !== '' || bank_ref != '')) {
			errors.push('You have either provide the payment mode, payment date or the bank reference so please set the payment status as \"Paid\".');
			error_fields.push('#add_form_field_paymentstatus');
			$("#add_form_field_paymentstatus").addClass("error-field");
		}else if(payment_active && payment_status=='Paid' && payment_mode==''){
			errors.push('For paid registrations the payment mode is required.');
			error_fields.push('#add_form_field_paymentmode');
			$("#add_form_field_paymentmode").addClass("error-field");
		}else if(payment_active && payment_status=='Paid' && payment_mode!='Cash' && bank_ref==''){
			errors.push('For paid registrations the bank reference is required.');
			error_fields.push('#add_form_field_bnkref');
			$("#add_form_field_bnkref").addClass("error-field");
		}else if(status=='Approved' && remarks==''){
			errors.push('The approval remark is required.');
			error_fields.push('#add_form_field_statusremarks');
			$("#add_form_field_statusremarks").addClass("error-field");
		}else if(status=='Disapproved' && remarks==''){
			errors.push('The disapproval remark is required.');
			error_fields.push('#add_form_field_statusremarks');
			$("#add_form_field_statusremarks").addClass("error-field");
		}		

		if(error_fields.length==0 && status=='Approved' && payment_status!='Paid'){
			if(!confirm('Really approve an unpaid registration?')){
				error_fields.push('#add_form_field_paymentstatus');
				$("#add_form_field_paymentstatus").addClass("error-field");
			}
		}

		return {'errors': errors, 'error_fields': error_fields};

	},


	
	closeAddUserForm:function(){
		var self =this;
		return true;

	},


	setheaderBarText:function(text){
		$("#header-bar-text").find(":first-child").html(text);
		// $('#panel-heading-text').text("Add user");

	},

	removeProfilePicSelection: function(e){
		e.preventDefault();
		e.stopPropagation();
		$('#add_form_field_profilepic').val('').removeClass('error-field');
	},

	markProfilePicForDeletion: function(e){
		e.preventDefault();
		e.stopPropagation();
		$(e.currentTarget).addClass('d-none');
		$('#undo_remove_profile_pic, #img_del_marked_msg').removeClass('d-none');
		$('#profile_pic_img').css('opacity','0.3');
		$('#delete_profile_pic').val('1');
	},

	removeProfilePicDeleteMarker: function(e){
		e.preventDefault();
		e.stopPropagation();
		$(e.currentTarget).addClass('d-none');
		$('#img_del_marked_msg').addClass('d-none');
		$('#remove_profile_pic').removeClass('d-none');
		$('#profile_pic_img').css('opacity','');
		$('#delete_profile_pic').val('0');

	},
	

	onHashChange:function(e){
		var hash=location.hash.replace(/^#/,'');
		// alert(hash);
		if(memregfuncs.curr_page_hash!=memregfuncs.prev_page_hash){
			memregfuncs.prev_page_hash=memregfuncs.curr_page_hash;
		}
		memregfuncs.curr_page_hash=hash;

		var hash_params={mode:''};
		if(hash!=''){
			var hash_params_temp=hash.split('&');
			var hash_params_count= hash_params_temp.length;
			for(var i=0; i<hash_params_count; i++){
				var temp=hash_params_temp[i].split('=');
				hash_params[temp[0]]=decodeURIComponent(temp[1]);
			}
		}



		switch(hash_params.mode.toLowerCase()){
			case 'view':
							$('.alert-success, .alert-danger').addClass('d-none');

							if(hash_params.hasOwnProperty('recid') && hash_params.recid!=''){
								memregfuncs.openRecordForViewing(hash_params.recid);
							}else{
								location.hash=memregfuncs.prev_page_hash;
							}
							break;

			case 'edit':
							$('.alert-success, .alert-danger').addClass('d-none');
							$('#msgFrm').removeClass('d-none');
							if(hash_params.hasOwnProperty('recid') && hash_params.recid!=''){
								memregfuncs.openRecordForEditing(hash_params.recid);

							}else{
								location.hash=memregfuncs.prev_page_hash;
							}
							break;



			default:

					if(memregfuncs.default_list){
						memregfuncs.default_list = false;
						if(hash==''){
							$("#search-field_status").val('New'); // Only ne sattuses to be listed by default
							memregfuncs.doSearch();
							break; // Break out of this case section
						}
					}

					$('.alert-success, .alert-danger').addClass('d-none');
					$('#msgFrm').removeClass('d-none');
					var params={mode:'getList',pno:1, searchdata:"[]", sortdata:JSON.stringify(memregfuncs.sortparams), listformat:'html'};

					if(hash_params.hasOwnProperty('pno')){
						params['pno']=hash_params.pno
					}else{
						params['pno']=1;
					}

					if(hash_params.hasOwnProperty('searchdata')){
						params['searchdata']=hash_params.searchdata;

					}
					if(hash_params.hasOwnProperty('sortdata')){
						params['sortdata']=hash_params.sortdata;

					}

					memregfuncs.searchparams=JSON.parse(params['searchdata']);
					memregfuncs.sortparams=JSON.parse(params['sortdata']);

					if(memregfuncs.sortparams.length==0){
						memregfuncs.sortparams.push(memregfuncs.default_sort);
						params['sortdata']=JSON.stringify(memregfuncs.sortparams);
					}

					if(memregfuncs.searchparams.length>0){
							$.each(memregfuncs.searchparams , function(idx,data) {
									//console.log(data);
									switch (data.searchon) {
										case 'name':
											$("#search-field_name").val(data.searchtext);
											break;
										case 'status':
											$("#search-field_status").val(data.searchtext);
											break;
									}

							});
							//$('#close_box').removeClass('d-none');
						$("#search_field").val(memregfuncs.searchparams[0]['searchon'] || '');
					}
					// params['searchdata']=encodeURIComponent(params['searchdata']);
					// params['sortdata']=encodeURIComponent(params['sortdata']);

					if(memregfuncs.searchparams.length>0){
						if(memregfuncs.searchparams[0]['searchon'] == 'status')
							$("#search_text").val(memregfuncs.searchparams[0]['searchtext'][0]=='1'?'Active':'Inactive');
						else
							$("#search_text").val(memregfuncs.searchparams[0]['searchtext'] || '');

						$("#search_field").val(memregfuncs.searchparams[0]['searchon'] || '');
						//$('#close_box').removeClass('d-none');

					}

					$("#common-processing-overlay").removeClass('d-none');

					common_js_funcs.callServer({cache:'no-cache',async:true,dataType:'json',type:'post', url:self.ajax_data_script,params:params,successResponseHandler:memregfuncs.showList,successResponseHandlerParams:{self:memregfuncs}});

					var show_srch_form = false;
					if (typeof(Storage) !== "undefined") {
						srch_frm_visible = localStorage.memreg_search_toggle;
					} else {
						srch_frm_visible = Cookies.get('memreg_search_toggle');
					}
					if(srch_frm_visible && srch_frm_visible == 'visible')
						show_srch_form = true;
					$('.toggle-search').toggleClass('search-form-visible', show_srch_form);
					$('#search_records').closest('.panel-search').toggleClass('d-none', !show_srch_form);
					var search_form_cont = $('#search_records').closest('.panel-search');
					if(search_form_cont.hasClass('d-none'))
						$('.toggle-search').prop('title','Open search panel');
					else{
						$('.toggle-search').prop('title','Close search panel');
						$("#search-field_fullname").focus();
					}

					// $("#search-field_fullname").focus();

		}


		//$("[data-rel='tooltip']").tooltip({html:true, placement:'top', container:'body'});




	}

}