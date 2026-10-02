
function searchCourse() {
    const searchInput = document.getElementById("search");
    const filterSelect = document.getElementById("filter");
    const countDisplay = document.getElementById("course-count");
    const noResultsRow = document.getElementById("no-courses-row");

    const query = searchInput ? searchInput.value.toLowerCase().trim() : "";
    const selectedCode = filterSelect ? filterSelect.value : "all";

    const tableBody = document.getElementById("courseTable");
    if (!tableBody) return;

    const rows = tableBody.querySelectorAll("tr:not(#no-courses-row)");
    let visibleCount = 0;

    rows.forEach(row => {
        const cells = row.getElementsByTagName("td");
        if (cells.length >= 3) {
            const courseName = cells[1].textContent.toLowerCase();
            const courseCode = cells[2].textContent.trim();
            const fullText = row.textContent.toLowerCase();

            const matchesSearch = !query || fullText.includes(query);
            const matchesFilter = selectedCode === "all" || courseCode.includes(selectedCode);

            if (matchesSearch && matchesFilter) {
                row.style.display = "";
                visibleCount++;
            } else {
                row.style.display = "none";
            }
        }
    });

    // Handle empty state
    if (noResultsRow) {
        noResultsRow.style.display = visibleCount === 0 ? "" : "none";
    }

    // Update count indicator
    if (countDisplay) {
        if (visibleCount === 0) {
            countDisplay.textContent = "No courses match your filter criteria.";
        } else if (visibleCount === rows.length) {
            countDisplay.textContent = `Showing all ${visibleCount} courses`;
        } else {
            countDisplay.textContent = `Showing ${visibleCount} of ${rows.length} courses`;
        }
    }
}

document.addEventListener("DOMContentLoaded", () => {
    const filterSelect = document.getElementById("filter");
    const searchInput = document.getElementById("search");

    if (filterSelect) {
        filterSelect.addEventListener("change", searchCourse);
    }

    if (searchInput) {
        searchInput.addEventListener("input", searchCourse);
    }

    // Initial count
    searchCourse();
});
