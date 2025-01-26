import { animate, scroll } from "https://cdn.jsdelivr.net/npm/motion@latest/+esm";

const loginForm = document.querySelector(".login-form");
const slider = document.querySelector(".hero-slider");

const sequence = [
	[slider, { backgroundPosition: "-450% 100%" }, { duration: 1.5, easing: "linear" }, { at: 1 }],
	[loginForm, { opacity: 1, transform: "translateY(0px)" }, { easing: "ease-in" }],
];

animate(sequence);
