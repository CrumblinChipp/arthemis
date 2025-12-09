// Make these functions global so they can be called from onclick in the HTML
window.openAdminModal = function() {
    document.getElementById("adminModal").classList.remove("hidden");
};
window.closeAdminModal = function() {
    document.getElementById("adminModal").classList.add("hidden");
    document.getElementById("adminError").classList.add("hidden");
};
window.verifyAdmin = function() {
    const pass = document.getElementById("adminPassword").value;
    // ... rest of the verifyAdmin logic ...
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

export const colors = [
    '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', 
    '#9966FF', '#FF9F40', '#8cff40ff', '#7074efff'
];

export function formatLabels(labels) {
    // ... your label formatting logic ...
    return labels.map(l => {
        try {
            const d = new Date(l);
            const mm = String(d.getMonth() + 1).padStart(2, "0");
            const dd = String(d.getDate()).padStart(2, "0");
            return `${mm}-${dd}`;
        } catch (e) {
            return l;
        }
    });
}