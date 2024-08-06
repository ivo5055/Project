<script>
    function addItemToList(itemId, itemName, itemPrice) {
        const list = document.getElementById('selected-items-list');
        const listItem = document.createElement('li');
        listItem.dataset.itemId = itemId;
        listItem.innerHTML = `
            <span class='item-name'>${itemName}</span>
            <span class='item-price'>$${itemPrice.toFixed(2)}</span>
            <button type='button' onclick='removeItem(this)'>Remove</button>
        `;
        list.appendChild(listItem);
    }

    function removeItem(button) {
        const listItem = button.parentElement;
        listItem.remove();
    }

    function clearAllItems() {
        const list = document.getElementById('selected-items-list');
        list.innerHTML = '';
    }

    document.querySelectorAll('.reserve-button').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();

            const itemId = this.previousElementSibling.value;
            const itemName = this.previousElementSibling.previousElementSibling.value;
            const itemPrice = parseFloat(this.previousElementSibling.previousElementSibling.previousElementSibling.value);

            addItemToList(itemId, itemName, itemPrice);
        });
    });

    document.getElementById('confirm-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const list = document.getElementById('selected-items-list');
        const items = Array.from(list.children).map(item => item.dataset.itemId);

        if (items.length === 0) {
            alert('No items selected.');
            return;
        }

        fetch('reserve_items.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ items: items })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Items reserved successfully!');
                clearAllItems();
            } else {
                alert('Error reserving items.');
            }
        });
    });
</script>
