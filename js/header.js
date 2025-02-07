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

document.addEventListener("DOMContentLoaded", function () {
    // Select elements safely
    const info = document.querySelector(".user-info");
    const menu = document.querySelector(".popover-menu");
    const caret = document.querySelector(".fa-caret-left");
    const menuToggle = document.querySelector(".menu-toggle");
    const headerNav = document.querySelector(".header-nav");

    // Ensure elements exist before adding event listeners
    if (info && menu && caret) {
        info.addEventListener("mouseover", () => {
            console.log("hovered");
            menu.classList.add("show");
            caret.style.transform = "rotate(-90deg)";
        });

        info.addEventListener("mouseout", () => {
            menu.classList.remove("show");
            caret.style.transform = "rotate(0deg)";
        });
    } else {
        console.warn("One or more elements for user-info hover effect not found.");
    }

    if (menuToggle && headerNav) {
        menuToggle.addEventListener("click", function () {
            headerNav.classList.toggle("active");
        });

        // Close the menu when a link is clicked
        headerNav.addEventListener("click", function (e) {
            if (e.target.tagName === "A") {
                headerNav.classList.remove("active");
            }
        });
    } else {
        console.warn("Menu toggle or header navigation not found.");
    }

    // Toggle user popover menu safely
    if (info && menu) {
        info.addEventListener("click", function (e) {
            e.stopPropagation();
            menu.style.display = menu.style.display === "block" ? "none" : "block";
        });

        // Close popover menu when clicking outside
        document.addEventListener("click", function () {
            menu.style.display = "none";
        });
    }
});
