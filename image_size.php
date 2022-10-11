<?php
session_start(); 
if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH'])!='xmlhttprequest') {sleep(2);exit;} // ajax request
if(!isset($_POST['unox']) || $_POST['unox']!=$_SESSION['unox']) {sleep(2);exit;} // appel depuis uno.php
?>
<?php
include('../../config.php');
include('lang/lang.php');
$busy = '';
if(file_exists('../../data/busy.json')) {
	$q = file_get_contents('../../data/busy.json');
	$a = json_decode($q,true);
	$busy = (!empty($a['nom'])?$a['nom']:'');
}
else exit;
// ********************* actions *************************************************************************
if(isset($_POST['action'])) {
	switch ($_POST['action']) {
		// ********************************************************************************************
		case 'plugin':
		$qual = 75;
		if(file_exists('../../../imageSizeR.html')) unlink('../../../imageSizeR.html');
		if(file_exists('../../data/image_size.json')) {
			$q = file_get_contents('../../data/image_size.json'); $a = json_decode($q,true);
			if(!empty($a['qual'])) $qual = $a['qual'];
		}
		?>
		<link rel="stylesheet" type="text/css" media="screen" href="uno/plugins/image_size/image_size.css" />
		<div class="blocForm">
			<h2>Image Size</h2>
			<p><?php echo T_("Automatic resizing of images added to your page to fit the display, optimize loading and improve SEO."); ?></p>
			<p>
				<?php echo T_("This plugin only allows you to resize images larger than their HTML display."); ?>
				(<span class="ui-icon ui-icon-closethick" style="display:inline-block;margin:0 6px"></span>)
			</p>
			<p><?php echo T_("The created images are saved at the same place as the original image. The width is added to the end of the name."); ?></p>
			<h3><?php echo T_("Settings"); ?></h3>
			<table class="hForm">
				<tr>
					<td><label><?php echo T_("JPG quality"); ?></label></td>
					<td>
						<input type="number" class="input" name="qual" id="imageSizeQual" style="width:100px;" min="40" max="100" value="<?php echo $qual; ?>" />
					</td>
					<td><em><?php echo T_("Quality in percent for images in JPG format. (default: 75)");?></em></td>
				</tr>
			</table>
			<div class="bouton fr" onClick="f_save_imageSize();" title="<?php echo T_("Save settings");?>"><?php echo T_("Save"); ?></div>
			<div class="clear"></div>
			<h3><?php echo T_("Images from your page");?></h3>
			<p id="pubImageSize"><?php echo T_("Don't forget to PUBLISH when you're done.");?></p>
			<form id="frmImageSize">
				<table id="tableImageSize">
					<tr>
						<th><?php echo T_("Image");?></th>
						<th><?php echo T_("Chapter");?></th>
						<th class="imsrc"><?php echo T_("Source");?></th>
						<th><?php echo T_("Width displayed");?></th>
						<th class="getwid"><?php echo T_("Image file Width");?><br /><span class="bouton" onClick="f_getwid_imageSize();"><?php echo T_("Get Width");?></span></th>
						<th class="resize"><?php echo T_("Resize");?><br /><span class="bouton" onClick="f_resize_imageSize();"><?php echo T_("Resize");?></span></th>
					</tr>
				</table>
			</form>
		</div>
		<?php
		$q = file_get_contents('../../data/'.$busy.'/site.json'); $a = json_decode($q,true);
		if(isset($a['url']) && file_exists('../../../'.$a['nom'].'.html')) {
			$b = file_get_contents('../../../'.$a['nom'].'.html');
			$o = file_get_contents('image_sizeInc.js');
			$j = '<style>body{visibility:hidden;}</style><script>'.$o.'</script>';
			$b = str_replace('</body>',$j.'</body>',$b);
			file_put_contents('../../../imageSizeR.html', $b);
			$u = (isset($a['url']))?$a['url']:'';
			$url = $u.'/imageSizeR.html'.'?'.rand();
			if($url && (empty($u) || strpos($u, $_SERVER['SERVER_NAME'])!==false)) echo '<iframe id="imageSizeFrame" src="'.$url.'" title="preview"></iframe>';
			else { ?>
			
		<div class="blocForm warningIms">
			<p><?php echo T_("No publication or incorrect Base URL configuration !"); ?></p>
		</div>
		<?php }
		}
		break;
		// ********************************************************************************************
		case 'save':
		$a = array();
		if(file_exists('../../data/image_size.json')) {
			$q = file_get_contents('../../data/image_size.json');
			$a = json_decode($q,true);
		}
		$a['qual'] = (isset($_POST['qual'])?intval($_POST['qual']):75);
		$out = json_encode($a);
		if(file_put_contents('../../data/image_size.json', $out)) echo T_('Backup performed');
		else echo '!'.T_('Impossible backup');
		break;
		// ********************************************************************************************
		case 'getwid':
		$f = '../../../'.strip_tags($_POST['src']);
		if(file_exists($f)) {
			$s = getimagesize($f);
			if(!empty($s[0])) echo $s[0];
		}
		break;
		// ********************************************************************************************
		case 'resize':
		$f = '../../../'.strip_tags($_POST['src']);
		$f = str_replace(['.jpeg','.JPEG'], '.jpg', $f);
		$w = intval($_POST['wid']);
		$c = strip_tags($_POST['chap']);
		if(file_exists($f)) {
			$q = file_get_contents('../../data/image_size.json');
			$a = json_decode($q,true);
			$qual = (!empty($a['qual'])?$a['qual']:75);
			$src = imagecreatefromjpeg($f);
			$img = imagescale($src, $w);
			$p = substr(strip_tags($_POST['src']), 0, -4).'-'.$w.'.jpg';
			if(imagejpeg($img, '../../../'.$p, $qual)) {
				$q = file_get_contents('../../data/'.$busy.'/site.json');
				$a = json_decode($q,true);
				foreach($a['chap'] as $v) {
					$t = remove_accents($v['t']);
					$t = preg_replace('/[^a-zA-Z0-9%]/s', '', $t);
					if($t==$c) {
						$o = file_get_contents('../../data/'.$busy.'/chap'.$v['d'].'.txt');
						$o = str_replace('<IMG', '<img', $o);
						$ni = 0;
						while(($ni=strpos($o, '<img', $ni))!==false) {
							$nf = strpos($o, '>', $ni);
							$oi = substr($o, $ni, $nf-$ni); // img tag
							if(strpos($oi, strip_tags($_POST['src']))!==false) {
								$of = str_replace(strip_tags($_POST['src']), $p, $oi);
								$mi = strpos($of,'width:');
								if($mi) {
									$mf = strpos($of,'px',$mi);
									if($mf && $mf-$mi<15) {
										$mf += 2;
										if(substr($of,$mf,1)==';') ++$mf;
										$of = substr($of,0,$mi) . substr($of,$mf);
									}
								}
								$mi = strpos($of,'height:');
								if($mi) {
									$mf = strpos($of,'px',$mi);
									if($mf && $mf-$mi<15) {
										$mf += 2;
										if(substr($of,$mf,1)==';') ++$mf;
										$of = substr($of,0,$mi) . substr($of,$mf);
									}
								}
								$o = str_replace($oi, $of, $o);
								break 1;
							}
							$ni += 4;
						}
						if(file_put_contents('../../data/'.$busy.'/chap'.$v['d'].'.txt', $o)) echo 'OK';
					}
				}
			}
		}
		break;
		// ********************************************************************************************
	}
	clearstatcache();
	exit;
}
//
function remove_accents($s) { // Idem central.php
    if(!preg_match('/[\x80-\xff]/',$s)) return $s;
    $c = array(
	chr(195).chr(128) => 'A', chr(195).chr(129) => 'A', chr(195).chr(130) => 'A', chr(195).chr(131) => 'A', chr(195).chr(132) => 'A', chr(195).chr(133) => 'A',
	chr(195).chr(135) => 'C', chr(195).chr(136) => 'E', chr(195).chr(137) => 'E', chr(195).chr(138) => 'E', chr(195).chr(139) => 'E', chr(195).chr(140) => 'I',
	chr(195).chr(141) => 'I', chr(195).chr(142) => 'I', chr(195).chr(143) => 'I', chr(195).chr(145) => 'N', chr(195).chr(146) => 'O', chr(195).chr(147) => 'O',
	chr(195).chr(148) => 'O', chr(195).chr(149) => 'O', chr(195).chr(150) => 'O', chr(195).chr(153) => 'U', chr(195).chr(154) => 'U', chr(195).chr(155) => 'U',
	chr(195).chr(156) => 'U', chr(195).chr(157) => 'Y', chr(195).chr(159) => 's', chr(195).chr(160) => 'a', chr(195).chr(161) => 'a', chr(195).chr(162) => 'a',
	chr(195).chr(163) => 'a', chr(195).chr(164) => 'a', chr(195).chr(165) => 'a', chr(195).chr(167) => 'c', chr(195).chr(168) => 'e', chr(195).chr(169) => 'e',
	chr(195).chr(170) => 'e', chr(195).chr(171) => 'e', chr(195).chr(172) => 'i', chr(195).chr(173) => 'i', chr(195).chr(174) => 'i', chr(195).chr(175) => 'i',
	chr(195).chr(177) => 'n', chr(195).chr(178) => 'o', chr(195).chr(179) => 'o', chr(195).chr(180) => 'o', chr(195).chr(181) => 'o', chr(195).chr(182) => 'o',
	chr(195).chr(182) => 'o', chr(195).chr(185) => 'u', chr(195).chr(186) => 'u', chr(195).chr(187) => 'u', chr(195).chr(188) => 'u', chr(195).chr(189) => 'y',
	chr(195).chr(191) => 'y',
	chr(196).chr(128) => 'A', chr(196).chr(129) => 'a', chr(196).chr(130) => 'A', chr(196).chr(131) => 'a', chr(196).chr(132) => 'A', chr(196).chr(133) => 'a',
	chr(196).chr(134) => 'C', chr(196).chr(135) => 'c', chr(196).chr(136) => 'C', chr(196).chr(137) => 'c', chr(196).chr(138) => 'C', chr(196).chr(139) => 'c',
	chr(196).chr(140) => 'C', chr(196).chr(141) => 'c', chr(196).chr(142) => 'D', chr(196).chr(143) => 'd', chr(196).chr(144) => 'D', chr(196).chr(145) => 'd',
	chr(196).chr(146) => 'E', chr(196).chr(147) => 'e', chr(196).chr(148) => 'E', chr(196).chr(149) => 'e', chr(196).chr(150) => 'E', chr(196).chr(151) => 'e',
	chr(196).chr(152) => 'E', chr(196).chr(153) => 'e', chr(196).chr(154) => 'E', chr(196).chr(155) => 'e', chr(196).chr(156) => 'G', chr(196).chr(157) => 'g',
	chr(196).chr(158) => 'G', chr(196).chr(159) => 'g', chr(196).chr(160) => 'G', chr(196).chr(161) => 'g', chr(196).chr(162) => 'G', chr(196).chr(163) => 'g',
	chr(196).chr(164) => 'H', chr(196).chr(165) => 'h', chr(196).chr(166) => 'H', chr(196).chr(167) => 'h', chr(196).chr(168) => 'I', chr(196).chr(169) => 'i',
	chr(196).chr(170) => 'I', chr(196).chr(171) => 'i', chr(196).chr(172) => 'I', chr(196).chr(173) => 'i', chr(196).chr(174) => 'I', chr(196).chr(175) => 'i',
	chr(196).chr(176) => 'I', chr(196).chr(177) => 'i', chr(196).chr(178) => 'IJ',chr(196).chr(179) => 'ij',chr(196).chr(180) => 'J', chr(196).chr(181) => 'j',
	chr(196).chr(182) => 'K', chr(196).chr(183) => 'k', chr(196).chr(184) => 'k', chr(196).chr(185) => 'L', chr(196).chr(186) => 'l', chr(196).chr(187) => 'L',
	chr(196).chr(188) => 'l', chr(196).chr(189) => 'L', chr(196).chr(190) => 'l', chr(196).chr(191) => 'L', chr(197).chr(128) => 'l', chr(197).chr(129) => 'L',
	chr(197).chr(130) => 'l', chr(197).chr(131) => 'N', chr(197).chr(132) => 'n', chr(197).chr(133) => 'N', chr(197).chr(134) => 'n', chr(197).chr(135) => 'N',
	chr(197).chr(136) => 'n', chr(197).chr(137) => 'N', chr(197).chr(138) => 'n', chr(197).chr(139) => 'N', chr(197).chr(140) => 'O', chr(197).chr(141) => 'o',
	chr(197).chr(142) => 'O', chr(197).chr(143) => 'o', chr(197).chr(144) => 'O', chr(197).chr(145) => 'o', chr(197).chr(146) => 'OE',chr(197).chr(147) => 'oe',
	chr(197).chr(148) => 'R', chr(197).chr(149) => 'r', chr(197).chr(150) => 'R', chr(197).chr(151) => 'r', chr(197).chr(152) => 'R', chr(197).chr(153) => 'r',
	chr(197).chr(154) => 'S', chr(197).chr(155) => 's', chr(197).chr(156) => 'S', chr(197).chr(157) => 's', chr(197).chr(158) => 'S', chr(197).chr(159) => 's',
	chr(197).chr(160) => 'S', chr(197).chr(161) => 's', chr(197).chr(162) => 'T', chr(197).chr(163) => 't', chr(197).chr(164) => 'T', chr(197).chr(165) => 't',
	chr(197).chr(166) => 'T', chr(197).chr(167) => 't', chr(197).chr(168) => 'U', chr(197).chr(169) => 'u', chr(197).chr(170) => 'U', chr(197).chr(171) => 'u',
	chr(197).chr(172) => 'U', chr(197).chr(173) => 'u', chr(197).chr(174) => 'U', chr(197).chr(175) => 'u', chr(197).chr(176) => 'U', chr(197).chr(177) => 'u',
	chr(197).chr(178) => 'U', chr(197).chr(179) => 'u', chr(197).chr(180) => 'W', chr(197).chr(181) => 'w', chr(197).chr(182) => 'Y', chr(197).chr(183) => 'y',
	chr(197).chr(184) => 'Y', chr(197).chr(185) => 'Z', chr(197).chr(186) => 'z', chr(197).chr(187) => 'Z', chr(197).chr(188) => 'z', chr(197).chr(189) => 'Z',
	chr(197).chr(190) => 'z', chr(197).chr(191) => 's'
    );
    $s = strtr($s, $c);
    return $s;
}

?>
