/************************************************************
 *  DASHBOARD SCRIPT (charts + navigation + section switching)
 ************************************************************/

document.addEventListener("DOMContentLoaded", () => {

    /* -------------------------------------------------------
     * 1. FETCH SERVER-PASSED DATA
     * ----------------------------------------------------- */
    const labels = window.dashboardData?.labels || [];
    const totals = window.dashboardData?.totals || [];
    const buildingDatasets = window.dashboardData?.buildingDatasets || [];
    const composition = window.dashboardData?.composition || [];
    const colors = [
        '#FF6384', // red
        '#36A2EB', // blue
        '#FFCE56', // yellow
        '#4BC0C0', // green
        '#9966FF', // purple
        '#FF9F40'  // orange
    ];

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

    /* -------------------------------------------------------
     * 2. LINE CHART (Total Weight Over Time)
     * ----------------------------------------------------- */
    const lineCanvas = document.getElementById("lineChart");

    if (lineCanvas) {
        const ctxLine = lineCanvas.getContext("2d");

        new Chart(ctxLine, {
            type: "line",
            data: {
                labels: formattedLabels,
                datasets: [{
                    label: "Overall weight (kg)",
                    data: totals,
                    borderWidth: 2,
                    fill:false,
                    borderColor: "#0062ffff",
                }]
            },
            options: {
                responsive: true,
                tension:0.3,
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
                cutout: "50%",
                responsive: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom',
                        labels: {
                            boxWidth: 20,
                            boxHeight: 2,
                            font: {
                                size: 12,
                                family: 'Inter, sans-serif'
                            },
                            padding: 20,
                            generateLabels: (chart) => {
                                const dataset = chart.data.datasets[0];
                                return chart.data.labels.map((label, i) => ({
                                    text: `${label}: ${dataset.data[i]} kg`, // show value
                                    fillStyle: dataset.backgroundColor[i],
                                    strokeStyle: dataset.backgroundColor[i],
                                    lineWidth: 2,
                                    hidden: false,
                                    index: i
                                }));
                            }
                        }
                    }
                }
            }
        });

    }

    /* -------------------------------------------------------
    * 4. BUILDING LINE CHART (Total Weight PER BUILDING)
    * ----------------------------------------------------- */

    // Prepare datasets (no labels for Chart.js legend)
    const datasets = buildingDatasets.map((b, index) => ({
        data: b.totals,
        borderWidth: 2,
        borderColor: colors[index % colors.length],
        fill: false,
        tension: 0.3
    }));
    const buildingTotals = buildingDatasets.map(b =>
        b.totals.reduce((sum, val) => sum + val, 0)
    );

    // Render the chart
    new Chart(document.getElementById("buildingLineChart"), {
        type: 'line',
        data: {
            labels: formattedLabels, // your formatted dates
            datasets: datasets
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false } // hide Chart.js legend
            }
        }
    });

    // Render separate summary labels
    const summaryContainer = document.getElementById("perBuildingSummary");
    summaryContainer.innerHTML = ""; // clear existing content

    buildingDatasets.forEach((b, i) => {
        const total = buildingTotals[i];
        const color = colors[i % colors.length];

        const labelDiv = document.createElement("div");
        labelDiv.classList.add("flex", "items-center", "gap-2");

        // Color marker
        const marker = document.createElement("span");
        marker.style.backgroundColor = color;
        marker.style.width = "20px";
        marker.style.height = "4px";
        marker.style.display = "inline-block";

        // Text
        const text = document.createElement("span");
        text.textContent = `${b.name}: ${total} kg`;
        text.classList.add("text-sm", "font-medium", "text-gray-800"); // ensure visible text

        labelDiv.appendChild(marker);
        labelDiv.appendChild(text);

        summaryContainer.appendChild(labelDiv);
    });



    /* -------------------------------------------------------
    * 5. NAVIGATION HIGHLIGHT + SECTION SWITCHING
    * ----------------------------------------------------- */
    const navItems = document.querySelectorAll(".nav-item");
    const sections = document.querySelectorAll(".content-section");

    // Function to switch section and highlight nav
    function showSection(target) {
        // Highlight nav
        navItems.forEach(i => i.classList.remove("bg-green-900", "text-green-400"));
        const activeNav = document.querySelector(`.nav-item[data-nav="${target}"]`);
        activeNav?.classList.add("bg-green-900", "text-green-400");

        // Show section
        sections.forEach(section => section.classList.add("hidden"));
        document.querySelector(`[data-section="${target}"]`)?.classList.remove("hidden");
    }

    // Add click event to nav items
    navItems.forEach(item => {
        item.addEventListener("click", () => {
            const target = item.getAttribute("data-nav");
            showSection(target);
        });
    });

    showSection("dashboard");

});
