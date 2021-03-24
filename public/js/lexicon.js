/*---------------------------
 * WORD EDITION
 *--------------------------*/
document.querySelectorAll("[data-id]").forEach(element => {
    element.querySelector("input[name=content]").addEventListener(
        "input",
        debounce(() => {
            return fetch("update-word", {
                method: "POST",
                body: new FormData(element)
            });
        }, 2000)
    );
});

// inspired by http://www.kevinsubileau.fr/informatique/boite-a-code/php-html-css/javascript-debounce-throttle-reduire-appels-fonction.html
function debounce(func, wait) {
    let result;
    let timeout = null;
    return function() {
        var later = () => {
            timeout = null;
            result = func.apply(this, arguments);
        };
        // Tant que la fonction est appelée, on reset le timeout.
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
        return result;
    };
}

/*---------------------------
 * TAG EDITION
 *--------------------------*/
document.querySelectorAll(".tag").forEach(element => {
    element.addEventListener("click", event => {
        const tag = event.target;
        const form = new FormData();
        form.set("word_id", tag.dataset.wordId);
        form.set("tag_id", tag.dataset.tagId);
        const headers = new Headers();
        headers.append("X-CSRF-TOKEN", element.dataset.csrf);
        const url = element.classList.contains("active")
            ? "/remove-tag"
            : "/add-tag";
        fetch(url, {
            method: "POST",
            body: form,
            headers
        });
        element.classList.toggle("active");
    });
});
