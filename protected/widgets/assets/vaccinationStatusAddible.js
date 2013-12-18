(function($) {

    /*
     * Zu Freitext wechseln
     */
    $(document).on('click', '.js-change-to-free-text', function(e) {
        $(this).parent().hide().siblings('.free-text').show();
        $(this).parent().find('option:selected').removeAttr('selected');
    });
    
    /*
     * Zu Dropdown wechseln
     */
    $(document).on('click', '.js-change-to-dropdown', function(e) {
        $(this).parent().hide().siblings('.dropdown').show();
        $(this).parent().find('input').val('');
    });

}(jQuery));
