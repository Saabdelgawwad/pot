$(function() {
    window.advancedInput = function(advancedInput, source, modalUrl, maxEntries) {
        advancedInput = $(advancedInput);

        advancedInput.data('maxEntries', maxEntries);
        if (maxEntries > 0 && advancedInput.find('li').length - 1 >= maxEntries) {
            $('.add-item input', advancedInput).hide();
        }

        var input = advancedInput.find('.add-item input');

        input.autoGrowInput({comfortZone: 10, minWidth: 10, maxWidth: 20000});

        input.typeahead({
            source: source,
            sourceDefaults: {
                display: 'label',
                info: 'info',
            },
            itemSelected: function(item) {
                if (item.id instanceof Array) {
                    $.each(item.id, function() {
                        createAdvancedInputElement(advancedInput, this, item.type, this);
                    });
                } else {
                    createAdvancedInputElement(advancedInput, item.id, null, item.label);
                }
                $('.add-item input', advancedInput).val('').width(10);
            }
        });

        input.on('blur', function() {
            input.val('');
        });

        advancedInput.on('click', 'a.close', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var parent = $(this).parent();
            parent.fadeOut(350, function() {
                parent.remove();
            });
            $('.add-item input', advancedInput).show();
        });

        advancedInput.find('.modalButton').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            showModal(advancedInput, modalUrl);
        });

        advancedInput.find('.modalButtonGrid').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            showModalGrid(advancedInput);
        });

        advancedInput.on('click', function() {
            $(this).find('.add-item input').focus();
        });
    };

    function showModal(advancedInput, url) {
        $('#modal-advanced-input-grid-view').modal('show');
        $('#modal-advanced-input .modal-body').html('Lade...');


        $.ajax(url).success(function(data) {
            var html = [];
            $.each(data, function() {
                var checkbox = $('<input/>', {
                    type: 'checkbox',
                    id: 'checkbox-' + this.id,
                    value: this.id,
                    checked: advancedInput.find('input[value="' + this.id + '"]').length > 0
                }).data('item', this);

                html.push(checkbox, $('<label/>', {
                    for : 'checkbox-' + this.id
                }).append(' ' + this.label));
            });
            $('#modal-advanced-input .modal-body').html(html);
        });

        $('#modal-advanced-input-add-element').off('click').on('click', function(e) {
            e.preventDefault();
            $('#modal-advanced-input').modal('hide');

            clearAdvancedInput(advancedInput);
            $('#modal-advanced-input .modal-body input[type=checkbox]:checked').each(function() {
                attrName = $('#modal-advanced-input .modal-body label[for=' + $(this).attr('id') + ']').html();
                createAdvancedInputElement(advancedInput, $(this).attr('value'), null, attrName);
            });
        });
    }

    function showModalGrid(advancedInput) {
        $('#modal-advanced-input-grid-view').modal('show');

        $('#modal-advanced-input-grid-view').off('click', '.add-customer').on('click', '.add-customer', function(e) {
            e.preventDefault();

            var id = $(this).attr('data-id');
            var lastName = $(this).attr('data-lastname');
            var surName = $(this).attr('data-firstname');
            var fullName = lastName + ' ' + surName;

            clearAdvancedInput(advancedInput);

            createAdvancedInputElement(advancedInput, id, null, fullName);
            $('#modal-advanced-input-grid-view').modal('hide');

        });
    }
    function createAdvancedInputElement(advancedInput, id, type, label) {
        var element = '<li class="set">';
        element += '<span>' + window.encodeHtml(label) + '</span>';
        element += '<a class="close" href="#">&times;</a>';
        element += '<input type="hidden" name="' + window.encodeHtml(advancedInput.data('name')) + '[]" value="' + window.encodeHtml(id) + '" />';
        element += '</li>';

        $('.add-item', advancedInput).before(element);

        var maxEntries = advancedInput.data('maxEntries');
        if (maxEntries > 0 && advancedInput.find('li').length - 1 >= maxEntries) {
            $('.add-item input', advancedInput).hide();
        }
    }

    window.clearAdvancedInput = function(advancedInput) {
        $('li:not(.add-item)', advancedInput).remove();
        $('.add-item input', advancedInput).val('');
    };
});
