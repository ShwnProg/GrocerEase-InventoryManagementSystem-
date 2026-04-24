const searchInput = document.getElementById('search');

const restoreModal = document.getElementById('restore-modal');
const confirmModal = document.getElementById('confirm-modal');

const modal = document.getElementById('add-modal');
const addBtn = document.getElementById('addbtn');
const closeBtn = document.getElementById('close-modal');

// SEARCH
if (searchInput) {
    searchInput.addEventListener('keyup', function () {
        const value = this.value.toLowerCase();
        document.querySelectorAll('.menu-table tbody tr').forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(value) ? '' : 'none';
        });
    });
}

// ADD MODAL
if (addBtn && modal && closeBtn) {
    addBtn.addEventListener('click', () => modal.classList.add('active'));
    closeBtn.addEventListener('click', () => modal.classList.remove('active'));
    modal.addEventListener('click', (e) => {
        if (e.target === modal) modal.classList.remove('active');
    });
}

// DELETE CONFIRM MODAL

const deleteAlertConfig = (name) => ({
    title: `Delete ${name}?`,
    text: "This action cannot be undone.",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#c82828",
    cancelButtonColor: "#6b7280",
    confirmButtonText: "Delete",
    cancelButtonText: "Cancel"
});

function deleteProduct(id, name) {
    Swal.fire(deleteAlertConfig(name)).then((result) => {

        if (result.isConfirmed) {

            $.ajax({
                url: '../validation/products/delete_product.php',
                type: 'POST',
                data: { product_id: id },

                success: function () {
                    Swal.fire({
                        title: 'Deleted!',
                        text: 'Product has been deleted.',
                        icon: 'success',
                        confirmButtonColor: '#1c5515'
                    }).then(() => location.reload());
                },

                error: function () {
                    Swal.fire('Error!', 'Something went wrong.', 'error');
                }
            });

        }

    });
}
function deleteCategory(id, name) {

    Swal.fire(deleteAlertConfig(name)).then((result) => {

        if (result.isConfirmed) {

            $.ajax({
                url: '../validation/categories/delete_category.php',
                type: 'POST',
                dataType: 'json',
                data: { category_id: id },

                success: function (res) {

                    if (res.status === "success") {

                        Swal.fire({
                            title: 'Deleted!',
                            text: res.message,
                            icon: 'success',
                            confirmButtonColor: '#1c5515'
                        }).then(() => location.reload());

                    } else {

                        Swal.fire({
                            title: 'Error',
                            text: res.message,
                            icon: 'error'
                        });

                    }

                },

                error: function () {
                    Swal.fire('Server Error', 'Something went wrong.', 'error');
                }

            });

        }

    });
}
function removeSupplier(productId, supplierId, name) {

    Swal.fire(deleteAlertConfig(name)).then((result) => {

        if (result.isConfirmed) {

            $.ajax({
                url: '../validation/product_suppliers/remove_suppliers.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    product_id: productId,
                    supplier_id: supplierId
                },

                success: function (res) {

                    if (res.status === "success") {
                        Swal.fire({
                            title: "Removed!",
                            text: res.message,
                            icon: "success",
                            confirmButtonColor: "#1c5515"
                        }).then(() => location.reload());
                    } else {
                        Swal.fire("Error", res.message, "error");
                    }

                },

                error: function () {
                    Swal.fire("Server Error", "Something went wrong", "error");
                }

            });

        }

    });

}

function deleteSupplier(id, name) {

    Swal.fire({
        title: `Delete ${name}?`,
        text: "This action cannot be undone.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#c82828",
        cancelButtonColor: "#6b7280",
        confirmButtonText: "Delete",
        cancelButtonText: "Cancel"
    }).then((result) => {

        if (result.isConfirmed) {

            $.ajax({
                url: '../validation/suppliers/delete_supplier.php',
                type: 'POST',
                dataType: 'json',
                data: { supplier_id: id },

                success: function (res) {
                    if (res.status === "success") {
                        Swal.fire({
                            icon: "success",
                            title: "Deleted!",
                            text: res.message,
                            confirmButtonColor: "#3085d6"
                        }).then(() => location.reload());
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Error",
                            text: res.message,
                            confirmButtonColor: "#c82828"
                        });
                    }
                },

                error: function () {
                    Swal.fire({
                        icon: "error",
                        title: "Server Error",
                        text: "Something went wrong."
                    });
                }
            });

        }

    });
}
// RESTORE CONFIRM MODAL PRODUCT
function restoreProduct(id, name) {
    Swal.fire({
        title: `Restore ${name}?`,
        icon: "question",
        showCancelButton: true
    }).then((result) => {
        if (result.isConfirmed) {
            $.post('../validation/products/recover.php', { product_id: id }, function (res) {
                Swal.fire("Restored!", "Success", "success")
                    .then(() => location.reload());
            });
        }
    });
}

