var DiscountOffer={
	searchparams:[],  /* [{searchon:'',searchtype:'',searchtext:''},{},..] */
	sortparams:[],  /* [{sorton:'',sortorder:''},{},..] */
	default_sort:{sorton:'title',sortorder:'ASC'},
	paginationdata:{},
	cat_searchparams:[],  /* [{searchon:'',searchtype:'',searchtext:''},{},..] */
	cat_sortparams:[],  /* [{sorton:'',sortorder:''},{},..] */
	cat_default_sort:{sorton:'title',sortorder:'ASC'},
	cat_paginationdata:{},
	ajax_data_script:'discount-offers.php',
	curr_page_hash:'',
	prev_page_hash:'',
	hash_params:{},

	resetSearchParamsObj: ()=>{
		DiscountOffer.searchparams=[];
	},

	setSearchParams: obj=>{
		DiscountOffer.searchparams.push(obj);
	},
	
	getList: options=>{
		params=['mode=get-offers','pno='+encodeURIComponent(options?.pno || 1)];
		params.push('search_data='+encodeURIComponent(JSON.stringify(DiscountOffer.searchparams)));
		params.push('sort_data='+encodeURIComponent(JSON.stringify(DiscountOffer.sortparams)));
		params.push('ref='+Math.random());
		$("#common-processing-overlay").removeClass('d-none');
		location.hash=params.join('&');
	},

	showList: (resp,other_params)=>{
		if(resp[1].pno==1 && (resp[1]?.cat_active??'n')=='n' ){
			// If the requested category is not available then send the user back to the default screen
			location.hash = '';
			return;
		}
		$("#cat_list_container").addClass('d-none');
		$("#rec_list_container").removeClass('d-none');
		$("#common-processing-overlay").addClass('d-none');
		if(resp[0]!=0){
			alert('An unexpected error occurred. Please contact the web team.');
			return;
		}
		$('#fetch_more').data('pno', resp[1].pno+1);
		// $('#heading_rec_cnt').text(resp[1].rec_count??'0');

		if(resp[1].pno==1){
			$('#disc_offer_cat').text(resp[1].cat_name);
			if(resp[1].tot_rec_cnt>0)
				$('#heading_offers_rec_cnt').text((resp[1]['rec_count']==resp[1]['tot_rec_cnt'])?`${resp[1]['tot_rec_cnt']}`:`${resp[1]['rec_count'] || 0} of ${resp[1]['tot_rec_cnt']}`);
			else
				$('#heading_offers_rec_cnt').text('0');
		}


		const max_offer_per_row = 2;
		let row_len = 0, r=null;
		[...resp[1].records].forEach(o=>{
			if(row_len==0){
			}
			if(row_len%max_offer_per_row==0){
				// create a new row to house the offers
				$('#offers_list').append(r);
				r = $('#category_details_container_row').clone().removeAttr('id');
			}
			let c=$('#offer_clone').clone().removeAttr('id');
			c.find('.offer_details').toggleClass('mou_exists_margin_top', (o.mou_url??'')!='');
			c.find('.title_elem').text(o.title);
			c.find('.desc_elem').text(o.description);
			// c.find('.company').text(o.company);
			c.find('.url_elem').toggleClass('d-none', o.offer_url=='').find('a').prop('href',o.offer_url).text(o.offer_url);
			
			// c.find('.valid-upto').text(o.valid_upto);
			// c.find('.image').toggleClass('d-none', !o.dsk_img_url && !o.mob_img_url);
			// if(o.dsk_img_url)
			// 	c.find('.dsk-img img').prop('src', o.dsk_img_url)
			
			c.find('.mou-link').toggleClass('mou_exists', (o.mou_url??'')!='').find('a').prop('href', o.mou_url)
			c.find('.mob-img').toggleClass('d-none', (o.mob_img_url??'')=='').find('img').prop('src', o.mob_img_url)
			// $('#offers_list').append(c);
			r.append(c);
			row_len++;
		});
		if(row_len>0)
			$('#offers_list').append(r); // append the last row

		//$('#fetch_more').parent().toggleClass('d-none', resp[1].records.length==0);
		$('#fetch_more').parent().toggleClass('d-none', !resp[1].has_more_offers);
		if(resp[1].pno==1 && resp[1].records.length==0){
			let c=$('#nooffer_clone').clone().removeAttr('id');
			$('#offers_list').empty().append(c);
		}

	},
	
	fetchMore: e=>{
		$("#common-processing-overlay").removeClass('d-none');
		let params = {mode:'get-offers', pno:$(e.currentTarget).data('pno')??1, search_data:JSON.stringify(DiscountOffer.searchparams), sort_data: JSON.stringify(DiscountOffer.sortparams), ref: Math.random()};
		common_js_funcs.callServer({
			cache:'no-cache',
			async:true,
			dataType:'json',
			type:'post', 
			url:DiscountOffer.ajax_data_script,
			params:params,
			successResponseHandler:DiscountOffer.showList,
			successResponseHandlerParams:{}
		});
	},


	getCatList: options=>{
		let pno=1,
			params=['pno='+encodeURIComponent(options.pno || 1)]
		params.push('search_data='+encodeURIComponent(JSON.stringify(DiscountOffer.cat_searchparams)));
		params.push('sort_data='+encodeURIComponent(JSON.stringify(DiscountOffer.cat_sortparams)));
		params.push('ref='+Math.random());
		$("#common-processing-overlay").removeClass('d-none');
		location.hash=params.join('&');
	},

	showCatList: (resp,other_params)=>{
		let list_html=resp[1].list;
		$("#cat_list_container").removeClass('d-none');
		$("#rec_list_container").addClass('d-none');
		$("#common-processing-overlay").addClass('d-none');
		$("#catlistbox").html(list_html);
		if(resp[1].tot_rec_cnt>0)
			$('#heading_rec_cnt').text((resp[1]['rec_count']==resp[1]['tot_rec_cnt'])?`${resp[1]['tot_rec_cnt']}`:`${resp[1]['rec_count'] || 0} of ${resp[1]['tot_rec_cnt']}`);
		else
			$('#heading_rec_cnt').text('0');
		// $(".back-to-list-button").addClass('d-none').attr('href',"manage-offers.php#"+DiscountOffer.curr_page_hash);
		DiscountOffer.cat_paginationdata=resp[1].pagination_data;
		// DiscountOffer.setSortOrderIcon();
	},


	onHashChange:function(e){
		var hash=location.hash.replace(/^#/,'');
		// alert(hash);
		if(DiscountOffer.curr_page_hash!=DiscountOffer.prev_page_hash){
			DiscountOffer.prev_page_hash=DiscountOffer.curr_page_hash;
		}
		DiscountOffer.curr_page_hash=hash;

		var hash_params={mode:''};
		if(hash!=''){
			var hash_params_temp=hash.split('&');
			var hash_params_count= hash_params_temp.length;
			for(var i=0; i<hash_params_count; i++){
				var temp=hash_params_temp[i].split('=');
				hash_params[temp[0]]=decodeURIComponent(temp[1]);
			}
		}

		DiscountOffer.hash_params = {...hash_params}; // creating a shallow copy of the local object hash_params
		switch(hash_params.mode.toLowerCase()){
			case 'get-offers':
					$('.alert-success, .alert-danger').addClass('d-none');
					$('#msgFrm').removeClass('d-none');
					var params={mode:'get-offers',pno:1, search_data:"[]", sort_data:"[]"};

					if(hash_params.hasOwnProperty('pno')){
						params['pno']=hash_params.pno
					}else{
						params['pno']=1;
					}

					if(hash_params.hasOwnProperty('search_data')){
						params['search_data']=hash_params.search_data;

					}
					if(hash_params.hasOwnProperty('sort_data')){
						params['sort_data']=hash_params.sort_data;

					}

					DiscountOffer.searchparams=JSON.parse(params['search_data']);
					DiscountOffer.sortparams=JSON.parse(params['sort_data']);

					if(DiscountOffer.sortparams.length==0){
						DiscountOffer.sortparams.push(DiscountOffer.default_sort);
						params['sort_data']=JSON.stringify(DiscountOffer.sortparams);
					}

					if(DiscountOffer.searchparams.length==0 && hash_params.cid){
						DiscountOffer.setSearchParams({search_on:'category_id',search_type:'EQUAL',search_text:hash_params.cid});
						params['search_data']=JSON.stringify(DiscountOffer.searchparams);
						$('#offers_list').empty();
						setTimeout("DiscountOffer.getList();",0);
						 // change the has to use the searchdata instead
						return;
					}

					// document.search_form.reset();

					// if(DiscountOffer.searchparams.length>0){
					// 		$.each(DiscountOffer.searchparams , function(idx,data) {
					// 				//console.log(data);
					// 				switch (data.search_on) {

					// 					case 'cid':
					// 						// $("#search-field_bookingid").val(data.searchtext);
					// 						break;
					// 					case 'title':
					// 						// $("#search-field_evname").val(data.searchtext);
					// 						break;
					// 				}

					// 		});
							
					// }
					
					$("#common-processing-overlay").removeClass('d-none');

					common_js_funcs.callServer({cache:'no-cache',async:true,dataType:'json',type:'post', url:self.ajax_data_script,params:params,successResponseHandler:DiscountOffer.showList,successResponseHandlerParams:{self:DiscountOffer}});

					
					// DiscountOffer.showHidePanel('evreg_search_toggle');

					break;

			default:

					$('.alert-success, .alert-danger').addClass('d-none');
					$('#msgFrm').removeClass('d-none');
					var params={mode:'getCatList',pno:1, search_data:"[]", sort_data:JSON.stringify(DiscountOffer.cat_sortparams), listformat:'html'};

					if(hash_params.hasOwnProperty('pno')){
						params['pno']=hash_params.pno
					}else{
						params['pno']=1;
					}

					if(hash_params.hasOwnProperty('search_data')){
						params['search_data']=hash_params.search_data;

					}
					if(hash_params.hasOwnProperty('sort_data')){
						params['sort_data']=hash_params.sort_data;

					}

					DiscountOffer.cat_searchparams=JSON.parse(params['search_data']);
					DiscountOffer.cat_sortparams=JSON.parse(params['sort_data']);

					if(DiscountOffer.cat_sortparams.length==0){
						DiscountOffer.cat_sortparams.push(DiscountOffer.cat_default_sort);
						params['sort_data']=JSON.stringify(DiscountOffer.cat_sortparams);
					}

					
					$("#common-processing-overlay").removeClass('d-none');

					common_js_funcs.callServer({cache:'no-cache',async:true,dataType:'json',type:'post', url:self.ajax_data_script,params:params,successResponseHandler:DiscountOffer.showCatList,successResponseHandlerParams:{self:DiscountOffer}});
		}

	}

}