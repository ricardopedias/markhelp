/* 
---------------------------------------------------------------------------------------------
Este script contém as funcionalidades do menu lateral.
---------------------------------------------------------------------------------------------
*/

$('#version-select').change(function () {

    var versions = [];
    $('#version-select option').each(function(){ versions.push($(this).val()) });

    var versionSelected = $("option:selected", this).val();

    var currentUrl = (window.location.href);
    $.each(versions, function(k, v){

        if (currentUrl.match(v)) {
            window.location.href = currentUrl.replace(v, versionSelected);
            return;
        }
    });
});

$("menu ul li:has(ul)").each(function(){

    $(this).addClass("has-childs");

    $(this).find("> a")
        .attr({ href: "javascript:void(0);" })
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
