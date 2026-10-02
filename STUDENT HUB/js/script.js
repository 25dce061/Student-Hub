
function login() {

    let username = document.getElementById("username").value;
    let password = document.getElementById("password").value;

    let usernameRegex = /^[A-Za-z]{3,15}$/;
    let passwordRegex = /^[A-Za-z0-9]{4,}$/;

    if (username == "" || password == "") {
        alert("Please enter username and password");
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

    if (username == "yesha" && password == "1234") {
        alert("Login Successfully");
        window.location.href = "../pages/dashboard.html";
    } else { alert("Invalid ID or Password");}
}


function register() {

    let name = document.getElementById("name");
    let username = document.getElementById("username");
    let email = document.getElementById("email");
    let password = document.getElementById("password");
    let confirmPassword = document.getElementById("confirmPassword");

    let nameRegex = /^[A-Za-z ]{3,30}$/;
    let usernameRegex = /^[A-Za-z0-9]{3,15}$/;
    let emailRegex = /^[a-z0-9._%+-]+@gmail\.com$/;
    let passwordRegex = /^(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])[A-Za-z0-9]{8,}$/;


    if (name.value == "") {
        alert("Please enter your full name");
        return false;
    }

    if (!nameRegex.test(name.value)) {
        alert("Invalid name: only letters are allowed");
        return false;
    }


    if (username.value == "") {
        alert("Please enter username");
        return false;
    }

    if (!usernameRegex.test(username.value)) {
        alert("Invalid username");
        return false;
    }


    if (email.value == "") {
        alert("Please enter your email");
        return false;
    }

    if (!emailRegex.test(email.value)) {
        alert("Email must be lowercase and end with @gmail.com");
        return false;
    }


    if (password.value == "") {
        alert("Please enter password");
        return false;
    }

    if (!passwordRegex.test(password.value)) {
        alert("Password must have 8 characters, uppercase, lowercase and number");
        return false;
    }


    if (confirmPassword.value == "") {
        alert("Please confirm your password");
        return false;
    }

    if (password.value != confirmPassword.value) {
        alert("Passwords do not match");
        return false;
    }


    alert("Registration Successfully");

    window.location.href = "../index.html";

    return false;
}





async function loadCities() {

    let citySelect = document.getElementById("weather-city");

    if (!citySelect) return;

    try {

        let response = await fetch("cities.json");
        let cities = await response.json();

        cities.forEach(city => {

            let option = document.createElement("option");

            option.value = city.latitude + "," + city.longitude;
            option.textContent = city.city;

            citySelect.appendChild(option);
        });

    } catch (error) {

        console.log("Error loading cities:", error);
    }
}


async function fetchWeather(lat, lon) {

    let weatherData = document.getElementById("weather-data");
    let weatherError = document.getElementById("weather-error");
    let weatherLoading = document.getElementById("weather-loading");

    if (!weatherData) return;

    weatherLoading.style.display = "block";
    weatherError.style.display = "none";

    try {

        let url = `https://api.open-meteo.com/v1/forecast?latitude=${lat}&longitude=${lon}&current=temperature_2m,relative_humidity_2m,precipitation,weather_code,wind_speed_10m&daily=temperature_2m_max,temperature_2m_min,precipitation_probability_max,weather_code,sunrise,sunset&timezone=auto`;

        let response = await fetch(url);

        if (!response.ok) {
            throw new Error("Weather data not found");
        }

        let data = await response.json();

        displayWeather(data);

        weatherLoading.style.display = "none";
        weatherData.style.display = "block";

    } catch (error) {

        console.log(error);

        weatherLoading.style.display = "none";
        weatherError.style.display = "block";
    }
}


function displayWeather(data) {

    let current = data.current;
    let daily = data.daily;

    document.getElementById("current-temp").textContent =
        Math.round(current.temperature_2m) + "°C";

    document.getElementById("current-humidity").textContent =
        current.relative_humidity_2m + "%";

    document.getElementById("current-wind").textContent =
        current.wind_speed_10m + " km/h";

    document.getElementById("current-rain").textContent =
        current.precipitation + " mm";

    document.getElementById("current-sunrise").textContent =
        new Date(daily.sunrise[0]).toLocaleTimeString([], {
            hour: "2-digit",
            minute: "2-digit"
        });

    document.getElementById("current-sunset").textContent =
        new Date(daily.sunset[0]).toLocaleTimeString([], {
            hour: "2-digit",
            minute: "2-digit"
        });


    let forecast = document.getElementById("weather-forecast");

    if (!forecast) return;

    forecast.innerHTML = "";

    for (let i = 0; i < daily.time.length; i++) {

        let item = document.createElement("div");

        item.className = "forecast-item";

        item.innerHTML = `
            <div>
                ${new Date(daily.time[i]).toLocaleDateString("en-US", {
                    weekday: "short"
                })}
            </div>

            <div>
                ${Math.round(daily.temperature_2m_max[i])}° /
                ${Math.round(daily.temperature_2m_min[i])}°
            </div>

            <div>
                ${daily.precipitation_probability_max[i]}% rain
            </div>
        `;

        forecast.appendChild(item);
    }
}


document.addEventListener("DOMContentLoaded", function() {

    let citySelect = document.getElementById("weather-city");
    let refreshBtn = document.getElementById("weather-refresh");
    let cityLabel = document.getElementById("current-city-name");

    loadCities();

    if (citySelect) {

        citySelect.addEventListener("change", function() {

            let [lat, lon] = citySelect.value.split(",");

            let cityName =
                citySelect.options[citySelect.selectedIndex].text;

            cityLabel.textContent = cityName;

            fetchWeather(lat, lon);
        });
    }

    if (refreshBtn) {

        refreshBtn.addEventListener("click", function() {

            let [lat, lon] = citySelect.value.split(",");

            fetchWeather(lat, lon);
        });
    }
});
let rows = document.querySelectorAll("#courseTable tr");

document.getElementById("search").addEventListener("input", function() {

    let search = this.value.toLowerCase();

    rows.forEach(function(row) {

        let course = row.cells[1].textContent.toLowerCase();

        if (course.includes(search)) {
            row.style.display = "";
        } else {
            row.style.display = "none";
        }

    });

});


document.getElementById("filter").addEventListener("change", function() {

    let value = this.value;

    rows.forEach(function(row) {

        let code = row.cells[2].textContent;

        if (value === "all" || code === value) {
            row.style.display = "";
        } else {
            row.style.display = "none";
        }

    });

});

