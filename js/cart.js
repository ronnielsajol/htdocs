document.addEventListener("DOMContentLoaded", function () {
	// Add to cart
	document.querySelectorAll(".add-to-cart-btn").forEach((button) => {
		button.addEventListener("click", function () {
			const productId = this.dataset.productId;
			const quantity = this.dataset.quantity || 1;

			console.log(productId, quantity);
			fetch("/cart/add", {
				method: "POST",
				headers: {
					"Content-Type": "application/json",
				},
				body: JSON.stringify({
					product_id: productId,
					quantity: quantity,
				}),
			})
				.then((response) => response.json())
				.then((data) => console.log(data))
				.catch((error) => console.error("Error:", error));
		});
	});

	// Update quantity
	document.querySelectorAll(".cart-item").forEach((item) => {
		const input = item.querySelector(".quantity-input");
		const decreaseButton = item.querySelector(".decrease");
		const increaseButton = item.querySelector(".increase");

		const updateQuantity = (newQuantity) => {
			const productId = item.dataset.productId;

			fetch("/cart/update", {
				method: "POST",
				headers: {
					"Content-Type": "application/json",
				},
				body: JSON.stringify({
					action: "update",
					product_id: productId,
					quantity: newQuantity,
				}),
			})
				.then((response) => response.json())
				.then((data) => {
					if (data.success) {
						// Update the input value with the new quantity
						input.value = newQuantity;
						// Optionally, update the total without reloading
						updateCartTotal();
					} else {
						alert("Error updating cart");
					}
				});
		};

		// Listener for the input field
		input.addEventListener("change", function () {
			const newQuantity = parseInt(this.value, 10);
			if (newQuantity >= 1 && newQuantity <= 10) {
				updateQuantity(newQuantity);
			} else {
				alert("Quantity must be between 1 and 10");
				this.value = Math.min(Math.max(newQuantity, 1), 10); // Reset invalid value
			}
		});

		// Listener for the decrease button
		decreaseButton.addEventListener("click", () => {
			const currentQuantity = parseInt(input.value, 10);
			if (currentQuantity > 1) {
				updateQuantity(currentQuantity - 1);
			}
		});

		// Listener for the increase button
		increaseButton.addEventListener("click", () => {
			const currentQuantity = parseInt(input.value, 10);
			if (currentQuantity < 10) {
				updateQuantity(currentQuantity + 1);
			}
		});
	});

	// Remove from cart
	document.querySelectorAll(".remove-item").forEach((button) => {
		button.addEventListener("click", function () {
			const productId = this.dataset.productId;

			fetch("/cart/remove", {
				method: "POST",
				headers: {
					"Content-Type": "application/json",
				},
				body: JSON.stringify({
					product_id: productId,
				}),
			})
				.then((response) => {
					// Check if the response is OK (status 200–299)
					if (!response.ok) {
						throw new Error(`HTTP error! Status: ${response.status}`);
					}
					return response.json(); // Parse response only if it's valid JSON
				})
				.then((data) => {
					if (data.success) {
						// Remove item from DOM without reload
						const cartItem = document.querySelector(`.cart-item[data-product-id="${productId}"]`);
						if (cartItem) {
							cartItem.remove();

							updateCartTotal();
						}
					} else {
						alert(data.message || "Error removing item from cart");
					}
				})
				.catch((error) => {
					console.error("Error:", error);
					alert("Something went wrong. Please try again.");
				});
		});
	});
});

function updateCartTotal() {
	let total = 0;

	// Loop through each cart item to calculate the total
	document.querySelectorAll(".cart-item").forEach((item) => {
		const priceElement = item.querySelector(".item-price");
		const quantityElement = item.querySelector(".quantity-input");

		if (!priceElement || !quantityElement) {
			console.error("Missing price or quantity element for item:", item);
			return;
		}

		const priceText = priceElement.textContent.replace("₱", "").replace(/,/g, "").trim();

		console.log(priceText);
		const quantityText = quantityElement.value.trim();

		const price = parseInt(priceText);
		console.log("price:", price);
		const quantity = parseInt(quantityText, 10);

		if (isNaN(price) || isNaN(quantity)) {
			console.error("Invalid price or quantity:", { price, quantity, item, priceElement });
			return;
		}

		total += price * quantity;
		console.log(price, quantity);
	});
	console.log("Updated Total:", total);

	// Update the total in the DOM
	const totalElement = document.querySelector(".cart-total");
	if (totalElement) {
		totalElement.textContent = `₱${total.toFixed(2)}`;
	} else {
		console.error("Total element not found in the DOM.");
	}

	const checkoutBtn = document.querySelector(".checkout-btn");
	if (checkoutBtn) {
		if (total == 0 || total == 0.0) {
			checkoutBtn.disabled = true;
			checkoutBtn.classList.add("disabled");
		} else {
			checkoutBtn.disabled = false;
			checkoutBtn.classList.remove("disabled");
		}
	}
}
