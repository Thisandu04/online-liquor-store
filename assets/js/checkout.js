document.addEventListener('DOMContentLoaded', function () {
    const paymentSelect = document.getElementById('payment_method');
    const cardFields = document.getElementById('cardFields');
    const form = document.getElementById('checkoutForm');

    function toggleCardFields() {
        if (!paymentSelect || !cardFields) return;
        if (paymentSelect.value === 'Card') {
            cardFields.style.display = 'block';
        } else {
            cardFields.style.display = 'none';
        }
    }

    if (paymentSelect) {
        toggleCardFields();
        paymentSelect.addEventListener('change', toggleCardFields);
    }

    if (form) {
        form.addEventListener('submit', function (e) {
            const method = paymentSelect ? paymentSelect.value : '';

            if (method === 'Card') {
                const cardNumber = document.getElementById('card_number').value.trim();
                const expiry = document.getElementById('card_expiry').value.trim();
                const cvv = document.getElementById('card_cvv').value.trim();

                if (!/^\d{16}$/.test(cardNumber)) {
                    e.preventDefault();
                    alert('Please enter a valid 16-digit card number.');
                    return;
                }
                if (!/^\d{2}\/\d{2}$/.test(expiry)) {
                    e.preventDefault();
                    alert('Please enter expiry in MM/YY format.');
                    return;
                }
                if (!/^\d{3}$/.test(cvv)) {
                    e.preventDefault();
                    alert('Please enter a valid 3-digit CVV.');
                    return;
                }
            }

            const quantity = parseInt(document.getElementById('quantity').value, 10);
            if (!quantity || quantity < 1) {
                e.preventDefault();
                alert('Please enter a valid quantity.');
            }
        });
    }
});