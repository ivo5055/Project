function toggleDropdown(dropdownId) {
    var dropdownContent = document.getElementById(dropdownId);
    var dropdowns = document.getElementsByClassName("dropdown-content");

    // Close all dropdowns except the one being toggled
    for (var i = 0; i < dropdowns.length; i++) {
        if (dropdowns[i].id !== dropdownId) {
            dropdowns[i].style.display = 'none';
        }
    }

    // Toggle the current dropdown
    if (dropdownContent.style.display === 'block') {
        dropdownContent.style.display = 'none';
    } else {
        dropdownContent.style.display = 'block';
    }
}

// Close the dropdown if the user clicks outside of it
window.onclick = function(event) {
    if (!event.target.matches('.dropbtn') && !event.target.matches('.dropbtnN img')) {
        var dropdowns = document.getElementsByClassName("dropdown-content");
        for (var i = 0; i < dropdowns.length; i++) {
            var openDropdown = dropdowns[i];
            if (openDropdown.style.display === 'block') {
                openDropdown.style.display = 'none';
            }
        }
    }
}