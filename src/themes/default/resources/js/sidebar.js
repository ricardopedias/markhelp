
$('menu ul li:has(ul)').each(function(){

    $(this).addClass('has-childs');

    $(this).find('> a')
        .attr({ href: 'javascript:void(0);' })
        .click(function() {
            
            if ($(this).parent().hasClass('open')) {
                $(this).parent().removeClass('open');
                //$(this).next('ul').removeClass('open');
                $(this).find('.arrow').removeClass('up').addClass('down');
                return;
            }

            // $('menu ul li:has(ul)').find('ul').removeClass('open');
            $(this).parent().addClass('open');
            //$(this).next('ul').addClass('open');
            $(this).find('.arrow').removeClass('down').addClass('up');
        })
        .append('<i class="arrow down"></i>');
});

// $('menu ul li:has(ul)').click(function(){
//     $(this).addClass('open');
// });
