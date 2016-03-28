<?
##################################################################################
### Phrozen - one simple way to protect your scripts in real time.
### Joel De Gan 2002.
### http://www.tenshimedia.com, http://www.joihost.com
### I also do [QBoard] - http://www.qboard.org
### If you use this somewhere, give me credit for the idea..
### joeldg@tenshimedia.com, icq #13223329
### 
### The idea, you want to encode your scripts, however you don't want to make
### the end user have to install something to "un-encode" them.
###
### solution: "Phrozen"
###
### This is a more robust version than what I submitted to zend.com.
### To run this example you will need to create a directory under where this
### file is located called "pages" and chmod it so that it is writable.
### (or run the script from command lane as root)
### load up the page, select the file you wish to "Phreeze", it will create a 
### file in the pages directory that is an md5 hash of the code. 
### the encoded code in the file will be uncoded on the fly, written to disk
### and then included into this page.
###
### Obviously this technique also manages to address even includes, however I 
### stress that having multiple levels of includes might not be the wisest choice.
### Additionally, some re-coding of projects will be necessary, unless you
### already write your programs using 'switch' pages (script clearinghouse)
### see the bottom of this file for more info on that.
###
### I am working out an idea for doing remote licensing of products written in 
### PHP, passing keys back and forth (software license keys) and having all the
### pages encrypted. However, this would be a free download type of thing to 
### test the product, but I want it hard to reverse engineer as well as having
### control over other parts of it. i.e. encoded algorithm pulled from a 
### webserver based on a key, unencoded and run locally to be able to run the
### program in it's full state.
###
### some more comments at the bottom and plastered around in the code.
###
### DISCLAIMER AND TERMS OF USE: I am not responsible if you fry all your data.
### 	Use of this software is deterministic on you being intelligent and you
###	are expected to have a good working knowledge of PHP and programming.
###	for license see:
###	http://www.opensource.org/licenses/gpl-license.html
###
##################################################################################
$page_dir = getcwd() . "/pages/"; // where out 'phro' files are located.
$include_dir = "pages/"; // same dir for includes for this example
$temp = "temp.php"; // also can use  the following; md5(time()) . ".php";
$temp_file = $page_dir . $temp;
$include_this = $include_dir . $temp;
define ("PHROFAT", $page_dir.".PHROFAT"); // maps filenames to phro names.
// the following the the detmining factor on if we show the html
define ("SETUP_MODE", 1); # 0=no, 1=yes

@mkdir('pages', 0777); // try to make it, comment this out after first run.

### YOU SHOULD NOT HAVE TO EDIT ANYTHING BELOW THIS LINE. UNLESS YOU FEEL LIKE DIGGING AROUND
### IN MY CODE.. IN THAT CASE I HAVE COMMENTED IT SO YOU SHOULD NOT HAVE PROBLEMS.

if(SETUP_MODE == 1){
   if($remove){
	remove_file_from_fat($remove);
   }//fi
   if($addfile){
	phreeze_file($addfile);
   }//fi
}//fi

// this function just reads an entire file into a string
// I am planning on setting this up to also be able to do URL's so that you
// could have remote hosting of PHP scripts. This would be a nice way
// to license things perhaps..
function file_get_contents($filename, $use_include_path = 0) {
	if ($fd = @fopen ($filename, "rb", $use_include_path)){
		$contents = fread($fd, filesize($filename));
		fclose($fd);
	}//fi
  return $contents;
}//end function

///////////////////////////////////////////////////////////////////
// I found the following two functions on zend.com and thought they were nice
// they also are fast.. feel free however to replace them with any encryption
// that you prefer.
// These have not been altered in any way for use in this program.
function trans_encrypt($data){
    for($i = 0, $key = 27, $c = 48; $i <= 255; $i++){
        $c = 255 & ($key ^ ($c << 1));
        $table[$key] = $c;
        $key = 255 & ($key + 1);
    }
    $len = strlen($data);
    for($i = 0; $i < $len; $i++){
        $data[$i] = chr($table[ord($data[$i])]);
    }
    return chunk_split(base64_encode($data));
}

