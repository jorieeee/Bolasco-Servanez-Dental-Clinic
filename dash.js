function autoScrollToBottom() {
    var chatContainer = document.getElementById("chat-container"); // Replace with your actual container ID
    if (chatContainer) {
        chatContainer.scrollTo({
            top: chatContainer.scrollHeight,
            behavior: "smooth" // Enables smooth scrolling
        });
    }
}

// Call this function whenever new content is added
function observeNewMessages() {
    const chatContainer = document.getElementById("chat-container"); // Replace with your actual container ID
    if (chatContainer) {
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.addedNodes.length > 0) {
                    autoScrollToBottom();
                }
            });
        });

        observer.observe(chatContainer, { childList: true, subtree: true });
    }
}

// Run the observer when the page loads
document.addEventListener("DOMContentLoaded", () => {
    observeNewMessages();
    autoScrollToBottom(); // Ensures scrolling starts at the bottom
});


function toggleDropdown() {
    document.getElementById("dropdown").classList.toggle("show");
}

// Close the dropdown if the user clicks outside of it
window.onclick = function(event) {
    if (!event.target.matches('.profile img')) {
        const dropdowns = document.getElementsByClassName("dropdown-content");
        for (let i = 0; i < dropdowns.length; i++) {
            dropdowns[i].classList.remove('show');
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const calendarDays = document.getElementById('calendar-days');
    const monthYear = document.getElementById('month-year');
    const prevBtn = document.getElementById('prev-btn');
    const nextBtn = document.getElementById('next-btn');

    let currentDate = new Date();

    function renderCalendar(date) {
        const currentMonth = date.getMonth();
        const currentYear = date.getFullYear();

        monthYear.textContent = `${date.toLocaleString('default', { month: 'long' })} ${currentYear}`;

        calendarDays.innerHTML = '';

        const firstDay = new Date(currentYear, currentMonth, 1).getDay();
        const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();

        for (let i = 0; i < firstDay; i++) {
            const emptyCell = document.createElement('div');
            emptyCell.classList.add('calendar-day', 'empty');
            calendarDays.appendChild(emptyCell);
        }

        for (let day = 1; day <= daysInMonth; day++) {
            const dayCell = document.createElement('div');
            dayCell.classList.add('calendar-day');
            dayCell.textContent = day;

            const today = new Date();
            if (
                day === today.getDate() &&
                currentMonth === today.getMonth() &&
                currentYear === today.getFullYear()
            ) {
                dayCell.classList.add('today');
            }

            calendarDays.appendChild(dayCell);
        }
    }

    prevBtn.addEventListener('click', () => {
        currentDate.setMonth(currentDate.getMonth() - 1);
        renderCalendar(currentDate);
    });

    nextBtn.addEventListener('click', () => {
        currentDate.setMonth(currentDate.getMonth() + 1);
        renderCalendar(currentDate);
    });

    renderCalendar(currentDate);
});





function renderPatients(list) {
    table.innerHTML = "";
    list.forEach(patient => {
        const row = document.createElement("tr");

        row.innerHTML = `
            <td>${patient.name}</td>
            <td>
                ${patient.email} <br>
                <small>${patient.phone}</small>
            </td>
            <td>${new Date(patient.appointment).toDateString()}</td>
            <td><span class="${patient.status === "active" ? "active" : "inactive"}">${patient.status}</span></td>
        `;

        table.appendChild(row);
    });
}

function filterPatients() {
    const search = document.getElementById("search").value.toLowerCase();
    const status = document.getElementById("statusFilter").value;
    const filtered = patients.filter(p => {
        const matchName = p.name.toLowerCase().includes(search) || p.email.toLowerCase().includes(search);
        const matchStatus = status === "all" || p.status === status;
        return matchName && matchStatus;
    });

    renderPatients(filtered);
}
document.querySelectorAll('.status').forEach(item => {
    item.addEventListener('click', () => {
        document.querySelectorAll('.status').forEach(el => {
            el.classList.remove('active', 'inactive');
            el.classList.add('inactive');  // Set all to inactive
        });
        item.classList.remove('inactive');
        item.classList.add('active');  // Activate the clicked one
    });
});
document.getElementById("search").addEventListener("input", filterPatients);
document.getElementById("statusFilter").addEventListener("change", filterPatients);
document.getElementById("appointmentFilter").addEventListener("change", filterPatients);

renderPatients(patients);

// Modal management functions
function openModal() {
    document.getElementById("addPatientModal").style.display = "block";
}

function closeModal() {
    document.getElementById("addPatientModal").style.display = "none";
}

// Save patient function (dummy function)
function savePatient() {
    alert("Patient information saved!");
    closeModal();
}

// Event listener for opening modal
document.getElementById("add-btn").addEventListener("click", openModal);

// Event listener for closing modal
document.querySelector(".close-btn").addEventListener("click", closeModal);

// Render the initial patient list
renderPatients(patients);

function showTab(event, tabId) {
    // Hide all tab content
    let tabContents = document.querySelectorAll('.tab-content');
    tabContents.forEach(tab => tab.style.display = 'none');

    // Remove active class from all buttons
    let tabButtons = document.querySelectorAll('.tab-btn');
    tabButtons.forEach(button => button.classList.remove('active'));

    // Show the selected tab content
    document.getElementById(tabId).style.display = 'block';

    // Add active class to the clicked button
    event.currentTarget.classList.add('active');
}

document.addEventListener("DOMContentLoaded", function () {
    const addBtn = document.getElementById("add-btn");
    const appointmentContainer = document.querySelector(".appointment-container");
    const cancelBtn = document.querySelector(".cancel");

    // Hide appointment container initially
    appointmentContainer.style.display = "none";

    // Show appointment form when clicking "+ New Appointment"
    addBtn.addEventListener("click", function () {
        appointmentContainer.style.display = "block";
    });

    // Hide appointment form when clicking "Cancel"
    cancelBtn.addEventListener("click", function () {
        appointmentContainer.style.display = "none";
    });
});
