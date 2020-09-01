
let elements = document.querySelectorAll('select.version-select');

elements.forEach(function(select, key){
    select.addEventListener('change', function(){
        let versionUrl = this.options[this.selectedIndex].value;
        window.location.href = versionUrl;
    });
});


// $("menu ul li:has(ul)").each(function(){

//     $(this).addClass("has-childs");

//     $(this).find("> a")
//         .attr({ href: "javascript:void(0);" })
//         .click(function() {
            
//             if ($(this).parent().hasClass("open")) {
//                 $(this).parent().removeClass("open");
//                 $(this).find(".arrow").removeClass("up").addClass("down");
//                 return;
//             }

//             $(this).parent().addClass("open");
//             $(this).find(".arrow").removeClass("down").addClass("up");
//         })
//         .append("<i class='arrow down'></i>");
// });
