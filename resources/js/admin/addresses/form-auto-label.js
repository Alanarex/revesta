/**
 * Auto-generate label from address fields
 * This script handles real-time label generation for both create and edit address forms
 */

document.addEventListener('DOMContentLoaded', function() {
    const addressForm = document.getElementById('addressForm');
    
    // Only run on address forms
    if (!addressForm) {
        return;
    }

    // Get form fields
    const numberField = document.getElementById('number');
    const streetField = document.getElementById('street');
    const postalCodeField = document.getElementById('postal_code');
    const cityField = document.getElementById('city');
    const labelField = document.getElementById('label');

    // Function to generate label
    function generateLabel() {
        // Collect values
        const number = (numberField?.value || '').trim();
        const street = (streetField?.value || '').trim();
        const postalCode = (postalCodeField?.value || '').trim();
        const city = (cityField?.value || '').trim();

        // Build label: "number street, postalCode city"
        let label = '';
        
        if (number) {
            label += number + ' ';
        }
        
        if (street) {
            label += street;
        }
        
        if (postalCode || city) {
            if (label) label += ', ';
            if (postalCode) label += postalCode + ' ';
            if (city) label += city;
        }

        // Update label field
        if (labelField) {
            labelField.value = label.trim();
        }
    }

    // Add event listeners to all address fields
    if (numberField) {
        numberField.addEventListener('input', generateLabel);
        numberField.addEventListener('change', generateLabel);
    }

    if (streetField) {
        streetField.addEventListener('input', generateLabel);
        streetField.addEventListener('change', generateLabel);
    }

    if (postalCodeField) {
        postalCodeField.addEventListener('input', generateLabel);
        postalCodeField.addEventListener('change', generateLabel);
    }

    if (cityField) {
        cityField.addEventListener('input', generateLabel);
        cityField.addEventListener('change', generateLabel);
    }

    // Generate label on page load (for edit page)
    generateLabel();
});
