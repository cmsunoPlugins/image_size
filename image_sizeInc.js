document.querySelectorAll('#pagesContent img').forEach(function(im){
	if(im.getAttribute('data-echo'))im.src=im.getAttribute('data-echo');
});
window.onload=function(){
	document.querySelectorAll('#pagesContent img').forEach(function(im){
		let s=im.getAttribute('src'),w=im.getBoundingClientRect().width,tr,td,c;
		if(s.substring(0,6)=='files/'&&w!=0){
			tr=document.createElement('tr');
			td=document.createElement('td');td.innerHTML='<img src="'+s+'" style="max-width:90px" />';
			tr.appendChild(td);
			td=document.createElement('td');
			c=im.closest('.blocChap');
			if(c)td.innerHTML=c.id.substring(0,c.length-8);
			tr.appendChild(td);
			td=document.createElement('td');td.innerText=s;td.className="imsrc";
			tr.appendChild(td);
			td=document.createElement('td');td.innerText=parseInt(w+.5)+' px';
			tr.appendChild(td);
			td=document.createElement('td');td.className="getwid";
			tr.appendChild(td);
			td=document.createElement('td');td.className="resize";
			tr.appendChild(td);
			parent.document.getElementById('tableImageSize').appendChild(tr); // outside Iframe
		}
	});
	parent.document.querySelectorAll('#tableImageSize .getwid .bouton').forEach(function(e){e.style.display='inline-block';});
	parent.document.getElementById('imageSizeFrame').remove();
}
