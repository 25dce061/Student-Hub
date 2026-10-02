
document.addEventListener("DOMContentLoaded", function () {

    const search = document.getElementById("faq-search");
    const category = document.getElementById("faq-category");
    const items = document.querySelectorAll(".faq-item");
    const count = document.getElementById("faq-count");

    function filterFAQ() {

        let text = search.value.toLowerCase();
        let type = category.value;
        let visible = 0;

        items.forEach(function (item) {

            let question = item.querySelector("summary").textContent.toLowerCase();
            let answer = item.querySelector(".faq-answer").textContent.toLowerCase();
            let itemCategory = item.dataset.category;

            let searchMatch =
                question.includes(text) || answer.includes(text);

            let categoryMatch =
                type === "all" || itemCategory === type;

            if (searchMatch && categoryMatch) {
                item.style.display = "";
                visible++;
            } else {
                item.style.display = "none";
            }
        });

        if (visible === 0)
            count.textContent = "No matching questions found.";
        else
            count.textContent = "Showing " + visible + " questions";
    }

    search.addEventListener("input", filterFAQ);
    category.addEventListener("change", filterFAQ);

    filterFAQ();


    /* Ask Question Modal */

    const modal = document.getElementById("ask-modal");
    const open = document.getElementById("open-ask-modal");
    const close = document.getElementById("close-ask-modal");
    const form = document.getElementById("ask-form");
    const success = document.getElementById("ask-success");

    open.onclick = function () {
        modal.showModal();
    };

    close.onclick = function () {
        modal.close();
    };

    form.onsubmit = function (e) {
        e.preventDefault();

        let name = document.getElementById("ask-name").value;

        success.textContent =
            "Thank you, " + name + "! Your question has been submitted.";

        success.style.display = "block";

        setTimeout(function () {
            modal.close();
            form.reset();
            success.style.display = "none";
        }, 2000);
    };

});


