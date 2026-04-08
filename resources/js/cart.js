document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".add-to-cart-form").forEach((form) => {
        form.addEventListener("submit", function (e) {
            e.preventDefault();

            let formData = new FormData(this);
            let button = this.querySelector('button[type="submit"]');
            let originalText = button.innerHTML;

            button.disabled = true;
            button.innerHTML =
                '<i class="fas fa-spinner fa-spin"></i> Đang thêm...';

            fetch(this.action, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector(
                        'meta[name="csrf-token"]',
                    ).content,
                    Accept: "application/json",
                },
                body: formData,
            })
                .then((res) => res.json())
                .then((data) => {
                    if (data.success) {
                        alert(data.message);

                        // Update cart count
                        let cartCount = document.getElementById("cart-count");
                        if (cartCount) {
                            cartCount.innerText = data.cart_count;
                        }
                    } else {
                        alert(data.message);
                    }
                })
                .catch((err) => {
                    console.error(err);
                    alert("Có lỗi xảy ra!");
                })
                .finally(() => {
                    button.disabled = false;
                    button.innerHTML = originalText;
                });
        });
    });
});
