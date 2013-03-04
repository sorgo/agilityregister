#!/usr/bin/php -q
<?php

error_reporting(E_ALL-E_NOTICE-E_WARNING);

@dl("php_gtk2.so");

include("konfiguracia.php");
include($G["dir"].".php");

$font="Arial Bold";
$font="Skoda Sans Bold";

@mkdir($G["dir"]."/print");

$G["body"]=array(25,20,17,14,12,10,9,8,7,6,5,4,3,2,1);
$G["bodyJ"]=array(18,15,13,12,11,10,9,8,7,6,5,4,3,2,1);

$cmd="mplayer scifi-online.mp3";
shell_exec("nohup $cmd > /dev/null 2> /dev/null & echo $!");

// define menu definition 
$menu_definition = array(
	//'_Nastavenie' => array('_Základné údaje', '_Behy', "_Súčty",'Š_tartovka'),
    //'_Tlač' => array("_Prezentácia","Š_tartovka",'Papiere pre _zapisovateľov',"<hr>","_Behy","_Súčty",'_Export'),
    '_Zobraz' => array("_Displej"),
    "_Režim" =>  array( array('A->A', 'A->B', 'B->A', 'B->B')),
    //'_Program' => array("_Koniec","_O programe"),
);

function getmicrotime(){
	list($usec, $sec) = explode(" ",microtime());
	return ((float)$usec + (float)$sec);
}

function grab_webcam($cas) {
	global $G;
	
	$cas2=floor($cas);
	$stotiny=str_pad(round(($cas-$cas2)*100),2,"0",STR_PAD_LEFT);
	#streamer -d -c /dev/video0 -o test.jpeg -f jpeg -j 75
#	$cmd="streamer -d -c ".$G["videodev"]." -o foto/agi".date("Y-m-d-H-i-s").".".$stotiny.".jpeg -f jpeg -j 75 -s 640x480";


#	echo "spustam ".$cmd."\n";
	#system($cmd);
	#shell_exec($cmd." &");
	#$p=popen($cmd,"r");


#	shell_exec("nohup $cmd > /dev/null 2> /dev/null & echo $!");
}

function pipni() {
	$cmd="mplayer scifi-beep-low.mp3";
	shell_exec("nohup $cmd > /dev/null 2> /dev/null & echo $!");
}

function hovor($cislo) {
   $stovky=floor($cislo/100);
   $desiatky=floor(($cislo-$stovky*100)/10);
   $jednotky=floor($cislo-$stovky*100-$desiatky*10);
   $desatina=round(($cislo-$stovky*100-$desiatky*10-$jednotky)*100);
   if ($stovky>0) $t=($stovky*100)." ";
   if ($desiatky==1) {
   	$t.=($desiatky*10+$jednotky)." ";
   } elseif($desiatky==0 && $jednotky==1) {
   	$t.="jedna ";
   } elseif($desiatky==0 && $jednotky==2) {
   	$t.="dve ";
   } else {
   	if ($desiatky>0) $t.=($desiatky*10)." ";
   	if ($jednotky>0 || $desiatky==0) $t.=($jednotky+0)." ";
   }
	if (false)  	
  	switch($desiatky*10+$jednotky){
		case 1:
			$t.="celá ";
			break;
		case 2:
		case 3:
		case 4:
			$t.="celé ";
			break;
  		default:
			$t.="celých ";
  	}
   if ($desatina>9 && $desatina<21) {
   	$t.=($desatina+0)." ";
   } elseif ($desatina>0) {
   	$t.=(floor($desatina/10)*10+0)." ";
   	if (($desatina-floor($desatina/10)*10)>0)
   	$t.=(($desatina-floor($desatina/10)*10)+0)." ";
   } else {
   	$t.="presne ";
   }

	$cmd="espeak -v sk -s 130 -p 40 \"   ".str_replace(","," ",$t)."  \"";
	shell_exec("nohup $cmd > /dev/null 2> /dev/null & echo $!");	
}

function _hovor($cislo) {
   $stovky=floor($cislo/100);
   $desiatky=floor(($cislo-$stovky*100)/10);
   $jednotky=floor($cislo-$stovky*100-$desiatky*10);
   $desatina=round(($cislo-$stovky*100-$desiatky*10-$jednotky)*100);
   if ($stovky>0) $cmd="sound/".($stovky*100).".wav ";
   if ($desiatky==1) {
   	$cmd.="sound/".($desiatky*10+$jednotky).".wav ";
   } elseif($desiatky==0 && $jednotky==1) {
   	$cmd.="sound/1b.wav ";
   } elseif($desiatky==0 && $jednotky==2) {
   	$cmd.="sound/2b.wav ";
   } else {
   	if ($desiatky>0) $cmd.="sound/".($desiatky*10).".wav ";
   	if ($jednotky>0 || $desiatky==0) $cmd.="sound/".($jednotky+0).".wav ";
   }
	if (false)  	
  	switch($desiatky*10+$jednotky){
		case 1:
			$cmd.="sound/cela.wav ";
			break;
		case 2:
		case 3:
		case 4:
			$cmd.="sound/cele.wav ";
			break;
  		default:
			$cmd.="sound/celych.wav ";
  	}
   if ($desatina>9 && $desatina<21) {
   	$cmd.="sound/".($desatina+0).".wav ";
   } elseif ($desatina>0) {
   	$cmd.="sound/".(floor($desatina/10)*10+0).".wav ";
   	if (($desatina-floor($desatina/10)*10)>0)
   	$cmd.="sound/".(($desatina-floor($desatina/10)*10)+0).".wav ";
   } else {
   	$cmd.="sound/0.wav sound/0.wav";
   }
	$cmd="mplayer ".$cmd;
	#echo $cmd."\n";
	shell_exec("nohup $cmd > /dev/null 2> /dev/null & echo $!");
}

function zobraz_cas($cas,$text=false) {
	global $G;

	if ($text) {
		$G["progress"]->set_text($cas);
		$G["cast"]->set_text($cas);
		$G["displejcas"]->set_text($cas);
	} else { 
		$sekundy=floor($cas);
		$stotiny=round(($cas-$sekundy)*100);
		$stotiny=str_pad($stotiny, 2, STR_PAD_LEFT, '0'); // note 2 
		$sekundy=str_pad($sekundy, 3, STR_PAD_LEFT, '0'); // note 2 	   	
   	$G["progress"]->set_text($sekundy.".".$stotiny);
   	$G["cast"]->set_text($sekundy.".".$stotiny);
		$G["displejcas"]->set_text($sekundy.".".$stotiny);
	}
	$ch=$G["chyb"]->get_text();
	$odm=$G["odm"]->get_text();
	$beh=$G["beh"]->get_label();
	if ($odm>2) {
		$tb="DIS";
	} elseif ($cas>$G["behy"][$beh][8]) {
		$tb="DIS";
#		var_export($G["behy"][$beh]);
	} elseif ($G["disk"]->get_label()=="*** DISK ***") {
		$tb="DIS";
	} elseif ($G["nest"]->get_label()=="*** NEST ***") {
		$tb="N";
	} else {
		$tb=$cas-$G["behy"][$beh][7];
		if ($tb<0) $tb=0;
		$tb+=5*$ch+5*$odm;
		#var_export($G["behy"][$beh]);
		$tb=sprintf("%1.2f",$tb);
		$tb=str_replace(",",".",$tb);
	}
	$G["displejchyby"]->set_label($ch);
	$G["displejodm"]->set_label($odm);
	$G["displejtb"]->set_label($tb);
}

