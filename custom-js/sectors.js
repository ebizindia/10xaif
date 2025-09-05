var sectorfuncs={
	searchparams:[],  /* [{searchon:'',searchtype:'',searchtext:''},{},..] */
	sortparams:[],  /* [{sorton:'',sortorder:''},{},..] */
	default_sort:{sorton:'sector',sortorder:'ASC'},
	paginationdata:{},
	defaultleadtabtext:'Sectors',
	filtersapplied:[],
	statuschangestarted:0,
	ajax_data_script:'sectors.php',
	curr_page_hash:'',
	prev_page_hash:'',
	name_pattern: /^[A-Z0-9_ -]+$/i,
	pp_max_filesize:0,
	
	initiateStatusChange:function(statuscell){
		var self=sectorfuncs;

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
		sectorfuncs.setPanelVisibilityStatus('sector_search_toggle', elem.hasClass('search-form-visible')?'':'visible'); // set closed status for the search panel
		sectorfuncs.showHidePanel('sector_search_toggle');
	},


	setPanelVisibilityStatus: function(panel, status){
		if (typeof(Storage) !== "undefined") {
			localStorage[panel] = status;
		} else {
			Cookies.set(panel, status, {path : '/'});
		}
	},

	showHidePanel: function(panel){
		if(panel === 'sector_search_toggle'){
			let show_srch_form = false;
			if (typeof(Storage) !== "undefined") {
				srch_frm_visible = localStorage.sector_search_toggle;
			} else {
				srch_frm_visible = Cookies.get('sector_search_toggle');
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
				$("#search-field_sector").focus();
			}
		}
	},


	confirmAndExecuteStatusChange:function(statuscell){
		var self=sectorfuncs;

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
			var options={cache:'no-cache',dataType:'json',async:true,type:'post',url:sectorfuncs.ajax_data_script+"?mode=changeStatus",data:"newstatus="+newstatus+"&recordid="+userid,successResponseHandler:sectorfuncs.handleStatusChangeResponse,successResponseHandlerParams:{statuscell:statuscell,rowelem:rowelem}};
			common_js_funcs.callServer(options);
			$(statuscell).removeClass("status-grn");
			$(statuscell).removeClass("status-red");
			if(parseInt(newstatus)==1){
				$(statuscell).addClass("status-grn");
			}else{
				$(statuscell).addClass("status-red");
			}
		}else{
			sectorfuncs.statuschangestarted=0;
			sectorfuncs.abortStatusChange(statuscell);

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
							sectorfuncs.statuschangestarted=0;
							sectorfuncs.abortStatusChange(statuscell);
						}
					},
					"Yes":	{
						"label": "Yes",
						"className": "btn-danger btn-primary",
						"callback": function(ev){

							var options={cache:'no-cache',dataType:'json',async:true,type:'post',url:sectorfuncs.ajax_data_script+"?mode=changeStatus",data:"newstatus="+newstatus+"&recordid="+userid,successResponseHandler:sectorfuncs.handleStatusChangeResponse,successResponseHandlerParams:{statuscell:statuscell,rowelem:rowelem}};
							common_js_funcs.callServer(options);
						}
					}

				}

		});*/




	},

	abortStatusChange:function(statuscell){
		var self=sectorfuncs;

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
		var self=sectorfuncs;

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
		var self=sectorfuncs;
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
			$('#heading_rec_cnt').text('(0)');
			
		}

		$("#add-record-button").removeClass('d-none');
		$("#refresh-list-button").removeClass('d-none');
		$(".back-to-list-button").addClass('d-none').attr('href',"sectors.php#"+sectorfuncs.curr_page_hash);
		$("#edit-record-button").addClass('d-none');
		self.paginationdata=resp[1].paginationdata;

		self.setSortOrderIcon();


	},


	onListRefresh:function(resp,otherparams){
		var self=sectorfuncs;
		$("#common-processing-overlay").addClass('d-none');
		var listhtml=resp[1].list;
		$("#userlistbox").html(listhtml);
		self.paginationdata=resp[1].paginationdata;
		self.setSortOrderIcon();
	},

	expandFilterBox:function(){
		var self=sectorfuncs;
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
		var self=sectorfuncs;
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
		var self=sectorfuncs;
		self.searchparams=[];
	},

	setSearchParams:function(obj){
		var self=sectorfuncs;
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

		var self=sectorfuncs;
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

		sectorfuncs.resetSearchParamsObj();
		let fld = '';
		$('.panel-search .srchfld').each(function(i, el){
			let val = $.trim($(el).val());
			if(val!=''){
				fld = $(el).data('fld');
				sectorfuncs.setSearchParams({searchon:$(el).data('fld'),searchtype:$(el).data('type'),searchtext:val});
			}
		});

		if(sectorfuncs.searchparams.length<=0)
			return false;

		var options={pno:1};
		sectorfuncs.getList(options);
		return false;
	},


	changePage:function(ev){
		ev.preventDefault();
		if(!$(ev.currentTarget).parent().hasClass('disabled')){
			var self=sectorfuncs;
			var pno=$(ev.currentTarget).data('page');
			self.getList({pno:pno});
			// return false;
		}

	},



	sortTable:function(e){
		var self=e.data.self;

		var elemid=e.currentTarget.id;
		var elemidparts=elemid.split('_');
		var sorton=elemidparts[1].replace('-','_');
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

		sectorfuncs.sortparams=[];
		sectorfuncs.sortparams.push({sorton:sorton, sortorder:sortorder});
		var options={pno:pno};
		sectorfuncs.getList(options);

	},



	setSortOrderIcon:function(){
		var self=sectorfuncs;
		if(self.sortparams.length>0){
			var sorton = self.sortparams[0].sorton == 'user_type'?'usertype':self.sortparams[0].sorton.replace('_','-');
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
		var self=sectorfuncs;
		if(recordid=='')
			return false;

		$("#record-save-button").addClass('d-none').attr('disabled', 'disabled');
		$("#common-processing-overlay").removeClass('d-none');
		var coming_from='';
		var options={mode:'viewrecord',recordid:recordid,loadingmsg:"Opening the lead '"+recordid+"' for viewing...",leadtabtext:'View Ad Banner Details',coming_from:coming_from}
		self.openRecord(options);
		 return false;

	},

	openRecordForEditing:function(recordid){
		var self=sectorfuncs;
		if(recordid=='')
			return false;

		document.addrecform.reset();
		$(".form-control").removeClass("error-field");
		$("#record-save-button").removeClass('d-none').attr('disabled', false);
		// $("#add_form_field_role").next('.select2-container').removeClass("error-field");
		$("#common-processing-overlay").removeClass('d-none');
		$("#record-add-cancel-button").attr('href',"sectors.php#"+sectorfuncs.prev_page_hash);
		$('#msgFrm').removeClass('d-none');
		var coming_from='';//elem.data('in-mode');
		var options={mode:'editrecord',recordid:recordid,leadtabtext:'Edit Sector',coming_from:coming_from}
		self.openRecord(options);
		return false;

	},


	openRecord:function(options){
		var self=sectorfuncs;
		var opts={leadtabtext:'Sector Details'};
		$.extend(true,opts,options);

		sectorfuncs.dep_rowno_max=-1;

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
					location.hash=sectorfuncs.prev_page_hash;
					return;
				}

				sectorfuncs.removeEditRestrictions();

				let sector = resp[1].record_details.sector || '';
				let sector_disp = resp[1].record_details.sector_disp || '';
				let active = resp[1].record_details.active || '';
				

				var contobj=$("#rec_detail_add_edit_container");

				$('.alert-danger').addClass('d-none').find('.alert-message').html('');
				$('#msgFrm').removeClass('d-none');
				contobj.find(".form-actions").removeClass('d-none');

				contobj.find("form[name=addrecform]:eq(0)").data('mode','edit-rec').find('input[name=status]').prop('checked',false).end().get(0).reset();

				$('.addonly').addClass('d-none');
				$('.editonly').removeClass('d-none');
				$('#edit_form_field_sector, input[name=active]').attr('disabled', false);
				$('#add_form_field_sector').attr('disabled', true)

				contobj.find("#add_edit_mode").val('updaterec');
				contobj.find("#add_edit_recordid").val(rec_id);
				contobj.find("input[name=status]").prop('checked', false);
				if(active!='')
					contobj.find("#edit_form_field_status_"+active).prop('checked', true);
				contobj.find("#edit_form_field_sector").val(sector).focus();

				let header_text = 'Edit Sector';
				
				contobj.find("#record-add-cancel-button").data('back-to',coming_from);
				contobj.find("#record-save-button>span:eq(0)").html('Save Changes');
				contobj.find("#panel-heading-text").text(header_text);
				contobj.find("#infoMsg").html('Edit Sector <b>' + sector_disp +  '</b>');
				sectorfuncs.setheaderBarText(header_text);

				sectorfuncs.applyEditRestrictions(resp[1].edit_restricted_fields);
				container_id='rec_detail_add_edit_container';


			}else{

				var message="Sorry, the edit window could not be opened (Server error).";
				if(resp[0]==1){
					message="Sorry, the edit window could not be opened (Sector ID missing).";
				}else if(resp[0]==2){
					message="Sorry, the edit window could not be opened (Server error).";
				}else if(resp[0]==3){
					message="Sorry, the edit window could not be opened (Invalid sector ID).";
				}

				alert(message);
				location.hash=sectorfuncs.prev_page_hash;
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
			// var self=sectorfuncs;
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
			var self=sectorfuncs;
		}

		var currpage=self.paginationdata.curr_page;

		var options={pno:currpage,successResponseHandler:self.onListRefresh};
		self.getList(options);
		return false;

	},


	handleAddRecResponse:function(resp){
		var self=sectorfuncs;
		$(".form-control").removeClass("error-field");

		if(resp.error_code==0){
			var message_container = '.alert-success';
			$("#record-add-cancel-button>i:eq(0)").next('span').html('Close');
			$("form[name=addrecform]").find(".error-field").removeClass('error-field').end().get(0).reset();
			$("#add_form_field_sector").focus();

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
		var self=sectorfuncs;

		var mode_container='rec_detail_add_edit_container';
		$(".form-control").removeClass("error-field");

		if(resp.error_code==0){
			
			var message_container = '.alert-success';

			$("#add_form_field_sector").focus();
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

		var self=sectorfuncs;
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
		// var pp_max_filesize=sectorfuncs.pp_max_filesize;
		$(".form-control").removeClass("error-field");
		// $("#add_form_field_role").next('.select2-container').removeClass("error-field");
		if(typeof opts=='object' && opts.hasOwnProperty('mode'))
			mode=opts.mode;

		const frm = $('#addrecform');
		let sector = active = sector_fld = '';
		if(mode=='add-rec'){
			sector_fld_id = '#add_form_field_sector';
			sector=$.trim($(sector_fld_id).val());
		}else{
			sector_fld_id = '#edit_form_field_sector';
			sector=$.trim($(sector_fld_id).val());
			active =frm.find('input[name=active]:checked').val();
		}
		
		if(sector == ''){
			errors.push('Sector is required.');
			error_fields.push(sector_fld_id);
			$(sector_fld_id).addClass("error-field");

		}else if(mode=='edit-rec' && active==''){
			errors.push('Please select a status for the sector.');
			error_fields.push('#edit_form_field_status_y');
			$("#edit_form_field_status_y").addClass("error-field");

		}

		return {'errors': errors, 'error_fields': error_fields};

	},

	openAddUserForm:function(e){
		if(typeof e=='object' && e.hasOwnProperty('data') && e.data.hasOwnProperty('self')){
			var self=e.data.self;
		}else{
			var self=sectorfuncs;
		}
		document.addrecform.reset();
		
		sectorfuncs.removeEditRestrictions();

		sectorfuncs.dep_rowno_max=-1;
		$(".form-control").removeClass("error-field");
		$("#refresh-list-button").addClass('d-none');
		$("#add-record-button").addClass('d-none');
		$("#edit-record-button").addClass('d-none');
		$("#rec_list_container").addClass('d-none');
		$("#rec_detail_add_edit_container").removeClass('d-none').find("#panel-heading-text").text('Add Sectors').end();
		$('#msgFrm').removeClass('d-none');
			
		$(".back-to-list-button").removeClass('d-none');
		
		$("#rec_detail_add_edit_container").find("#record-save-button>span:eq(0)").html('Add Sectors').end().find("#add_edit_mode").val('createrec').end().find("#add_edit_recordid").val('').end().find("#record-add-cancel-button").data('back-to','').attr('href',"sectors.php#"+sectorfuncs.prev_page_hash);
		$("form[name=addrecform]").data('mode','add-rec').find(".error-field").removeClass('error-field').end().find('input[name=active]').prop('checked',false).end().get(0).reset();

		// $("#add_form_field_status_n").prop('checked',false);
		// $("#add_form_field_status_y").prop('checked',true);

		self.setheaderBarText("");
		$('.addonly').removeClass('d-none');
		$('.editonly').addClass('d-none');
		$('#edit_form_field_sector, input[name=active]').attr('disabled', true);
		$('#add_form_field_sector').attr('disabled', false).focus();
		
		document.querySelector('.main-content').scrollIntoView(true);
		return false;

	},
	deleteSector:function(ev){
		ev.preventDefault();
		ev.stopPropagation();
		const elem = $(ev.currentTarget);
		let id =elem.data('recid');
		let sector =elem.data('sector');
		
		if(confirm(`Really delete the sector "${sector}" ?`)){

			let rec_details = {};
			common_js_funcs.callServer({cache:'no-cache',async:false,dataType:'json',type:'post',url:sectorfuncs.ajax_data_script,params:{mode:'deleteSector', rec_id:id},
				successResponseHandler:function(resp,status,xhrobj){
					if(resp.error_code == 0)
						sectorfuncs.handleDeleteResp(resp);
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
		sectorfuncs.refreshList();
	},

	closeAddUserForm:function(){
		var self =this;
		return true;

	},


	setheaderBarText:function(text){
		$("#header-bar-text").find(":first-child").html(text);
		// $('#panel-heading-text').text("Add user");

	},

	
	onHashChange:function(e){
		var hash=location.hash.replace(/^#/,'');
		// alert(hash);
		if(sectorfuncs.curr_page_hash!=sectorfuncs.prev_page_hash){
			sectorfuncs.prev_page_hash=sectorfuncs.curr_page_hash;
		}
		sectorfuncs.curr_page_hash=hash;

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
								sectorfuncs.openAddUserForm();
								break;

			case 'edit':
							$('.alert-success, .alert-danger').addClass('d-none');
							$('#msgFrm').removeClass('d-none');
							if(hash_params.hasOwnProperty('recid') && hash_params.recid!=''){
								sectorfuncs.openRecordForEditing(hash_params.recid);

							}else{
								location.hash=sectorfuncs.prev_page_hash;
							}
							break;



			default:

					$('.alert-success, .alert-danger').addClass('d-none');
					$('#msgFrm').removeClass('d-none');
					var params={mode:'getList',pno:1, searchdata:"[]", sortdata:JSON.stringify(sectorfuncs.sortparams), listformat:'html'};

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

					sectorfuncs.searchparams=JSON.parse(params['searchdata']);
					sectorfuncs.sortparams=JSON.parse(params['sortdata']);

					if(sectorfuncs.sortparams.length==0){
						sectorfuncs.sortparams.push(sectorfuncs.default_sort);
						params['sortdata']=JSON.stringify(sectorfuncs.sortparams);
					}

					if(sectorfuncs.searchparams.length>0){
							$.each(sectorfuncs.searchparams , function(idx,data) {
									//console.log(data);
									switch (data.searchon) {

										case 'sector':
											$("#search-field_sector").val(data.searchtext);
											break;
									}

							});
					}
					$("#common-processing-overlay").removeClass('d-none');

					common_js_funcs.callServer({cache:'no-cache',async:true,dataType:'json',type:'post', url:self.ajax_data_script,params:params,successResponseHandler:sectorfuncs.showList,successResponseHandlerParams:{self:sectorfuncs}});

					sectorfuncs.showHidePanel('sector_search_toggle');

		}


		//$("[data-rel='tooltip']").tooltip({html:true, placement:'top', container:'body'});




	}

}