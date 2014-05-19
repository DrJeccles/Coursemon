<?php
	
	//update specific row when first inserted
	function update($mysqli, $crn, $sem)
	{
		//get seats for this class
		if($sem == 0)
		{
			$res = get_seats("https://oscar.gatech.edu/pls/bprod/bwckschd.p_disp_detail_sched?term_in=201408&crn_in=$crn");
		}
		else if($sem == 1)
		{
			$res = get_seats("https://oscar.gatech.edu/pls/bprod/bwckschd.p_disp_detail_sched?term_in=201405&crn_in=$crn");
		}
					//update table with seats remaining
		//print("updating table\n");
		$query = "UPDATE course SET cap = '$res->cap', act='$res->act', rem = '$res->rem' WHERE crn = '$crn'";
		$mysqli->query($query);
	}
	
	function get_seats($url){

		if( !($data = file_get_contents($url)) ) return false;
		//find first occurance of the word seats
		$sub = 0; //characters to subtract b/c of single digit results
		$spos = strpos($data, "Seats");
		$rem = substr($data, $spos + 18 + 22, 3);
		//test to see if it's only two digits
		if(!is_numeric($rem))
		{$rem = substr($rem, 0, 2);$sub++;}
		//test to see if it's only one digit
		if(!is_numeric($rem))
		{$rem = substr($rem, 0, 1);$sub++;}
		$res->cap = strval($rem);
		//print("Capacity: $res->cap\n");
		$rem = substr($data, $spos + 18 + 31 + 22 - $sub, 3);
		//test to see if it's only two digits
		if(!is_numeric($rem))
		{$rem = substr($rem, 0, 2);$sub++;}
		//test to see if it's only one digit
		if(!is_numeric($rem))
		{$rem = substr($rem, 0, 1);$sub++;}
		$res->act = strval($rem);
		//print("Actual: $res->act\n");
		$rem = substr($data, $spos + 18 + 31 + 31 + 22 - $sub, 3);
		//test to see if it's only two digits
		if(!is_numeric($rem))
		{$rem = substr($rem, 0, 2);$sub++;}
		//test to see if it's only one digit
		if(!is_numeric($rem))
		{$rem = substr($rem, 0, 1);$sub++;}
		$res->rem = strval($rem);
		//print("Remaining: $res->rem\n");
		return $res;
	}
?>