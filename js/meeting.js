var usersfuncs={
	searchparams:[],  /* [{searchon:'',searchtype:'',searchtext:''},{},..] */
	sortparams:[],  /* [{sorton:'',sortorder:''},{},..] */
	default_sort:{sorton:'name',sortorder:'ASC'},
	paginationdata:{},
	defaultleadtabtext:'Users List',
	filtersapplied:[],
	statuschangestarted:0,
	ajax_data_script:'users.php',
	curr_page_hash:'',
	prev_page_hash:'',
	// email_pattern:new RegExp("^\\w+([.']?-*\\w+)*@\\w+([.-]?\\w+)*(\\.\\w{2,4})+$","i"),
	email_pattern:/^([^\@;:"'#()=,&]+|"[^\;:"',&]+")@(\w+([\.-]?\w+)*(\.\w{2,})+|[\[]\d{0,3}(\.\d{0,3}){3}[\]])$/,
	mobile_pattern: /^[+]?\d{8,15}$/,
	memno_pattern: /^[A-Z0-9][A-Z0-9-]*$/i,
	name_pattern: /^[A-Z -]+$/i,
	batchno_pattern: /^\d{4}$/,
	batch_no_min:1940, // will be feeded from the config file
	batch_no_max:2024, // will be feeded from the config file
	salutaions:[],
	user_roles:[],
	all_user_roles:[],
	deps_list:[],
	user_levels:{},
	dep_rowno_max:-1,
	pp_max_filesize:0,
	groups_selector:null,
	// work_ind:[],
	cities:[],
	countries:[],
	states:[],

	initiateStatusChange:function(statuscell){
		var self=usersfuncs;

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
		let elem = $(ev.currentTarget);
		if(elem.hasClass('search-form-visible')){
			usersfuncs.setPanelVisibilityStatus('user_search_toggle', ''); // set closed status for the search panel
		}else{
			usersfuncs.setPanelVisibilityStatus('user_search_toggle', 'visible'); // set visible status for the search panel
			usersfuncs.setPanelVisibilityStatus('user_sort_toggle', ''); // set closed status for sort panel
		}

		usersfuncs.showHidePanel('user_search_toggle');
		usersfuncs.showHidePanel('user_sort_toggle');


		// elem.toggleClass('search-form-visible', !elem.hasClass('search-form-visible'));
		// let  search_form_cont = $('#search_records').closest('.panel-search')
		// search_form_cont.toggleClass('d-none', !elem.hasClass('search-form-visible'));
		// usersfuncs.onSearchToggle();
		// if(!search_form_cont.hasClass('d-none')){
		// 	// Turn off the sorting panel if it is on
		// 	$('.toggle-sort').removeClass('sort-form-visible');
		// 	$('#sort_records').closest('.panel-sort').addClass('d-none');
		// 	usersfuncs.onSortToggle();
		// }

		// var search_form_cont = $('#search_records').closest('.panel-search');
		// if(search_form_cont.hasClass('d-none'))
		// 	elem.prop('title','Open search panel');
		// else{
		// 	elem.prop('title','Close search panel');
		// 	$("#search-field_fullname").focus();

			
			
		// }
		// if (typeof(Storage) !== "undefined") {
		// 	localStorage.user_search_toggle = elem.hasClass('search-form-visible') ? 'visible' : '';
		// } else {
		// 	Cookies.set('user_search_toggle', elem.hasClass('search-form-visible') ? 'visible' : '', {path : '/'/*, secure: true*/});
		// }
	},

	toggleSortPanel: function(ev){
		let elem = $(ev.currentTarget);
		if(elem.hasClass('sort-form-visible')){
			usersfuncs.setPanelVisibilityStatus('user_sort_toggle', ''); // set closed status for the sort panel
		}else{
			usersfuncs.setPanelVisibilityStatus('user_sort_toggle', 'visible'); // set visible status for the sort panel
			usersfuncs.setPanelVisibilityStatus('user_search_toggle', ''); // set closed status for search panel
		}

		usersfuncs.showHidePanel('user_sort_toggle');
		usersfuncs.showHidePanel('user_search_toggle');


		// elem.toggleClass('sort-form-visible', !elem.hasClass('sort-form-visible'));
		// let sort_form_cont = $('#sort_records').closest('.panel-sort');
		// sort_form_cont.toggleClass('d-none', !elem.hasClass('sort-form-visible'));
		// usersfuncs.onSortToggle();
		// if(!sort_form_cont.hasClass('d-none')){
		// 	// Turn off the search panel if it is on
		// 	$('.toggle-search').removeClass('search-form-visible');
		// 	$('#search_records').closest('.panel-ssearch').addClass('d-none');
		// 	usersfuncs.onSearchToggle();
		// }


		// let sort_form_cont = $('#sort_records').closest('.panel-sort');
		// if(sort_form_cont.hasClass('d-none'))
		// 	elem.prop('title','Open sort panel');
		// else{
		// 	elem.prop('title','Close sort panel');
		// 	$("#sorton").focus();

		// 	// Turn off the serach panel is it is on
		// 	$('.toggle-search').removeClass('search-form-visible');
		// 	$('#search_records').closest('.panel-search').addClass('d-none');
			
		// }

		// if (typeof(Storage) !== "undefined") {
		// 	localStorage.user_sort_toggle = elem.hasClass('sort-form-visible') ? 'visible' : '';
		// } else {
		// 	Cookies.set('user_sort_toggle', elem.hasClass('sort-form-visible') ? 'visible' : '', {path : '/'});
		// }
	},

	setPanelVisibilityStatus: function(panel, status){
		if (typeof(Storage) !== "undefined") {
			localStorage[panel] = status;
		} else {
			Cookies.set(panel, status, {path : '/'});
		}
	},

	showHidePanel: function(panel){
		if(panel === 'user_search_toggle'){
			let show_srch_form = false;
			if (typeof(Storage) !== "undefined") {
				srch_frm_visible = localStorage.user_search_toggle;
			} else {
				srch_frm_visible = Cookies.get('user_search_toggle');
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
				setTimeout(()=>{
					$('#search-field_name').focus();
				}, 100);
			}
		}else if(panel === 'user_sort_toggle'){
			let show_sort_form = false;
			if (typeof(Storage) !== "undefined") {
				sort_frm_visible = localStorage.user_sort_toggle;
			} else {
				sort_frm_visible = Cookies.get('user_sort_toggle');
			}
			if(sort_frm_visible && sort_frm_visible == 'visible')
				show_sort_form = true;

			$('.toggle-sort').toggleClass('sort-form-visible', show_sort_form);
			$('#sort_records').closest('.panel-sort').toggleClass('d-none', !show_sort_form);
			let sort_form_cont = $('#sort_records').closest('.panel-sort');
			if(sort_form_cont.hasClass('d-none'))
				$('.toggle-sort').prop('title','Open sorting panel');
			else{
				$('.toggle-sort').prop('title','Close sorting panel');
				setTimeout(()=>{
					$('#sorton').focus();
				}, 100);
			}
		}
	},

	// onSearchToggle: function(){
	// 	let form_cont = $('#search_records').closest('.panel-search');
	// 	let toggle_btn = $('.toggle-search');
	// 	let storage_val = '';
	// 	if(form_cont.hasClass('d-none'))
	// 		toggle_btn.prop('title','Open search panel');
	// 	else{
	// 		toggle_btn.prop('title','Close search panel');
	// 		$("#search-field_name").focus();
	// 		storage_val = 'visible';
	// 	}
	// 	if (typeof(Storage) !== "undefined") {
	// 		localStorage.user_search_toggle = storage_val;
	// 	} else {
	// 		Cookies.set('user_search_toggle', storage_val, {path : '/'});
	// 	}
	// },

	// onSortToggle: function(){
	// 	let form_cont = $('#sort_records').closest('.panel-sort');
	// 	let toggle_btn = $('.toggle-sort');
	// 	let storage_val = '';
	// 	if(form_cont.hasClass('d-none'))
	// 		toggle_btn.prop('title','Open sorting panel');
	// 	else{
	// 		toggle_btn.prop('title','Close sorting panel');
	// 		$("#sorton").focus();
	// 		storage_val = 'visible';
	// 	}
	// 	if (typeof(Storage) !== "undefined") {
	// 		localStorage.user_sort_toggle = storage_val;
	// 	} else {
	// 		Cookies.set('user_sort_toggle', storage_val, {path : '/'});
	// 	}
	// },

	

	confirmAndExecuteStatusChange:function(statuscell){
		var self=usersfuncs;

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
			var options={cache:'no-cache',dataType:'json',async:true,type:'post',url:usersfuncs.ajax_data_script+"?mode=changeStatus",data:"newstatus="+newstatus+"&recordid="+userid,successResponseHandler:usersfuncs.handleStatusChangeResponse,successResponseHandlerParams:{statuscell:statuscell,rowelem:rowelem}};
			common_js_funcs.callServer(options);
			$(statuscell).removeClass("status-grn");
			$(statuscell).removeClass("status-red");
			if(parseInt(newstatus)==1){
				$(statuscell).addClass("status-grn");
			}else{
				$(statuscell).addClass("status-red");
			}
		}else{
			usersfuncs.statuschangestarted=0;
			usersfuncs.abortStatusChange(statuscell);

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
							usersfuncs.statuschangestarted=0;
							usersfuncs.abortStatusChange(statuscell);
						}
					},
					"Yes":	{
						"label": "Yes",
						"className": "btn-danger btn-primary",
						"callback": function(ev){

							var options={cache:'no-cache',dataType:'json',async:true,type:'post',url:usersfuncs.ajax_data_script+"?mode=changeStatus",data:"newstatus="+newstatus+"&recordid="+userid,successResponseHandler:usersfuncs.handleStatusChangeResponse,successResponseHandlerParams:{statuscell:statuscell,rowelem:rowelem}};
							common_js_funcs.callServer(options);
						}
					}

				}

		});*/




	},

	abortStatusChange:function(statuscell){
		var self=usersfuncs;

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
		var self=usersfuncs;

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
		var self=usersfuncs;
		var listhtml=resp[1].list;
		self.user_count=resp[1]['reccount'];
		$("#user_list_container").removeClass('d-none');
		$("#user_detail_view_container").addClass('d-none');
		$("#user_detail_add_edit_container").addClass('d-none');
		$("#common-processing-overlay").addClass('d-none');
		// $('#search_field').select2({minimumResultsForSearch: -1});

		// Hide the search panel in mobile view
		if(resp[1].reccount>0 && $('#menu-toggle').is(':visible')){
			usersfuncs.setPanelVisibilityStatus('user_search_toggle', '');
			usersfuncs.showHidePanel('user_search_toggle');
		}


		$("#userlistbox").html(listhtml);
		
		if(resp[1].tot_rec_cnt>0){
			$('#heading_rec_cnt').text((resp[1]['reccount']==resp[1]['tot_rec_cnt'])?`(${resp[1]['tot_rec_cnt']})`:`(${resp[1]['reccount'] || 0} of ${resp[1]['tot_rec_cnt']})`);
			usersfuncs.setExportLink(resp[1]['reccount']>0?true:false);
		}else{
			$('#heading_rec_cnt').text('(0)');
			usersfuncs.setExportLink(false);
		}

		$("#add-record-button").removeClass('d-none');
		$("#refresh-list-button").removeClass('d-none');
		$(".back-to-list-button").addClass('d-none').attr('href',"users.php#"+usersfuncs.curr_page_hash);
		$("#edit-record-button").addClass('d-none');
		self.paginationdata=resp[1].paginationdata;

		self.setSortOrderIcon();


	},


	onListRefresh:function(resp,otherparams){
		var self=usersfuncs;
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
		var self=usersfuncs;
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
		var self=usersfuncs;
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
		var self=usersfuncs;
		self.searchparams=[];
	},

	setSearchParams:function(obj){
		var self=usersfuncs;
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
				if(remove_all==='joining_dt')
					$("#search-field_joinedafterdt_picker").datepicker('setDate', null);
				else
					$('.panel-search .srchfld[data-fld='+remove_all+']').val('');
			}
		}

		var self=usersfuncs;
		// self.filtersapplied=[]; // remove the filter bar messages
		if(remove_all===true){
			self.resetSearchParamsObj();
			document.search_form.reset();
			$("#search-field_joinedafterdt_picker").datepicker('setDate', null);
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

		usersfuncs.resetSearchParamsObj();
		$('.panel-search .srchfld').each(function(i, el){
			let val = $.trim($(el).val());
			let display_text = '';
			if($(el).data('fld')=='sector_id' || $(el).data('fld')=='grp_id')
				display_text = $(el).find('option:selected').text();
			if(val!=''){
				usersfuncs.setSearchParams({searchon:$(el).data('fld'),searchtype:$(el).data('type'),searchtext:val, disp_text:display_text});
			}
		});

		if(usersfuncs.searchparams.length<=0)
			return false;

		var options={pno:1};
		usersfuncs.getList(options);
		//self.toggleSearch(this);
		return false;
	},


	changePage:function(ev){
		ev.preventDefault();
		if(!$(ev.currentTarget).parent().hasClass('disabled')){
			var self=usersfuncs;
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
		if(self.sortparams[0].sorton==sorton){
			if(self.paginationdata.curr_page!='undefined' && self.paginationdata.curr_page>1){
				pno = self.paginationdata.curr_page;
			}
		}

		usersfuncs.sortparams=[];
		usersfuncs.sortparams.push({sorton:sorton, sortorder:sortorder});
		var options={pno:pno};
		usersfuncs.getList(options);

	},



	setSortOrderIcon:function(){
		var self=usersfuncs;
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

	doSort: function(){
		let sorton = $('#orderlist-sorton').val();
		let sortorder = $('input[name=sortorder]:checked').val();
		usersfuncs.sortparams=[];
		usersfuncs.sortparams.push({sorton:sorton, sortorder:sortorder});
		let pno = 1;
		// if(usersfuncs.paginationdata?.curr_page && usersfuncs.paginationdata.curr_page>1){
		// 	pno = usersfuncs.paginationdata.curr_page;
		// } Page number should be reset if the sorting feature is used
		let options={pno: pno};
		usersfuncs.getList(options);
		return false;
	},

	openRecordForViewing:function(recordid){
		var self=usersfuncs;
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
		var self=usersfuncs;
		if(recordid=='')
			return false;

		document.adduserform.reset();
		$(".form-control").removeClass("error-field");
		$("#record-save-button").removeClass('d-none').attr('disabled', false);
		// $("#add_form_field_role").next('.select2-container').removeClass("error-field");
		$("#common-processing-overlay").removeClass('d-none');
		$("#record-add-cancel-button").attr('href',"users.php#"+usersfuncs.prev_page_hash);
		$('#msgFrm').removeClass('d-none');
		var coming_from='';//elem.data('in-mode');
		var options={mode:'editrecord',recordid:recordid,leadtabtext:'Edit Member\'s Details',coming_from:coming_from}
		self.openRecord(options);
		return false;

	},


	openRecord:function(options){
		var self=usersfuncs;
		var opts={leadtabtext:'Lead Details'};
		$.extend(true,opts,options);

		usersfuncs.dep_rowno_max=-1;

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

		if(!resp[1].allow_detail_view){
			location.hash=usersfuncs.prev_page_hash;
			return;
		}


		if(otherparams.mode=='editrecord'){
			var coming_from=otherparams.coming_from;


			if(user_id!=''){

				if(resp[1].can_edit===false){
					// User is not authorised to edit this record so send him back to the previous screen
					location.hash=usersfuncs.prev_page_hash;
					return;
				}

				usersfuncs.removeEditRestrictions();

				// var selected_roleids=[];
				// var assigned_roles = resp[1].record_details.assigned_roles || [];
				// for(var i=0; i<assigned_roles.length; i++){
				// 	selected_roleids.push(assigned_roles[i]['role_id']);
				// }

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
				let spousenam = resp[1].record_details.spouse_name || '';
				let spousegendar = resp[1].record_details.spouse_gender;
				let spousedob =resp[1].record_details.spouse_dob || '';
				let spousewhatsapp = spouse_wa_num = resp[1].record_details.spouse_whatapp || '';
				if(spouse_wa_num!=''){
					if(!/^[+0]/.test(spouse_wa_num))
						spouse_wa_num = `+${country_code}${spouse_wa_num}`;
				}
				let spouseemail = resp[1].record_details.spouse_email || '';
				let spouseprofession = resp[1].record_details.spouse_profession || '';
				let spousechildren = resp[1].record_details.spouse_children || '';
				let mobile2 = resp[1].record_details.mobile2 || '';
				let edu_qual = resp[1].record_details.edu_qual || '';
				let linkedin_accnt = resp[1].record_details.linkedin_accnt || '';
				let x_accnt = resp[1].record_details.x_accnt || '';
				let fb_accnt = resp[1].record_details.fb_accnt || '';
				let website = resp[1].record_details.website || '';
				let gender = resp[1].record_details.gender || '';
				let blood_grp = resp[1].record_details.blood_grp || '';
				let batch_no = resp[1].record_details.batch_no || '';
				let profile_pic = resp[1].record_details.profile_pic || '';
				let profile_pic_url = resp[1].record_details.profile_pic_url || '';
				let dob = resp[1].record_details.dob || '';
				let annv = resp[1].record_details.annv || '';
				// let marital_status = resp[1].record_details.marital_status || '';
				let residence_city = resp[1].record_details.residence_city || '';
				let spousename = resp[1].record_details.spouse_name || '';
				let residence_state = resp[1].record_details.residence_state || '';
				let residence_country = resp[1].record_details.residence_country || '';
				let residence_pin = resp[1].record_details.residence_pin || '';
				let residence_addrline1 = resp[1].record_details.residence_addrline1 || '';
				let residence_addrline2 = resp[1].record_details.residence_addrline2 || '';
				let residence_addrline3 = resp[1].record_details.residence_addrline3 || '';
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
				let membership_no = resp[1].record_details.membership_no || '';
				let assigned_sector_ids = resp[1].record_details.assigned_sector_ids || '';
				let desig_in_assoc = resp[1].record_details.desig_in_assoc || '';
				let role = resp[1].record_details.assigned_roles[0]['role'] || '';
				let status = resp[1].record_details.active ?? '';
				let dnd = resp[1].record_details.dnd ?? '';
				// let hashtags = resp[1].record_details.hashtags ?? '';
				let joining_dt = resp[1].record_details.joining_dt ?? '';
				// let exp_dt = resp[1].record_details.exp_dt || '';
				let remarks = resp[1].record_details.remarks ?? '';

				let payment_status = resp[1].record_details.payment_status ?? '';
				let paid_on = resp[1].record_details.paid_on_dt ?? '';
				let payment_mode = resp[1].record_details.payment_mode ?? '';
				let membership_fee = resp[1].record_details.membership_fee ?? '';
				let payment_txn_ref = resp[1].record_details.payment_txn_ref ?? '';
				let payment_instrument_type = resp[1].record_details.payment_instrument_type ?? '';
				let payment_instrument = resp[1].record_details.payment_instrument ?? '';

				var contobj=$("#user_detail_add_edit_container");

				$('.alert-danger').addClass('d-none').find('.alert-message').html('');
				$('#msgFrm').removeClass('d-none');
				contobj.find(".form-actions").removeClass('d-none');
                //console.log(dob);
				contobj.find("form[name=adduserform]:eq(0)").data('mode','edit-user').find('input[name=status]').attr('checked',false).end().get(0).reset();
               contobj.find("#add_form_field_spouse_fname").val(spousenam); 
	            if (spousegendar === 'M') {
			        document.getElementById('spgendM').checked = true;
			    } else if(spousegendar === 'F') {
			        document.getElementById('spgendF').checked = true;
			    }
             	contobj.find("#spousdob").datepicker('setDate', spousedob!=''?new Date(spousedob):null);
               contobj.find("#add_form_field_spouse_whatsapp").val(spousewhatsapp); 
               contobj.find("#add_form_field_spouse_email").val(spouseemail); 
               contobj.find("#add_form_field_spouse_profession").val(spouseprofession); 
               contobj.find("#add_form_field_children").val(spousechildren); 
				contobj.find("#add_edit_mode").val('updateUser');
				contobj.find("#add_edit_recordid").val(user_id);
				contobj.find("#add_form_field_title").val(title);
				contobj.find("#add_form_field_fname").val(fname);
				contobj.find("#add_form_field_mname").val(mname);
				contobj.find("#add_form_field_lname").val(lname);
				contobj.find("#add_form_field_email").val(email).siblings('.email-icon-form-input').data('url',`mailto:${email}`).toggleClass('d-none', email=='');
				contobj.find("#add_form_field_mobile").val(mobile).siblings('.wa-icon-form-input').data('url',`https://wa.me/${wa_num}`).toggleClass('d-none', wa_num=='').end().siblings('.tel-icon-form-input').data('url',`tel:${wa_num}`).toggleClass('d-none', wa_num=='');
				contobj.find("#add_form_field_mobile2").val(mobile2);
				contobj.find("#add_form_field_eduqual").val(edu_qual);
				contobj.find("#add_form_field_linkedinaccnt").val(linkedin_accnt);
				contobj.find("#add_form_field_xaccnt").val(x_accnt);
				contobj.find("#add_form_field_fbaccnt").val(fb_accnt);
				contobj.find("#add_form_field_website").val(website);
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
				contobj.find("#add_form_field_sector").find('option').removeClass('d-none').end().val(assigned_sector_ids[0]).find('option:not(:selected)[data-active=n]').addClass('d-none').end();

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
				

				contobj.find("#add_form_field_joiningdt_picker").datepicker('setDate', joining_dt!=''?new Date(joining_dt):null);
				// contobj.find("#add_form_field_joiningdt").text(joining_dt).parents('#joining_dt_row:eq(0)').removeClass('d-none').end(); 
				// contobj.find("#add_form_field_expdt_picker").datepicker('setDate', exp_dt!=''?new Date(exp_dt):null);
				contobj.find("#add_form_field_memno").val(membership_no); //.parents('.form-group:eq(0)').removeClass('d-none'); // Showing the membership no field for view purposes only. Editing the same will not be allowed which is being controlled via the config file.

				// contobj.find("#add_form_field_memtype").val(membership_type);
				usersfuncs.rebuildGroupOptions(resp[1].groups, resp[1].assigned_grp_ids);
				contobj.find("#add_form_field_desiginassoc").val(desig_in_assoc);
				contobj.find("#add_form_field_role_"+role).prop('checked', true);
				contobj.find("#add_form_field_password").val('');
				contobj.find("#add_form_field_status_"+status).prop('checked', true);
				contobj.find("#add_form_field_dnd").attr('checked', dnd=='y');
				contobj.find("#add_form_field_remarks").val(remarks);

				contobj.find("#default_pwd_msg").addClass('d-none');

				if(common_js_funcs.is_admin){
					contobj.find("#add_form_field_membershipfee").val(membership_fee); 
					contobj.find("#add_form_field_paymentstatus").val(payment_status); 
					contobj.find("#add_form_field_paidon_picker").datepicker('setDate', paid_on==''?null:new Date(paid_on)); 
					contobj.find("#add_form_field_paymentmode").val(payment_mode); 
					contobj.find("#add_form_field_bnkref").val(payment_txn_ref); 
					contobj.find("#add_form_field_instrument").val(payment_instrument); 
					contobj.find("#add_form_field_instrumenttype").val(payment_instrument_type); 
				}

				$('#add_form_field_paidon_picker').datepicker(!common_js_funcs.is_admin?'disable':'enable').css('background-color', 'transparent');
				$('#user_detail_add_edit_container').find('.pmtdtls input[type=text]').toggleClass('non-editable', !common_js_funcs.is_admin).end().find('.pmtdtls select').toggleClass('non-editable', !common_js_funcs.is_admin).end();
				$('#user_detail_add_edit_container').find('.pmtdtls').toggleClass('d-none', !common_js_funcs.is_admin); // hide the payment section completely for non admins

				// contobj.find("#add_form_field_status_"+status).attr('checked',true).parents('div.form-group').hide();
				// contobj.find("#add_form_field_login_access_"+login_access).attr('checked',true).parents('div.form-group').hide();
				let header_text = 'Edit Member';
				if(resp[1].cuid == login_acnt_id){ // cuid has the users table's id
					contobj.find("#add_form_field_status_"+status).parents('div.form-group').hide().end().end();
					header_text = 'Edit Your Profile';
				}else{
					contobj.find("#add_form_field_status_"+status).parents('div.form-group').show().end().end();
				}

				contobj.find("#record-add-cancel-button").data('back-to',coming_from);
				contobj.find("#record-save-button>span:eq(0)").html('Save Changes');
				contobj.find("#add_password_msg").addClass('d-none');
				contobj.find("#edit_password_msg").removeClass('d-none');
				contobj.find("#panel-heading-text").text(header_text);
				contobj.find("#infoMsg").html('Edit member <b>' + title + ' ' + name +  '</b>');
				usersfuncs.setheaderBarText(header_text);

				usersfuncs.applyEditRestrictions(resp[1].edit_restricted_fields);
				container_id='user_detail_add_edit_container';
				setTimeout(()=>{
					$("#add_form_field_dnd").focus();
				},100);


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
				location.hash=usersfuncs.prev_page_hash;
				return;

			}

		}else if(otherparams.mode=='viewrecord'){

			if(user_id!=''){
				
				let title = resp[1].record_details.title || '';
				let name = resp[1].record_details.name || '';
				let email = resp[1].record_details.email || '';
				let mobile = resp[1].record_details.mobile || '';
				let spnam =  resp[1].record_details.spnam || '';
				console.log(spnam);
				let wa_num = mobile;
				if(wa_num!=''){
					if(!/^[+0]/.test(wa_num))
						wa_num = `+${country_code}${wa_num}`;
				}
				let mobile2 = resp[1].record_details.mobile2 || '';
				let mobile2_num = mobile2;
				if(mobile2_num!=''){
					if(!/^[+0]/.test(mobile2_num))
						mobile2_num = `+${country_code}${mobile2_num}`;
				}
				let edu_qual = resp[1].record_details.edu_qual || '';
				let linkedin_accnt = resp[1].record_details.linkedin_accnt || '';
				let x_accnt = resp[1].record_details.x_accnt || '';
				let spousenam = resp[1].record_details.spouse_name || '';
				let spousegendar = (resp[1].record_details.spouse_gender === 'M') ? 'Male' : (resp[1].record_details.spouse_gender === 'F') ? 'Female' : '';
				let spousedob =resp[1].record_details.spouse_dob_view || '';
				// let formdet = new Date(spousedob);
				// let newd = (formdet instanceof Date && !isNaN(formdet)) ? formdet.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' }).replace(/ /g, '-') : '';
				let spousewhatsapp = resp[1].record_details.spouse_whatapp || '';
				let spouseemail = resp[1].record_details.spouse_email || '';
				let spouseprofession = resp[1].record_details.spouse_profession_view || '';
				let spousechildren = resp[1].record_details.spouse_children_view || '';
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
				let membership_no = resp[1].record_details.membership_no || '';
				let groups = resp[1].record_details.grp_names || [];
				let desig_in_assoc = resp[1].record_details.desig_in_assoc || '';
				let role = resp[1].record_details.role_view || '';
				let status = resp[1].record_details.status_view || '';
				let dnd = resp[1].record_details.dnd_view || '';
				// let hashtags = resp[1].record_details.hashtags || '';
				let joining_dt = resp[1].record_details.joining_dt_view || '';
				// let expdt = resp[1].record_details.exp_dt_view || '';
				let remarks = resp[1].record_details.remarks_view || '';

				let payment_status = resp[1].record_details.payment_status ?? '';
				let paid_on = resp[1].record_details.paid_on_dt_view ?? '';
				let payment_mode = resp[1].record_details.payment_mode ?? '';
				let membership_fee = resp[1].record_details.membership_fee ?? '';
				let payment_txn_ref = resp[1].record_details.payment_txn_ref ?? '';
				let payment_instrument_type = resp[1].record_details.payment_instrument_type ?? '';
				let payment_instrument = resp[1].record_details.payment_instrument ?? '';
				
				// console.log(newd);

				var contobj=$("#user_detail_view_container");
				contobj.find("#view_form_field_name").text(`${title} ${name}`);
				contobj.find("#view_form_field_batchno").text(`(${batch_no})`);
				contobj.find("#view_form_field_profilepic").attr('src', profile_pic_url);
				contobj.find("#view_form_field_memno").text(membership_no);
				contobj.find("#view_form_field_email").attr('href',`mailto:${email}`).toggleClass('d-none', email=='');
				contobj.find("#view_form_field_mobile").attr('href',`https://wa.me/${wa_num}`).toggleClass('d-none', mobile=='');
				contobj.find("#view_form_field_mobile_tel").attr('href',`tel:${wa_num}`).toggleClass('d-none', mobile=='');
				contobj.find("#view_form_field_mobile2").attr('href',`tel:${mobile2_num}`).toggleClass('d-none', mobile==mobile2 || mobile2=='');
				contobj.find("#view_form_field_eduqual").text(edu_qual)
				contobj.find("#view_form_field_linkedinaccnt").attr('href',linkedin_accnt).toggleClass('d-none', linkedin_accnt=='');
				contobj.find("#view_form_field_xaccnt").attr('href',x_accnt).toggleClass('d-none', x_accnt=='');
				contobj.find("#view_form_field_fbaccnt").attr('href',fb_accnt).toggleClass('d-none', fb_accnt=='');
				contobj.find("#view_form_field_website").attr('href',website).toggleClass('d-none', website=='');
				contobj.find("#view_form_field_bloodgrp").text(blood_grp);
				contobj.find("#view_form_field_dob").text(dob);
				contobj.find("#view_form_field_annv").text(annv);
				// contobj.find("#view_form_field_maritalstatus").text(marital_status);
				contobj.find("#view_form_field_resaddrline1").text(residence_addrline1).toggleClass('d-none', residence_addrline1=='');
				contobj.find("#view_form_field_spouse_name").text(spousenam);
				contobj.find("#spgender").text(spousegendar).toggleClass('d-none', spousegendar=='');
				contobj.find("#spdob").text(spousedob).toggleClass('d-none', spousedob=='');
				contobj.find("#spwapp").text(spousewhatsapp).toggleClass('d-none', spousewhatsapp=='');
				contobj.find("#spemail").text(spouseemail).toggleClass('d-none', spouseemail=='');
				contobj.find("#spproff").html(spouseprofession).toggleClass('d-none', spouseprofession=='');
				contobj.find("#view_form_field_children").html(spousechildren).toggleClass('d-none', spousechildren=='');
				contobj.find("#view_form_field_resaddrline2").text(residence_addrline2).toggleClass('d-none', residence_addrline2=='');
				contobj.find("#view_form_field_resaddrline3").text(residence_addrline3).toggleClass('d-none', residence_addrline3=='');
				contobj.find("#view_form_field_rescity").text(`${residence_city}${residence_pin!=''?', '+residence_pin:''}`);
				let st_cr = residence_state;
				if(residence_country!='')
					st_cr += st_cr==''?residence_country:(', '+residence_country);

				contobj.find("#view_form_field_resstate").text(st_cr).toggleClass('d-none', st_cr=='');
				

				contobj.find("#view_form_field_worktype").text(work_type).toggleClass('d-none', work_type=='');
				contobj.find("#view_form_field_sector").text(sectors.join(', ')).toggleClass('d-none', sectors=='');
				contobj.find("#view_form_field_workcompany").text(work_company).toggleClass('d-none', work_company=='');
				contobj.find("#view_form_field_designation").text(designation).toggleClass('d-none', designation=='');
				let ct_pin = work_city;
				if(work_pin!='')
					ct_pin += ct_pin==''?work_pin:(', '+work_pin);

				st_cr = work_state;
				if(work_country!='')
					st_cr += st_cr==''?work_country:(', '+work_country);

				contobj.find("#view_form_field_workcity").text(ct_pin).toggleClass('d-none', ct_pin=='');
				contobj.find("#view_form_field_workstate").text(st_cr).toggleClass('d-none', st_cr=='');
				contobj.find("#view_form_field_workaddrline1").text(work_addrline1).toggleClass('d-none', work_addrline1=='');
				contobj.find("#view_form_field_workaddrline2").text(work_addrline2).toggleClass('d-none', work_addrline2=='');
				contobj.find("#view_form_field_workaddrline3").text(work_addrline3).toggleClass('d-none', work_addrline3=='');
				
				contobj.find("#view_form_field_joiningdt").text(joining_dt);
				// contobj.find("#view_form_field_expirydt").text(expdt);
				contobj.find("#view_form_field_gender").text(gender);
				contobj.find("#view_form_field_desiginassoc").text(desig_in_assoc);
				contobj.find("#view_form_field_memtype").text(groups.join(', '));
				// contobj.find("#view_form_field_hashtags").text(hashtags);


				contobj.find('#payment_info_view').toggleClass('d-none', !common_js_funcs.is_admin);
				if(common_js_funcs.is_admin){
					contobj.find("#view_form_field_paymentstatus").text(payment_status);
					contobj.find("#view_form_field_paymentmode").text(payment_mode);
					contobj.find("#view_form_field_membershipfee").text(membership_fee!=''?new Intl.NumberFormat("en-IN", { style: "currency", currency: "INR", "maximumFractionDigits":0 }).format(membership_fee):'');
					contobj.find("#view_form_field_paidon").text(paid_on);
					contobj.find("#view_form_field_bnkref").text(payment_txn_ref);
					contobj.find("#view_form_field_instrumenttype").text(payment_instrument_type);
					contobj.find("#view_form_field_instrument").text(payment_instrument);
				}
				
				
				// non-admins will not see role and status of others
				contobj.find("#view_form_field_role").text(common_js_funcs.is_admin?role:'').parent('p').toggleClass('d-none', !common_js_funcs.is_admin).end();
				// contobj.find("#view_form_field_status").text(common_js_funcs.is_admin?status:'').parent('p').toggleClass('d-none', !common_js_funcs.is_admin).end();
				contobj.find("#view_form_field_remarks").html(common_js_funcs.is_admin?remarks:'').parent('p').toggleClass('d-none', !common_js_funcs.is_admin).end();
				
				contobj.find("#view_form_field_dnd").toggleClass('d-none', resp[1].record_details.dnd=='n' || (!resp[1].show_dnd && resp[1].cuid!=resp[1].record_details.user_acnt_id) );
				// Show the edit button if the logged in user is an ADMIN or he is viewing his own profile
				$('#vu_pg_edit_btn_cont').toggleClass('d-none', !resp[1].can_edit).find('a').attr('href', `users.php#mode=edit&recid=${user_id}`); 
				contobj.find('.member_details_block').toggleClass('inactive', common_js_funcs.is_admin && resp[1].record_details.active=='n').toggleClass('admin_member', common_js_funcs.is_admin && role=='Admin');
				contobj.find('.social-center').toggleClass('d-none', (resp[1].cuid!=resp[1].record_details.user_acnt_id && !common_js_funcs.is_admin && resp[1].record_details.dnd=='y' ));
				contobj.find('.req-contact-btn-cont').toggleClass('d-none', (resp[1].active=='n' || resp[1].user_acnt_status=='0' || resp[1].cuid==resp[1].record_details.user_acnt_id || common_js_funcs.is_admin || resp[1].record_details.dnd!='y' )).find('#req_contact_btn').data({recid:user_id, mnm:name, bno:batch_no});


				// contobj.find("#view_form_field_title").text(title);
				// contobj.find("#view_form_field_email").text(email).siblings('.email-icon-form-input').data('url',`mailto:${email}`).toggleClass('d-none', email=='');
				// contobj.find("#view_form_field_mobile").text(mobile).siblings('.wa-icon-form-input').data('url',`https://wa.me/${wa_num}`).toggleClass('d-none', mobile=='').end().siblings('.tel-icon-form-input').data('url',`tel:${wa_num}`).toggleClass('d-none', mobile=='');

				// contobj.find("#view_form_field_mobile2").text(mobile2)
				// contobj.find("#view_form_field_rescountry").text();
				// contobj.find("#view_form_field_respin").text(residence_pin);
				// contobj.find("#view_form_field_resaddrline2").text(residence_addrline2);
				// contobj.find("#view_form_field_resaddrline3").text(residence_addrline3);
				// contobj.find("#view_form_field_workind").text(work_ind);

				// contobj.find("#view_form_field_workcountry").text(work_country);
				// contobj.find("#view_form_field_workpin").text(work_pin);
				// contobj.find("#view_form_field_workaddrline2").text(work_addrline2);
				// contobj.find("#view_form_field_workaddrline3").text(work_addrline3);

				
				container_id='user_detail_view_container';
			}else{
				var message="Sorry, the view window could not be opened (Server error).";
				if(resp[0]==1){
					message="Sorry, the view window could not be opened (Member ID missing).";
				}else if(resp[0]==2){
					message="Sorry, the view window could not be opened (Server error).";
				}else if(resp[0]==3){
					message="Sorry, the view window could not be opened (Invalid member ID).";
				}

				alert(message);
				location.hash=usersfuncs.prev_page_hash;
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

	},

	applyEditRestrictions: function(restricted_fields){
		const contobj=$("#user_detail_add_edit_container");
		restricted_fields.forEach(fld=>{
			switch(fld){
				// case 'exp_dt':
				// 	contobj.find("#add_form_field_expdt_picker").addClass('non-editable rstrctedt').datepicker( "option", "disabled", true );
				// 	break;
				case 'joining_dt':
					contobj.find("#add_form_field_joiningdt_picker").addClass('non-editable rstrctedt').datepicker( "option", "disabled", true ).siblings('button.ui-datepicker-trigger')?.addClass('d-none');
					break;
				case 'membership_no':
					contobj.find("#add_form_field_memno").prop('disabled', restricted_fields.includes('membership_no')).addClass('rstrctedt');
					break;
				case 'batch_no':
					contobj.find("#add_form_field_batchno").prop('disabled', restricted_fields.includes('batch_no')).addClass('rstrctedt');
					break;
				case 'groups':
					contobj.find("#add_form_field_memtype").prop('disabled', restricted_fields.includes('groups')).addClass('rstrctedt');
					usersfuncs.groups_selector.disable();
					break;
				case 'desig_in_assoc':
					contobj.find("#add_form_field_desiginassoc").prop('disabled', restricted_fields.includes('desig_in_assoc')).addClass('rstrctedt');
					break;
				case 'role':
					contobj.find("input[name=role]").prop('disabled', restricted_fields.includes('role')).addClass('rstrctedt');
					break;
				case 'status':
					contobj.find("input[name=status]").prop('disabled', restricted_fields.includes('status')).addClass('rstrctedt');
					break;
				case 'profile_pic':
					contobj.find("#add_form_field_profilepic").prop('disabled', restricted_fields.includes('profile_pic'));
					contobj.find(".profile_image").addClass('d-none');
					contobj.find("#remove_profile_pic_selection").addClass('d-none');
					break;
				case 'remarks':
					contobj.find("#add_form_field_remarks").prop('disabled', restricted_fields.includes('remarks')).addClass('rstrctedt');
					break;	
			}

		});
	},

	removeEditRestrictions: function(){
		const contobj=$("#user_detail_add_edit_container");
		contobj.find("#add_form_field_memno, #add_form_field_batchno, #add_form_field_memtype, #add_form_field_desiginassoc, input[name=role], input[name=status], #add_form_field_profilepic, #add_form_field_remarks").prop('disabled', false).end()
			.find(".profile_image, #remove_profile_pic_selection").removeClass('d-none').end();
		// contobj.find("#add_form_field_expdt_picker").removeClass('non-editable rstrctedt').datepicker( "option", "disabled", false );
		contobj.find("#add_form_field_joiningdt_picker").removeClass('non-editable rstrctedt').datepicker( "option", "disabled", false ).siblings('button.ui-datepicker-trigger')?.removeClass('d-none');
						
		contobj.find('.rstrctedt').removeClass('rstrctedt');	
		usersfuncs.groups_selector.enable();
	},


	initializeRolesSelector:function(){
		var contobj=$("#user_detail_add_edit_container");
		if(contobj.find("#add_form_field_role").hasClass('select2-hidden-accessible'))
			contobj.find("#add_form_field_role").select2('destroy');
		contobj.find("#add_form_field_role").select2({
			minLength:0,
			tags:true,
			tokenSeparators: [','],
			width:'200px',
			placeholder:'Select/add one or more roles...',
			data:usersfuncs.user_roles,

			multiple: true

		});

	},




	backToList:function(e){
		// if(typeof e=='object' && e.hasOwnProperty('data') && e.data.hasOwnProperty('self')){
			// var self=e.data.self;
		// }else{
			// var self=usersfuncs;
		// }


		// $("#back-to-list-button").addClass('d-none');
		// $("#refresh-list-button").removeClass('d-none');
		// $("#add-record-button").removeClass('d-none');
		// $("#edit-record-button").addClass('d-none');
		// $("#user_list_container").removeClass('d-none');
		// $("#user_detail_view_container").addClass('d-none');
		// $("#user_detail_add_edit_container").addClass('d-none');

		// self.setheaderBarText("Users List");



	},


	refreshList:function(e){
		if(typeof e=='object' && e.hasOwnProperty('data') && e.data.hasOwnProperty('self')){
			var self=e.data.self;
		}else{
			var self=usersfuncs;
		}

		var currpage=self.paginationdata.curr_page;

		var options={pno:currpage,successResponseHandler:self.onListRefresh};
		self.getList(options);
		return false;

	},


	handleAddUserResponse:function(resp){
		var self=usersfuncs;
		$(".form-control").removeClass("error-field");

		if(resp.error_code==0){
			var message_container = '.alert-success';
			$("#record-add-cancel-button>i:eq(0)").next('span').html('Close');
			$("form[name=adduserform]").find(".error-field").removeClass('error-field').end().get(0).reset();
			$("#add_form_field_title").val('');
			$("#add_form_field_role_REGULAR").prop('checked',true);
			$("#add_form_field_status_y").prop('checked',true).parents('div.form-group').show();
			$('#img_del_marked_msg').addClass('d-none');
			$('#profile_pic_img').attr('src',""); // empty the profile image src
			$('#add_form_field_profilepic').val(''); // empty the profile pic file input
			$("#add_form_field_dob_picker").datepicker('setDate', null);
			$("#add_form_field_annv_picker").datepicker('setDate', null);
			$("#spousdob").datepicker('setDate', null);
			// $("#add_form_field_expdt_picker").datepicker('setDate', def_exp_dt); // set the default exp date
			$('input[name=gender]').prop('checked', false);
			$('#add_form_field_gender_M').prop('checked', true);
			$('#add_form_field_memtype').val('Student');
			$("#add_form_field_dnd").attr('checked', false);
			// $("#add_form_field_title").focus();

			usersfuncs.rebuildGroupOptions(resp.other_data.groups, resp.other_data.default_groups);

			document.querySelector('.main-content').scrollIntoView(true);
			setTimeout(()=>{
				$("#add_form_field_dnd").focus();
			},0);
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

	handleUpdateUserResponse:function(resp){
		var self=usersfuncs;

		var mode_container='user_detail_add_edit_container';
		$(".form-control").removeClass("error-field");

		if(resp.error_code==0){
			var back_to_mode=$("#record-add-cancel-button").data('back-to');
			// alert('aftersave: '+back_to_mode);
			mode_container=(back_to_mode!='list-mode')?'user_detail_view_container':'user_list_container';
			var message_container = '.alert-success';

			// if(resp.other_data.new_roles.length>0){
			// 	for(prop in resp.other_data.new_roles){
			// 		usersfuncs.user_roles.push(resp.other_data.new_roles[prop]);
			// 		usersfuncs.all_user_roles.push(resp.other_data.new_roles[prop]);
			// 	}
			// }


			// usersfuncs.initializeRolesSelector();
			// $('#add_form_field_role').val(resp.other_data.roleids_for_showing_selected).trigger('change');


			// if(resp.other_data.recordid==resp.other_data.loggedin_user_id){
			// 	// The user has edited his details

			// 	// $(".navbar-header span.user-info").html("Hi "+resp.other_data.name);
			// 	parent.location.reload();
			// }else{
				let email = $("#add_form_field_email").val().trim();
				let mobile = $("#add_form_field_mobile").val().trim();
				mobile = (resp.other_data.mobile!='')?resp.other_data.mobile:mobile;
				$("#add_form_field_mobile").val(mobile);
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
				if(resp.other_data.recordid==resp.other_data.loggedin_user_id){
					// The user has edited his own profile
					$('.user-info>.user-name').text(resp.other_data.profile_details.name);
				}

				usersfuncs.rebuildGroupOptions(resp.other_data.groups, resp.other_data.selected_groups);

			// }
			setTimeout(()=>{
				$("#add_form_field_dnd").focus();
			},0);
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

	saveUserDetails:function(formelem){

		var self=usersfuncs;
		var data_mode=$(formelem).data('mode');

		var res = self.validateUserDetails({mode:data_mode});
		if(res.error_fields.length>0){

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


	onPGUserSelectDeselect:function(e){
		e.preventDefault();
		var val = $("input[name=is_pg_user]:checked").val();

		$('#add_form_field_pglist .pgcheckbox').prop('checked','');

		if(val == 'Y'){
			// show the list
			$('#add_form_field_pglist_cont').removeClass('d-none');

		}else{
			// hide the list
			$('#add_form_field_pglist_cont').addClass('d-none');
		}

	},


	validateUserDetails:function(opts){
		var errors = [], error_fields=[];
		var nameRegex = /^[a-zA-Z\s\-]+$/;
		var phoneRegex = /^\+?[0-9]+$/;
		var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
		// return {'errors': errors, 'error_fields': error_fields}; // for testing php validation
		let mode='add-user';
		// var pp_max_filesize=usersfuncs.pp_max_filesize;
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
		var spouseName = $('#add_form_field_spouse_fname').val().trim();
		var spouseWhatsApp = $('#add_form_field_spouse_whatsapp').val().trim();
		var spouseEmail = $('#add_form_field_spouse_email').val().trim();
		let batch_no=$.trim(frm.find('#add_form_field_batchno').val());
		let dob=$.trim(frm.find('#add_form_field_dob').val());
		let res_city=$.trim(frm.find('#add_form_field_rescity').val());
		let res_addrline1 =$.trim(frm.find('#add_form_field_resaddrline1').val());
	 	let res_pin=$.trim(frm.find('#add_form_field_respin').val());
	 	
	 	let fb_accnt=$.trim(frm.find('#add_form_field_fbaccnt').val());
		let x_accnt=$.trim(frm.find('#add_form_field_xaccnt').val());
		let linkedin_accnt=$.trim(frm.find('#add_form_field_linkedinaccnt').val());
		let website=$.trim(frm.find('#add_form_field_website').val());
		
		let mem_no=$.trim(frm.find('#add_form_field_memno').val());
		//let sector = $.trim(frm.find('#add_form_field_sector').val());
		let desig=$.trim(frm.find('#add_form_field_desiginassoc').val());
		let role=$.trim(frm.find('input[name=role]:checked').val());
		let user_status =frm.find('input[name=status]:checked').val();
		//let remarks =frm.find('#add_form_field_remarks:not(:disabled)').val() || '';

		let membership_fee = $.trim($('#add_form_field_membershipfee').val());
		let payment_status = $.trim($('#add_form_field_paymentstatus').val());
		let paid_on = $.trim(frm.find('#add_form_field_paidon').val());
		let payment_mode = $.trim($('#add_form_field_paymentmode').val());
		let bank_ref = $.trim($('#add_form_field_bnkref').val());
		let payment_active = !$('#add_form_field_paymentstatus').hasClass('non-editable');

		// let exp_dt=$.trim(frm.find('#add_form_field_expdt').val());
		

		if(!frm.find('#add_form_field_title').hasClass('rstrctedt') && title == ''){
			errors.push('Salutation is required.');
			error_fields.push('#add_form_field_title');
			$("#add_form_field_title").addClass("error-field");

		}else if(!frm.find('#add_form_field_title').hasClass('rstrctedt') && usersfuncs.salutaions.indexOf(title)==-1){
			errors.push('Salutation should be one of these: '+usersfuncs.salutaions.join(', '));
			error_fields.push('#add_form_field_title');
			$("#add_form_field_title").addClass("error-field");

		}else if(!frm.find('#add_form_field_fname').hasClass('rstrctedt') && fname == ''){
			errors.push('First name is required.');
			error_fields.push('#add_form_field_fname');
			$("#add_form_field_fname").addClass("error-field");

		}else if(!usersfuncs.name_pattern.test(fname)){
			errors.push('The first name has invalid characters.');
			error_fields.push('#add_form_field_fname');
			$("#add_form_field_fname").addClass("error-field");

		}else if(mname!='' && !usersfuncs.name_pattern.test(mname)){
			errors.push('The middle name has invalid characters.');
			error_fields.push('#add_form_field_mname');
			$("#add_form_field_mname").addClass("error-field");

		}else if(!frm.find('#add_form_field_lname').hasClass('rstrctedt') && lname==''){
			errors.push('Surname is required.');
			error_fields.push('#add_form_field_lname');
			$("#add_form_field_lname").addClass("error-field");

		}else if(lname!='' && !usersfuncs.name_pattern.test(lname)){
			errors.push('The surname has invalid characters.');
			error_fields.push('#add_form_field_lname');
			$("#add_form_field_lname").addClass("error-field");

		}else if(!frm.find('#add_form_field_email').hasClass('rstrctedt') && email == ''){
			errors.push('A unique email address is required.');
			error_fields.push('#add_form_field_email');
			$("#add_form_field_email").addClass("error-field");
		}else if(!frm.find('#add_form_field_email').hasClass('rstrctedt') && !usersfuncs.email_pattern.test(email)){
			errors.push('The provided email address is invalid.');
			error_fields.push('#add_form_field_email');
			$("#add_form_field_email").addClass("error-field");
		}else if(!frm.find('#add_form_field_email').hasClass('rstrctedt') && email.length>255){
			errors.push('The email address is too long.');
			error_fields.push('#add_form_field_email');
			$("#add_form_field_email").addClass("error-field");
		}else if(mobile==''){
			errors.push('The WhatsApp number is required.');
			error_fields.push('#add_form_field_mobile');
			$("#add_form_field_mobile").addClass("error-field");
		}else if(!usersfuncs.mobile_pattern.test(mobile)){
			errors.push('The WhatsApp number is not valid.');
			error_fields.push('#add_form_field_mobile');
			$("#add_form_field_mobile").addClass("error-field");
		}else if(mobile2!='' && !usersfuncs.mobile_pattern.test(mobile2)){
			errors.push('The alternate mobile number is not valid.');
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
			errors.push('Please provide a valid url of the website, starting with https:// or http://');
			error_fields.push('#add_form_field_website');
			$("#add_form_field_website").addClass("error-field");
		}else if(gender==''){
			errors.push('The gender is required.');
			error_fields.push('#add_form_field_gender_M');
			$("#add_form_field_gender_M").addClass("error-field");
		}else if(gender!='M' && gender!='F'){
			errors.push('The gender value is invalid.');
			error_fields.push('#add_form_field_gender_M');
			$("#add_form_field_gender_M").addClass("error-field");
		}else if(dob==''){
			errors.push('The date of birth is required.');
			error_fields.push('#add_form_field_dob_picker');
			$("#add_form_field_dob_picker").addClass("error-field");
		}else if(spouseName.length > 0 && !nameRegex.test(spouseName)){
		  	errors.push('Only alphabets, hyphen, and spaces are allowed.');
			error_fields.push('#add_form_field_spouse_fname');
			$("#add_form_field_spouse_fname").addClass("error-field");  
		}else if(spouseWhatsApp.length > 0 && !phoneRegex.test(spouseWhatsApp)){
		   errors.push('Only digits with an optional "+" at the beginning are allowed.');
			error_fields.push('#add_form_field_spouse_whatsapp');
			$("#add_form_field_spouse_whatsapp").addClass("error-field"); 
		}else if(spouseEmail.length > 0 && !emailRegex.test(spouseEmail)){
		    errors.push('Must be a valid email address.');
			error_fields.push('#add_form_field_spouse_email');
			$("#add_form_field_spouse_email").addClass("error-field");    
		}
// 		else if(res_city==''){
// 			errors.push('The residence city is required.');
// 			error_fields.push('#add_form_field_rescity');
// 			$("#add_form_field_rescity").addClass("error-field");
// 		}
		else if(payment_active==true && payment_status=='Paid' && !/^\d+$/.test(membership_fee)){
			errors.push('Membership fee is required for paid payment status.');
			error_fields.push('#add_form_field_membershipfee');
			$("#add_form_field_membershipfee").addClass("error-field");
		}else if(payment_active==true && payment_status!=='Paid' && (paid_on!=='' || payment_mode!=='' || bank_ref!='') ){
			errors.push('You have either provide the payment mode, payment date or the bank reference so please set the payment status as \"Paid\".');
			error_fields.push('#add_form_field_paymentstatus');
			$("#add_form_field_paymentstatus").addClass("error-field");
		}else if(payment_active==true && payment_status=='Paid' && paid_on==''){
			errors.push('Please select the date of payment.');
			error_fields.push('#add_form_field_paidon_picker');
			$("#add_form_field_paidon_picker").addClass("error-field");
		}else if(payment_active==true && payment_status=='Paid' && payment_mode==''){
			errors.push('Please select the mode of payment.');
			error_fields.push('#add_form_field_paymentmode');
			$("#add_form_field_paymentmode").addClass("error-field");
		}else if(payment_active==true && payment_status=='Paid' && payment_mode!=='Cash' && bank_ref==''){
			errors.push('Please enter the bank reference ID for the payment.');
			error_fields.push('#add_form_field_bnkref');
			$("#add_form_field_bnkref").addClass("error-field");
		}/*else if(exp_dt==''){
			errors.push('The membership expiry date is required.');
			error_fields.push('#add_form_field_expdt_picker');
			$("#add_form_field_expdt_picker").addClass("error-field");
		}*/else if(!frm.find('#add_form_field_memno').hasClass('rstrctedt') && mem_no!='' && !usersfuncs.memno_pattern.test(mem_no) ){
			errors.push('The membership No. contains invalid characters.');
			error_fields.push('#add_form_field_memno');
			$("#add_form_field_memno").addClass("error-field");
		}else if(!frm.find('input[name=role]').hasClass('rstrctedt') && role==''){
			errors.push('Please select a role for the member.');
			error_fields.push('#add_form_field_role_REGULAR');
			$("#add_form_field_role_REGULAR").addClass("error-field");
		}		


		return {'errors': errors, 'error_fields': error_fields};

	},

	rebuildGroupOptions: function(groups, default_selection){
		usersfuncs.groups_selector.dropdownClear();
		usersfuncs.groups_selector.clear();
		$('#add_form_field_memtype').empty();
		groups.forEach(function(grp){
			let opt = $('<option>');
			opt.val(grp.id);
			opt.text(grp.grp);
			$('#add_form_field_memtype').append(opt);

		});
		$('#add_form_field_memtype').val(default_selection);
		// $('#add_form_field_memtype').tokenize2().trigger('tokenize:tokens:remove');
		// let tok = usersfuncs.initializeGroupsSelector('add_form_field_memtype');
		usersfuncs.groups_selector.remap();
		// usersfuncs.groups_selector.trigger('tokenize:load');
	},

	initializeGroupsSelector: function(elem_id){
		// $(elem).tokenize2('destroy');

		usersfuncs.groups_selector =  $('#'+elem_id).tokenize2({
			// max number of tags
			tokensMaxItems: 0,
			// allow you to create custom tokens
			tokensAllowCustom:true,
			// max items in the dropdown
			dropdownMaxItems: 50,
			// allow you to choose if the first item is automatically selected on search
			dropdownSelectFirstItem:true,
			// minimum/maximum of characters required to start searching
			searchMinLength: 0,
			searchMaxLength: 0,
			// specify if Tokenize2 will search from the begining of a string
			searchFromStart:false,
			// choose if you want your search highlighted in the result dropdown
			searchHighlight:true,
			// custom delimiter
			delimiter:',',
			// display no results message
			displayNoResultsMessage:false,
			noResultsMessageText:'No results mached "%s"',
			// custom delimiter
			delimiter:',',
			// data source
			dataSource:'select',
			// waiting time between each search
			debounce: 0,
			// custom placeholder text
			placeholder:'Type and select',
			// enable sortable
			// requires jQuery UI
			sortable:false,
			// tabIndex
			tabIndex: 0,
			// allows empty values
			allowEmptyValues:false,
			// z-index
			zIndexMargin: 500

		});

	},


	openAddUserForm:function(e){
		if(typeof e=='object' && e.hasOwnProperty('data') && e.data.hasOwnProperty('self')){
			var self=e.data.self;
		}else{
			var self=usersfuncs;
		}
		document.adduserform.reset();

		usersfuncs.removeEditRestrictions();

		usersfuncs.dep_rowno_max=-1;
		$(".form-control").removeClass("error-field");
		$("#refresh-list-button").addClass('d-none');
		$("#add-record-button").addClass('d-none');
		$("#edit-record-button").addClass('d-none');
		$("#user_list_container").addClass('d-none');
		$("#user_detail_view_container").addClass('d-none');
		$("#user_detail_add_edit_container").removeClass('d-none').find("#panel-heading-text").text('Add Member').end();
		$('#msgFrm').removeClass('d-none');
			
		$(".back-to-list-button").removeClass('d-none');
		$("#add_password_msg").removeClass('d-none');
		$("#edit_password_msg").addClass('d-none');

		$("#user_detail_add_edit_container").find("#record-save-button").removeClass('d-none disabled').attr('disabled', false).find("span:eq(0)").html('Add Member').end().end().find("#add_edit_mode").val('createUser').end().find("#add_edit_recordid").val('').end().find("#add_edit_usertype").val('').end().find("#record-add-cancel-button").data('back-to','').attr('href',"users.php#"+usersfuncs.prev_page_hash);
		$("form[name=adduserform]").data('mode','add-user').find(".error-field").removeClass('error-field').end().find('input[name=status]').attr('checked',false).end().get(0).reset();

		// $("form[name=adduserform]").find("#add_form_field_role").select2('val','');
		$("form[name=adduserform]").find("#add_form_field_title").val('');

		$("#add_form_field_role_ADMIN").prop('checked',false);
		$("#add_form_field_role_REGULAR").prop('checked',true);
		$("#add_form_field_status_n").prop('checked',false);
		$("#add_form_field_status_y").prop('checked',true).parents('div.form-group').show();

		$("#add_form_field_email").val('').siblings('.email-icon-form-input').data('url','').toggleClass('d-none',true);
		$("#add_form_field_mobile").val('').siblings('.wa-icon-form-input').data('url','').toggleClass('d-none', true).end().siblings('.tel-icon-form-input').data('url','').toggleClass('d-none', true);

		$('#user_detail_add_edit_container .profile_image').addClass('d-none'); // hide the profile image viewer section
		$('#delete_profile_pic').val('0');
		$('#img_del_marked_msg').addClass('d-none');
		$('#profile_pic_img').attr('src',""); // empty the profile image src
		$('#add_form_field_profilepic').val(''); // empty the profile pic file input
		$("#add_form_field_dob_picker").datepicker('setDate', null);
		$("#add_form_field_annv_picker").datepicker('setDate', null);
		$('input[name=gender]').prop('checked', false);
		$('#add_form_field_gender_M').prop('checked', true);
		usersfuncs.groups_selector.dropdownClear();
		usersfuncs.groups_selector.clear();
		$('#add_form_field_memtype').val(['1']);
		// usersfuncs.initializeGroupsSelector('add_form_field_memtype');
		usersfuncs.groups_selector.remap();
				
		self.setheaderBarText("");
		$("#default_pwd_msg").removeClass('d-none');

		$("#add_form_field_dnd").attr('checked', false);
		// $("#add_form_field_joiningdt").text('').parents('#joining_dt_row').addClass('d-none');
		// $("#add_form_field_expdt_picker").datepicker('setDate', def_exp_dt);
		// $("#add_form_field_title").focus();
		document.querySelector('.main-content').scrollIntoView(true);
		setTimeout(()=>{
			$("#add_form_field_dnd").focus();
		},100);
		
		$('#add_form_field_sector>option').removeClass('d-none');
		$('#add_form_field_sector>option[data-active=n]').addClass('d-none');
		// membership no would be added manually so commented out the below line
		// $('#add_form_field_memno').parents('.form-group:eq(0)'); // Hiding the membership no field from users view as manual adding of membership no will not be allowed, it would be generated automatically

		// Preparing the payment fields to allow entry of payment details
		$('#user_detail_add_edit_container').find('.pmtdtls input[type=text]').removeClass('non-editable').end().find('.pmtdtls select').removeClass('non-editable').end(); 
		//.find('#add_form_field_pmtfailmsg').parents('.pmtdtls:eq(0)').addClass('d-none').end().end();
		$('#add_form_field_paidon_picker').datepicker('enable');
		//////////////

	},
	deleteUser:function(ev){
		var elem = $(ev.currentTarget);
		var id =elem.data('recid');
		// alert(id);
		if(confirm('Do you want to delete this user?')){

			var rec_details = {};
			common_js_funcs.callServer({cache:'no-cache',async:false,dataType:'json',type:'post',url:usersfuncs.ajax_data_script,params:{mode:'deleteUser', user_id:id},
				successResponseHandler:function(resp,status,xhrobj){
					if(resp.error_code == 0)
						usersfuncs.handleDeleteResp(resp);
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
		usersfuncs.refreshList();
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
	

	sendContactReq: function(e){
		e.preventDefault();
		e.stopPropagation();
		$("#common-processing-overlay").addClass('d-none').css('zIndex','');
		let modal_box = $('#reqcontactdialog');
		modal_box.find('.reqcontacterror').text('').addClass('d-none').end().find('#reqcontactform')[0].reset();
		const clicked_elem = $(e.currentTarget);
		const rec_id = clicked_elem.data('recid');
		const mem_nm = clicked_elem.data('mnm');
		const mem_bno = clicked_elem.data('bno');
		$('#reqcontactstaticBackdropLabel').html('Send Contact Request');
		$('#reqcontact_mem_id').val(rec_id);
		$('#reqcontact_to').text(`${mem_nm}`);
		$('#reqcontact_msg').val('');
		let dg = modal_box.modal({
			backdrop: 'static',
			keyboard: false,
		});
		dg.on('shown.bs.modal', function(e){
			$('#reqcontact_msg').focus();
			modal_box.find('#reqcontactform').on('submit', event => {
				e.stopPropagation();
				$(this).find('.error-field').removeClass('error-field');
				modal_box.find('.reqcontacterror').text('').addClass('d-none');
				let msg = $('#reqcontact_msg').val();
				if(msg==''){
					alert('Please enter a short message specifying your purpose.');
					modal_box.find("#reqcontact_msg").addClass("error-field").get(0).focus();
					return false;
				}
				
				$("#common-processing-overlay").removeClass('d-none').css('zIndex','2000');
				return true;

			});

		});
		dg.on('hidden.bs.modal', function(e){
			dg.off('shown.bs.modal');
			dg.off('hidden.bs.modal');
			modal_box.find('#reqcontactform').off('submit');
			dg.modal('dispose');
			delete dg, modal_box;
		});	
		
	},


	handleReqContactResponse: function(resp){
		$("#common-processing-overlay").addClass('d-none').css('zIndex','');
		if(resp.error_code==0){
			$('#reqcontactdialog').modal('hide');
		}else{
			if(resp.error_field ?? false){
				$(resp.error_field).addClass('error-field');
				$(resp.error_field)[0].focus();
			}
		}
		alert(resp.message);
	},



	onHashChange:function(e){
		var hash=location.hash.replace(/^#/,'');
		// alert(hash);
		if(usersfuncs.curr_page_hash!=usersfuncs.prev_page_hash){
			usersfuncs.prev_page_hash=usersfuncs.curr_page_hash;
		}
		usersfuncs.curr_page_hash=hash;

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
			case 'addmeeting':
								$('.alert-success, .alert-danger').addClass('d-none');
								$('#msgFrm').removeClass('d-none');
								usersfuncs.openAddUserForm();
								break;

			case 'view':
							$('.alert-success, .alert-danger').addClass('d-none');

							if(hash_params.hasOwnProperty('recid') && hash_params.recid!=''){
								usersfuncs.openRecordForViewing(hash_params.recid);
							}else{
								location.hash=usersfuncs.prev_page_hash;
							}
							break;

			case 'edit':
							$('.alert-success, .alert-danger').addClass('d-none');
							$('#msgFrm').removeClass('d-none');
							if(hash_params.hasOwnProperty('recid') && hash_params.recid!=''){
								usersfuncs.openRecordForEditing(hash_params.recid);

							}else{
								location.hash=usersfuncs.prev_page_hash;
							}
							break;



			default:

					$('.alert-success, .alert-danger').addClass('d-none');
					$('#msgFrm').removeClass('d-none');
					var params={mode:'getList',pno:1, searchdata:"[]", sortdata:JSON.stringify(usersfuncs.sortparams), listformat:'html'};

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

					usersfuncs.searchparams=JSON.parse(params['searchdata']);
					usersfuncs.sortparams=JSON.parse(params['sortdata']);

					if(usersfuncs.sortparams.length==0){
						usersfuncs.sortparams.push(usersfuncs.default_sort);
						params['sortdata']=JSON.stringify(usersfuncs.sortparams);
					}

					// Set the selected sort field and order in the sorting panel
					$('#orderlist-sorton').val(usersfuncs.sortparams[0].sorton);
					$('#orderlist_sortorder_'+usersfuncs.sortparams[0].sortorder).attr('checked', true);

					// Set the selected search criteria in the search panel
					if(usersfuncs.searchparams.length>0){
							$.each(usersfuncs.searchparams , function(idx,data) {
									//console.log(data);
									switch (data.searchon) {

										case 'name':
											$("#search-field_name").val(data.searchtext);
											break;
										case 'email':
											$("#search-field_email").val(data.searchtext);
											break;
										case 'mob':
											$("#search-search_mob").val(data.searchtext);
											break;
										case 'batch_no':
											$("#search-field_batch-no").val(data.searchtext);
											break;
										case 'residence_city':
											$("#search-field_residence-city").val(data.searchtext);
											break;
										case 'residence_country':
											$("#search-field_residence-country").val(data.searchtext);
											break;
										case 'blood_grp':
											$("#search-field_blood-grp").val(data.searchtext);
											break;
										case 'work_company':
											$("#search-field_work-company").val(data.searchtext);
											break;
										// case 'work_ind':
										// 	$("#search-field_work-ind").val(data.searchtext);
										// 	break;
										case 'sector_id':
											$("#search-field_sector").val(data.searchtext);
											break;
										case 'grp_id':
											$("#search-field_grp").val(data.searchtext);
											break;
											
										// case 'joining_dt':
										// 	$("#search-field_joinedafterdt_picker").datepicker('setDate', new Date(data.searchtext));
										// 	break;
									}

							});
							
					}
					
					if(usersfuncs.searchparams.length>0){
						if(usersfuncs.searchparams[0]['searchon'] == 'status')
							$("#search_text").val(usersfuncs.searchparams[0]['searchtext'][0]=='1'?'Active':'Inactive');
						else
							$("#search_text").val(usersfuncs.searchparams[0]['searchtext'] || '');

						$("#search_field").val(usersfuncs.searchparams[0]['searchon'] || '');
						//$('#close_box').removeClass('d-none');

					}

					$("#common-processing-overlay").removeClass('d-none');

					common_js_funcs.callServer({cache:'no-cache',async:true,dataType:'json',type:'post', url:self.ajax_data_script,params:params,successResponseHandler:usersfuncs.showList,successResponseHandlerParams:{self:usersfuncs}});

					usersfuncs.showHidePanel('user_search_toggle');
					usersfuncs.showHidePanel('user_sort_toggle');

		}


		//$("[data-rel='tooltip']").tooltip({html:true, placement:'top', container:'body'});




	}

}