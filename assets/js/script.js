

function openAddWorkshopModal() {
    const addWorkshopModal = document.getElementById('addWorkshopModal');
    addWorkshopModal.style.display = 'block';
}
function closeAddWorkshopModal() {
    document.getElementById('addWorkshopModal').style.display = 'none';
}

let workshops = [
    { name: 'Workshop A', status: 'active' },
    { name: 'Workshop B', status: 'active' },
    // Add more workshops as needed
];

// JavaScript function to toggle between blocking and unblocking a workshop




// Update icon to block icon
function updateIconToBlock(workshopName) {
    const icon = document.querySelector(`[data-service-provider="${workshopName}"]`);
    icon.style.color = 'red'; 
    // if (icon) {
    //     icon.classList.remove('fa-check-circle');
    //     icon.classList.add('fa-ban');
    // }
}

// Update icon to unblock icon
function updateIconToUnblock(workshopName) {
    const icon = document.querySelector(`[data-service-provider="${workshopName}"]`);
    icon.style.color = 'black'; 
    // if (icon) {
    //     icon.classList.remove('fa-ban');
    //     icon.classList.add('fa-check-circle');
    // }
}

// Update icon to loading icon
function updateIconToLoading(serviceProviderId) {
    const icon = document.querySelector(`[data-service-provider="${serviceProviderId}"]`);
    if (icon) {
        // Remove existing icon classes
        icon.classList.remove('fa-check-circle', 'fa-ban');
        // Add loading icon class
        icon.classList.add('fa-spinner', 'fa-spin');
    }
}


class Workshop {
    constructor(district, streetName, city) {
        this.district = district;
        this.streetName = streetName;
        this.city = city;
    }
}

class ServiceProvider {
    constructor(id, name, phoneNumber, email) {
        this.id = id;
        this.name = name;
        this.phoneNumber = phoneNumber;
        this.email = email;
    }
}

function showDetails(workshop, serviceProvider) {
    const modal = document.getElementById('myModal');
    const district = document.getElementById('district');
    const streetName = document.getElementById('streetName');
    const city = document.getElementById('city');
    const providerId = document.getElementById('providerId');
    const providerName = document.getElementById('providerName');
    const providerPhone = document.getElementById('providerPhone');
    const providerEmail = document.getElementById('providerEmail');

    district.innerText = workshop.district;
    streetName.innerText = workshop.streetName;
    city.innerText = workshop.city;
    providerId.innerText = serviceProvider.id;
    providerName.innerText = serviceProvider.name;
    providerPhone.innerText = serviceProvider.phoneNumber;
    providerEmail.innerText = serviceProvider.email;

    modal.style.display = 'block';
}
function closeModal() {
    document.getElementById('myModal').style.display = 'none';
}


