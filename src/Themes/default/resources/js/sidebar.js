import Choices from "choices.js";

document.querySelectorAll("select.version-select").forEach(function(select, key){

    new Choices(select, {
        searchEnabled: false,
        loadingText: "Carregando...",
        noResultsText: "Sem resultados",
        noChoicesText: "Nada para selecionar",
        itemSelectText: "Selecione",
    });

    select.addEventListener("change", function(){
        let versionUrl = this.options[this.selectedIndex].value;
        window.location.href = versionUrl;
    });
});

document.querySelectorAll("menu ul li").forEach(function(element, key){

    let sublist = element.querySelector("ul");
    if (sublist !== null) {

        let item = element.querySelector("a");
        item.setAttribute("href", "javascript:void(0);");

        // item.insertAdjacentHTML("beforeend", "<i class="arrow down"></i>");

        item.addEventListener("click", function (event) {

            let parent = event.target.parentNode;
    
            if (parent.classList.contains("open") === true) {
                parent.classList.remove("open");
                let arrow = event.target.querySelector(".arrow");
                arrow.classList.remove("up");
                arrow.classList.remove("down")
                arrow.classList.add("down");
                return;
            }

            parent.classList.add("open");
            let arrow = event.target.querySelector(".arrow");
                arrow.classList.remove("up");
                arrow.classList.remove("down")
                arrow.classList.add("up");

            // Não segue o link
            event.preventDefault();
        }, false);
    }
});