$G["limit"]=3; #ochranny limit pred viacnasobnym spustenim v sekundach
$G["log"]=fopen("logy/".date("Y-m-d-H-i-s").".log","w");
$G["js"]=fopen("/dev/input/js0","rbw+");
$G["videodev"]="/dev/video1";
#echo "otvaram js port\n";
$G["senzory"]["AA"]=array("1:6");
$G["senzory"]["AB"]=array("1:6","1:7");
$G["senzor3"]="2:2"; #prepinanie rezimu
$G["rezim"]="AA";
$G["senzor1"]=$G["senzor2"]=$G["senzory"]["AA"][0];
$G["historia"]=array(
	0=>"-",
	1=>"-",
	2=>"-",
	3=>"-",
	4=>"-"
);


$G["bezim"]=false;

$f=fopen($G["dir"]."/startovka.csv","r");
while ($l=fgetcsv($f,1000)) {
	if (substr($l[0],0,1)!="#")
		$G["startovka"][$l[1]]=$l;
}
$f=fopen($G["dir"]."/behy.csv","r");
while ($l=fgetcsv($f,1000)) {
	$G["behy"][$l[0]]=$l;
}

foreach($G["behy"] as $k => $v) {
	$f=@fopen($G["dir"]."/".$k.".csv","r");
	if($f)
	while ($l=fgetcsv($f,1000)) {
		$G["vysledky"][$k][$l[0]]=$l;
	}
}

$G["timy"]=array();	
$f=fopen($G["dir"]."/timy.csv","r");
while ($l=fgetcsv($f,1000)) {
	if (substr($l[0],0,1)=="#") continue;
	$G["timy"][$l[0]]=array("name"=>$l[1], "teams"=>array($l[2],$l[3],$l[4]));
}
#print_r($G["timy"]);

#var_export($G["vysledky"]);
#exit;


$window2 = new GtkWindow();
$window2->set_title('displej');
$window2->set_size_request(780, 550);
//$window2->connect_simple('destroy', array('Gtk','main_quit'));
$window2->add($v2box = new GtkVBox());
$window2->modify_bg(Gtk::STATE_NORMAL, GdkColor::parse("#ffffff"));

$G["displejmeno"] = new GtkLabel('-');
$G["displejmeno"]->modify_font(new PangoFontDescription($font."  25"));
$G["displejmeno"]->modify_fg(Gtk::STATE_NORMAL, GdkColor::parse("#800000"));
$v2box->pack_start($G["displejmeno"],1,1,3);
$G["displejcas"] = new GtkLabel('000.00');
$G["displejcas"]->modify_font(new PangoFontDescription($font."  170"));
$G["displejcas"]->modify_fg(Gtk::STATE_NORMAL, GdkColor::parse("#800000"));
$v2box->pack_start($G["displejcas"],1,1);
$v2box->pack_start($h3box = new GtkHBox(), 1, 1);
$h3box->pack_start($chnapis=new GtkLabel("CHYBY / ODM"), 1);
$chnapis->modify_font(new PangoFontDescription($font."  25"));
$chnapis->modify_fg(Gtk::STATE_NORMAL, GdkColor::parse("#800000"));
$h3box->pack_start($chnapis3=new GtkLabel("TB"), 1);
$chnapis3->modify_font(new PangoFontDescription($font."  25"));
$chnapis3->modify_fg(Gtk::STATE_NORMAL, GdkColor::parse("#800000"));
$v2box->pack_start($h2box = new GtkHBox(), 1, 1);
$h2box->pack_start($G["displejchyby"]=new GtkLabel("0"), 1);
$G["displejchyby"]->modify_font(new PangoFontDescription($font."  85"));
$G["displejchyby"]->modify_fg(Gtk::STATE_NORMAL, GdkColor::parse("#800000"));
$h2box->pack_start($G["displejodm"]=new GtkLabel('0'), 1);
$G["displejodm"]->modify_font(new PangoFontDescription($font."  85"));
$G["displejodm"]->modify_fg(Gtk::STATE_NORMAL, GdkColor::parse("#800000"));
$h2box->pack_start($G["displejtb"]=new GtkLabel('0.00'), 1);
$G["displejtb"]->modify_font(new PangoFontDescription($font."  85"));
$G["displejtb"]->modify_fg(Gtk::STATE_NORMAL, GdkColor::parse("#800000"));


$window = new GtkWindow();
$window->set_title($argv[0]);
$window->set_size_request(750, 550);
$window->connect_simple('destroy', array('Gtk','main_quit'));
$window->add($vbox = new GtkVBox());
$window->modify_bg(Gtk::STATE_NORMAL, GdkColor::parse("#ffffff"));

$window->connect_after('key-press-event', 'onKeyPress'); 

function onKeyPress($widget, $event) { 
  global $righthand, $lefthand; 
  if (get_class($widget)=="GtkWindow") {
  	echo "Pressed: " . $event->keyval; 
	}
  return true; 
}

$G["rezimdisplej"]=new GtkLabel('A->A');
$G["sensorstatus1"]=new GtkLabel('[ ]');
$G["sensorstatus2"]=new GtkLabel('[ ]');
setup_menu($vbox, $menu_definition);
$vbox->pack_start($hbox = new GtkHBox(), 0, 0);
$hbox->pack_start($G["rezimdisplej"], 0);
$hbox->pack_start($G["sensorstatus1"], 0);
$hbox->pack_start($G["sensorstatus2"], 0);
$hbox->pack_start($button2 = new GtkButton('START'), 1, 0);
$hbox->pack_start($posledny=new GtkLabel('000.00'), 0);
$button2->connect('clicked', 'stop');

$G["progress"] = new GtkLabel('000.00');
$G["progress"]->modify_font(new PangoFontDescription($font."  145"));
$G["progress"]->modify_fg(Gtk::STATE_NORMAL, GdkColor::parse("#800000"));
$vbox->pack_start($G["progress"]);
$vbox->pack_start($hbox4 = new GtkHBox(), 0, 0);
$hbox4->pack_start(new GtkLabel('BEH:'),0,1);
$hbox4->pack_start($buttonbp = new GtkButton('      <<<      '),0,1);
reset($G["behy"]);
$cb=current($G["behy"]);
$hbox4->pack_start($G["beh"] = new GtkLabel($cb[0]),1,1);
$hbox4->pack_start($buttonbd = new GtkButton('      >>>      '),0,1);
$buttonbp->connect('clicked', 'buttonbp');
$buttonbd->connect('clicked', 'buttonbd');
$hbox4->pack_start($buttonbexport = new GtkButton('Uložiť výsledky'),0,1);
$buttonbexport->connect("clicked","exportcsv");
$vbox->pack_start($hbox3 = new GtkHBox(), 0, 0);
$hbox3->pack_start(new GtkLabel('TÍM:'),0,1);
$hbox3->pack_start($buttonmp = new GtkButton('      <<<      '),0,1);
$hbox3->pack_start($G["cislo"] = new GtkLabel('0'),0,1);
$hbox3->pack_start($G["meno"] = new GtkLabel('Meno'),1,1);
$hbox3->pack_start($buttonmd = new GtkButton('      >>>      '),0,1);
$buttonmp->connect('clicked', 'buttonmp');
$buttonmd->connect('clicked', 'buttonmd');
$vbox->pack_start($G["cast"] = new GtkEntry("000.00"),0, 0);
$vbox->pack_start($hbox5 = new GtkHBox(), 0, 0);
$hbox5->pack_start(new GtkLabel('CH'));
$hbox5->pack_start($G["chyb"]=new GtkLabel('0'));
$hbox5->pack_start($chplus=new GtkButton('_CH+'));
$hbox5->pack_start($chminus=new GtkButton('CH-'));
$chplus->connect('clicked', 'chplus');
$chminus->connect('clicked', 'chminus');
$vbox->pack_start($hbox6 = new GtkHBox(), 0, 0);
$hbox6->pack_start(new GtkLabel('ODM'));
$hbox6->pack_start($G["odm"]=new GtkLabel('0'));
$hbox6->pack_start($oplus=new GtkButton('_O+'));
$hbox6->pack_start($ominus=new GtkButton('O-'));
$oplus->connect('clicked', 'oplus');
$ominus->connect('clicked', 'ominus');
$vbox->pack_start($hbox2 = new GtkHBox(), 0, 0);
$hbox2->pack_start($G["disk"]=new GtkButton('_DISK'));
$hbox2->pack_start($G["nest"]=new GtkButton('_NEST'));
$hbox2->pack_start($potvrd=new GtkButton('OK'));

