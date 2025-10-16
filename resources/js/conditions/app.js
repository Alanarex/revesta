$(document).ready(function () {
    console.log('Conditions app initialized');
    $('select[name$="[model]"]').on('change', function () {
        const $modelSelect = $(this);
        const model = $modelSelect.val();
        const conditionId = $modelSelect.attr('name').match(/conditions\[(\d+)]/)[1];
        const attributeSelect = $(`select[name="conditions[${conditionId}][attribute]"]`);

        // Clear and refill attribute select
        const attributes = modelAttributes[model]?.attributes || {};
        attributeSelect.empty();

        $.each(attributes, function (key, label) {
            attributeSelect.append(`<option value="${key}">${label}</option>`);
        });
    });

    // Add new row
    $('.add-row').on('click', function () {
        const aidId = $(this).data('aid-id');
        const newId = 'new_' + Date.now();//to prevent override in values

        const firstModelKey = Object.keys(modelAttributes)[0];
        const firstAttributes = modelAttributes[firstModelKey]?.attributes || {};

        const modelOptions = Object.entries(modelAttributes).map(([key, model]) => {
            return `<option value="${key}" ${key === firstModelKey ? 'selected' : ''}>${model.label}</option>`;
        }).join('');

        const attributeOptions = Object.entries(firstAttributes).map(([key, label]) => {
            return `<option value="${key}">${label}</option>`;
        }).join('');

        const firstAttributeType = modelAttributes[firstModelKey]['types'][Object.keys(firstAttributes)[0]];

        const row = `
        <tr>
            <input type="hidden" name="conditions[${aidId}][${newId}][aid_id]" value="${aidId}">
            <td>
                <select name="conditions[${aidId}][${newId}][model]" class="form-select model-select">
                    ${modelOptions}
                </select>
            </td>
            <td>
                <select name="conditions[${aidId}][${newId}][attribute]" class="form-select attribute-select">
                    ${attributeOptions}
                </select>
            </td>
            <td>
                <select name="conditions[${aidId}][${newId}][operator]" class="form-select">
                    ${operatorSelect}
                </select>
            </td>
            <td>
                <input type="${firstAttributeType}" name="conditions[${aidId}][${newId}][value]" class="form-control" value="">
            </td>
            <td class="text-center p-1 border-0">
                <button type="button" class="btn p-0 m-0 border-0 bg-transparent text-danger remove-row">
                    <i class="fa-solid fa-xmark fa-lg"></i>
                </button>
            </td>
        </tr>
    `;

        $(this).closest('table').find('tbody').append(row);
    });

    // Remove row
    $(document).on('click', '.remove-row', function () {
        $(this).closest('tr').remove();
    });

    // Update attribute select when model changes
    $(document).on('change', '.model-select', function () {
    
        const model = $(this).val();
        const attributeSelect = $(this).closest('tr').find('.attribute-select');
        const attributes = modelAttributes[model]?.attributes || {};

        attributeSelect.empty();
        $.each(attributes, function (key, label) {
            attributeSelect.append(`<option value="${key}">${label}</option>`);
        });
    });

    // Trigger model change on load for existing rows
    $('select[name$="[model]"]').trigger('change');

});