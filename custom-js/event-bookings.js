var evbkngfuncs={
	searchparams:[],  /* [{searchon:'',searchtype:'',searchtext:''},{},..] */
	sortparams:[],  /* [{sorton:'',sortorder:''},{},..] */
	default_sort:{sorton:'registered_on',sortorder:'DESC'},
	paginationdata:{},
	summary_searchparams:[],  /* [{searchon:'',searchtype:'',searchtext:''},{},..] */
	summary_sortparams:[],  /* [{sorton:'',sortorder:''},{},..] */
	summary_default_sort:{sorton:'ev_start_dt',sortorder:'DESC'},
	summary_paginationdata:{},
	defaultleadtabtext:'Event Registrations',
	filtersapplied:[],
	statuschangestarted:0,
	ajax_data_script:'event-bookings.php',
	curr_page_hash:'',
	prev_page_hash:'',
	hash_params:{},
	recid:'',   // the recid for the bookings list view
	
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
			localStorage.evbkng_search_toggle = elem.hasClass('search-form-visible') ? 'visible' : '';
		} else {
			Cookies.set('evbkng_search_toggle', elem.hasClass('search-form-visible') ? 'visible' : '', {path : '/'/*, secure: true*/});
		}
	},


	toggleSummarySearch: function(ev){
		var elem = $(ev.currentTarget);
		elem.toggleClass('search-form-visible', !elem.hasClass('search-form-visible'));
		$('#search_summary_records').closest('.panel-search').toggleClass('d-none', !elem.hasClass('search-form-visible'));
		var search_form_cont = $('#search_summary_records').closest('.panel-search');
		if(search_form_cont.hasClass('d-none'))
			elem.prop('title','Open search panel');
		else{
			elem.prop('title','Close search panel');
			$("#search-field_evname").focus();
		}
		if (typeof(Storage) !== "undefined") {
			localStorage.evbkngsumr_search_toggle = elem.hasClass('search-form-visible') ? 'visible' : '';
		} else {
			Cookies.set('evbkngsumr_search_toggle', elem.hasClass('search-form-visible') ? 'visible' : '', {path : '/'/*, secure: true*/});
		}
	},

	
	getSummaryList:function(options){
		var self=this;
		var pno=1;
		var params=[];
		if('pno' in options){
			params.push('pno='+encodeURIComponent(options.pno));
		}else{
			params.push('pno=1');
		}

		params.push('searchdata='+encodeURIComponent(JSON.stringify(self.summary_searchparams)));
		params.push('sortdata='+encodeURIComponent(JSON.stringify(self.summary_sortparams)));

		params.push('ref='+Math.random());

		$("#common-processing-overlay").removeClass('d-none');

		location.hash=params.join('&');


	},


	showSummaryList:function(resp,otherparams){
		//console.log(resp);
		var self=evbkngfuncs;
		var listhtml=resp[1].list;
		$("#event_bookings_summary_list_container").removeClass('d-none');
		$("#rec_list_container, #rec_detail_view_container, #common-processing-overlay, #add-record-button, #refresh-list-button").addClass('d-none');
		$("#summaryreclistbox").html(listhtml);
		
		// if(resp[1].tot_rec_cnt>0){
		// 	$('#summary_heading_rec_cnt').text((resp[1]['reccount']==resp[1]['tot_rec_cnt'])?`(${resp[1]['tot_rec_cnt']})`:`(${resp[1]['reccount'] || 0} of ${resp[1]['tot_rec_cnt']})`);
			
		// }else{
		// 	$('#summary_heading_rec_cnt').text('(0)')
		// }
			
		$("#rec_list_container .back-to-list-button").addClass('d-none').attr('href',"event-bookings.php#"+evbkngfuncs.curr_page_hash);
		// $("#edit-record-button").addClass('d-none');
		self.summary_paginationdata=resp[1].paginationdata;

		self.setSummarySortOrderIcon();


	},



	getList:function(options){
		var self=this;
		var pno=1;
		var params=['mode=bookings'];
		if('pno' in options){
			params.push('pno='+encodeURIComponent(options.pno));
		}else{
			params.push('pno=1');
		}

		if(evbkngfuncs.recid!='')
			params.push('recid='+encodeURIComponent(evbkngfuncs.recid));

		params.push('searchdata='+encodeURIComponent(JSON.stringify(self.searchparams)));
		params.push('sortdata='+encodeURIComponent(JSON.stringify(self.sortparams)));

		params.push('ref='+Math.random());

		$("#common-processing-overlay").removeClass('d-none');

		location.hash=params.join('&');


	},


	user_count:0,
	showList:function(resp,otherparams){
		//console.log(resp);
		var self=evbkngfuncs;
		var listhtml=resp[1].list;
		self.user_count=resp[1]['reccount'];
		$("#rec_list_container").removeClass('d-none');
		$("#event_bookings_summary_list_container, #rec_detail_view_container").addClass('d-none');
		$("#common-processing-overlay").addClass('d-none');
		// $('#search_field').select2({minimumResultsForSearch: -1});
		$("#userlistbox").html(listhtml);
		
		if(resp[1].tot_rec_cnt>0){
			$('#heading_rec_cnt').text((resp[1]['reccount']==resp[1]['tot_rec_cnt'])?`(${resp[1]['tot_rec_cnt']})`:`(${resp[1]['reccount'] || 0} of ${resp[1]['tot_rec_cnt']})`);
			evbkngfuncs.setExportLink(resp[1]['reccount']>0?true:false);
			
		}else{
			$('#heading_rec_cnt').text('(0)')
			evbkngfuncs.setExportLink(false);
		}
			
		$("#add-record-button").removeClass('d-none');
		$("#refresh-list-button").removeClass('d-none');
		$("#rec_detail_view_container .back-to-list-button").addClass('d-none').attr('href',"event-bookings.php#"+evbkngfuncs.curr_page_hash);
		$("#rec_list_container .back-to-list-button").removeClass('d-none');
		// $("#record-add-cancel-button").addClass('d-none').attr('href',"ad-banners.php#"+evbkngfuncs.curr_page_hash);
		$("#edit-record-button").addClass('d-none');
		self.paginationdata=resp[1].paginationdata;
		if(resp[1].bookings_for_event!=='')
			$('#booking_for_event').text(`Showing registrations for the event "${resp[1].bookings_for_event}".`).removeClass('d-none');
		else
			$('#booking_for_event').text(`Showing registrations for all the events.`).removeClass('d-none');
		

		self.setSortOrderIcon();


	},


	onListRefresh:function(resp,otherparams){
		var self=evbkngfuncs;
		$("#common-processing-overlay").addClass('d-none');
		var listhtml=resp[1].list;
		$("#userlistbox").html(listhtml);
		self.paginationdata=resp[1].paginationdata;
		self.setSortOrderIcon();
	},
