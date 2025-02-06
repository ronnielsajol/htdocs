const info = document.querySelector(".user-info");
const menu = document.querySelector(".popover-menu");
const caret = document.querySelector(".fa-caret-left");

info.addEventListener("mouseover", () => {
	console.log("hovered");
	menu.classList.add("show");
});

info.addEventListener("mouseout", () => {
	menu.classList.remove("show");
});
