const token = sessionStorage.getItem("token");

if (token) {
    const response = fetch("http://localhost/home", {
        method: "GET", // or POST
        headers: {
            "Content-Type": "application/json",
            "Authorization": `Bearer ${token}` // Send token in Authorization header
        }
    });

    const result = response.json();
    console.log(result);
} else {
    console.log("No token available");
}

document.addEventListener("DOMContentLoaded", function () {
  const productGrid = document.querySelector(".product-grid");
  const paginationSummary = document.getElementById("pagination-summary");
  const pageInfo = document.getElementById("page-info");
  const prevBtn = document.getElementById("prev-btn");
  const nextBtn = document.getElementById("next-btn");
  const searchForm = document.getElementById("search-form");
  const searchInput = document.getElementById("search-input");
  const sortBySelect = document.getElementById("sort-by");
  const orderSelect = document.getElementById("order");
  const resetBtn = document.getElementById("reset-btn");
  const username = document.querySelector(".username");
  
  

  username.innerText = sessionStorage.getItem("username");

  let page = 1;
  let search = "";
  let sortBy = "";
  let order = "asc";

  function fetchProducts() {
      let apiUrl = `http://localhost/products?page=${page}&search=${encodeURIComponent(search)}&sort_by=${sortBy}&order=${order}`;

      fetch(apiUrl)
          .then(response => response.json())
          .then(data => {
              productGrid.innerHTML = "";
              data.products.forEach(product => {
                  const productCard = document.createElement("div");
                  productCard.classList.add("item-card");
                  if (product.quantity === 0) productCard.classList.add("out-of-stock-card");

                  productCard.innerHTML = `
                      <img src="${product.image}" alt="${product.name}" width="260px">
                      <h2>${product.name}</h2>
                      <p class="out-of-stock">${product.quantity === 0 ? "OUT OF STOCK" : ""}</p>
                      <p class="item-price">₱${parseFloat(product.price).toFixed(2)}</p>
                      <button class="add-to-cart-btn" type="button" data-product-id="${product.id}" ${product.quantity === 0 ? "disabled" : ""}>
                          ${product.quantity === 0 ? "Sold Out" : "Add to Cart"}
                      </button>
                  `;
                  productGrid.appendChild(productCard);
              });

              paginationSummary.textContent = `Showing ${data.startItem}–${data.endItem} of ${data.totalProducts} items`;
              pageInfo.textContent = `Page ${page} of ${Math.ceil(data.totalProducts / 40)}`;
              prevBtn.disabled = page === 1;
              nextBtn.disabled = page >= Math.ceil(data.totalProducts / 40);
          });
  }

  searchForm.addEventListener("submit", function (event) {
      event.preventDefault();
      search = searchInput.value.trim();
      page = 1;
      fetchProducts();
  });

  sortBySelect.addEventListener("change", function () {
      sortBy = this.value;
      fetchProducts();
  });

  orderSelect.addEventListener("change", function () {
      order = this.value;
      fetchProducts();
  });

  resetBtn.addEventListener("click", function () {
      search = "";
      sortBy = "";
      order = "";
      searchInput.value = "";
      sortBySelect.value = "";
      orderSelect.value = "";
      page = 1;
      fetchProducts();
  });

  prevBtn.addEventListener("click", function () {
      if (page > 1) {
          page--;
          fetchProducts();
      }
  });

  nextBtn.addEventListener("click", function () {
      page++;
      fetchProducts();
  });

  fetchProducts();
});
