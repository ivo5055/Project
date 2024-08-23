let basket = [];

function addToBasket(itemId, itemName, itemPrice) {
    basket.push({ Id: itemId, name: itemName, price: itemPrice });
    renderBasket();
}

function renderBasket() {
    const basketList = document.getElementById('basket-items');
    basketList.innerHTML = '';
    let total = 0;
    basket.forEach(item => {
        total += parseFloat(item.price); // Calculate total price
        const li = document.createElement('li');
        li.innerHTML = `${item.name} - ${item.price}лв.`;
        basketList.appendChild(li);
    });

    // Add total price to the basket
    const totalItem = document.createElement('li');
    totalItem.innerHTML = `<strong>Общо: ${total.toFixed(2)}лв.</strong>`;
    basketList.appendChild(totalItem);
}

function clearBasket() {
    basket = [];
    renderBasket();
}

function confirmBasket() {
    if (basket.length === 0) {
        alert('Вашата кошница е празна.');
        return;
    }

    const confirmation = confirm('Сигурен ли сте, че искате да потвърдите резервирането на тези ястия?');
    if (!confirmation) {
        return;
    }

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhr.onload = function() {
        if (xhr.status === 200) {
            alert('Ястията бяха успешно резервирани!');
            clearBasket();
        } else {
            alert('Неуспешно резервиране на ястия. Моля, опитайте отново.');
        }
    };

    xhr.send("confirm_reserve=true&selected_items=" + JSON.stringify(basket));
}

