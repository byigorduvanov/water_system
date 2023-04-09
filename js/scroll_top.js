const GoTop = document.querySelector(".gotopbtn");

function handleScroll() {
	window.pageYOffset > 100
		? GoTop.classList.add("active")
		: GoTop.classList.remove("active");
}

window.addEventListener("scroll", handleScroll);