//summary_searchparams
//summary_sortparams
	setExportLink: function(show){
		const dnld_elem = $('#export_booking');
		if(dnld_elem.length<=0) // the download link element does not exist, the user might not be in ADMIN role
			return;
		let url = '#';
		if(show===true){
			let params = [];
			params.push('mode=export');
			params.push('recid='+evbkngfuncs.recid);
			params.push('searchdata='+encodeURIComponent(JSON.stringify(this.searchparams)));
			params.push('sortdata='+encodeURIComponent(JSON.stringify(this.sortparams)));
			params.push('ref='+Math.random());
			url = `${window.location.origin}${window.location.pathname}?${params.join('&')}`;
			
		}
		dnld_elem.attr('href',url).toggleClass('d-none', show!==true);
	},


	expandFilterBox:function(){
		var self=evbkngfuncs;
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
		var self=evbkngfuncs;
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
		var self=evbkngfuncs;
		self.searchparams=[];
	},

	setSearchParams:function(obj){
		var self=evbkngfuncs;
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
				if(remove_all==='evreg_falls_in_period'){
					$("#search-field_evregperiodstart_picker").datepicker('setDate', null);
					$("#search-field_evregperiodend_picker").datepicker('setDate', null);
				}else
					$('#rec_list_container .panel-search .srchfld[data-fld='+remove_all+']').val('');
			}
		}

		// var self=evbkngfuncs;
		// self.filtersapplied=[]; // remove the filter bar messages
		if(remove_all===true){
			evbkngfuncs.resetSearchParamsObj();
			document.querySelector('#rec_list_container .search-form').reset();
			$("#search-field_evregperiodstart_picker").datepicker('setDate', null);
			$("#search-field_evregperiodend_picker").datepicker('setDate', null);
		}else{
			evbkngfuncs.searchparams = evbkngfuncs.searchparams.filter(fltr=>{
				return fltr.searchon !== remove_all;
			});
		}
		var options={pno:1};
		evbkngfuncs.getList(options);
		return false;
	},


	doSearch:function(){

		evbkngfuncs.resetSearchParamsObj();
		let period_text = ['',''];
		let period = false;
		let fld = '';
		$('#rec_list_container .panel-search .srchfld').each(function(i, el){
			let val = $.trim($(el).val());
			if(val!=''){
				fld = $(el).data('fld');
				if(fld=='evreg_period_start')
					period_text[0] = val;
				else if(fld=='evreg_period_end')
					period_text[1] = val;
				if(fld=='evreg_period_start' || fld=='evreg_period_end'){
					if(period_text[0]!='' && period_text[1]!='')
					evbkngfuncs.setSearchParams({searchon:'registered_on',searchtype:'BETWEEN',searchtext:period_text});
				}else{

					evbkngfuncs.setSearchParams({searchon:$(el).data('fld'),searchtype:$(el).data('type'),searchtext:val});
				}

			}
		});

		if(evbkngfuncs.searchparams.length<=0)
			return false;

		var options={pno:1};
		evbkngfuncs.getList(options);
		//self.toggleSearch(this);
		return false;
	},


	resetSuammarySearchParamsObj:function(){
		var self=evbkngfuncs;
		self.summary_searchparams=[];
	},

	setSummarySearchParams:function(obj){
		var self=evbkngfuncs;
		self.summary_searchparams.push(obj);

	},

	clearSummarySearch:function(e){
		let remove_all = true;
		if(e){
			e.stopPropagation();
			elem = e.currentTarget;
			if($(elem).hasClass('remove_filter')){
				remove_all = $(elem).data('fld');
				$(elem).parent('.searched_elem').remove();
				if(remove_all==='evreg_falls_in_period'){
					$("#search-field_evregperiodstart_picker").datepicker('setDate', null);
					$("#search-field_evregperiodend_picker").datepicker('setDate', null);
				}else
					$('#event_bookings_summary_list_container .panel-search .srchfld[data-fld='+remove_all+']').val('');
			}
		}

		// var self=evbkngfuncs;
		// self.filtersapplied=[]; // remove the filter bar messages
		if(remove_all===true){
			evbkngfuncs.resetSuammarySearchParamsObj();
			document.querySelector('#event_bookings_summary_list_container .search-form').reset();
			$("#search-field_evregperiodstart_picker").datepicker('setDate', null);
			$("#search-field_evregperiodend_picker").datepicker('setDate', null);
		}else{
			evbkngfuncs.summary_searchparams = evbkngfuncs.summary_searchparams.filter(fltr=>{
				return fltr.searchon !== remove_all;
			});
		}
		var options={pno:1};
		evbkngfuncs.getSummaryList(options);
		return false;
	},


	doSummarySearch:function(){

		evbkngfuncs.resetSuammarySearchParamsObj();
		let period_text = ['',''];
		let period = false;
		let fld = '';
		$('#event_bookings_summary_list_container .panel-search .srchfld').each(function(i, el){
			let val = $.trim($(el).val());
			if(val!=''){
				fld = $(el).data('fld');
				if(fld=='evreg_period_start')
					period_text[0] = val;
				else if(fld=='evreg_period_end')
					period_text[1] = val;
				if(fld=='evreg_period_start' || fld=='evreg_period_end'){
					if(period_text[0]!='' && period_text[1]!='')
					evbkngfuncs.setSummarySearchParams({searchon:'registered_on',searchtype:'BETWEEN',searchtext:period_text});
				}else{

					evbkngfuncs.setSummarySearchParams({searchon:$(el).data('fld'),searchtype:$(el).data('type'),searchtext:val});
				}

			}
		});

		if(evbkngfuncs.summary_searchparams.length<=0)
			return false;

		var options={pno:1};
		evbkngfuncs.getSummaryList(options);
		return false;
	},


	


	changePage:function(ev){
		ev.preventDefault();
		if(!$(ev.currentTarget).parent().hasClass('disabled')){
			var self=evbkngfuncs;
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

		evbkngfuncs.sortparams=[];
		evbkngfuncs.sortparams.push({sorton:sorton, sortorder:sortorder});
		var options={pno:pno};
		evbkngfuncs.getList(options);

	},



	setSortOrderIcon:function(){
		var self=evbkngfuncs;
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



	sortSummaryTable:function(e){
		var self=e.data.self;

		var elemid=e.currentTarget.id;
		var elemidparts=elemid.split('_');
		var sorton=elemidparts[1].replace(/-/g,'_');
		var sortorder='ASC';

		if($(e.currentTarget).find("i:eq(0)").hasClass('fa-sort-up')){
			sortorder='DESC';
		}

		var pno = 1;
		// if(self.summary_sortparams[0].sorton==sorton){
		// 	if(self.summary_paginationdata.curr_page!='undefined' && self.summary_paginationdata.curr_page>1){
		// 		pno = self.summary_paginationdata.curr_page;
		// 	}
		// } Page number should be reset if the sorting feature is used

		evbkngfuncs.summary_sortparams=[];
		evbkngfuncs.summary_sortparams.push({sorton:sorton, sortorder:sortorder});
		var options={pno:pno};
		evbkngfuncs.getSummaryList(options);

	},



	setSummarySortOrderIcon:function(){
		var self=evbkngfuncs;
		if(self.summary_sortparams.length>0){
			var sorton = self.summary_sortparams[0].sorton.replace(/_/g,'-');
			var colheaderelemid='colheader_'+sorton;

			if(self.summary_sortparams[0].sortorder=='DESC'){
				var sort_order_class='fa-sort-down';
			}else{
				var sort_order_class='fa-sort-up';
			}
			$("#"+colheaderelemid).siblings('th.sortable').removeClass('sorted-col').find('i:eq(0)').removeClass('fa-sort-down fa-sort-up').addClass('fa-sort').end().end().addClass('sorted-col').find('i:eq(0)').removeClass('fa-sort-down fa-sort-up').addClass(sort_order_class);


		}
	},


	openRecordForViewing:function(recordid){
		var self=evbkngfuncs;
		if(recordid=='')
			return false;

		// $("#record-save-button").addClass('d-none').attr('disabled', 'disabled');
		$("#common-processing-overlay").removeClass('d-none');
		$("#record-add-cancel-button").attr('href',"event-bookings.php#"+evbkngfuncs.prev_page_hash);
		var coming_from='';
		var options={mode:'viewrecord',recordid:recordid,loadingmsg:"Opening the lead '"+recordid+"' for viewing...",leadtabtext:'View Event Details',coming_from:coming_from}
		self.openRecord(options);
		 return false;

	},

	openRecordForEditing:function(recordid){
		var self=evbkngfuncs;
		if(recordid=='')
			return false;

		document.addrecform.reset();
		$(".form-control").removeClass("error-field");
		$("#record-save-button").removeClass('d-none').attr('disabled', false);
		// $("#add_form_field_role").next('.select2-container').removeClass("error-field");
		$("#common-processing-overlay").removeClass('d-none');
		$("#record-add-cancel-button").attr('href',"event-bookings.php#"+evbkngfuncs.prev_page_hash);
		$('#msgFrm').removeClass('d-none');
		var coming_from='';//elem.data('in-mode');
		var options={mode:'editrecord',recordid:recordid,leadtabtext:'Edit Event\'s Details',coming_from:coming_from}
		self.openRecord(options);
		return false;

	},


	openRecord:function(options){
		var self=evbkngfuncs;
		var opts={leadtabtext:'Event Details'};
		$.extend(true,opts,options);

		evbkngfuncs.dep_rowno_max=-1;

		var params={mode:"getRecordDetails",recordid:opts.recordid};
		var options={cache:'no-cache',async:true,type:'post',dataType:'json',url:self.ajax_data_script,params:params,successResponseHandler:self.showLeadDetailsWindow,successResponseHandlerParams:{self:self,mode:opts.mode,recordid:opts.recordid,coming_from:opts.coming_from,header_bar_text:opts.leadtabtext}};
		common_js_funcs.callServer(options);

	},


	showLeadDetailsWindow:function(resp,otherparams){
		const self=otherparams.self;
		let container_id='';
		$("#common-processing-overlay").addClass('d-none');
		const rec_id= resp[1].record_details.id ??''; // event reg table's id
		
		if(otherparams.mode=='viewrecord'){
			var coming_from=otherparams.coming_from;


			if(rec_id!=''){

				let booking_id = resp[1].record_details.booking_id || '';
				let name = resp[1].record_details.name || '';
				let name_disp = resp[1].record_details.name_disp || '';
				let mem_name_disp = resp[1].record_details.mem_name_disp || '';
				let memno_disp = resp[1].record_details.memno_disp || '';
				let mem_mobile = resp[1].record_details.mem_mobile || '';
				let mem_email = resp[1].record_details.mem_email || '';
				let dsk_img_url = resp[1].record_details.dsk_img_url || '';
				let dsk_img_org_width = resp[1].record_details.dsk_img_org_width || '';
				let mob_img_url = resp[1].record_details.mob_img_url || '';
				let mob_img_org_width = resp[1].record_details.mob_img_org_width || '';
				let description_disp = resp[1].record_details.description_disp || '';
				let venue_disp = resp[1].record_details.venue_disp || '';
				let period_disp = resp[1].record_details.period_disp || '';
				let ev_start_dt = resp[1].record_details.ev_start_dt || '';
				let ev_end_dt = resp[1].record_details.ev_end_dt || '';
				let time_text_disp = resp[1].record_details.time_text_disp || '';
				let reg_end_dt_disp = resp[1].record_details.reg_end_dt_disp || '';

				let booking_status = resp[1].record_details.reg_status || '';
				let booking_date = resp[1].record_details.booking_date || '';
				let no_of_tickets = resp[1].record_details.no_of_tickets || '';
				let attended = resp[1].record_details.attended || 0;
				let no_show = (no_of_tickets==''?0:no_of_tickets) - attended;
				let price_per_tkt = resp[1].record_details.price_per_tkt || '';
				let offer = resp[1].record_details.offer || '';
				let conv_fee = resp[1].record_details.conv_fee || '';
				let amount_paid = resp[1].record_details.amount_paid || '';
				let payment_mode = resp[1].record_details.payment_mode || '';
				let payment_status = resp[1].record_details.payment_status || '';
				
				let pmt_req_amount = resp[1].record_details.payment_details?.pmt_req_amount || '';
				let pmt_failure_msg = resp[1].record_details.payment_details?.pmt_failure_msg || '';
				let pmt_total_taxes = resp[1].record_details.payment_details?.pmt_total_taxes || '';
				let pmt_fees = resp[1].record_details.payment_details?.pmt_fees || '';
				let pmt_instrument_type = resp[1].record_details.payment_details?.pmt_instrument_type || '';
				let pmt_billing_instrument = resp[1].record_details.payment_details?.pmt_billing_instrument || '';
				let pmt_bank_reference_number = resp[1].record_details.payment_details?.pmt_bank_reference_number || '';


				var contobj=$("#rec_detail_view_container");

				contobj.find("#view_form_field_bookingid").text(booking_id);
				contobj.find("#view_form_field_eventname").html(name_disp);
				contobj.find("#view_form_field_evdescription").html(description_disp);
				contobj.find("#view_form_field_evvenue").html(venue_disp);
				contobj.find("#view_form_field_evperiod").text(period_disp);
				contobj.find("#view_form_field_evperiod_label").text(ev_start_dt!=ev_end_dt?'Dates: ':'Date: ');
				contobj.find("#view_form_field_evtime").text(time_text_disp);
				contobj.find("#view_form_field_ppt").html((price_per_tkt<=0?`<span class="free" >Free</span>`:`${new Intl.NumberFormat("en-IN", { style: "currency", currency: "INR", "maximumFractionDigits":0 }).format(price_per_tkt)}`) + (offer=='EB'?`<span style="margin-left:10px;"  >(Early Bird Offer)</span>`:''));
				// contobj.find("#view_form_field_evregperiod").text(reg_end_dt_disp);
				contobj.find("#view_form_field_regstatus").text(booking_status);
				contobj.find("#view_form_field_registeredon").text(booking_date);
				contobj.find("#view_form_field_bookedby").html(mem_name_disp);
				contobj.find("#view_form_field_membershipno").html(memno_disp).toggleClass('d-none', memno_disp=='');
				contobj.find("#view_form_field_membermobile").html(`<a href="tel:${mem_mobile}"  target="_blank" rel="noopener"  >${mem_mobile}</a>`);
				contobj.find("#view_form_field_memberemail").html(`<a href="mailto:${mem_email}" target="_blank" rel="noopener" >${mem_email}</a>`);
				contobj.find("#view_form_field_nooftkt").text(no_of_tickets);
				contobj.find("#view_form_field_attended").text(attended);
				contobj.find("#view_form_field_noshow").text(no_show);
				contobj.find("#view_form_field_paymentmode").text(payment_mode);
				contobj.find("#view_form_field_evdskimg").attr({src: dsk_img_url, width: dsk_img_org_width}).end();
				contobj.find("#view_form_field_evmobimg").attr({src: mob_img_url, width: mob_img_org_width}).end();
				
				contobj.find("#view_form_field_paymentstatus").text(payment_status).toggleClass('text-success', payment_status=='Paid').toggleClass('text-danger', payment_status=='Failed');
				contobj.find("#view_form_field_pmtfailuremsg").text(pmt_failure_msg).toggleClass('d-none', payment_status!='Failed');
				contobj.find("#view_form_field_amountpaid").html(`${new Intl.NumberFormat("en-IN", { style: "currency", currency: "INR", "maximumFractionDigits":0 }).format(pmt_req_amount)}`);
				contobj.find("#view_form_field_pmtfees").text(new Intl.NumberFormat("en-IN", { style: "currency", currency: "INR", "maximumFractionDigits":2 }).format(pmt_fees)).parents('.form-group:eq(0)').toggleClass('d-none', payment_status=='Failed').end();
				contobj.find("#view_form_field_pmttax").text(new Intl.NumberFormat("en-IN", { style: "currency", currency: "INR", "maximumFractionDigits":2 }).format(pmt_total_taxes)).parents('.form-group:eq(0)').toggleClass('d-none', payment_status=='Failed').end();
				contobj.find("#view_form_field_instrtype").text(pmt_instrument_type);
				contobj.find("#view_form_field_instr").text(pmt_billing_instrument);
				contobj.find("#view_form_field_bankref").text(pmt_bank_reference_number).parents('.form-group:eq(0)').toggleClass('d-none', payment_status=='Failed').end();

				let header_text = 'View Registration ( '+booking_id+' )';
				
				// contobj.find("#record-add-cancel-button").data('back-to',coming_from);
				contobj.find("#panel-heading-text").text(header_text);
				// contobj.find("#infoMsg").html('View Registration For <b>' + name_disp +  '</b>');
				evbkngfuncs.setheaderBarText(header_text);

				container_id='rec_detail_view_container';


			}else{

				var message="Sorry, the view window could not be opened (Server error).";
				if(resp[0]==1){
					message="Sorry, the view window could not be opened (Registration reference is missing).";
				}else if(resp[0]==2){
					message="Sorry, the view window could not be opened (Server error).";
				}else if(resp[0]==3){
					message="Sorry, the view window could not be opened (Invalid registration reference).";
				}

				alert(message);
				location.hash=evbkngfuncs.prev_page_hash;
				return;

			}

		}

		if(container_id!=''){
			$(".back-to-list-button").removeClass('d-none');
			$("#refresh-list-button").addClass('d-none');
			$("#add-record-button").addClass('d-none');
			$("#rec_list_container").addClass('d-none');

			if(container_id!='rec_detail_add_edit_container'){
				$("#edit-record-button").removeClass('d-none').data('recid',otherparams.recordid);
			}else if(container_id!='rec_detail_view_container'){
				$("#rec_detail_view_container").addClass('d-none');
				$("#edit-record-button").addClass('d-none');
			}

			$("#"+container_id).removeClass('d-none');
			self.setheaderBarText(otherparams.header_bar_text);

		}

	},

	
	backToList:function(e){
		// if(typeof e=='object' && e.hasOwnProperty('data') && e.data.hasOwnProperty('self')){
			// var self=e.data.self;
		// }else{
			// var self=evbkngfuncs;
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
			var self=evbkngfuncs;
		}

		var currpage=self.paginationdata.curr_page;

		var options={pno:currpage,successResponseHandler:self.onListRefresh};
		self.getList(options);
		return false;

	},


	handleAddRecResponse:function(resp){
		var self=evbkngfuncs;
		$(".form-control").removeClass("error-field");

		if(resp.error_code==0){
			var message_container = '.alert-success';
			$("#record-add-cancel-button>i:eq(0)").next('span').html('Close');
			$("form[name=addrecform]").find(".error-field").removeClass('error-field').end().get(0).reset();
			$('#event_selector').val('').trigger('change');
			$('#add_form_field_eventname').text('');
			$('#event_details_n_reg_section').addClass('d-none');
			let thanks_msg = '';
			if(resp.other_data.no_of_tickets==1){
				thanks_msg = `A person has `;
			}else{
				thanks_msg = `${resp.other_data.no_of_tickets} persons have `;
			}
			thanks_msg += ` been successfully registered for the event ${resp.other_data.ev_name_disp}.<br>Your registration ID is ${resp.other_data.booking_id}.`;
			setTimeout(()=>{$('#booking_success_msg').removeClass('d-none').find('.booking_success_msg>.msg-text').html(thanks_msg);}, 0);


			$("#event_selector").focus();

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

	
	setheaderBarText:function(text){
		$("#header-bar-text").find(":first-child").html(text);
		// $('#panel-heading-text').text("Add user");

	},

	
	onHashChange:function(e){
		var hash=location.hash.replace(/^#/,'');
		// alert(hash);
		if(evbkngfuncs.curr_page_hash!=evbkngfuncs.prev_page_hash){
			evbkngfuncs.prev_page_hash=evbkngfuncs.curr_page_hash;
		}
		evbkngfuncs.curr_page_hash=hash;

		var hash_params={mode:''};
		if(hash!=''){
			var hash_params_temp=hash.split('&');
			var hash_params_count= hash_params_temp.length;
			for(var i=0; i<hash_params_count; i++){
				var temp=hash_params_temp[i].split('=');
				hash_params[temp[0]]=decodeURIComponent(temp[1]);
			}
		}

		evbkngfuncs.hash_params = {...hash_params}; // creating a shallow copy of the local object hash_params

		switch(hash_params.mode.toLowerCase()){
			case 'view':
							$('.alert-success, .alert-danger').addClass('d-none');

							if(hash_params.hasOwnProperty('recid') && hash_params.recid!=''){
								evbkngfuncs.openRecordForViewing(hash_params.recid);
							}else{
								location.hash=evbkngfuncs.prev_page_hash;
							}
							break;					



			case 'bookings':

					$('.alert-success, .alert-danger').addClass('d-none');
					$('#msgFrm').removeClass('d-none');
					var params={mode:'getList',pno:1, searchdata:"[]", sortdata:JSON.stringify(evbkngfuncs.sortparams), listformat:'html'};

					if(hash_params.hasOwnProperty('pno')){
						params['pno']=hash_params.pno
					}else{
						params['pno']=1;
					}

					if(hash_params.hasOwnProperty('recid') && hash_params.recid!=''){
						evbkngfuncs.recid = hash_params.recid
						params['recid']=hash_params.recid
						$('#event_name_search_cont').addClass('d-none').find('input').val('').end();
					}else{
						$('#event_name_search_cont').removeClass('d-none');
						evbkngfuncs.recid = ''
					}

					if(hash_params.hasOwnProperty('searchdata')){
						params['searchdata']=hash_params.searchdata;

					}
					if(hash_params.hasOwnProperty('sortdata')){
						params['sortdata']=hash_params.sortdata;

					}

					evbkngfuncs.searchparams=JSON.parse(params['searchdata']);
					evbkngfuncs.sortparams=JSON.parse(params['sortdata']);

					if(evbkngfuncs.sortparams.length==0){
						evbkngfuncs.sortparams.push(evbkngfuncs.default_sort);
						params['sortdata']=JSON.stringify(evbkngfuncs.sortparams);
					}

					if(evbkngfuncs.searchparams.length>0){
							$.each(evbkngfuncs.searchparams , function(idx,data) {
									//console.log(data);
									switch (data.searchon) {

										case 'booking_id':
											$("#search-field_bookingid").val(data.searchtext);
											break;
										case 'mem_name':
											$("#search-field_memname").val(data.searchtext);
											break;
										case 'mem_membership_no':
											$("#search-field_memno").val(data.searchtext);
											break;
										case 'ev_name':
											$("#search-field_name").val(data.searchtext);
											break;
										case 'registered_on':
											$("#search-field_evregperiodstart_picker").datepicker('setDate', new Date(data.searchtext[0]));
											$("#search-field_evregperiodend_picker").datepicker('setDate', new Date(data.searchtext[1]));
											
											break;
									}

							});
							//$('#close_box').removeClass('d-none');
						$("#search_field").val(evbkngfuncs.searchparams[0]['searchon'] || '');
					}
					// params['searchdata']=encodeURIComponent(params['searchdata']);
					// params['sortdata']=encodeURIComponent(params['sortdata']);

					if(evbkngfuncs.searchparams.length>0){
						if(evbkngfuncs.searchparams[0]['searchon'] == 'status')
							$("#search_text").val(evbkngfuncs.searchparams[0]['searchtext'][0]=='1'?'Active':'Inactive');
						else
							$("#search_text").val(evbkngfuncs.searchparams[0]['searchtext'] || '');

						$("#search_field").val(evbkngfuncs.searchparams[0]['searchon'] || '');
						//$('#close_box').removeClass('d-none');

					}

					$("#common-processing-overlay").removeClass('d-none');

					common_js_funcs.callServer({cache:'no-cache',async:true,dataType:'json',type:'post', url:self.ajax_data_script,params:params,successResponseHandler:evbkngfuncs.showList,successResponseHandlerParams:{self:evbkngfuncs}});

					var show_srch_form = false;
					if (typeof(Storage) !== "undefined") {
						srch_frm_visible = localStorage.evbkng_search_toggle;
						
					} else {
						srch_frm_visible = Cookies.get('evbkng_search_toggle');
						
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

					break;

			default:

					$('.alert-success, .alert-danger').addClass('d-none');
					$('#msgFrm').removeClass('d-none');

					// Reset the search and sort on the bookings screen
					evbkngfuncs.resetSearchParamsObj();
					document.querySelector('#rec_list_container .search-form').reset();
					$("#search-field_evregperiodstart_picker").datepicker('setDate', null);
					$("#search-field_evregperiodend_picker").datepicker('setDate', null);
					evbkngfuncs.sortparams = [];
					evbkngfuncs.sortparams.push(evbkngfuncs.default_sort);
					////////////////

					var params={mode:'getSummaryList',pno:1, searchdata:"[]", sortdata:JSON.stringify(evbkngfuncs.summary_sortparams), listformat:'html'};

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

					evbkngfuncs.summary_searchparams=JSON.parse(params['searchdata']);
					evbkngfuncs.summary_sortparams=JSON.parse(params['sortdata']);

					if(evbkngfuncs.summary_sortparams.length==0){
						evbkngfuncs.summary_sortparams.push(evbkngfuncs.summary_default_sort);
						params['sortdata']=JSON.stringify(evbkngfuncs.summary_sortparams);
					}

					if(evbkngfuncs.summary_searchparams.length>0){
						$.each(evbkngfuncs.summary_searchparams , function(idx,data) {
								switch (data.searchon) {

									case 'ev_name':
										$("#search-field_evname").val(data.searchtext);
										break;
								}

						});
					}

					$("#common-processing-overlay").removeClass('d-none');

					common_js_funcs.callServer({cache:'no-cache',async:true,dataType:'json',type:'post', url:self.ajax_data_script,params:params,successResponseHandler:evbkngfuncs.showSummaryList,successResponseHandlerParams:{self:evbkngfuncs}});

					var show_summary_srch_form = false;
					if (typeof(Storage) !== "undefined") {
						summary_srch_frm_visible = localStorage.evbkngsumr_search_toggle;
					} else {
						summary_srch_frm_visible = Cookies.get('evbkngsumr_search_toggle');
					}

					if(summary_srch_frm_visible && summary_srch_frm_visible == 'visible')
						show_summary_srch_form = true;
					$('#event_bookings_summary_list_container .toggle-search').toggleClass('search-form-visible', show_summary_srch_form);
					$('#search_summary_records').closest('.panel-search').toggleClass('d-none', !show_summary_srch_form);
					var search_form_cont = $('#search_summary_records').closest('.panel-search');
					if(search_form_cont.hasClass('d-none'))
						$('.toggle-search').prop('title','Open search panel');
					else{
						$('.toggle-search').prop('title','Close search panel');
						$("#search-field_evname").focus();
					}	

					// $("#search-field_fullname").focus();

		}


		//$("[data-rel='tooltip']").tooltip({html:true, placement:'top', container:'body'});




	}

}