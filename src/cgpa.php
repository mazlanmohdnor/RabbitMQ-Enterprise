<?php


	$randomfloat = 1 + mt_rand() / mt_getrandmax() * (4 - 1);
	// if($round>0)
		$randomfloat = round($randomfloat,2);

	echo $randomfloat;
