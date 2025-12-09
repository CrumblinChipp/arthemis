document.addEventListener("DOMContentLoaded", () => {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    /* -------------------------------------------------------
    * GLOBAL FUNCTIONS (To be called from Blade onclick)
    * ----------------------------------------------------- */
    // Moved the functions outside of DOMContentLoaded so they are available globally
    window.openAdminModal = function() {
        document.getElementById("adminModal").classList.remove("hidden");
    };
    window.closeAdminModal = function() {
        document.getElementById("adminModal").classList.add("hidden");
        document.getElementById("adminError").classList.add("hidden");
    };
    window.verifyAdmin = function() {
        // Your existing verifyAdmin logic...
        const pass = document.getElementById("adminPassword").value;
        fetch("/admin/verify", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ password: pass })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                window.closeAdminModal();
                location.reload(); 
            } else {
                document.getElementById("adminError").classList.remove("hidden");
            }
        });
    };
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
        '#FF9F40',  // orange
        '#8cff40ff',
        '#7074efff'
    ];
    window.openAdminModal = openAdminModal;
    window.closeAdminModal = closeAdminModal;
    window.verifyAdmin = verifyAdmin;

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
                    borderColor: "#00a711ff",
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

    /* -------------------------------------------------------
    * 6. ADMIN MODAL
    * ----------------------------------------------------- */
    // OPEN MODAL
    document.getElementById("openAdminModal").addEventListener("click", () => {
        document.getElementById("adminModal").classList.remove("hidden");
    }); 
    // CLOSE MODAL
    function closeAdminModal() {
        document.getElementById("adminModal").classList.add("hidden");
        document.getElementById("adminError").classList.add("hidden");
    }
    // CLICK OUTSIDE TO CLOSE
    document.getElementById("adminModal").addEventListener("click", (e) => {
        if (e.target === e.currentTarget) {
            closeAdminModal();
        }
    });
    // VERIFY PASSWORD
    function verifyAdmin() {
        const pass = document.getElementById("adminPassword").value;

        fetch("/admin/verify", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ password: pass })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                closeAdminModal();
                location.reload(); // reload page so the admin nav appears
            } else {
                document.getElementById("adminError").classList.remove("hidden");
            }
        });
    }
    /* -------------------------------------------------------
    * 7. ADMIN NAVIGATION
    * ----------------------------------------------------- */
    document.getElementById("admin-back").addEventListener("click", function () {
        document.getElementById("admin-nav").classList.remove("hidden");
        document.getElementById("admin-content").classList.add("hidden");
    });

    // Admin nav buttons
    const adminNavItems = document.querySelectorAll("[data-admin-page]");
    const adminPages = document.querySelectorAll("#admin-content-inner > div"); // all pages inside content inner

    adminNavItems.forEach(button => {
        button.addEventListener("click", function () {
            const page = this.getAttribute("data-admin-page");
            const isMobile = window.innerWidth < 768;

            // Show selected page
            showAdminPage(page);

            // Mobile behavior
            if (isMobile) {
                document.getElementById("admin-nav").classList.add("hidden");
                document.getElementById("admin-content").classList.remove("hidden");
            } 
            // Desktop behavior
            else {
                document.getElementById("admin-nav").classList.remove("hidden");
                document.getElementById("admin-content").classList.remove("hidden");
            }
        });
    });

    // Function to show one admin page and highlight the active button
    function showAdminPage(pageId) {
        // Hide all pages
        adminPages.forEach(p => p.classList.add("hidden"));

        // Remove active class from all buttons
        adminNavItems.forEach(btn => btn.classList.remove("bg-green-900", "text-green-400"));

        // Show the selected page
        const page = document.getElementById(pageId + "-page"); // match ID
        if (page) page.classList.remove("hidden");

        // Highlight the clicked button
        const activeBtn = document.querySelector(`.admin-nav-item[data-admin-page="${pageId}"]`);
        if (activeBtn) activeBtn.classList.add("bg-green-900", "text-green-400");

        // Initialize dynamic fields if Add Campus
        if (pageId === "add-campus") {
            initAddBuildingButton();
            initAddCampusSubmit();
        }
    }

    /* -------------------------------------------------------
    * 8. ADD BUILDING TEXBOX
    * ----------------------------------------------------- */
    function initAddBuildingButton() {
        const addBuildingBtn = document.getElementById('add-building-btn');
        const buildingsWrapper = document.getElementById('building-wrapper');

        addBuildingBtn.addEventListener('click', () => {
            const div = document.createElement('div');
            div.classList.add('flex', 'items-center', 'mb-2', 'building-item');
            div.innerHTML = `
                <input type="text" name="buildings[]" 
                    class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-600"
                    placeholder="Enter building name">
                <button type="button" class="ml-2 px-2 py-1 bg-red-500 text-white rounded hover:bg-red-600 remove-building-btn">×</button>
            `;
            buildingsWrapper.appendChild(div);

        });

        document.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-building')) {
            e.target.closest('.building-item').remove();
            }
        });
    }
