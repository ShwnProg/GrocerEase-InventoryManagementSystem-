// search functionality
document.getElementById('search').addEventListener('keyup', function () {
    let value = this.value.toLowerCase();
    let rows = document.querySelectorAll('.menu-table tbody tr');

    rows.forEach(row => {
        let text = row.textContent.toLowerCase();
        row.style.display = text.includes(value) ? '' : 'none';
    });
});

// modal functionality
const modal = document.getElementById('add-modal');
const addBtn = document.getElementById('addbtn');
const closeBtn = document.getElementById('close-modal');

addBtn.addEventListener('click', () => {
    modal.classList.add('active');
});

closeBtn.addEventListener('click', () => {
    modal.classList.remove('active');
});

// click outside to close
modal.addEventListener('click', (e) => {
    if (e.target === modal) {
        modal.classList.remove('active');
    }
});