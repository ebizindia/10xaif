<?php
namespace scriptProviderFuncs;
function getCss($page){
	$css_files=array();


	switch(strtolower($page)){
		case 'dashboard': break;
		case 'login': break;
		case 'members': 
			$css_files[]=CONST_THEMES_CSS_PATH . 'tokenize2.min.css'; 
			break;
		case 'discount-offers': 
			$css_files[]=CONST_THEMES_CUSTOM_CSS_PATH . 'grid-block.css'; 
			$css_files[]=CONST_THEMES_CUSTOM_CSS_PATH . 'category-details.css'; 
			break;
		case 'feedbacktoadmin': break;
		case 'scan-tkt': 
			$css_files[]=CONST_THEMES_CUSTOM_CSS_PATH . 'mobile.'.RESOURCE_VERSION.'.css'; 
			$css_files[]=CONST_THEMES_CUSTOM_CSS_PATH . 'qrscan.'.RESOURCE_VERSION.'.css'; 
			break;
		case 'settings': 
			$css_files[]=CONST_THEMES_CUSTOM_CSS_PATH . 'settings.'.RESOURCE_VERSION.'.css'; 
			break;
		
		break;
	}

	// $css_files[]=CONST_THEMES_CSS_PATH.'bootstrap-datetimepicker.min.css';
	return $css_files;
}

function getJavascripts($page){
	$js_files=array('BSH'=>array(),'BSB'=>array());
	switch(strtolower($page)){
		case 'dashboard': 
			break;
		case 'login': 	
			$js_files['BSB'][]=CONST_THEMES_CUSTOM_JAVASCRIPT_PATH . "login.".RESOURCE_VERSION.".js";
			break;
		case 'members':
			$js_files['BSB'][]=CONST_THEMES_CUSTOM_JAVASCRIPT_PATH . 'tokenize2-customized.'.RESOURCE_VERSION.'.js'; 
			$js_files['BSB'][]=CONST_THEMES_CUSTOM_JAVASCRIPT_PATH . 'users.'.RESOURCE_VERSION.'.js';
			break;
		case 'mem-regs':
			$js_files['BSB'][]=CONST_THEMES_CUSTOM_JAVASCRIPT_PATH . 'mem-regs.'.RESOURCE_VERSION.'.js';
			break;
		case 'feedbacktoadmin':
			$js_files['BSB'][]=CONST_THEMES_CUSTOM_JAVASCRIPT_PATH . 'feedback.'.RESOURCE_VERSION.'.js';
			break;
		case 'noticetomem':
			$js_files['BSB'][]=CONST_THEMES_JAVASCRIPT_PATH . 'ckeditor5/ckeditor.'.RESOURCE_VERSION.'.js';
			$js_files['BSB'][]=CONST_THEMES_CUSTOM_JAVASCRIPT_PATH . 'notice.'.RESOURCE_VERSION.'.js';
			break;
		case 'ad-banners':
			$js_files['BSB'][]=CONST_THEMES_CUSTOM_JAVASCRIPT_PATH . 'ad-banners.'.RESOURCE_VERSION.'.js';
			break;	
		case 'events':
			$js_files['BSB'][]=CONST_THEMES_CUSTOM_JAVASCRIPT_PATH . 'events.'.RESOURCE_VERSION.'.js';
			break;	
		case 'manage-offers':
			$js_files['BSB'][]=CONST_THEMES_CUSTOM_JAVASCRIPT_PATH . 'manage-offers.'.RESOURCE_VERSION.'.js';
			break;            
		case 'discount-offers':
			$js_files['BSB'][]=CONST_THEMES_CUSTOM_JAVASCRIPT_PATH . 'discount-offers.'.RESOURCE_VERSION.'.js';
			break;            
		case 'event-registrations':
			$js_files['BSB'][]=CONST_THEMES_CUSTOM_JAVASCRIPT_PATH . 'event-registrations.'.RESOURCE_VERSION.'.js';
			break;	
		case 'event-bookings':
			$js_files['BSB'][]=CONST_THEMES_CUSTOM_JAVASCRIPT_PATH . 'event-bookings.'.RESOURCE_VERSION.'.js';
			break;	
		case 'sectors':
			$js_files['BSB'][]=CONST_THEMES_CUSTOM_JAVASCRIPT_PATH . 'sectors.'.RESOURCE_VERSION.'.js';
			break;
		case 'scan-tkt':
			$js_files['BSB'][]=CONST_THEMES_CUSTOM_JAVASCRIPT_PATH . 'scan-etickets.'.RESOURCE_VERSION.'.js';
			$js_files['BSB'][]=CONST_THEMES_CUSTOM_JAVASCRIPT_PATH . 'html5-qrcode.2.3.8.min.'.RESOURCE_VERSION.'.js';
			$js_files['BSB'][]=CONST_THEMES_CUSTOM_JAVASCRIPT_PATH . 'QRScan.'.RESOURCE_VERSION.'.js';
			break;
		case 'meeting':
			$js_files['BSB'][]=CONST_THEMES_CUSTOM_JAVASCRIPT_PATH . 'meeting.'.RESOURCE_VERSION.'.js';
			break;
		case 'meetings':
			$js_files['BSB'][]=CONST_THEMES_CUSTOM_JAVASCRIPT_PATH . 'meetings.'.RESOURCE_VERSION.'.js';
			break;
		case 'roster':
			$js_files['BSB'][]=CONST_THEMES_CUSTOM_JAVASCRIPT_PATH . 'roster.'.RESOURCE_VERSION.'.js';
			break;
		case 'goals':
			$js_files['BSB'][]=CONST_THEMES_CUSTOM_JAVASCRIPT_PATH . 'goals.'.RESOURCE_VERSION.'.js';
			break;	
		case 'all-goals':
			$js_files['BSB'][]=CONST_THEMES_CUSTOM_JAVASCRIPT_PATH . 'all-goals.'.RESOURCE_VERSION.'.js';
			break;		
		case 'constitution':
			$js_files['BSB'][]=CONST_THEMES_CUSTOM_JAVASCRIPT_PATH . 'constitution.'.RESOURCE_VERSION.'.js';
			break;
		case 'settings':
			$js_files['BSB'][]=CONST_THEMES_CUSTOM_JAVASCRIPT_PATH . 'settings.'.RESOURCE_VERSION.'.js';
			break;		
			
			
    }
	$js_files['BSB'][]=CONST_THEMES_CUSTOM_JAVASCRIPT_PATH . "common-functions.".RESOURCE_VERSION.".js";
	return $js_files;
}


