
document.addEventListener('DOMContentLoaded', function () {
    const filterButton = document.getElementById('filterButton');
    const filterForm = document.getElementById('filterForm');
    const clearFilterButton = document.getElementById('clearFilter');
    const buildingButtons = document.querySelectorAll('.building-button');
    const buildingInput = document.getElementById('building');
    const applyFilterButton = document.getElementById('applyFilterButton');
    const filterFields = document.querySelectorAll('.filter-field');
    const otherDropdownButtons = document.querySelectorAll('.dropbtn, .dropbtnN');

    console.log('filterForm:', filterForm); // Debugging line

    // Toggle filter form visibility when filter button is clicked
    filterButton.addEventListener('click', function (event) {
        event.stopPropagation();
        filterForm.classList.toggle('show');
    });

    // Clear filter form when clear button is clicked
    clearFilterButton.addEventListener('click', function () {
        // Reset each form field manually
        filterFields.forEach(field => {
            if (field.type === 'checkbox' || field.type === 'radio') {
                field.checked = false;
            } else {
                field.value = '';
            }
        });
    
        // Reset building input to default value
        buildingInput.value = '1'; // or the default building value
    
    });
    

    // Handle building button clicks
    buildingButtons.forEach(button => {
        button.addEventListener('click', function () {
            buildingButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            buildingInput.value = this.getAttribute('data-building');
            filterForm.submit();
        });
    });

    // Prevent form submission if no fields are filled
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
});