$potvrd->connect('clicked', 'potvrd');

$G["disk"]->connect('clicked','disk_toggle');
$G["nest"]->connect('clicked','nest_toggle');

function exportcsv() {
	global $G;
	
	$beh=$G["beh"]->get_label();

	echo "exportujem csv\n";
	echo $beh."\n";	
	
	$G['abeh']=$beh;
	
	#var_export($G["vysledky"][$beh]);
	$par=$G["behy"][$beh];
	#var_export($par);
	$vv=$G["vysledky"][$beh];
	foreach($vv as $k=>$v) {
		if ($v[3]=="DIS" || $v[3]==100) {
			$vv[$k][4]=888;
		} elseif ($v[3]=="N" || $v[3]==200) {
			$vv[$k][4]=999;
		} else { 
			$vv[$k][4]=str_replace(",",".",$v[3])-$par[7];
			if ($vv[$k][4]<0) $vv[$k][4]=0;
			$vv[$k][4]+=5*$v[1]+5*$v[2];
		}
	}
#	var_export($vv);
	usort($vv,"fci");
#	var_export($vv);
#	$vv[]="";

	$out="";
	$poradie=1;
	foreach($vv as $v) {
		$body=0;
		if (strpos($beh,"J")>0) {
			$body=$G["bodyJ"][$poradie-1]+0;
		} else {
			$body=$G["body"][$poradie-1]+0;
		}
		if ($v[4]>100) $body=0;
		$out.=$v[0].",".($v[1]+0).",".($v[2]+0).",\"".$v[3]."\",".($body+0)."\n";
		$poradie++;
	}
#$out=serialize($vv);
	$o=fopen($G["dir"]."/".$beh.".csv","w");
	fputs($o,$out);
	fclose($o);
/*

	$out=$par[1]."\n";	
	$out.="Dátum:,".$par[3]."\n";	
	$out.="Rozhodca:,".$par[4]."\n";	
	$out.="Dĺžka parkúru:,".$par[5]." m\n";	
	$out.="Počet prekážok:,".$par[6]."\n";	
	$out.="Štandardný čas:,".$par[7]." s\n";	
	$out.="Maximálny čas:,".$par[8]." s\n";	
	$out.="Počet tímov:,".count($vv)."\n\n";	

$poradie=1;
	$out.="\"Por.\",\"Kat\",\"Priezvisko\",\"Meno\",\"Klub\",\"Meno psa\",\"Plemeno\",\"ch\",\"odm\",\"čas\",\"TB\",\"post\",\"hodn\"\n";

	foreach($vv as $v) {
		$v[3]=str_replace(",",".",$v[3]);
		if ($v[3]!="N" && $v[3]!="DIS") {
			$v[3]=$v[3]+0;
		}
		if (str_replace(",",".",$v[3])>0) {
			$out.=$poradie.",";
		} else {
			$out.="-,";
		}		
		$T=$G["startovka"][$v[0]];
		$out.=$T[0].","; #kat
		$out.=$T[3].","; #priezvisk 
		$out.=$T[2].","; #meno
		$out.=$T[4].","; #klub
		$out.=$T[5].","; #meno psa
		$out.=$T[6].","; #plemeno
		$out.=($v[1]+0).","; #chyb
		$out.=($v[2]+0).","; #odm
		$out.=str_replace(",",".",$v[3]).","; #cas

	switch($v[4]) {
		case 100:
		case 200:
			$out.="-,-,".$v[3];
			break;
		default:
			$out.="\"".str_replace(",",".",$v[4])."\","; #TB		
			$out.="\"".sprintf("%1.2f",$par[5]/$v[3])."\",";
			if ($v[4]<6) {
				$out.="V";
			} elseif ($v[4]<16) {
				$out.="VD";
			} elseif ($v[4]<26) {
				$out.="D";
			} else {
				$out.="BO";
			}
			
	}


		$out.="\n";
		$poradie++;
	}
	
	$o=fopen($G["dir"]."/print/".$beh."-print.csv","w");
	fputs($o,$out);
	fclose($o);
	*/

$G["startovka"]=array();	
$f=fopen($G["dir"]."/startovka.csv","r");
while ($l=fgetcsv($f,1000)) {
	if (substr($l[0],0,1)!="#")
		$G["startovka"][$l[1]]=$l;
}
$G["behy"]=array();	
$f=fopen($G["dir"]."/behy.csv","r");
while ($l=fgetcsv($f,1000)) {
	$G["behy"][$l[0]]=$l;
}

$G["timy"]=array();	
$f=fopen($G["dir"]."/timy.csv","r");
while ($l=fgetcsv($f,1000)) {
	$G["timy"][$l[0]]=array("name"=>$l[1], "teams"=>array($l[2],$l[3],$l[4]));
}
print_r($G["timy"]);
while(key($G["behy"])!=$G["abeh"]){
	next($G["behy"]);

}

}
function fci($a,$b) {
	if ($a[4]>$b[4]) { #4 tb #3 cas
		return 1;
	} elseif ($a[4]<$b[4]) {
		return -1;
	} else {
	   if ($a[3]!="N" && $a[3]!="DIS") {
	   	if ($a[4]==$b[4] && $b[4]>0 && false) {
				return (($a[3]>$b[3]) ? -1 : 1);
			} else {
				return (($a[3]<$b[3]) ? -1 : 1);
			}
	   } else
	   	return 0;	
	}
}

function chplus() {
	global $G;
	$G["chyb"]->set_text($G["chyb"]->get_text()+1);
	pocitajtb();	
}
function chminus() {
	global $G;
	if ($G["chyb"]->get_text()>0) {
		$G["chyb"]->set_text($G["chyb"]->get_text()-1);	
		pocitajtb();
	}	
}
function oplus() {
	global $G;
	$G["odm"]->set_text($G["odm"]->get_text()+1);
	pocitajtb();	
}
function ominus() {
	global $G;
	if ($G["odm"]->get_text()>0) {
		$G["odm"]->set_text($G["odm"]->get_text()-1);	
		pocitajtb();
	}	
}
function pocitajtb() {
	#prepocet tb podla st. casu a chyb
}
load_startovka($cb[0]);
$p=current($G["astartovka"]);
show_vysledky($G['beh']->get_text(),$p[1]);

$window->show_all();


function show_display() {
	global $window2, $G;
	$window2->show_all();
}


$timeout_ID = Gtk::timeout_add(10, 'process_task');

Gtk::main();

function buttonbp() {
	global $G;
	
	if (prev($G["behy"])) {
		$b=current($G["behy"]);
		$G["beh"]->set_text($b[0]);
		load_startovka($b[0]);
	} else {
	 reset($G["behy"]);
		$b=current($G["behy"]);
		$G["beh"]->set_text($b[0]);
		load_startovka($b[0]);
	}
}
function buttonbd() {
	global $G;
	
	if (next($G["behy"])) {
		$b=current($G["behy"]);
		$G["beh"]->set_text($b[0]);
		load_startovka($b[0]);
	} else {
	 reset($G["behy"]);
		$b=current($G["behy"]);
		$G["beh"]->set_text($b[0]);
		load_startovka($b[0]);
	}
}

