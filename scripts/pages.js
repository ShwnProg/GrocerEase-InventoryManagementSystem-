const searchInput = document.getElementById('search');

// SIMPLE SEARCH 
if (searchInput) {
    searchInput.addEventListener('keyup', function () {
        const value = this.value.toLowerCase();
        document.querySelectorAll('.menu-table tbody tr').forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(value) ? '' : 'none';
        });
    });
}

//  HELPERS 
function showFeedback(containerId, message, type) {
    const el = document.getElementById(containerId);
    if (el) el.innerHTML = `<div class="${type}-message">${message}</div>`;
}

function clearFeedback(containerId) {
    const el = document.getElementById(containerId);
    if (el) el.innerHTML = '';
}

//  ADD MODAL
const addModal = document.getElementById('add-modal');
const addBtn   = document.getElementById('addbtn');
const closeBtn = document.getElementById('close-modal');

if (addBtn)   addBtn.addEventListener('click', () => addModal.classList.add('active'));
if (closeBtn) closeBtn.addEventListener('click', () => addModal.classList.remove('active'));
if (addModal) {
    addModal.addEventListener('click', e => {
        if (e.target === addModal) addModal.classList.remove('active');
    });
}

// EDIT MODAL
const editModal    = document.getElementById('edit-modal');
const closeEditBtn = document.getElementById('close-edit-modal');
const confirmEdit  = document.getElementById('confirm-edit');

function openEditModal(productId, supplierId, supplierName, costPrice) {
    document.getElementById('edit-product-id').value      = productId;
    document.getElementById('edit-supplier-id').value     = supplierId;
    document.getElementById('edit-supplier-label').textContent = supplierName;
    document.getElementById('edit-cost-price').value      = costPrice;
    clearFeedback('edit-feedback');
    editModal.classList.add('active');
}

function closeEditModal() {
    editModal.classList.remove('active');
}

document.addEventListener('click', e => {
    const btn = e.target.closest('.open-edit-modal');
    if (!btn) return;
    openEditModal(
        btn.dataset.productId,
        btn.dataset.supplierId,
        btn.dataset.supplierName,
        btn.dataset.costPrice
    );
});

if (closeEditBtn) closeEditBtn.addEventListener('click', closeEditModal);
if (editModal) {
    editModal.addEventListener('click', e => {
        if (e.target === editModal) closeEditModal();
    });
}

if (confirmEdit) {
    confirmEdit.addEventListener('click', () => {
        const productId    = document.getElementById('edit-product-id').value;
        const supplierId   = document.getElementById('edit-supplier-id').value;
        const supplierName = document.getElementById('edit-supplier-label').textContent;
        const costPrice    = document.getElementById('edit-cost-price').value.trim();

        clearFeedback('edit-feedback');

        $.ajax({
            url: '../validation/product_suppliers/edit_process.php',
            type: 'POST',
            dataType: 'json',
            data: { product_id: productId, supplier_id: supplierId, supplier_name: supplierName, cost_price: costPrice },
            success(res) {
                if (res.status === 'success') {
                    showFeedback('edit-feedback', res.message, 'success');
                    setTimeout(() => location.reload(), 1200);
                } else {
                    showFeedback('edit-feedback', res.message, 'error');
                }
            },
            error() {
                showFeedback('edit-feedback', 'Server error. Please try again.', 'error');
            },
        });
    });
}

document.addEventListener('keydown', e => {
    if (e.key !== 'Escape') return;
    if (editModal?.classList.contains('active')) closeEditModal();
    if (addModal?.classList.contains('active'))  addModal.classList.remove('active');
});

