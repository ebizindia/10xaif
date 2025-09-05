var evregfuncs={
	searchparams:[],  /* [{searchon:'',searchtype:'',searchtext:''},{},..] */
	sortparams:[],  /* [{sorton:'',sortorder:''},{},..] */
	default_sort:{sorton:'registered_on',sortorder:'DESC'},
	paginationdata:{},
	event_searchparams:[],  /* [{searchon:'',searchtype:'',searchtext:''},{},..] */
	event_sortparams:[],  /* [{sorton:'',sortorder:''},{},..] */
	event_default_sort:{sorton:'start_dt',sortorder:'DESC'},
	event_paginationdata:{},
	defaultleadtabtext:'Event Registrations',
	filtersapplied:[],
	statuschangestarted:0,
	ajax_data_script:'event-registrations.php',
	curr_page_hash:'',
	prev_page_hash:'',
	name_pattern: /^[A-Z0-9_ -]+$/i,
	int_pattern: /^\d+$/,
	gst_pattern: /^\d+(\.\d{1,2})?$/,
	pp_max_filesize:0,
	hash_params:{},
	
	initiateStatusChange:function(statuscell){
		var self=evregfuncs;

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
	// toggleSearch: function(ev){
	// 	var elem = $(ev.currentTarget);
	// 	elem.toggleClass('search-form-visible', !elem.hasClass('search-form-visible'));
	// 	$('#search_records').closest('.panel-search').toggleClass('d-none', !elem.hasClass('search-form-visible'));
	// 	var search_form_cont = $('#search_records').closest('.panel-search');
	// 	if(search_form_cont.hasClass('d-none'))
	// 		elem.prop('title','Open search panel');
	// 	else{
	// 		elem.prop('title','Close search panel');
	// 		$("#search-field_fullname").focus();
	// 	}
	// 	if (typeof(Storage) !== "undefined") {
	// 		localStorage.evreg_search_toggle = elem.hasClass('search-form-visible') ? 'visible' : '';
	// 	} else {
	// 		Cookies.set('evreg_search_toggle', elem.hasClass('search-form-visible') ? 'visible' : '', {path : '/'/*, secure: true*/});
	// 	}
	// },

	toggleSearch: function(ev){
		let elem = $(ev.currentTarget);
		let srch_sec = elem.hasClass('eventsearch')?'evreg_ev_search_toggle':'evreg_search_toggle';
		evregfuncs.setPanelVisibilityStatus(srch_sec, elem.hasClass('search-form-visible')?'':'visible'); // set closed status for the search panel
		evregfuncs.showHidePanel(srch_sec);
	},


	// toggleEventSearch: function(ev){
	// 	let elem = $(ev.currentTarget);
	// 	evregfuncs.setPanelVisibilityStatus('evreg_ev_search_toggle', elem.hasClass('search-form-visible')?'':'visible'); // set closed status for the search panel
	// 	evregfuncs.showHidePanel('evreg_ev_search_toggle');
	// },

	setPanelVisibilityStatus: function(panel, status){
		if (typeof(Storage) !== "undefined") {
			localStorage[panel] = status;
		} else {
			Cookies.set(panel, status, {path : '/'});
		}
	},

	showHidePanel: function(panel){
		if(panel === 'evreg_search_toggle'){
			let show_srch_form = false;
			if (typeof(Storage) !== "undefined") {
				srch_frm_visible = localStorage.evreg_search_toggle;
			} else {
				srch_frm_visible = Cookies.get('evreg_search_toggle');
			}
			if(srch_frm_visible && srch_frm_visible == 'visible')
				show_srch_form = true;

			$('.bookingssearch.toggle-search').toggleClass('search-form-visible', show_srch_form);
			$('#search_records').closest('.panel-search').toggleClass('d-none', !show_srch_form);
			let search_form_cont = $('#search_records').closest('.panel-search');
			if(search_form_cont.hasClass('d-none'))
				$('.bookingssearch.toggle-search').prop('title','Open search panel');
			else{
				$('.bookingssearch.toggle-search').prop('title','Close search panel');
				$("#search-field_name").focus();
			}
		}else if(panel === 'evreg_ev_search_toggle'){
			let show_srch_form = false;
			if (typeof(Storage) !== "undefined") {
				srch_frm_visible = localStorage.evreg_ev_search_toggle;
			} else {
				srch_frm_visible = Cookies.get('evreg_ev_search_toggle');
			}
			if(srch_frm_visible && srch_frm_visible == 'visible')
				show_srch_form = true;

			$('.eventsearch.toggle-search').toggleClass('search-form-visible', show_srch_form);
			$('#search_evrecords').closest('.panel-search').toggleClass('d-none', !show_srch_form);
			let search_form_cont = $('#search_evrecords').closest('.panel-search');
			if(search_form_cont.hasClass('d-none'))
				$('.eventsearch.toggle-search').prop('title','Open search panel');
			else{
				$('.eventsearch.toggle-search').prop('title','Close search panel');
				$("#search-field_evname").focus();
			}
		}
	},

	confirmAndExecuteStatusChange:function(statuscell){
		var self=evregfuncs;

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
			var options={cache:'no-cache',dataType:'json',async:true,type:'post',url:evregfuncs.ajax_data_script+"?mode=changeStatus",data:"newstatus="+newstatus+"&recordid="+userid,successResponseHandler:evregfuncs.handleStatusChangeResponse,successResponseHandlerParams:{statuscell:statuscell,rowelem:rowelem}};
			common_js_funcs.callServer(options);
			$(statuscell).removeClass("status-grn");
			$(statuscell).removeClass("status-red");
			if(parseInt(newstatus)==1){
				$(statuscell).addClass("status-grn");
			}else{
				$(statuscell).addClass("status-red");
			}
		}else{
			evregfuncs.statuschangestarted=0;
			evregfuncs.abortStatusChange(statuscell);

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
							evregfuncs.statuschangestarted=0;
							evregfuncs.abortStatusChange(statuscell);
						}
					},
					"Yes":	{
						"label": "Yes",
						"className": "btn-danger btn-primary",
						"callback": function(ev){

							var options={cache:'no-cache',dataType:'json',async:true,type:'post',url:evregfuncs.ajax_data_script+"?mode=changeStatus",data:"newstatus="+newstatus+"&recordid="+userid,successResponseHandler:evregfuncs.handleStatusChangeResponse,successResponseHandlerParams:{statuscell:statuscell,rowelem:rowelem}};
							common_js_funcs.callServer(options);
						}
					}

				}

		});*/




	},

	abortStatusChange:function(statuscell){
		var self=evregfuncs;

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
		var self=evregfuncs;

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
		params.push('mode=bookings');
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
		var self=evregfuncs;
		var listhtml=resp[1].list;
		self.user_count=resp[1]['reccount'];
		$("#rec_list_container").removeClass('d-none');
		$("#rec_detail_add_edit_container, #event_available_list_container").addClass('d-none');
		$("#rec_detail_view_container").addClass('d-none');
		$("#reg_thanks_container").addClass('d-none');
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
		$(".back-to-list-button", "#rec_detail_view_container").attr('href',"event-registrations.php#"+evregfuncs.curr_page_hash);
		$("#record-add-cancel-button").addClass('d-none').attr('href',"ad-banners.php#"+evregfuncs.curr_page_hash);
		$("#edit-record-button").addClass('d-none');
		self.paginationdata=resp[1].paginationdata;

		self.setSortOrderIcon();


	},


	onListRefresh:function(resp,otherparams){
		var self=evregfuncs;
		$("#common-processing-overlay").addClass('d-none');
		var listhtml=resp[1].list;
		$("#userlistbox").html(listhtml);
		self.paginationdata=resp[1].paginationdata;
		self.setSortOrderIcon();
	},


	getEventList:function(options){
		var self=this;
		var pno=1;
		var params=[];
		if('pno' in options){
			params.push('pno='+encodeURIComponent(options.pno));
		}else{
			params.push('pno=1');
		}

		params.push('searchdata='+encodeURIComponent(JSON.stringify(self.event_searchparams)));
		params.push('sortdata='+encodeURIComponent(JSON.stringify(self.event_sortparams)));

		params.push('ref='+Math.random());

		$("#common-processing-overlay").removeClass('d-none');

		location.hash=params.join('&');


	},


	user_count:0,
	showEventList:function(resp,otherparams){
		//console.log(resp);
		var self=evregfuncs;
		var listhtml=resp[1].list;
		self.user_count=resp[1]['reccount'];
		$("#event_available_list_container").removeClass('d-none');
		$("#rec_list_container, #rec_detail_add_edit_container, #rec_detail_view_container, #common-processing-overlay").addClass('d-none');
		$("#eventslistbox").html(listhtml);
		
		$("#add-record-button").removeClass('d-none');
		$("#refresh-list-button").removeClass('d-none');
		$(".back-to-list-button").attr('href',"event-registrations.php#"+evregfuncs.curr_page_hash);
		$("#record-add-cancel-button").addClass('d-none').attr('href',"event-registrations.php#"+evregfuncs.curr_page_hash);
		$("#edit-record-button").addClass('d-none');
		self.event_paginationdata=resp[1].paginationdata;

		self.setEventSortOrderIcon();


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
		var self=evregfuncs;
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
		var self=evregfuncs;
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
		var self=evregfuncs;
		self.searchparams=[];
	},

	setSearchParams:function(obj){
		var self=evregfuncs;
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
				if(remove_all==='ev_falls_in_period'){
					$("#search-field_evperiodstart_picker").datepicker('setDate', null);
					$("#search-field_evperiodend_picker").datepicker('setDate', null);
				}else
					$('.panel-search .srchfld[data-fld='+remove_all+']').val('');
			}
		}

		// var self=evregfuncs;
		// self.filtersapplied=[]; // remove the filter bar messages
		if(remove_all===true){
			evregfuncs.resetSearchParamsObj();
			document.search_form.reset();
			$("#search-field_evperiodstart_picker").datepicker('setDate', null);
			$("#search-field_evperiodend_picker").datepicker('setDate', null);
		}else{
			evregfuncs.searchparams = evregfuncs.searchparams.filter(fltr=>{
				return fltr.searchon !== remove_all;
			});
		}
		var options={pno:1};
		evregfuncs.getList(options);
		return false;
	},


	doSearch:function(){

		evregfuncs.resetSearchParamsObj();
		let period_text = ['',''];
		let period = false;
		let fld = '';
		$('.panel-search .srchfld').each(function(i, el){
			let val = $.trim($(el).val());
			if(val!=''){
				fld = $(el).data('fld');
				if(fld=='ev_period_start')
					period_text[0] = val;
				else if(fld=='ev_period_end')
					period_text[1] = val;
				else
					evregfuncs.setSearchParams({searchon:$(el).data('fld'),searchtype:$(el).data('type'),searchtext:val});
				/*if(fld!='ev_period_start' && fld!='ev_period_end'){
					if(period_text[0]!='' && period_text[1]!='')
					evregfuncs.setSearchParams({searchon:'ev_falls_in_period',searchtype:'CONTAINS',searchtext:period_text});
				}else{

				}*/

			}
		});

		if(period_text[0]!='' || period_text[1]!='')
			evregfuncs.setSearchParams({searchon:'ev_falls_in_period',searchtype:'CONTAINS',searchtext:period_text});

		if(evregfuncs.searchparams.length<=0)
			return false;

		var options={pno:1};
		evregfuncs.getList(options);
		//self.toggleSearch(this);
		return false;
	},


	resetEventSearchParamsObj:function(){
		evregfuncs.event_searchparams=[];
	},

	setEventSearchParams:function(obj){
		evregfuncs.event_searchparams.push(obj);

	},

	clearEventSearch:function(e){
		let remove_all = true;
		if(e){
			e.stopPropagation();
			elem = e.currentTarget;
			if($(elem).hasClass('remove_filter')){
				remove_all = $(elem).data('fld');
				$(elem).parent('.searched_elem').remove();
				if(remove_all==='ev_falls_in_period'){
					// $("#search-field_evperiodstart_picker").datepicker('setDate', null);
					// $("#search-field_evperiodend_picker").datepicker('setDate', null);
				}else
					$('.panel-search .srchfld[data-fld='+remove_all+']').val('');
			}
		}

		// var self=evregfuncs;
		// self.filtersapplied=[]; // remove the filter bar messages
		if(remove_all===true){
			evregfuncs.resetEventSearchParamsObj();
			document.event_search_form.reset();
			// $("#search-field_evperiodstart_picker").datepicker('setDate', null);
			// $("#search-field_evperiodend_picker").datepicker('setDate', null);
		}else{
			evregfuncs.event_searchparams = evregfuncs.event_searchparams.filter(fltr=>{
				return fltr.searchon !== remove_all;
			});
		}
		var options={pno:1};
		evregfuncs.getEventList(options);
		return false;
	},


	doEventSearch:function(){

		evregfuncs.resetEventSearchParamsObj();
		let period_text = ['',''];
		let period = false;
		let fld = '';
		$('.panel-search .srchfld').each(function(i, el){
			let val = $.trim($(el).val());
			if(val!=''){
				fld = $(el).data('fld');
				if(fld=='ev_period_start')
					period_text[0] = val;
				else if(fld=='ev_period_end')
					period_text[1] = val;
				else
					evregfuncs.setEventSearchParams({searchon:$(el).data('fld'),searchtype:$(el).data('type'),searchtext:val});
				/*if(fld!='ev_period_start' && fld!='ev_period_end'){
					if(period_text[0]!='' && period_text[1]!='')
					evregfuncs.setSearchParams({searchon:'ev_falls_in_period',searchtype:'CONTAINS',searchtext:period_text});
				}else{

				}*/

			}
		});

		if(period_text[0]!='' || period_text[1]!='')
			evregfuncs.setEventSearchParams({searchon:'ev_falls_in_period',searchtype:'CONTAINS',searchtext:period_text});

		if(evregfuncs.event_searchparams.length<=0)
			return false;

		var options={pno:1};
		evregfuncs.getEventList(options);
		//self.toggleSearch(this);
		return false;
	},


	changePage:function(ev){
		ev.preventDefault();
		if(!$(ev.currentTarget).parent().hasClass('disabled')){
			var self=evregfuncs;
			var pno=$(ev.currentTarget).data('page');
			console.log($(ev.currentTarget).parents('#event_available_list_container'));
			if($(ev.currentTarget).parents('#event_available_list_container').length>0)
				self.getEventList({pno:pno});
			else
				self.getList({pno:pno});
			// return false;
		}

	},

	changePageNew:function(ev){
		alert('events page');
		ev.preventDefault();
		if(!$(ev.currentTarget).parent().hasClass('disabled')){
			var self=evregfuncs;
			var pno=$(ev.currentTarget).data('page');
			self.getList({pno:pno});
			// return false;
		}

	},

	changeEventPage:function(ev){
		// alert('bookings page');
		ev.preventDefault();
		if(!$(ev.currentTarget).parent().hasClass('disabled')){
			var self=evregfuncs;
			var pno=$(ev.currentTarget).data('page');
			self.getEventList({pno:pno});
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

		evregfuncs.sortparams=[];
		evregfuncs.sortparams.push({sorton:sorton, sortorder:sortorder});
		var options={pno:pno};
		evregfuncs.getList(options);

	},



	setSortOrderIcon:function(){
		var self=evregfuncs;
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


	sortEventTable:function(e){
		var self=e.data.self;

		var elemid=e.currentTarget.id;
		var elemidparts=elemid.split('_');
		var sorton=elemidparts[1].replace(/-/g,'_');
		var sortorder='ASC';

		if($(e.currentTarget).find("i:eq(0)").hasClass('fa-sort-up')){
			sortorder='DESC';
		}

		var pno = 1;
		
		evregfuncs.event_sortparams=[];
		evregfuncs.event_sortparams.push({sorton:sorton, sortorder:sortorder});
		var options={pno:pno};
		evregfuncs.getEventList(options);

	},



	setEventSortOrderIcon:function(){
		var self=evregfuncs;
		if(self.event_sortparams.length>0){
			var sorton = self.event_sortparams[0].sorton == self.event_sortparams[0].sorton.replace(/_/g,'-');
			var colheaderelemid='colheader_'+sorton;

			if(self.event_sortparams[0].sortorder=='DESC'){
				var sort_order_class='fa-sort-down';
			}else{
				var sort_order_class='fa-sort-up';
			}
			$("#"+colheaderelemid).siblings('th.sortable').removeClass('sorted-col').find('i:eq(0)').removeClass('fa-sort-down fa-sort-up').addClass('fa-sort').end().end().addClass('sorted-col').find('i:eq(0)').removeClass('fa-sort-down fa-sort-up').addClass(sort_order_class);


		}
	},


	openRecordForViewing:function(recordid){
		var self=evregfuncs;
		if(recordid=='')
			return false;

		// $("#record-save-button").addClass('d-none').attr('disabled', 'disabled');
		$("#common-processing-overlay").removeClass('d-none');
		var coming_from='';
		var options={mode:'viewrecord',recordid:recordid,loadingmsg:"Opening the lead '"+recordid+"' for viewing...",leadtabtext:'View Event Details',coming_from:coming_from}
		self.openRecord(options);
		 return false;

	},

	openRecordForEditing:function(recordid){
		var self=evregfuncs;
		if(recordid=='')
			return false;

		document.addrecform.reset();
		$(".form-control").removeClass("error-field");
		$("#record-save-button").removeClass('d-none').attr('disabled', false);
		// $("#add_form_field_role").next('.select2-container').removeClass("error-field");
		$("#common-processing-overlay").removeClass('d-none');
		$("#record-add-cancel-button").attr('href',"event-registrations.php#"+evregfuncs.prev_page_hash);
		$('#msgFrm').removeClass('d-none');
		var coming_from='';//elem.data('in-mode');
		var options={mode:'editrecord',recordid:recordid,leadtabtext:'Edit Event\'s Details',coming_from:coming_from}
		self.openRecord(options);
		return false;

	},


	openRecord:function(options){
		var self=evregfuncs;
		var opts={leadtabtext:'Event Details'};
		$.extend(true,opts,options);

		evregfuncs.dep_rowno_max=-1;

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
				let price_per_tkt = resp[1].record_details.price_per_tkt || '';
				let offer = resp[1].record_details.offer || '';
				let gst_perc = resp[1].record_details.gst_perc || '';
				let conv_fee = resp[1].record_details.conv_fee || '';
				let total_amount = resp[1].record_details.total_amount || '';


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
				contobj.find("#view_form_field_nooftkt").text(no_of_tickets);
				contobj.find("#view_form_field_totalamount").html(total_amount<=0?`<span class="free" >Free</span>`:new Intl.NumberFormat("en-IN", { style: "currency", currency: "INR", "maximumFractionDigits":0 }).format(total_amount));
				contobj.find("#view_form_field_evdskimg").attr({src: dsk_img_url, width: dsk_img_org_width}).end();
				contobj.find("#view_form_field_evmobimg").attr({src: mob_img_url, width: mob_img_org_width}).end();
				

				let header_text = 'View Registration ( '+booking_id+' )';
				
				contobj.find("#record-add-cancel-button").data('back-to',coming_from);
				contobj.find("#panel-heading-text").text(header_text);
				// contobj.find("#infoMsg").html('View Registration For <b>' + name_disp +  '</b>');
				evregfuncs.setheaderBarText(header_text);

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
				location.hash=evregfuncs.prev_page_hash;
				return;

			}

		}

		if(container_id!=''){
			$(".back-to-list-button").removeClass('d-none');
			$("#refresh-list-button").addClass('d-none');
			$("#add-record-button").addClass('d-none');
			$("#rec_list_container, #event_available_list_container").addClass('d-none');

			if(container_id!='rec_detail_add_edit_container'){
				$("#rec_detail_add_edit_container").addClass('d-none');
				$("#edit-record-button").removeClass('d-none').data('recid',otherparams.recordid);
			}else if(container_id!='rec_detail_view_container'){
				$("#user_detail_view_container").addClass('d-none');
				$("#edit-record-button").addClass('d-none');
			}

			$("#"+container_id).removeClass('d-none');
			self.setheaderBarText(otherparams.header_bar_text);

		}

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
			// var self=evregfuncs;
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
			var self=evregfuncs;
		}

		var currpage=self.paginationdata.curr_page;

		var options={pno:currpage,successResponseHandler:self.onListRefresh};
		self.getList(options);
		return false;

	},


	handleAddRecResponse:function(resp){
		var self=evregfuncs;
		$(".form-control").removeClass("error-field");

		if(resp.error_code==0){
			var message_container = '.alert-success';
			$("#record-add-cancel-button>i:eq(0)").next('span').html('Close');
			$("form[name=addrecform]").find(".error-field").removeClass('error-field').end().get(0).reset();
			$('#event_selector').val('');//.trigger('change');
			$('#add_form_field_eventname').text('');
			let pmt_req = resp.other_data?.pmt_req??'';
			$('#event_details_n_reg_section').addClass('d-none');
			if(pmt_req!=''){
				// start the payment process
				$('#payment_btn_cont a.im-checkout-btn').attr('href', pmt_req).get(0).click();
			}else{
				let thanks_msg = '';
				if(resp.other_data.no_of_tickets==1){
					thanks_msg = `One person has `;
				}else{
					thanks_msg = `${resp.other_data.no_of_tickets} persons have `;
				}
				thanks_msg += ` been successfully registered for the event ${resp.other_data.ev_name_disp}.<br>Your Registration ID is ${resp.other_data.booking_id}.`;
				setTimeout(()=>{$('#booking_success_msg').removeClass('d-none').find('.booking_success_msg>.msg-text').html(thanks_msg);}, 0);
				document.querySelector('.main-content').scrollIntoView(true);
			}


			// $("#event_selector").focus();

		}else if(resp.error_code==2){
			var message_container ='';
			if(resp.error_fields.length>0){
				var msg = resp.message;
				alert(msg);
				$(resp.error_fields[0]).focus();
				$(resp.error_fields[0]).addClass("error-field");
			}

		}else{
			if(resp.error_code==10){
				// The early bird offer is no more available. Reload the form
				setTimeout("alert(`Press 'OK' to reload the page and then try registering again.`);  window.location.reload();", 3000);
			}
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

	/*handleUpdateRecResponse:function(resp){
		var self=evregfuncs;

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

	},*/

	saveRecDetails:function(formelem){
		var self=evregfuncs;
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
		// var pp_max_filesize=evregfuncs.pp_max_filesize;
		$(".form-control").removeClass("error-field");
		// $("#add_form_field_role").next('.select2-container').removeClass("error-field");
		if(typeof opts=='object' && opts.hasOwnProperty('mode'))
			mode=opts.mode;

		const frm = $('#addrecform');
		let no_of_tkt = parseInt($.trim(frm.find('#add_form_field_nooftickets').val()));
		let max_tkt_allowed = parseInt($.trim(frm.find('#add_form_field_nooftickets').data('max') || 0))
		let event_id = $.trim(frm.find('#add_edit_eventid', '#addrecform').val());

	 	if(event_id==''){
			errors.push('Please select an event.');
			error_fields.push('#event_selector');
			$("#event_selector").addClass("error-field");

		}else if(no_of_tkt=='' || isNaN(no_of_tkt) || no_of_tkt<=0){
			errors.push('Please enter the number of persons.');
			error_fields.push('#add_form_field_nooftickets');
			$("#add_form_field_nooftickets").addClass("error-field");

		}else if(no_of_tkt > max_tkt_allowed){
			errors.push(`The number of persons cannot be more than ${max_tkt_allowed}.`);
			error_fields.push('#add_form_field_nooftickets');
			$("#add_form_field_nooftickets").addClass("error-field");

		}

		return {'errors': errors, 'error_fields': error_fields};

	},

	openAddUserForm:function(ev_id=''){
		var self=evregfuncs;

		if(ev_id==''){
			alert('Invalid registration URL.');
			window.location = $('#rec_detail_add_edit_container .back-to-list-button').attr('href');
			return false;
		}


		$('#event_details_n_reg_section').find('.alert-danger, .alert-success').addClass('d-none');
		$('#add_form_field_eventname').text('');
		$('#event_details_n_reg_section').addClass('d-none');
		$('#booking_success_msg').addClass('d-none');
		document.addrecform.reset();

		$('#add_edit_eventid', '#addrecform').val('');
		$('#add_edit_offer', '#addrecform').val('');
		
		$(".form-control").removeClass("error-field");
		$("#refresh-list-button").addClass('d-none');
		$("#add-record-button").addClass('d-none');
		$("#edit-record-button").addClass('d-none');
		$("#rec_list_container, #event_available_list_container").addClass('d-none');
		$("#rec_detail_view_container").addClass('d-none');
		$("#rec_detail_add_edit_container").removeClass('d-none').find("#panel-heading-text").text('Register').end();
		$('#msgFrm').removeClass('d-none');
			
		$(".back-to-list-button").removeClass('d-none');
		$("#rec_detail_add_edit_container").find("#record-save-button>span:eq(0)").html('Register').end().find("#add_edit_mode").val('createrec').end().find("#add_edit_recordid").val('').end().find("#record-add-cancel-button").data('back-to','').attr('href',"event-registrations.php#"+evregfuncs.prev_page_hash);
		$("form[name=addrecform]").data('mode','add-rec').find(".error-field").removeClass('error-field').end().find('input[name=active]').attr('checked',false).end().get(0).reset();

		self.setheaderBarText("");
		// $('#event_selector').val(ev_id).trigger('change');
		evregfuncs.getEventDetails(ev_id);
		if($('#event_selector').val()=='')
			$('#event_selector').focus();
		else
			setTimeout(()=>{ $('#add_form_field_nooftickets').focus(); }, 0);
		// document.querySelector('.main-content').scrollIntoView(true);
		return false;

	},

	showRegistrationForm: function(error, ev_details){
		document.addrecform.reset();
		$('#booking_success_msg').addClass('d-none');
		$('#event_details_n_reg_section').find('.alert-danger, .alert-success').addClass('d-none');
		if(error>0 || Object.entries(ev_details).length<=0){
			// no event selected
			$('#event_details_n_reg_section').addClass('d-none');
			$('#event_selector').val('');

			alert('Invalid registration URL.');
			window.location = $('#rec_detail_add_edit_container .back-to-list-button').attr('href');
			return;
		}

		let max_tkt_per_person = (ev_details.max_tkts_allowed || 0);
		let tkts_already_booked = (ev_details.tkts_already_booked || 0);
		let tkts_still_allowed = max_tkt_per_person > tkts_already_booked?max_tkt_per_person - tkts_already_booked:0; 
		if( tkts_still_allowed <= 0  ){
			alert(`You have already registered for the maximum number of persons allowed for the event "${ev_details.name_disp}"! Thanks.`);
			window.location = $('#rec_detail_add_edit_container .back-to-list-button:visible').attr('href');
			return;
		}

		$('#event_details_n_reg_section').removeClass('d-none');
		$('#add_form_field_eventname').html(ev_details.name_disp);
		$('#add_edit_eventid', '#addrecform').val(ev_details.id);
		$('#add_edit_offer', '#addrecform').val(ev_details.offer || '');
		$('#add_form_field_evdskimg', '#addrecform').attr('src', ev_details.dsk_img_url);
		$('#add_form_field_evmobimg', '#addrecform').attr('src', ev_details.mob_img_url);
		$('#add_form_field_evdescription', '#addrecform').html(ev_details.description_disp);
		$('#add_form_field_evperiod', '#addrecform').html(ev_details.period_disp).end();
		$('#add_form_field_evperiod_label', '#addrecform').text((ev_details.start_dt!=ev_details.end_dt?'Dates: ':'Date: ')).end();
		$('#add_form_field_evtime', '#addrecform').html(ev_details.time_text_disp).end();
		$('#add_form_field_evvenue', '#addrecform').html(ev_details.venue_disp).end();
		$('#add_form_field_ppt', '#addrecform').html(ev_details.offer=='EB'?( ( ev_details.tkt_price!=ev_details.offer_tkt_price?(`<span style="margin-right: 10px; text-decoration: line-through"  >` + (ev_details.tkt_price<=0?'Free':`${new Intl.NumberFormat("en-IN", { style: "currency", currency: "INR", "maximumFractionDigits":0 }).format(ev_details.tkt_price)}`) + `</span>` + (ev_details.offer_tkt_price<=0?`<span class="free" >Free</span>`:`${new Intl.NumberFormat("en-IN", { style: "currency", currency: "INR", "maximumFractionDigits":0 }).format(ev_details.offer_tkt_price)}`) ):( ev_details.offer_tkt_price<=0?`<span class="free" >Free</span>`:`${new Intl.NumberFormat("en-IN", { style: "currency", currency: "INR", "maximumFractionDigits":0 }).format(ev_details.offer_tkt_price)}` ) ) + `<span style="margin-left:5px; font-size: 12px;"  >( Early Bird Offer)</span>` ):(ev_details.tkt_price<=0?`<span class="free" >Free</span>`:`${new Intl.NumberFormat("en-IN", { style: "currency", currency: "INR", "maximumFractionDigits":0 }).format(ev_details.tkt_price)}`)).end();
		// $('#add_form_field_evregperiod', '#addrecform').html(ev_details.reg_end_dt_disp).end();

		$('#flds_for_ev_registration', '#addrecform').toggleClass('d-none', !ev_details.reg_allowed);
		$('#max_tkts_available_for_booking').text(tkts_still_allowed || 0);
		$('#add_form_field_nooftickets').data({max: tkts_still_allowed || 0, ppt: ev_details.offer!=''?ev_details.offer_tkt_price:ev_details.tkt_price || 0, gst: ev_details.gst_perc || 0, cnf: ev_details.conv_fee || 0});
		$('#add_form_field_gstamount').parent().siblings('label').text('')
		$('#add_form_field_baseamount, #add_form_field_gstamount, #add_form_field_convfee, #add_form_field_totalamount, #add_form_field_roffdiscount').text('');


	},

	getEventDetails:function(ev_id){
		$('#event_selector').val(ev_id);

		if(ev_id!='' && $('#event_selector').val()!=null ){
			common_js_funcs.callServer({cache:'no-cache',async:true,dataType:'json',type:'post',url:evregfuncs.ajax_data_script,params:{mode:'getEventDetails', id:ev_id},
			successResponseHandler:function(resp,extra){
				evregfuncs.showRegistrationForm(resp[0], resp[1]);
			},
			successResponseHandlerParams:{}});
		}else{
			evregfuncs.showRegistrationForm(0,{});
		}

	},

	onEventChange: function(ev){
		var elem = $(ev.currentTarget);
		var id =elem.val();
		let hash_params = {...evregfuncs.hash_params}
		hash_params.e = id;
		location.hash = (new URLSearchParams(hash_params)).toString();
	},


	// calcTktBaseAmount:function(no_of_tickets, price_per_tkt){
	// 	if(!isNaN(no_of_tickets) && !isNaN(price_per_tkt)){
	// 		return no_of_tickets*price_per_tkt;
	// 	}
	// 	return 0;
	// },

	// calcTktGstAmount:function(amt, gst_rate){
	// 	if(!isNaN(amt) && !isNaN(gst_rate)){
	// 		let gst_amt = (gst_rate/100)*amt;
	// 		return gst_amt.toFixed(2);
	// 	}
	// 	return 0;
	// },

	calcTktBaseAmountNGst:function(no_of_tickets, price_per_tkt, gst_rate){
		let amt = [0,0]; // Base price, GST amount
		if(!isNaN(no_of_tickets) && !isNaN(price_per_tkt)){
			// return parseFloat(no_of_tickets * price_per_tkt * (100/(100+gst_rate))).toFixed(2);
			let price_with_gst = parseFloat(no_of_tickets * price_per_tkt);
			let price_without_gst  = price_with_gst * (100/(100+gst_rate));
			let gst = price_with_gst - price_without_gst;
			amt[0] = price_without_gst.toFixed(2);
			amt[1] = gst.toFixed(2);
		}
		return amt;
	},

	calcConvFee:function(no_of_tickets, conv_fee_per_tkt){
		if(!isNaN(no_of_tickets) && !isNaN(conv_fee_per_tkt)){
			return no_of_tickets*conv_fee_per_tkt;
		}
		return 0;
	},

	calcTotAmt:function(base_amt, gst_amt, conv_fee){
		let amt = [0,0,0];
		if(!isNaN(base_amt) && !isNaN(gst_amt) && !isNaN(conv_fee)){
			amt[0] = parseFloat(base_amt) + parseFloat(gst_amt) + parseFloat(conv_fee);
			amt[1] = Math.floor(amt[0]); // rounded off
			amt[2] = amt[0] - amt[1]; // Round off discount 
		}
		return amt;
	},

	onTktEntry: function(ev){
		let ev_type =  ev.type.toLowerCase();
		let val = ev.currentTarget.value.trim().replace(/\D+/g, '');
		ev.currentTarget.value = val;

		let ppt = parseInt($('#add_form_field_nooftickets').data('ppt'),10);
		let gst_rate = parseFloat($('#add_form_field_nooftickets').data('gst'),10);
		let conv_fee = parseInt($('#add_form_field_nooftickets').data('cnf'),10);

		// let base_amt = evregfuncs.calcTktBaseAmount(val, ppt);
		// let gst_amt = evregfuncs.calcTktGstAmount(base_amt, gst_rate);
		let base_amt_n_gst = evregfuncs.calcTktBaseAmountNGst(val, ppt, gst_rate);
		let tot_conv_fee = evregfuncs.calcConvFee(val, conv_fee);
		// let tot_amt = evregfuncs.calcTotAmt(base_amt, gst_amt, tot_conv_fee);
		let tot_amt = evregfuncs.calcTotAmt(base_amt_n_gst[0], base_amt_n_gst[1], tot_conv_fee);

		$('#add_form_field_baseamount').text(`${new Intl.NumberFormat("en-IN", { style: "currency", currency: "INR", "maximumFractionDigits":2 }).format(base_amt_n_gst[0])}` );

		$('#add_form_field_gstamount').text('');

		$('#add_form_field_convfee').text(new Intl.NumberFormat("en-IN", { style: "currency", currency: "INR", "maximumFractionDigits":2 }).format(tot_conv_fee) );

		$('#add_form_field_roffdiscount').text(tot_amt[2]>0?new Intl.NumberFormat("en-IN", { style: "currency", currency: "INR", "maximumFractionDigits":2 }).format(tot_amt[2]):'0');
		
		$('#add_form_field_totalamount').text(new Intl.NumberFormat("en-IN", { style: "currency", currency: "INR", "maximumFractionDigits":2 }).format(tot_amt[1]) );

		

		
	},


	

	deleteUser:function(ev){
		var elem = $(ev.currentTarget);
		var id =elem.data('recid');
		// alert(id);
		if(confirm('Do you want to delete this user?')){

			var rec_details = {};
			common_js_funcs.callServer({cache:'no-cache',async:false,dataType:'json',type:'post',url:evregfuncs.ajax_data_script,params:{mode:'deleteUser', user_id:id},
				successResponseHandler:function(resp,status,xhrobj){
					if(resp.error_code == 0)
						evregfuncs.handleDeleteResp(resp);
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
		evregfuncs.refreshList();
	},

	closeAddUserForm:function(){
		var self =this;
		return true;

	},


	setheaderBarText:function(text){
		$("#header-bar-text").find(":first-child").html(text);
		// $('#panel-heading-text').text("Add user");

	},

	showRegThanks: function(e){
		$("#rec_list_container, #event_available_list_container, #rec_detail_view_container, #rec_detail_add_edit_container").addClass('d-none');
		$("#reg_thanks_container").removeClass('d-none'); // .find("#panel-heading-text").text('Book Tickets').end();
	},

	
	onHashChange:function(e){
		var hash=location.hash.replace(/^#/,'');
		// alert(hash);
		if(evregfuncs.curr_page_hash!=evregfuncs.prev_page_hash){
			evregfuncs.prev_page_hash=evregfuncs.curr_page_hash;
		}
		evregfuncs.curr_page_hash=hash;

		var hash_params={mode:''};
		if(hash!=''){
			var hash_params_temp=hash.split('&');
			var hash_params_count= hash_params_temp.length;
			for(var i=0; i<hash_params_count; i++){
				var temp=hash_params_temp[i].split('=');
				hash_params[temp[0]]=decodeURIComponent(temp[1]);
			}
		}

		evregfuncs.hash_params = {...hash_params}; // creating a shallow copy of the local object hash_params

		switch(hash_params.mode.toLowerCase()){
			case 'regthanks':
				evregfuncs.showRegThanks(hash_params.e || '');
				break;
			case 'book':
								$('.alert-success, .alert-danger').addClass('d-none');
								$('#msgFrm').removeClass('d-none');
								evregfuncs.openAddUserForm(hash_params.e || '');
								break;

			// case 'edit':
			// 				$('.alert-success, .alert-danger').addClass('d-none');
			// 				$('#msgFrm').removeClass('d-none');
			// 				if(hash_params.hasOwnProperty('recid') && hash_params.recid!=''){
			// 					evregfuncs.openRecordForEditing(hash_params.recid);

			// 				}else{
			// 					location.hash=evregfuncs.prev_page_hash;
			// 				}
			// 				break;

			case 'view':
							$('.alert-success, .alert-danger').addClass('d-none');

							if(hash_params.hasOwnProperty('recid') && hash_params.recid!=''){
								evregfuncs.openRecordForViewing(hash_params.recid);
							}else{
								location.hash=evregfuncs.prev_page_hash;
							}
							break;					



			case 'bookings':

					$('.alert-success, .alert-danger').addClass('d-none');
					$('#msgFrm').removeClass('d-none');
					var params={mode:'getBookings',pno:1, searchdata:"[]", sortdata:JSON.stringify(evregfuncs.sortparams), listformat:'html'};

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

					evregfuncs.searchparams=JSON.parse(params['searchdata']);
					evregfuncs.sortparams=JSON.parse(params['sortdata']);

					if(evregfuncs.sortparams.length==0){
						evregfuncs.sortparams.push(evregfuncs.default_sort);
						params['sortdata']=JSON.stringify(evregfuncs.sortparams);
					}

					document.search_form.reset();

					if(evregfuncs.searchparams.length>0){
							$.each(evregfuncs.searchparams , function(idx,data) {
									//console.log(data);
									switch (data.searchon) {

										case 'booking_id':
											$("#search-field_bookingid").val(data.searchtext);
											break;
										case 'ev_name':
											$("#search-field_evname").val(data.searchtext);
											break;
										case 'ev_description':
											$("#search-field_evdescription").val(data.searchtext);
											break;
										case 'ev_venue':
											$("#search-field_evvenue").val(data.searchtext);
											break;
										case 'ev_falls_in_period':
											$("#search-field_evperiodstart_picker").datepicker('setDate', data.searchtext[0]!=''?new Date(data.searchtext[0]):null);
											$("#search-field_evperiodend_picker").datepicker('setDate', data.searchtext[1]!=''?new Date(data.searchtext[1]):null);
											
											break;
									}

							});
							//$('#close_box').removeClass('d-none');
						// $("#search_field").val(evregfuncs.searchparams[0]['searchon'] || '');
					}
					// params['searchdata']=encodeURIComponent(params['searchdata']);
					// params['sortdata']=encodeURIComponent(params['sortdata']);

					// if(evregfuncs.searchparams.length>0){
					// 	if(evregfuncs.searchparams[0]['searchon'] == 'status')
					// 		$("#search_text").val(evregfuncs.searchparams[0]['searchtext'][0]=='1'?'Active':'Inactive');
					// 	else
					// 		$("#search_text").val(evregfuncs.searchparams[0]['searchtext'] || '');

					// 	$("#search_field").val(evregfuncs.searchparams[0]['searchon'] || '');
					// 	//$('#close_box').removeClass('d-none');

					// }

					$("#common-processing-overlay").removeClass('d-none');

					common_js_funcs.callServer({cache:'no-cache',async:true,dataType:'json',type:'post', url:self.ajax_data_script,params:params,successResponseHandler:evregfuncs.showList,successResponseHandlerParams:{self:evregfuncs}});

					// var show_srch_form = false;
					// if (typeof(Storage) !== "undefined") {
					// 	srch_frm_visible = localStorage.evreg_search_toggle;
					// } else {
					// 	srch_frm_visible = Cookies.get('evreg_search_toggle');
					// }
					// if(srch_frm_visible && srch_frm_visible == 'visible')
					// 	show_srch_form = true;
					// $('.toggle-search').toggleClass('search-form-visible', show_srch_form);
					// $('#search_records').closest('.panel-search').toggleClass('d-none', !show_srch_form);
					// var search_form_cont = $('#search_records').closest('.panel-search');
					// if(search_form_cont.hasClass('d-none'))
					// 	$('.toggle-search').prop('title','Open search panel');
					// else{
					// 	$('.toggle-search').prop('title','Close search panel');
					// 	$("#search-field_fullname").focus();
					// }

					evregfuncs.showHidePanel('evreg_search_toggle');

					break;

			default:

					$('.alert-success, .alert-danger').addClass('d-none');
					$('#msgFrm').removeClass('d-none');
					var params={mode:'getList',pno:1, searchdata:"[]", sortdata:JSON.stringify(evregfuncs.event_sortparams), listformat:'html'};

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

					evregfuncs.event_searchparams=JSON.parse(params['searchdata']);
					evregfuncs.event_sortparams=JSON.parse(params['sortdata']);

					if(evregfuncs.event_sortparams.length==0){
						evregfuncs.event_sortparams.push(evregfuncs.event_default_sort);
						params['sortdata']=JSON.stringify(evregfuncs.event_sortparams);
					}

					document.event_search_form.reset();

					if(evregfuncs.event_searchparams.length>0){
							$.each(evregfuncs.event_searchparams , function(idx,data) {
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
									}

							});
							//$('#close_box').removeClass('d-none');
						// $("#search_field").val(evregfuncs.searchparams[0]['searchon'] || '');
					}
					// params['searchdata']=encodeURIComponent(params['searchdata']);
					// params['sortdata']=encodeURIComponent(params['sortdata']);

					// if(evregfuncs.searchparams.length>0){
					// 	if(evregfuncs.searchparams[0]['searchon'] == 'status')
					// 		$("#search_text").val(evregfuncs.searchparams[0]['searchtext'][0]=='1'?'Active':'Inactive');
					// 	else
					// 		$("#search_text").val(evregfuncs.searchparams[0]['searchtext'] || '');

					// 	$("#search_field").val(evregfuncs.searchparams[0]['searchon'] || '');
					// 	//$('#close_box').removeClass('d-none');

					// }

					$("#common-processing-overlay").removeClass('d-none');

					common_js_funcs.callServer({cache:'no-cache',async:true,dataType:'json',type:'post', url:self.ajax_data_script,params:params,successResponseHandler:evregfuncs.showEventList,successResponseHandlerParams:{self:evregfuncs}});

					// var show_srch_form = false;
					// if (typeof(Storage) !== "undefined") {
					// 	srch_frm_visible = localStorage.evreg_search_toggle;
					// } else {
					// 	srch_frm_visible = Cookies.get('evreg_search_toggle');
					// }
					// if(srch_frm_visible && srch_frm_visible == 'visible')
					// 	show_srch_form = true;
					// $('.toggle-search').toggleClass('search-form-visible', show_srch_form);
					// $('#search_records').closest('.panel-search').toggleClass('d-none', !show_srch_form);
					// var search_form_cont = $('#search_records').closest('.panel-search');
					// if(search_form_cont.hasClass('d-none'))
					// 	$('.toggle-search').prop('title','Open search panel');
					// else{
					// 	$('.toggle-search').prop('title','Close search panel');
					// 	$("#search-field_fullname").focus();
					// }

					evregfuncs.showHidePanel('evreg_ev_search_toggle');

					

		}


		//$("[data-rel='tooltip']").tooltip({html:true, placement:'top', container:'body'});




	}

}