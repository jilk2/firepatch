document.addEventListener("DOMContentLoaded", function () {

    // =====================================================
    // ELEMENTEN
    // =====================================================

    const filters =
        document.querySelectorAll(".log-filter");

    const rows =
        document.querySelectorAll("#logbook-body tr");

    const dateInput =
        document.getElementById("filter-date");

    const previousButton =
        document.getElementById("previous-page");

    const nextButton =
        document.getElementById("next-page");

    const pageInfo =
        document.getElementById("page-info");

    const countElement =
        document.getElementById("logbook-count");


    // =====================================================
    // INSTELLINGEN
    // =====================================================

    let currentFilter = "all";

    let currentPage = 1;

    const rowsPerPage = 5;


    // =====================================================
    // LOGBOEK FILTEREN
    // =====================================================

    function filterLogs() {

        rows.forEach(function (row) {

            const type =
                row.dataset.type;

            const date =
                row.dataset.date;


            // Controleer type

            let filterMatch = false;

            if (currentFilter === "all") {

                filterMatch = true;

            } else if (type === currentFilter) {

                filterMatch = true;

            }


            // Controleer datum

            let dateMatch = true;

            if (dateInput.value !== "") {

                dateMatch =
                    date === dateInput.value;

            }


            // Toon of verberg regel

            if (filterMatch && dateMatch) {

                row.dataset.visible = "true";

            } else {

                row.dataset.visible = "false";

            }

        });


        currentPage = 1;

        showPage();

    }


    // =====================================================
    // PAGINATION
    // =====================================================

    function showPage() {

        const visibleRows = [];


        rows.forEach(function (row) {

            if (row.dataset.visible === "true") {

                visibleRows.push(row);

            }

        });


        const totalRows =
            visibleRows.length;


        const totalPages =
            Math.max(
                1,
                Math.ceil(
                    totalRows / rowsPerPage
                )
            );


        // Controleer huidige pagina

        if (currentPage > totalPages) {

            currentPage = totalPages;

        }


        // Eerst alles verbergen

        rows.forEach(function (row) {

            row.style.display = "none";

        });


        // Bepaal welke regels zichtbaar zijn

        const start =
            (currentPage - 1) * rowsPerPage;

        const end =
            start + rowsPerPage;


        for (
            let i = start;
            i < end && i < visibleRows.length;
            i++
        ) {

            visibleRows[i].style.display = "";

        }


        // =================================================
        // PAGINA INFORMATIE
        // =================================================

        pageInfo.textContent =
            "Pagina " +
            currentPage +
            " van " +
            totalPages;


        // =================================================
        // AANTAL LOGREGELS
        // =================================================

        if (totalRows === 0) {

            countElement.textContent =
                "Geen logregels gevonden.";

        } else {

            const startNumber =
                start + 1;

            const endNumber =
                Math.min(
                    end,
                    totalRows
                );


            countElement.textContent =
                "Getoond: " +
                startNumber +
                "-" +
                endNumber +
                " van " +
                totalRows +
                " logregels";

        }


        // =================================================
        // PAGINATION BUTTONS
        // =================================================

        previousButton.disabled =
            currentPage === 1;


        nextButton.disabled =
            currentPage === totalPages;

    }


    // =====================================================
    // FILTER KNOPPEN
    // =====================================================

    filters.forEach(function (filter) {

        filter.addEventListener(
            "click",
            function (event) {

                event.preventDefault();


                // Welk filter is gekozen?

                currentFilter =
                    filter.dataset.filter;


                // Active class veranderen

                filters.forEach(function (item) {

                    item.parentElement.classList.remove(
                        "active"
                    );

                });


                filter.parentElement.classList.add(
                    "active"
                );


                // Logboek opnieuw filteren

                filterLogs();

            }
        );

    });


    // =====================================================
    // DATUM FILTER
    // =====================================================

    dateInput.addEventListener(
        "change",
        function () {

            filterLogs();

        }
    );


    // =====================================================
    // VORIGE PAGINA
    // =====================================================

    previousButton.addEventListener(
        "click",
        function () {

            if (currentPage > 1) {

                currentPage--;

                showPage();

            }

        }
    );


    // =====================================================
    // VOLGENDE PAGINA
    // =====================================================

    nextButton.addEventListener(
        "click",
        function () {

            const visibleRows = [];


            rows.forEach(function (row) {

                if (
                    row.dataset.visible === "true"
                ) {

                    visibleRows.push(row);

                }

            });


            const totalPages =
                Math.max(
                    1,
                    Math.ceil(
                        visibleRows.length /
                        rowsPerPage
                    )
                );


            if (currentPage < totalPages) {

                currentPage++;

                showPage();

            }

        }
    );


    // =====================================================
    // START
    // =====================================================

    rows.forEach(function (row) {

        row.dataset.visible = "true";

    });


    filterLogs();

});