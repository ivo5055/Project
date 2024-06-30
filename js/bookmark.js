    // bookmark.js

    // Function to toggle bookmark status
    function bookmarkRoom(button) {
        const offerId = button.getAttribute('data-offer-id');
        const userId = getUserId(); // Function to get the current user's ID

        // Determine action based on current state
        const action = button.classList.contains('bookmarked') ? 'remove' : 'add';

        // Send the offerId and action to the server using Fetch API
        fetch('bookmarkHandler.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ offerId: offerId, userId: userId, action: action })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update local storage and UI
                const localStorageKey = `bookmarkedRooms_${userId}`;
                var bookmarkedRooms = JSON.parse(localStorage.getItem(localStorageKey)) || [];
                var index = bookmarkedRooms.indexOf(parseInt(offerId));
                if (index === -1 && action === 'add') {
                    bookmarkedRooms.push(parseInt(offerId)); // Add room ID to bookmarks
                    button.classList.add('bookmarked'); // Add class immediately
                    button.textContent = 'Unbookmark'; // Update button text
                } else if (index !== -1 && action === 'remove') {
                    bookmarkedRooms.splice(index, 1); // Remove room ID from bookmarks
                    button.classList.remove('bookmarked'); // Remove class immediately
                    button.textContent = 'Bookmark'; // Update button text
                }
                localStorage.setItem(localStorageKey, JSON.stringify(bookmarkedRooms)); // Save updated bookmarks
            } else {
                console.error('Failed to bookmark the room: ' + data.message);
            }
        })
        .catch(error => console.error('Error:', error));
    }

    // Function to update UI based on local storage bookmarks
    function updateBookmarkUI() {
        const userId = getUserId(); // Function to get the current user's ID
        const localStorageKey = `bookmarkedRooms_${userId}`;
        var bookmarkedRooms = JSON.parse(localStorage.getItem(localStorageKey)) || [];
        var bookmarkButtons = document.querySelectorAll('.bookmark-button');
        bookmarkButtons.forEach(function(button) {
            var roomId = button.getAttribute('data-offer-id');
            if (bookmarkedRooms.includes(parseInt(roomId))) { // Ensure roomId is treated as a number
                button.classList.add('bookmarked');
                button.textContent = 'Unbookmark';
            } else {
                button.classList.remove('bookmarked');
                button.textContent = 'Bookmark';
            }
        });
    }

    // Function to get user ID from hidden input or global variable
    function getUserId() {
        // Retrieve the user ID from a hidden input field or a global JavaScript variable set by the server
        return document.getElementById('userId').value;
    }

    // Fetch and update bookmarks on page load
    document.addEventListener('DOMContentLoaded', function() {
        fetch('getBookmarks.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const userId = getUserId(); // Function to get the current user's ID
                const localStorageKey = `bookmarkedRooms_${userId}`;
                localStorage.setItem(localStorageKey, JSON.stringify(data.bookmarks));
                updateBookmarkUI();
            } else {
                console.error('Failed to fetch bookmarks: ' + data.message);
            }
        })
        .catch(error => console.error('Error:', error));
    });
