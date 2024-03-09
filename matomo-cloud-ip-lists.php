<?php
header("Content-type: text/html; charset=utf-8");
// header("Cache-Control: no-cache, no-store, must-revalidate");
//----------------------------------------------

error_reporting(E_ALL);
ini_set("display_errors", 1);

//----------------------------------------------

/*
amazon aws
ip-ranges.json
https://ip-ranges.amazonaws.com/ip-ranges.json

microsoft azure
ServiceTags_Public_20240227.json
https://www.microsoft.com/en-us/download/confirmation.aspx?id=56519

google cloud
cloud.json
https://www.gstatic.com/ipranges/cloud.json

oracle cloud
public_ip_ranges.json
https://docs.cloud.oracle.com/en-us/iaas/tools/public_ip_ranges.json

google digital ocean
google.csv
https://www.digitalocean.com/geo/google.csv

--------------------------------------------

selbst-gesammelte-ip-ranges.txt

*/
// -------------------------------------------------------------------------------------------

function csv_to_array($csv_file_path) {
	$csv_array = array();

	// Read the CSV and put the rows in a array - BEGIN
	$fopen_path_csv = $csv_file_path;

	if (file_exists($fopen_path_csv)) {
		if (($fopen_stream_csv = fopen($fopen_path_csv, "r")) !== false) {
			
			// fgetcsv($fopen_stream_csv, 0, ";"); // leap the first row in the CSV table.
			
			// array_shift($csv_columns); // delete the first element.
			
			$csv_array_sub = array();
			while (($csv_row = fgetcsv($fopen_stream_csv, 0, ",")) !== false) {
				
				// array_shift($csv_row); // delete the first element.
				
				if ($csv_row !== array(null)) { // ignore empty lines
					
					for ($i = 0; $i < count($csv_row); $i++) {
						
						$csv_array_sub[$i] = trim($csv_row[$i]);
					}
					array_push($csv_array, $csv_array_sub);
				}
			}
		}
		fclose($fopen_stream_csv);
	}
	return $csv_array;
}

// -------------------------------------------------------------------------------------------

// JSON
$amazon_list_raw_file_path = __DIR__ . "/cloud-ip-lists/" . "ip-ranges.json";
$microsoft_list_raw_file_path = __DIR__ . "/cloud-ip-lists/" . "ServiceTags_Public_20240227.json";
$google_cloud_list_raw_file_path = __DIR__ . "/cloud-ip-lists/" . "cloud.json";
$oracle_cloud_list_raw_file_path = __DIR__ . "/cloud-ip-lists/" . "public_ip_ranges.json";

$amazon_list_raw_content = file_get_contents($amazon_list_raw_file_path);
$microsoft_list_raw_content = file_get_contents($microsoft_list_raw_file_path);
$google_cloud_list_raw_content = file_get_contents($google_cloud_list_raw_file_path);
$oracle_cloud_list_raw_content = file_get_contents($oracle_cloud_list_raw_file_path);

$amazon_list_json = json_decode($amazon_list_raw_content, true);
$microsoft_list_json = json_decode($microsoft_list_raw_content, true);
$google_cloud_list_json = json_decode($google_cloud_list_raw_content, true);
$oracle_cloud_list_json = json_decode($oracle_cloud_list_raw_content, true);

$ip_list_all_array = Array();

$amazon_list_array = Array();
for ($i = 0; $i < count($amazon_list_json["prefixes"]); $i++) {
	$ip = $amazon_list_json["prefixes"][$i]["ip_prefix"];
	array_push($amazon_list_array, $ip);
	array_push($ip_list_all_array, $ip);
}
for ($i = 0; $i < count($amazon_list_json["ipv6_prefixes"]); $i++) {
	$ip = $amazon_list_json["ipv6_prefixes"][$i]["ipv6_prefix"];
	array_push($amazon_list_array, $ip);
	array_push($ip_list_all_array, $ip);
}

$microsoft_list_array = Array();
for ($i = 0; $i < count($microsoft_list_json["values"]); $i++) {
	for ($ii = 0; $ii < count($microsoft_list_json["values"][$i]["properties"]["addressPrefixes"]); $ii++) {
		$ip = $microsoft_list_json["values"][$i]["properties"]["addressPrefixes"][$ii];
		array_push($microsoft_list_array, $ip);
		array_push($ip_list_all_array, $ip);
	}
}

$google_cloud_list_array = Array();
for ($i = 0; $i < count($google_cloud_list_json["prefixes"]); $i++) {
	if (array_key_exists("ipv4Prefix", $google_cloud_list_json["prefixes"][$i]) === true) {
		$ip = $google_cloud_list_json["prefixes"][$i]["ipv4Prefix"];
		array_push($google_cloud_list_array, $ip);
		array_push($ip_list_all_array, $ip);
	}
	if (array_key_exists("ipv6Prefix", $google_cloud_list_json["prefixes"][$i]) === true) {
		$ip = $google_cloud_list_json["prefixes"][$i]["ipv6Prefix"];
		array_push($google_cloud_list_array, $ip);
		array_push($ip_list_all_array, $ip);
	}
}

$oracle_cloud_list_array = Array();
for ($i = 0; $i < count($oracle_cloud_list_json["regions"]); $i++) {
	for ($ii = 0; $ii < count($oracle_cloud_list_json["regions"][$i]["cidrs"]); $ii++) {
		$ip = $oracle_cloud_list_json["regions"][$i]["cidrs"][$ii]["cidr"];
		array_push($oracle_cloud_list_array, $ip);
		array_push($ip_list_all_array, $ip);
	}
}

// -------------------------------------------------------------------------------------------

// CSV
$google_list_raw_file_path = __DIR__ . "/cloud-ip-lists/" . "google.csv";

$google_list_full_array = csv_to_array($google_list_raw_file_path);

$google_list_array = Array();
for ($i = 0; $i < count($google_list_full_array); $i++) {
	$ip = $google_list_full_array[$i][0];
	array_push($google_list_array, $ip);
	array_push($ip_list_all_array, $ip);
}

// -------------------------------------------------------------------------------------------

// Own
$own_list_raw_file_path = __DIR__ . "/" . "selbst-gesammelte-ip-ranges.txt";

// $own_list_raw_content = file_get_contents($own_list_raw_file_path);
$own_list_full_array = csv_to_array($own_list_raw_file_path);

$own_list_array = Array();
for ($i = 0; $i < count($own_list_full_array); $i++) {
	$ip = $own_list_full_array[$i][0];
	array_push($own_list_array, $ip);
	array_push($ip_list_all_array, $ip);
}

// -------------------------------------------------------------------------------------------

// Filter doubles
$ip_list_array = Array();
$concat = "";
for ($i = 0; $i < count($ip_list_all_array); $i++) {
	if (in_array($ip_list_all_array[$i], $ip_list_array) === false) {
		$ip = $ip_list_all_array[$i];
		array_push($ip_list_array, $ip);
		$concat .= $ip;
		if ($i < (count($ip_list_all_array) - 1)) {
			$concat .= PHP_EOL;
		}
	}
}

// var_dump($ip_list_array);
echo $concat;

?>