function buttonmp() {
	global $G;
	
	if (prev($G["astartovka"])) {
		$p=current($G["astartovka"]);
		set_meno($p[1]);
		show_vysledky($G['beh']->get_text(),$p[1]);
	} else {
	 reset($G["astartovka"]);
		$p=current($G["astartovka"]);
		set_meno($p[1]);
		show_vysledky($G['beh']->get_text(),$p[1]);
	}
}
function show_vysledky($beh,$id) {
	global $G;
	#echo "v ".$beh." ".$id."\n";
	$G["chyb"]->set_text($G["vysledky"][$beh][$id][1]);
	$G["odm"]->set_text($G["vysledky"][$beh][$id][2]);
	$G["cast"]->set_text($G["vysledky"][$beh][$id][3]);
	if($G["vysledky"][$beh][$id][3]=="DIS") {
		$G["disk"]->set_label("*** DISK ***");
		$G["nest"]->set_label("NEST");
	} elseif($G["vysledky"][$beh][$id][3]=="N") {
		$G["disk"]->set_label("DISK");
		$G["nest"]->set_label("*** NEST ***");
	} else {
		$G["disk"]->set_label("DISK");
		$G["nest"]->set_label("NEST");
	}
}
function potvrd() {
	global $G;
	
	$beh=$G["beh"]->get_label();
	$id=$G["cislo"]->get_label();
	
	#echo "zapis ".$beh." ".$id."\n";
	#var_export($G["vysledky"][$beh][$id]);
	$cas=$G["cast"]->get_text();
	if ($G["disk"]->get_label()=="*** DISK ***") $cas="DIS";
	if ($G["nest"]->get_label()=="*** NEST ***") $cas="N";
	$G["vysledky"][$beh][$id]=array(
		0=>$id,
		1=>$G["chyb"]->get_text(),
		2=>$G["odm"]->get_text(),
		3=>$cas
	);
	#var_export($G["vysledky"][$beh][$id]);
}
function disk_toggle() {
	global $G;
	
	$G["disk"]->set_label(($G["disk"]->get_label()=="DISK"?"*** DISK ***":"DISK"));
}
function nest_toggle() {
	global $G;
	
	$G["nest"]->set_label(($G["nest"]->get_label()=="NEST"?"*** NEST ***":"NEST"));
}

function buttonmd() {
	global $G;
	
	if (next($G["astartovka"])) {
		$p=current($G["astartovka"]);
		set_meno($p[1]);
		show_vysledky($G['beh']->get_text(),$p[1]);
	} else {
	 reset($G["astartovka"]);
		$p=current($G["astartovka"]);
		set_meno($p[1]);
		show_vysledky($G['beh']->get_text(),$p[1]);
	}
}


function load_startovka($beh) {
	global $G;
	
	$filter=$G["behy"][$beh][2];
	#echo "filter: ".$filter."\n";
	$G["astartovka"]=array();
	if(strlen($filter)==1) {
		$G["astartovka"]=array();
		foreach($G["startovka"] as $k=>$v) {
			if (substr($v[0],0,1)==$filter) {
				$G["astartovka"][$v[1]]=$v;
			}
		}
	} elseif (strpos($filter,",")>0) {
		$f2=explode(",",$filter);
		#var_export($f2);
		foreach($G["startovka"] as $k=>$v) {
			if (in_array($v[0],$f2)) {
				$G["astartovka"][$v[1]]=$v;
			}
		}
	} else {
		$G["astartovka"]=array();
		foreach($G["startovka"] as $k=>$v) {
		print_r($v);
			if ($v[0]==$filter) {
				$G["astartovka"][$v[1]]=$v;
			}
		}
	}
	#var_export($G["astartovka"]);
	$p=current($G["astartovka"]);
	set_meno($p[1]);
	show_vysledky($beh,$p[1]);
}
function set_meno($c) {
	global $G;
	#echo "set meno ".$c."\n";
	$G["cislo"]->set_text($c);
	$G["meno"]->set_text($G["startovka"][$c][3]." ".$G["startovka"][$c][2]."+".$G["startovka"][$c][5]);	
	$G["displejmeno"]->set_text($c." ".$G["startovka"][$c][3]." ".$G["startovka"][$c][2]."\n".$G["startovka"][$c][5]);
	$G["displejmeno"]->set_alignment(.5,0);	
	#$G["displejmeno"]->set_text($c." ".$G["startovka"][$c][2]." ".$G["startovka"][$c][3]."+".$G["startovka"][$c][5]);	
}

function set_rezim($rezim) {
	global $G;

	$G["rezimdisplej"]->set_text($rezim);
	switch($rezim) {
		case "A->A":
			$G["senzor1"]=$G["senzor2"]=$G["senzory"]["AA"][0];
			break;
		case "A->B":
			$G["senzor1"]=$G["senzory"]["AB"][0];
			$G["senzor2"]=$G["senzory"]["AB"][1];
			break;
		case "B->A":
			$G["senzor1"]=$G["senzory"]["AB"][1];
			$G["senzor2"]=$G["senzory"]["AB"][0];
			break;
		case "B->B":
			$G["senzor1"]=$G["senzor2"]=$G["senzory"]["AB"][1];
			break;
	}
}

function on_button($button) {
	global $G;
					if ($G["rezim"]=="AA") {
						#echo "A->B";
						$button->set_label("A->B");
						$G["senzor1"]=$G["senzory"]["AB"][0];
						$G["senzor2"]=$G["senzory"]["AB"][1];
						$G["rezim"]="AB";
					} elseif($G["rezim"]=="AB") {
						#echo "B->A";
						$button->set_label("B->A");
						$G["senzor1"]=$G["senzory"]["AB"][1];
						$G["senzor2"]=$G["senzory"]["AB"][0];
						$G["rezim"]="BA";
					} elseif($G["rezim"]=="BA") {
						#echo "B->B";
						$button->set_label("B->B");
						$G["senzor1"]=$G["senzor2"]=$G["senzory"]["AB"][1];
						$G["rezim"]="BB";
					} else {
						#echo "A->A";
						$button->set_label("A->A");
						$G["senzor1"]=$G["senzor2"]=$G["senzory"]["AA"][0];
						$G["rezim"]="AA";
					}
}

function stop() {
	global $G,$button2;

	if ($G["bezim"]){
					$button2->set_label("START");
					$cielcas=getmicrotime();
					$cas=$cielcas-$G["startcas"];
					$G["bezim"]=false;
				pipni();				
				hovor($cas);
					fputs($G["log"],date("Y-m-d-H-i-s")."\tručné vypnutie\n");
					zobraz_cas($cas);
	} else {
					$button2->set_label("STOP");
					$G["startcas"]=getmicrotime();
					$G["bezim"]=true;
	}
}

