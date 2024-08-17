document.addEventListener('DOMContentLoaded', function () {
    const basket = [];

    function updateBasketUI() {
        const basketItemsContainer = document.getElementById('basket-items');
        basketItemsContainer.innerHTML = '';
        basket.forEach(item => {
            const li = document.createElement('li');
            li.textContent = `${item.name} - $${item.price.toFixed(2)}`;
            basketItemsContainer.appendChild(li);
        });
    }

    document.querySelectorAll('.reserve-button').forEach(button => {
        button.addEventListener('click', function () {
            const itemId = this.dataset.itemId;
            const itemName = this.dataset.itemName;
            const itemPrice = parseFloat(this.dataset.itemPrice);
            basket.push({ id: itemId, name: itemName, price: itemPrice });
            updateBasketUI();
        });
    });

    document.getElementById('clear-basket').addEventListener('click', function () {
        basket.length = 0; // Clear the basket array
        updateBasketUI();
    });

    document.getElementById('confirm-basket').addEventListener('click', function () {
        if (basket.length > 0) {
            // Send basket data to the server via POST
            fetch('includes/reserve_item.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ items: basket })
            }).then(response => response.json())
              .then(data => {
                  alert('Reservation confirmed!');
                  basket.length = 0;
                  updateBasketUI();
              }).catch(error => console.error('Error:', error));
        } else {
            alert('Your basket is empty.');
        }
    });
});
