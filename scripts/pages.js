// SEARCH
const searchInput = document.getElementById('search');
if (searchInput) {
    searchInput.addEventListener('keyup', function () {
        const value = this.value.toLowerCase();
        document.querySelectorAll('.menu-table tbody tr').forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(value) ? '' : 'none';
        });
    });
}

// ADD MODAL
const modal = document.getElementById('add-modal');
const addBtn = document.getElementById('addbtn');
const closeBtn = document.getElementById('close-modal');

addBtn.addEventListener('click', () => modal.classList.add('active'));
closeBtn.addEventListener('click', () => modal.classList.remove('active'));
modal.addEventListener('click', (e) => {
    if (e.target === modal) modal.classList.remove('active');
});

// DELETE CONFIRM MODAL
const confirmModal = document.getElementById('confirm-modal');
const cancelDelete = document.getElementById('cancel-delete');

if (cancelDelete && confirmModal) {
    
    const currentPage = window.location.pathname.split('/').pop();

    cancelDelete.addEventListener('click', () => {
        window.location.href = `${currentPage}?cancel_delete=1`;
    });

    confirmModal.addEventListener('click', (e) => {
        if (e.target === confirmModal) {
            window.location.href = `${currentPage}?cancel_delete=1`;
        }
    });
}

// EDIT MODAL
const editModal = document.getElementById('edit-modal');
const closeEditBtn = document.getElementById('close-edit-modal');

if (editModal && closeEditBtn) {
    const cancelUrl = editModal.dataset.cancelUrl;

    closeEditBtn.addEventListener('click', () => {
        editModal.classList.remove('active');
        window.location.href = cancelUrl;
    });

    editModal.addEventListener('click', (e) => {
        if (e.target === editModal) {
            editModal.classList.remove('active');
            window.location.href = cancelUrl;
        }
    });
}