function removeSupplier(productId, supplierId, name) {
    Swal.fire({
        title: `Remove ${name}?`,
        text: 'This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e9a33a',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Remove',
        cancelButtonText: 'Cancel',
    }).then(result => {
        if (!result.isConfirmed) return;
        $.ajax({
            url: '../validation/product_suppliers/remove_suppliers.php',
            type: 'POST',
            dataType: 'json',
            data: { product_id: productId, supplier_id: supplierId },
            success(res) {
                if (res.status === 'success') {
                    Swal.fire({ title: 'Removed!', text: res.message, icon: 'success', confirmButtonColor: '#1c5515' })
                        .then(() => location.reload());
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            },
            error() { Swal.fire('Server Error', 'Something went wrong.', 'error'); },
        });
    });
}

//  DELETE PRODUCT 
function deleteProduct(id, name) {
    Swal.fire({
        title: `Delete ${name}?`, text: 'This action cannot be undone.', icon: 'warning',
        showCancelButton: true, confirmButtonColor: '#c82828', cancelButtonColor: '#6b7280',
        confirmButtonText: 'Delete', cancelButtonText: 'Cancel',
    }).then(result => {
        if (!result.isConfirmed) return;
        $.ajax({
            url: '../validation/products/delete_product.php', type: 'POST', data: { product_id: id },
            success() {
                Swal.fire({ title: 'Deleted!', text: 'Product has been deleted.', icon: 'success', confirmButtonColor: '#1c5515' })
                    .then(() => location.reload());
            },
            error() { Swal.fire('Error!', 'Something went wrong.', 'error'); },
        });
    });
}

//  DELETE CATEGORY 
function deleteCategory(id, name) {
    Swal.fire({
        title: `Delete ${name}?`, text: 'This action cannot be undone.', icon: 'warning',
        showCancelButton: true, confirmButtonColor: '#c82828', cancelButtonColor: '#6b7280',
        confirmButtonText: 'Delete', cancelButtonText: 'Cancel',
    }).then(result => {
        if (!result.isConfirmed) return;
        $.ajax({
            url: '../validation/categories/delete_category.php', type: 'POST', dataType: 'json', data: { category_id: id },
            success(res) {
                if (res.status === 'success') {
                    Swal.fire({ title: 'Deleted!', text: res.message, icon: 'success', confirmButtonColor: '#1c5515' })
                        .then(() => location.reload());
                } else {
                    Swal.fire({ title: 'Error', text: res.message, icon: 'error' });
                }
            },
            error() { Swal.fire('Server Error', 'Something went wrong.', 'error'); },
        });
    });
}

//  DELETE SUPPLIER 
function deleteSupplier(id, name) {
    Swal.fire({
        title: `Delete ${name}?`, text: 'This action cannot be undone.', icon: 'warning',
        showCancelButton: true, confirmButtonColor: '#c82828', cancelButtonColor: '#6b7280',
        confirmButtonText: 'Delete', cancelButtonText: 'Cancel',
    }).then(result => {
        if (!result.isConfirmed) return;
        $.ajax({
            url: '../validation/suppliers/delete_supplier.php', type: 'POST', dataType: 'json', data: { supplier_id: id },
            success(res) {
                if (res.status === 'success') {
                    Swal.fire({ icon: 'success', title: 'Deleted!', text: res.message, confirmButtonColor: '#3085d6' })
                        .then(() => location.reload());
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: res.message, confirmButtonColor: '#c82828' });
                }
            },
            error() { Swal.fire({ icon: 'error', title: 'Server Error', text: 'Something went wrong.' }); },
        });
    });
}

//  RESTORE / HARD-DELETE: PRODUCTS 
function restoreProduct(id, name) {
    Swal.fire({ title: `Restore ${name}?`, icon: 'question', showCancelButton: true }).then(result => {
        if (!result.isConfirmed) return;
        $.post('../validation/products/recover.php', { product_id: id }, () => {
            Swal.fire('Restored!', 'Success', 'success').then(() => location.reload());
        });
    });
}

