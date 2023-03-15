function addAutocompleteToMyInput(idOfInput, idOfAutocompleteSuggestions) {
    $('#' + idOfInput).on('input', function () {
        var value = $(this).val();
        $.ajax({
            url: '/',
            type: 'POST',
            data: {value: value},
            success: function (data) {
                $('#' + idOfAutocompleteSuggestions).html(data);
            }
        });
    });
}