function openModal() {
    document.getElementById("appointmentModal").style.display = "flex";
}

function closeModal() {
    document.getElementById("appointmentModal").style.display = "none";
}

document.getElementById("openModalBtn").addEventListener("click", openModal);

window.onclick = function(event) {
    var modal = document.getElementById("appointmentModal");
    if (event.target === modal) {
        closeModal();
    }
}
function openRevenueModal() {
document.getElementById("revenueModal").style.display = "flex";
}
function closeRevenueModal() {
document.getElementById("revenueModal").style.display = "none";
}
document.getElementById("openRevenueBtn").addEventListener("click", openRevenueModal);
const searchInput = document.getElementById('search');
const statusFilter = document.getElementById('statusFilter');
const appointmentFilter = document.getElementById('appointmentFilter');
const tableRows = document.querySelectorAll('.table-container tr:not(:first-child)');

function filterTable() {
    const searchValue = searchInput.value.toLowerCase();
    const statusValue = statusFilter.value;
    const appointmentValue = appointmentFilter.value;
    const currentDate = new Date();

    tableRows.forEach(row => {
        const cells = row.querySelectorAll('td');
        if (cells.length < 5) return;

        const name = cells[0].textContent.toLowerCase();
        const status = cells[4].textContent.toLowerCase();
        const dateStr = cells[2].textContent;
        const rowDate = new Date(dateStr);

        let show = true;

        if (!name.includes(searchValue)) {
            show = false;
        }

        if (statusValue !== 'all') {
            if ((statusValue === 'active' && status !== 'scheduled') ||
                (statusValue === 'inactive' && status !== 'done')) {
                show = false;
            }
        }

        if (appointmentValue !== 'all') {
            if ((appointmentValue === 'upcoming' && rowDate < currentDate) ||
                (appointmentValue === 'past' && rowDate >= currentDate)) {
                show = false;
            }
        }

        row.style.display = show ? '' : 'none';
    });
}

searchInput.addEventListener('input', filterTable);
statusFilter.addEventListener('change', filterTable);
appointmentFilter.addEventListener('change', filterTable);

// Set the current date in Asian time zone (example using Tokyo)
const options = { year: 'numeric', month: 'long' };
const date = new Date().toLocaleDateString('en-GB', options); // Adjust the locale as needed
document.getElementById("dateDisplay").innerText = ` ${date}`;

function closeRevenueModal() {
document.getElementById("revenueModal").style.display = "none";
}

// Function to show the modal (can be triggered elsewhere)
function openRevenueModal() {
document.getElementById("revenueModal").style.display = "block";
}


document.getElementById("exportBtn").addEventListener("click", function () {
const element = document.getElementById("contentToExport");

// Make the content temporarily visible for PDF generation
element.style.display = 'block';

const opt = {
margin:       0.5,
filename:     'Bolasco-Servanez Dental Clinic_Appointment.pdf',
image:        { type: 'jpeg', quality: 0.98 },
html2canvas:  { scale: 2 },
jsPDF:        { unit: 'in', format: 'letter', orientation: 'portrait' }
};

// Generate the PDF
html2pdf().set(opt).from(element).save().then(function() {
// After saving the PDF, hide the content again
element.style.display = 'none';
});
});



document.getElementById('currentDate').textContent = new Date().toLocaleDateString();

function updateDate() {
const options = { timeZone: 'Asia/Manila', year: 'numeric', month: 'long', day: 'numeric' };
const today = new Date().toLocaleDateString('en-PH', options);
document.getElementById('currentDate').textContent = today;
}

updateDate(); // Call on load



