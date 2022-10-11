jQuery(document).ready(function(){
	jQuery("#pagesContent img").each(function(){
		if(typeof(jQuery(this).attr('data-echo'))!='undefined')jQuery(this).attr("src",jQuery(this).attr('data-echo'));
	});
	window.onload=function(){
		var s,b,tr,td;
		jQuery("#pagesContent img").each(function(){
			s=jQuery(this).attr("src");
			if(s.substring(0,6)=='files/'&&jQuery(this).width()!=0){
				tr=document.createElement('tr');
				td=document.createElement('td');td.innerHTML='<img src="'+s+'" style="max-width:90px" />';
				tr.appendChild(td);
				b=jQuery(this).closest('.blocChap').attr("id");
				td=document.createElement('td');td.innerHTML=b.substring(0,b.length-8);
				tr.appendChild(td);
				td=document.createElement('td');td.innerHTML=s;td.className="imsrc";
				tr.appendChild(td);
				td=document.createElement('td');td.innerHTML=parseInt(jQuery(this).width()+.5)+' px';
				tr.appendChild(td);
				td=document.createElement('td');td.className="getwid";
				tr.appendChild(td);
				td=document.createElement('td');td.className="resize";
				tr.appendChild(td);
				jQuery("#tableImageSize", parent.document).append(tr); // (parent.document) : Execution outside Iframe
			}
		});
		jQuery("#tableImageSize .getwid .bouton", parent.document).css("display","inline-block");
		jQuery("#imageSizeFrame", parent.document).remove();
	}
});
