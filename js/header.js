const info = document.querySelector(".user-info");
const menu = document.querySelector(".popover-menu");
const caret = document.querySelector(".fa-caret-left");

info.addEventListener("mouseover", () => {
	console.log("hovered");
	menu.classList.add("show");
	caret.style.transform = "rotate(-90deg)";
});

info.addEventListener("mouseout", () => {
	menu.classList.remove("show");
	caret.style.transform = "rotate(0deg)";
});
