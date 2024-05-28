const buildingButtons = document.querySelectorAll('.building-button');

    // Add event listener to building buttons
    buildingButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remove the active class from all building buttons
            buildingButtons.forEach(btn => {
                btn.classList.remove('active');
            });

            // Add the active class to the clicked button
            button.classList.add('active');

            // Get the value of the building from the data attribute
            const building = button.getAttribute('data-building');
            // Redirect to the page with the selected building as a parameter
            window.location.href = `offers.php?building=${building}`;
        });
    });

    // Check if there is a building parameter in the URL and highlight the corresponding button
    const urlParams = new URLSearchParams(window.location.search);
    const selectedBuilding = urlParams.get('building');
    if (selectedBuilding) {
        // Remove active class from all buttons
        buildingButtons.forEach(btn => {
            btn.classList.remove('active');
        });
        // Add active class to the button corresponding to the selected building
        const selectedButton = document.querySelector(`.building-button[data-building="${selectedBuilding}"]`);
        if (selectedButton) {
            selectedButton.classList.add('active');
        }
    }



    