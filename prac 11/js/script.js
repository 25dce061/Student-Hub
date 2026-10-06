
function login() {
    const usernameInput = document.getElementById("username");
    const passwordInput = document.getElementById("password");
    const messageDisplay = document.getElementById("Message");

    if (!usernameInput || !passwordInput) return;

    const username = usernameInput.value.trim();
    const password = passwordInput.value.trim();

    const usernameRegex = /^[A-Za-z]{3,15}$/;
    const passwordRegex = /^[A-Za-z0-9]{4,}$/;

    if (username === "" || password === "") {
        alert("Please enter both username and password");
        return;
    }

    if (!usernameRegex.test(username)) {
        alert("Username must contain only letters (3-15 characters)");
        return;
    }

    if (!passwordRegex.test(password)) {
        alert("Password must contain at least 4 letters or numbers");
        return;
    }

    
    if (username.toLowerCase() === "yesha" && password === "1234") {
        if (messageDisplay) {
            messageDisplay.style.color = "#166534";
            messageDisplay.textContent = "Login Successful! Redirecting...";
        }
        alert("Login Successful! Redirecting to Dashboard...");
        window.location.href = "dashboard.html";
    } else {
        if (messageDisplay) {
            messageDisplay.style.color = "#991b1b";
            messageDisplay.textContent = "Invalid Username or Password";
        }
        alert("Invalid ID or Password. Try yesha / 1234");
    }
}


function register() {
    const name = document.getElementById("name");
    const username = document.getElementById("username");
    const email = document.getElementById("email");
    const password = document.getElementById("password");
    const confirmPassword = document.getElementById("confirmPassword");

    const nameRegex = /^[A-Za-z ]{3,30}$/;
    const usernameRegex = /^[A-Za-z0-9]{3,15}$/;
    const emailRegex = /^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$/i;
    const passwordRegex = /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d@$!%*#?&]{8,}$/;

    if (!name || name.value.trim() === "") {
        alert("Please enter your full name");
        return false;
    }

    if (!nameRegex.test(name.value.trim())) {
        alert("Invalid name: only alphabets and spaces are allowed (3-30 characters)");
        return false;
    }

    if (!username || username.value.trim() === "") {
        alert("Please enter a username");
        return false;
    }

    if (!usernameRegex.test(username.value.trim())) {
        alert("Invalid username: alphanumeric characters only (3-15 characters)");
        return false;
    }

    if (!email || email.value.trim() === "") {
        alert("Please enter your email address");
        return false;
    }

    if (!emailRegex.test(email.value.trim())) {
        alert("Please enter a valid email address");
        return false;
    }

    if (!password || password.value === "") {
        alert("Please enter a password");
        return false;
    }

    if (!passwordRegex.test(password.value)) {
        alert("Password must contain at least 8 characters, including letters and numbers");
        return false;
    }

    if (!confirmPassword || confirmPassword.value === "") {
        alert("Please confirm your password");
        return false;
    }

    if (password.value !== confirmPassword.value) {
        alert("Passwords do not match");
        return false;
    }

    alert("Registration Successful! Please login to continue.");
    window.location.href = "login.html";
    return false;
}
document.addEventListener("DOMContentLoaded", function () {

    let events = [];
    let page = 1;
    let perPage = 2;

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


document.addEventListener("DOMContentLoaded", () => {
    const profileForm = document.getElementById("profile-form");
    const profileModal = document.getElementById("profile-dialog");
    const previewProfileBtn = document.getElementById("preview-profile-btn");
    const closeProfileBtn = document.getElementById("close-profile-dialog");

    function showProfilePreview() {
        if (!profileModal) return;

        const nameField = document.getElementById("fullname") || document.getElementById("name");
        const idField = document.getElementById("id");
        const collegeField = document.getElementById("college");
        const deptField = document.getElementById("department");
        const mobileField = document.getElementById("mobile2");

        const modalName = document.getElementById("modal-student-name");
        const modalId = document.getElementById("modal-student-id");
        const modalCollege = document.getElementById("modal-student-college");
        const modalDept = document.getElementById("modal-student-dept");
        const modalMobile = document.getElementById("modal-student-mobile");

        if (nameField && modalName) modalName.textContent = nameField.value;
        if (idField && modalId) modalId.textContent = idField.value;
        if (collegeField && modalCollege) modalCollege.textContent = collegeField.value;
        if (deptField && modalDept) modalDept.textContent = deptField.value;
        if (mobileField && modalMobile) modalMobile.textContent = mobileField.value;

        if (typeof profileModal.showModal === "function") {
            profileModal.showModal();
        } else {
            profileModal.setAttribute("open", "true");
        }
    }

    if (previewProfileBtn) {
        previewProfileBtn.addEventListener("click", showProfilePreview);
    }

    if (profileForm) {
        profileForm.addEventListener("submit", (e) => {
            e.preventDefault();
            alert("Profile successfully saved and updated!");
            showProfilePreview();
        });
    }

    if (closeProfileBtn && profileModal) {
        closeProfileBtn.addEventListener("click", () => {
            if (typeof profileModal.close === "function") {
                profileModal.close();
            } else {
                profileModal.removeAttribute("open");
            }
        });
    }

    if (profileModal) {
        profileModal.addEventListener("click", (e) => {
            const rect = profileModal.getBoundingClientRect();
            const isInDialog = (rect.top <= e.clientY && e.clientY <= rect.top + rect.height
                             && rect.left <= e.clientX && e.clientX <= rect.left + rect.width);
            if (!isInDialog) {
                if (typeof profileModal.close === "function") profileModal.close();
                else profileModal.removeAttribute("open");
            }
        });
    }
});
// Dark and Light Mode
document.addEventListener("DOMContentLoaded", function () {

    let button = document.getElementById("theme-toggle");

    if (button) {

        // Load saved theme
        if (localStorage.getItem("theme") === "dark") {
            document.body.classList.add("dark-mode");
            button.textContent = "☀️ Light Mode";
        }

        // Toggle theme
        button.addEventListener("click", function () {

            document.body.classList.toggle("dark-mode");

            if (document.body.classList.contains("dark-mode")) {
                localStorage.setItem("theme", "dark");
                button.textContent = "☀️ Light Mode";
            } else {
                localStorage.setItem("theme", "light");
                button.textContent = "🌙 Dark Mode";
            }

        });

    }

});
