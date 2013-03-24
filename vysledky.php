#!/usr/bin/php -q
<?php
#require('fpdf/fpdf.php');
require('fpdf/fpdf.php');

$G["teamcount"]=4;
$G["teamcountresults"]=3;

error_reporting(E_ALL-E_NOTICE);

include("konfiguracia.php");
include($G["dir"].".php");

$G["nadpis"]=$G["nazov"];

if ($argv[1]=="fontimport") {

require('fpdf/font/makefont/makefont.php');
require('fpdf\\font\\makefont\\makefont.php');
$d = dir(".");
while (false !== ($entry = $d->read())) {
   #echo $entry."\n";
	if (strtolower(substr($entry,-4))==".pfb"
	|| strtolower(substr($entry,-4))==".ttf") {
		$cmd="ttf2pt1 ".$entry." ".substr($entry,0,-4);
		echo $cmd."\n";
		exec($cmd);
		MakeFont($entry, substr($entry,0,-4).'.afm', 'cp1250');
	}
}
$d->close();
exit;
}

class aPDF extends FPDF
{

	var $nadpis; # nadpis PDF suboru
	var $cislastran;
	
	//Page header
	function Header()
	{
	
	

if ($this->noheader) {
} else {	

	
    	//Logo
   	    if ($this->PageNo()==1) {
					#$this->Image("logo_all.jpg",$this->CurPageFormat[$this->CurOrientation=="P" ? 0 : 1]-40,$this->y,320/10*.8,278/10*.8);
					#$this->Image("mc12/mc12.jpg",$this->CurPageFormat[$this->CurOrientation=="P" ? 0 : 1]-40,$this->y,164/10*1.4,200/10*1.4);
   	    			$this->Image("js13.jpg",$this->CurPageFormat[$this->CurOrientation=="P" ? 0 : 1]-60,$this->y,600/10*0.75,384/10*0.75);
					#$this->Image("eukanuba.jpg",$this->CurPageFormat[$this->CurOrientation=="P" ? 0 : 1]-75,$this->y,60,22);

					if ($argv[1]!="ZAPIS") {
#						$this->Image("rc.jpg",$this->CurPageFormat[$this->CurOrientation=="P" ? 0 : 1]-45,$this->CurPageFormat[$this->CurOrientation=="P" ? 1 : 0]-18,35,10);
#						$this->Image("phc.jpg",10,$this->CurPageFormat[$this->CurOrientation=="P" ? 1 : 0]-18,35,10);
					}
				} else {
					if ($argv[1]!="ZAPIS") {
						#$this->Image("logo_all.jpg",10,$this->CurPageFormat[$this->CurOrientation=="P" ? 1 : 0]-18,320/30,278/30);
						#$this->Image("mc12/mc12.jpg",10,$this->CurPageFormat[$this->CurOrientation=="P" ? 1 : 0]-40,$this->y,706/10*.4,599/10*.4);

#						$this->Image("rc.jpg",$this->CurPageFormat[$this->CurOrientation=="P" ? 0 : 1]-45,5,35,10);
#						$this->Image("phc.jpg",$this->CurPageFormat[$this->CurOrientation=="P" ? 0 : 1]-85,6,35,10);
					}
				}
    	//Arial bold 15
    	$this->SetFont('SkodaSans','B',12);
    	//Move to the right
    	//$this->Cell(80);
    	//Title
    	$this->Cell(10,10,iconv("utf-8","windows-1250",$this->nadpis));
		#var_export($this->CurOrientation);
		#exit;
				#tieto vsade

    	//Line break
    	$this->Ln(10);
}
	}
	