function getDomReadyJsCode($page, $dom_ready_data = []){
	$autocomplete_wait_time = AUTOCOMPLETE_WAIT_TIME;
	$js_code="";

	switch(strtolower($page)){
		case 'common':
			$cookie_path = ($dom_ready_data['cookie_path']=='')?'/':$dom_ready_data['cookie_path'];
			$js_code = <<<EOF
				var options = {
					user_settings: {$dom_ready_data['user_settings']},
					allowed_menu_perms: {$dom_ready_data['allowed_menu_perms']},
					user_types: {$dom_ready_data['user_types']},
					click_event : $.fn.tap ? "tap" : "click",
					user_uploaded_files_url_path : "{$dom_ready_data['user_uploaded_files_url_path']}",
					noimage_file : "{$dom_ready_data['noimage_file']}",
					other_data:{$dom_ready_data['other_data']},
					cookie_path:"{$cookie_path}",
					sponsor_ads:{$dom_ready_data['sponsor_ads']},
					ad_display_interval:{$dom_ready_data['ad_display_interval']},
					is_admin:{$dom_ready_data['is_admin']},
				}
				$.extend(true, common_js_funcs, options);
				$('.close-guide').on(common_js_funcs.click_event, function (){
					common_js_funcs.pageGuideClosed($(this));
				});

				$('.main-content').on('change', 'input.error-field', e=>{
					$(e.target).removeClass('error-field');
				});
								

				$('.main-content').on(common_js_funcs.click_event,'input[type=checkbox]',function(ev){
					this.blur();
				});

				$('.main-content').on('focus','tr.delete_rec>td:not(:first) input,tr.delete_rec>td:not(:first) select',function(ev){
					this.blur();
				});

				$('.main-content').on('focus','tr.non-editable-rec>td input,tr.non-editable-rec>td select',function(ev){
					this.blur();
				});

				$('.main-content').on(common_js_funcs.click_event, '.nopropagate', e=>{
					e.stopPropagation();
				});

				$('.main-content').on(common_js_funcs.click_event + ' input keydown keyup keypress paste', 'input.non-editable, select.non-editable, textarea.non-editable', e=>{
					$(e.currentTarget).blur();
					e.preventDefault();
					e.stopPropagation();
				});

				$('body').on('keypress', '.noEnterSubmit', e=>{
					if(e.which==13)
						e.preventDefault();
				});

				$('.clear-multiselect').on('click', function(e){
					e.preventDefault();
					e.stopPropagation();
					const mulselid = $(this).data('listid');
					const mulsel = $('#'+mulselid);
					mulsel.multiselect('deselectAll');
					mulsel.multiselect('rebuild');
				})

				common_js_funcs.cleanDatepicker();
EOF;
				if($dom_ready_data['show_sponsor_ad']==true){
					$js_code .= <<<EOF
					common_js_funcs.cycleSponsorAds(0);
EOF;
				}
										
				$js_code .= <<<EOF

				// scroll the active menu into view 
				document.querySelector('.sidebar-nav li.active')?.scrollIntoView(true);

				if($(window).hashchange){
					// In mobile view at times, when the screen changes but the page doesn't load and if the menu is open it remains so. The below code will turn off the menu for such cases. 
					// This is an additional common handler being added to the hashchange event, as individual modules have their own hashchange handler function.
					$(window).hashchange(()=>{
						if($('#menu-toggle').is(':visible')){
							$("#wrapper").removeClass("toggled");
						}
					});
				}
				
EOF;
			break;
		case 'dashboard': 	
			$js_code = <<<EOF
			$('.main-content').on(common_js_funcs.click_event,'td.clickable-cell',common_js_funcs.changeLocationWithDataProperty);
EOF;
		break;
		case 'login': 	break;
		case 'feedbacktoadmin':
			$js_code = <<<EOF
			// $('#add_form_field_msgbody').on('keyup' , {self:feedbackfuncs} , feedbackfuncs.remCount).focus();
			feedbackfuncs.char_limit = {$dom_ready_data['feedbacktoadmin']['feedback_max_chars']};
			// $('#remcount').html(feedbackfuncs.char_limit);
EOF;
			break;
		case 'noticetomem':
			$js_code = <<<EOF
			$('#remove_attachment').on(common_js_funcs.click_event, noticefuncs.removeAttachment);
			$('#add_form_field_sendwamsg').on('change', noticefuncs.toggleWhatsApp);
			$('.togglegrpsel').on(common_js_funcs.click_event, noticefuncs.selDeselAllGrps);

			ClassicEditor
		    .create( document.querySelector( '#add_form_field_msgbody' ), {
		    	toolbar: {
				    removeItems: [ 'link', 'blockQuote', 'codeBlock', 'code', 'uploadImage', 'insertImage', 'insertTable', 'mediaEmbed'],
				    shouldNotGroupWhenFull: true
				}
		    } )
		    .then(new_editor => {
		    	noticefuncs.email_msg_editor = new_editor;
		    })
		    .catch( error => {
		        console.error( error );
		    } );
EOF;
			break;
		case 'members':
			$datepicker_icon = CONST_THEMES_CUSTOM_IMAGES_PATH.'datepicker.gif';
			$js_code=<<<EOF


			$('.main-content').on(common_js_funcs.click_event,'.record-list-refresh-button',{self:usersfuncs},usersfuncs.refreshList);

			$('.main-content').on(common_js_funcs.click_event,'.clickable-cell',{self:usersfuncs},common_js_funcs.changeLocationWithDataProperty);
			$('.main-content').on(common_js_funcs.click_event,'.page-link',{self:usersfuncs},usersfuncs.changePage);
			$('.main-content').on(common_js_funcs.click_event,'.toggle-search',{self:usersfuncs},usersfuncs.toggleSearch);
			$('.main-content').on(common_js_funcs.click_event,'.toggle-sort',{self:usersfuncs},usersfuncs.toggleSortPanel);
			$('.main-content').on(common_js_funcs.click_event,'.record-delete-button',{self:usersfuncs},usersfuncs.deleteUser);

			$('#users-list>thead>tr>th.sortable').bind(common_js_funcs.click_event,{self:usersfuncs},usersfuncs.sortTable);

			$('#user_detail_view_container .email-icon-form-input, #user_detail_view_container .wa-icon-form-input, #user_detail_view_container .tel-icon-form-input, #user_detail_add_edit_container .email-icon-form-input, #user_detail_add_edit_container .wa-icon-form-input, #user_detail_add_edit_container .tel-icon-form-input').on(common_js_funcs.click_event, common_js_funcs.changeLocationWithDataProperty);			
			$('#remove_profile_pic_selection').on(common_js_funcs.click_event, usersfuncs.removeProfilePicSelection);

			$('#remove_profile_pic').on(common_js_funcs.click_event, usersfuncs.markProfilePicForDeletion);
			$('#undo_remove_profile_pic').on(common_js_funcs.click_event, usersfuncs.removeProfilePicDeleteMarker);

			$('#user_list_container').on(common_js_funcs.click_event,'.searched_elem .remove_filter' ,usersfuncs.clearSearch);

			$('#req_contact_btn').on(common_js_funcs.click_event, usersfuncs.sendContactReq);

			usersfuncs.user_roles = usersfuncs.all_user_roles = {$dom_ready_data['users']['user_roles_list']}||[];
			usersfuncs.user_levels = {$dom_ready_data['users']['user_levels']}||{};


			$('#add_form_field_dob_picker').datepicker({
				dateFormat:'dd-M-yy',
				altFormat: 'yy-mm-dd',
				altField: '#add_form_field_dob',
				showOn: "both",
				buttonImage: 'images/calendar.png',
				// buttonImageOnly: true,
				showButtonPanel: true,
				changeMonth: true,
				changeYear: true,
				yearRange: '1900:+0',
				maxDate: "+0d"
			});
			$('#spousdob').datepicker({
				dateFormat:'dd-M-yy',
				altFormat: 'yy-mm-dd',
				altField: '#add_form_field_spouse_dob',
				showOn: "both",
				buttonImage: 'images/calendar.png',
				// buttonImageOnly: true,
				showButtonPanel: true,
				changeMonth: true,
				changeYear: true,
				yearRange: '1900:+0',
				maxDate: "+0d"
			});

			$('#add_form_field_annv_picker').datepicker({
				dateFormat:'dd-M-yy',
				altFormat: 'yy-mm-dd',
				altField: '#add_form_field_annv',
				showOn: "both",
				buttonImage: 'images/calendar.png',
				// buttonImageOnly: true,
				showButtonPanel: true,
				changeMonth: true,
				changeYear: true,
				yearRange: '1900:+0',
				maxDate: "+0d"
			});

// 			$('#search-field_joinedafterdt_picker').datepicker({
// 				dateFormat:'dd-M-yy',
// 				altFormat: 'yy-mm-dd',
// 				altField: '#search-field_joining-dt',
// 				showOn: "both",
// 				buttonImage: 'images/calendar.png',
// 				buttonImageOnly: true,
// 				showButtonPanel: true,
// 				changeMonth: true,
// 				changeYear: true,
// 				yearRange: '1940:+0',
// 				maxDate: "+0d"
// 			});

			$('#add_form_field_paidon_picker').datepicker({
				dateFormat:'dd-M-yy',
				altFormat: 'yy-mm-dd',
				altField: '#add_form_field_paidon',
				showOn: "both",
				buttonImage: 'images/calendar.png',
				// buttonImageOnly: true,
				showButtonPanel: true,
				changeMonth: true,
				changeYear: true,
				yearRange: '2000:+0',
				maxDate: "+0d"
			});
// 			$('#add_form_field_expdt_picker').datepicker({
// 				dateFormat:'dd-M-yy',
// 				altFormat: 'yy-mm-dd',
// 				altField: '#add_form_field_expdt',
// 				showOn: "both",
// 				buttonImage: 'images/calendar.png',
// 				// buttonImageOnly: true,
// 				showButtonPanel: true,
// 				changeMonth: true,
// 				changeYear: true,
// 				yearRange: '2024:2099',
// 				// maxDate: "+0d"
// 			});

			$('#add_form_field_joiningdt_picker').datepicker({
				dateFormat:'dd-M-yy',
				altFormat: 'yy-mm-dd',
				altField: '#add_form_field_joiningdt',
				showOn: "both",
				buttonImage: 'images/calendar.png',
				// buttonImageOnly: true,
				showButtonPanel: true,
				changeMonth: true,
				changeYear: true,
				yearRange: '2002:+0',
				maxDate: "+0d"
			});

			usersfuncs.cities = {$dom_ready_data['users']['cities']};
			usersfuncs.countries = {$dom_ready_data['users']['countries']};
			usersfuncs.states = {$dom_ready_data['users']['states']};


			$('#add_form_field_desiginassoc').autocomplete({
				source: {$dom_ready_data['users']['designations']}

			});
					
			$('#add_form_field_rescity').autocomplete({
				source: usersfuncs.cities
			});
			$('#add_form_field_workcity').autocomplete({
				source: usersfuncs.cities
			});
			$('#add_form_field_resstate').autocomplete({
				source: usersfuncs.states
			});
			$('#add_form_field_workstate').autocomplete({
				source: usersfuncs.states
			});
			$('#add_form_field_rescountry').autocomplete({
				source: usersfuncs.countries
			});
			$('#add_form_field_workcountry').autocomplete({
				source: usersfuncs.countries
			});
			$('#add_form_field_worktype').autocomplete({
				source: {$dom_ready_data['users']['work_type']}
			});
			$('#search-field_residence-country').autocomplete({
				source: usersfuncs.countries
			});
			$('#search-field_residence-city').autocomplete({
				source: usersfuncs.cities
			});
						

			usersfuncs.batch_no_min = {$dom_ready_data['users']['field_meta']['batch_no']['min']};
			usersfuncs.batch_no_max = {$dom_ready_data['users']['field_meta']['batch_no']['max']};
			
			usersfuncs.initializeGroupsSelector('add_form_field_memtype'); 
			
			$(window).hashchange(usersfuncs.onHashChange);
			$(window).hashchange();
			usersfuncs.salutaions={$dom_ready_data['users']['salutation']} || [];
EOF;
			break;

		case 'mem-regs':
			$datepicker_icon = CONST_THEMES_CUSTOM_IMAGES_PATH.'datepicker.gif';
			$js_code=<<<EOF


			$('.main-content').on(common_js_funcs.click_event,'.record-list-refresh-button',{self:memregfuncs},memregfuncs.refreshList);

			$('.main-content').on(common_js_funcs.click_event,'td.clickable-cell',{self:memregfuncs},common_js_funcs.changeLocationWithDataProperty);
			$('.main-content').on(common_js_funcs.click_event,'.page-link',{self:memregfuncs},memregfuncs.changePage);
			$('.main-content').on(common_js_funcs.click_event,'.toggle-search',{self:memregfuncs},memregfuncs.toggleSearch);
			$('.main-content').on(common_js_funcs.click_event,'.record-delete-button',{self:memregfuncs},memregfuncs.deleteUser);

			$('#users-list>thead>tr>th.sortable').bind(common_js_funcs.click_event,{self:memregfuncs},memregfuncs.sortTable);

			$('#user_detail_view_container .email-icon-form-input, #user_detail_view_container .wa-icon-form-input, #user_detail_view_container .tel-icon-form-input, #user_detail_add_edit_container .email-icon-form-input, #user_detail_add_edit_container .wa-icon-form-input, #user_detail_add_edit_container .tel-icon-form-input').on(common_js_funcs.click_event, common_js_funcs.changeLocationWithDataProperty);			
			$('#remove_profile_pic_selection').on(common_js_funcs.click_event, memregfuncs.removeProfilePicSelection);

			$('#remove_profile_pic').on(common_js_funcs.click_event, memregfuncs.markProfilePicForDeletion);
			$('#undo_remove_profile_pic').on(common_js_funcs.click_event, memregfuncs.removeProfilePicDeleteMarker);
			$('#add_form_field_status').on(common_js_funcs.click_event, memregfuncs.onRegStatusChange); 

			$('#user_list_container').on(common_js_funcs.click_event,'.searched_elem .remove_filter' ,memregfuncs.clearSearch);

			$('#add_form_field_dob_picker').datepicker({
				dateFormat:'dd-M-yy',
				altFormat: 'yy-mm-dd',
				altField: '#add_form_field_dob',
				showOn: "both",
				buttonImage: 'images/calendar.png',
				// buttonImageOnly: true,
				showButtonPanel: true,
				changeMonth: true,
				changeYear: true,
				yearRange: '1900:+0',
				maxDate: "+0d"
			});


			$('#add_form_field_annv_picker').datepicker({
				dateFormat:'dd-M-yy',
				altFormat: 'yy-mm-dd',
				altField: '#add_form_field_annv',
				showOn: "both",
				buttonImage: 'images/calendar.png',
				// buttonImageOnly: true,
				showButtonPanel: true,
				changeMonth: true,
				changeYear: true,
				yearRange: '1900:+0',
				maxDate: "+0d"
			});

			$('#add_form_field_paidon_picker').datepicker({
				dateFormat:'dd-M-yy',
				altFormat: 'yy-mm-dd',
				altField: '#add_form_field_paidon',
				showOn: "both",
				buttonImage: 'images/calendar.png',
				// buttonImageOnly: true,
				showButtonPanel: true,
				changeMonth: true,
				changeYear: true,
				yearRange: '2000:+0',
				maxDate: "+0d"
			});


			// $('#add_form_field_desiginassoc').autocomplete({
			// 	source: {$dom_ready_data['users']['designations']}

			// });
					
			// $('#add_form_field_memtype').autocomplete({
			// 	source: {$dom_ready_data['users']['mem_types']}

			// });
			$('#add_form_field_rescity').autocomplete({
				source: {$dom_ready_data['users']['cities']}
			});
			$('#add_form_field_workcity').autocomplete({
				source: {$dom_ready_data['users']['cities']}
			});
			$('#add_form_field_resstate').autocomplete({
				source: {$dom_ready_data['users']['states']}
			});
			$('#add_form_field_workstate').autocomplete({
				source: {$dom_ready_data['users']['states']}
			});
			$('#add_form_field_rescountry').autocomplete({
				source: {$dom_ready_data['users']['countries']}
			});
			$('#add_form_field_workcountry').autocomplete({
				source: {$dom_ready_data['users']['countries']}
			});
			$('#add_form_field_worktype').autocomplete({
				source: {$dom_ready_data['users']['work_type']}
			});
			// $('#add_form_field_workind').autocomplete({
			// 	source: {$dom_ready_data['users']['work_ind']}
			// });
						
			
			memregfuncs.batch_no_min = {$dom_ready_data['users']['field_meta']['batch_no']['min']};
			memregfuncs.batch_no_max = {$dom_ready_data['users']['field_meta']['batch_no']['max']};
					
			
			
			// $('#search-field_role').select2({placeholder: 'Role',allowClear: true , tokenSeparators: [","]});
			// $('#search-field_status').select2({placeholder: 'Status',allowClear: true});
			$(window).hashchange(memregfuncs.onHashChange);
			$(window).hashchange();
			memregfuncs.salutaions={$dom_ready_data['users']['salutation']} || [];
EOF;
			break;
				
		case 'ad-banners':
			$datepicker_icon = CONST_THEMES_CUSTOM_IMAGES_PATH.'datepicker.gif';
			$js_code=<<<EOF

			$('.main-content').on(common_js_funcs.click_event,'td.clickable-cell',{self:adfuncs},common_js_funcs.changeLocationWithDataProperty);
			$('.main-content').on(common_js_funcs.click_event,'.page-link',{self:adfuncs},adfuncs.changePage);
			$('.main-content').on(common_js_funcs.click_event,'.toggle-search',{self:adfuncs},adfuncs.toggleSearch);
			// $('.main-content').on(common_js_funcs.click_event,'.record-delete-button',{self:adfuncs},adfuncs.deleteUser);

			$('#recs-list>thead>tr>th.sortable').bind(common_js_funcs.click_event,{self:adfuncs},adfuncs.sortTable);

			$('#rec_list_container').on(common_js_funcs.click_event,'.searched_elem .remove_filter' ,adfuncs.clearSearch);

			$('#add_form_field_startdt_picker').datepicker({
				dateFormat:'dd-M-yy',
				altFormat: 'yy-mm-dd',
				altField: '#add_form_field_startdt',
				showOn: "both",
				buttonImage: 'images/calendar.png',
				buttonImageOnly: true,
				showButtonPanel: true,
				changeMonth: true,
				changeYear: true,
				yearRange: '2024:+10',
				// minDate: "-0d"
			});

			$('#add_form_field_enddt_picker').datepicker({
				dateFormat:'dd-M-yy',
				altFormat: 'yy-mm-dd',
				altField: '#add_form_field_enddt',
				showOn: "both",
				buttonImage: 'images/calendar.png',
				buttonImageOnly: true,
				showButtonPanel: true,
				changeMonth: true,
				changeYear: true,
				yearRange: '2024:+10',
				// minDate: "-0d"
			});
			
			$('#search-field_periodend_picker').datepicker({
				dateFormat:'dd-M-yy',
				altFormat: 'yy-mm-dd',
				altField: '#search-field_periodend',
				// showOn: "both",
				// buttonImage: 'images/calendar.png',
				// buttonImageOnly: true,
				showButtonPanel: true,
				changeMonth: true,
				changeYear: true,
				yearRange: '2024:+10'
			});

			$('#search-field_periodstart_picker').datepicker({
				dateFormat:'dd-M-yy',
				altFormat: 'yy-mm-dd',
				altField: '#search-field_periodstart',
				// showOn: "both",
				// buttonImage: 'images/calendar.png',
				// buttonImageOnly: true,
				showButtonPanel: true,
				changeMonth: true,
				changeYear: true,
				yearRange: '2024:+10'
			});

			

			


			$(window).hashchange(adfuncs.onHashChange);
			$(window).hashchange();
			
EOF;
			break;
		case 'events':
			$datepicker_icon = CONST_THEMES_CUSTOM_IMAGES_PATH.'datepicker.gif';
			$js_code=<<<EOF

			$('.main-content').on(common_js_funcs.click_event,'td.clickable-cell',{self:evfuncs},common_js_funcs.changeLocationWithDataProperty);
			$('.main-content').on(common_js_funcs.click_event,'.page-link',{self:evfuncs},evfuncs.changePage);
			$('.main-content').on(common_js_funcs.click_event,'.toggle-search',{self:evfuncs},evfuncs.toggleSearch);
			// $('.main-content').on(common_js_funcs.click_event,'.record-delete-button',{self:evfuncs},evfuncs.deleteUser);

			$('#recs-list>thead>tr>th.sortable').bind(common_js_funcs.click_event,{self:evfuncs},evfuncs.sortTable);

			$('#rec_list_container').on(common_js_funcs.click_event,'.searched_elem .remove_filter' ,evfuncs.clearSearch);

			$('#event_booking_link_cont a').on(common_js_funcs.click_event, e=>{
				e.preventDefault();
				e.stopPropagation();
				let bk_lnk = $(e.currentTarget).data('bk_lnk');
				navigator.clipboard.writeText(bk_lnk);
				alert(`The registration link shown below has been copied to the clipboard.\n\n\${bk_lnk}`);
			});

			$('#add_form_field_ebtktpricechk').on('click', e=>{
				$('#early_bird_pricing_rules').toggleClass('d-none', !$(e.currentTarget).is(':checked'));
				evfuncs.enableDisableEarlyBirdOffer();
			});
			$('#add_form_field_ebenddtchk').on('click', e=>{
				evfuncs.allowDisallowEarlyBirdEndDate($(e.target).is(':checked'),null);
			});
			$('#add_form_field_ebmaxcntchk').on('click', e=>{
				evfuncs.allowDisallowEarlyBirdRegCnt($(e.target).is(':checked'),'');
			});

			$('#add_form_field_ebmaxcnt,#add_form_field_ebtktprice').on('input change', e=>{
				if(/([^0-9]+)/.test(e.currentTarget.value)){
					let cnt=e.currentTarget.value.replace(/[^0-9]+/g,''); 
					cnt = cnt!=''?parseInt(cnt,10):'';
					e.currentTarget.value = cnt>=0?cnt:'';   
				}
				evfuncs.setUnsetEarlyBirdRulesText();
			});

			$('#add_form_field_startdt_picker').datepicker({
				dateFormat:'dd-M-yy',
				altFormat: 'yy-mm-dd',
				altField: '#add_form_field_startdt',
				showOn: "both",
				buttonImage: 'images/calendar.png',
				buttonImageOnly: true,
				showButtonPanel: true,
				changeMonth: true,
				changeYear: true,
				yearRange: '2024:+10',
				minDate: "-0d"
			});

			$('#add_form_field_enddt_picker').datepicker({
				dateFormat:'dd-M-yy',
				altFormat: 'yy-mm-dd',
				altField: '#add_form_field_enddt',
				showOn: "both",
				buttonImage: 'images/calendar.png',
				buttonImageOnly: true,
				showButtonPanel: true,
				changeMonth: true,
				changeYear: true,
				yearRange: '2024:+10',
				minDate: "-0d"
			});

			$('#add_form_field_ebenddt_picker').datepicker({
				dateFormat:'dd-M-yy',
				altFormat: 'yy-mm-dd',
				altField: '#add_form_field_ebenddt',
				showOn: "both",
				buttonImage: 'images/calendar.png',
				buttonImageOnly: true,
				showButtonPanel: true,
				changeMonth: true,
				changeYear: true,
				yearRange: '2024:+10',
				
			}).on('change', function(e) {
		        // `e` here contains the extra attributes
		        evfuncs.setUnsetEarlyBirdRulesText();
		    });

			$('#add_form_field_regstartdt_picker').datepicker({
				dateFormat:'dd-M-yy',
				altFormat: 'yy-mm-dd',
				altField: '#add_form_field_regstartdt',
				showOn: "both",
				buttonImage: 'images/calendar.png',
				buttonImageOnly: true,
				showButtonPanel: true,
				changeMonth: true,
				changeYear: true,
				yearRange: '2024:+10',
				minDate: "-0d"
			});

			$('#add_form_field_regenddt_picker').datepicker({
				dateFormat:'dd-M-yy',
				altFormat: 'yy-mm-dd',
				altField: '#add_form_field_regenddt',
				showOn: "both",
				buttonImage: 'images/calendar.png',
				buttonImageOnly: true,
				showButtonPanel: true,
				changeMonth: true,
				changeYear: true,
				yearRange: '2024:+10',
				minDate: "-0d"
			});
			
			$('#search-field_periodend_picker').datepicker({
				dateFormat:'dd-M-yy',
				altFormat: 'yy-mm-dd',
				altField: '#search-field_periodend',
				// showOn: "both",
				// buttonImage: 'images/calendar.png',
				// buttonImageOnly: true,
				showButtonPanel: true,
				changeMonth: true,
				changeYear: true,
				yearRange: '2024:+10'
			});

			$('#search-field_periodstart_picker').datepicker({
				dateFormat:'dd-M-yy',
				altFormat: 'yy-mm-dd',
				altField: '#search-field_periodstart',
				// showOn: "both",
				// buttonImage: 'images/calendar.png',
				// buttonImageOnly: true,
				showButtonPanel: true,
				changeMonth: true,
				changeYear: true,
				yearRange: '2024:+10'
			});

			

			


			$(window).hashchange(evfuncs.onHashChange);
			$(window).hashchange();
			
EOF;
			break;
		

		case 'event-registrations':
			$js_code=<<<EOF

			$('.main-content').on(common_js_funcs.click_event,'td.clickable-cell',{self:evregfuncs},common_js_funcs.changeLocationWithDataProperty);
			$('.main-content').on(common_js_funcs.click_event,'.page-link',{self:evregfuncs},evregfuncs.changePage);
			$('.main-content').on(common_js_funcs.click_event,'.toggle-search',{self:evregfuncs},evregfuncs.toggleSearch);
			$('#event_selector').on('change',evregfuncs.onEventChange);

			$('#recs-list>thead>tr>th.sortable').bind(common_js_funcs.click_event,{self:evregfuncs},evregfuncs.sortTable);

			$('#rec_list_container').on(common_js_funcs.click_event,'.searched_elem .remove_filter' ,evregfuncs.clearSearch);  
			$('#add_form_field_nooftickets').on('keyup', evregfuncs.onTktEntry); 
			
			$('#search-field_evperiodend_picker').datepicker({
				dateFormat:'dd-M-yy',
				altFormat: 'yy-mm-dd',
				altField: '#search-field_evperiodend',
				// showOn: "both",
				// buttonImage: 'images/calendar.png',
				// buttonImageOnly: true,
				showButtonPanel: true,
				changeMonth: true,
				changeYear: true,
				yearRange: '2023:+10'
			});

			$('#search-field_evperiodstart_picker').datepicker({
				dateFormat:'dd-M-yy',
				altFormat: 'yy-mm-dd',
				altField: '#search-field_evperiodstart',
				// showOn: "both",
				// buttonImage: 'images/calendar.png',
				// buttonImageOnly: true,
				showButtonPanel: true,
				changeMonth: true,
				changeYear: true,
				yearRange: '2023:+10'
			});

			$(window).hashchange(evregfuncs.onHashChange);
			$(window).hashchange();
			
EOF;
			break;
		

		case 'manage-offers':
			$datepicker_icon = CONST_THEMES_CUSTOM_IMAGES_PATH.'datepicker.gif';
			$js_code=<<<EOF

			$('.main-content').on(common_js_funcs.click_event,'td.clickable-cell',{},common_js_funcs.changeLocationWithDataProperty);
			$('.main-content').on(common_js_funcs.click_event,'.page-link',{},ManageOffer.changePage);
			$('.main-content').on(common_js_funcs.click_event,'.toggle-search',{},ManageOffer.toggleSearch);
			
			$('#recs-list>thead>tr>th.sortable').bind(common_js_funcs.click_event,{},ManageOffer.sortTable);
			$('#rec_list_container').on(common_js_funcs.click_event,'.searched_elem .remove_filter' ,ManageOffer.removeFilter);

			$('#add_form_field_valid_upto_picker').datepicker({
				dateFormat:'dd-M-yy',
				altFormat: 'yy-mm-dd',
				altField: '#add_form_field_valid_upto',
				showOn: "both",
				buttonImage: 'images/calendar.png',
				buttonImageOnly: true,
				showButtonPanel: true,
				changeMonth: true,
				changeYear: true,
				yearRange: '+0:2099',
				minDate: "-0d",
				defaultDate: "2099-12-31",
				onSelect:(dt, ob)=>{
					
				}
			});
			
			$('#add_do_form').on(common_js_funcs.click_event, '.clear-file-input', ManageOffer.clearFileInput);
			$('#add_do_form').on(common_js_funcs.click_event, '.remove-existing-file, .keep-existing-file', ManageOffer.handleExistingFileRemoval);
			$('#add_do_form').on('change', 'input[type=file]', ManageOffer.fileSelected);
			$(window).hashchange(ManageOffer.onHashChange);
			$(window).hashchange();
			
EOF;
			break;
		

		case 'discount-offers':
			$js_code=<<<EOF
			$('#cat_list_container').on(common_js_funcs.click_event,'.clickable-cell',{self:DiscountOffer},common_js_funcs.changeLocationWithDataProperty);
			$('#fetch_more').on(common_js_funcs.click_event, DiscountOffer.fetchMore);
			$(window).hashchange(DiscountOffer.onHashChange);
			$(window).hashchange();
EOF;
			break;
		
		case 'event-bookings':
			$js_code=<<<EOF

			$('.main-content').on(common_js_funcs.click_event,'td.clickable-cell',{self:evbkngfuncs},common_js_funcs.changeLocationWithDataProperty);
			$('.main-content').on(common_js_funcs.click_event,'.page-link',{self:evbkngfuncs},evbkngfuncs.changePage);
			$('#rec_list_container').on(common_js_funcs.click_event,'.toggle-search',{self:evbkngfuncs},evbkngfuncs.toggleSearch);
			$('#event_bookings_summary_list_container').on(common_js_funcs.click_event,'.toggle-search',{self:evbkngfuncs},evbkngfuncs.toggleSummarySearch);
			$('#event_selector').on('change',evbkngfuncs.onEventChange);

			$('#recs-list>thead>tr>th.sortable').bind(common_js_funcs.click_event,{self:evbkngfuncs},evbkngfuncs.sortTable);
			$('#summary-recs-list>thead>tr>th.sortable').bind(common_js_funcs.click_event,{self:evbkngfuncs},evbkngfuncs.sortSummaryTable);

			$('#rec_list_container').on(common_js_funcs.click_event,'.searched_elem .remove_filter' ,evbkngfuncs.clearSearch);  
			$('#event_bookings_summary_list_container').on(common_js_funcs.click_event,'.searched_elem .remove_filter' ,evbkngfuncs.clearSummarySearch);  
							
			$('#search-field_evregperiodend_picker').datepicker({
				dateFormat:'dd-M-yy',
				altFormat: 'yy-mm-dd',
				altField: '#search-field_evregperiodend',
				// showOn: "both",
				// buttonImage: 'images/calendar.png',
				// buttonImageOnly: true,
				showButtonPanel: true,
				changeMonth: true,
				changeYear: true,
				yearRange: '2023:+10'
			});

			$('#search-field_evregperiodstart_picker').datepicker({
				dateFormat:'dd-M-yy',
				altFormat: 'yy-mm-dd',
				altField: '#search-field_evregperiodstart',
				// showOn: "both",
				// buttonImage: 'images/calendar.png',
				// buttonImageOnly: true,
				showButtonPanel: true,
				changeMonth: true,
				changeYear: true,
				yearRange: '2023:+10'
			});

			$(window).hashchange(evbkngfuncs.onHashChange);
			$(window).hashchange();
			
EOF;
			break;
		

		case 'sectors':
			$js_code=<<<EOF

			$('.main-content').on(common_js_funcs.click_event,'td.clickable-cell',{self:sectorfuncs},common_js_funcs.changeLocationWithDataProperty);
			$('.main-content').on(common_js_funcs.click_event,'.page-link',{self:sectorfuncs},sectorfuncs.changePage);
			$('.main-content').on(common_js_funcs.click_event,'.toggle-search',{self:sectorfuncs},sectorfuncs.toggleSearch);
			$('.main-content').on(common_js_funcs.click_event,'.record-delete-button',{self:sectorfuncs},sectorfuncs.deleteSector);

			$('#recs-list>thead>tr>th.sortable').bind(common_js_funcs.click_event,{self:sectorfuncs},sectorfuncs.sortTable);

			$('#rec_list_container').on(common_js_funcs.click_event,'.searched_elem .remove_filter' ,sectorfuncs.clearSearch);

			$(window).hashchange(sectorfuncs.onHashChange);
			$(window).hashchange();
			
EOF;
			break;
		
		case 'scan-tkt':
			$js_code=<<<EOF
			$('#add_form_field_noofguests')?.on('input', e=>{
					if(e.currentTarget.value.length>3){e.currentTarget.value = e.currentTarget.value.substr(0,3); }  
					if(/^0/.test(e.currentTarget.value)){
						let cnt=e.currentTarget.value.replace(/^0+/,''); e.currentTarget.value = cnt!=''?parseInt(cnt,10):'';
					}else if(/[^0-9]+/.test(e.currentTarget.value)){ let cnt=e.currentTarget.value.replace(/[^0-9]+/g,''); e.currentTarget.value = cnt!=''?parseInt(cnt,10):'';   } 
					return false;
				});
			$('#record-cancel-button').on('click', scantktfuncs.onEntryCancel);	
			scantktfuncs.initScanner();
			
EOF;
			break;
		case 'meeting':
			$js_code=<<<EOF

			$(window).hashchange(usersfuncs.onHashChange);
			$(window).hashchange();
			
EOF;
		case 'meetings':
			$js_code=<<<EOF

			$('.main-content').on(common_js_funcs.click_event,'td.clickable-cell',{self:meetfuncs},common_js_funcs.changeLocationWithDataProperty);
			$('.main-content').on(common_js_funcs.click_event,'.page-link',{self:meetfuncs},meetfuncs.changePage);
			$('.main-content').on(common_js_funcs.click_event,'.toggle-search',{self:meetfuncs},meetfuncs.toggleSearch);
			// $('.main-content').on(common_js_funcs.click_event,'.record-delete-button',{self:meetfuncs},meetfuncs.deleteUser);

			$('#recs-list>thead>tr>th.sortable').bind(common_js_funcs.click_event,{self:meetfuncs},meetfuncs.sortTable);

			$('#rec_list_container').on(common_js_funcs.click_event,'.searched_elem .remove_filter' ,meetfuncs.clearSearch);

			

			$(window).hashchange(meetfuncs.onHashChange);
			$(window).hashchange();
			
EOF;
			break;
		case 'roster':
			$js_code=<<<EOF

			$(window).hashchange(usersfuncs.onHashChange);
			$(window).hashchange();
			
EOF;
			break;		
			
		case 'goals':
			$js_code=<<<EOF

			$(window).hashchange(usersfuncs.onHashChange);
			$(window).hashchange();
			
EOF;
			break;

		case 'all-goals':
			$js_code=<<<EOF

			$(window).hashchange(usersfuncs.onHashChange);
			$(window).hashchange();
			
EOF;
			break;	
			
			

	}

	return $js_code;
}




?>
