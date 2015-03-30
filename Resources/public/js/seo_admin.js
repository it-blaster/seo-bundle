$(function () {
    $(document).on('click', '.seo-tab-button', function(e) {
        e.preventDefault();

        var id = $(this).attr('href');

        $('.seo-language-tabs li').removeClass('active');
        $(this).closest('li').addClass('active');

        $('.seo-tab-content .tab-pane').removeClass('active').addClass('hidden');
        $(id).removeClass('hidden').addClass('active');

    })
})