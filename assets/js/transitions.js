document.addEventListener("DOMContentLoaded", function () {
    const url = window.location.href;
    const hashIndex = url.indexOf('#');
    const sectionId = hashIndex !== -1 ? url.substring(hashIndex + 1) : 'dashboard';
    const link = document.getElementById(`${sectionId}Link`);
    if (link) {
        link.classList.add('active');
    }
    transitionTo(sectionId);
});
 
function transitionTo(sectionId) {
    const activeLink = document.querySelector('.active');
    if (activeLink) {
        activeLink.classList.remove('active');
    }
    const link = document.getElementById(sectionId + 'Link');
    if (link) {
        link.classList.add('active');
    //    localStorage.setItem("activeLink", sectionId + 'Link');
    }
    const content = document.querySelector('.content');
    const cards = content.querySelectorAll('.card');
    cards.forEach(card => {
        card.style.opacity = 0;
    });
    setTimeout(() => {
        cards.forEach(card => {
            card.style.display = 'none';
        });
        const selectedCard = content.querySelector('#' + sectionId);
        selectedCard.style.display = 'block';
        setTimeout(() => {
            selectedCard.style.opacity = 1;
        }, 50);
    }, 300);
}