// HARD DELETE PRODUCT
function hardDeleteProduct(id, name) {

    Swal.fire({
        title: `Delete ${name}?`,
        text: "This action is permanent.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#c82828",
        cancelButtonColor: "#6b7280",
        confirmButtonText: "Delete",
        cancelButtonText: "Cancel"
    }).then((result) => {

        if (result.isConfirmed) {

            $.ajax({
                url: '../validation/products/hard_delete_product.php',
                type: 'POST',
                dataType: 'json',
                data: { product_id: id },

                success: function (res) {
                    if (res.status === "success") {
                        Swal.fire("Deleted!", res.message, "success")
                            .then(() => location.reload());
                    } else {
                        Swal.fire("Error", res.message, "error");
                    }
                },

                error: function () {
                    Swal.fire("Error", "Server error occurred", "error");
                }
            });

        }

    });
}

// RESTORE CONFIMATION MODAL CATEGORY
function restoreCategory(id, name) {
    Swal.fire({
        title: `Restore ${name}?`,
        icon: "question",
        showCancelButton: true
    }).then((result) => {
        if (result.isConfirmed) {
            $.post('../validation/categories/recover.php', { category_id: id }, function (res) {
                Swal.fire("Restored!", "Success", "success")
                    .then(() => location.reload());
            });
        }
    });
}

// HARD DELETE CATEGORY
function hardDeleteCategory(id, name) {

    Swal.fire({
        title: `Delete ${name}?`,
        text: "This action is permanent.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#c82828",
        cancelButtonColor: "#6b7280",
        confirmButtonText: "Delete",
        cancelButtonText: "Cancel"
    }).then((result) => {

        if (result.isConfirmed) {

            $.ajax({
                url: '../validation/categories/hard_delete_category.php',
                type: 'POST',
                dataType: 'json',
                data: { category_id: id },

                success: function (res) {
                    if (res.status === "success") {
                        Swal.fire("Deleted!", res.message, "success")
                            .then(() => location.reload());
                    } else {
                        Swal.fire("Error", res.message, "error");
                    }
                },

                error: function () {
                    Swal.fire("Error", "Server error occurred", "error");
                }
            });

        }

    });
}

// RESTORE CONFIMATION MODAL SUPPLIER
function restoreSupplier(id, name) {
    Swal.fire({
        title: `Restore ${name}?`,
        icon: "question",
        showCancelButton: true
    }).then((result) => {
        if (result.isConfirmed) {
            $.post('../validation/suppliers/recover.php', { supplier_id: id }, function (res) {
                Swal.fire("Restored!", "Success", "success")
                    .then(() => location.reload());
            });
        }
    });
}

// HARD DELETE SUPPLIER
function hardDeleteSupplier(id, name) {

    Swal.fire({
        title: `Delete ${name}?`,
        text: "This action is permanent.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#c82828",
        cancelButtonColor: "#6b7280",
        confirmButtonText: "Delete",
        cancelButtonText: "Cancel"
    }).then((result) => {

        if (result.isConfirmed) {

            $.ajax({
                url: '../validation/suppliers/hard_delete_supplier.php',
                type: 'POST',
                dataType: 'json',
                data: { supplier_id: id },

                success: function (res) {
                    if (res.status === "success") {
                        Swal.fire("Deleted!", res.message, "success")
                            .then(() => location.reload());
                    } else {
                        Swal.fire("Error", res.message, "error");
                    }
                },

                error: function () {
                    Swal.fire("Error", "Server error occurred", "error");
                }
            });

        }

    });
}

document.addEventListener('keydown', (e) => {
    if (e.key !== 'Escape') return;

    const currentPage = window.location.pathname.split('/').pop();

    if (restoreModal?.classList.contains('active')) {
        window.location.href = `${currentPage}?tab=products&cancel_restore=1`;
    }

    if (confirmModal?.classList.contains('active')) {
        window.location.href = `${currentPage}?cancel_delete=1`;
    }
});

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

// STOCK IN MODAL
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
        document.getElementById('stock-out-product-id').value = btn.dataset.id;
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