function process_task() {
    global $start_time,$G;
/*
	if ($G["js"]) {
		$G["stav"]->set_label('spojene');
	} else {
		$G["stav"]->set_label('ODPOJENE');
	}
 */

	$read=array($G["js"]); 
	$write=$except=NULL;

	if (false === ($num_changed_streams=@stream_select($read,$write,$except,0,10000))) {
		#echo "CHYBA\n";
		$num_changed_streams=1;
		
		
		if ($G["bezim"]) {
			$acas=getmicrotime();
			$jcas=$cas;
			$cas=$acas-$G["startcas"];
			#echo $G["rezim"]." CAS: ".sprintf("%1.2f",$cas)."            \r";
			$sekundy=floor($cas);
			$stotiny=floor(($cas-$sekundy)*10);
			$stotiny=str_pad($stotiny, 1, STR_PAD_LEFT, '0'); // note 2 
			$sekundy=str_pad($sekundy, 3, STR_PAD_LEFT, '0'); // note 2 
			if ($G["z"]==5) {
				zobraz_cas($sekundy.".".$stotiny."0",true);
				#echo $sekundy.".".$stotiny."X\r";
				$G["z"]=0;
			}
			$G["z"]++;
	   	#global $progress;
   	 	#$progress->set_text($sekundy.":".$stotiny);
		}
		
	} elseif ($num_changed_streams>0) {
		#echo "m\n";
		for($i2=0;$i2<$num_changed_streams;$i2++) {
			if ($read[$i2]==$G["js"]) {
				#echo "mam js\n";
				$buf=fread($G["js"],8);
				for($i=0;$i<strlen($buf);$i++) {
					$bd[$i]=ord($buf[$i]);
				}
		
				for($i=0;$i<8;$i++) {	
					echo sprintf("%3d",$bd[$i])."|";
				}
				echo "\n";
				$cas=$bd[3]*256*256*256+$bd[2]*256*256+$bd[1]*256+$bd[0];
				$senzorid=$bd[6].":".$bd[7];
				#echo " cas: ".$cas."\n";
				#echo "\n";
				$acas=getmicrotime();
				$cas1=$acas-$G["startcas"];
				
				if ($senzorid==$G["senzory"]["AB"][0]) {
					$G["sensorstatus1"]->set_text($bd[4]==1 ? "[*]" : "[ ]");
				} elseif ($senzorid==$G["senzory"]["AB"][1]) {
					$G["sensorstatus2"]->set_text($bd[4]==1 ? "[*]" : "[ ]");
				}
				
				if ($senzorid==$G["senzor1"] && $bd[4]==1 && $G["bezim"]==false && $cas1>$G["limit"]) {
					#start casomiery					
					$G["startcas"]=getmicrotime();
					$G["startcas2"]=$cas;
					$G["bezim"]=true;
					fputs($G["log"],date("Y-m-d-H-i-s")."\tstart\n");
					global $posledny;
					for($i=0;$i<count($G["historia"]);$i++) {
						if ($i==count($G["historia"])-1) {
							$G["historia"][$i]=$G["progress"]->get_text();
						} else {
							$G["historia"][$i]=$G["historia"][$i+1];
						}
					}
					$posledny->set_text(join(" ; ",$G["historia"]));
					global $button2;
					$button2->set_label("STOP");
					pipni();
					grab_webcam($G["startcas"]);
				} elseif ($senzorid==$G["senzor2"] && $bd[4]==1 && $G["bezim"]==true && $cas1>$G["limit"]) {
					$cielcas=getmicrotime();
					$cielcas2=$cas;
					$cas=$cielcas-$G["startcas"];
					#echo "CAS: ".sprintf("%1.2f",$cas)."\n";
					$cas2=($cielcas2-$G["startcas2"])/1000;
					#echo "CAS2: ".sprintf("%1.2f",$cas)."\n";
					fputs($G["log"],date("Y-m-d-H-i-s")."\t".$cas2."\n");
					global $button2;
					$button2->set_label("START");
					$G["bezim"]=false;
					$G["startcas"]=$cielcas;
					zobraz_cas($cas2);
					pipni();
					hovor($cas2);
					grab_webcam($cielcas);
				} elseif(($senzorid==$G["senzor1"] || $senzorid==$G["senzor2"]) && $bd[4]==1 && $cas1>$G["limit"]) {
					#ak mam signal, zaznamenam kameru
	//				pipni();
					grab_webcam(getmicrotime());
				}
			}
		}
	} else {
		#echo "x";
		if ($G["bezim"]) {
			$acas=getmicrotime();
			$jcas=$cas;
			$cas=$acas-$G["startcas"];
			#echo $G["rezim"]." CAS: ".sprintf("%1.2f",$cas)."            \r";
			$sekundy=floor($cas);
			$stotiny=floor(($cas-$sekundy)*10);
			$stotiny=str_pad($stotiny, 1, STR_PAD_LEFT, '0'); // note 2 
			$sekundy=str_pad($sekundy, 3, STR_PAD_LEFT, '0'); // note 2 
			if ($G["z"]==5) {
				zobraz_cas($sekundy.".".$stotiny."0",true);
				#echo $sekundy.".".$stotiny."X\r";
				$G["z"]=0;
			}
			$G["z"]++;
	   	#global $progress;
   	 	#$progress->set_text($sekundy.":".$stotiny);
		}

	}
    while (Gtk::events_pending()) {Gtk::main_iteration();}
    return true;
}






