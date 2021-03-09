<?php


$arr = [1,3,5,4,5,7];


function makeArr($arr){

	$newAr = [];

	foreach ($arr as $key => $value) {

		if(count($arr) >=3){

			array_push($newAr, call_user_func_array('func', array_slice($arr, 0, 3) ) );

			unset($arr[$key]);
		}	

	}

	return $newAr;
	
}


function func($a, $b, $c){

	return ($a > $b && $b < $c) || ($a < $b && $b >$c) ? 1 : 0;
		
}

var_dump(makeArr($arr));

echo "<hr>";
/*
*************************************************
*/

$arr2 = [

	[1,2,3,2,7],
	[4,5,6,8,1],
	[7,8,9,4,5]
];

$preg = [1,2,3,4,5,6,7,8,9];
$size = 3;

$temp = [];
$res = [];

$count = count($arr2[0]);


while(count($arr2[0]) >= $size) { 

	foreach ($arr2 as  $key => $value) {

		$res = array_merge($res, array_slice($value, 0, $size));

		unset($arr2[$key][0]);

		$arr2[$key] = array_values($arr2[$key]);
	}

	array_push($temp, searchNum($res, $preg));

	$res=[];

}

function searchNum($arr, $pregArr) {

	foreach ($pregArr as $value) {
		
		if(!in_array($value, $arr)) return false;
	}
	return true;
}

var_dump($temp);

echo "<hr>";
/*
*************************************************
*/


$strArr = [

	['Hello', 'world'],
	['Brad', 'come', 'to', 'dinner', 'with', 'us'],
	['He', 'loves', 'takos']

];

$rules = ['LEFT', 'RIGHT', 'LEFT'];

$limit = 16;

foreach ($strArr as $key => $value) {

	$tempStr = implode($value,' ');

	$strLen = iconv_strlen($tempStr);


	if(is_limit($strLen, $limit)){

		render($rules[$key], $tempStr);
		
	}else { 

		$e = toPart($tempStr, $limit);

		foreach ($e as $value) {

			render($rules[$key], $value);

		}
	}
	
}


function is_limit($lenStr, $limit) {

	return $lenStr <= $limit ;
}


function toPart($str, $limit) {

	for ($i=$limit--; $i <= iconv_strlen($str); $i+=$limit--) { 

		$str = substr_replace($str, "|", $i, 0);

	}

	$arr = explode("|", $str);
	return $arr;
}


function render($rules, $str){

	if($rules == 'LEFT'){

			echo "*{$str} *<br>";
		}else{

			echo "* {$str}*<br>";
		}

}