document.addEventListener('DOMContentLoaded', initAddBuildingButton);

    /* -------------------------------------------------------
    * 9. ADD CAMPUS
    * ----------------------------------------------------- */
    function initAddCampusSubmit() {
        const form = document.getElementById("add-campus-form");

        if (!form || form.dataset.bound === "true") return;

        form.addEventListener("submit", function(e) {
            e.preventDefault();

            const formData = new FormData(form);

            fetch(addCampusRoute, { 
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    form.reset();

                    document.getElementById('buildings-wrapper').innerHTML = `
                        <div class="flex items-center mb-2">
                            <input type="text" name="buildings[]" 
                                class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-600"
                                placeholder="Enter building name" required>
                        </div>
                    `;
                } else {
                    alert("Something went wrong.");
                }
            })
            .catch(err => console.error(err));
        });

        form.dataset.bound = "true";
    }

    /* -------------------------------------------------------
    * 10. WASTE ENTRY SUBMISSION
    * ----------------------------------------------------- */
    const wasteModal = document.getElementById("wasteModal");
    const confirmModal = document.getElementById("confirmModal");

    document.getElementById("openWasteModal").onclick = () => {
        wasteModal.classList.remove("hidden");
        wasteModal.classList.add("flex");
    };

    document.getElementById("cancelMain").onclick = () => {
        wasteModal.classList.add("hidden");
        wasteModal.classList.remove("flex");
    };

    document.getElementById("submitMain").onclick = () => {
        confirmModal.classList.remove("hidden");
        confirmModal.classList.add("flex");
    };

    document.getElementById("cancelConfirm").onclick = () => {
        confirmModal.classList.add("hidden");
        confirmModal.classList.remove("flex");
    };

    document.getElementById("confirmSubmit").onclick = () => {
        const payload = {
            name: document.getElementById("entryName").value,
            campus_id: document.getElementById("entryCampus").value,
            building_id: document.getElementById("entryBuilding").value,
            biodegradable: document.getElementById("bio").value,
            recyclable: document.getElementById("recyclable").value,
            residual: document.getElementById("residual").value,
            infectious: document.getElementById("infectious").value,
            _token: "{{ csrf_token() }}"
        };

        fetch("/waste-entry/store", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                confirmModal.classList.add("hidden");
                confirmModal.classList.remove("flex");
                wasteModal.classList.add("hidden");
                wasteModal.classList.remove("flex");

                alert("Waste entry saved to database!");
            }
        })
        .catch(err => {
            console.error(err);
            alert("Submission failed.");
        });
    };

    /* ---------------------------------------------
    DYNAMIC BUILDINGS BASED ON SELECTED CAMPUS
    ---------------------------------------------- */

    document.getElementById("entryCampus").addEventListener("change", function () {
        let campusId = this.value;

        fetch(`/get-buildings/${campusId}`)
            .then(res => res.json())
            .then(data => {
                let buildingDropdown = document.getElementById("entryBuilding");

                buildingDropdown.innerHTML = ""; // clear old options
                buildingDropdown.innerHTML = `<option value="">Select Building</option>`;

                data.forEach(building => {
                    buildingDropdown.innerHTML += `
                        <option value="${building.id}">${building.name}</option>
                    `;
                });
            });
    });
    fetch('/waste-entry/store', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
        },
        body: JSON.stringify(data)
    })


});
