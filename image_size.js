//
// CMSUno
// Plugin Image Size
//
function f_save_imageSize(){
	var qual=document.getElementById("imageSizeQual").value;
	jQuery.post('uno/plugins/image_size/image_size.php',{'action':'save','unox':Unox,'qual':qual},function(r){
		f_alert(r);
	});
}
//
function f_getwid_imageSize(){
	var t,v;
	t=jQuery("#tableImageSize tr");
	for(v=1;v<t.length;v++){
		(function(i){
			var a=jQuery(t[i]).find("td");
			var s=jQuery(a[2]).text(),b=jQuery(a[3]).text();
			jQuery.post('uno/plugins/image_size/image_size.php',{'action':'getwid','unox':Unox,'src':s},function(r){
				jQuery(a[4]).text(r+' px');
				if(Number(b.substring(0,b.length-3))<Number(r)){
					jQuery(a[5]).append('<input class="inpckb" type="checkbox" /><br /><input class="inpnbr" type="number" value="'+b.substring(0,b.length-3)+'" />');
				}
				else jQuery(a[5]).append('<span class="ui-icon ui-icon-closethick" style="display:inline-block;"></span>')
			});
		})(v);
	}
	jQuery("#tableImageSize th.getwid .bouton").remove();
	jQuery("#tableImageSize th.resize .bouton").css("display","inline-block");
}
//
function f_resize_imageSize(){
	var t,v,b=0;
	t=jQuery("#tableImageSize tr");
	for(v=1;v<t.length;v++){
		(function(i){
			var a=jQuery(t[i]).find(".resize"),s=jQuery(t[i]).find(".imsrc"),w,c,ch;
			if(a){
				w=jQuery(a[0]).children(".inpnbr").val();
				c=jQuery(t[i]).find("td");ch=jQuery(c[1]).text();
				if(jQuery(a[0]).children(".inpckb").is(":checked")&&w.length!=0){
					b=1;
					jQuery.post('uno/plugins/image_size/image_size.php',{
						'action':'resize',
						'unox':Unox,
						'src':jQuery(s[0]).text(),
						'wid':jQuery(a[0]).children(".inpnbr").val(),
						'chap':ch
					},function(r){
						if(r=='OK'){
							jQuery(c[4]).text(jQuery(a[0]).children(".inpnbr").val()+' px');
							jQuery(a[0]).html('<span class="ui-icon ui-icon-check" style="display:inline-block;"></span>');
						}
						else jQuery(a[0]).text(r);
					});
				}
			}
			if(b)jQuery("#pubImageSize").show();
		})(v);
	}
}
