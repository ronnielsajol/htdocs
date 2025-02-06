import { animate, scroll } from "https://cdn.jsdelivr.net/npm/motion@latest/+esm";

const loginForm = document.querySelector(".login-form");
const slider = document.querySelector(".hero-slider");

const sequence = [
	[slider, { backgroundPosition: "-450% 100%" }, { duration: 1.5, easing: "linear" }, { at: 1 }],
	[loginForm, { opacity: 1, transform: "translateY(0px)" }, { easing: "ease-in" }],
];

animate(sequence);

document.addEventListener("DOMContentLoaded", () => {
	const loginForm = document.getElementById("loginForm");
	const errorMessage = document.getElementById("login-error-message");
	const message = sessionStorage.getItem("registerMessage");

	if (message) {
		document.getElementById("message-box").innerText = message;
		sessionStorage.removeItem("registerMessage"); // Remove after showing
	}

	loginForm.addEventListener("submit", async (e) => {
		e.preventDefault();

		const formData = new FormData(loginForm);
		const requestData = Object.fromEntries(formData.entries());

		try {
			const response = await fetch("http://localhost/login", {
				method: "POST",
				headers: {
					"Content-Type": "application/json",
				},
				body: JSON.stringify(requestData),
			});

			const result = await response.json();

			if (result.success) {
				console.log("Success:", result);
				sessionStorage.setItem("username", result.username);
				sessionStorage.setItem("token", result.token); // Store token
				username.textContent = sessionStorage.getItem("username");
				const token = sessionStorage.getItem("token"); // Get the token

				if (token) {
					fetch("http://localhost/home", {
						method: "GET",
						headers: {
							"Content-Type": "application/json",
							Authorization: `Bearer ${token}`, // Send the token in the Authorization header
						},
					})
						.then((response) => {
							if (response.ok) {
								window.location.href = result.redirect;
							} else {
								throw new Error("Unauthorized Access");
							}
						}) // Parse the response JSON
						.catch((error) => console.error("Error:", error));
				} else {
					console.error("No token available");
				}
			} else {
				errorMessage.textContent = result.message || "Invalid credentials.";
				errorMessage.style.display = "block";
			}
		} catch (error) {
			console.error("Error:", error);
			errorMessage.textContent = "An error occurred. Please try again later.";
			errorMessage.style.display = "block";
		}
	});
});