function uprav_startovku() {
	global $G,$model,$view;

	$dialog = new GtkDialog(); // note 1
	$viewport1 = new GtkViewPort();
	$dialog->set_title('Úprava štartovnej listiny');
	$dialog->set_position(GTK::WIN_POS_CENTER);
	$dialog->set_default_size(1024,420);

/*
kat,
c.
meno
priezvisko
klub
meno psa
plemeno
pp
krajina
VZ
*/

	$model = new GtkListStore(
		Gobject::TYPE_STRING,
		Gobject::TYPE_STRING,
		Gobject::TYPE_STRING,
		Gobject::TYPE_STRING,
		Gobject::TYPE_STRING,
		Gobject::TYPE_STRING,
		Gobject::TYPE_STRING,
		Gobject::TYPE_BOOLEAN,
		Gobject::TYPE_STRING,
		Gobject::TYPE_STRING
);
	
	$view = new GtkTreeView($model);
#	$view->set_grid_lines(Gtk::TREE_VIEW_GRID_LINES_BOTH); 
	
	$column1 = new GtkTreeViewColumn('Kat');
	$column2 = new GtkTreeViewColumn('Št.Č.');
	$column3 = new GtkTreeViewColumn('Meno');
	$column4 = new GtkTreeViewColumn('Priezvisko');
	$column5 = new GtkTreeViewColumn('Klub');
	$column6 = new GtkTreeViewColumn('Meno psa');
	$column7 = new GtkTreeViewColumn('Plemeno');
	$column8 = new GtkTreeViewColumn('PP');
	$column9 = new GtkTreeViewColumn('Krajina');
	$column10 = new GtkTreeViewColumn('Číslo VZ');
	
	$view->append_column($column1);
	$view->append_column($column2);
	$view->append_column($column3);
	$view->append_column($column4);
	$view->append_column($column5);
	$view->append_column($column6);
	$view->append_column($column7);
	$view->append_column($column8);
	$view->append_column($column9);
	$view->append_column($column10);
	
	$cell_renderer1 = new GtkCellRendererCombo();
	$cell_renderer2 = new GtkCellRendererText();
	$cell_renderer3 = new GtkCellRendererText();
	$cell_renderer4 = new GtkCellRendererText();
	$cell_renderer5 = new GtkCellRendererText();
	$cell_renderer6 = new GtkCellRendererText();
	$cell_renderer7 = new GtkCellRendererText();
	$cell_renderer8 = new GtkCellRendererToggle();
	$cell_renderer9 = new GtkCellRendererText();
	$cell_renderer10 = new GtkCellRendererText();

	$category = new GtkListStore(Gobject::TYPE_STRING); // note 2
	$list = array('SA0','SA1','SA2','SA3','MA0','MA1','MA2','MA3','LA0','LA1','LA2','LA3');
	foreach($list as $choice) {
	    $category->append(array($choice)); //n4 note3
	}
	$cell_renderer1->set_property('model', $category); // note 4
	$cell_renderer1->set_property('text-column', 0); // note 5
	$cell_renderer1->set_property('editable', true); // note 6
	$cell_renderer1->set_property('has-entry', true); // note 7 
/*
	$category = new GtkListStore(Gobject::TYPE_STRING); // note 2
	$list = array('sk', 'cz', 'hu', 'si', 'cr', 'at', 'pl', 'ru');
	foreach($list as $choice) {
	    $category->append(array($choice)); //n4 note3
	}
	$cell_renderer9->set_property('model', $category); // note 4
	$cell_renderer9->set_property('text-column', 0); // note 5
	$cell_renderer9->set_property('editable', true); // note 6
	$cell_renderer9->set_property('has-entry', true); // note 7 
*/
	$cell_renderer1->set_property('width', 50);
	$cell_renderer1->set_property('editable', true);
	$cell_renderer2->set_property('width', 30);
	$cell_renderer2->set_property('editable', true);
	$cell_renderer3->set_property('width', 110);
	$cell_renderer3->set_property('editable', true);
	$cell_renderer4->set_property('width', 110);
	$cell_renderer4->set_property('editable', true);
	$cell_renderer5->set_property('width', 120);
	$cell_renderer5->set_property('editable', true);
	$cell_renderer6->set_property('width', -1);
	$cell_renderer6->set_property('editable', true);
	$cell_renderer7->set_property('width', 100);
	$cell_renderer7->set_property('editable', true);
	$cell_renderer8->set_property('width', 40);
	$cell_renderer8->set_property('editable', true);
	$cell_renderer9->set_property('width', 50);
	$cell_renderer9->set_property('editable', true);
	$cell_renderer10->set_property('width', 50);
	$cell_renderer10->set_property('editable', true);
	
$cell_renderer1->connect('edited',  'callback_start1');
$cell_renderer2->connect('edited',  'callback_start2');
$cell_renderer3->connect('edited',  'callback_start3');
$cell_renderer4->connect('edited',  'callback_start4');
$cell_renderer5->connect('edited',  'callback_start5');
$cell_renderer6->connect('edited',  'callback_start6');
$cell_renderer7->connect('edited',  'callback_start7');
$cell_renderer8->connect('toggled',  'callback_start8');
$cell_renderer9->connect('edited',  'callback_start9');
$cell_renderer10->connect('edited',  'callback_start10');

	
	#$cell_renderer3->set_property('width', -1);
	$column1->pack_start($cell_renderer1, true);
	$column2->pack_start($cell_renderer2, true);
	$column3->pack_start($cell_renderer3, true);
	$column4->pack_start($cell_renderer4, true);
	$column5->pack_start($cell_renderer5, true);
	$column6->pack_start($cell_renderer6, true);
	$column7->pack_start($cell_renderer7, true);
	$column8->pack_start($cell_renderer8, true);
	$column9->pack_start($cell_renderer9, true);
	$column10->pack_start($cell_renderer10, true);

	$column1->set_attributes($cell_renderer1, 'text', 0);
	$column2->set_attributes($cell_renderer2, 'text', 1);
	$column3->set_attributes($cell_renderer3, 'text', 2);
	$column4->set_attributes($cell_renderer4, 'text', 3);
	$column5->set_attributes($cell_renderer5, 'text', 4);
	$column6->set_attributes($cell_renderer6, 'text', 5);
	$column7->set_attributes($cell_renderer7, 'text', 6);
	$column8->set_attributes($cell_renderer8, 'active', 7);
	$column9->set_attributes($cell_renderer9, 'text', 8);
	$column10->set_attributes($cell_renderer10, 'text', 9);

#var_export($G["startovka"]);exit;
	#for($i=0;$i<count($G["startovka"]);$i++) {
	#	$model->append($G["startovka"][$i]);
#	}
	foreach($G["startovka"] as $r) {
		while (count($r)<10) $r[]="";
		
		for($i=0;$i<count($r);$i++) $r[$i]=$r[$i];
		$model->append($r);
	}

$toolbar=new GtkHButtonBox();
$addbutton=GtkButton::new_from_stock(Gtk::STOCK_ADD);
$deletebutton=GtkButton::new_from_stock(Gtk::STOCK_DELETE);
$loadbutton=GtkButton::new_from_stock(Gtk::STOCK_REFRESH);
$savebutton=GtkButton::new_from_stock(Gtk::STOCK_SAVE);

$savebutton->connect("pressed","uloz_udaje");
$addbutton->connect("pressed","pridaj_riadok");
$deletebutton->connect("pressed","vymaz_riadok");


	$toolbar->add($addbutton);
	$toolbar->add($deletebutton);
	$toolbar->add($loadbutton);
	$toolbar->add($savebutton);
	$toolbar->set_size_request(0,40);


$sw=new GtkScrolledWindow();
$sw->set_policy( Gtk::POLICY_AUTOMATIC, Gtk::POLICY_AUTOMATIC);
$sw->add($view);
$sw->set_size_request(-1,560);

$vbox = new GtkVBox();
$vbox->add($toolbar);
$vbox->add($sw);

	$dialog->vbox->pack_start($vbox); // note 3




	$dialog->show_all(); // note 4
	$dialog->run(); // note 5
	$dialog->destroy(); // note 6 

}


function callback_start1($cellrenderertext, $path, $new_text) {
  global $model;
  $iter = $model->get_iter($path);
  $model->set($iter, 0, $new_text);
  $GLOBALS["vars"]["vz_zmena"]=true;
}
function callback_start2($cellrenderertext, $path, $new_text) {
  global $model;
  $iter = $model->get_iter($path);
  $model->set($iter, 1, $new_text);
  $GLOBALS["vars"]["vz_zmena"]=true;
}
function callback_start3($cellrenderertext, $path, $new_text) {
  global $model;
  $iter = $model->get_iter($path);
  $model->set($iter, 2, $new_text);
  $GLOBALS["vars"]["vz_zmena"]=true;
}
function callback_start4($cellrenderertext, $path, $new_text) {
  global $model;
  $iter = $model->get_iter($path);
  $model->set($iter, 3, $new_text);
  $GLOBALS["vars"]["vz_zmena"]=true;
}
function callback_start5($cellrenderertext, $path, $new_text) {
  global $model;
  $iter = $model->get_iter($path);
  $model->set($iter, 4, $new_text);
  $GLOBALS["vars"]["vz_zmena"]=true;
}
function callback_start6($cellrenderertext, $path, $new_text) {
  global $model;
  $iter = $model->get_iter($path);
  $model->set($iter, 5, $new_text);
  $GLOBALS["vars"]["vz_zmena"]=true;
}
function callback_start7($cellrenderertext, $path, $new_text) {
  global $model;
  $iter = $model->get_iter($path);
  $model->set($iter, 6, $new_text);
  $GLOBALS["vars"]["vz_zmena"]=true;
}
function callback_start9($cellrenderertext, $path, $new_text) {
  global $model;
  $iter = $model->get_iter($path);
  $model->set($iter, 8, $new_text);
  $GLOBALS["vars"]["vz_zmena"]=true;
}
function callback_start10($cellrenderertext, $path, $new_text) {
  global $model;
  $iter = $model->get_iter($path);
  $model->set($iter, 9, $new_text);
  $GLOBALS["vars"]["vz_zmena"]=true;
}
function callback_start8($cellrenderer, $path) {
  global $model;
  $value = $cellrenderer->get_active();
  $iter = $model->get_iter($path);
  $model->set($iter, 7, !$value);
  $GLOBALS["vars"]["vz_zmena"]=true;
}