	//Page footer
	function Footer()
	{
if ($this->noheader) {
} else {	    	//Position at 1.5 cm from bottom
    	$this->SetY(-15);
    	//Arial italic 8
    	$this->SetFont('SkodaSans','I',8);
    	//Page number
   	$this->Cell(0,10,($this->garant!="" ? "Garant: ".iconv("utf-8","windows-1250",$this->garant)." " : "")."Spracovanï¿½: ".date("d.m.Y H:i").' Strana '.$this->PageNo().'/{nb}',0,0,'C');
}
	}

function BasicTable($header,$data)
{
	 $w=array(7,7,8,40,40,40,40,7,7,10,10,10,10,10,10,12);
	 $c=array("C","C","C","L","L","L","L","C","C","R","R","R","R","C","C","C");
    //Header
    $wi=0;

    $this->SetFillColor(240,240,240);
    $this->SetTextColor(0);
    $this->SetDrawColor(64,64,64);
    $this->SetLineWidth(.3);
    $this->SetFont('','B');
    foreach($header as $col) {
        $this->Cell($w[$wi],7,$col,1,0,$c[$wi]);
        $wi++;
    }
    $this->Ln();
    //Data
	 $this->SetFillColor(240,240,240);
    $this->SetTextColor(0);
    $this->SetDrawColor(64,64,64);
    $this->SetLineWidth(.3);
    $this->SetFont('','');
    $ri=0;
    foreach($data as $row)
    {
    	  $wi=0; 	    
        foreach($row as $col) {
            $this->Cell($w[$wi],5,iconv("utf-8","windows-1250",$col),1,0,$c[$wi],($ri%2==0));
            $wi++;
        }
        $this->Ln();
        $ri++;
    }
}

function TeamBasicTable($header,$data)
{
	global $G;
	
	 $w=array(7,7,8,40,40,40,40,7,7,10,10,10,10,10,10,12);
	 $c=array("C","C","C","L","L","L","L","C","C","R","R","R","R","C","C","C");
    //Header
    $wi=0;

    $this->SetFillColor(240,240,240);
    $this->SetTextColor(0);
    $this->SetDrawColor(64,64,64);
    $this->SetLineWidth(.3);
    $this->SetFont('','B');
    foreach($header as $col) {
        $this->Cell($w[$wi],7,$col,1,0,$c[$wi]);
        $wi++;
    }
    $this->Ln();
    //Data
	 $this->SetFillColor(240,240,240);
    $this->SetTextColor(0);
    $this->SetDrawColor(64,64,64);
    $this->SetLineWidth(.3);
    $this->SetFont('','');
    $ri=0;
    foreach($data as $row)
    {
    	  $wi=0;
    	  if ($ri % ($G["teamcount"]+1) == $G["teamcount"]) {
    	  	#$this->SetLineWidth(.6);
    	  	$this->SetFont('','B');
    	  } else {
    	  	#$this->SetLineWidth(.3);
    	  	$this->SetFont('','');
    	  }
        foreach($row as $col) {
            $this->Cell($w[$wi],5,iconv("utf-8","windows-1250",$col),1,0,$c[$wi],($ri%2==1));
            $wi++;
        }
        $this->Ln();
        $ri++;
    }
}


function VZTable($header,$data)
{

/* namerane vo VZ

05mm prazdne

12mm datum
40mm miesto/organizator
12mm beh
15mm TB na trati
16mm TB cas

10mm prazdne

15mm TB spolu
15mm hodnotenie
15mm poradie
25mm rozhodca
25mm podpis


vyska: 15mm

*/ 
	

	 $w=array(12,40,12,15,16,10,15,15,15,25,25);
	 $c=array("C","C","C","C","C","C","C","C","C","C","C");
	 $v=array(7,8,11,11,11,6,11,12,12,7,7);
	 $f=array("","","B","","","","B","B","B","","");
    //Header
    $wi=0;

    //Data
	 $this->SetFillColor(240,240,240);
    $this->SetTextColor(0);
    $this->SetDrawColor(240,240,240);
    #$this->SetDrawColor(255,255,255);
    $this->SetLineWidth(.3);
    $ri=0;
    
#    $vr=12.26; #real 12,19
    $vr=12.26*257/256;
    #$vr*=245/237;
    foreach($data as $row)
    {
    	  $wi=0; 	    
        foreach($row as $col) {
        		if ($wi==11) continue;
            $this->SetFont('',$f[$wi],$v[$wi]);
            if ($wi==1) {
					$ox=$x=$this->x;
					$oy=$y=$this->y;
					#$this->Image("MKATA3PP.jpg",$this->x+2,$this->y+2,496/50*.6,551/50*.6);
					$y+=2;
					$this->SetXY($x,$y);	            
	            foreach(explode("\n",$col) as $riadok) {
	            	$this->Cell($w[$wi],3,iconv("utf-8","windows-1250",$riadok),0,0,$c[$wi]);
	            	$y+=4;
		            $this->SetXY($x,$y);
					}
	            $this->SetFont('',$f[$wi],$v[$wi]);
	            $this->SetFont('','',4);
            	$this->Cell($w[$wi],2,iconv("utf-8","windows-1250",$row[11]),0,0,"C");
            	unset($row[11]);
	            $this->SetXY($ox,$oy);
	            $this->SetFont("","",6);
	            $this->Cell($w[$wi],$vr,"",$wi!=5,0,$c[$wi]);
	            
					#echo $col;
               #echo $this->x;
            } elseif ($wi==10) {
            	$this->Cell($w[$wi],$vr,"",$wi!=5,0,$c[$wi]);
#				$this->Image("podpis_vakonic.jpg",$this->x-25,$this->y+.5,16.4,11.4);
            } else {
            	$this->Cell($w[$wi],$vr,iconv("utf-8","windows-1250",$col),$wi!=5,0,$c[$wi]);
			}
            $wi++;
        }
        $this->Ln();
        $ri++;
    }
}


function StartTable($header,$data)
{
	 $w=array(7,7,40,35,45,40,7);
	 $c=array("C","C","L","L","L","L","L","C","C","R","R","R","C","C");
    //Header
    $wi=0;

    $this->SetFillColor(240,240,240);
    $this->SetTextColor(0);
    $this->SetDrawColor(64,64,64);
    $this->SetLineWidth(.3);
    $this->SetFont('','B');
    foreach($header as $col) {
        $this->Cell($w[$wi],7,$col,1,0,$c[$wi]);
        $wi++;
    }
    $this->Ln();
    //Data
	 $this->SetFillColor(240,240,240);
    $this->SetTextColor(0);
    $this->SetDrawColor(64,64,64);
    $this->SetLineWidth(.3);
    $this->SetFont('','');
    $ri=0;
    foreach($data as $row)
    {
    	  $wi=0; 	    
    	  if ($row[1]=="-") {
    	  	$this->SetFont('','B');
    	  } else {
    	  	$this->SetFont('','');
    	  }
    	  
        foreach($row as $col) {
            $this->Cell($w[$wi],5,iconv("utf-8","windows-1250",$col),1,0,$c[$wi],($ri%2==0));
            $wi++;
        }
        $this->Ln();
        $ri++;
    }
}

function PrezentTable($header,$data)
{
	 $w=array(7,7,35,30,55,10,10,10,30);
	 $c=array("C","C","L","L","L","L","L","C","C","R","R","R","C");
    //Header
    $wi=0;

    $this->SetFillColor(240,240,240);
    $this->SetTextColor(0);
    $this->SetDrawColor(64,64,64);
    $this->SetLineWidth(.3);
    $this->SetFont('','B',7);
    foreach($header as $col) {
        $this->Cell($w[$wi],7,$col,1,0,$c[$wi]);
        $wi++;
    }
    $this->Ln();
    //Data
	 $this->SetFillColor(240,240,240);
    $this->SetTextColor(0);
    $this->SetDrawColor(64,64,64);
    $this->SetLineWidth(.3);
    $this->SetFont('','',7);
    $ri=0;
    foreach($data as $row)
    {
    	  $wi=0; 	    
        foreach($row as $col) {
            $this->Cell($w[$wi],5,iconv("utf-8","windows-1250",$col),1,0,$c[$wi],($ri%2==0));
            $wi++;
        }
        $this->Ln();
        $ri++;
    }
}

function ZapisTable($header,$data)
{
	 $w=array(10,15,45,65,50,50,40);
	 $c=array("C","C","L","L","L","L","L","C","C","R","R","R","C");
    //Header
    $wi=0;

    $this->SetFillColor(240,240,240);
    $this->SetTextColor(0);
    $this->SetDrawColor(64,64,64);
    $this->SetLineWidth(.3);
    $this->SetFont('','B');
    foreach($header as $col) {
        $this->Cell($w[$wi],7,$col,1,0,$c[$wi]);
        $wi++;
    }
    $this->Ln();
    //Data
	 $this->SetFillColor(240,240,240);
    $this->SetTextColor(0);
    $this->SetDrawColor(64,64,64);
    $this->SetLineWidth(.3);
    $this->SetFont('','',9);
    $ri=0;
    foreach($data as $row)
    {
    	  $wi=0; 	    
        foreach($row as $col) {
        if ($wi==0) $this->SetFont('','B',12);
        if ($wi==1) $this->SetFont('','',9);
        if ($wi==1) $this->SetFont('','B',10);
        if ($wi==2) $this->SetFont('','',9);
            $this->Cell($w[$wi],10,iconv("utf-8","windows-1250",$col),1,0,$c[$wi],($ri%2==0));
            $wi++;
        }
        $this->Ln();
        $ri++;
    }
}


function SuctyTable($header,$data) {
	global $G,$pdf;
	
	 $w=array(7,7,7,35,40,35,40,10,10,10,10,10,10,10,10,10,10,10,10);
	 $c=array("C","C","C","L","L","L","L","R","R","R","R","R","R","R","R","R","R");
    //Header
    $wi=0;

    $this->SetFillColor(240,240,240);
    $this->SetTextColor(0);
    $this->SetDrawColor(64,64,64);
    $this->SetLineWidth(.3);
    $this->SetFont('','');
    $wi=0;
    foreach($header as $col) {
    	if ($wi>3) {
    		$this->setFont('','',6);
    	} else {
    		$this->setFont('','',8);
    	}    	
        	if ($wi>count($row)-3) {
        		$pdf->setFont('','B');
        	} else {
        		$pdf->setFont('','');
        	}
        $this->Cell($w[$wi],7,$col,1,0,$c[$wi]);
        $wi++;
    }
 	$this->setFont('','',8);
    $this->Ln();
    //Data
	 $this->SetFillColor(240,240,240);
    $this->SetTextColor(0);
    $this->SetDrawColor(64,64,64);
    $this->SetLineWidth(.3);
    $this->SetFont('','');
    $ri=0;
    foreach($data as $row)
    {
    	  $wi=0; 	    
				$this->setFont('','',8);
        foreach($row as $col) {
        	if ($wi>6) {
        		if (strlen($col)>6) {
 							$this->setFont('','',6);
        		} elseif (strlen($col)>5) {
 							$this->setFont('','',7);
        		} else {
						 	$this->setFont('','',8);
        		}
        	}
        	if ($wi>count($row)-3 || ($wi>2 && $wi<4)) {
        		$pdf->setFont('','B');
        	} else {
        		$pdf->setFont('','');
        	}
            $this->Cell($w[$wi],5,iconv("utf-8","windows-1250",$col),1,0,$c[$wi],($ri%2==0));
            $wi++;
        }
        $this->Ln();
        $ri++;
    }
}


function SuctyTeamTable($header,$data) {
	global $G,$pdf;
	
	 $w=array(7,7,7,35,40,35,40,10,10,10,10,10,10,10,10,10,10,10,10);
	 $c=array("C","C","C","L","L","L","L","R","R","R","R","R","R","R","R","R","R");
    //Header
    $wi=0;

    $this->SetFillColor(240,240,240);
    $this->SetTextColor(0);
    $this->SetDrawColor(64,64,64);
    $this->SetLineWidth(.3);
    $this->SetFont('','');
    $wi=0;
    foreach($header as $col) {
    	if ($wi>3) {
    		$this->setFont('','',6);
    	} else {
    		$this->setFont('','',8);
    	}    	
        	if ($wi>count($row)-3) {
        		$pdf->setFont('','B');
        	} else {
        		$pdf->setFont('','');
        	}
        $this->Cell($w[$wi],7,$col,1,0,$c[$wi]);
        $wi++;
    }
 	$this->setFont('','',8);
    $this->Ln();
    //Data
	 $this->SetFillColor(240,240,240);
    $this->SetTextColor(0);
    $this->SetDrawColor(64,64,64);
    $this->SetLineWidth(.3);
    $this->SetFont('','');
    $ri=0;
    foreach($data as $row)
    {
    	  $wi=0; 	    
				$this->setFont('','',8);
        foreach($row as $col) {
        	if ($wi>6) {
        		if (strlen($col)>6) {
 							$this->setFont('','',6);
        		} elseif (strlen($col)>5) {
 							$this->setFont('','',7);
        		} else {
						 	$this->setFont('','',8);
        		}
        		if (substr($col,0,2)=="XX") {
					$col=substr($col,2);
 							$this->setFont('','',5);
        		} else {
						 	#$this->setFont('','',8);
        		}
        	}
        	if ($wi>count($row)-3 || ($wi>2 && $wi<4)) {
        		#$pdf->setFont('','B');
        	} else {
        		#$pdf->setFont('','');
        	}
        	
    	  if ($ri % ($G["teamcount"]+1) == $G["teamcount"]) {
    	  	#$this->SetLineWidth(.6);
    	  	$this->SetFont('','B');
    	  } else {
    	  	$this->SetFont('','');
    	  }
            $this->Cell($w[$wi],5,iconv("utf-8","windows-1250",$col),1,0,$c[$wi],($ri%2==1));
            $wi++;
        }
        $this->Ln();
        $ri++;
    }
}

}



#$G["subor"]=join("_",$behy);


$f=fopen($G["dir"]."/startovka.csv","r");
while ($l=fgetcsv($f,1000)) {
	if (substr($l[0],0,1)=="#") continue;
	$G["startovka"][$l[1]]=$l;
	#var_export($l);exit;
	if ($l[7]=="") 
	$G["startovka_k"][$l[1]]=$l;
}
#var_export($G["startovka_k"]);exit;
$f=fopen($G["dir"]."/behy.csv","r");
while ($l=fgetcsv($f,1000)) {
	if (substr($l[0],0,1)=="#") continue;
	$G["behy"][$l[0]]=$l;
}

