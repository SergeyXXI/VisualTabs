function toggleForm(formsBlock, headers, forms, links)
{	
	for(var el of headers)
	{
		if(el.classList.contains("hidden")) el.classList.remove("hidden");
		else  								el.classList.add("hidden");
	}
	
	for(el of forms)
	{
		if(el.classList.contains("hidden")) el.classList.remove("hidden");
		else  								el.classList.add("hidden");
	}
	
	for(el of links)
	{
		if(el.classList.contains("hidden")) el.classList.remove("hidden");
		else  								el.classList.add("hidden");
	}

	const err = formsBlock.querySelector("span");
	
	if(err)
	{
		if(err.id === "log-err")
		{
			forms[0].classList.contains("hidden") ? 
			err.style.display = "none" 			  :
			err.style.display = "inline";
		}
		else
		{
			forms[1].classList.contains("hidden") ? 
			err.style.display = "none" 			  :
			err.style.display = "inline";
		}
	}
	
}

if(document.getElementById("wrapper"))
{
	const wrapper 		  = document.getElementById("wrapper"),
		  formsBlock      = document.getElementById("forms-block"),
	 	  guestContent    = document.getElementById("guest-content"),
	 	  tabForm         = document.getElementById("tabform"),
	 	  inputOrder      =	document.getElementById("input-order"),		  
	 	  forms    	      = formsBlock.getElementsByTagName("FORM"),
	  	  headers  	      = formsBlock.getElementsByTagName("h1"),
	  	  links           = formsBlock.getElementsByTagName("a"),		  
		  closeBtnTabform = tabForm.querySelector("a"),
		  tabFormInput 	  = tabForm.querySelector("input[type='text']"), 
		  tabFormSubmit   = tabForm.querySelector("input[type='submit']");		
		
	var guestTab;		

	if(document.getElementById("reg-err"))     toggleForm(formsBlock, headers, forms, links);
	if(document.getElementById("reg-success")) links[0].remove();

	wrapper.addEventListener("click", e =>
	{
		if(!e.target.classList.contains("tab-container") 			 &&
		   (e.target != tabForm && e.target.parentNode != tabForm)   &&
		   tabForm.style.display === "block")
		{
			tabForm.style.display = "none";
			guestTab.style.cursor = "pointer";
		} 
	});

	formsBlock.addEventListener("click", e =>
	{		
		if(e.target.tagName == "A")
		{			
			e.preventDefault();
			toggleForm(formsBlock, headers, forms, links);			
		} 
		
	});	

	guestContent.addEventListener("click", (e) =>
	{
		if(e.target.classList.contains("tab-container"))
		{			
			if(tabForm.style.display == "block") guestTab.style.cursor = "pointer";
			guestTab = e.target;			
			guestTab.style.cursor = "default";
			guestTab.appendChild(tabForm);
			tabForm.style.display = "block";
			inputOrder.value = guestTab.dataset.item;
									
		} 
		
	});

	closeBtnTabform.addEventListener("click", () =>
	{
		tabForm.style.display = "none";
		guestTab.style.cursor = "pointer";
	});

	tabFormInput.addEventListener("input", () =>
	{		
		if(tabFormInput.value.length > 2) tabFormSubmit.removeAttribute("disabled");
		else 					  		  tabFormSubmit.setAttribute("disabled", "disabled");
	});
}
else
{	
	if(!document.getElementById("content"))
	{		
		const tipContainer = `<div id="tip-container">
								  <h1>Добавьте первую закладку</h1>
								  <img src="src/arrow.png">								
							  </div>`;
		
		document.querySelector("a[href='exit.php']").insertAdjacentHTML("afterend", tipContainer);

		const img = document.querySelector("img[src='src/arrow.png']");		
		img.addEventListener("click", () => document.getElementById("url").focus());
	}

	
	const logTabFormInput = document.querySelector("#log-tabform input[type='text']");
	const logTabFormSubmit = document.querySelector("#log-tabform input[type='submit']");

	logTabFormInput.addEventListener("input", () =>
	{		
		if(logTabFormInput.value.length > 2) logTabFormSubmit.removeAttribute("disabled");
		else 					  		     logTabFormSubmit.setAttribute("disabled", "disabled");
	});

}






