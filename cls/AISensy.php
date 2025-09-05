<?php
namespace eBizIndia;
class AISensy{

	private string $api_key;
	private string $version='2';
	private string $api_url_base = 'https://backend.aisensy.com/';
	private string $content_type = 'application/json';
	private string $user_name = 'Arun';
	private string $override_recipient = '';
	public static $curl_data = [];

	public function __construct(string $api_key, string $version = ''){
		if($version != '')
			$this->version = $version;
		$this->api_key = $api_key;
	}

	public function setContentType(string $content_type=''){
		if($content_type == '')
			$this->content_type = 'application/json';
		else
			$this->content_type = $content_type;
	}

	public function resetOverrideRecipient(){
		$this->override_recipient='';
	}

	public function setOverrideRecipient($number){
		$this->override_recipient=$number;
	}
	
	public function sendCampaignMessage(string $campaign_name, string $recipient, array $template_params, array $media=[]){
		$headers = [
				'Content-Type: ' . $this->content_type,
			];
		if($this->override_recipient != '')
			$recipient = $this->override_recipient;
		$data = [
				    "apiKey" => $this->api_key,
    				"campaignName" => $campaign_name,
    				"destination" => $recipient,
    				"userName" => $this->user_name,
    				"templateParams" => $template_params
				];
		if(!empty($media)){
			$data['media'] = [
				'url' => $media['url'],
				'filename' => $media['filename'],
			];
		}		
		$data = json_encode($data);

		$response = $this->HTTPRequest(resource: $this->api_url_base. 'campaign/t1/api/v2', method: 'POST', headers: $headers, post_fields: $data);
		return $response;
	}

	public static function HTTPRequest(string $resource, string $method = 'GET', array|string $post_fields = [], array $headers = []){
		$options = [
            CURLOPT_CONNECTTIMEOUT => 15,
            CURLOPT_HEADER => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_URL => $resource,
            CURLOPT_ENCODING => 'gzip'
        ];
        $query_str_pairs = [];
		if(is_array($post_fields)){
			foreach($post_fields as $key=>$field){
				$query_str_pairs[] = $key . '=' . rawurlencode($field);
			}
		}

        switch ($method) {
            case 'GET':
                if(count($query_str_pairs) > 0)
                	$options[CURLOPT_URL] .= '?' . implode('&', $query_str_pairs);
                break;
			case 'POST':
                $options[CURLOPT_POST] = true;
                if(count($query_str_pairs) > 0)
                	$options[CURLOPT_POSTFIELDS] = implode('&', $query_str_pairs);
                else
                	$options[CURLOPT_POSTFIELDS] = $post_fields;
                break;
        }
        
		if(is_array($headers)){
			$headers = array_filter($headers);
			if(count($headers) > 0)
				$options[CURLOPT_HTTPHEADER] = $headers;
		}
        $curl_handle = curl_init();
        curl_setopt_array($curl_handle, $options);
        $response = curl_exec($curl_handle);
        self::$curl_data['error_no'] = curl_errno($curl_handle);
        
        self::$curl_data['http_code'] = curl_getinfo($curl_handle, CURLINFO_HTTP_CODE);

        $parts = explode("\r\n\r\n", $response);
        $response_body = array_pop($parts);
        $response_headers = [];
        foreach (explode("\r\n", array_pop($parts)) as $i => $line) {
            if (strpos($line, ':') !== false) {
                list ($key, $value) = explode(': ', $line);
                $key = str_replace('-', '_', strtolower($key));
                $response_headers[$key] = trim($value);
            }
        }
        self::$curl_data['response_headers'] = $response_headers;
        curl_close($curl_handle);

        return $response_body;
    }

    public static function getLastHTTPCode(){
    	return self::$curl_data['http_code'];
    }

    public static function getLastHTTPResponseHeaders(){
    	return self::$curl_data['response_headers'];
    }

}
