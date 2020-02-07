/* 
---------------------------------------------------------------------------------------------
Este script contém as funcionalidades do menu lateral.
---------------------------------------------------------------------------------------------
*/

$("menu ul li:has(ul)").each(function(){

    $(this).addClass("has-childs");

    $(this).find("> a")
        .attr({ href: 'javascript:void(0);' })
        .click(function() {
            
            if ($(this).parent().hasClass("open")) {
                $(this).parent().removeClass("open");
                $(this).find(".arrow").removeClass("up").addClass("down");
                return;
            }

            $(this).parent().addClass("open");
            $(this).find(".arrow").removeClass("down").addClass("up");
        })
        .append("<i class='arrow down'></i>");
});
