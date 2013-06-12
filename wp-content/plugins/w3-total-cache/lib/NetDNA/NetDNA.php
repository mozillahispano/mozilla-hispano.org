<?php

if (!defined('ABSPATH')) {
    die();
}

w3_require_once(W3TC_LIB_OAUTH_DIR . '/W3tcOAuth.php');

require_once("CurlException.php");

/** 
 * NetDNA REST Client Library
 * 
 * @copyright 2012
 * @author Karlo Espiritu
 * @version 1.0 2012-09-21
*/
class NetDNA {
	
	public $alias;

	public $key;

	public $secret;
	
	public $netdnarws_url = 'https://rws.netdna.com';
	
	
	public function __construct($alias, $key, $secret, $options=null) {
		$this->alias  = $alias;
		$this->key    = $key;
		$this->secret = $secret;
		$consumer = new W3tcOAuthConsumer($key, $secret, NULL);
		
	}

	private function execute($selected_call, $method_type, $params) {
		$consumer = new W3tcOAuthConsumer($this->key, $this->secret, NULL);

		// the endpoint for your request
		$endpoint = "$this->netdnarws_url/$this->alias$selected_call"; 
		
		//parse endpoint before creating OAuth request
		$parsed = parse_url($endpoint);
		if (array_key_exists("parsed", $parsed))
		{
		    parse_str($parsed['query'], $params);
		}

		//generate a request from your consumer
		$req_req = W3tcOAuthRequest::from_consumer_and_token($consumer, NULL, $method_type, $endpoint, $params);

		//sign your OAuth request using hmac_sha1
		$sig_method = new W3tcOAuthSignatureMethod_HMAC_SHA1();
		$req_req->sign_request($sig_method, $consumer, NULL);

		// create curl resource 
		$ch = curl_init(); 
		// set url 
		curl_setopt($ch, CURLOPT_URL, $req_req); 
		//return the transfer as a string
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER , FALSE);

		// set curl custom request type if not standard
		if ($method_type != "GET" && $method_type != "POST") {
		    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method_type);
		}


		if ($method_type == "POST" || $method_type == "PUT" || $method_type == "DELETE") {
		    $query_str = W3tcOAuthUtil::build_http_query($params);
		    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:', 'Content-Length: ' . strlen($query_str)));
		    curl_setopt($ch, CURLOPT_POSTFIELDS,  $query_str);
		}

		// retrieve headers
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLINFO_HEADER_OUT, 1);

		// make call
		$result = curl_exec($ch);
		$headers = curl_getinfo($ch);
		$curl_error = curl_error($ch);

		// close curl resource to free up system resources 
		curl_close($ch);

		// $json_output contains the output string 
		$json_output = substr($result, $headers['header_size']);

		// catch errors
		if(!empty($curl_error) || empty($json_output)) { 
			throw new CurlException("CURL ERROR: $curl_error, Output: $json_output", $headers['http_code'], null, $headers);
		}

		return $json_output;
	}
	
	public function get($selected_call, $params = array()){
		 
		return $this->execute($selected_call, 'GET', $params);
	}
	
	public function post($selected_call, $params = array()){
		return $this->execute($selected_call, 'POST', $params);
	}
	
	public function put($selected_call, $params = array()){
		return $this->execute($selected_call, 'PUT', $params);
	}
	
	public function delete($selected_call, $params = array()){
		return $this->execute($selected_call, 'DELETE', $params);
	}

    /**
     * Finds the zone id that matches the provided url.
     * @param $url
     * @return null
     */
    public function get_zone_id($url) {
        $zone_id = null;
        $pull_zones =  json_decode($this->get('/zones/pull.json'));

        if (preg_match("(200|201)", $pull_zones->code)) {
            foreach ($pull_zones->data->pullzones as $zone) {
                if (trim($zone->url, '/') != trim($url, '/'))
                    continue;
                else {
                    $zone_id = $zone->id;
                    break;
                }
            }
        } else
            return null;
        return $zone_id;
    }

    public function get_stats_per_zone($zone_id) {
        $api_stats = json_decode($this->get("/reports/{$zone_id}/stats.json"), true);
        if (preg_match("(200|201)", $api_stats['code'])) {
            $summary = $api_stats['data']['summary'];
            return $summary;
        } else
            return null;
    }

    public function get_list_of_file_types_per_zone($zone_id) {
        $api_list = json_decode($this->get("/reports/pull/{$zone_id}/filetypes.json"), true);
        if (preg_match("(200|201)", $api_list['code'])) {
            $stats['total'] = $api_list['data']['total'];

            foreach($api_list['data']['filetypes'] as $filetyp) {
                $stats['filetypes'][] = $filetyp;
            }
            $stats['summary'] = $api_list['data']['summary'];
            return $stats;
        } else
            return null;
    }

    public function get_list_of_popularfiles_per_zone($zone_id) {
        $api_popularfiles = json_decode($this->get("/reports/{$zone_id}/popularfiles.json"), true);
        if (preg_match("(200|201)", $api_popularfiles['code'])) {
            $popularfiles = $api_popularfiles['data']['popularfiles'];
            return $popularfiles;
        } else
            return null;
    }

    public function get_account() {
        $api_account = json_decode($this->get("/account.json"), true);
        if (preg_match("(200|201)", $api_account['code'])) {
            $account = $api_account['data']['account'];
            return $account;
        } else
            return null;
    }

    public function get_pull_zone($zone_id) {
        $api_pull_zone = json_decode($this->get("/zones/pull.json/{$zone_id}"), true);
        if (preg_match("(200|201)", $api_pull_zone['code'])) {
            $pull_zone = $api_pull_zone['data']['pullzone'];
            return $pull_zone;
        } else
            return null;
    }

    /**
     * @param $zone
     * @return mixed
     * @throws Exception
     */
    public function create_pull_zone($zone) {
        $zone_data = json_decode($this->post('/zones/pull.json', $zone), true);
        if (preg_match("(200|201)", $zone_data['code'])) {
            return $zone_data['data']['pullzone'];
        } else
            throw new Exception($zone_data['error']['message']);
    }

    public function get_zones_by_url($url) {
        $zone_id = null;
        $pull_zones =  json_decode($this->get('/zones/pull.json'), true);
        $zones = array();
        if (preg_match("(200|201)", $pull_zones['code'])) {
            foreach ($pull_zones ['data']['pullzones'] as $zone) {
                if (trim($zone['url'], '/') != trim($url, '/'))
                    continue;
                else {
                    $zones[] = $zone;
                }
            }
        } else
            return null;
        return $zones;
    }
}