function trans_decrypt($data){
    $data = base64_decode($data);
    for($i = 0, $key = 27, $c = 48; $i <= 255; $i++){
        $c = 255 & ($key ^ ($c << 1));
        $table[$c] = $key;
        $key = 255 & ($key + 1);
    }
    $len = strlen($data);
    for($i = 0; $i < $len; $i++){
        $data[$i] = chr($table[ord($data[$i])]);
    }
    return $data;
} 
/////////////////////////////////////////////////////////////////////

### 
### phreeze/unfreeze functions, you can obviously add to these
### add in gzcompressing, encryption etc.
###
function phreeze($z){
    $z = serialize($z);
    //$z = chunk_split(base64_encode($z)); // this is done in the encrypt
    $z = trans_encrypt($z);
  return $z;
}//end function

function unphreeze($z){
    $z = trans_decrypt($z);	
    //$z = base64_decode($z); // this is done in the decrypt
    $z = unserialize($z);
    $z = stripslashes($z); // this is necessary.. don't remove
  return $z;
}//end function

######################################################################################
###
### Functions for doing a rudimentary file allocation table in our pages directory.
### This is really the heart of the program here.. Everything you would need to do
### with your new files..  Additionally, try not to ever delete your pages/.PHROFAT
### file as it is how you access all your files..  sort of like wiping the FAT on your
### harddrive I suppose..  anyway..
###
##################################PHAT FUNCTIONS#######################################
### simply load the Phrozen FAT table from disk.
function load_fat(){
	return unserialize(file_get_contents(PHROFAT));
} //end function

### write Phrozen FAT table to disk
### this file should only be called by other functions or you could loose 
### the entire fat table.
function write_fat($fat){
	$fp = fopen (PHROFAT, "w+");
	fputs ($fp, serialize($fat));
	fclose($fp);
}//end function

### removes file, loads fat, so no need to pass the FAT in.
function remove_file_from_fat($file){
	global $page_dir;
	$fat = load_fat();
	$filename = explode("/", $file);
	$filename = $filename[count($filename)-1];	
	if (array_key_exists($filename, $fat)){
		while(list($key,$val)=each($fat)){
			if($key <> "$filename"){
				$newfat[$key] = $val;
			}else{
				// nuke the file
				unlink($page_dir.$val); // delete the phro file
			}//fi
		}//wend
		write_fat($newfat); // rewrite the phat
	}//fi
}//end function

### simple helper function
### add file, loads fat, so no need to pass the FAT in.
function add_file_to_fat($file, $phro){
	$fat = load_fat();
	$fat = array_merge( $fat, array($file => $phro));
	write_fat($fat);
}//end function


### get and include the file.
function load_file($file){
	global $page_dir, $temp_file, $include_this;
	$fat = load_fat();
	if (array_key_exists($file, $fat)){
		$filetoload =  $fat[$file];
		if (file_exists($page_dir . $filetoload)) {
			$thecode = file_get_contents($page_dir . $filetoload);
			$out = unphreeze($thecode);
			$fp = fopen ($temp_file, "w");
			fputs ($fp, $out); // slap the data in the file
			fflush($fp); // flush contents to file
			fclose($fp); // close our handle
			require "$include_this"; // include it
			unlink($temp_file);// remove the temp file
		}else{
			// dump an error to the user...
			echo "<br><br><center><h3>\n";
			echo "<font color=red>$file found in the PHAT, but not on disk?</font>\n";
			echo "</h3></center>\n";			
		}//fi	
	}else{
		// dump this error because the file is not Phrozen
		echo "<br><br><center><h3>\n";
		echo "<font color=red>$file not found in the PHAT</font>\n";
		echo "</h3></center>\n";
	}//fi
}//end function

### expects full path to file
### this is where we phreeze the file, and tos the phro file in the pages dir
### we also set up an entry in the phat.
function phreeze_file($file){
	global $page_dir, $temp_file, $include_this;
	$filename = explode("/", $file);
	$filename = $filename[count($filename)-1];
	
	$load_data = file_get_contents($file);
	$phcode = phreeze($load_data);
	$name = md5($phcode) . ".phro";
	
	if (file_exists($page_dir . $name)) {
		$fp = fopen ($page_dir.$name, "aw");
		fputs ($fp, $phcode);
		fclose($fp);
	}else{
		$fp = fopen ($page_dir .$name, "w");
		fputs ($fp, $phcode);
		fclose($fp);
	}//fi
	add_file_to_fat($filename, $name);
}//end functions
#################################^PHAT FUNCTIONS^######################################

