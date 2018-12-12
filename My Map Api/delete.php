<?php  
$myFile = "data.json";
// create empty array
$arr_data = array();
//Get data from existing json file
$jsondata = file_get_contents($myFile);
// converts json data into array
$arr_data = json_decode($jsondata, true);
//find value of each object in file json in 
foreach($arr_data as $elementKey => $element) {
	foreach($element as $valueKey => $value) {
		if( $value == $_POST["name"]){
		            //delete this particular object from the $arr_data
			unset($arr_data[$elementKey]);
			break;
			$check=true;
		}
	}
	if($check=false){
		break;
	}
}

//Convert updated array to JSON
$jsondata = json_encode($arr_data, JSON_PRETTY_PRINT);
//write json data into data.json file
file_put_contents($myFile, $jsondata)
?>