(function ($) {

    $('#vc_ui-panel-edit-element').on('vcPanel.shown', function () {

        $('.woodmart-vc-datepicker').each(function () {
            $(this).find('.woodmart-vc-datepicker-value').datepicker({ dateFormat: 'yy/mm/dd' });
        });

    });

})(jQuery);
