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
const modal    = document.getElementById('add-modal');
const addBtn   = document.getElementById('addbtn');
const closeBtn = document.getElementById('close-modal');

if (addBtn && modal && closeBtn) {
    addBtn.addEventListener('click', () => modal.classList.add('active'));
    closeBtn.addEventListener('click', () => modal.classList.remove('active'));
    modal.addEventListener('click', (e) => {
        if (e.target === modal) modal.classList.remove('active');
    });
}

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
const editModal    = document.getElementById('edit-modal');
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

// STOCK IN MODAL
const stockInModal = document.getElementById('stock-in-modal');
const closeStockIn = document.getElementById('close-stock-in');

document.querySelectorAll('.open-stock-in').forEach(btn => {
    btn.addEventListener('click', () => {
        document.getElementById('stock-in-product-id').value          = btn.dataset.id;
        document.getElementById('stock-in-product-name').textContent  = btn.dataset.name;
        document.getElementById('stock-in-product-name-input').value  = btn.dataset.name;
        stockInModal.classList.add('active');
    });
});

if (closeStockIn) {
    closeStockIn.addEventListener('click', () => stockInModal.classList.remove('active'));
}
if (stockInModal) {
    stockInModal.addEventListener('click', (e) => {
        if (e.target === stockInModal) stockInModal.classList.remove('active');
    });
}

// STOCK OUT MODAL
const stockOutModal = document.getElementById('stock-out-modal');
const closeStockOut = document.getElementById('close-stock-out');

document.querySelectorAll('.open-stock-out').forEach(btn => {
    btn.addEventListener('click', () => {
        document.getElementById('stock-out-product-id').value         = btn.dataset.id;
        document.getElementById('stock-out-product-name').textContent = btn.dataset.name;
        document.getElementById('stock-out-product-name-input').value = btn.dataset.name;
        stockOutModal.classList.add('active');
    });
});

if (closeStockOut) {
    closeStockOut.addEventListener('click', () => stockOutModal.classList.remove('active'));
}
if (stockOutModal) {
    stockOutModal.addEventListener('click', (e) => {
        if (e.target === stockOutModal) stockOutModal.classList.remove('active');
    });
}