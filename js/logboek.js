document.addEventListener("DOMContentLoaded", function () {

    // =========================================
    // ELEMENTEN
    // =========================================

    const rows =
        document.querySelectorAll("#logbook-body tr");

    const filterSort =
        document.getElementById("filter-sort");

    const filterValue =
        document.getElementById("filter-value");

    const filterDate =
        document.getElementById("filter-date");

    const previousButton =
        document.getElementById("previous-page");

    const nextButton =
        document.getElementById("next-page");

    const pageInfo =
        document.getElementById("page-info");

    const countElement =
        document.getElementById("logbook-count");


    let currentPage = 1;

    const rowsPerPage = 5;



    // =========================================
    // TWEEDE DROPDOWN MAKEN
    // =========================================

    function updateSecondDropdown() {

        const selectedFilter =
            filterSort.value;


        // Oude opties verwijderen

        filterValue.innerHTML = "";


        // =====================================
        // ALLES
        // =====================================

        if (selectedFilter === "all") {

            const option =
                document.createElement("option");

            option.value = "all";
            option.textContent = "Alles";

            filterValue.appendChild(option);


            filterLogs();

            return;
        }



        // =====================================
        // ACTIVITEIT / LOCATIE / STATUS
        // =====================================

        filterValue.disabled = false;


        // Eerst "Alles" toevoegen

        const allOption =
            document.createElement("option");

        allOption.value = "all";
        allOption.textContent = "Alles";

        filterValue.appendChild(allOption);



        // Unieke waarden verzamelen

        const values = [];


        rows.forEach(function (row) {

            let value = "";


            if (selectedFilter === "activity") {

                value = row.dataset.activity;

            }


            if (selectedFilter === "location") {

                value = row.dataset.location;

            }


            if (selectedFilter === "status") {

                value = row.dataset.status;

            }


            if (
                value &&
                !values.includes(value)
            ) {

                values.push(value);

            }

        });



        // Alfabetisch sorteren

        values.sort(function (a, b) {

            return a.localeCompare(
                b,
                "nl"
            );

        });



        // Opties toevoegen

        values.forEach(function (value) {

            const option =
                document.createElement("option");


            option.value = value;


            // Status netjes vertalen

            if (selectedFilter === "status") {

                if (value === "active") {

                    option.textContent =
                        "Monitoring";

                } else if (value === "pending") {

                    option.textContent =
                        "Wacht";

                } else if (value === "done") {

                    option.textContent =
                        "Voltooid";

                } else {

                    option.textContent =
                        value;

                }

            } else {

                option.textContent =
                    value;

            }


            filterValue.appendChild(option);

        });


        // Opnieuw filteren

        filterLogs();

    }



    // =========================================
    // LOGS FILTEREN
    // =========================================

    function filterLogs() {

        const selectedFilter =
            filterSort.value;

        const selectedValue =
            filterValue.value;

        const selectedDate =
            filterDate.value;



        rows.forEach(function (row) {

            let filterMatch = true;

            let dateMatch = true;



            // =====================================
            // ACTIVITEIT
            // =====================================

            if (
                selectedFilter === "activity" &&
                selectedValue !== "all"
            ) {

                filterMatch =
                    row.dataset.activity ===
                    selectedValue;

            }



            // =====================================
            // LOCATIE
            // =====================================

            if (
                selectedFilter === "location" &&
                selectedValue !== "all"
            ) {

                filterMatch =
                    row.dataset.location ===
                    selectedValue;

            }



            // =====================================
            // STATUS
            // =====================================

            if (
                selectedFilter === "status" &&
                selectedValue !== "all"
            ) {

                filterMatch =
                    row.dataset.status ===
                    selectedValue;

            }



            // =====================================
            // DATUM
            // =====================================

            if (selectedDate !== "") {

                dateMatch =
                    row.dataset.date ===
                    selectedDate;

            }



            // =====================================
            // RESULTAAT
            // =====================================

            if (filterMatch && dateMatch) {

                row.dataset.visible = "true";

            } else {

                row.dataset.visible = "false";

            }

        });



        currentPage = 1;

        showPage();

    }



    // =========================================
    // PAGINA TONEN
    // =========================================

    function showPage() {

        const visibleRows = [];


        rows.forEach(function (row) {

            if (
                row.dataset.visible === "true"
            ) {

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



        if (currentPage > totalPages) {

            currentPage = totalPages;

        }



        // Alles eerst verbergen

        rows.forEach(function (row) {

            row.style.display = "none";

        });



        const start =
            (currentPage - 1) *
            rowsPerPage;


        const end =
            start +
            rowsPerPage;



        // Huidige pagina tonen

        for (
            let i = start;
            i < end &&
            i < visibleRows.length;
            i++
        ) {

            visibleRows[i].style.display = "";

        }



        // =====================================
        // PAGINA INFO
        // =====================================

        pageInfo.textContent =
            "Pagina " +
            currentPage +
            " van " +
            totalPages;



        // =====================================
        // AANTAL LOGREGELS
        // =====================================

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



        // =====================================
        // PAGINATION BUTTONS
        // =====================================

        previousButton.disabled =
            currentPage === 1;


        nextButton.disabled =
            currentPage === totalPages;

    }



    // =========================================
    // EERSTE DROPDOWN
    // =========================================

    filterSort.addEventListener(
        "change",
        function () {

            updateSecondDropdown();

        }
    );



    // =========================================
    // TWEEDE DROPDOWN
    // =========================================

    filterValue.addEventListener(
        "change",
        function () {

            filterLogs();

        }
    );



    // =========================================
    // DATUM
    // =========================================

    filterDate.addEventListener(
        "change",
        function () {

            filterLogs();

        }
    );



    // =========================================
    // VORIGE PAGINA
    // =========================================

    previousButton.addEventListener(
        "click",
        function () {

            if (currentPage > 1) {

                currentPage--;

                showPage();

            }

        }
    );



    // =========================================
    // VOLGENDE PAGINA
    // =========================================

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



    // =========================================
    // START
    // =========================================

    rows.forEach(function (row) {

        row.dataset.visible = "true";

    });


    updateSecondDropdown();

});