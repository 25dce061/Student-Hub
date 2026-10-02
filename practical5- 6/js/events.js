document.addEventListener("DOMContentLoaded", function () {

    let events = [];
    let page = 1;
    let perPage = 4;

    let search = document.getElementById("event-search");
    let filter = document.getElementById("event-filter");
    let container = document.getElementById("events-container");

    let previous = document.getElementById("events-prev");
    let next = document.getElementById("events-next");
    let pageText = document.getElementById("events-page-indicator");
    let count = document.getElementById("event-count");


    // Fetch JSON
    fetch("../data/events.json")
        .then(response => response.json())
        .then(data => {

            events = data;
            showEvents();

        });


    // Display events
    function showEvents() {

        let text = search.value.toLowerCase();
        let type = filter.value;

        let result = events.filter(function (event) {

            return (
                event.title.toLowerCase().includes(text) &&
                (type == "all" || event.category == type)
            );

        });


        let totalPages = Math.ceil(result.length / perPage);

        if (totalPages == 0) {
            totalPages = 1;
        }

        if (page > totalPages) {
            page = 1;
        }


        let start = (page - 1) * perPage;

        let pageEvents =
            result.slice(start, start + perPage);


        container.innerHTML = "";


        pageEvents.forEach(function (event) {

            container.innerHTML += `
                <div class="event-card">

                    <span class="event-tag">
                        ${event.category}
                    </span>

                    <h2>${event.title}</h2>

                    <p>${event.description}</p>

                    <p>📅 ${event.date}</p>

                    <p>📍 ${event.venue}</p>

                    <p>🏢 ${event.department}</p>

                </div>
            `;

        });


        count.textContent =
            "Showing " + pageEvents.length +
            " of " + result.length + " events";


        pageText.textContent =
            "Page " + page + " of " + totalPages;


        previous.disabled = page == 1;
        next.disabled = page == totalPages;

    }


    // Search
    search.addEventListener("input", function () {

        page = 1;
        showEvents();

    });


    // Filter
    filter.addEventListener("change", function () {

        page = 1;
        showEvents();

    });


    // Previous
    previous.addEventListener("click", function () {

        page--;
        showEvents();

    });


    // Next
    next.addEventListener("click", function () {

        page++;
        showEvents();

    });

});