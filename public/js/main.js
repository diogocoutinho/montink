// Cart Management
const cart = {
  items: [],

  addItem(product) {
    const existingItem = this.items.find(
      (item) =>
        item.id === product.id && item.variation_id === product.variation_id
    );

    if (existingItem) {
      existingItem.quantity += product.quantity;
    } else {
      this.items.push(product);
    }

    this.updateCartCount();
    this.saveCart();
  },

  removeItem(productId, variationId = null) {
    this.items = this.items.filter(
      (item) => !(item.id === productId && item.variation_id === variationId)
    );
    this.updateCartCount();
    this.saveCart();
  },

  updateQuantity(productId, variationId, quantity) {
    const item = this.items.find(
      (item) => item.id === productId && item.variation_id === variationId
    );

    if (item) {
      item.quantity = quantity;
      this.updateCartCount();
      this.saveCart();
    }
  },

  clear() {
    this.items = [];
    this.updateCartCount();
    this.saveCart();
  },

  getTotal() {
    return this.items.reduce((total, item) => {
      return total + item.price * item.quantity;
    }, 0);
  },

  updateCartCount() {
    const count = this.items.reduce((total, item) => total + item.quantity, 0);
    $(".cart-count").text(count);
  },

  saveCart() {
    localStorage.setItem("cart", JSON.stringify(this.items));
  },

  loadCart() {
    const savedCart = localStorage.getItem("cart");
    if (savedCart) {
      this.items = JSON.parse(savedCart);
      this.updateCartCount();
    }
  },
};

// CEP Validation
function validateCEP(cep) {
  cep = cep.replace(/\D/g, "");
  if (cep.length !== 8) {
    return false;
  }
  return true;
}

async function fetchCEP(cep) {
  if (!validateCEP(cep)) {
    return null;
  }

  try {
    const response = await fetch(`https://viacep.com.br/ws/${cep}/json/`);
    const data = await response.json();

    if (data.erro) {
      return null;
    }

    return {
      address: data.logradouro,
      city: data.localidade,
      state: data.uf,
    };
  } catch (error) {
    console.error("Error fetching CEP:", error);
    return null;
  }
}

// Shipping Cost Calculation
function calculateShipping(subtotal) {
  if (subtotal >= 200) {
    return 0;
  } else if (subtotal >= 52 && subtotal <= 166.59) {
    return 15;
  }
  return 20;
}

// Coupon Validation
function validateCoupon(coupon, subtotal) {
  if (!coupon.is_active) {
    return false;
  }

  const now = new Date();
  const validFrom = new Date(coupon.valid_from);
  const validUntil = new Date(coupon.valid_until);

  if (now < validFrom || now > validUntil) {
    return false;
  }

  if (subtotal < coupon.min_order_value) {
    return false;
  }

  return true;
}

// Initialize
$(document).ready(function () {
  cart.loadCart();

  // Add to Cart Button Handler
  $(".add-to-cart").on("click", function (e) {
    e.preventDefault();
    const productId = $(this).data("product-id");
    const variationId = $(this).data("variation-id");
    const quantity = parseInt($(`#quantity-${productId}`).val()) || 1;
    const price = parseFloat($(this).data("price"));
    const name = $(this).data("name");

    cart.addItem({
      id: productId,
      variation_id: variationId,
      quantity: quantity,
      price: price,
      name: name,
    });

    showAlert("Produto adicionado ao carrinho!", "success");
  });

  // CEP Input Handler
  $("#shipping_zipcode").on("blur", async function () {
    const cep = $(this).val();
    const addressData = await fetchCEP(cep);

    if (addressData) {
      $("#shipping_address").val(addressData.address);
      $("#shipping_city").val(addressData.city);
      $("#shipping_state").val(addressData.state);
    } else {
      showAlert("CEP inválido ou não encontrado", "danger");
    }
  });

  // Coupon Form Handler
  $("#coupon-form").on("submit", async function (e) {
    e.preventDefault();
    const couponCode = $("#coupon_code").val();
    const subtotal = cart.getTotal();

    try {
      const response = await fetch(`/api/coupons/validate/${couponCode}`);
      const coupon = await response.json();

      if (validateCoupon(coupon, subtotal)) {
        const discount = coupon.discount_percentage
          ? (subtotal * coupon.discount_percentage) / 100
          : coupon.discount_amount;

        $("#discount_amount").val(discount);
        updateOrderTotal();
        showAlert("Cupom aplicado com sucesso!", "success");
      } else {
        showAlert("Cupom inválido ou não aplicável", "danger");
      }
    } catch (error) {
      showAlert("Erro ao validar cupom", "danger");
    }
  });
});

// Utility Functions
function showAlert(message, type = "info") {
  const alert = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
  $(".alert-container").html(alert);
}

function updateOrderTotal() {
  const subtotal = cart.getTotal();
  const shipping = calculateShipping(subtotal);
  const discount = parseFloat($("#discount_amount").val()) || 0;
  const total = subtotal + shipping - discount;

  $("#subtotal").text(`R$ ${subtotal.toFixed(2)}`);
  $("#shipping_cost").text(`R$ ${shipping.toFixed(2)}`);
  $("#discount").text(`R$ ${discount.toFixed(2)}`);
  $("#total").text(`R$ ${total.toFixed(2)}`);
}
