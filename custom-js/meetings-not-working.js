var meetfuncs={
	searchparams:[],  /* [{searchon:'',searchtype:'',searchtext:''},{},..] */
	sortparams:[],  /* [{sorton:'',sortorder:''},{},..] */
	default_sort:{sorton:'meet_date',sortorder:'DESC'},
	paginationdata:{},
	defaultleadtabtext:'Events',
	filtersapplied:[],
	statuschangestarted:0,
	ajax_data_script:'meetings.php',
	curr_page_hash:'',
	prev_page_hash:'',
	name_pattern: /^[A-Z0-9_ -]+$/i,
	int_pattern: /^\d+$/,
	gst_pattern: /^\d+(\.\d{1,2})?$/,
	pp_max_filesize:0,
	default_list: true,

	addRow:function() {
        var tableBody = document.getElementById("session-rows");
        
        // Create a new row and cells
        var newRow = document.createElement("tr");
        
        // Time inputs cell
        var dateCell = document.createElement("td");
        dateCell.innerHTML = '<div class="form-row"><div class="col"><input type="text" name="time_from[]" id="time_from" placeholder="Start Time" class="form-control"></div><div class="col-auto d-flex align-items-center">To</div><div class="col"><input type="text" name="time_to[]" id="time_to" placeholder="End Time" class="form-control"></div></div><input type="hidden" name="agenda_id[]" value="">';
        newRow.appendChild(dateCell);
        
        // Topic input cell
        var topicCell = document.createElement("td");
        topicCell.innerHTML = '<textarea name="topic[]" id="topic" rows="3" placeholder="Enter Topic" class="form-control"></textarea>';
        newRow.appendChild(topicCell);
        
        // Actions cell
        var actionsCell = document.createElement("td");
        actionsCell.innerHTML = '<button type="button" class="btn btn-danger delete-row" onclick="meetfuncs.deleteRow(this)"> - </button>';
        newRow.appendChild(actionsCell);
        
        // Append the new row to the table body
        tableBody.appendChild(newRow);
    },

    // Function to delete a row
    deleteRow:function(button) {
        var row = button.closest("tr");
        row.remove();
    },
	
	initiateStatusChange:function(statuscell){
		var self=meetfuncs;

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
		var self=meetfuncs;

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
			var options={cache:'no-cache',dataType:'json',async:true,type:'post',url:meetfuncs.ajax_data_script+"?mode=changeStatus",data:"newstatus="+newstatus+"&recordid="+userid,successResponseHandler:meetfuncs.handleStatusChangeResponse,successResponseHandlerParams:{statuscell:statuscell,rowelem:rowelem}};
			common_js_funcs.callServer(options);
			$(statuscell).removeClass("status-grn");
			$(statuscell).removeClass("status-red");
			if(parseInt(newstatus)==1){
				$(statuscell).addClass("status-grn");
			}else{
				$(statuscell).addClass("status-red");
			}
		}else{
			meetfuncs.statuschangestarted=0;
			meetfuncs.abortStatusChange(statuscell);
		}
	},

	abortStatusChange:function(statuscell){
		var self=meetfuncs;

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
		var self=meetfuncs;

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
		var self=meetfuncs;
		var listhtml=resp[1].list;
		self.user_count=resp[1]['reccount'];
		$("#rec_list_container").removeClass('d-none');
		$("#rec_detail_add_edit_container").addClass('d-none');
		$("#common-processing-overlay").addClass('d-none');
		$("#meetlistbox").html(listhtml);
		
		if(resp[1].tot_rec_cnt>0){
			$('#heading_rec_cnt').text((resp[1]['reccount']==resp[1]['tot_rec_cnt'])?`(${resp[1]['tot_rec_cnt']})`:`(${resp[1]['reccount'] || 0} of ${resp[1]['tot_rec_cnt']})`);
		}else{
			$('#heading_rec_cnt').text('(0)')
		}
			
		$("#add-record-button").removeClass('d-none');
		$("#refresh-list-button").removeClass('d-none');
		$(".back-to-list-button").addClass('d-none').attr('href',"meetings.php#"+meetfuncs.curr_page_hash);
		$("#edit-record-button").addClass('d-none');
		self.paginationdata=resp[1].paginationdata;
		self.setSortOrderIcon();
	},

	onListRefresh:function(resp,otherparams){
		var self=meetfuncs;
		$("#common-processing-overlay").addClass('d-none');
		var listhtml=resp[1].list;
		$("#meetlistbox").html(listhtml);
		self.paginationdata=resp[1].paginationdata;
		self.setSortOrderIcon();
	},

	showLeadDetailsWindow:function(resp,otherparams){
        // Clear existing agenda rows first
        $("#session-rows").empty();

		const self=otherparams.self;
		let container_id='';
		$("#common-processing-overlay").addClass('d-none');
		const rec_id= resp[1].record_details.id ??''; 
		
		if(otherparams.mode=='editrecord'){
			var coming_from=otherparams.coming_from;

			if(rec_id!=''){
				if(resp[1].can_edit===false){
					location.hash=meetfuncs.prev_page_hash;
					return;
				}

				meetfuncs.removeEditRestrictions();

				let meet_date = resp[1].record_details.meet_date || '';
				let meet_time = resp[1].record_details.meet_time || '';
				let venue = resp[1].record_details.venue??'';
				let active = resp[1].record_details.active || '';
				const today_obj = new Date(resp[1].today);
				
				var contobj=$("#rec_detail_add_edit_container");

				$('.alert-danger').addClass('d-none').find('.alert-message').html('');
				$('#msgFrm').removeClass('d-none');
				contobj.find(".form-actions").removeClass('d-none');

				contobj.find("form[name=addmeetform]:eq(0)").data('mode','edit-rec').find('input[name=status]').attr('checked',false).end().get(0).reset();

				contobj.find("#add_edit_mode").val('updaterec');
				contobj.find("#add_edit_recordid").val(rec_id);
				contobj.find("#meet_date").val(meet_date);
				contobj.find("#meet_time").val(meet_time);
				contobj.find("#venue").val(venue);

                // Populate agenda items if they exist
                if(resp[1].agenda_record_details && resp[1].agenda_record_details.length > 0) {
                    resp[1].agenda_record_details.forEach(function(agenda) {
                        var tableBody = document.getElementById("session-rows");
                        var newRow = document.createElement("tr");
                        
                        // Time inputs cell
                        var timeCell = document.createElement("td");
                        timeCell.innerHTML = '<div class="form-row"><div class="col"><input type="text" name="time_from[]" id="time_from" value="' + agenda.time_from + '" placeholder="Start Time" class="form-control"></div><div class="col-auto d-flex align-items-center">To</div><div class="col"><input type="text" name="time_to[]" id="time_to" value="' + agenda.time_to + '" placeholder="End Time" class="form-control"></div></div><input type="hidden" name="agenda_id[]" value="' + agenda.id + '">';
                        newRow.appendChild(timeCell);
                        
                        // Topic input cell
                        var topicCell = document.createElement("td");
                        topicCell.innerHTML = '<textarea name="topic[]" id="topic" rows="3" placeholder="Enter Topic" class="form-control">' + agenda.topic + '</textarea>';
                        newRow.appendChild(topicCell);
                        
                        // Actions cell
                        var actionsCell = document.createElement("td");
                        actionsCell.innerHTML = '<button type="button" class="btn btn-danger delete-row" onclick="meetfuncs.deleteRow(this)"> - </button>';
                        newRow.appendChild(actionsCell);
                        
                        tableBody.appendChild(newRow);
                    });
                } else {
                    // Add one empty row if no agenda items exist
                    meetfuncs.addRow();
                }

				let header_text = 'Edit Event';
				
				contobj.find("#record-add-cancel-button").data('back-to',coming_from);
				contobj.find("#record-save-button>span:eq(0)").html('Save Changes');
				contobj.find("#panel-heading-text").text(header_text);
				contobj.find("#infoMsg").html('Edit Event');
				meetfuncs.setheaderBarText(header_text);

				meetfuncs.applyEditRestrictions(resp[1].edit_restricted_fields);
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
				location.hash=meetfuncs.prev_page_hash;
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
				$("#edit-record-button").removeClass('d-none').data('reci