function hardDeleteProduct(id, name) {
    Swal.fire({
        title: `Delete ${name}?`, text: 'This action is permanent.', icon: 'warning',
        showCancelButton: true, confirmButtonColor: '#c82828', cancelButtonColor: '#6b7280',
        confirmButtonText: 'Delete', cancelButtonText: 'Cancel',
    }).then(result => {
        if (!result.isConfirmed) return;
        $.ajax({
            url: '../validation/products/hard_delete_product.php', type: 'POST', dataType: 'json', data: { product_id: id },
            success(res) {
                if (res.status === 'success') {
                    Swal.fire('Deleted!', res.message, 'success').then(() => location.reload());
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            },
            error() { Swal.fire('Error', 'Server error occurred', 'error'); },
        });
    });
}

//  RESTORE / HARD-DELETE: CATEGORIES 
function restoreCategory(id, name) {
    Swal.fire({ title: `Restore ${name}?`, icon: 'question', showCancelButton: true }).then(result => {
        if (!result.isConfirmed) return;
        $.post('../validation/categories/recover.php', { category_id: id }, () => {
            Swal.fire('Restored!', 'Success', 'success').then(() => location.reload());
        });
    });
}

function hardDeleteCategory(id, name) {
    Swal.fire({
        title: `Delete ${name}?`, text: 'This action is permanent.', icon: 'warning',
        showCancelButton: true, confirmButtonColor: '#c82828', cancelButtonColor: '#6b7280',
        confirmButtonText: 'Delete', cancelButtonText: 'Cancel',
    }).then(result => {
        if (!result.isConfirmed) return;
        $.ajax({
            url: '../validation/categories/hard_delete_category.php', type: 'POST', dataType: 'json', data: { category_id: id },
            success(res) {
                if (res.status === 'success') {
                    Swal.fire('Deleted!', res.message, 'success').then(() => location.reload());
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            },
            error() { Swal.fire('Error', 'Server error occurred', 'error'); },
        });
    });
}

//  RESTORE / HARD-DELETE: SUPPLIERS 
function restoreSupplier(id, name) {
    Swal.fire({ title: `Restore ${name}?`, icon: 'question', showCancelButton: true }).then(result => {
        if (!result.isConfirmed) return;
        $.post('../validation/suppliers/recover.php', { supplier_id: id }, () => {
            Swal.fire('Restored!', 'Success', 'success').then(() => location.reload());
        });
    });
}

function hardDeleteSupplier(id, name) {
    Swal.fire({
        title: `Delete ${name}?`, text: 'This action is permanent.', icon: 'warning',
        showCancelButton: true, confirmButtonColor: '#c82828', cancelButtonColor: '#6b7280',
        confirmButtonText: 'Delete', cancelButtonText: 'Cancel',
    }).then(result => {
        if (!result.isConfirmed) return;
        $.ajax({
            url: '../validation/suppliers/hard_delete_supplier.php', type: 'POST', dataType: 'json', data: { supplier_id: id },
            success(res) {
                if (res.status === 'success') {
                    Swal.fire('Deleted!', res.message, 'success').then(() => location.reload());
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            },
            error() { Swal.fire({ icon: 'error', title: 'Server Error', text: 'Something went wrong.' }); },
        });
    });
}

//  STOCK IN MODAL 
const stockInModal = document.getElementById('stock-in-modal');
const closeStockIn = document.getElementById('close-stock-in');

document.querySelectorAll('.open-stock-in').forEach(btn => {
    btn.addEventListener('click', () => {
        document.getElementById('stock-in-product-id').value = btn.dataset.id;
        document.getElementById('stock-in-product-name').textContent = btn.dataset.name;
        document.getElementById('stock-in-product-name-input').value = btn.dataset.name;
        stockInModal.classList.add('active');
    });
});

if (closeStockIn) closeStockIn.addEventListener('click', () => stockInModal.classList.remove('active'));
if (stockInModal) {
    stockInModal.addEventListener('click', e => {
        if (e.target === stockInModal) stockInModal.classList.remove('active');
    });
}

//  STOCK OUT MODAL 
const stockOutModal = document.getElementById('stock-out-modal');
const closeStockOut = document.getElementById('close-stock-out');

document.querySelectorAll('.open-stock-out').forEach(btn => {
    btn.addEventListener('click', () => {
        document.getElementById('stock-out-product-id').value = btn.dataset.id;
        document.getElementById('stock-out-product-name').textContent = btn.dataset.name;
        document.getElementById('stock-out-product-name-input').value = btn.dataset.name;
        stockOutModal.classList.add('active');
    });
});

if (closeStockOut) closeStockOut.addEventListener('click', () => stockOutModal.classList.remove('active'));
if (stockOutModal) {
    stockOutModal.addEventListener('click', e => {
        if (e.target === stockOutModal) stockOutModal.classList.remove('active');
    });
}