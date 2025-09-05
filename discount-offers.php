<?php
$page='discount-offers';
require_once 'inc.php';
$template_type='';
$page_title = 'Epic Offers'.CONST_TITLE_AFX;
$page_description = 'One can see the available epic offers.';
$body_template_file = CONST_THEMES_TEMPLATE_INCLUDE_PATH . 'discount-offers.tpl';
$body_template_data = array();
$page_renderer->registerBodyTemplate($body_template_file,$body_template_data);

$discount_offer = new \eBizIndia\DiscountOffer();	

if(filter_has_var(INPUT_POST,'mode') && $_POST['mode']=='get-offers'){
	$result=[0,[]]; // error code and list html
	$error=0; // no error
	$today=date('Y-m-d');
	$tot_rec_options = $options = [
		'fields_to_fetch' => ['record_count'],
		'filters' => [
			['field' => 'valid_upto', 'type' => 'ON_OR_AFTER', 'value' => [$today]],
			['field' => 'display_period', 'value'=>[$today, $today]],
			['field' => 'active', 'value'=>'y'],
			['field' => 'category_active', 'value'=>'y'],
		]
	];

	$pno=(isset($_POST['pno']) && $_POST['pno']!='' && is_numeric($_POST['pno']))?$_POST['pno']:((isset($_GET['pno']) && $_GET['pno']!='' && is_numeric($_GET['pno']))?$_GET['pno']:1);
	$recs_per_page=(isset($_POST['recs_per_page']) && $_POST['recs_per_page']!='' && is_numeric($_POST['recs_per_page']))?$_POST['recs_per_page']:((isset($_GET['recs_per_page']) && $_GET['recs_per_page']!='' && is_numeric($_GET['recs_per_page']))?$_GET['recs_per_page']:CONST_RECORDS_PER_PAGE);
	$recs_per_page=30;  // Should always be a multiple of the number of offers to be shown ina one row which as of now is two

	$filter_text = [];
	$category_id = '';
	if(filter_has_var(INPUT_POST, 'search_data') && $_POST['search_data']!=''){
		$search_data=json_decode($_POST['search_data'],true);
		if(!is_array($search_data)){
			$error=2; // invalid search parameters
		}else if(!empty($search_data)){
			
			foreach($search_data as $filter){
				$field=$filter['search_on'];
				if($field=='cid'){
					$field='category_id';
					$category_id = $filter['search_text'];
				}else if($field=='category_id'){
					$category_id = $filter['search_text'];
				}

				if(array_key_exists('search_type',$filter)){
					$type=$filter['search_type'];
				}else{
					$type='';
				}

				if(array_key_exists('search_text', $filter))
					$value= \eBizIndia\trim_deep($filter['search_text']);
				else
					$value='';

				$options['filters'][] = array('field'=>$field,'type'=>$type,'value'=>$value);

			}
		}
	}

	if($pno==1){
		// The tot record count will be category specific, other filters if implemented later will not be applied
		$tot_rec_options['filters'][] = array('field'=>'category_id','type'=>'EQUAL','value'=>$category_id);
		$result[1]['$tot_rec_options'] = $tot_rec_options;
		$tot_rec_cnt = \eBizIndia\DiscountOffer::getList($tot_rec_options);
		$result[1]['tot_rec_cnt'] = $tot_rec_cnt[0]['record_count'];

		// get the category details
		$doffcat_obj = new \eBizIndia\DiscountOfferCat($category_id);
		$doff_cat_details = $doffcat_obj->getDetails();
		if(!empty($doff_cat_details)){
			$result[1]['cat_name'] = $doff_cat_details[0]['name'];
			$result[1]['cat_active'] = $doff_cat_details[0]['active']; // If the requested category is found to be inactive then the user will be sent back to the categories page
		}
	}
	
	$record_count = \eBizIndia\DiscountOffer::getList($options);
	$record_count = $record_count[0]['record_count'];
	
	$options['fields_to_fetch']=['title', 'offer_url', 'mou', 'description', 'mob_img'];
	$options['page'] = $pno;
	$options['recs_per_page'] = $recs_per_page;
	$options['resource_only'] = true;
	$options['order_by'] = [['field' => 'title', 'type'=> 'ASC']];

	// $result[1]['options'] = $options;

	$res=\eBizIndia\DiscountOffer::getList($options);
	if($res===false){
		$error=1; // db error
	}

	$result[0]=$error;
	$result[1]['pno']=(int)$pno;
	$result[1]['rec_count']=$record_count;
	$result[1]['has_more_offers']=$record_count > $recs_per_page*$pno;
	$result[1]['records']=[];
	while($o_one = $res->fetch(\PDO::FETCH_ASSOC)){
		$o_one['valid_upto'] = date('d-M-Y', strtotime($o_one['valid_upto']));
		// if($o_one['dsk_img']!='')
		// 	$o_one['dsk_img_url']=CONST_DISCOUNT_OFFER_IMG_URL_PATH.$o_one['dsk_img'];
		if($o_one['mob_img']!='')
			$o_one['mob_img_url']=CONST_DISCOUNT_OFFER_IMG_URL_PATH.$o_one['mob_img'];
		if($o_one['mou']!='')
			$o_one['mou_url']=CONST_DISCOUNT_OFFER_MOU_URL_PATH.$o_one['mou'];
		
		unset($o_one['dsk_img'], $o_one['mob_img'], $o_one['mou']);
		$result[1]['records'][] = $o_one;
	}

	echo json_encode($result,JSON_HEX_TAG);
	exit;
}elseif(filter_has_var(INPUT_POST,'mode') && $_POST['mode']=='getCatList'){ // list of discount offer categories
	$result=array(0,array()); // error code and list html
	$options=[];
	$options['filters'] = [
		['field'=>'active', 'type'=>'EQUAL', 'value'=>'y'],
	];

	$pno=(isset($_POST['pno']) && $_POST['pno']!='' && is_numeric($_POST['pno']))?$_POST['pno']:((isset($_GET['pno']) && $_GET['pno']!='' && is_numeric($_GET['pno']))?$_GET['pno']:1);
	$recs_per_page=(isset($_POST['recs_per_page']) && $_POST['recs_per_page']!='' && is_numeric($_POST['recs_per_page']))?$_POST['recs_per_page']:((isset($_GET['recs_per_page']) && $_GET['recs_per_page']!='' && is_numeric($_GET['recs_per_page']))?$_GET['recs_per_page']:CONST_RECORDS_PER_PAGE);

	// $tot_rec_options = [
	// 	'fields_to_fetch'=>['record_count'],
	// 	'filters' => [
			
	// 	],
		
	// ];

	$options['fields_to_fetch'] = ['record_count'];

	// get total record count
	// $tot_rec_cnt = \eBizIndia\DiscountOfferCat::getList($tot_rec_options); 
	// $result[1]['tot_rec_cnt'] = $tot_rec_cnt[0]['record_count'];

	$recordcount = \eBizIndia\DiscountOfferCat::getList($options);
	$result[1]['tot_rec_cnt'] = $recordcount = $recordcount[0]['record_count'];
	$paginationdata=\eBizIndia\getPaginationData($recordcount,$recs_per_page,$pno,CONST_PAGE_LINKS_COUNT);
	$result[1]['paginationdata']=$paginationdata;


	if($recordcount>0){
		$noofrecords=$paginationdata['recs_per_page'];
		unset($options['fields_to_fetch']);
		$options['page'] = $pno;
		$options['recs_per_page'] = $noofrecords;
				
		if(isset($_POST['sort_data']) && $_POST['sort_data']!=''){
			$options['order_by']=[];
			$sortdata=json_decode($_POST['sort_data'],true);
			foreach($sortdata as $sort_param){

				$options['order_by'][]=array('field'=>$sort_param['sorton'],'type'=>$sort_param['sortorder']);

			}
		}

		$records=\eBizIndia\DiscountOfferCat::getList($options);
		if($records===false){
			$error=1; // db error
		}else{
			$result[1]['list']=$records;
		}
	}

	$result[0]=$error;
	$result[1]['rec_count']=$recordcount;

	// \eBizIndia\_p($result); exit;

	if($_POST['listformat']=='html'){

		$get_list_template_data=array();
		$get_list_template_data['mode']=$_POST['mode'];
		$get_list_template_data[$_POST['mode']]=array();
		$get_list_template_data[$_POST['mode']]['error']=$error;
		$get_list_template_data[$_POST['mode']]['records']=$records;
		$get_list_template_data[$_POST['mode']]['records_count']=count($records??[]);
		$get_list_template_data[$_POST['mode']]['cu_id']=$loggedindata[0]['id'];
		$get_list_template_data[$_POST['mode']]['filtertext']=$result[1]['filtertext'];
		$get_list_template_data[$_POST['mode']]['filtercount']=count($filtertext??[]);
		$get_list_template_data[$_POST['mode']]['tot_col_count']=count($records[0]??[])+1; // +1 for the action column

		$paginationdata['link_data']="";
		$paginationdata['page_link']='#';//"users.php#pno=<<page>>&sorton=".urlencode($options['order_by'][0]['field'])."&sortorder=".urlencode($options['order_by'][0]['type']);
		$get_list_template_data[$_POST['mode']]['pagination_html']=$page_renderer->fetchContent(CONST_THEMES_TEMPLATE_INCLUDE_PATH.'pagination-bar.tpl',$paginationdata);

		$get_list_template_data['logged_in_user']=$loggedindata[0];
		
		$page_renderer->updateBodyTemplateData($get_list_template_data);
		$result[1]['list']=$page_renderer->fetchContent();

	}

	echo json_encode($result,JSON_HEX_TAG);
	exit;

}

$additional_base_template_data = [
									'page_title' => $page_title,
									'page_description' => $page_description,
									'template_type'=>$template_type,
									'dom_ready_code'=>\scriptProviderFuncs\getDomReadyJsCode($page,$dom_ready_data),
									'other_js_code'=>$jscode,
									'module_name' => $page
								];


$page_renderer->updateBodyTemplateData($additional_body_template_data);
$page_renderer->updateBaseTemplateData($additional_base_template_data);
$page_renderer->addCss(\scriptProviderFuncs\getCss($page));
$js_files=\scriptProviderFuncs\getJavascripts($page);
$page_renderer->addJavascript($js_files['BSH'],'BEFORE_SLASH_HEAD');
$page_renderer->addJavascript($js_files['BSB'],'BEFORE_SLASH_BODY');
$page_renderer->renderPage();
