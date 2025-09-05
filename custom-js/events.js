var evfuncs={
	searchparams:[],  /* [{searchon:'',searchtype:'',searchtext:''},{},..] */
	sortparams:[],  /* [{sorton:'',sortorder:''},{},..] */
	default_sort:{sorton:'start_dt',sortorder:'DESC'},
	paginationdata:{},
	defaultleadtabtext:'Events',
	filtersapplied:[],
	statuschangestarted:0,
	ajax_data_script:'events.php',
	curr_page_hash:'',
	prev_page_hash:'',
	name_pattern: /^[A-Z0-9_ -]+$/i,
	int_pattern: /^\d+$/,
	gst_pattern: /^\d+(\.\d{1,2})?$/,
	pp_max_filesize:0,
	default_list: true,
	
	initiateStatusChange:function(statuscell){
		var self=evfuncs;

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
			localStorage.event_search_toggle = elem.hasClass('search-form-visible') ? 'visible' : '';
		} else {
			Cookies.set('event_search_toggle', elem.hasClass('search-form-visible') ? 'visible' : '', {path : '/'/*, secure: true*/});
		}
	},

	confirmAndExecuteStatusChange:function(statuscell){
		var self=evfuncs;

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
			var options={cache:'no-cache',dataType:'json',async:true,type:'post',url:evfuncs.ajax_data_script+"?mode=changeStatus",data:"newstatus="+newstatus+"&recordid="+userid,successResponseHandler:evfuncs.handleStatusChangeResponse,successResponseHandlerParams:{statuscell:statuscell,rowelem:rowelem}};
			common_js_funcs.callServer(options);
			$(statuscell).removeClass("status-grn");
			$(statuscell).removeClass("status-red");
			if(parseInt(newstatus)==1){
				$(statuscell).addClass("status-grn");
			}else{
				$(statuscell).addClass("status-red");
			}
		}else{
			evfuncs.statuschangestarted=0;
			evfuncs.abortStatusChange(statuscell);

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
							evfuncs.statuschangestarted=0;
							evfuncs.abortStatusChange(statuscell);
						}
					},
					"Yes":	{
						"label": "Yes",
						"className": "btn-danger btn-primary",
						"callback": function(ev){

							var options={cache:'no-cache',dataType:'json',async:true,type:'post',url:evfuncs.ajax_data_script+"?mode=changeStatus",data:"newstatus="+newstatus+"&recordid="+userid,successResponseHandler:evfuncs.handleStatusChangeResponse,successResponseHandlerParams:{statuscell:statuscell,rowelem:rowelem}};
							common_js_funcs.callServer(options);
						}
					}

				}

		});*/




	},

	abortStatusChange:function(statuscell){
		var self=evfuncs;

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
		var self=evfuncs;

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
		var self=evfuncs;
		var listhtml=resp[1].list;
		self.user_count=resp[1]['reccount'];
		$("#rec_list_container").removeClass('d-none');
		$("#rec_detail_add_edit_container").addClass('d-none');
		$("#common-processing-overlay").addClass('d-none');
		// $('#search_field').select2({minimumResultsForSearch: -1});
		$("#userlistbox").html(listhtml);
		
		if(resp[1].tot_rec_cnt>0){
			$('#heading_rec_cnt').text((resp[1]['reccount']==resp[1]['tot_rec_cnt'])?`(${resp[1]['tot_rec_cnt']})`:`(${resp[1]['reccount'] || 0} of ${resp[1]['tot_rec_cnt']})`);
			
		}else{
			$('#heading_rec_cnt').text('(0)')
		}
			
		$("#add-record-button").removeClass('d-none');
		$("#refresh-list-button").removeClass('d-none');
		$(".back-to-list-button").addClass('d-none').attr('href',"events.php#"+evfuncs.curr_page_hash);
		$("#edit-record-button").addClass('d-none');
		self.paginationdata=resp[1].paginationdata;

		self.setSortOrderIcon();


	},


	onListRefresh:function(resp,otherparams){
		var self=evfuncs;
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
		var self=evfuncs;
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
		var self=evfuncs;
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
		var self=evfuncs;
		self.searchparams=[];
	},

	setSearchParams:function(obj){
		var self=evfuncs;
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
				if(remove_all==='falls_in_period'){
					$("#search-field_periodstart_picker").datepicker('setDate', null);
					$("#search-field_periodend_picker").datepicker('setDate', null);
				}else
					$('.panel-search .srchfld[data-fld='+remove_all+']').val('');
			}
		}

		var self=evfuncs;
		// self.filtersapplied=[]; // remove the filter bar messages
		if(remove_all===true){
			self.resetSearchParamsObj();
			document.search_form.reset();
			$("#search-field_periodstart_picker").datepicker('setDate', null);
			$("#search-field_periodend_picker").datepicker('setDate', null);
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

		evfuncs.resetSearchParamsObj();
		let period_text = ['',''];
		let period = false;
		let fld = '';
		$('.panel-search .srchfld').each(function(i, el){
			let val = $.trim($(el).val());
			if(val!=''){
				fld = $(el).data('fld');
				if(fld=='period_start')
					period_text[0] = val;
				else if(fld=='period_end')
					period_text[1] = val;
				else
					evfuncs.setSearchParams({searchon:$(el).data('fld'),searchtype:$(el).data('type'),searchtext:val});
				/*if(fld!=='period_start' && fld!=='period_end'){
					if(period_text[0]!='' && period_text[1]!='')
					evfuncs.setSearchParams({searchon:'falls_in_period',searchtype:'CONTAINS',searchtext:period_text});
				}else{

				}*/

			}
		});

		if(period_text[0]!='' || period_text[1]!='')
			evfuncs.setSearchParams({searchon:'falls_in_period',searchtype:'CONTAINS',searchtext:period_text});

		if(evfuncs.searchparams.length<=0){
			if($('.clear-filter').length>0)
				$('.clear-filter').trigger('click');
			return false;
		}

		var options={pno:1};
		evfuncs.getList(options);
		//self.toggleSearch(this);
		return false;
	},


	changePage:function(ev){
		ev.preventDefault();
		if(!$(ev.currentTarget).parent().hasClass('disabled')){
			var self=evfuncs;
			var pno=$(ev.currentTarget).data('page');
			self.getList({pno:pno});
			// return false;
		}

	},



	sortTable:function(e){
		var self=e.data.self;

		var elemid=e.currentTarget.id;
		var elemidparts=elemid.split('_');
		var sorton=elemidparts[1].replace(/-/g,'_');
		var sortorder='ASC';

		if(sorton == 'usertype')
			sorton = 'user_type';

		if($(e.currentTarget).find("i:eq(0)").hasClass('fa-sort-up')){
			sortorder='DESC';
		}

		var pno = 1;
		// if(self.sortparams[0].sorton==sorton){
		// 	if(self.paginationdata.curr_page!='undefined' && self.paginationdata.curr_page>1){
		// 		pno = self.paginationdata.curr_page;
		// 	}
		// } Page number should be reset if the sorting feature is used

		evfuncs.sortparams=[];
		evfuncs.sortparams.push({sorton:sorton, sortorder:sortorder});
		var options={pno:pno};
		evfuncs.getList(options);

	},



	setSortOrderIcon:function(){
		var self=evfuncs;
		if(self.sortparams.length>0){
			var sorton = self.sortparams[0].sorton == 'user_type'?'usertype':self.sortparams[0].sorton.replace(/_/g,'-');
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
		var self=evfuncs;
		if(recordid=='')
			return false;

		$("#record-save-button").addClass('d-none').attr('disabled', 'disabled');
		$("#common-processing-overlay").removeClass('d-none');
		var coming_from='';
		var options={mode:'viewrecord',recordid:recordid,loadingmsg:"Opening the lead '"+recordid+"' for viewing...",leadtabtext:'View Event Details',coming_from:coming_from}
		self.openRecord(options);
		 return false;

	},

	openRecordForEditing:function(recordid){
		var self=evfuncs;
		if(recordid=='')
			return false;

		document.addrecform.reset();
		$(".form-control").removeClass("error-field");
		$("#record-save-button").removeClass('d-none').attr('disabled', false);
		// $("#add_form_field_role").next('.select2-container').removeClass("error-field");
		$("#common-processing-overlay").removeClass('d-none');
		$("#record-add-cancel-button").attr('href',"events.php#"+evfuncs.prev_page_hash);
		$('#msgFrm').removeClass('d-none');
		var coming_from='';//elem.data('in-mode');
		var options={mode:'editrecord',recordid:recordid,leadtabtext:'Edit Event\'s Details',coming_from:coming_from}
		self.openRecord(options);
		return false;

	},


	openRecord:function(options){
		var self=evfuncs;
		var opts={leadtabtext:'Event Details'};
		$.extend(true,opts,options);

		evfuncs.dep_rowno_max=-1;

		var params={mode:"getRecordDetails",recordid:opts.recordid};
		var options={cache:'no-cache',async:true,type:'post',dataType:'json',url:self.ajax_data_script,params:params,successResponseHandler:self.showLeadDetailsWindow,successResponseHandlerParams:{self:self,mode:opts.mode,recordid:opts.recordid,coming_from:opts.coming_from,header_bar_text:opts.leadtabtext}};
		common_js_funcs.callServer(options);

	},


	showLeadDetailsWindow:function(resp,otherparams){
		const self=otherparams.self;
		let container_id='';
		$("#common-processing-overlay").addClass('d-none');
		const rec_id= resp[1].record_details.id ??''; // ad_banners table's id
		
		if(otherparams.mode=='editrecord'){
			var coming_from=otherparams.coming_from;


			if(rec_id!=''){

				if(resp[1].can_edit===false){
					// User is not authorised to edit this record so send him back to the previous screen
					location.hash=evfuncs.prev_page_hash;
					return;
				}

				evfuncs.removeEditRestrictions();

				let name = resp[1].record_details.name || '';
				let name_disp = resp[1].record_details.name_disp || '';
				let booking_link = resp[1].record_details.booking_link??'';
				let description = resp[1].record_details.description??'';
				let venue = resp[1].record_details.venue??'';
				let tkt_price = resp[1].record_details.tkt_price??'';
				
				let early_bird = resp[1].record_details.early_bird??'';
				let early_bird_tkt_price = resp[1].record_details.early_bird_tkt_price??'';
				let early_bird_end_dt = resp[1].record_details.early_bird_end_dt??'';
				let early_bird_max_cnt = resp[1].record_details.early_bird_max_cnt??'';


				let gst_perc = resp[1].record_details.gst_perc??'';
				let conv_fee = resp[1].record_details.conv_fee??'';
				let start_dt = resp[1].record_details.start_dt || '';
				let end_dt = resp[1].record_details.end_dt || '';
				let time_text = resp[1].record_details.time_text || '';
				let max_tkt_per_person = resp[1].record_details.max_tkt_per_person || '';
				let dsk_img = resp[1].record_details.dsk_img || '';
				let dsk_img_url = resp[1].record_details.dsk_img_url || '';
				let dsk_img_max_width = resp[1].record_details.dsk_img_max_width || '';
				let dsk_img_org_width = resp[1].record_details.dsk_img_org_width || '';
				let mob_img = resp[1].record_details.mob_img || '';
				let mob_img_url = resp[1].record_details.mob_img_url || '';
				let mob_img_max_width = resp[1].record_details.mob_img_max_width || '';
				let mob_img_org_width = resp[1].record_details.mob_img_org_width || '';
				let reg_start_dt = resp[1].record_details.reg_start_dt || '';
				let reg_end_dt = resp[1].record_details.reg_end_dt || '';
				let reg_active = resp[1].record_details.reg_active || '';
				let active = resp[1].record_details.active || '';
				const today_obj = new Date(resp[1].today);
				const start_dt_obj = new Date(start_dt);
				const end_dt_obj = new Date(end_dt);
				const reg_start_dt_obj = reg_start_dt!=''?new Date(reg_start_dt):null;
				const reg_end_dt_obj = reg_end_dt!=''?new Date(reg_end_dt):null;


				var contobj=$("#rec_detail_add_edit_container");

				$('.alert-danger').addClass('d-none').find('.alert-message').html('');
				$('#msgFrm').removeClass('d-none');
				contobj.find(".form-actions").removeClass('d-none');

				contobj.find("form[name=addrecform]:eq(0)").data('mode','edit-rec').find('input[name=status]').attr('checked',false).end().get(0).reset();

				contobj.find("#add_edit_mode").val('updaterec');
				contobj.find("#add_edit_recordid").val(rec_id);
				contobj.find("#add_form_field_name").val(name);
				contobj.find("#event_booking_link_cont").find('a').data('bk_lnk', booking_link).end().toggleClass('d-none', booking_link=='');
				contobj.find("#add_form_field_description").val(description);
				contobj.find("#add_form_field_venue").val(venue);
				contobj.find("#add_form_field_tktprice").val(tkt_price);
				contobj.find("#add_form_field_gstperc").val(gst_perc);
				contobj.find("#add_form_field_convfee").val(conv_fee);
				contobj.find("#add_form_field_maxtktperperson").val(max_tkt_per_person);
				contobj.find("#add_form_field_dskimg").val('');
				contobj.find("#dsk_banner_img").attr({src: dsk_img_url, width: (dsk_img!='')?dsk_img_org_width:''}).parent('.ad_banner_image').toggleClass('d-none', dsk_img=='').end();
				contobj.find("#mob_banner_img").attr({src: mob_img_url, width: (mob_img!='')?mob_img_org_width:''}).parent('.ad_banner_image').toggleClass('d-none', mob_img=='').end();

				if(early_bird=='y'){
					$('#add_form_field_ebtktpricechk').trigger('click'); //prop('checked', true);
					$('#add_form_field_ebtktprice').val(early_bird_tkt_price);
					if(early_bird_end_dt!=''){
						$('#add_form_field_ebenddtchk').prop('checked', true); 
						evfuncs.allowDisallowEarlyBirdEndDate(true, new Date(early_bird_end_dt));
						// $('#add_form_field_ebenddt_picker').datepicker('setDate', new Date(early_bird_end_dt));
					}

					if(early_bird_max_cnt!==''){
						$('#add_form_field_ebmaxcntchk').prop('checked', true); 
						evfuncs.allowDisallowEarlyBirdRegCnt(true, early_bird_max_cnt);
						// $('#add_form_field_ebmaxcnt').val(early_bird_max_cnt);
					}
				}else{
					$('#add_form_field_ebtktpricechk').prop('checked', false);
					evfuncs.enableDisableEarlyBirdOffer();
					$('#early_bird_pricing_rules').addClass('d-none');
				}

				
				if(start_dt_obj<today_obj){
					contobj.find("#add_form_field_startdt_picker").datepicker('option', 'minDate', start_dt_obj);
					contobj.find("#add_form_field_enddt_picker").datepicker('option', 'minDate', start_dt_obj);
				}else{
					contobj.find("#add_form_field_startdt_picker").datepicker('option', 'minDate', "-0d");
					contobj.find("#add_form_field_enddt_picker").datepicker('option', 'minDate', "-0d");
				}
				contobj.find("#add_form_field_startdt_picker").datepicker('setDate', start_dt_obj);
				contobj.find("#add_form_field_enddt_picker").datepicker('setDate', end_dt_obj);


				
				if(reg_start_dt_obj!=null && reg_start_dt_obj<today_obj){
					contobj.find("#add_form_field_regstartdt_picker").datepicker('option', 'minDate', reg_start_dt_obj);
					contobj.find("#add_form_field_regenddt_picker").datepicker('option', 'minDate', reg_start_dt_obj);
				}else{
					contobj.find("#add_form_field_regstartdt_picker").datepicker('option', 'minDate', "-0d");
					contobj.find("#add_form_field_regenddt_picker").datepicker('option', 'minDate', "-0d");
				}
				contobj.find("#add_form_field_regstartdt_picker").datepicker('setDate', reg_start_dt_obj);
				contobj.find("#add_form_field_regenddt_picker").datepicker('setDate', reg_end_dt_obj);


				contobj.find("#add_form_field_timetext").val(time_text);
				
				contobj.find("input[name=reg_active]").attr('checked', false);
				if(active!='')
					contobj.find("#add_form_field_regactive_"+reg_active).attr('checked', true);

				contobj.find("input[name=active]").attr('checked', false);
				if(active!='')
					contobj.find("#add_form_field_status_"+active).attr('checked', true);
				

				let header_text = 'Edit Event';
				
				contobj.find("#record-add-cancel-button").data('back-to',coming_from);
				contobj.find("#record-save-button>span:eq(0)").html('Save Changes');
				contobj.find("#panel-heading-text").text(header_text);
				contobj.find("#infoMsg").html('Edit Event <b>' + name_disp +  '</b>');
				evfuncs.setheaderBarText(header_text);

				evfuncs.applyEditRestrictions(resp[1].edit_restricted_fields);
				container_id='rec_detail_add_edit_container';


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
				location.hash=evfuncs.prev_page_hash;
				return;

			}

		}

		if(container_id!=''){
			$(".back-to-list-button").removeClass('d-none');
			$("#refresh-list-button").addClass('d-none');
			$("#add-record-button").addClass('d-none');
			$("#rec_list_container").addClass('d-none');

			if(container_id!='rec_detail_add_edit_container'){
				$("#rec_detail_add_edit_container").addClass('d-none');
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
					contobj.find("#add_form_field_dskimg").prop('disabled', restricted_fields.includes('dsk_img')).addClass('rstrctedt');
					break;
			}

		});
	},

	removeEditRestrictions: function(){
		const contobj=$("#rec_detail_add_edit_container");
		contobj.find("#add_form_field_name, input[name=active], #add_form_field_dskimg").prop('disabled', false).end();			
		contobj.find('.rstrctedt').removeClass('rstrctedt');	
	},


	
	backToList:function(e){
		// if(typeof e=='object' && e.hasOwnProperty('data') && e.data.hasOwnProperty('self')){
			// var self=e.data.self;
		// }else{
			// var self=evfuncs;
		// }


		// $("#back-to-list-button").addClass('d-none');
		// $("#refresh-list-button").removeClass('d-none');
		// $("#add-record-button").removeClass('d-none');
		// $("#edit-record-button").addClass('d-none');
		// $("#rec_list_container").removeClass('d-none');
		// $("#user_detail_view_container").addClass('d-none');
		// $("#rec_detail_add_edit_container").addClass('d-none');

		// self.setheaderBarText("Users List");



	},


	refreshList:function(e){
		if(typeof e=='object' && e.hasOwnProperty('data') && e.data.hasOwnProperty('self')){
			var self=e.data.self;
		}else{
			var self=evfuncs;
		}

		var currpage=self.paginationdata.curr_page;

		var options={pno:currpage,successResponseHandler:self.onListRefresh};
		self.getList(options);
		return false;

	},


	handleAddRecResponse:function(resp){
		var self=evfuncs;
		$(".form-control").removeClass("error-field");

		if(resp.error_code==0){
			var message_container = '.alert-success';
			$("#record-add-cancel-button>i:eq(0)").next('span').html('Close');
			$("form[name=addrecform]").find(".error-field").removeClass('error-field').end().get(0).reset();
			$("#add_form_field_status_y").prop('checked',true);
			$("#add_form_field_regactive_y").prop('checked',true);
			$('#dsk_banner_img').attr('src',""); // empty the dsk image src
			$('#mob_banner_img').attr('src',""); // empty the mob image src
			$('#add_form_field_dskimg').val(''); // empty the dsk img file input
			$('#add_form_field_mobimg').val(''); // empty the mob img file input
			$("#add_form_field_startdt_picker").datepicker('setDate', null);
			$("#add_form_field_enddt_picker").datepicker('setDate', null);
			$("#add_form_field_regstartdt_picker").datepicker('setDate', null);
			$("#add_form_field_regenddt_picker").datepicker('setDate', null);
			$("#add_form_field_name").focus();

			document.querySelector('.main-content').scrollIntoView(true);
		}else if(resp.error_code==2){
			var message_container ='';
			if(resp.error_fields.length>0){
				var msg = resp.message;
				alert(msg);
				$(resp.error_fields[0]).focus();
				$(resp.error_fields[0]).addClass("error-field");
			}

		}else{
			var message_container = '.alert-danger';
		}

		$('#record-save-button, #record-add-cancel-button').removeClass('disabled').attr('disabled',false);
		$("#common-processing-overlay").addClass('d-none');

		if(message_container!=''){
			$(message_container).removeClass('d-none').siblings('.alert').addClass('d-none').end().find('.alert-message').html(resp.message);
			var page_scroll='.main-container-inner';
			common_js_funcs.scrollTo($(page_scroll));
			$('#msgFrm').addClass('d-none');
		}
	},

	handleUpdateRecResponse:function(resp){
		var self=evfuncs;

		var mode_container='rec_detail_add_edit_container';
		$(".form-control").removeClass("error-field");

		if(resp.error_code==0){
			
			var message_container = '.alert-success';

			// Update the dsk and mob images if required
			if(resp.other_data.dsk_img_url && resp.other_data.dsk_img_url!=''){
				// if(resp.other_data.dsk_img_org_width < resp.other_data.dsk_img_max_width)
					$('#dsk_banner_img').attr({width:resp.other_data.dsk_img_org_width, src:resp.other_data.dsk_img_url}).parent('.ad_banner_image').removeClass('d-none').end();
				// else
				// 	$('#dsk_banner_img').attr({width:'', src:resp.other_data.dsk_img_url}).parent('.ad_banner_image').removeClass('d-none').end();
			}else{
				$('#dsk_banner_img').parent('.ad_banner_image').toggleClass('d-none', $('#dsk_banner_img').attr('src')=='');
			}

			if(resp.other_data.mob_img_url && resp.other_data.mob_img_url!=''){
				// if(resp.other_data.mob_img_org_width < resp.other_data.mob_img_max_width)
					$('#mob_banner_img').attr({width:resp.other_data.mob_img_org_width, src:resp.other_data.mob_img_url}).parent('.ad_banner_image').removeClass('d-none').end();
				// else
				// 	$('#mob_banner_img').attr({width:'', src:resp.other_data.mob_img_url}).parent('.ad_banner_image').removeClass('d-none').end();
			}else {
				$('#mob_banner_img').parent('.ad_banner_image').toggleClass('d-none', $('#mob_banner_img').attr('src')=='');
			}
			
			$('#add_form_field_dskimg, #add_form_field_mobimg').val('');
			
			$("#add_form_field_name").focus();
		}else if(resp.error_code==2){
			// data validation errors

			var message_container ='';

			if(resp.error_fields.length>0){
				alert(resp.message);
				setTimeout(()=>{$(resp.error_fields[0]).addClass("error-field").focus(); },0);

			}

		}else{
			var message_container = '.alert-danger';
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

	saveRecDetails:function(formelem){

		var self=evfuncs;
		var data_mode=$(formelem).data('mode');

		var res = self.validateRecDetails({mode:data_mode});
		if(res.error_fields.length>0){

			alert(res.errors[0]);
			setTimeout(function(){
				$(res.error_fields[0],'#addrecform').focus();
			},0);
			return false;

		}

		$("#common-processing-overlay").removeClass('d-none');
		$('#record-save-button, #record-add-cancel-button').addClass('disabled').attr('disabled',true);
		$('#rec_detail_add_edit_container .error-field').removeClass('error-field');

		return true;

	},


	validateRecDetails:function(opts){
		var errors = [], error_fields=[];
		// return {'errors': errors, 'error_fields': error_fields}; // for testing php validation
		let mode='add-rec';
		// var pp_max_filesize=evfuncs.pp_max_filesize;
		$(".form-control").removeClass("error-field");
		// $("#add_form_field_role").next('.select2-container').removeClass("error-field");
		if(typeof opts=='object' && opts.hasOwnProperty('mode'))
			mode=opts.mode;

		const frm = $('#addrecform');
		let name=$.trim(frm.find('#add_form_field_name').val());
	 	let description=$.trim(frm.find('#add_form_field_description').val());
	 	let venue=$.trim(frm.find('#add_form_field_venue').val());
	 	let max_tkt_per_person=$.trim(frm.find('#add_form_field_maxtktperperson').val());
	 	let tkt_price = $.trim(frm.find('#add_form_field_tktprice').val());

	 	let eb_tkt_price_chk = frm.find('#add_form_field_ebtktpricechk').is(':checked');
	 	let eb_tkt_price = $.trim(frm.find('#add_form_field_ebtktprice').val());
	 	let eb_validtill_chk = frm.find('#add_form_field_ebenddtchk').is(':checked');
	 	let eb_valid_till = frm.find('#add_form_field_ebenddt').val();
	 	let eb_regcnt_chk = frm.find('#add_form_field_ebmaxcntchk').is(':checked');
	 	let eb_reg_cnt = frm.find('#add_form_field_ebmaxcnt').val();
	 	
	 	let gst_perc = $.trim(frm.find('#add_form_field_gstperc').val());
	 	let conv_fee = $.trim(frm.find('#add_form_field_convfee').val());
	 	let startdt=$.trim(frm.find('#add_form_field_startdt').val());
	 	let enddt=$.trim(frm.find('#add_form_field_enddt').val());
	 	let time_text=$.trim(frm.find('#add_form_field_timetext').val());
		let reg_startdt=$.trim(frm.find('#add_form_field_regstartdt').val());
	 	let reg_enddt=$.trim(frm.find('#add_form_field_regenddt').val());
		let reg_active =frm.find('input[name=reg_active]:checked').val();
		let active =frm.find('input[name=active]:checked').val();
		let dsk_img_url = frm.find('#dsk_banner_img').attr('src') || '';
		let dsk_img = frm.find('#add_form_field_dskimg').val() || '';
		let mob_img_url = frm.find('#mob_banner_img').attr('src') || '';
		let mob_img = frm.find('#add_form_field_mobimg').val() || '';

		if(!frm.find('#add_form_field_name').hasClass('rstrctedt') && name == ''){
			errors.push('A name is required for the event.');
			error_fields.push('#add_form_field_name');
			$("#add_form_field_name").addClass("error-field");

		}else if(!frm.find('#add_form_field_name').hasClass('rstrctedt') && !evfuncs.name_pattern.test(name)){
			errors.push('Name contains invalid characters.');
			error_fields.push('#add_form_field_name');
			$("#add_form_field_name").addClass("error-field");

		}else if(!frm.find('#add_form_field_description').hasClass('rstrctedt') && description==''){
			errors.push('Event description is required.');
			error_fields.push('#add_form_field_description');
			$("#add_form_field_description").addClass("error-field");
		}else if(!frm.find('#add_form_field_venue').hasClass('rstrctedt') && venue==''){
			errors.push('Event venue is required.');
			error_fields.push('#add_form_field_venue');
			$("#add_form_field_venue").addClass("error-field");
		}else if(!frm.find('#add_form_field_dskimg').hasClass('rstrctedt') && ((mode=='add-rec'  &&  dsk_img=='') || (mode='edit-rec' && dsk_img=='' && dsk_img_url==''))  ){
			errors.push('An image for the desktop screens is required.');
			error_fields.push('#add_form_field_dskimg');
			$("#add_form_field_dskimg").addClass("error-field");
		}else if(!frm.find('#add_form_field_mobimg').hasClass('rstrctedt') && ((mode=='add-rec'  &&  mob_img=='') || (mode='edit-rec' && mob_img=='' && mob_img_url==''))  ){
			errors.push('An image for the mobile screens is required.');
			error_fields.push('#add_form_field_mobimg');
			$("#add_form_field_mobimg").addClass("error-field");
		}else if(!frm.find('#add_form_field_startdt_picker').hasClass('rstrctedt') && startdt==''){
			errors.push('The start date is required.');
			error_fields.push('#add_form_field_startdt_picker');
			$("#add_form_field_startdt_picker").addClass("error-field");
		}else if(!frm.find('#add_form_field_enddt_picker').hasClass('rstrctedt') && enddt==''){
			errors.push('The end date is required.');
			error_fields.push('#add_form_field_enddt_picker');
			$("#add_form_field_enddt_picker").addClass("error-field");
		}else if(!frm.find('#add_form_field_timetext').hasClass('rstrctedt') && time_text==''){
			errors.push('The time info is required.');
			error_fields.push('#add_form_field_timetext');
			$("#add_form_field_timetext").addClass("error-field");
		}else if(!frm.find('#add_form_field_maxtktperperson').hasClass('rstrctedt') && max_tkt_per_person==''){
			errors.push('Max persons per member is required.');
			error_fields.push('#add_form_field_maxtktperperson');
			$("#add_form_field_maxtktperperson").addClass("error-field");

		}else if(!frm.find('#add_form_field_maxtktperperson').hasClass('rstrctedt') && !evfuncs.int_pattern.test(max_tkt_per_person)){
			errors.push('Max persons per member is invalid.');
			error_fields.push('#add_form_field_maxtktperperson');
			$("#add_form_field_maxtktperperson").addClass("error-field");

		}else if(!frm.find('#add_form_field_tktprice').hasClass('rstrctedt') && tkt_price==''){
			errors.push('Price per person is required.');
			error_fields.push('#add_form_field_tktprice');
			$("#add_form_field_tktprice").addClass("error-field");

		}else if(!frm.find('#add_form_field_tktprice').hasClass('rstrctedt') && !evfuncs.int_pattern.test(tkt_price)){
			errors.push('Price per person is invalid.');
			error_fields.push('#add_form_field_tktprice');
			$("#add_form_field_tktprice").addClass("error-field");

		}else if(eb_tkt_price_chk && eb_tkt_price==''){
			errors.push('Early bird price per person is required.');
			error_fields.push('#add_form_field_ebtktprice');
			$("#add_form_field_ebtktprice").addClass("error-field");

		}else if(eb_tkt_price_chk && !evfuncs.int_pattern.test(eb_tkt_price)){
			errors.push('Early bird price per person is invalid.');
			error_fields.push('#add_form_field_ebtktprice');
			$("#add_form_field_ebtktprice").addClass("error-field");

		}else if(eb_tkt_price_chk && !eb_validtill_chk && !eb_regcnt_chk){
			errors.push('Please select at least one rule to apply to the early bird pricing.');
			error_fields.push('#add_form_field_ebenddtchk');
			$("#add_form_field_ebenddtchk,#add_form_field_ebmaxcntchk").addClass("error-field");

		}else if(eb_validtill_chk && eb_valid_till==''){
			errors.push('Please select the end date for the early bird offer.');
			error_fields.push('#add_form_field_ebenddt_picker');
			$("#add_form_field_ebenddt_picker").addClass("error-field");

		}else if(eb_regcnt_chk && eb_reg_cnt==''){
			errors.push('Please enter the number of persons to whom the early bird offer would apply.');
			error_fields.push('#add_form_field_ebmaxcnt');
			$("#add_form_field_ebmaxcnt").addClass("error-field");

		}else if(!frm.find('#add_form_field_gstperc').hasClass('rstrctedt') && gst_perc==''){
			errors.push('GST percentage is required.');
			error_fields.push('#add_form_field_gstperc');
			$("#add_form_field_gstperc").addClass("error-field");

		}else if(!frm.find('#add_form_field_gstperc').hasClass('rstrctedt') && !evfuncs.gst_pattern.test(gst_perc)){
			errors.push('GST percentage is invalid.');
			error_fields.push('#add_form_field_gstperc');
			$("#add_form_field_gstperc").addClass("error-field");

		}else if(!frm.find('#add_form_field_convfee').hasClass('rstrctedt') && conv_fee==''){
			errors.push('Convenience fee per person is required.');
			error_fields.push('#add_form_field_convfee');
			$("#add_form_field_convfee").addClass("error-field");

		}else if(!frm.find('#add_form_field_convfee').hasClass('rstrctedt') && !evfuncs.int_pattern.test(conv_fee)){
			errors.push('Convenience fee per person is invalid.');
			error_fields.push('#add_form_field_convfee');
			$("#add_form_field_convfee").addClass("error-field");

		}

		return {'errors': errors, 'error_fields': error_fields};

	},

	openAddUserForm:function(e){
		if(typeof e=='object' && e.hasOwnProperty('data') && e.data.hasOwnProperty('self')){
			var self=e.data.self;
		}else{
			var self=evfuncs;
		}
		document.addrecform.reset();
		
		evfuncs.removeEditRestrictions();

		evfuncs.dep_rowno_max=-1;
		$(".form-control").removeClass("error-field");
		$("#refresh-list-button").addClass('d-none');
		$("#add-record-button").addClass('d-none');
		$("#edit-record-button").addClass('d-none');
		$("#rec_list_container").addClass('d-none');
		$("#rec_detail_add_edit_container").removeClass('d-none').find("#panel-heading-text").text('Create Event').end();
		$('#msgFrm').removeClass('d-none');
			
		$(".back-to-list-button").removeClass('d-none');
		$("#rec_detail_add_edit_container").find("#record-save-button>span:eq(0)").html('Create Event').end().find("#add_edit_mode").val('createrec').end().find("#add_edit_recordid").val('').end().find("#record-add-cancel-button").data('back-to','').attr('href',"events.php#"+evfuncs.prev_page_hash);
		$("form[name=addrecform]").data('mode','add-rec').find(".error-field").removeClass('error-field').end().find('input[name=active]').attr('checked',false).end().get(0).reset();

		$("#add_form_field_status_n").prop('checked',false);
		$("#add_form_field_status_y").prop('checked',true);

		$("#add_form_field_regactive_n").prop('checked',false);
		$("#add_form_field_regactive_y").prop('checked',true);
		

		$('#rec_detail_add_edit_container .ad_banner_image').addClass('d-none'); // hide the banner image viewer sections
		$('#dsk_banner_img').attr('src',""); // empty the dsk image src
		$('#mob_banner_img').attr('src',""); // empty the mob image src
		$('#add_form_field_dskimg').val(''); // empty the dsk image file input
		$('#add_form_field_mobimg').val(''); // empty the mob image file input
		$("#add_form_field_startdt_picker").datepicker('setDate', null).datepicker('option', 'minDate', "-0d");
		$("#add_form_field_enddt_picker").datepicker('setDate', null).datepicker('option', 'minDate', "-0d");
		$("#add_form_field_regstartdt_picker").datepicker('setDate', null).datepicker('option', 'minDate', "-0d");
		$("#add_form_field_regenddt_picker").datepicker('setDate', null).datepicker('option', 'minDate', "-0d");
		$("#event_booking_link_cont").find('a').data('bk_lnk', '').end().addClass('d-none');
		$('#add_form_field_ebtktpricechk').attr('checked', false); //.trigger('click');
		evfuncs.enableDisableEarlyBirdOffer();
		$('#early_bird_pricing_rules').addClass('d-none');
				
		self.setheaderBarText("");
		$('#add_form_field_name').focus();
		
		document.querySelector('.main-content').scrollIntoView(true);
		return false;

	},
	deleteUser:function(ev){
		var elem = $(ev.currentTarget);
		var id =elem.data('recid');
		// alert(id);
		if(confirm('Do you want to delete this user?')){

			var rec_details = {};
			common_js_funcs.callServer({cache:'no-cache',async:false,dataType:'json',type:'post',url:evfuncs.ajax_data_script,params:{mode:'deleteUser', user_id:id},
				successResponseHandler:function(resp,status,xhrobj){
					if(resp.error_code == 0)
						evfuncs.handleDeleteResp(resp);
					else
						alert(resp.message);
				},
				successResponseHandlerParams:{}});
			return rec_details;
		}

	},
	handleDeleteResp:function(resp){
		// console.log(resp);return false;
		alert(resp.message);
		evfuncs.refreshList();
	},

	closeAddUserForm:function(){
		var self =this;
		return true;

	},


	enableDisableEarlyBirdOffer: function(){
		$('#add_form_field_ebenddtchk').prop('checked', false); //.trigger('click');
		evfuncs.allowDisallowEarlyBirdEndDate(false, null);
		$('#add_form_field_ebmaxcntchk').prop('checked', false); //.trigger('click');
		evfuncs.allowDisallowEarlyBirdRegCnt(false,'');
		$('#add_form_field_ebtktprice').val('').prop('disabled', !$('#add_form_field_ebtktpricechk').is(':checked')).toggleClass('non-editable', !$('#add_form_field_ebtktpricechk').is(':checked')).get(0).focus();
	},

	allowDisallowEarlyBirdEndDate: function(status, dt=null){
		$('#add_form_field_ebenddt_picker').datepicker('setDate', dt);
		$('#add_form_field_ebenddt_picker').toggleClass('non-editable', !status).datepicker( "option", "disabled", !status);
		evfuncs.setUnsetEarlyBirdRulesText();
	},

	allowDisallowEarlyBirdRegCnt: function(status, cnt=''){
		$('#add_form_field_ebmaxcnt').val(cnt).toggleClass('non-editable', !status);
		if(status)
			document.getElementById('add_form_field_ebmaxcnt').focus();
		evfuncs.setUnsetEarlyBirdRulesText();
	},

	setUnsetEarlyBirdRulesText: function(){
		// let text= `Offer applies to the first 100 registrations done within 1st January 2025.`;
		let reg_cnt_text = dt_text = msg_text = msg_text1 = '';
		let msg_text2 = `persons who have registered`;
		if($('#add_form_field_ebenddtchk').is(':checked')){
			dt_text = $('#add_form_field_ebenddt_picker').datepicker("getDate")?.toLocaleDateString() || '';
			if(dt_text!='')
				dt_text = ` till ${dt_text}`;
		}

		if($('#add_form_field_ebmaxcntchk').is(':checked')){
			reg_cnt_text = $.trim($('#add_form_field_ebmaxcnt').val() || '');
			let reg_cnt = reg_cnt_text!==''?parseInt(reg_cnt_text, 10):'';
			if(reg_cnt_text!='' && reg_cnt>0){
				reg_cnt_text = ` first ${reg_cnt_text}`;
				msg_text1 = `A registration as a whole will qualify for the early bird offer if at least one person qualifies for the offer as per the set rules.`;
				if(parseInt(reg_cnt, 10)==1)
					msg_text2 = `person who has registered`;
			}else if(reg_cnt_text==='0'){
				reg_cnt_text = dt_text = '';
				msg_text = 'The Early Bird offer rules are set so as not to apply on any registration.';
			}

		}

		if(dt_text!='' || reg_cnt_text!='')
			msg_text= `The early bird offer will apply to the${reg_cnt_text} ${msg_text2} for the event${dt_text}. ${msg_text1}`;	
		$('#eb_applicability_text').find('span').text(msg_text).end().toggleClass('d-none', msg_text=='');
		
	},

	setheaderBarText:function(text){
		$("#header-bar-text").find(":first-child").html(text);
		// $('#panel-heading-text').text("Add user");

	},

	
	onHashChange:function(e){
		var hash=location.hash.replace(/^#/,'');
		// alert(hash);
		if(evfuncs.curr_page_hash!=evfuncs.prev_page_hash){
			evfuncs.prev_page_hash=evfuncs.curr_page_hash;
		}
		evfuncs.curr_page_hash=hash;

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
			case 'addrec':
								$('.alert-success, .alert-danger').addClass('d-none');
								$('#msgFrm').removeClass('d-none');
								evfuncs.openAddUserForm();
								break;

			case 'edit':
							$('.alert-success, .alert-danger').addClass('d-none');
							$('#msgFrm').removeClass('d-none');
							if(hash_params.hasOwnProperty('recid') && hash_params.recid!=''){
								evfuncs.openRecordForEditing(hash_params.recid);

							}else{
								location.hash=evfuncs.prev_page_hash;
							}
							break;



			default:
					if(evfuncs.default_list){
						evfuncs.default_list = false;
						if(hash==''){
							$("#search-field_active").val('y'); // Only active events to be listed by default
							evfuncs.doSearch();
							break; // Break out of this case section
						}
					}
					$('.alert-success, .alert-danger').addClass('d-none');
					$('#msgFrm').removeClass('d-none');
					var params={mode:'getList',pno:1, searchdata:"[]", sortdata:JSON.stringify(evfuncs.sortparams), listformat:'html'};

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

					evfuncs.searchparams=JSON.parse(params['searchdata']);
					evfuncs.sortparams=JSON.parse(params['sortdata']);

					if(evfuncs.sortparams.length==0){
						evfuncs.sortparams.push(evfuncs.default_sort);
						params['sortdata']=JSON.stringify(evfuncs.sortparams);
					}

					if(evfuncs.searchparams.length>0){
							$.each(evfuncs.searchparams , function(idx,data) {
									//console.log(data);
									switch (data.searchon) {

										case 'name':
											$("#search-field_name").val(data.searchtext);
											break;
										case 'description':
											$("#search-field_description").val(data.searchtext);
											break;
										case 'venue':
											$("#search-field_venue").val(data.searchtext);
											break;
										case 'falls_in_period':
											$("#search-field_periodstart_picker").datepicker('setDate', data.searchtext[0]!=''?new Date(data.searchtext[0]):null);
											$("#search-field_periodend_picker").datepicker('setDate', data.searchtext[1]!=''?new Date(data.searchtext[1]):null);
											break;
										case 'active':
											$("#search-field_active").val(data.searchtext);
											break;	
									}

							});
							//$('#close_box').removeClass('d-none');
						$("#search_field").val(evfuncs.searchparams[0]['searchon'] || '');
					}
					// params['searchdata']=encodeURIComponent(params['searchdata']);
					// params['sortdata']=encodeURIComponent(params['sortdata']);

					if(evfuncs.searchparams.length>0){
						if(evfuncs.searchparams[0]['searchon'] == 'status')
							$("#search_text").val(evfuncs.searchparams[0]['searchtext'][0]=='1'?'Active':'Inactive');
						else
							$("#search_text").val(evfuncs.searchparams[0]['searchtext'] || '');

						$("#search_field").val(evfuncs.searchparams[0]['searchon'] || '');
						//$('#close_box').removeClass('d-none');

					}

					$("#common-processing-overlay").removeClass('d-none');

					common_js_funcs.callServer({cache:'no-cache',async:true,dataType:'json',type:'post', url:self.ajax_data_script,params:params,successResponseHandler:evfuncs.showList,successResponseHandlerParams:{self:evfuncs}});

					var show_srch_form = false;
					if (typeof(Storage) !== "undefined") {
						srch_frm_visible = localStorage.event_search_toggle;
					} else {
						srch_frm_visible = Cookies.get('event_search_toggle');
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