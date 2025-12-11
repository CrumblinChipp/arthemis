let lineChartInstance = null;
let donutChartInstance = null;
let buildingLineChartInstance = null;

window.openAdminModal = function() {
    document.getElementById("adminModal")?.classList.remove("hidden");
};
window.closeAdminModal = function() {
    document.getElementById("adminModal")?.classList.add("hidden");
    document.getElementById("adminError")?.classList.add("hidden");
};
window.verifyAdmin = function() {
    const pass = document.getElementById("adminPassword").value;
    fetch("/admin/verify", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')?.content
        },
        body: JSON.stringify({ password: pass })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            window.closeAdminModal();
            location.reload(); 
        } else {
            document.getElementById("adminError")?.classList.remove("hidden");
        }
    });
};


document.addEventListener("DOMContentLoaded", () => {

    /* -------------------------------------------------------
     * 1. FETCH SERVER-PASSED DATA
     * ----------------------------------------------------- */
    const labels = window.dashboardData?.labels || [];
    const totals = window.dashboardData?.totals || [];
    const buildingDatasets = window.dashboardData?.buildingDatasets || [];
    const composition = window.dashboardData?.composition || [];
    const colors = [
        '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', 
        '#9966FF', '#FF9F40', '#8cff40ff', '#7074efff'
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
        if (lineChartInstance) {
            lineChartInstance.destroy();
        }

        const ctxLine = lineCanvas.getContext("2d");

        lineChartInstance = new Chart(ctxLine, { 
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
        if (donutChartInstance) {
            donutChartInstance.destroy();
        }
        
        const donutCtx = donutCanvas.getContext("2d");

        donutChartInstance = new Chart(donutCtx, {
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
        label: b.name,
        data: b.totals,
        borderWidth: 2,
        borderColor: colors[index % colors.length],
        fill: false,
        tension: 0.3
    }));
    const buildingTotals = buildingDatasets.map(b =>
        b.totals.reduce((sum, val) => sum + val, 0)
    );
    
    const buildingCanvas = document.getElementById("buildingLineChart");
    if (buildingCanvas) {
        if (buildingLineChartInstance) {
            buildingLineChartInstance.destroy();
        }

        buildingLineChartInstance = new Chart(buildingCanvas, { 
            type: 'line',
            data: {
                labels: formattedLabels,
                datasets: datasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                }
            }
        });
    }


    // Render separate summary labels
    const summaryContainer = document.getElementById("perBuildingSummary");
    if (summaryContainer) {
        summaryContainer.innerHTML = "";

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
            text.classList.add("text-sm", "font-medium", "text-gray-800"); 

            labelDiv.appendChild(marker);
            labelDiv.appendChild(text);

            summaryContainer.appendChild(labelDiv);
        });
    }


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
    // Open Modal
    document.getElementById("openAdminModal")?.addEventListener("click", () => {
        document.getElementById("adminModal")?.classList.remove("hidden");
    }); 
    
    // Click outsdie to close
    document.getElementById("adminModal")?.addEventListener("click", (e) => {
        if (e.target === e.currentTarget) {
            window.closeAdminModal();
        }
    });
    
    /* -------------------------------------------------------
     * 7. ADMIN NAVIGATION
     * ----------------------------------------------------- */
    document.getElementById("admin-back")?.addEventListener("click", function () {
        document.getElementById("admin-nav")?.classList.remove("hidden");
        document.getElementById("admin-content")?.classList.add("hidden");
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
                document.getElementById("admin-nav")?.classList.add("hidden");
                document.getElementById("admin-content")?.classList.remove("hidden");
            } 
            // Desktop behavior
            else {
                document.getElementById("admin-nav")?.classList.remove("hidden");
                document.getElementById("admin-content")?.classList.remove("hidden");
            }
        });
    });

    function showAdminPage(pageId) {
        // Hide all pages
        adminPages.forEach(p => p.classList.add("hidden"));

        // Remove active class from all buttons
        adminNavItems.forEach(btn => btn.classList.remove("bg-green-900", "text-green-400"));

        // Show the selected page
        const page = document.getElementById(pageId); 
        if (page) page.classList.remove("hidden");

        // Highlight the clicked button
        const activeBtn = document.querySelector(`.admin-nav-item[data-admin-page="${pageId}"]`);
        if (activeBtn) activeBtn.classList.add("bg-green-900", "text-green-400");
        
        // Call initialization functions when a section is opened
        if (pageId === 'add-campus') {
            initAddBuildingButton();
            initAddCampusSubmit();
        }
    }

    /* -------------------------------------------------------
     * 8. ADD BUILDING TEXBOX
     * ----------------------------------------------------- */
    function initAddBuildingButton() {
        const addBuildingBtn = document.getElementById('add-building-btn');
        if (!addBuildingBtn || addBuildingBtn.dataset.bound === "true") return; 
        
        const buildingsWrapper = document.getElementById('building-wrapper');

        addBuildingBtn.addEventListener('click', () => {
            const div = document.createElement('div');
            div.classList.add('flex', 'items-center', 'mb-2', 'building-item');
            div.innerHTML = `
                <input type="text" name="buildings[]" 
                    class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-600"
                    placeholder="Enter building name" required>
                <button type="button" class="ml-2 px-2 py-1 bg-red-500 text-white rounded hover:bg-red-600 remove-building-btn">×</button>
            `;
            buildingsWrapper?.appendChild(div);
        });

        document.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-building-btn')) {
            e.target.closest('.building-item')?.remove();
            }
        });
        addBuildingBtn.dataset.bound = "true";
    }

    /* -------------------------------------------------------
     * 9. ADD CAMPUS
     * ----------------------------------------------------- */
    function initAddCampusSubmit() {
        const form = document.getElementById("add-campus-form");

        if (!form || form.dataset.bound === "true") return;
        const campusRoute = window.addCampusRoute; 

        form.addEventListener("submit", function(e) {
            e.preventDefault();

            const formData = new FormData(form);

            fetch(campusRoute, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')?.content
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    form.reset();

                    document.getElementById('building-wrapper').innerHTML = `
                        <div class="flex items-center mb-2 building-item">
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
    
    if (document.getElementById("openWasteModal")) {
        document.getElementById("openWasteModal").onclick = () => {
            wasteModal?.classList.remove("hidden");
            wasteModal?.classList.add("flex");
        };
    }

    if (document.getElementById("cancelMain")) {
        document.getElementById("cancelMain").onclick = () => {
            wasteModal?.classList.add("hidden");
            wasteModal?.classList.remove("flex");
        };
    }

    if (document.getElementById("submitMain")) {
        document.getElementById("submitMain").onclick = () => {
            confirmModal?.classList.remove("hidden");
            confirmModal?.classList.add("flex");
        };
    }

    if (document.getElementById("cancelConfirm")) {
        document.getElementById("cancelConfirm").onclick = () => {
            confirmModal?.classList.add("hidden");
            confirmModal?.classList.remove("flex");
        };
    }

    if (document.getElementById("confirmSubmit")) {
        document.getElementById("confirmSubmit").onclick = () => {
            
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
            
            const payload = {
                name: document.getElementById("entryName")?.value || '',
                campus_id: document.getElementById("entryCampus")?.value || '',
                date: new Date().toISOString().slice(0, 10),
                building_id: document.getElementById("entryBuilding")?.value || '',
                
                biodegradable_kg: document.getElementById("bio")?.value || 0,
                recyclable_kg: document.getElementById("recyclable")?.value || 0,
                residual_kg: document.getElementById("residual")?.value || 0,
                infectious_kg: document.getElementById("infectious")?.value || 0,
                
            };
            
            fetch("/waste-entry/store", {
                method: "POST",
                headers: { 
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken, 
                    "Accept": "application/json"
                },
                body: JSON.stringify(payload)
            })
            .then(res => {
                if (!res.ok) {
                    if (res.status === 422) {
                        return res.json().then(data => {
                            // Display validation errors if available
                            alert('Validation Failed: ' + Object.values(data.errors).flat().join('\n'));
                            throw new Error("Validation failed.");
                        });
                    } else {
                        throw new Error(`Server responded with status: ${res.status}`);
                    }
                }
                return res.json();
            })
            .then(data => {
                if (data.success) {
                    confirmModal?.classList.add("hidden");
                    confirmModal?.classList.remove("flex");
                    wasteModal?.classList.add("hidden");
                    wasteModal?.classList.remove("flex");

                    showToast("Waste entry saved to database!");
                    location.reload(); 
                } else {
                    showToast("Submission failed. Server error.");
                }
            })
            .catch(err => {
                console.error(err);
                showToast("Submission failed. Check console for details.");
            });
        };
    }

    /* ---------------------------------------------
     * 11.DYNAMIC BUILDINGS BASED ON SELECTED CAMPUS
     * ---------------------------------------------- */

    document.getElementById("entryCampus")?.addEventListener("change", function () {
        let campusId = this.value;
        if (!campusId) return;

        fetch(`/get-buildings/${campusId}`)
            .then(res => res.json())
            .then(data => {
                let buildingDropdown = document.getElementById("entryBuilding");
                if (!buildingDropdown) return;
                
                buildingDropdown.innerHTML = "";
                let defaultOption = document.createElement('option');
                defaultOption.value = "";
                defaultOption.textContent = "Select Building";
                buildingDropdown.appendChild(defaultOption);

                data.forEach(building => {
                    let option = document.createElement('option');
                    option.value = building.id;
                    option.textContent = building.name;
                    buildingDropdown.appendChild(option);
                });
            })
            .catch(err => console.error("Error fetching buildings:", err));
    });

    /* ---------------------------------------------
     * 12.TOAST FOR ALERT
     * ---------------------------------------------- */

        window.showToast = function(message, type = 'success', duration = 3000) {
        const container = document.getElementById('toast-container');
        if (!container) return;

        let bgColor, textColor, iconClass;
        
        switch (type) {
            case 'success':
                bgColor = 'bg-emerald-500';
                iconClass = '✅';
                break;
            case 'error':
                bgColor = 'bg-rose-600';
                iconClass = '❌';
                break;
            case 'warning':
                bgColor = 'bg-amber-500';
                iconClass = '⚠️';
                break;
            case 'info':
                bgColor = 'bg-blue-500';
                iconClass = 'ℹ️';
                break;
            default:
                bgColor = 'bg-neutral-600';
                iconClass = '💬';
        }
        
        textColor = 'text-white'; 

        const toast = document.createElement('div');
        toast.className = `flex items-center p-3 rounded-lg shadow-xl ${bgColor} ${textColor} transform transition-all ease-in-out duration-300 opacity-0 translate-x-full`;
        toast.innerHTML = `
            <span class="mr-3 text-lg">${iconClass}</span>
            <p class="font-medium">${message}</p>
        `;

        container.appendChild(toast);
        
        setTimeout(() => {
            toast.classList.remove('opacity-0', 'translate-x-full');
            toast.classList.add('opacity-100', 'translate-x-0');
        }, 10);

        setTimeout(() => {
            toast.classList.remove('opacity-100', 'translate-x-0');
            toast.classList.add('opacity-0', 'translate-x-full');

            setTimeout(() => {
                toast.remove();
            }, 400);

        }, duration);
    }
});