foreach($G["behy"] as $k => $v) {
	$f=@fopen($G["dir"]."/".$k.".csv","r");
	if($f)
	while ($l=fgetcsv($f,1000)) {
		#if (substr($l[0],0,1)=="#") continue;
		$G["vysledky"][$k][$l[0]]=$l;
	}
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
#print_r($G["timy"]);




/*
exportsucet();
*/

function zrobsucet($behy,$nadpis="Sï¿½ï¿½et behov") {
	global $pdf,$G;

	$filter="";
	$body=false;
	$teamy=false;

	if ($behy[count($behy)-1]=="PP") {
		$filter="PP";
		unset($behy[count($behy)-1]);
	}

	if ($behy[count($behy)-1]=="V") {
		$filter="V";
		unset($behy[count($behy)-1]);
	}

	if ($behy[count($behy)-1]=="BEZV") {
		$filter="BEZV";
		unset($behy[count($behy)-1]);
	}
	
	if ($behy[count($behy)-1]=="B") {
		$body=true;
		unset($behy[count($behy)-1]);
	}

	

	$hlavicka=array("por","ï¿½.","kat.","meno psovoda","meno psa","klub","plemeno");


	if ($body) {
		foreach($behy as $beh) {
			$hlavicka[]=$beh." B";
			$hlavicka[]=$beh." TB";
			foreach($G["vysledky"][$beh] as $v) {
				$rataj[$v[0]]=$v[0];
			}
		}
		$hlavicka[]="Body";
		$hlavicka[]="TB";
	} else {
		foreach($behy as $beh) {
			$hlavicka[]=$beh." ï¿½as";
			$hlavicka[]=$beh." TB";
			foreach($G["vysledky"][$beh] as $v) {
				$rataj[$v[0]]=$v[0];
			}
		}
	
		$hlavicka[]="ï¿½as";
		$hlavicka[]="TB";
	}

	foreach($behy as $beh) {

		$par=$G["behy"][$beh];
		$vv=$G["vysledky"][$beh];

		foreach($rataj as $p) {
			if (isset($G["vysledky"][$beh])) {
				$v=$G["vysledky"][$beh][$p];
				#body quickfix
				$v[10]=$v[4];
				$v[3]=str_replace(",",".",$v[3]);
				if ($v[3]!="N") {				
					if ($v[3]>=$par[8]) $v[3]="DIS";
				}
				if ($v[3]!="N" && $v[3]!="DIS") $v[3]+=0;
				switch ($v[3]) {
					case "DIS":
					case "100":
					case "888":
						$data[$p][$beh]["cas"]=100;
						$data[$p][$beh]["tb"]=100;
						break;
					case "N":
					case "200":
					case "999":
						$data[$p][$beh]["cas"]=100;
						$data[$p][$beh]["tb"]=100;
						break;
					default:
						$v[4]=$v[3]-$par[7];
						if ($v[4]<0) $v[4]=0;
						$v[4]+=5*$v[1]+5*$v[2];
						$data[$p][$beh]["cas"]=$v[3];
						$data[$p][$beh]["tb"]=$v[4];
				}
			} else {
				$data[$p][$beh]["cas"]=100;
				$data[$p][$beh]["tb"]=100;
			}
			$data[$p][$beh]["body"]=$v[10];
		}
	}

	foreach($rataj as $p) {
		foreach($behy as $beh) {
			
			$sucet[$p]["cas"]+=$data[$p][$beh]["cas"];
			$sucet[$p]["tb"]+=$data[$p][$beh]["tb"];
			$sucet[$p]["body"]+=$data[$p][$beh]["body"];
			if (!strpos($beh,"J")) {
				$sucet[$p]["bodyA"]+=$data[$p][$beh]["body"];
			}
		}
	}

	if ($body) {
		uasort($sucet,"sucetbody");
	} else {
		uasort($sucet,"sucetfci");
	}

	#var_export($sucet);
	$poradie=1;
	foreach($sucet as $k=>$v) {
		$T=$G["startovka"][$k];
		
		#var_export($T);
		if ($filter=="PP" && $T[7]!="A") continue;
		if ($filter=="V" && substr($T[0],1,2)!="AV") continue;
		if ($filter=="BEZV" && substr($T[0],1,2)=="AV") continue;
		
		$r=array(
			($body ? 
				($sucet[$k]["body"]>0 ? $poradie : "-")
				:
				($sucet[$k]["tb"]<100*count($behy) ? $poradie : "-")
			)
				
		,$k,$T[0],$T[3]." ".$T[2],$T[5],$T[4],$T[6].($T[7]!="" ?" (pp)":""));

		if ($body) {
			foreach($behy as $beh) {
				$r[]=sprintf("%d",$data[$k][$beh]["body"]);
				$r[]=sprintf("%1.2f",$data[$k][$beh]["tb"]);
			}
			$r[]=sprintf("%d",$sucet[$k]["body"]);
			$r[]=sprintf("%1.2f",$sucet[$k]["tb"]);
		} else {
			foreach($behy as $beh) {
				$r[]=sprintf("%1.2f",$data[$k][$beh]["cas"]);
				$r[]=sprintf("%1.2f",$data[$k][$beh]["tb"]);
			}
			$r[]=sprintf("%1.2f",$sucet[$k]["cas"]);
			$r[]=sprintf("%1.2f",$sucet[$k]["tb"]);
		}
		
		#hack na MSR krizencov
		if (substr($nadpis,0,5)=="MSR k") {
			if (isset($G["startovka_k"][$k])) $tab[]=$r;
		} else {
			$tab[]=$r;
		}
		$poradie++;
	}

	$pdf->AddPage();
	$pdf->SetFont('SkodaSans','BL',12);
	$pdf->Write(10,$nadpis);
	$pdf->ln(4);
	$nadpis2="Sï¿½ï¿½et: ";
	foreach ($behy as $beh) {
		$nadpis2.=$beh." ".iconv("utf-8","windows-1250",$G["behy"][$beh][1])." (".$G["behy"][$beh][3].") + ";
		#$nadpis.=iconv("utf-8","windows-1250",$G["behy"][$beh][1])." (".$G["behy"][$beh][3].") + ";
	}
	$pdf->SetFont('','',9);
	$pdf->Write(10,substr($nadpis2,0,-2));
	$pdf->Ln();
	$pdf->SetFont('skodasans','',8);
	$pdf->suctytable($hlavicka,$tab);
}



function zrobteamysucet($behy,$nadpis="Sï¿½ï¿½et behov - druï¿½stvï¿½") {
	global $pdf,$G;

	$filter="";
	$body=false;
	$teamy=false;

	if ($behy[count($behy)-1]=="PP") {
		$filter="PP";
		unset($behy[count($behy)-1]);
	}
	
	if ($behy[count($behy)-1]=="B") {
		$body=true;
		unset($behy[count($behy)-1]);
	}


	$hlavicka=array("por","ï¿½.","kat.","meno psovoda","meno psa","klub","plemeno");


	if ($body) {
		foreach($behy as $beh) {
			$hlavicka[]=$beh." B";
			$hlavicka[]=$beh." TB";
			foreach($G["vysledky"][$beh] as $v) {
				$rataj[$v[0]]=$v[0];
			}
		}
		$hlavicka[]="Body";
		$hlavicka[]="TB";
	} else {
		foreach($behy as $beh) {
			$hlavicka[]=$beh." ï¿½as";
			$hlavicka[]=$beh." TB";
			foreach($G["vysledky"][$beh] as $v) {
				$rataj[$v[0]]=$v[0];
			}
		}
	
		$hlavicka[]="ï¿½as";
		$hlavicka[]="TB";
	}


	$ignoruj=array();
	foreach($G["timy"] as $k=>$t) {
		foreach($behy as $beh) {
			$zotried=array();
			foreach($t["teams"] as $k2=>$v2) {
				$par=$G["behy"][$beh];
				$vv=$G["vysledky"][$beh];		
				$v=$vv[$v2];
				$v[3]=str_replace(",",".",$v[3]);
				if ($v[3]!="DIS" && $v[3]!="N" && $v[3]>=$par[8]) {
					$v[4]=100;
				}
				if ($v[3]=="DIS" || $v[4]==888 || $v[4]==100) {
					$v[4]=100;
					$stats["DIS"]++;
				} elseif ($v[3]=="N") {
					$v[4]=100;
					$stats["pocet"]--;
				} else {
					$v[4]=str_replace(",",".",$v[3])-$par[7];
					if ($v[4]<0) $v[4]=0;
					$v[5]=sprintf("%1.2f",$v[4]);
					$v[4]+=5*$v[1]+5*$v[2];
				}
				$zotried[$v2]["cas"]=($v[4]==100 ? 100 : $v[3]);
				$zotried[$v2]["tb"]=$v[4];
				$zotried[$v2]["id"]=$v2;
				$zotried[$v2]["beh"]=$beh;
			}
			usort($zotried,"sucetfci");
			#var_dump($zotried);exit;
			foreach($zotried as $tpor=>$datax) {
				#echo $tpor;
				if ($tpor>=$G["teamcountresults"]) {
					$ignoruj[$datax["beh"]][$datax["id"]]=true;
				}
			}
		}
		#var_dump($ignoruj);exit;
	}

	$teamres=array();
	foreach($G["timy"] as $k=>$t) {
		#print_r($k);
		#print_r($t);
		$vysledkov=0;
		$mamdaco=false;
		foreach($t["teams"] as $k2=>$v2) {
			foreach($behy as $beh) {
				$vv=$G["vysledky"][$beh];
				if (isset($vv[$v2])) $mamdaco=true;
			}
		}		
		
		
		if (!$mamdaco) continue;

		foreach($t["teams"] as $k2=>$v2) {
			
			foreach($behy as $beh) {

				$par=$G["behy"][$beh];
				$vv=$G["vysledky"][$beh];
			
				$v=$vv[$v2];
				$v[3]=str_replace(",",".",$v[3]);
				if ($v[3]!="DIS" && $v[3]!="N" && $v[3]>=$par[8]) {
					$v[4]=100;
				}
				if ($v[3]=="DIS" || $v[4]==888 || $v[4]==100) {
					$v[4]=100;
					$stats["DIS"]++;
				} elseif ($v[3]=="N") {
					$v[4]=100;
					$stats["pocet"]--;
				} else {
					$v[4]=str_replace(",",".",$v[3])-$par[7];
					if ($v[4]<0) $v[4]=0;
					$v[5]=sprintf("%1.2f",$v[4]);
					$v[4]+=5*$v[1]+5*$v[2];
				}
				#$teamres[$k]["cas"]+=$v[3];
				if (!isset($ignoruj[$beh][$v2])) {
					$teamres[$k]["cas"]+=($v[4]==100 ? 100 : $v[3]);
					$teamres[$k]["tb"]+=$v[4];
					$vysledkov++;
				}
			}
		}
		$teamres[$k]["id"]=$k;
		$teamres[$k]["cas"]+=($G["teamcountresults"]*count($behy)-$vysledkov)*100;
		$teamres[$k]["tb"]+=($G["teamcountresults"]*count($behy)-$vysledkov)*100;
	}
	usort($teamres,"sucetfci");

	foreach($behy as $beh) {

		$par=$G["behy"][$beh];
		$vv=$G["vysledky"][$beh];

		foreach($rataj as $p) {
			if (isset($G["vysledky"][$beh])) {
				$v=$G["vysledky"][$beh][$p];
				#body quickfix
				$v[10]=$v[4];
				$v[3]=str_replace(",",".",$v[3]);
				if ($v[3]!="N") {				
					if ($v[3]>=$par[8]) $v[3]="DIS";
				}
				if ($v[3]!="N" && $v[3]!="DIS") $v[3]+=0;
				switch ($v[3]) {
					case "DIS":
					case "100":
					case "888":
						$data[$p][$beh]["cas"]=100;
						$data[$p][$beh]["tb"]=100;
						break;
					case "N":
					case "200":
					case "999":
						$data[$p][$beh]["cas"]=100;
						$data[$p][$beh]["tb"]=100;
						break;
					default:
						$v[4]=$v[3]-$par[7];
						if ($v[4]<0) $v[4]=0;
						$v[4]+=5*$v[1]+5*$v[2];
						$data[$p][$beh]["cas"]=$v[3];
						$data[$p][$beh]["tb"]=$v[4];
				}
			} else {
				$data[$p][$beh]["cas"]=100;
				$data[$p][$beh]["tb"]=100;
			}
			$data[$p][$beh]["body"]=$v[10];
		}
	}

	foreach($rataj as $p) {
		foreach($behy as $beh) {
			if (!isset($ignoruj[$beh][$p])) {
				$sucet[$p]["cas"]+=$data[$p][$beh]["cas"];
				$sucet[$p]["tb"]+=$data[$p][$beh]["tb"];
				$sucet[$p]["body"]+=$data[$p][$beh]["body"];
				if (!strpos($beh,"J")) {
					$sucet[$p]["bodyA"]+=$data[$p][$beh]["body"];
				}
			}
		}
	}

	if ($body) {
		uasort($sucet,"sucetbody");
	} else {
		uasort($sucet,"sucetfci");
	}

	#var_export($sucet);
	$poradie=0;
	
	foreach ($teamres as $tv) {

		$vysledkov=0;
		foreach($G["timy"][$tv["id"]]["teams"] as $tt) {
			$T=$G["startovka"][$tt];

		$r=array(
			'-'				
		,$tt,$T[0],$T[3]." ".$T[2],$T[5],$T[4],$T[6].($T[7]!="" ?" (pp)":""));

		foreach($behy as $beh) {
				if (isset($ignoruj[$beh][$tt])) {
					$igntxt="XX";
				} else $igntxt="";
				$r[]=$igntxt.sprintf("%1.2f",$data[$tt][$beh]["cas"]);
				$r[]=$igntxt.sprintf("%1.2f",$data[$tt][$beh]["tb"]);
			}
			$r[]=sprintf("%1.2f",$sucet[$tt]["cas"]);
			$r[]=sprintf("%1.2f",$sucet[$tt]["tb"]);
			$tab[]=$r;
		}
		$poradie++;
		for($i=count($G["timy"][$tv["id"]]["teams"]);$i<$G["teamcount"];$i++) {
			$fr=array("-","-","-","-","-","-","-");
			foreach($behy as $beh) {
				$igntxt=($i>=$G["teamcountresults"] ? "XX" : "");
				$fr[]=$igntxt."100,00";$fr[]=$igntxt."100,00";
			}
			$fr[]=$igntxt.sprintf("%1.2f",count($behy)*100*($igntxt=="XX"?0:1));
			$fr[]=$igntxt.sprintf("%1.2f",count($behy)*100*($igntxt=="XX"?0:1));
			$tab[]=$fr;
		}
		$r2=array(
		($tv["tb"]<$G["teamcount"]*100*count($behy) ? $poradie : "-")
		,$tv["id"],"",$G["timy"][$tv["id"]]["name"],"","","");
		for($i=0;$i<count($behy);$i++) {
			$r2[]="";$r2[]="";
		}
		$r2[]=$tv["cas"];
		$r2[]=$tv["tb"];
		$tab[]=$r2;
	}

	$pdf->AddPage();
	$pdf->SetFont('SkodaSans','BL',12);
	$pdf->Write(10,$nadpis);
	$pdf->ln(4);
	$nadpis2="Sï¿½ï¿½et: ";
	foreach ($behy as $beh) {
		$nadpis2.=$beh." ".iconv("utf-8","windows-1250",$G["behy"][$beh][1])." (".$G["behy"][$beh][3].") + ";
		#$nadpis.=iconv("utf-8","windows-1250",$G["behy"][$beh][1])." (".$G["behy"][$beh][3].") + ";
	}
	$pdf->SetFont('','',9);
	$pdf->Write(10,substr($nadpis2,0,-2));
	$pdf->Ln();
	$pdf->SetFont('skodasans','',8);
	$pdf->suctyteamtable($hlavicka,$tab);
}




function zrobpdf($beh) {
	global $G,$pdf;

	$par=$G["behy"][$beh];
	$vv=$G["vysledky"][$beh];

	$pdf->AddPage();
	$pdf->SetFont('SkodaSans','BL',16);

	$pdf->Write(10,iconv("utf-8","windows-1250",$par[1]));
	$pdf->Ln();
	$pdf->SetFont('skodasans','',8);
	$pdf->Write(8,'Dï¿½tum: '.$par[3]);
	$pdf->Ln(4);
	$pdf->Write(8,'Rozhodca: '.iconv("utf-8","windows-1250",$par[4]));
	$pdf->Ln(4);
	$pdf->Write(8,'Dï¿½ka parkï¿½ru: '.$par[5]." m");
	$pdf->Ln(4);
	$pdf->Write(8,'Poï¿½et prekï¿½ok: '.$par[6]);
	$pdf->Ln(4);
	$pdf->Write(8,'ï¿½tandardnï¿½ ï¿½as: '.$par[7]." s (".sprintf("%1.2f",$par[5]/$par[7])." m/s)");
	$pdf->Ln(4);
	$pdf->Write(8,'Maximï¿½lny ï¿½as: '.$par[8]." s (".sprintf("%1.2f",$par[5]/$par[8])." m/s)");
	$pdf->Ln(4);
	$pdf->Write(8,'Poï¿½et tï¿½mov: '.count($vv));
	$pdf->Ln(10);

$poradie=1;

$hlavicka=array(
"por.", #0
"ï¿½.", #1
"kat.", #2
"meno psovoda", #3
"klub", #4
"meno psa", #5
"plemeno", #6
"CH", #7
"OD", #8
"ï¿½as", #9
"TB ï¿½as", #10
"TB", #11
"post", #12
"hodn", #13
"body", #14
"VZ",#15
);

$tab=array();
$stats=array();
$stats["pocet"]=count($vv);
$stats["max"]=0;
$atsbody=0;
	foreach($vv as $v) {
		if ($v[3]!="N") $atsbody++;
	}

	foreach($vv as $v) {
		$r=array();
		
		$v[3]=str_replace(",",".",$v[3]);
		#var_export($v);
		
		#body quickfix
		$v[10]=$v[4];
		
		$v[3]=str_replace(",",".",$v[3]);
		$v[4]=str_replace(",",".",$v[4]);
		$v[5]="-";
		if ($v[3]!="DIS" && $v[3]!="N" && $v[3]>=$par[8]) {
			$v[4]=100;
		}
		if ($v[3]=="DIS" || $v[4]==888 || $v[4]==100) {
			$v[4]=888;
			$stats["DIS"]++;
		} elseif ($v[3]=="N") {
			$v[4]=888;
			$stats["pocet"]--;
		} else {
			$v[4]=str_replace(",",".",$v[3])-$par[7];
			if ($v[4]<0) $v[4]=0;
			$v[5]=sprintf("%1.2f",$v[4]);
			$v[4]+=5*$v[1]+5*$v[2];
		}

		if ($v[4]==0) $stats["cisto"]++;

		if ($v[3]!="N" && $v[3]!="DIS") {
			$v[3]=$v[3]+0;
		}
		if (str_replace(",",".",$v[3])>0) {
			$r[0]=$poradie;
		} else {
			$r[0]="-";
		}		
		$T=$G["startovka"][$v[0]];
		$r[1]=$T[1]; #cislo
		$r[2]=$T[0]; #kat
		$r[3]=$T[3]." ".$T[2]; #meno
		$r[4]=$T[4]; #klub
		$r[5]=$T[5]; #meno psa
		$r[6]=$T[6]; #plemeno
		$r[6].=($T[7]!="" ? " (pp)":"");
		if ($v[3]=="N" || $v[3]=="DIS") {
			if ($v[3]=="N") {			
				$r[7]=$r[8]="-"; #chyb,odm
			} else {
				$r[7]=($v[1]+0); #chyb
				$r[8]=($v[2]+0); #odm
			}
			$r[9]=$v[3];
		} else {		
			$r[7]=($v[1]+0); #chyb
			$r[8]=($v[2]+0); #odm
			$r[9]=sprintf("%1.2f",str_replace(",",".",$v[3])); #cas
		}
		#hodnotenie
		$r[13]=$v[5];
		

	switch($v[4]) {
		case 100:
		case 200:
		case 888:
		case 999:
			$r[10]="-";
			$r[11]="-";
			$r[12]=$v[3];
			if ($v[4]==100 || $v[4]==888) $r[12]="DIS";
			break;
		default:
			if (strlen($par[2])==1) {
				$G["ATS"][$r[1]]+=$atsbody-$poradie;
			}
			$r[10]=sprintf("%1.2f",str_replace(",",".",$v[4])); #TB		
			$r[11]=sprintf("%1.2f",$par[5]/$v[3]);
			$stats["rychlost"]+=$par[5]/$v[3];
			if ($stats["max"]<$par[5]/$v[3]) $stats["max"]=$par[5]/$v[3];
			if ($v[4]<6) {
				$r[12]="V";
				$stats["V"]++;
			} elseif ($v[4]<16) {
				$r[12]="VD";
				$stats["VD"]++;
			} elseif ($v[4]<26) {
				$r[12]="D";
				$stats["D"]++;
			} else {
				$r[12]="BO";
				$stats["BO"]++;
			}
	}
		#body
		$r[14]=$v[10];
		#vz
		$r[15]=$T[9];
		$poradie++;


	$tab[]=$r;	
	
	}

	$pdf->BasicTable($hlavicka,$tab);


	$pdf->SetFont('skodasans','',8);
	$spolu=count($vv);
	$pdf->Write(8,
	"ï¿½tartovali: ".($stats["pocet"]+0)." (".sprintf("%d",$stats["pocet"]/$spolu*100)."%) ".
	"ï¿½istï¿½ behy: ".($stats["cisto"]+0)." (".sprintf("%d",$stats["cisto"]/$spolu*100)."%) ".
	"V: ".($stats["V"]+0)." (".sprintf("%d",$stats["V"]/$spolu*100)."%) ".
	"VD: ".($stats["VD"]+0)." (".sprintf("%d",$stats["VD"]/$spolu*100)."%) ".
	"D: ".($stats["D"]+0)." (".sprintf("%d",$stats["D"]/$spolu*100)."%) ".
	"BO: ".($stats["BO"]+0)." (".sprintf("%d",$stats["BO"]/$spolu*100)."%) ".
	"DIS: ".($stats["DIS"]+0)." (".sprintf("%d",$stats["DIS"]/$spolu*100)."%) ".
	($stats["DIS"]<$stats["pocet"] ? "Priemernï¿½ postupovï¿½ rï¿½chlosï¿½: ".sprintf("%1.2f",$stats["rychlost"]/($stats["pocet"]-$stats["DIS"]))." m/s ":"").
	($stats["max"]>0 ? "Max. postupovï¿½ rï¿½chlosï¿½ï¿½: ".sprintf("%1.2f",$stats["max"])." m/s" :"")
	);

	foreach($vv as $k=>$v) {
		#var_export($v);
		$v[3]=str_replace(",",".",$v[3]);
		$v[4]=str_replace(",",".",$v[4]);
		if ($v[3]=="DIS" || $v[3]==100) {
			$v[3]=$v[4]=888;
		} elseif ($v[3]=="N" || $v[4]==200) {
			$v[3]=$v[4]=888;
		} else { 
			$v[4]=str_replace(",",".",$v[3])-$par[7];
			if ($v[4]<0) $v[4]=0;
			$v[4]+=5*$v[1]+5*$v[2];
		
		}
		#var_export($v);
		$G["sucet"][$v[0]][0]=$v[0];
		$G["sucet"][$v[0]][3]+=$v[3];
		$G["sucet"][$v[0]][1]+=$v[1];
		$G["sucet"][$v[0]][2]+=$v[2];
		$G["sucet"][$v[0]][4]+=$v[4];
	}

}

function zrobpdfteamy($beh) {
	global $G,$pdf;

	$par=$G["behy"][$beh];
	$vv=$G["vysledky"][$beh];

	$pdf->AddPage();
	$pdf->SetFont('SkodaSans','BL',16);

	$pdf->Write(10,iconv("utf-8","windows-1250",$par[1])." - druï¿½stvï¿½");
	$pdf->Ln();
	$pdf->SetFont('skodasans','',8);
	$pdf->Write(8,'Dï¿½tum: '.$par[3]);
	$pdf->Ln(4);
	$pdf->Write(8,'Rozhodca: '.iconv("utf-8","windows-1250",$par[4]));
	$pdf->Ln(4);
	$pdf->Write(8,'Dï¿½ka parkï¿½ru: '.$par[5]." m");
	$pdf->Ln(4);
	$pdf->Write(8,'Poï¿½et prekï¿½ok: '.$par[6]);
	$pdf->Ln(4);
	$pdf->Write(8,'ï¿½tandardnï¿½ ï¿½as: '.$par[7]." s (".sprintf("%1.2f",$par[5]/$par[7])." m/s)");
	$pdf->Ln(4);
	$pdf->Write(8,'Maximï¿½lny ï¿½as: '.$par[8]." s (".sprintf("%1.2f",$par[5]/$par[8])." m/s)");
	#$pdf->Ln(4);
	#$pdf->Write(8,'Poï¿½et tï¿½mov: '.count($vv));
	$pdf->Ln(10);

$poradie=1;

$hlavicka=array(
"por.", #0
"ï¿½.", #1
"kat.", #2
"meno psovoda", #3
"klub", #4
"meno psa", #5
"plemeno", #6
"CH", #7
"OD", #8
"ï¿½as", #9
"TB ï¿½as", #10
"TB", #11
"post", #12
"hodn", #13
"body", #14
"VZ",#15
);

$tab=array();
$stats=array();
$stats["pocet"]=count($vv);
$stats["max"]=0;
$atsbody=0;
	$ignoruj = array();	

	foreach($G["timy"] as $k=>$t) {		
		$vysledkov=0;
		$zotried=array();
		foreach($t["teams"] as $k2=>$v2) {
			$v=$vv[$v2];
			$v[3]=str_replace(",",".",$v[3]);
			if ($v[3]!="DIS" && $v[3]!="N" && $v[3]>=$par[8]) {
				$v[4]=100;
			}
			if ($v[3]=="DIS" || $v[4]==888 || $v[4]==100) {
				$v[4]=100;
				$stats["DIS"]++;
			} elseif ($v[3]=="N") {
				$v[4]=100;
				$stats["pocet"]--;
			} else {
				$v[4]=str_replace(",",".",$v[3])-$par[7];
				if ($v[4]<0) $v[4]=0;
				$v[5]=sprintf("%1.2f",$v[4]);
				$v[4]+=5*$v[1]+5*$v[2];
			}
			$zotried[]=array(
				"cas"=>($v[4]==100 ? 100 : $v[3]),
				"tb" =>$v[4],
				"id" =>$v2
			);
		}
		usort($zotried,"sucetfci");
		foreach($zotried as $kz=>$z) {
			if ($kz>=$G["teamcountresults"]) $ignoruj[$z["id"]]=true;
		}
	}	


	$teamres=array();
	foreach($G["timy"] as $k=>$t) {		
		#print_r($k);
		#print_r($t);
		$vysledkov=0;
		$mamdaco=false;
		foreach($t["teams"] as $k2=>$v2) {
			if (isset($vv[$v2])) $mamdaco=true;
		}
		if (!$mamdaco) continue;

		foreach($t["teams"] as $k2=>$v2) {
			echo "tim".$v2."\n";
			
			$v=$vv[$v2];
			$v[3]=str_replace(",",".",$v[3]);
			if ($v[3]!="DIS" && $v[3]!="N" && $v[3]>=$par[8]) {
				$v[4]=100;
			}
			if ($v[3]=="DIS" || $v[4]==888 || $v[4]==100) {
				$v[4]=100;
				$stats["DIS"]++;
			} elseif ($v[3]=="N") {
				$v[4]=100;
				$stats["pocet"]--;
			} else {
				$v[4]=str_replace(",",".",$v[3])-$par[7];
				if ($v[4]<0) $v[4]=0;
				$v[5]=sprintf("%1.2f",$v[4]);
				$v[4]+=5*$v[1]+5*$v[2];
			}
			#$teamres[$k]["cas"]+=$v[3];
			
			if (!isset($ignoruj[$v2])) {
				$teamres[$k]["cas"]+=($v[4]==100 ? 100 : $v[3]);
				$teamres[$k]["tb"]+=$v[4];
			}
			$vysledkov++;
		}
		$teamres[$k]["id"]=$k;
		if ($vysledkov<$G["teamcountresults"]) {
			$teamres[$k]["cas"]+=($G["teamcountresults"]-$vysledkov)*100;
			$teamres[$k]["tb"]+=($G["teamcountresults"]-$vysledkov)*100;
		}
	}
	usort($teamres,"sucetfci");
	#print_r($teamres);
	#print_r($vv);
	#exit;
	
	$tab=array();

	$poradie=0;
	foreach ($teamres as $tv) {
		$chybspolu=0;
		$odmspolu=0;
		#$tbspolu=0;
		$tbcasspolu=0;
		echo "druzstvo ".$tv["id"]."\n";
		print_r($G["timy"][$tv["id"]]["teams"]);
		foreach($G["timy"][$tv["id"]]["teams"] as $tt) {
			if ($tt+0<1) continue;
			$r=array();
			echo "tim ".$tt."\n";
			$v=$vv[$tt];
			
			$v[3]=str_replace(",",".",$v[3]);
			if ($v[3]!="DIS" && $v[3]!="N" && $v[3]>=$par[8]) {
				$v[4]=100;
			}
			if ($v[3]=="DIS" || $v[4]==888 || $v[4]==100) {
				$v[4]=100;
				$stats["DIS"]++;
			} elseif ($v[3]=="N") {
				$v[4]=100;
				$stats["pocet"]--;
			} else {
				$v[4]=str_replace(",",".",$v[3])-$par[7];
				if ($v[4]<0) $v[4]=0;
				$v[5]=sprintf("%1.2f",$v[4]);
				if (!isset($ignoruj[$tt])) {
					$tbcasspolu+=$v[4];
				}
				$v[4]+=5*$v[1]+5*$v[2];
			}
			$T=$G["startovka"][$tt];
			$r[0]='';
			$r[1]=$T[1]; #cislo
			$r[2]=$T[0]; #kat
			$r[3]=$T[3]." ".$T[2]; #meno
			$r[4]=$T[4]; #klub
			$r[5]=$T[5]; #meno psa
			$r[6]=$T[6]; #plemeno
			$r[6].=($T[7]!="" ? " (pp)":"");
			if ($v[3]=="N" || $v[3]=="DIS") {
				if ($v[3]=="N") {			
					$r[7]=$r[8]="-"; #chyb,odm
				} else {
					$r[7]=($v[1]+0); #chyb
					$r[8]=($v[2]+0); #odm
				}
				$r[9]=$v[3];
			} else {		
				$r[7]=($v[1]+0); #chyb
				$r[8]=($v[2]+0); #odm
				$r[9]=sprintf("%1.2f",str_replace(",",".",$v[3])); #cas
			}
			if (!isset($ignoruj[$tt])) {
				$chybspolu+=$v[1];
				$odmspolu+=$v[2];
			}
			#hodnotenie
			$r[13]=$v[5];
			
			
			switch($v[4]) {
				case 100:
				case 200:
				case 888:
				case 999:
					$r[10]="-";
					$r[11]="-";
					$r[12]=$v[3];
					if ($v[4]==100 || $v[4]==888) $r[12]="DIS";
					break;
				default:
					if (strlen($par[2])==1) {
						$G["ATS"][$r[1]]+=$atsbody-$poradie;
					}
					$r[10]=sprintf("%1.2f",str_replace(",",".",$v[4])); #TB
					$r[11]=sprintf("%1.2f",$par[5]/$v[3]);
					$stats["rychlost"]+=$par[5]/$v[3];
					if ($stats["max"]<$par[5]/$v[3]) $stats["max"]=$par[5]/$v[3];
					if ($v[4]<6) {
						$r[12]="V";
						$stats["V"]++;
					} elseif ($v[4]<16) {
						$r[12]="VD";
						$stats["VD"]++;
					} elseif ($v[4]<26) {
						$r[12]="D";
						$stats["D"]++;
					} else {
						$r[12]="BO";
						$stats["BO"]++;
					}
			}
				#body
				$r[14]=$v[10];
				#vz
				$r[15]=$T[9];
				
			if (isset($ignoruj[$tt])) {
				$r[7]="(".$r[7].")";
				$r[8]="(".$r[8].")";
				$r[9]="(".$r[9].")";
				$r[10]="(".$r[10].")";
				$r[11]="(".$r[11].")";
			}
			$tab[]=$r;
		}
		for($i=count($G["timy"][$tv["id"]]["teams"]);$i<$G["teamcount"];$i++) {
			if ($i>=$G["teamcountresults"]) {
				$tab[]=array("-","-","-","-","-","-","-","-","-","(100)","-","(100)","-","-","-","-");
			} else {
				$tab[]=array("-","-","-","-","-","-","-","-","-","100,00","-","100,00","-","-","-","-");
			}
		}
		$poradie++;
		$tab[]=array(
		($tv["tb"]<$G["teamcount"]*100 ? $poradie : "-")
		,$tv["id"],"",$G["timy"][$tv["id"]]["name"],"","","",
		$chybspolu, $odmspolu, $tv["cas"], $tbcasspolu, $tv["tb"], "" , "", "" , "");
	
	}
	
	$pdf->TeamBasicTable($hlavicka,$tab);

}

function statistika() {
	global $G,$pdf;

	$pdf=new aPDF("P","mm","A4");
	$pdf->AddFont('SkodaSans','','SkodaSansRg.php');
	$pdf->AddFont('SkodaSans','B','SkodaSansBd.php');
	$pdf->AddFont('SkodaSans','I','SkodaSansIt.php');
	$pdf->AddFont('SkodaSans','BL','SkodaSansBL.php');
	$pdf->AddFont('SkodaSans','BLI','SkodaSansBLI.php');
	$pdf->AddFont('SkodaSans','BI','SkodaSansBI.php');

	$pdf->AliasNbPages();

	$pdf->nadpis=$G["nadpis"];


	$pdf->AddPage();
	$pdf->SetFont('SkodaSans','BL',16);

	$pdf->Write(10,"ï¿½tatistika");
	$pdf->Ln();
	$pdf->SetFont('skodasans','',8);
	
	$velkosti=array();
	$kategorie=array();
	$kluby=array();
	$plemena=array();
	$pp=0;
	foreach($G["startovka"] as $v) {
		$vek=substr($v[0],0,1);
		$vyk=substr($v[0],1,2);
		$velkosti[$vek]++;
		$kategorie[$vyk]++;
#		$v[4]=iconv("windows-1250","utf-8",$v[4]);
		$kluby[$v[4]]++;
#		$v[6]=iconv("windows-1250","utf-8",$v[6]);
		$plemena[$v[6]]++;
		$krajiny[$v[8]]++;
		if(ereg("A",$v[7]))$pp++;
	}
	
	$pdf->SetFont('','B',10);
	$pdf->write(10,"Poï¿½et tï¿½mov: ".count($G["startovka"]));
	$pdf->ln();
	$pdf->SetFont('','B',10);
	$pdf->write(10,"Veï¿½kostnï¿½ kategï¿½rie");
	$pdf->ln(4);
	$pdf->SetFont('','',8);
	foreach($velkosti as $k=>$v) { 	
		$pdf->Write(10,iconv("utf-8","windows-1250",$k.": ".$v));
		$pdf->ln(4);
	}

	$pdf->SetFont('','B',10);
	$pdf->write(10,"Vï¿½konnostnï¿½ kategï¿½rie");
	$pdf->ln(4);
	$pdf->SetFont('','',8);
	foreach($kategorie as $k=>$v) { 	
		$pdf->Write(10,iconv("utf-8","windows-1250",$k.": ".$v));
		$pdf->ln(4);
	}

	$pdf->SetFont('','B',10);
	$pdf->write(10,"Kluby");
	$pdf->ln(8);
	$pdf->SetFont('','',8);
	arsort($kluby);
	foreach($kluby as $k=>$v) { 	
		$pdf->Write(4,iconv("utf-8","windows-1250",$k.": ".$v."  "));
		#$pdf->ln(4);
	}
	$pdf->ln(4);

	$pdf->SetFont('','B',10);
	$pdf->write(10,"Krajiny");
	$pdf->ln(8);
	$pdf->SetFont('','',8);
	arsort($krajiny);
	$cc=array(
	"sk"=>"Slovensko",
	"hu"=>"Maï¿½arsko",
	"si"=>"Slovinsko",
	"ru"=>"Rusko",
	"cz"=>"ï¿½eskï¿½ republika",
	"at"=>"Rakï¿½sko",
	"pl"=>"Poï¿½sko",
	"cr"=>"Chorvï¿½tsko",
	"ch"=>"ï¿½vajï¿½iarsko",
	);
	foreach($krajiny as $k=>$v) { 	
		$krajina=$cc[$k];
		$pdf->Write(4,$krajina.": ".$v."  ");
		#$pdf->ln(4);
	}
	$pdf->ln(4);

	$pdf->SetFont('','B',10);
	$pdf->write(10,"Plemenï¿½");
	$pdf->ln(8);
	$pdf->SetFont('','',8);
	arsort($plemena);
	foreach($plemena as $k=>$v) { 	
		$pdf->Write(4,iconv("utf-8","windows-1250",$k.": ".$v." "));
	}
	$pdf->ln(4);
	$pdf->SetFont('','B',8);
	$pdf->write(10,"S preukazom pï¿½vodu: ".$pp." (".round($pp/count($G["startovka"])*100)." %)");
	$pdf->ln(4);

}

function zapis() {
	global $pdf,$G;

	foreach($G["startovka"] as $t) {
		$r=array();
		$r[0]=$t[1];
		$r[1]=$t[0];
		$r[2]=$t[3]." ".$t[2];
		#$r[3]=$t[4];
		$r[4]=$t[5]." / ".$t[6];
		$r[5]=$r[6]=$r[7]="";
		$tab[substr($t[0],0,1)][]=$r;
	}

	$hlavicka=array(
		"ï¿½.",
		"kat.",
		"psovod",
		#"klub",
		"pes",
		"chyby",
		"odmietnutia",
		"ï¿½as"
	);

#var_export($tab);
#exit;

	$kategorie=array("S","M","L","P");

	foreach($kategorie as $k) {
		$pdf->AddPage();
		$pdf->SetFont('SkodaSans','BL',10);
		$pdf->Write(10,"Zï¿½pis ".$k);
		$pdf->SetFont('SkodaSans','B',10);
		$pdf->Ln(4);
		$pdf->Write(10,"Beh:                                     Dï¿½tum:                                       Zapisovateï¿½:");
		$pdf->Ln();
		$pdf->ZapisTable($hlavicka,$tab[$k]);
	}
	
}

function startovka($param) {
	global $pdf,$G;

	$tab=array(
	#"SA0"=>array(),
	#"MA0"=>array(),
	#"LA0"=>array(),
	#"SA"=>array(),
	#"MA"=>array(),
	#"LA"=>array()
	);

	foreach($G["startovka"] as $t) {
		
		$r=array();
		$r[0]=$t[1];
		$r[1]=$t[0];
		$r[2]=$t[3]." ".$t[2];
		$r[3]=$t[4];
		$r[4]=$t[5];
		$r[5]=$t[6];
		$r[6]=(ereg("A",$t[7])?"PP":"");
		#$r[7]=ereg("P",$t[7]);
		if ($param=="P" && !ereg("P",$t[7])) continue;
		if (substr($t[0],2,1)=="V") {
			switch(substr($t[0],0,1)) {
				case "L":
					$tab["M"][]=$r;
					break;
				case "M":
					$tab["S"][]=$r;
					break;
				case "S":
					$tab["S"][]=$r;
					break;
			}
		} elseif(substr($t[0],2,1)=="0") {
			$tab[$t[0]][]=$r;
		} else {
			$tab[substr($t[0],0,1)][]=$r;
		}
	}

	$pdf=new aPDF("P","mm","A4");
	$pdf->AddFont('SkodaSans','','SkodaSansRg.php');
	$pdf->AddFont('SkodaSans','B','SkodaSansBd.php');
	$pdf->AddFont('SkodaSans','I','SkodaSansIt.php');
	$pdf->AddFont('SkodaSans','BL','SkodaSansBL.php');
	$pdf->AddFont('SkodaSans','BLI','SkodaSansBLI.php');
	$pdf->AddFont('SkodaSans','BI','SkodaSansBI.php');

	$pdf->AliasNbPages();

	$pdf->nadpis=$G["nadpis"];


	$pdf->AddPage();
	$pdf->SetFont('SkodaSans','BL',16);

	$pdf->Write(10,"Šartovná listina");
	$pdf->Ln();
	$pdf->SetFont('skodasans','',8);

	$hlavicka=array(
		"è.",
		"Kat.",
		"Meno psovoda",
		"Klub",
		"Meno psa",
		"Plemeno",
		"PP"
	);

#var_export($tab);
#exit;

	foreach($tab as $k=>$v) {
		$pdf->SetFont('SkodaSans','BL',8);
		$pdf->Write(9,"Kategória ".$k);
		$pdf->Ln();
		$pdf->SetFont('SkodaSans','',7.5);
		$pdf->StartTable($hlavicka,$v);
	}
	
}

function startovkaTeams($param) {
	global $pdf,$G;

	$tab=array(
	#"SA0"=>array(),
	#"MA0"=>array(),
	#"LA0"=>array(),
	"S"=>array(),
	"M"=>array(),
	"L"=>array()
	);

	foreach(array("S","M","L") as $kat) {
		foreach($G["timy"] as $k=>$t) {
		#print_r($k);
		#print_r($t);
			$vysledkov=0;
			$mamdaco=false;
			foreach($t["teams"] as $k2=>$v2) {
				$t2=$G["startovka"][$v2];
				if (substr($t2[0],0,1)==$kat) $mamdaco=true;
			}
			if (!$mamdaco) continue;
	
			$tab[$kat][]=array(
				$k, #id teamu
				"-",
				$t["name"],
				"","","",""
				
			);
	
			foreach($t["teams"] as $k2=>$v2) {
				$t2=$G["startovka"][$v2];
				$r=array();
				$r[0]=$t2[1];
				$r[1]=$t2[0];
				$r[2]=$t2[3]." ".$t2[2];
				$r[3]=$t2[4];
				$r[4]=$t2[5];
				$r[5]=$t2[6];
				$r[6]=(ereg("A",$t2[7])?"PP":"");
	
				$tab[$kat][]=$r;
			}
		}
	}

	$pdf=new aPDF("P","mm","A4");
	$pdf->AddFont('SkodaSans','','SkodaSansRg.php');
	$pdf->AddFont('SkodaSans','B','SkodaSansBd.php');
	$pdf->AddFont('SkodaSans','I','SkodaSansIt.php');
	$pdf->AddFont('SkodaSans','BL','SkodaSansBL.php');
	$pdf->AddFont('SkodaSans','BLI','SkodaSansBLI.php');
	$pdf->AddFont('SkodaSans','BI','SkodaSansBI.php');

	$pdf->AliasNbPages();

	$pdf->nadpis=$G["nadpis"];


	$pdf->AddPage();
	$pdf->SetFont('SkodaSans','BL',16);

	$pdf->Write(10,"Štartovná listina");
	$pdf->Ln();
	$pdf->SetFont('skodasans','',8);

	$hlavicka=array(
		"è.",
		"Kat.",
		"Meno psovoda",
		"Klub",
		"Meno psa",
		"Plemeno",
		"PP"
	);

#var_export($tab);
#exit;

	foreach($tab as $k=>$v) {
		$pdf->SetFont('SkodaSans','BL',8);
		$pdf->Write(9,"Kategória ".$k);
		$pdf->Ln();
		$pdf->SetFont('SkodaSans','',7.5);
		$pdf->StartTable($hlavicka,$v);
	}
	
}

function prezencka() {
	global $pdf,$G;

	foreach($G["startovka"] as $t) {
		$r=array();
		$r[0]=$t[1];
		$r[1]=$t[0];
		$r[2]=$t[3]." ".$t[2];
		$r[3]=$t[4];
		$r[4]=$t[5]." / ".$t[6];
#print_r($t);
		$r[5]=$t[9];
		$r[6]=$r[7]=$r[8]="";
		#$r[9]=$t[7];
		$tab[]=$r;
	}

	$pdf=new aPDF("P","mm","A4");
	$pdf->noheader=true;
	$pdf->AddFont('SkodaSans','','SkodaSansRg.php');
	$pdf->AddFont('SkodaSans','B','SkodaSansBd.php');
	$pdf->AddFont('SkodaSans','I','SkodaSansIt.php');
	$pdf->AddFont('SkodaSans','BL','SkodaSansBL.php');
	$pdf->AddFont('SkodaSans','BLI','SkodaSansBLI.php');
	$pdf->AddFont('SkodaSans','BI','SkodaSansBI.php');

	$pdf->AliasNbPages();

	$pdf->nadpis=$G["nadpis"];

	$pdf->AddPage();
	$pdf->SetFont('SkodaSans','BL',16);

	$pdf->Write(10,"Prezenï¿½nï¿½ listina");
	$pdf->Ln();
	$pdf->SetFont('skodasans','',8);

	$hlavicka=array(
		"ï¿½.",
		"Kat.",
		"Meno psovoda",
		"Klub",
		"Pes",
		"VZ",
		"Platba",
		"Meraï¿½",
		"Poznï¿½mka"
	);

#var_export($tab);
#exit;

		$pdf->SetFont('SkodaSans','',8);
		$pdf->PrezentTable($hlavicka,$tab);
	
}


function ats() {
	global $G,$pdf;

	#var_export($G["ATS"]);	

	$pdf=new aPDF("P","mm","A4");
	$pdf->AddFont('SkodaSans','','SkodaSansRg.php');
	$pdf->AddFont('SkodaSans','B','SkodaSansBd.php');
	$pdf->AddFont('SkodaSans','I','SkodaSansIt.php');
	$pdf->AddFont('SkodaSans','BL','SkodaSansBL.php');
	$pdf->AddFont('SkodaSans','BLI','SkodaSansBLI.php');
	$pdf->AddFont('SkodaSans','BI','SkodaSansBI.php');

	$pdf->AliasNbPages();

	$pdf->nadpis=$G["nadpis"];


	$pdf->AddPage();
	$pdf->SetFont('SkodaSans','BL',16);

	$pdf->Write(10,"ATS body");
	$pdf->Ln();
	$pdf->SetFont('skodasans','',7);


	 $pdf->SetFillColor(240,240,240);
    $pdf->SetTextColor(0);
    $pdf->SetDrawColor(64,64,64);
    $pdf->SetLineWidth(.3);
    $pdf->SetFont('','');

	$ri=0;

	
	
	
    foreach($G["startovka"] as $p)
    {

		if (substr($p[0],0,1)!=$oldkat) {
			$oldkat=substr($p[0],0,1);
        $pdf->Cell(90,5,$oldkat,1,0,"R",($ri%2==0));
        $pdf->Ln();
		}    
        $pdf->Cell(80,5,iconv("utf-8","windows-1250",$p[3]." ".$p[2]." + ".$p[5]),1,0,"L",($ri%2==0));
        $pdf->Cell(10,5,($G["ATS"][$p[1]]+1),1,0,"R",($ri%2==0));
        $pdf->Ln();
        $ri++;
    }

}

for($i=1;$i<count($argv);$i++) {
	$behy[]=$argv[$i];
}


function behy() {
	global $G;

	foreach($G["behy"] as $beh) {
		echo $beh[0]."\t".$beh[1]." (".$beh[2].")\n";
	}
}




function vz() {
	global $G,$pdf;


	$pdf=new aPDF("P","mm","A4");
	$pdf->noheader=true;
	$pdf->AddFont('SkodaSans','','SkodaSansRg.php');
	$pdf->AddFont('SkodaSans','B','SkodaSansBd.php');
	$pdf->AddFont('SkodaSans','I','SkodaSansIt.php');
	$pdf->AddFont('SkodaSans','BL','SkodaSansBL.php');
	$pdf->AddFont('SkodaSans','BLI','SkodaSansBLI.php');
	$pdf->AddFont('SkodaSans','BI','SkodaSansBI.php');

	$pdf->SetMargins(5,12,5); # bolo 5 12 5
	
	#$pdf->AliasNbPages();

	#$pdf->nadpis=$G["nadpis"];

	$pdf->AddPage();
	$pdf->SetFont('skodasans','',8);


	$tab=array();

	foreach($G["behy"] as $beh) {

		$par=$beh;
		$vv=$G["vysledky"][$beh[0]];

		if(strlen($par[2])==1) continue;

		$poradie=1;
		$pocet=count($vv);

		foreach($vv as $v) {
			if ($v[3]=="N") $pocet--;
		}

		foreach($vv as $v) {
			if ($v[3]=="N") continue;

			$riadok=array();

			$p=$G["startovka"][$v[0]];

			$riadok[0]=$par[3];
			$riadok[1]=iconv("windows-1250","utf-8",$G["nazovdovz"]);
			$riadok[1]=$G["nazovdovz"];
			$riadok[2]=$par[2];
			if ($v[3]=="DIS") {
				$riadok[3]=$riadok[4]="-";
				$riadok[5]=""; #medzera
				$riadok[6]="-";
				$riadok[7]="DIS";
				$riadok[8]="-/".$pocet;
			} else {
				$tbcas=$v[3]-$par[7];
				if ($tbcas<0) $tbcas=0;
				$tbtrat=$v[1]*5+$v[2]*5;
				$riadok[3]=sprintf("%1.2f",$tbtrat);
				$riadok[4]=sprintf("%1.2f",$tbcas);
				$riadok[5]=""; #medzera
				$riadok[6]=sprintf("%1.2f",$tbcas+$tbtrat);
				$v[4]=$tbtrat+$tbcas;
				if ($v[4]<6) {
					$riadok[7]="V";
				} elseif ($v[4]<16) {
					$riadok[7]="VD";
				} elseif ($v[4]<26) {
					$riadok[7]="D";
				} else {
					$riadok[7]="BO";
				}
				$riadok[8]=$poradie."./".$pocet;
			}
			$riadok[9]=$par[4];
			$riadok[10]=$par[4];
			$riadok[11]=$p[3]." ".$p[2]." + ".$p[5];
			$poradie++;
			$tab[]=$riadok;
		}

	}

	$pdf->VZTable($hlavicka,$tab);

}

function vzmsr() {
	global $G,$pdf;


	$pdf=new aPDF("P","mm","A4");
	$pdf->noheader=true;
	$pdf->AddFont('SkodaSans','','SkodaSansRg.php');
	$pdf->AddFont('SkodaSans','B','SkodaSansBd.php');
	$pdf->AddFont('SkodaSans','I','SkodaSansIt.php');
	$pdf->AddFont('SkodaSans','BL','SkodaSansBL.php');
	$pdf->AddFont('SkodaSans','BLI','SkodaSansBLI.php');
	$pdf->AddFont('SkodaSans','BI','SkodaSansBI.php');

	$pdf->SetMargins(5,12,5); # bolo 5 12 5
	
	#$pdf->AliasNbPages();

	#$pdf->nadpis=$G["nadpis"];

	$pdf->AddPage();
	$pdf->SetFont('skodasans','',8);

	$tab=array();

	foreach($G["behy"] as $beh) {

		$par=$beh;
		#print_r($beh);
		$vv=$G["vysledky"][$beh[0]];
		#if(strlen($par[2])==1) continue;
		if(!in_array($par[0],array(
		#"SOJS",
		#"SOJM",
		"SOJL",
		#"NOAS",
		#"NOAM",
		"NOAL"
		))) continue;
		echo "spracujem vysledky ".$par[0]." (".count($vv).")\n";
		
		$poradie=1;
		$pocet=count($vv);

		foreach($vv as $v) {
			if ($v[3]=="N") $pocet--;
		}

		foreach($vv as $v) {
			if ($v[3]=="N") continue;

			$riadok=array();

			$p=$G["startovka"][$v[0]];

			$riadok[0]=$par[3];
			$riadok[1]=iconv("windows-1250","utf-8",$G["nazovdovz"]);
			$riadok[1]=$G["nazovdovz"];
			#$riadok[2]=$par[2];
			$riadok[2]=substr($par[0],1,-1);
			if ($v[3]=="DIS") {
				$riadok[3]=$riadok[4]="-";
				$riadok[5]=""; #medzera
				$riadok[6]="-";
				$riadok[7]="DIS";
				$riadok[8]="-/".$pocet;
			} else {
				$tbcas=$v[3]-$par[7];
				if ($tbcas<0) $tbcas=0;
				$tbtrat=$v[1]*5+$v[2]*5;
				$riadok[3]=sprintf("%1.2f",$tbtrat);
				$riadok[4]=sprintf("%1.2f",$tbcas);
				$riadok[5]=""; #medzera
				$riadok[6]=sprintf("%1.2f",$tbcas+$tbtrat);
				$v[4]=$tbtrat+$tbcas;
				if ($v[4]<6) {
					$riadok[7]="V";
				} elseif ($v[4]<16) {
					$riadok[7]="VD";
				} elseif ($v[4]<26) {
					$riadok[7]="D";
				} else {
					$riadok[7]="BO";
				}
				$riadok[8]=$poradie."./".$pocet;
			}
			$riadok[9]=$par[4];
			$riadok[10]=$par[4];
			$riadok[11]=$p[3]." ".$p[2]." + ".$p[5];
			$poradie++;
			$tab[]=$riadok;
		}

	}

	$pdf->VZTable($hlavicka,$tab);

}

	$pdf=new aPDF("L","mm","A4");
	$pdf->AddFont('SkodaSans','','SkodaSansRg.php');
	$pdf->AddFont('SkodaSans','B','SkodaSansBd.php');
	$pdf->AddFont('SkodaSans','I','SkodaSansIt.php');
	$pdf->AddFont('SkodaSans','BL','SkodaSansBL.php');
	$pdf->AddFont('SkodaSans','BLI','SkodaSansBLI.php');
	$pdf->AddFont('SkodaSans','BI','SkodaSansBI.php');

	$pdf->AliasNbPages();

	$pdf->nadpis=$G["nadpis"];
	$pdf->garant=$G["garant"];



if ($behy[count($behy)-1]=="T") {
	echo "huh";
		$G["teamy"]=true;
		unset($behy[count($behy)-1]);
}



if ($argv[1]=="ATS") {
	$G["robATS"]=true;
	foreach($G["behy"] as $v)
		zrobpdf($v[0]);
	 #statistika();
	ats();
	$pdf->Output($G["dir"]."/print/ATS.pdf","F");
} elseif ($argv[1]=="ALL") {

	foreach($G["behy"] as $v) 
		zrobpdf($v[0]);
		#statistika();
	$pdf->Output($G["dir"]."/print/vysledky.pdf","F");
	system("evince \"".$G["dir"]."/print/vysledky.pdf\"");
} elseif ($argv[1]=="ALLMSR") {
	$b=array(
		"SOJS","SOJM","SOJL",
		array("SOAS","T"),
		array("SOAM","T"),
		array("SOAL","T"),
		array("NOJS","T"),
		array("NOJM","T"),
		array("NOJL","T"),
		"NOAS","NOAM","NOAL",
		"DruÅ¾stvÃ¡ SMALL"=>array("SOAS","NOJS","T"),
		"DruÅ¾stvÃ¡ MEDIUM"=>array("SOAM","NOJM","T"),
		"DruÅ¾stvÃ¡ LARGE"=>array("SOAL","NOJL","T"),
		"Jednotlivci SMALL"=>array("SOJS","NOAS"),
		"Jednotlivci MEDIUM"=>array("SOJM","NOAM"),
		"Jednotlivci LARGE"=>array("SOJL","NOAL"),
	);
	foreach($b as $k=>$v) {
		if (is_array($v) && count($v)>1) {
			if ($v[count($v)-1]=="T") {
				if (count($v)==2) {
					zrobpdfteamy($v[0]);
				} else {
					$v2=$v;
					unset($v2[count($v2)-1]);
					zrobteamysucet($v2,iconv("utf-8","windows-1250",$k));
				}
			} else {
				zrobsucet($v,iconv("utf-8","windows-1250",$k));
			}
		} else {
			zrobpdf($v);
		}
	}
		#statistika();
	$pdf->Output($G["dir"]."/print/vysledky.pdf","F");
	system("evince \"".$G["dir"]."/print/vysledky.pdf\"");
} elseif ($argv[1]=="SUCTY") {
	foreach($G["sucty"] as $k=>$v)
		zrobsucet(explode(",",$v),iconv("utf-8","windows-1250",$k));
	$pdf->Output($G["dir"]."/print/sucty.pdf","F");
	system("evince \"".$G["dir"]."/print/sucty.pdf\"");
} elseif ($argv[1]=="STAT") {
	statistika();
	$pdf->Output($G["dir"]."/print/statistika.pdf","F");
	system("evince \"".$G["dir"]."/print/statistika.pdf\"");
} elseif ($argv[1]=="VZ") {
	vz();
	$pdf->Output($G["dir"]."/print/VZ.pdf","F");
	system("evince \"".$G["dir"]."/print/VZ.pdf\"");
} elseif ($argv[1]=="VZMSR") {
	vzmsr();
	$pdf->Output($G["dir"]."/print/VZ.pdf","F");
	system("evince \"".$G["dir"]."/print/VZ.pdf\"");
} elseif ($argv[1]=="START") {
	startovka($argv[2]);
	$pdf->Output($G["dir"]."/print/startovka.pdf","F");
	system("evince \"".$G["dir"]."/print/startovka.pdf\"");
} elseif ($argv[1]=="STARTT") {
	startovkaTeams($argv[2]);
	$pdf->Output($G["dir"]."/print/startovka_druzstva.pdf","F");
	system("evince \"".$G["dir"]."/print/startovka_druzstva.pdf\"");
} elseif ($argv[1]=="PREZENCKA") {
	prezencka();
	$pdf->Output($G["dir"]."/print/prezencka.pdf","F");
	system("evince \"".$G["dir"]."/print/prezencka.pdf\"");
} elseif ($argv[1]=="BEHY") {
	behy();
} elseif ($argv[1]=="ZAPIS") {
	zapis();
	$pdf->Output($G["dir"]."/print/zapis.pdf","F");
	system("evince \"".$G["dir"]."/print/zapis.pdf\"");
} elseif (count($behy)==1) {
	if ($G["teamy"]) {
		zrobpdfteamy($behy[0]);
	} else {
		zrobpdf($behy[0]);
	}
	$beh=$behy[0];
	$pdf->Output($G["dir"]."/print/".$beh.($G["teamy"]?"_druzstva":"").".pdf","F");
	system("evince \"".$G["dir"]."/print/".$beh.($G["teamy"]?"_druzstva":"").".pdf\"");
} elseif ($argv[1]=="") {
	echo "\nTlacove zostavy casomiery\n\n";
	echo "pouzitie: vysledky [ZOSTAVA | [beh [beh [...]]]\n\n";
	echo "PREZENCKA\t\ttabulka pre prezentaciu\n";
	echo "START\t\tstastovna listina\n";
	echo "STARTT\t\tstastovna listina druzstiev\n";
	echo "ZAPIS\t\ttlaciva pre zapisovatelov\n";
	echo "ALL\t\tkompletne PDF s vysledkami\n";
	echo "SUCTY\t\tkompletne PDF s vysledkami suctov behov\n";
	echo "STAT\t\tstatistika prihlasenych\n";
	echo "ATS\t\tbody do ATS z pretekov\n\n";
	echo "BEHY\t\tvypise zoznam behov\n\n";
	echo "VZ\t\tlac do VZ\n\n";
	echo "VZMSR\t\tlac do VZ pre MSR\n\n";
	echo "beh\t\tvysledky behu\n";
	echo "beh1 beh2\tvysledky suctu behov\n\n";
} else {
	if ($G["teamy"]) {
		zrobteamysucet($behy);
	} else {
		zrobsucet($behy);
	}
	$pdf->Output($G["dir"]."/print/".join("_",$behy).($G["teamy"]?"_druzstva":"").".pdf","F");
	system("evince \"".$G["dir"]."/print/".join("_",$behy).($G["teamy"]?"_druzstva":"").".pdf\"");
}



















function pripocitaj($beh) {
	global $G;

	$par=$G["behy"][$beh];
	$vv=$G["vysledky"][$beh];


	foreach($vv as $k=>$v) {
		#var_export($v);
		$v[3]=str_replace(",",".",$v[3]);
		$v[4]=str_replace(",",".",$v[4]);
		if ($v[3]=="DIS" || $v[3]==100) {
			$v[3]=$v[4]=888;
		} elseif ($v[3]=="N" || $v[3]==200) {
			$v[3]=$v[4]=888;
		} else { 
			$v[4]=str_replace(",",".",$v[3])-$par[7];
			if ($v[4]<0) $v[4]=0;
			$v[5]=$v[4]; #tb cas
			$v[4]+=5*$v[1]+5*$v[2];
		}
		#var_export($v);
		$G["sucet"][$v[0]][0]=$v[0];
		$G["sucet"][$v[0]][3]+=$v[3];
		$G["sucet"][$v[0]][1]+=$v[1];
		$G["sucet"][$v[0]][2]+=$v[2];
		$G["sucet"][$v[0]][4]+=$v[4];
	}
}

function exportsucet() {
	global $G;
	
	$vv=$G["sucet"];	

$poradie=1;
	$out.="\"Por.\",\"Kat\",\"Priezvisko\",\"Meno\",\"Klub\",\"Meno psa\",\"Plemeno\",\"ch\",\"odm\",\"ï¿½as\",\"TB\"\n";

	usort($vv,"fci");

	foreach($vv as $v) {
		if (str_replace(",",".",$v[3])>0) {
			$out.=$poradie.",";
		} else {
			$out.="-,";
		}		
		$T=$G["startovka"][$v[0]];
		$out.=$T[0].","; #kat
		$out.=$T[3].","; #priezvisko
		$out.=$T[2].","; #meno
		$out.=$T[4].","; #klub
		$out.=$T[5].","; #meno psa
		$out.=$T[6].","; #plemeno
		$out.=$v[1].","; #chyb
		$out.=$v[2].","; #odm
		$out.="\"".str_replace(".",",",$v[3])."\","; #cas
		$out.="\"".str_replace(".",",",$v[4])."\""; #tb

		$out.="\n";
		$poradie++;
	}
	
	$o=fopen($G["dir"]."/print/".$G["subor"]."-print.csv","w");
	fputs($o,$out);
	fclose($o);
}
function fci($a,$b) {
	if ($a[4]>$b[4]) { #4 tb #3 cas
		return 1;
	} elseif ($a[4]<$b[4]) {
		return -1;
	} else {
	   if ($a[3]!="N" && $a[3]!="DIS") {
	   	if ($a[4]==$b[4]) {
				return (($a[3]>$b[3]) ? -1 : 1);
			} else {
				return (($a[3]<$b[3]) ? -1 : 1);
			}
	   } else
	   	return 0;	
	}
}

function sucetfci($a,$b) {
	if ($a["tb"]>$b["tb"]) {
		return 1;
	} elseif ($a["tb"]<$b["tb"]) {
		return -1;
	} else {
	   if ($a["cas"]!="N" && $a["cas"]!="DIS") {
			return (($a["cas"]<$b["cas"]) ? -1 : 1);
	   } else
	   	return 0;	
	}
}

function sucetbody($a,$b) {
	#echo "porovnavam: ";
	#print_r($a);print_r($b);
	if ($a["body"]<$b["body"]) {
		return 1;
	} elseif ($a["body"]>$b["body"]) {
		return -1;
	} else {
		if ($a["bodyA"]<$b["bodyA"]) {
			return 1;
		} elseif ($a["bodyA"]>$b["bodyA"]) {
			return -1;
		} else {
			if ($a["tb"]!=$b["tb"]) {
				return (($a["tb"]>$b["tb"]) ? 1 : -1);
			} else {
				return 0;
			}
		}
	}
}

?>