// if we are in setup mode the we need to display the following html..
if(SETUP_MODE == 1){
#####################################################################################################################
?>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#000000">
  <tr>
    <td width"100%" bgcolor="#000000" align="left"><br>
    <a href=index.phps><font color=white>view the source</font></a>
    </td>  	
    <td width"100%" bgcolor="#000000" align="right"><br>
    <h2><font color=white>[ Phrozen v0.2] Joel De Gan</font></h2>
    </td>
  </tr>
</table>
<table width="100%" border="0">
  <tr>
    <td width"100%">
    <p>[ Phrozen ] is a freeware/opensource (as in GNU) PHP encoder written in PHP it is entirely contained within this one file.
    The idea is that you can encode files through this interface you are viewing now. Then change the variable
    SETUP_MODE to 0 and include this file into a index.php file which acts as a switcher for all the files which you
    have encoded. Phrozen has PHAT&trade; functions which act as a rudimentary file allocation table. All the functions are 
    pretty easy to go through and use to have your files encoded. I offer no support for this software.
    <p>To download this file, <a href="phrozen.tar.gz">click</a>, view source or `wget http://204.251.2.89/phrozen/index.phps`
    
    </td>
  </tr>

  <tr>
    <td width"100%" bgcolor=#000000>
    	<font color=white><b>Select a file to Phreeze.</b></font>
    </td>
  </tr>
</table>

<table width=50%>
<?php
$curr = getcwd();
echo "root - [<a href=\"?dir=\">$curr</a>]<br><br>";

// now I know there are better ways to do this, however 
// this part of the file is really only for the admin
// programmer to view, so I am not that concerned..
exec("ls -la $curr/$dir",$lines,$rc);
 $count = count($lines) - 1;
 for ($i = 1; $i <= $count; $i++) {
     $type = substr($lines[$i],0,1);
     $name = strrchr($lines[$i]," ");
     $name = substr($name,1);
     $dire = substr($lines[$i],0,strpos($lines[$i],$name));

     if ($type == "d") {
        if ($name == "." or $name == "..") {
           ;
        } else {
            if ($dir == "") {
              $size=filesize($name);
   		echo  "<tr><td><a href=\"?dir=$name/\">$name</a></td><td>$size</td></tr>";
            }   else  {
              $size=filesize($name);
                echo "<tr><td><a href=?addfile=$dir$name>$name</a></td><td>$size</td></tr>";
            }//fi
        }//fi
     } else {
        if ($dir == "") {
        $size=filesize($name);
            echo "<tr><td><a href=?addfile=$dir$name>$name</a></td><td>$size</td></tr>";
        }   else  {
            echo "<tr><td><a href=?addfile=$dir$name>$name</a></td></tr>";
        }//fi
     }//fi
 }//rof
 ?>
</table>
<br>
<table width=100%>
<tr bgcolor=#000000><td colspan=2><font color=white><b>Current files in the PHAT (click to remove)</b></font></td></tr>
<?
$fat = load_fat();
while (list($key, $val) = @each ($fat)){
	echo "<tr><td><a href=?remove=$key>$key</a></td><td>$val</td></tr>\n";
}//wend

?>
</table>


<?
#####################################################################################################################
}//fi


/*
Here is an explaination and example of how to write a switcher page:

lets assume you have several pages encoded.. index.php, display.php and post.php..
1) copy the files to a new directory and make sure you always use a $go variable your pages.
	<form method=post action=?go=post&value=val> etc... This way everything goes through 
	one main page, then you have everything set up in that one file. (see http://ipn.joihost.com for an example)
	otherwise you will need to hardcode the load_file($PHP_SELF) into each file replaced. which still works
	but is not quite as elegant.
2) encode the files.
3) remove the files.
4) change your phrozen.php SETUP_MODE to 0 so you don't get the setup html..
5) write the following in as your new index.php

new index.php:

	include "phrozen.php";
	if($go == ""){$go = "index"; }
	load_file($go . ".php"); // 

then everywhere in your code where you have something that calls a different page
or whatever, you change calls like this
	<a href="display.php?name=Phro&last=Zen">bla</a>
-TO-
	<a href="/?go=display&name=Phro&last=Zen">bla</a>

which loads the index.php and passes in the "go" variable so display.php will be
decoded and run.

enjoy.
*/
?>
