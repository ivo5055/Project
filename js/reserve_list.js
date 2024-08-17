let basket = [];

    function addToBasket(itemId, itemName, itemPrice) {
        basket.push({ Id: itemId, name: itemName, price: itemPrice });
        renderBasket();
    }

    function renderBasket() {
        const basketList = document.getElementById('basket-items');
        basketList.innerHTML = '';
        basket.forEach(item => {
            const li = document.createElement('li');
            li.innerHTML = `${item.name} - $${item.price}`;
            basketList.appendChild(li);
        });
    }

    function clearBasket() {
        basket = [];
        renderBasket();
    }

    function confirmBasket() {
        if (basket.length === 0) {
            alert('Your basket is empty.');
            return;
        }

        const xhr = new XMLHttpRequest();
        xhr.open("POST", "", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

        xhr.onload = function() {
            if (xhr.status === 200) {
                alert('Items reserved successfully!');
                clearBasket(); // Clear the basket after confirming
            } else {
                alert('Failed to reserve items. Please try again.');
            }
        };

        xhr.send("confirm_reserve=true&selected_items=" + JSON.stringify(basket));
    }