function uloz_udaje() {
	global $model,$G;

	$G["startovka"]=array();
	
/*	
	$gtkmain_methods = get_class_methods(get_class($model));
foreach ($gtkmain_methods as $name => $value) {
  echo "$name : $value\n";
}
*/
	
	
  $model->for_each('process');
	#debug_log("ukladám zmeny v štartovnej listine");
  #print_r($G["startovka"]);exit;
	echo "ukladam udaje\n";
	export_startovka_csv();
  $GLOBALS["vars"]["vz_zmena"]=false;
#  var_export($G);
}

function process($model, $path, $iter) {
    global $G;
    
    $row=array();
    for($i=0;$i<10;$i++) {
    	$value=$model->get_value($iter, $i);
    	$row[]=$value;
    }
   	$G["startovka"][]=$row;
}

function export_startovka_csv() { // note 1
 		global $G;
if (count($G["startovka"])>0) {
			#debug_log("Export štartovky do CSV");
      $dialog = new GtkFileChooserDialog("Export CSV", null, Gtk::FILE_CHOOSER_ACTION_SAVE, // note 2
      array(Gtk::STOCK_OK, Gtk::RESPONSE_OK), null);
      $dialog->show_all();
      if ($dialog->run() == Gtk::RESPONSE_OK) {
          $CSV = $dialog->get_filename(); // get the input filename
          if (strtolower(substr($CSV,-4))!=".csv") $CSV.=".csv";
			    #debug_log("CSV: ".$CSV);
          #echo "selected_file = $selected_file\n";
          $out=fopen($CSV,"w");
          for($i=0;$i<count($G["startovka"]);$i++) {
          	fputs($out,join(";",$G["startovka"][$i])."\n");
          }
          fclose($out);
#	  var_dump($G["startovka"]);
	#debug_log("Export ".($i+0)." záznamov");
      }
      $dialog->destroy();
  } else {
  	#debug_log("EXPORT nemožný - prázdna štartovka");
  }
}

function pridaj_riadok() {
	global $model;
	$model->append(array('','','','','','','','','','',false,false));
  $GLOBALS["vars"]["vz_zmena"]=true;
}
function vymaz_riadok() {
   global $view;
    $model = $view->get_model();
    $selection = $view->get_selection();
    list($model, $selected_rows) =
        $selection->get_selected_rows();

    $i = 0;
    $rows_to_remove = array();
    foreach($selected_rows as $path) {
		  $GLOBALS["vars"]["vz_zmena"]=true;
        $iter = $model->get_iter($path);
        $meno = $model->get_value($iter, 2);
        $priezvisko = $model->get_value($iter, 3);
        #$price = $model->get_value($iter, 3);
        #print "Selection $i: $desc: $qty ($price)\n";
        ++$i;
        $rows_to_remove[] = $path[0]; // note 1
    }

    for ($i=count($rows_to_remove)-1; $i>=0; --$i) { // note 2
        #print "remove row: $rows_to_remove[$i]\n";

$dialogBox = new GtkDialog(
    "Naozaj vymazať?",
    NULL,
    Gtk::DIALOG_MODAL,
    array(
        Gtk::STOCK_NO, Gtk::RESPONSE_NO,
        Gtk::STOCK_YES, Gtk::RESPONSE_YES
    )
);

$dialogQues = new GtkLabel("Skutočne vymazať \"".$meno." ".$priezvisko."\" ?");
$topArea = $dialogBox->vbox;
$topArea->add($dialogQues);
 
/* Showing all widgets added */
$dialogBox->show_all();
 
/* Running the dialog box */
$result = $dialogBox->run();


        if($result==Gtk::RESPONSE_YES) unset($model[$rows_to_remove[$i]]); // note 3
        $dialogBox->destroy();
     }
}


// setup menu 
function setup_menu($vbox, $menus) {
	global $G;
	
    $menubar = new GtkMenuBar();
    $vbox->pack_start($menubar, 0, 0);
    foreach($menus as $toplevel => $sublevels) {
        $menubar->append($top_menu = new GtkMenuItem($toplevel));
        $menu = new GtkMenu();
        $top_menu->set_submenu($menu);
        foreach($sublevels as $submenu) {
            if (is_array($submenu)) { // set up radio menus 
                $i=0;
                $radio[0] = null;
                foreach($submenu as $radio_item) {
                    $radio[$i] = new GtkRadioMenuItem($radio[0], $radio_item);
                    $radio[$i]->connect('toggled', "on_toggle");
                    $menu->append($radio[$i]);
                    ++$i;
                }
                $radio[0]->set_active(1); // select the first item 
            } else {
                if ($submenu=='<hr>') {
                    $menu->append(new GtkSeparatorMenuItem());
                } else {
                    $submenu2 = str_replace('_', '', $submenu); // note 1 
                    $submenu2 = str_replace(' ', '_', $submenu2); // note 1 
                    $stock_image_name = 'Gtk::STOCK_'.strtoupper($submenu2); // note 1 
                    if (defined($stock_image_name)) {
                        $menu_item = new GtkImageMenuItem(constant($stock_image_name)); // note 2 
                    } else {
                        $menu_item = new GtkMenuItem($submenu);
                    }
                    $menu->append($menu_item);
                    $menu_item->connect('activate', 'on_menu_select');
                }
            }
        }
    }
}

// process radio menu selection 
function on_toggle($radio) {
	global $G;
		
    $label = $radio->child->get_label();
    $active = $radio->get_active();
    #echo("radio menu selected: $label\n");
    set_rezim($label);
}



