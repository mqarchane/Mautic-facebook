<?php

// facebook variables 
$challenge = isset($_REQUEST['hub_challenge']) ? $_REQUEST['hub_challenge'] : '';
$verify_token = isset($_REQUEST['hub_verify_token']) ? $_REQUEST['hub_verify_token'] : ''; 

$fb_access_token = ""; // you have to subscribe to the page that has the form to generate an Access Token

// this is used to subscribe to facebook webhook
if ($verify_token === 'abc133') { // 
  echo $challenge;
}

//this is for testing
//$string = '{"entry":[{"changes":[{"field":"leadgen","value":{"ad_id":0,"form_id":71742323768434126,"leadgen_id":7175053384232331969,"created_time":1491871976,"page_id":2100872676805012,"adgroup_id":0}}],"id":"2100872676805012","time":1491871977}],"object":"page"}	';
//$data = json_decode($string, true);

// Process retrieved data from facebook webhook
$data = json_decode(file_get_contents("php://input"),true);
$leadgen_id = $data['entry'][0]['changes'][0]['value']['leadgen_id']; // extract leadgen ID

// check leadgen ID before calling Facebook API
if($leadgen_id){
  $ch = curl_init();
  $url = "https://graph.facebook.com/v2.8/".$leadgen_id;
  $url_query = "access_token=".$fb_access_token; 
  $url_final = $url.'?'.$url_query;
  curl_setopt($ch, CURLOPT_URL, $url_final);
  curl_setopt($ch, CURLOPT_HTTPGET, 1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  $response = curl_exec($ch);
  //if (curl_errno($ch)) { 
  //    print curl_error($ch); 
  //} 
  curl_close ($ch);
  $data = json_decode($response, true);
  //var_dump($data);
  $lead_first = $data['field_data'][0][values][0];
  $lead_last = $data['field_data'][1][values][0];
  $lead_email = $data['field_data'][2][values][0];

} 

// check if we have a valid lead before calling Mautic Api
if($lead_email){

  //Create a new Contact
  $data =array(); 
  $data['first_name'] = $lead_first;
  $data['last_name'] = $lead_last;
  $data['email'] = $lead_email;
  $data['submit'] = "";
  $data['formId'] = 4;
  $formId = $data['formId'];
	
  $data = array('mauticform' => $data);
  
  // Change [path-to-mautic] to URL where your Mautic is
  $formUrl =  '[path-to-mautic]/form/submit?formId=' . $formId;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $formUrl);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $response = curl_exec($ch);
  //if (curl_errno($ch)) { 
  //    print curl_error($ch); 
  //} 
  curl_close($ch);
  return $response;

  echo "Contact Added!";
  //print_r($response); 

} // end valid lead check

?>
