document.addEventListener('DOMContentLoaded', function () {
    const reserveButtons = document.querySelectorAll('.reserve-button');
    const selectedItemsContainer = document.getElementById('selected-items');
    const confirmForm = document.getElementById('confirm-form');
    const selectedItemsData = document.getElementById('selected-items-data');
    const clearButton = document.getElementById('clear-selection');

    let selectedItems = [];

    reserveButtons.forEach(button => {
        button.addEventListener('click', function (event) {
            event.preventDefault();

            const itemName = this.closest('li').querySelector('.item-name').textContent;
            const itemPrice = this.closest('li').querySelector('.item-price').textContent;

            selectedItems.push({ name: itemName, price: itemPrice });
            renderSelectedItems();
        });
    });

    function renderSelectedItems() {
        selectedItemsContainer.innerHTML = '';
        selectedItems.forEach((item, index) => {
            const li = document.createElement('li');
            li.textContent = `${item.name} - ${item.price}`;
            selectedItemsContainer.appendChild(li);
        });

        confirmForm.style.display = selectedItems.length > 0 ? 'block' : 'none';
        selectedItemsData.value = JSON.stringify(selectedItems);
    }

    clearButton.addEventListener('click', function () {
        selectedItems = [];
        renderSelectedItems();
    });
});
