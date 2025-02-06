document.addEventListener("DOMContentLoaded", function () {
	fetchCartItemCount();

	// Event delegation for add to cart
	document.body.addEventListener("click", function (event) {
		if (event.target && event.target.matches(".add-to-cart-btn")) {
			const productId = event.target.dataset.productId;
			const quantityInput = document.getElementById(`quantity-${productId}`);
			const quantity = quantityInput ? quantityInput.value : 1;

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
				.then((data) => {
					if (data.success) {
						Toastify({
							text: "Item added to cart!",
							className: "toast-success",
							gravity: "bottom",
						}).showToast();

						fetchCartItemCount();
					} else {
						alert(data.message); // Show the failure message if there is one
					}
				})
				.catch((error) => console.error("Error:", error));
		}
	});

	// Event delegation for updating quantity
	document.body.addEventListener("input", function (event) {
		if (event.target && event.target.matches(".quantity-input")) {
			const input = event.target;
			const newQuantity = parseInt(input.value, 10);
			const productId = input.closest(".cart-item").dataset.productId;

			if (newQuantity >= 1 && newQuantity <= 10) {
				updateQuantity(productId, newQuantity);
			} else {
				alert("Quantity must be between 1 and 10");
				input.value = Math.min(Math.max(newQuantity, 1), 10); // Reset invalid value
			}
		}
	});

	// Event delegation for decrease and increase buttons
	document.body.addEventListener("click", function (event) {
		if (event.target && event.target.matches(".decrease")) {
			const input = event.target.closest(".cart-item").querySelector(".quantity-input");
			const currentQuantity = parseInt(input.value, 10);
			if (currentQuantity > 1) {
				updateQuantity(input.closest(".cart-item").dataset.productId, currentQuantity - 1);
			} else {
				removeFromCart(input.closest(".cart-item").dataset.productId);
			}
		} else if (event.target && event.target.matches(".increase")) {
			const input = event.target.closest(".cart-item").querySelector(".quantity-input");
			const currentQuantity = parseInt(input.value, 10);
			if (currentQuantity < 10) {
				updateQuantity(input.closest(".cart-item").dataset.productId, currentQuantity + 1);
			}
		}
	});

	// Event delegation for remove from cart button
	document.body.addEventListener("click", function (event) {
		if (event.target && event.target.matches(".remove-item")) {
			const productId = event.target.dataset.productId;
			removeFromCart(productId);
		}
	});
});

function updateQuantity(productId, newQuantity) {
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
				const input = document.querySelector(`.cart-item[data-product-id="${productId}"] .quantity-input`);
				if (input) {
					input.value = newQuantity;
					updateCartTotal();
					fetchCartItemCount();
				}
			} else {
				alert("Error updating cart");
			}
		});
}

function removeFromCart(productId) {
	fetch("/cart/remove", {
		method: "POST",
		headers: {
			"Content-Type": "application/json",
		},
		body: JSON.stringify({
			product_id: productId,
		}),
	})
		.then((response) => response.json())
		.then((data) => {
			if (data.success) {
				const cartItem = document.querySelector(`.cart-item[data-product-id="${productId}"]`);
				if (cartItem) {
					cartItem.remove();
					updateCartTotal();
				}
				fetchCartItemCount();
			} else {
				alert(data.message || "Error removing item from cart");
			}
		})
		.catch((error) => {
			console.error("Error:", error);
			alert("Something went wrong. Please try again.");
		});
}

function updateCartTotal() {
	let total = 0;

	document.querySelectorAll(".cart-item").forEach((item) => {
		const priceElement = item.querySelector(".item-price");
		const quantityElement = item.querySelector(".quantity-input");

		const priceText = priceElement.textContent.replace("₱", "").replace(/,/g, "").trim();
		const quantityText = quantityElement.value.trim();

		const price = parseInt(priceText);
		const quantity = parseInt(quantityText, 10);

		total += price * quantity;
	});

	const totalElement = document.querySelector(".cart-total");
	if (totalElement) {
		totalElement.textContent = `₱${total.toFixed(2)}`;
		Toastify({
			text: "Total price updated!",
			className: "toast-update",
			gravity: "bottom",
			offset: { y: 50 },
		}).showToast();
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

function fetchCartItemCount() {
	fetch("/cart/count")
		.then((response) => response.json())
		.then((data) => {
			if (data.count !== undefined) {
				document.querySelector(".cart-count").textContent = data.count;
			} else {
				console.error("Error fetching cart count:", data);
			}
		})
		.catch((error) => console.error("Error fetching cart item count:", error));
}
