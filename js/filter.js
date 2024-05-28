    // Get references to filter button and form
    const filterButton = document.getElementById('filterButton');
    const filterForm = document.getElementById('filterForm');

    // Add event listener to filter button
    filterButton.addEventListener('click', function() {
        // Toggle visibility of filter form
        filterForm.classList.toggle('show');
    });

    // Add event listener to clear button
    const clearFilterButton = document.getElementById('clearFilter');
    clearFilterButton.addEventListener('click', function() {
        // Reset filter form
        filterForm.reset();
        // Clear input values manually
        const inputs = filterForm.getElementsByTagName('input');
        for (let i = 0; i < inputs.length    ; i++) {
        inputs[i].value = '';
    }
    // Clear select value manually
    const select = filterForm.getElementsByTagName('select')[0];
    select.selectedIndex = 0;
});


document.addEventListener('DOMContentLoaded', function () {
    const buildingButtons = document.querySelectorAll('.building-button');
    const buildingInput = document.getElementById('building');
    const filterForm = document.getElementById('filterForm');
    const applyFilterButton = document.getElementById('applyFilterButton');
    const filterFields = document.querySelectorAll('.filter-field');
    const clearFilterButton = document.getElementById('clearFilter');

    buildingButtons.forEach(button => {
        button.addEventListener('click', function () {
            buildingButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            buildingInput.value = this.getAttribute('data-building');
            filterForm.submit();
        });
    });

    applyFilterButton.addEventListener('click', function (event) {
        let isAnyFieldFilled = false;
        filterFields.forEach(field => {
            if (field.value.trim() !== '') {
                isAnyFieldFilled = true;
            }
        });

        if (!isAnyFieldFilled) {
            event.preventDefault();
            const buildingValue = buildingInput.value;
            window.location.href = `?building=${buildingValue}`;
        }
    });

    clearFilterButton.addEventListener('click', function () {
        filterForm.reset();
        filterFields.forEach(field => {
            field.value = '';
        });
        buildingInput.value = '1'; // or the default building value
    });
});
