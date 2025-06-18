<?php

function x($data)
{
	echo '<pre>';
	print_r($data);
	die;
}

function y($data)
{
	echo '<pre>';
	print_r($data);
}

function getAmenitiesNames($value='')
{
	$amenities=array(
		'cardRequired'=>'Card Required',
		'breakfast'=>'Free breakfast',
		'privateBathroom'=>'Private Bathroom',
		'Free Cancellation'=>'Free Cancellation',
		'freeWifi'=>'Free Wifi',
		'refundable'=>'Refundable',
		'available'=>'Room Available',
		'balcony'=>'Balcony',
		'deposit'=>'Payment directly on the<br> site',
		'beds'=>'Bed',
	);
	if(!empty($value) && array_key_exists($value, $amenities))
	{
		return $amenities[$value];
	}
}


function fontArray($value='')
{
	$array=array(
		'cardRequired'=>'fa fa-credit-card',
		'breakfast'=>'fa fa-cutlery',
		'privateBathroom'=>'fa fa-shower',
		'Free Cancellation'=>'fa fa-scissors',
		'freeWifi'=>'fa fa-wifi',
		'refundable'=>'fa fa-angellist',
		'available'=>'fa fa-unlock-alt',
		'balcony'=>'fa fa-pagelines',
		'deposit'=>'fa fa-usd',
		'beds'=>'fa fa-hotel',
	);

	if(!empty($value) && array_key_exists($value, $array))
	{
		return $array[$value];
	}
}

//if deposite is key but values is not 1 then it must be *Reserve Now, Pay Later*	
?>