function uprav_behy() {
	global $G,$model,$view;

#debug_log('úprava behov');

	$dialog = new GtkDialog(); // note 1
	$viewport1 = new GtkViewPort();
	$dialog->set_title('Behy');
	$dialog->set_position(GTK::WIN_POS_CENTER);
	$dialog->set_default_size(960,420);

	$model = new GtkListStore(Gobject::TYPE_STRING,
	Gobject::TYPE_STRING,
	Gobject::TYPE_STRING,
	Gobject::TYPE_STRING,
	Gobject::TYPE_STRING,
	Gobject::TYPE_STRING,
	Gobject::TYPE_STRING,
	Gobject::TYPE_STRING,
	Gobject::TYPE_STRING);

#datum
#nazov
#druh (OA, OJ, A1, A2, A3, J1, J2, J3)
#rozhodca

#$rozhodcovia=explode(",",$G["config"]->__get("rozhodca"));

	$view = new GtkTreeView($model);
#	$view->set_grid_lines(Gtk::TREE_VIEW_GRID_LINES_BOTH); 
	
	$column1 = new GtkTreeViewColumn('Kód');
	$column2 = new GtkTreeViewColumn('Názov');
	$column3 = new GtkTreeViewColumn('Filter');
	$column4 = new GtkTreeViewColumn('Dátum');
	$column5 = new GtkTreeViewColumn('Rozhodca');
	$column6 = new GtkTreeViewColumn('Dĺžka');
	$column7 = new GtkTreeViewColumn('Prekážky');
	$column8 = new GtkTreeViewColumn('Štd.č');
	$column9 = new GtkTreeViewColumn('Max.č');
		
	$view->append_column($column1);
	$view->append_column($column2);
	$view->append_column($column3);
	$view->append_column($column4);
	$view->append_column($column5);
	$view->append_column($column6);
	$view->append_column($column7);
	$view->append_column($column8);
	$view->append_column($column9);
	
	$cell_renderer1 = new GtkCellRendererText();
	$cell_renderer2 = new GtkCellRendererText();
	$cell_renderer3 = new GtkCellRendererText();
	$cell_renderer4 = new GtkCellRendererText();
	$cell_renderer5 = new GtkCellRendererText();
	$cell_renderer6 = new GtkCellRendererText();
	$cell_renderer7 = new GtkCellRendererText();
	$cell_renderer8 = new GtkCellRendererText();
	$cell_renderer9 = new GtkCellRendererText();

	$cell_renderer1->set_property('width', 60);
	$cell_renderer1->set_property('editable', true);
	$cell_renderer2->set_property('width', 220);
	$cell_renderer2->set_property('editable', true);
	$cell_renderer3->set_property('width', 50);
	$cell_renderer3->set_property('editable', true);
	$cell_renderer4->set_property('width', 80);
	$cell_renderer4->set_property('editable', true);
	$cell_renderer5->set_property('width', 180);
	$cell_renderer5->set_property('editable', true);
	$cell_renderer6->set_property('width', 50);
	$cell_renderer6->set_property('editable', true);
	$cell_renderer7->set_property('width', 50);
	$cell_renderer7->set_property('editable', true);
	$cell_renderer8->set_property('width', 50);
	$cell_renderer8->set_property('editable', true);
	$cell_renderer9->set_property('width', 50);
	$cell_renderer9->set_property('editable', true);
	
$cell_renderer1->connect('edited',  'callback_start1');
$cell_renderer2->connect('edited',  'callback_start2');
$cell_renderer3->connect('edited',  'callback_start3');
$cell_renderer4->connect('edited',  'callback_start4');
$cell_renderer5->connect('edited',  'callback_start5');
$cell_renderer6->connect('edited',  'callback_start6');
$cell_renderer7->connect('edited',  'callback_start7');
$cell_renderer8->connect('edited',  'callback_start8');
$cell_renderer9->connect('edited',  'callback_start9');

#$cell_renderer3->set_property('width', -1);
	$column1->pack_start($cell_renderer1, true);
	$column2->pack_start($cell_renderer2, true);
	$column3->pack_start($cell_renderer3, true);
	$column4->pack_start($cell_renderer4, true);
	$column5->pack_start($cell_renderer5, true);
	$column6->pack_start($cell_renderer6, true);
	$column7->pack_start($cell_renderer7, true);
	$column8->pack_start($cell_renderer8, true);
	$column9->pack_start($cell_renderer9, true);
	
	$column1->set_attributes($cell_renderer1, 'text', 0);
	$column2->set_attributes($cell_renderer2, 'text', 1);
	$column3->set_attributes($cell_renderer3, 'text', 2);
	$column4->set_attributes($cell_renderer4, 'text', 3);
	$column5->set_attributes($cell_renderer5, 'text', 4);
	$column6->set_attributes($cell_renderer6, 'text', 5);
	$column7->set_attributes($cell_renderer7, 'text', 6);
	$column8->set_attributes($cell_renderer8, 'text', 7);
	$column9->set_attributes($cell_renderer9, 'text', 8);
	
	#print_r($G["behy"]);exit;
	
	foreach ($G["behy"] as $b) {
		$model->append($b);
	}

$toolbar=new GtkHButtonBox();
$addbutton=GtkButton::new_from_stock(Gtk::STOCK_ADD);
$deletebutton=GtkButton::new_from_stock(Gtk::STOCK_DELETE);
#$loadbutton=GtkButton::new_from_stock(Gtk::STOCK_REFRESH);
$savebutton=GtkButton::new_from_stock(Gtk::STOCK_SAVE);

$savebutton->connect("pressed","uloz_behy");
$addbutton->connect("pressed","pridaj_beh");
$deletebutton->connect("pressed","vymaz_beh");

	$toolbar->add($addbutton);
	$toolbar->add($deletebutton);
	#$toolbar->add($loadbutton);
	$toolbar->add($savebutton);
	$toolbar->set_size_request(0,40);

$sw=new GtkScrolledWindow();
$sw->set_policy( Gtk::POLICY_AUTOMATIC, Gtk::POLICY_AUTOMATIC);
$sw->add($view);
$sw->set_size_request(-1,560);

$vbox = new GtkVBox();
$vbox->add($toolbar);
$vbox->add($sw);

	$dialog->vbox->pack_start($vbox); // note 3

	$dialog->show_all(); // note 4
	$dialog->run(); // note 5
	$dialog->destroy(); // note 6 

}


function uloz_behy() {
	global $model,$G;

	$G["behy"]=array();
  
  $model->for_each('process_behy');
	#$G["config"]->__set("behy",serialize($G["behy"]));
	debug_log("ukladám zmeny v zozname behov");
	#echo "ukladam udaje\n";
  $GLOBALS["vars"]["behy_zmena"]=false;
  #var_export($G["behy"]);
 # $G["config"]->save();
}


function pridaj_beh() {
	global $model;
	$model->append(array('','','',''));
  $GLOBALS["vars"]["behy_zmena"]=true;
}
function vymaz_beh() {
   global $view;
    $model = $view->get_model();
    $selection = $view->get_selection();
    list($model, $selected_rows) =
        $selection->get_selected_rows();

    $i = 0;
    $rows_to_remove = array();
    foreach($selected_rows as $path) {
		  $GLOBALS["vars"]["behy_zmena"]=true;
        $iter = $model->get_iter($path);
        $nazov = $model->get_value($iter, 1);
        $datum = $model->get_value($iter, 0);
        #print "Selection $i: $desc: $qty ($price)\n";
        ++$i;
        $rows_to_remove[] = $path[0]; // note 1
    }

    for ($i=count($rows_to_remove)-1; $i>=0; --$i) { // note 2
        #print "remove row: $rows_to_remove[$i]\n";

$dialogBox = new GtkDialog(
    "Naozaj vymazať?",
    NULL,
    Gtk::DIALOG_MODAL,
    array(
        Gtk::STOCK_NO, Gtk::RESPONSE_NO,
        Gtk::STOCK_YES, Gtk::RESPONSE_YES
    )
);

$dialogQues = new GtkLabel("Skuto�ne vymaza� ".$datum." ".$nazov."?");
$topArea = $dialogBox->vbox;
$topArea->add($dialogQues);
 
/* Showing all widgets added */
$dialogBox->show_all();
 
/* Running the dialog box */
$result = $dialogBox->run();


        if($result==Gtk::RESPONSE_YES) unset($model[$rows_to_remove[$i]]); // note 3
        $dialogBox->destroy();
     }
}




function get_date() { // note 1 
    $getdate_dialog = new GetDate();
    $date = $getdate_dialog->calendar->get_date();
    $selected_date = 1+$date[1].'/'.$date[2].'/'.$date[0]; // note 2 
    return $selected_date;
}

class GetDate{ // note 1 

    var $calendar;

    function GetDate() {
        $dialog = new GtkDialog('Get Date', null, Gtk::DIALOG_MODAL);
        $dialog->set_position(Gtk::WIN_POS_CENTER_ALWAYS);
        $top_area = $dialog->vbox;
        //setlocale(LC_ALL, 'english');
        $top_area->pack_start($hbox = new GtkHBox());

        // set up the calendar 
        $this->calendar = new GtkCalendar();
        $top_area->pack_start($this->calendar, 0, 0);

        // add an OK button 
        $dialog->add_button(Gtk::STOCK_OK, Gtk::RESPONSE_OK);

        $this->dialog = $dialog;
        $dialog->set_has_separator(false);
        $dialog->show_all();
        $dialog->run();
        $dialog->destroy();
    }
}





// process menu item selection 
function on_menu_select($menu_item) {
	global $G;
    $item = $menu_item->child->get_label();
    switch ($item) {
    	case "_Koniec": Gtk::main_quit();break;
    	case "_Displej": show_display();break;
    	case "Š_tartovka": uprav_startovku();break;
    	case "_Behy": uprav_behy();break;
    }
}

?>
