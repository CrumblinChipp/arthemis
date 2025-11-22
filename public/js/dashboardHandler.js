/************************************************************
 *  DASHBOARD SCRIPT (charts + navigation + section switching)
 ************************************************************/

document.addEventListener("DOMContentLoaded", () => {

    /* -------------------------------------------------------
     * 1. FETCH SERVER-PASSED DATA
     * ----------------------------------------------------- */
    const labels = window.dashboardData?.labels || [];
    const totals = window.dashboardData?.totals || [];
    const composition = window.dashboardData?.composition || [];
    const buildings =window.dashboardData?.buildings || [];
    const wastePerBuilding =window.dashboardData?.wastePerBuilding || [];


    /* -------------------------------------------------------
     * 2. LINE CHART (Total Weight Over Time)
     * ----------------------------------------------------- */
    const lineCanvas = document.getElementById("lineChart");

    if (lineCanvas) {
        const ctxLine = lineCanvas.getContext("2d");

        const formattedLabels = labels.map(l => {
            try {
                const d = new Date(l);
                const mm = String(d.getMonth() + 1).padStart(2, "0");
                const dd = String(d.getDate()).padStart(2, "0");
                return `${mm}-${dd}`;
            } catch (e) {
                return l;
            }
        });

        new Chart(ctxLine, {
            type: "line",
            data: {
                labels: formattedLabels,
                datasets: [{
                    label: "Overall weight (kg)",
                    data: totals,
                    fill: true,
                    tension: 0.35,
                    borderColor: "#aac5f287",
                    backgroundColor: "rgba(169, 199, 250, 0.3)",
                    pointBackgroundColor: "#0062ffff",
                    pointRadius: 4
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });
    }


    /* -------------------------------------------------------
     * 3. DONUT CHART (Waste Composition)
     * ----------------------------------------------------- */
    const donutCanvas = document.getElementById("donutChart");

    if (donutCanvas) {
        const donutCtx = donutCanvas.getContext("2d");

        new Chart(donutCtx, {
            type: "doughnut",
            data: {
                labels: ["Biodegradable", "Residual", "Recyclable", "Infectious"],
                datasets: [{
                    data: composition,
                    backgroundColor: ["#13c722ff", "#225092ff", "#eaf63bff", "#fda247ff"],
                    borderWidth: 0
                }]
            },
            options: {
                plugins: { legend: { display: false } },
                cutout: "60%"
            }
        });
    }

    /* -------------------------------------------------------
     * 4. BUILDING LINE CHART (Total Weight Per Building)
     * ----------------------------------------------------- */

    const buildingCanvas = document.getElementById("buildingLineChart");

    if (buildingCanvas) {
        const ctxLine = buildingCanvas.getContext("2d");

        // Prepare datasets
        const datasets = buildings.map((building, index) => {
            const color = `hsl(${index * 60 % 360}, 70%, 50%)`; // distinct color per building

            return {
                label: building,
                data: labels.map(date => wasteData[date][building]), // get daily total for this building
                borderColor: color,
                backgroundColor: color,
                fill: false, // line chart, no area fill
                tension: 0.3 // smooth line
            };
        });

        new Chart(ctxLine, {
            type: "line",
            data: {
                labels: formattedLabels,
                datasets: datasets
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });
    }


    /* -------------------------------------------------------
     * 5. NAVIGATION HIGHLIGHT + SECTION SWITCHING
     * ----------------------------------------------------- */
    const navItems = document.querySelectorAll(".nav-item");
    const sections = document.querySelectorAll(".content-section");

    navItems.forEach(item => {
        item.addEventListener("click", () => {

            const target = item.getAttribute("data-nav");

            // Highlight clicked item
            navItems.forEach(i =>
                i.classList.remove("bg-green-900", "text-green-400")
            );
            item.classList.add("bg-green-900", "text-green-400");

            // Show correct content section
            sections.forEach(section => section.classList.add("hidden"));
            document
                .querySelector(`[data-section="${target}"]`)
                ?.classList.remove("hidden");
        });
    });

});
