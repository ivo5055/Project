function bookmarkRoom(button) {
    const offerId = button.getAttribute('data-offer-id');
    
    // Send the offerId to the server using Fetch API
    fetch('bookmarkHandler.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ offerId: offerId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Function to handle bookmarking
            var bookmarkedRooms = JSON.parse(localStorage.getItem('bookmarkedRooms')) || [];
            var index = bookmarkedRooms.indexOf(parseInt(offerId));
            if (index === -1) {
                bookmarkedRooms.push(parseInt(offerId)); // Add room ID to bookmarks
                button.classList.add('bookmarked'); // Add class immediately
            } else {
                bookmarkedRooms.splice(index, 1); // Remove room ID from bookmarks
                button.classList.remove('bookmarked'); // Remove class immediately
            }
            localStorage.setItem('bookmarkedRooms', JSON.stringify(bookmarkedRooms)); // Save updated bookmarks
        } else {
            console.error('Failed to bookmark the room: ' + data.message);
        }
    })
    .catch(error => console.error('Error:', error));
}

// Function to update UI based on bookmarked rooms
function updateBookmarkUI() {
    var bookmarkedRooms = JSON.parse(localStorage.getItem('bookmarkedRooms')) || [];
    var bookmarkButtons = document.querySelectorAll('.bookmark-button');
    bookmarkButtons.forEach(function(button) {
        var roomId = button.getAttribute('data-offer-id');
        if (bookmarkedRooms.includes(parseInt(roomId))) { // Ensure roomId is treated as a number
            button.classList.add('bookmarked');
        } else {
            button.classList.remove('bookmarked');
        }
    });
}

// Update UI on page load
document.addEventListener('DOMContentLoaded', function() {
    updateBookmarkUI();
});
