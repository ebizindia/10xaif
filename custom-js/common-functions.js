const common_js_funcs={

	device:{},
	sponsor_ads: [],
	ad_display_interval: 10000, // mili sec
	last_ad_index:-1,
	cycleSponsorAds: function(i){
		if(common_js_funcs.sponsor_ads.length<=0)
			return false;
		let ad = common_js_funcs.sponsor_ads[i];
        $('div.sponsor_add .dsk_img').attr({src: ad.dsk_img, width: ad.dsk_img_w});
		$('div.sponsor_add .mob_img').attr({src: ad.mob_img, width: ad.mob_img_w});
		$('div.sponsor_add').parent('.sponsor_add_link').attr('href', ad.target_link);
		setInterval(
	        function() {
	            i++;
	            i %= common_js_funcs.sponsor_ads.length;
	            let ad = common_js_funcs.sponsor_ads[i];
	            $('div.sponsor_add .dsk_img').attr({src: ad.dsk_img, width: ad.dsk_img_w});
				$('div.sponsor_add .mob_img').attr({src: ad.mob_img, width: ad.mob_img_w});
				$('div.sponsor_add').parent('.sponsor_add_link').attr('href', ad.target_link);
	            
	        },
	        common_js_funcs.ad_display_interval
	    );


		
	},
	safeHTML: function(txt){
		return $('<div/>').text(txt).html().replace(/\r?\n/g, '<br/>');
	},
	sanitizeNumber: function(elem, dec_places){
		var amt = $(elem).val();
		//console.log(amt)

		//console.log(amt)
		$(elem).val(common_js_funcs.formatNumberDec(amt, dec_places));
	},
	formatNumberDec: function(amt, dec_places){
		amt = amt.toString().replace(/\,/, '').match(/^(\d*(\.\d*)?)/)
		if(!amt)
			amt = 0;
		else if(amt[1] == '')
			amt = 0;
		else{
			if(amt[1][0] == '.')
				amt[1][0] = '0' + amt[1][0];
			amt = amt[1];
		}
		return parseFloat(amt).toFixed(dec_places).toLocaleString('en-IN')
	},
	isNumber:function(evt) {
	    evt = (evt) ? evt : window.event;
		var charCode = (evt.which) ? evt.which : evt.keyCode;

    	if(charCode > 31 &&  charCode != 37 && charCode != 39  &&  (charCode < 48 || charCode > 57) ) {
        	return false;
    	}
    	return true;
	},

	getReadableFileSizeString(file_size_in_bytes) {
		if(file_size_in_bytes == 0)
			return '0 B';
		var idx = -1;
		var byte_units = ['KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
		do {
			file_size_in_bytes = file_size_in_bytes / 1024;
			++idx;
		} while(file_size_in_bytes > 1024);
		return Math.max(file_size_in_bytes, 0.1).toFixed(1) + ' ' + byte_units[idx];
	},
	/* to get ordinal number of any number
	*	getOrdinalNumber(2)
	*	--> 2nd;
	*/
	getOrdinalNumber:function(n){
		var s=["th","st","nd","rd"],
	    v=n%100;
	    return n+(s[(v-20)%10]||s[v]||s[0]);
 	},
	getQuarterMonthName:function(month){
		switch (month%3) {
			case 1:
				return "Jan/Apr/Jul/Oct";
				break;
			case 2:
				return "Feb/May/Aug/Nov";
				break;
			case 0:
				return "Mar/Jun/Sep/Dec";
				break;
		}
	},
	getHalfYearlyMonthName:function(month) {
		switch (month%6) {
			case 1:
				return "Jan/Jul";
				break;
			case 2:
				return "Feb/Aug";
				break;
			case 3:
				return "Mar/Sep";
				break;
			case 4:
				return "Apr/Oct";
				break;
			case 5:
				return "May/Nov";
				break;
			case 0:
				return "Jun/Dec";
				break;
		}
	},
	downloadAttachment:function(ev){
		var elem = $(ev.currentTarget);
		var target = new Date().getTime();
		var gen_iframe = $("<iframe class='d-none' id='iframe_dl_att_" + target + "' name='iframe_dl_att_" + target + "'/>");
		$('body').append(gen_iframe);
		elem.prop('target', 'iframe_dl_att_' + target);
		return true;
	},
	
	abort_controller: null,

	callServer:function(options){
		var opts={
				abortable:false,
				type:'get',
				dataType:'json',
				url:'',
				data: null,
				cache:'no-cache', //false,
				params:{},
				headers:{},
				successResponseHandler:null,
				successResponseHandlerParams:{},
				errorResponseHandler:null,
				errorResponseHandlerParams:{},
				abortHandler: null,
				abortHandlerParams: {},

			};

			opts = {...opts, ...options};
			opts.cache = 'no-cache'; // overriding any other value passed in the options object

			if(Object.entries(opts.params).length>0)
				opts.data = new URLSearchParams(opts.params);
			opts.headers['X-Requested-With'] = 'XMLHttpRequest';
			let fetch_options = {
				credentials: 'same-origin',
				method: opts.type,
				cache: opts.cache,
				mode: 'same-origin',
				body: opts.data,
				headers: opts.headers,
			}

			if(opts.abortable){
				this.abort_controller = new AbortController();
				if(typeof opts.abortHandler === 'function')
					this.abort_controller.signal.onabort = () => {
						this.abort_controller = null;
						opts.abortHandler(opts.abortHandlerParams);
					}
				fetch_options.signal = this.abort_controller.signal
			}

			fetch(
				opts.url??location.pathname,
				fetch_options

			).then(response => {
				this.abort_controller = null;
				if(response.ok && response.status === 200){
					if(opts.dataType === 'blob')
						return response.blob();
					else
						return response.json();
				}else{
					throw new Error('Failed in some way', { cause: response.status });
				}
			}).then(resp => {
				if(opts.successResponseHandler!=null)
					opts.successResponseHandler(resp,opts.successResponseHandlerParams);
			}).catch(err => {
				console.log(err);
				if(err.name === 'AbortError'){
					// console.log('Fetch aborted');
				}
				if(opts.errorResponseHandler!=null){
					opts.errorResponseHandler(err,opts.errorResponseHandlerParams);
				}else if(err.cause == 401){
					alert('Apparently your session has expired. Please try again after reloading the page.');
					location.href="login.php?referurl="+encodeURIComponent(location.href);
					
				}else if(err.cause == 423){
					alert('Apparently your session has expired or no company is loaded.');
					location.replace(location.pathname);
					
				}else{
					alert("An unexpected error was encountered. Please try again after reloading the page.");
				}
			});

	},


	isSessionActive: function(options){
		$("#common-processing-overlay").removeClass('d-none');
		common_js_funcs.callServer({
			cache:'no-cache',
			async:true,
			dataType:'json',
			type:'post',
			url:location.pathname,
			params:{act:'isSessionActive', sesscode: options.sesscode},
			successResponseHandler:function(resp, oth){
				$("#common-processing-overlay").addClass('d-none')
				if(resp.insession == true){
					if(options.callback)
						options.callback(oth);
				}else{
					alert('Your current session has expired. Please refresh the page and try again.');
				}
				
			},
			successResponseHandlerParams:options.callback_params});
	},

	openCompaniesMaster: function(e){
		location.href = 'company.php';
	},

	loadCompany: function(e){
		e.preventDefault();
		let loaded_comp_name = '';
		$("#common-processing-overlay").removeClass('d-none').css('zIndex','');
		let modal_box = $('#loadcompdialogglobal');
		modal_box.find('.comploaderror').text('').addClass('d-none');
		common_js_funcs.callServer({cache:'no-cache',async:true,dataType:'json',type:'post',url:location.pathname ,params:{act:'getCompanies'},
			successResponseHandler:function(resp, oth){
				$("#common-processing-overlay").addClass('d-none');
				let loaded_comp = resp[0].filter(comp => comp.id==resp[1]);
				loaded_comp_name = loaded_comp[0]?.name ?? '';
				if(loaded_comp_name!=''){
					modal_box.find('#loadcompdialogmsg1').find('span').text(loaded_comp_name).end().removeClass('d-none').end().find('#loadcompdialogmsg2').addClass('d-none').end().find('#unloadcompbtn').find('span').text(loaded_comp_name).end().removeClass('d-none').end();
				}else{
					modal_box.find('#loadcompdialogmsg2').removeClass('d-none').end().find('#loadcompdialogmsg1').addClass('d-none').end().find('#unloadcompbtn').addClass('d-none').end();
				}
				let comp_drop_down = modal_box.find('#loadcompany');
				// comp_drop_down.empty();
				comp_drop_down.find('option:gt(0)').remove();
				resp[0].filter(comp => comp.id!=resp[1]).forEach(function(dtls, idx){
					const option = document.createElement('option');
					option.value = dtls.id;
					option.text = `${dtls.name} (${dtls.period_start_dmy} : ${dtls.period_end_dmy} )`;
					option.setAttribute('data-name', dtls.name);
					option.setAttribute('data-period_start', dtls.period_start_dmy);
					option.setAttribute('data-period_end', dtls.period_end_dmy);
					comp_drop_down.append(option);
					// console.log({dtls, idx, loaded_comp_name});
				});
				let dg = modal_box.modal(
				{
					backdrop: 'static',
					keyboard: false,
				});
				dg.on('shown.bs.modal', function(e){
					modal_box.find('#loadcompbtn').on(common_js_funcs.click_event, event => {
						e.stopPropagation();
						modal_box.find('.comploaderror').text('').addClass('d-none');
						const dd = modal_box.find('#loadcompany')[0];
						const dd_selindex = dd.selectedIndex;
						const dd_sel_opt = dd.options[dd_selindex];
						const comp_id = dd_sel_opt.value; //modal_box.find('#loadcompany').val();
						if(comp_id!=''){
							const sel_comp = {id: dd_sel_opt.value, name: dd_sel_opt.getAttribute('data-name'), prdst: dd_sel_opt.getAttribute('data-period_start'), prdend: dd_sel_opt.getAttribute('data-period_end')} 
							$("#common-processing-overlay").removeClass('d-none').css('zIndex','2000');
							common_js_funcs.callServer({cache:'no-cache',async:true,dataType:'json',type:'post',url:location.pathname,params:{act:'loadCompany', rec_id:comp_id},
								successResponseHandler:function(resp, oth){
									$("#common-processing-overlay").addClass('d-none').css('zIndex','');
									common_js_funcs.handleLoadCompanyResp(resp, oth);
								},
								successResponseHandlerParams:{modal_box:modal_box, sel_comp: sel_comp}});
						}else{
							modal_box.find('.comploaderror').text('Please select a company to load.').removeClass('d-none');
						}
					});

					if(loaded_comp_name!=''){
						// For the unload company button
						modal_box.find('#unloadcompbtn').on(common_js_funcs.click_event, event => {
							e.stopPropagation();
							modal_box.find('.comploaderror').text('').addClass('d-none');
							$("#common-processing-overlay").removeClass('d-none').css('zIndex','2000');
							common_js_funcs.callServer({cache:'no-cache',async:true,dataType:'json',type:'post',url:location.pathname,params:{act:'unloadCompany'},
								successResponseHandler:function(resp, oth){
									$("#common-processing-overlay").addClass('d-none').css('zIndex','');
									common_js_funcs.handleUnloadCompanyResp(resp, oth);
								},
								successResponseHandlerParams:{modal_box:modal_box}});
						});
					}
				});
				dg.on('hidden.bs.modal', function(e){
					dg.off('shown.bs.modal');
					dg.off('hidden.bs.modal');
					modal_box.find('#loadcompbtn').off(common_js_funcs.click_event);
					if(loaded_comp_name!='')
						modal_box.find('#unloadcompbtn').off(common_js_funcs.click_event);
					dg.modal('dispose');
					delete dg, modal_box;
				});
			},
			successResponseHandlerParams:{}});

	},


	handleLoadCompanyResp:function(resp, oth){
		if(resp.error_code==0){
			if(location.pathname.includes('company.php')){
				oth.modal_box.modal('hide');
				const comp_disp_elem = document.getElementById('loaded_comp_disp');
				const containers = comp_disp_elem.getElementsByTagName('div');
				containers[0].innerText = oth.sel_comp.name;
				containers[1].innerText = `(${oth.sel_comp.prdst} to ${oth.sel_comp.prdend})`;
				comp_disp_elem.classList.remove('d-none');
				document.getElementById('import_masters_opt1').classList.remove('d-none');
				document.querySelector('.import-masters-from-comp').classList.remove('d-none');
			}else{
				location.replace(location.pathname);
			}
		}else{

			// alert(resp.msg);
			oth.modal_box.find('.comploaderror').text(resp.msg).removeClass('d-none');
		}
	},

	handleUnloadCompanyResp:function(resp, oth){
		if(resp.error_code==0){
			if(location.pathname.includes('company.php')){
				oth.modal_box.modal('hide');
				const comp_disp_elem = document.getElementById('loaded_comp_disp');
				const containers = comp_disp_elem.getElementsByTagName('div');
				containers[0].innerText = '';
				containers[1].innerText = '';
				comp_disp_elem.classList.add('d-none');
				document.getElementById('import_masters_opt1').classList.add('d-none');
				document.querySelector('.import-masters-from-comp').classList.add('d-none');
			}else{

				location.replace(location.pathname);
			}

		}else{

			// alert(resp.msg);
			oth.modal_box.find('.comploaderror').text(resp.msg).removeClass('d-none');
		}
	},



	importWhichMasterTickUntick: function(e){
		e.stopPropagation();
		const elem = $(e.target);
		const master = elem.closest('tr').data('master');
		const checked = elem.prop('checked');
		if(checked==false)
			$('#import_which_all').prop('checked',false);
		if($('#import_master_emp .import_which_master').is(':checked')){
			$('#import_master_inccat, #import_master_mt, #import_master_stg').find('.import_which_master').prop('checked',true).css('pointerEvents','none').addClass('disabled').end().find('.import_active_only').prop('checked',false).css('pointerEvents','none').addClass('disabled').end();
		}else if($('#import_master_geninc .import_which_master').is(':checked')){
			$('#import_master_inccat, #import_master_mt').find('.import_which_master').prop('checked',true).css('pointerEvents','none').addClass('disabled').end().find('.import_active_only').prop('checked',false).css('pointerEvents','none').addClass('disabled').end();
			$('#import_master_stg').find('.import_which_master, .import_active_only').css('pointerEvents','').removeClass('disabled');
		}else{
			$('#import_master_inccat, #import_master_mt, #import_master_stg').find('.import_which_master, .import_active_only').css('pointerEvents','').removeClass('disabled');
		}

	},


	importMasterActionChange: function(e){
		const elem = $(e.target);
		const master = elem.closest('tr').data('master');
		const val = $(e.target).val();
		if(master=='emp'){
			if(val=='MERGE'){
				$('#import_master_inccat, #import_master_mt, #import_master_stg').find('.import_action').val('MERGE').css('pointerEvents','none').addClass('disabled').end();
			}else{
				const geninc_action = $('#import_master_geninc').find('.import_action').val();
				if(geninc_action=='REPLACE'){
					$('#import_master_inccat, #import_master_mt, #import_master_stg').find('.import_action').css('pointerEvents','').removeClass('disabled').end();
				}else{
					$('#import_master_stg').find('.import_action').css('pointerEvents','').removeClass('disabled').end();
				}
			}	
		}else if(master=='geninc'){
			const emp_action = $('#import_master_emp').find('.import_action').val();
			if(emp_action=='REPLACE'){
				if(val=='MERGE'){
					$('#import_master_inccat, #import_master_mt').find('.import_action').val('MERGE').css('pointerEvents','none').addClass('disabled').end();
				}else{
					$('#import_master_inccat, #import_master_mt').find('.import_action').css('pointerEvents','').removeClass('disabled').end();
				}	
			}
		}
		
	},


	importMasters: function(e){
		e.preventDefault();
		e.stopPropagation();
		if(e.target.id=='import_masters_opt1')
			$('.nav-link.dropdown-toggle').trigger('click');
		if($('div.modal').hasClass('show'))
			return false;
		let loaded_comp_name = '';
		$("#common-processing-overlay").removeClass('d-none').css('zIndex','');
		let modal_box = $('#importmastersdialogglobal');
		modal_box.find('.comploaderror').text('').addClass('d-none');
		common_js_funcs.callServer({cache:'no-cache',async:true,dataType:'json',type:'post',url:location.pathname ,params:{act:'getCompaniesForImport'},
			successResponseHandler:function(resp, oth){
				$("#common-processing-overlay").addClass('d-none');
				let comp_drop_down = modal_box.find('#importfromcomp');
				comp_drop_down.find('option:gt(0)').remove();

				if(resp?.ec && resp.ec==1){
					alert(`Importing masters in the current company is not possible as it already has some transaction data.\nSo, in order to import the masters either you have to delete all the transaction data from the company or you may delete the company itself and create a new company before importing the masters.`);
				}else if(resp?.ec && resp.ec==2){
					alert(`Importing masters is not possible as the system failed to determine if the current company already has some transactions.`);
				}else{

					let loaded_comp = resp[0].filter(comp => comp.id==resp[1]);
					loaded_comp_name = loaded_comp[0]?.name ?? '';
					if(loaded_comp_name!=''){
						modal_box.find('#loadcompdialogmsg1').find('span').text(loaded_comp_name).end().removeClass('d-none').end().find('#loadcompdialogmsg2').addClass('d-none').end().find('#unloadcompbtn').find('span').text(loaded_comp_name).end().removeClass('d-none').end();
					}
					resp[0].filter(comp => comp.id!=resp[1]).forEach(function(dtls, idx){
						const option = document.createElement('option');
						option.value = dtls.id;
						option.text = `${dtls.name} (${dtls.period_start_dmy} : ${dtls.period_end_dmy} )`;
						option.setAttribute('data-name', dtls.name);
						option.setAttribute('data-period_start', dtls.period_start_dmy);
						option.setAttribute('data-period_end', dtls.period_end_dmy);
						comp_drop_down.append(option);
						// console.log({dtls, idx, loaded_comp_name});
					});
					let dg = modal_box.modal(
					{
						backdrop: 'static',
						keyboard: false,
					});
					dg.on('shown.bs.modal', function(e){
						modal_box.find('#importmastersbtn').on(common_js_funcs.click_event, event => {
							e.stopPropagation();
							modal_box.find('.comploaderror').text('').addClass('d-none');
							const dd = modal_box.find('#importfromcomp')[0];
							const dd_selindex = dd.selectedIndex;
							const dd_sel_opt = dd.options[dd_selindex];
							const comp_id = dd_sel_opt.value; 
							if(comp_id!=''){
								const p = modal_box.find('#pswd_for_import').val();
								if(p!=''){

									const sel_comp = {id: dd_sel_opt.value, name: dd_sel_opt.getAttribute('data-name'), prdst: dd_sel_opt.getAttribute('data-period_start'), prdend: dd_sel_opt.getAttribute('data-period_end')} 
									$("#common-processing-overlay").removeClass('d-none').css('zIndex','2000');

									const data = new FormData(document.getElementById('importmastersform'));
									const data_json = Object.fromEntries(data.entries());
									let params = {act:'importMaster', ...data_json};

									common_js_funcs.callServer({cache:'no-cache',async:true,dataType:'json',type:'post',url:location.pathname,params:params,
										successResponseHandler:function(resp, oth){
											$("#common-processing-overlay").addClass('d-none').css('zIndex','');
											common_js_funcs.handleImportMasterResp(resp, oth);
										},
										successResponseHandlerParams:{modal_box:modal_box, sel_comp: sel_comp}});
								}else{
									modal_box.find('.comploaderror').text('Please enter your password.').removeClass('d-none');
									modal_box.find('#pswd_for_import')[0].focus();
								}
							}else{
								modal_box.find('.comploaderror').text('Please select a company to import from.').removeClass('d-none');
							}
						});

					});
					dg.on('hidden.bs.modal', function(e){
						dg.off('shown.bs.modal');
						dg.off('hidden.bs.modal');
						modal_box.find('#importmastersbtn').off(common_js_funcs.click_event);
						dg.modal('dispose');
						delete dg, modal_box;
					});
				}
			},
			successResponseHandlerParams:{}});

	},


	handleImportMasterResp:function(resp, oth){
		if(resp.error_code==0){
			
			location.replace(location.pathname);
			
		}else{
			// alert(resp.msg);
			oth.modal_box.find('.comploaderror').text(resp.msg).removeClass('d-none');
		}
	},



	client_settings:{
		precision: 3,
		number_system: 'INDIAN'
	},



	toggleMenuMode:function(){
		//var paramstr="mode=toggleMenuMode";
		var options={cache:false,async:false,type:'post',dataType:'json',url:'?mode=toggleMenuMode',

			successResponseHandler:function(resp,otherparams){
				if(resp.error_code==0){
					location.reload();
				}else{
					alert("Sorry, the menu type could not be switched.");

				}

			},
			successResponseHandlerParams:{}
		};
		common_js_funcs.callServer(options);

	},


	array_unique:function(arr){
		var newarr=[];
		arr=arr || [];
		var arr_length=arr.length;
		for(var i=0; i<arr_length; i++){
			if(newarr.indexOf(arr[i])==-1)
				newarr.push(arr[i]);

		}

		newarr.sort();
		return newarr;
	},


	pageGuideClosed: function(elem){
		var data = $(elem).data(); //console.log(data)
		if(!data.module)
			return;
		common_js_funcs.callServer({
			type:'GET',
			url:'myprofile.php?mode=update_user_settings',
			data: 'module='+(data.module || ''),
			cache:false,
			successResponseHandler: function(resp, extra){},
			successResponseHandlerParams:{},
			errorResponseHandler: function(xhr, status, error, params){},
			errorResponseHandlerParams:{}
		});
	},

	validateEmail: function(value){
		var email_pattern=new RegExp("^\\w+([.']?-*\\w+)*@\\w+([.-]?\\w+)*(\\.\\w{2,4})+$","i");
		return email_pattern.test(value);
	},

	validatePhone: function(value){
		var phone_pattern=new RegExp("^[+() .0-9-]+$", "i");
		return phone_pattern.test(value);
	},

	dateDiff:function(date1, date2){
		//returns the date difference between date1 and date2 as date1 subtracted by date2
		//both dates are expected to be in ISO 8601 format like 2014-06-21T15:12:43 format
		date1 = new Date(date1);
		date2 = new Date(date2);
		return Math.ceil((date1-date2)/86400000);

	},

	compareDate: function(date1, date2, date_format){
		//separator = separator || '-';
		date_format = date_format || 'd-m-y';
		date_parts = [];
		switch(date_format.toLowerCase()){
			case 'd-m-y':
				date_parts_1 = date1.split('-');
				date_parts_2 = date2.split('-');
				ymd_1 = date_parts_1[2]+date_parts_1[1]+date_parts_1[0];
				ymd_2 = date_parts_2[2]+date_parts_2[1]+date_parts_2[0];
				break;
			case 'm-d-y':
				date_parts_1 = date1.split('-');
				date_parts_2 = date2.split('-');
				ymd_1 = date_parts_1[2]+date_parts_1[0]+date_parts_1[1];
				ymd_2 = date_parts_2[2]+date_parts_2[0]+date_parts_2[1];
				break;
		}
		//console.log(ymd_1+' '+ymd_2)
		if(ymd_1<ymd_2)
			return -1;
		else if(ymd_1>ymd_2)
			return 1;
		return 0;
	},

	toUserTime: function(elem, format){
		format = format || 'dd-mm-yyyy HH:MM';
		$(elem).each(function(){
			var utc = $(this).data('utc');
			var dt = new Date(utc*1000);
			$(this).html(utc == '' ? '&nbsp;' : dt.format(format));
		})
	},

	myCompareAlphaAsc: function(a, b) {
	    var str_a = a.innerText || a.textContent;
	    var str_b = b.innerText || b.textContent;
	    return str_a.localeCompare(str_b);
	},

	myCompareAlphaDesc: function(a, b) {
	    var str_a = a.innerText || a.textContent;
	    var str_b = b.innerText || b.textContent;
	    return str_b.localeCompare(str_a);
	},

	myCompareDefault: function(a, b) {
	    return parseInt($(a).data('order'), 10) > parseInt($(b).data('order'), 10);
	},


	/*handleSessionExpiry:function(resp){
		var self=this;
		if(typeof resp=='string'){
			resp=resp.split(':');
		}
		//may not need the hasOwnProperty
		if(resp.hasOwnProperty('SESS_EXPIRED') && resp.SESS_EXPIRED=='1'){
			self.notifyUserOfSessionExpiry();
			return false;
		}
		return true;
	},*/

	notifyUserOfSessionExpiry:function(showAlert){
		if(location.pathname == '/' || location.pathname == '/index.php')
			location.href="login.php?";
		else
			location.href="login.php?referurl="+encodeURIComponent(location.href);

	},

	scrollTo:function(elem_jquery_obj,options){

		var animation_options={duration:'slow',complete:null};

		$.extend(true,animation_options,options);

		$('html,body').animate({scrollTop:elem_jquery_obj.offset().top-50},animation_options);
	},

	changeLocationWithDataProperty:function(e){
		e.preventDefault();
		e.stopPropagation();
		var url=$(e.currentTarget).data('url');
		var target=$(e.currentTarget).data('target');
		var hash=$(e.currentTarget).data('hash');

		if(typeof url != 'undefined' && url!=''){
			if( (target || '')=='_blank' ){
				window.open(url);
			}else{
				window.location.href=url;
			}
		}else if(typeof hash != 'undefined' && hash!=''){
			window.location.hash=hash;
		}else{
			let a_tag = $(e.currentTarget).find('a:eq(0)');
			if(a_tag.length>0){
				if( a_tag.attr('target')=='_blank' ){
					window.open(a_tag.attr('href'));
				}else{
					window.location.href = a_tag.attr('href');
				}
			}
		}

	},

	formatDate: function(date, curr_format, new_format){
		var ymd='';
		switch(curr_format){
			case 'yyyy-mm-dd':
				ymd = date;
				break;
			case 'dd-mm-yyyy':
				date_parts = date.split('-');
				ymd = date_parts[2]+'-'+date_parts[1]+'-'+date_parts[0];
				break;
			case 'mm-dd-yyyy':
				date_parts = date.split('-');
				ymd = date_parts[2]+'-'+date_parts[0]+'-'+date_parts[1];
				break;
			case 'mmm-dd-yyyy':
				date_parts = date.split('-');
				ymd = date_parts[2]+'-'+this.__months[date_parts[0]]+'-'+date_parts[1];
				break;
			case 'dd-mmm-yyyy':
				date_parts = date.split('-');
				ymd = date_parts[2]+'-'+this.__months[date_parts[1]]+'-'+date_parts[0];
				break;
		}

		var date = new Date(ymd);
		new_format  =new_format || 'yyyy-mm-dd';
		return date.format(new_format);
	},

	textLimiter: function(elem, char_elem, options){
		options = options || {}
		var set=160;
		if(options.max_chars)
			set = options.max_chars;
		$(elem).val('');
		$(char_elem).text(set+" characters left");
		$(elem).on('input', function(e) {
			var tval = $(elem).val();
			tlength = tval.length;
			var len = this.value.length;
			if (len >= set)
				this.value = this.value.substring(0, set);
			var remainval=parseInt(set - len);
			if(remainval<=0)
				remain = 0+" characters left";
			else if(remainval<=1)
				remain = remainval+" character left";
			else
				remain = remainval+" characters left";

			$(char_elem).text(remain);
			if (remain <= 0 && e.which !== 0 && e.charCode !== 0) {
				$(elem).val((tval).substring(0, len - 1))
			}
		});
	},

	formatNumber: function(value, precision, system_to_use){
		return value;
		
	},

	numericOnly: function(elem, opt){
		var options = {};
		opt= opt || {}
		options = $.extend(options, opt);
		//console.log(options)
		$(elem).on('keyup paste', function(){
			var prev = $(this).data('prev-val');
			if($(this).val().trim()==''){
				$(this).data('prev-val', $(this).val().trim());
				if(prev!=$(this).val().trim() && opt.callback)
					opt.callback(this);

			}else if($.isNumeric($(this).val())) {
				$(this).val($(this).val().trim().replace(/e/i, ''));
				if(options.integer_only)
					$(this).val($(this).val().trim().replace(/\./i, ''));
				if(options.positive_only)
					$(this).val($(this).val().trim().replace(/\-/i, ''));
				if(prev!=$(this).val() && opt.callback)
					opt.callback(this);
				$(this).data('prev-val', $(this).val().trim());
				
			}else{
				$(this).val($(this).data('prev-val'));
				
			}

		});
	},
	sidenavShowHide:function(secid){
		$("#"+secid).slideToggle('fast', function(){
			$(this).siblings('a').toggleClass('active-nav');

		});

		return false;
	},


	getMonthNameAndYear: function(dt_Ymd){
		if(dt_Ymd=='')
			return '';
		const months = [
			'January', 'Fenruary', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December' 
		];
		let mnth = dt_Ymd.getMonth();
		let yr = dt_Ymd.getFullYear()
		return months[parseInt(mnth)]+' '+yr;
	},

	__me : '',

	__date_display_formats:{},

	__months: {'Jan':'01', 'Feb':'02', 'Mar':'03', 'Apr':'04', 'May':'05', 'Jun':'06', 'Jul':'07', 'Aug':'08',
	'Sep':'09', 'Oct':'10', 'Nov':'11', 'Dec':'12'},

	__dtp_fa_icons : {
		time: 'fa fa-clock-o',
		date: 'fa fa-calendar',
		up: 'fa fa-chevron-up',
		down: 'fa fa-chevron-down',
		previous: 'fa fa-chevron-left',
		next: 'fa fa-chevron-right',
		today: 'fa fa-screenshot',
		clear: 'fa fa-trash',
		close: 'fa fa-times'
	},

	cleanDatepicker: function() {
	   var old_fn = $.datepicker._updateDatepicker;

	   $.datepicker._updateDatepicker = function(inst) {
	      old_fn.call(this, inst);

	      var buttonPane = $(this).datepicker("widget").find(".ui-datepicker-buttonpane");

	      $("<button type='button' class='ui-datepicker-clean ui-state-default ui-priority-primary ui-corner-all'>Clear</button>").appendTo(buttonPane).click(function(ev) {
	          $.datepicker._clearDate(inst.input);
	      }) ;
	   }
	},

	tableToExcel:  (function() {
	  let uri = 'data:application/vnd.ms-excel;base64,'
	    , template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--><meta http-equiv="content-type" content="text/plain; charset=UTF-8"/></head><body><table>{table}</table></body></html>'
	    , base64 = function(s) { return window.btoa(unescape(encodeURIComponent(s))) }
	    , format = function(s, c) { return s.replace(/{(\w+)}/g, function(m, p) { return c[p]; }) }
	  return function(table, name, elem ) {
	    if (!table.nodeType) table = document.getElementById(table)
	    var ctx = {worksheet: name || 'Worksheet', table: table.innerHTML}
	    elem.href = uri + base64(format(template, ctx));
	  }
	})()

 }


common_js_funcs.device.isIOS =  (/(iPod|iPhone|iPad)/.test(navigator.userAgent)
 && /AppleWebKit/.test(navigator.userAgent));
common_js_funcs.device.isAndroid = navigator.userAgent.toLowerCase().indexOf("android") > -1;

//Doc @ http://code.iamkate.com/javascript/using-cookies/
var Cookies={set:function(b,c,a){b=[encodeURIComponent(b)+"="+encodeURIComponent(c)];a&&("expiry"in a&&("number"==typeof a.expiry&&(a.expiry=new Date(1E3*a.expiry+ +new Date)),b.push("expires="+a.expiry.toGMTString())),"domain"in a&&b.push("domain="+a.domain),"path"in a&&b.push("path="+a.path),"secure"in a&&a.secure&&b.push("secure"));document.cookie=b.join("; ")},get:function(b,c){for(var a=[],e=document.cookie.split(/; */),d=0;d<e.length;d++){var f=e[d].split("=");f[0]==encodeURIComponent(b)&&a.push(decodeURIComponent(f[1].replace(/\+/g,"%20")))}return c?a:a[0]},clear:function(b,c){c||(c={});c.expiry=-86400;this.set(b,"",c)}};
