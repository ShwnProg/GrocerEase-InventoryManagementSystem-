// search functionality
const searchInput = document.getElementById('search');
if (searchInput) {
    searchInput.addEventListener('keyup', function () {
        let value = this.value.toLowerCase();
        let rows = document.querySelectorAll('.menu-table tbody tr');
        rows.forEach(row => {
            let text = row.textContent.toLowerCase();
            row.style.display = text.includes(value) ? '' : 'none';
        });
    });
}
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

// DELETE MODAL
const confirmModal = document.getElementById('confirm-modal');
const cancelDelete = document.getElementById('cancel-delete');
const confirmDelete = document.getElementById('confirm-delete');

let deleteForm = null; 


document.querySelectorAll('.delete-form').forEach(form => {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        deleteForm = this; 
        confirmModal.classList.add('active');
    });
});

confirmDelete.addEventListener('click', () => {
    if (deleteForm) deleteForm.submit();
});

cancelDelete.addEventListener('click', () => {
    confirmModal.classList.remove('active');
    deleteForm = null;
});

confirmModal.addEventListener('click', (e) => {
    if (e.target === confirmModal) {
        confirmModal.classList.remove('active');
        deleteForm = null;
    }
});