// public/js/app.js

console.log('Gradebook app loaded');
console.log("Seating Plan Script Loaded");

document.addEventListener('DOMContentLoaded', function() {
    const getSelects = () => document.querySelectorAll('.seat-select');
    const counterDisplay = document.getElementById('student-count');

    function updateSelections() {
        const selects = getSelects();
        const selectedValues = Array.from(selects).map(s => s.value).filter(v => v !== "");

        // Update the Counter display
        if (counterDisplay) {
            counterDisplay.innerText = selectedValues.length;
        }

        selects.forEach(select => {
            // Toggle background color class based on selection
            const container = select.closest('.seat-container');
            if (container) {
                if (select.value !== "") {
                    container.classList.add('seat-assigned');
                } else {
                    container.classList.remove('seat-assigned');
                }
            }

            // Disable options already selected in other seats
            Array.from(select.options).forEach(option => {
                if (option.value !== "") {
                    option.disabled = selectedValues.includes(option.value) && select.value !== option.value;
                }
            });
        });
    }

    window.resetPlan = function() {
        if (confirm("Clear all seats?")) {
            getSelects().forEach(s => s.value = "");
            updateSelections();
        }
    };

    window.shufflePlan = function() {
        const selects = getSelects();
        if (selects.length === 0) return;

        // Get all student IDs from the first dropdown's options
        const studentIds = Array.from(selects[0].options)
            .map(opt => opt.value)
            .filter(val => val !== "");

        // Fisher-Yates Shuffle
        for (let i = studentIds.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [studentIds[i], studentIds[j]] = [studentIds[j], studentIds[i]];
        }

        // Apply shuffled students to grid
        selects.forEach((s, i) => {
            s.value = studentIds[i] || "";
        });
        updateSelections();
    };

    document.addEventListener('change', (e) => {
        if (e.target.classList.contains('seat-select')) updateSelections();
    });

    // Initial run to set colors and count on page load
    updateSelections();
});
