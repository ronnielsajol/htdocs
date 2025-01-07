const info = document.querySelector(".user-info");
const menu = document.querySelector(".popover-menu");

console.log(info);

info.addEventListener("mouseover", () => {
	console.log("hovered");
	menu.style.display = "block";
});

info.addEventListener("mouseout", () => {
	menu.style.display = "none";
});
