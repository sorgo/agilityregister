#!/usr/bin/php -q
<?php

$G["dir"]="msr12";

$timy=array(
#S
7,2,21,1,4,5,6,3,
#M
11,8,9,12,13,10,
#L
18,15,17,14,19,20
);



$f=fopen($G["dir"]."/startovka.csv","r");
while ($l=fgetcsv($f,1000)) {
	if (substr($l[0],0,1)=="#") continue;
	$G["startovka"][$l[1]]=$l;
}

$G["timy"]=array();	
$f=fopen($G["dir"]."/timy.csv","r");
while ($l=fgetcsv($f,1000)) {
	if (substr($l[0],0,1)=="#") continue;
	$G["timy"][$l[0]]=array("name"=>$l[1]);
	#, "teams"=>array($l[2],$l[3],$l[4]));
	if ($l[2]>0) $G["timy"][$l[0]]["teams"][]=$l[2];
	if ($l[3]>0) $G["timy"][$l[0]]["teams"][]=$l[3];
	if ($l[4]>0) $G["timy"][$l[0]]["teams"][]=$l[4];
	if ($l[5]>0) $G["timy"][$l[0]]["teams"][]=$l[5];
}

$beh=$argv[1];

print 'nastavim startovku podla '.$argv[1]."\n";

$out="";

if ($timy) {
	foreach($timy as $t) {
		$outr="";
		foreach($G["timy"][$t]["teams"] as $t) {
			$outr.="\"".join("\",\"",$G["startovka"][$t])."\"\n";
		}
		$out=$outr.$out;
	}
	$f=fopen($G["dir"]."/startovka-".$argv[1]."-T.csv","w");
	fputs($f,$out);
} else {

	$f=fopen($G["dir"]."/".$argv[1].".csv","r");
	while ($l=fgetcsv($f,1000)) {
		$out="\"".join("\",\"",$G["startovka"][$l[0]])."\"\n".$out;
	}
	$f=fopen($G["dir"]."/startovka-".$argv[1].".csv","w");
	fputs($f,$out);
}


?>