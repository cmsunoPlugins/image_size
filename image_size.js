//
// CMSUno
// Plugin Image Size
//
function f_save_imageSize(){
	let x=new FormData();
	x.set('action','save');
	x.set('unox',Unox);
	x.set('qual',document.getElementById("imageSizeQual").value);
	fetch('uno/plugins/image_size/image_size.php',{method:'post',body:x})
	.then(r=>r.text())
	.then(r=>f_alert(r));
}
//
function f_getwid_imageSize(){
	document.querySelectorAll('#tableImageSize tr').forEach(function(tr){
		let td=tr.querySelectorAll('td');
		if(!td.length)return;
		let s=td[2].innerText,w=td[3].innerText,x=new FormData();
		x.set('action','getwid');
		x.set('unox',Unox);
		x.set('src',s);
		fetch('uno/plugins/image_size/image_size.php',{method:'post',body:x})
		.then(r=>r.text())
		.then(function(r){
			td[4].innerText=r+' px'
			if(Number(w.substring(0,w.length-3))<Number(r)){
				td[5].insertAdjacentHTML('beforeend','<input class="inpckb" type="checkbox" /><br /><input class="inpnbr" type="number" value="'+w.substring(0,w.length-3)+'" />');
			}
			else td[5].insertAdjacentHTML('beforeend','<span class="ui-icon ui-icon-closethick" style="display:inline-block;"></span>');
		});
	});
	document.querySelectorAll('#tableImageSize th.getwid .bouton').forEach(function(e){e.remove();});
	document.querySelectorAll('#tableImageSize th.resize .bouton').forEach(function(e){e.style.display='inline-block';});
}
//
function f_resize_imageSize(){
	document.querySelectorAll('#tableImageSize tr').forEach(function(tr){
		let td=tr.querySelectorAll('td'),re=tr.querySelector('.resize'),s=tr.querySelector('.imsrc'),ok,w,x,b=0;
		if(td&&re&&s){
			ok=re.querySelector('.inpckb:checked');
			w=re.querySelector('.inpnbr[value]'); // Value not empty
			if(ok&&w){
				b=1;
				x=new FormData();
				x.set('action','resize');
				x.set('unox',Unox);
				x.set('src',s.innerText);
				x.set('wid',w.value);
				x.set('chap',td[1].innerText);
				fetch('uno/plugins/image_size/image_size.php',{method:'post',body:x})
				.then(r=>r.text())
				.then(function(r){
					if(r=='OK'){
						td[4].innerText=w.value+' px';
						re.insertAdjacentHTML('beforeend','<span class="ui-icon ui-icon-check" style="display:inline-block;"></span>');
					}
					else re.innerText=r;
				});
			}
		}
		if(b)document.getElementById('pubImageSize').style.display='block';
	});
}
