//https://tigu.hk.tlu.ee/~andrus.rinde/2022kevad/vr/styles/modal.css
let photoDir = "gallery_upload_normal/";
let photoId;

window.onload = function(){
	//kõik pisipildid paneme dialoogiakent avama
	let allThumbs = document.querySelector(".gallery").querySelectorAll(".thumbs");
	for(let i = 0; i < allThumbs.length; i ++){
		allThumbs[i].addEventListener("click", openModal);
	}
	document.querySelector("#modalclose").addEventListener("click", closeModal);
	document.querySelector("#modalimage").addEventListener("click", closeModal);
}

function openModal(e){
	photoId = e.target.dataset.id;
	for(let i = 1; i < 6; i ++){
		document.querySelector("#rate" + i).checked = false;
	}
	document.querySelector("#storeRating").addEventListener("click", storeRating);
	document.querySelector("#modalimage").src = photoDir + e.target.dataset.filename;
	document.querySelector("#modalcaption").innerHTML = e.target.alt;
	document.querySelector("#modal").showModal();
}

function closeModal(){
	document.querySelector("#modal").close();
}

function storeRating(){
	console.log("Hindame");
	let rating = 0;
	for(let i = 1; i < 6; i ++){
		if(document.querySelector("#rate" + i).checked){
			rating = i;
		}
		console.log(rating);
	}
	if(rating > 0){
		let webRequest = new XMLHttpRequest();
		webRequest.onreadystatechange = function(){
			if(this.readyState == 4 && this.status == 200){
				document.querySelector("#avgrating").innerHTML = this.responseText;
				document.querySelector("#storeRating").removeEventListener("click", storeRating);
			}
		};

		webRequest.open("GET", "store_photorating.php?photo=" + photoId + "&rating=" + rating, true);
		webRequest.send();
		
	}
}



