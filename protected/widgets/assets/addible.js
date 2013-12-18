(function($) {
    $.fn.addible = function(options) {
        var containerElement = $(this);
        var id = containerElement.attr('id');

        $('#' + id + '-add').on('click', function(e) {
            e.preventDefault();
            options.newId += 1;
            var newElement = $('#' + id + '-new');
            var model = newElement.clone().removeAttr('id').replaceInAllAttributes(options.placeholderId, options.newId).show();
            newElement.before(model);
            if (options.hideEmptyContainer) {
                containerElement.show();
            }
            if (options.afterAdd) {
                options.afterAdd(model);
            }
        });

        containerElement.on('click', '.' + id + '-remove', function(e) {
            e.preventDefault();
            $(this).tooltip('hide');
            var model = $(this).closest('.' + id + '-item');
            model.remove();
            if (model.data('id')) {
                containerElement.find('.' + id + '-item[data-id=' + model.data('id') + ']').remove();
            }
            if (options.hideEmptyContainer && containerElement.find('.' + id + '-item:visible').length === 0) {
                containerElement.hide();
            }
        });

        var defaultEditModels = {};
        containerElement.find('.' + id + '-edit-item').each(function() {
            defaultEditModels[$(this).data('id')] = $(this).clone();
        });

        containerElement.on('click', '.' + id + '-edit', function(e) {
            e.preventDefault();
            $(this).tooltip('hide');
            var model = $(this).closest('.' + id + '-view-item');
            containerElement.find('.' + id + '-edit-item[data-id=' + model.data('id') + ']').show();
            model.hide();
        });

        containerElement.on('click', '.' + id + '-view', function(e) {
            e.preventDefault();
            $(this).tooltip('hide');
            var model = $(this).closest('.' + id + '-edit-item');
            containerElement.find('.' + id + '-view-item[data-id=' + model.data('id') + ']').show();
            model.replaceWith(defaultEditModels[model.data('id')].clone().hide());
        });
    };
}(jQuery));
