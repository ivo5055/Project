document.addEventListener('DOMContentLoaded', () => {
    const container = document.querySelector('.reserved-items-container') as HTMLElement | null;
    const itemList = document.getElementById('selected-items') as HTMLUListElement | null;

    // Check if container and itemList are not null
    if (!container || !itemList) {
        console.error('Required elements not found.');
        return;
    }

    // Function to scroll to the bottom of the container
    function scrollToBottom() {
        if (container) {
            container.scrollTop = container.scrollHeight;
        }
    }

    // Example function to add a new item (replace with your actual item-adding logic)
    function addItem(itemContent: string) {
        if (itemList) {
            const listItem = document.createElement('li');
            listItem.textContent = itemContent;
            itemList.appendChild(listItem);

            // Scroll to the bottom after adding the new item
            scrollToBottom();
        }
    }

    // Simulate adding items (remove or replace this with your actual logic)
    // addItem('New Item 1');
    // addItem('New Item 2');

    // Example of handling dynamically added items
    // If you have a way to add items dynamically via JavaScript, ensure to call scrollToBottom() after each addition.
});
