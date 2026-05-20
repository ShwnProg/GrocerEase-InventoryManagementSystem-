<?php
require_once __DIR__ . '/../../autoload.php';
include "../../includes/auth_check.php";
$_SESSION['page_title'] = "SETTINGS";
?>
<!DOCTYPE html>
<html lang="en">
<?php include "../../includes/head.php" ?>
<body>
    <?php include '../../includes/sidebar.php'; ?>
    <main class="main-content">
        <?php include '../../includes/topbar.php'; ?>
        <section class="page-content backup-page">
            <div class="backup-workspace">
                <div class="chart-card backup-action-card">
                    <div class="backup-card-header">
                        <div class="backup-icon">
                            <i class="fa-solid fa-shield-halved"></i>
                        </div>
                        <div>
                            <h3>Backup and Recovery</h3>
                            <p>Create manual database backups and restore verified SQL files.</p>
                        </div>
                    </div>

                    <div class="backup-action-stack">
                        <button type="button" class="backup-command primary" id="backup-now">
                            <i class="fa-solid fa-database"></i>
                            <span>Backup now</span>
                        </button>
                        <button type="button" class="backup-command secondary" id="choose-restore">
                            <i class="fa-solid fa-file-arrow-up"></i>
                            <span>Upload restore file</span>
                        </button>
                    </div>

                    <div class="restore-file-card" id="restore-dropzone">
                        <div class="restore-file-icon">
                            <i class="fa-solid fa-file-shield"></i>
                        </div>
                        <div class="restore-file-copy">
                            <strong id="restore-file-name">No restore file selected</strong>
                            <span id="restore-file-help">Only valid .sql database backup files are accepted.</span>
                        </div>
                    </div>

                    <div class="restore-warning">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <span>Restoring a backup may overwrite current database data. Make sure you selected the correct file.</span>
                    </div>

                    <div class="validation-panel" id="validation-panel">
                        <i class="fa-solid fa-circle-info"></i>
                        <span>Select a backup file to validate it before recovery.</span>
                    </div>

                    <button type="button" class="backup-command danger restore-submit" id="restore-uploaded" disabled>
                        <i class="fa-solid fa-rotate-left"></i>
                        <span>Restore selected file</span>
                    </button>

                    <input type="file" id="restore-file" accept=".sql" hidden>
                </div>

                <div class="chart-card backup-history-card">
                    <div class="backup-history-header">
                        <div class="backup-card-header">
                            <div class="backup-icon">
                                <i class="fa-solid fa-clock-rotate-left"></i>
                            </div>
                            <div>
                                <h3>Backup History</h3>
                                <p>Search, download, restore, or delete saved backup files.</p>
                            </div>
                        </div>

                        <div class="backup-history-filters" aria-label="Backup history filters">
                            <div class="backup-search">
                                <i class="fa-solid fa-magnifying-glass"></i>
                                <input type="text" id="backup-search" placeholder="Search backup file">
                            </div>
                            <select id="backup-type-filter" aria-label="Filter by backup type">
                                <option value="all">All types</option>
                                <option value="Full Backup">Full backup</option>
                                <option value="Safety Backup">Safety backup</option>
                            </select>
                            <select id="backup-status-filter" aria-label="Filter by backup status">
                                <option value="all">All status</option>
                                <option value="valid">Valid</option>
                                <option value="invalid">Needs review</option>
                            </select>
                            <button type="button" class="backup-filter-reset" id="backup-filter-reset" title="Clear filters">
                                <i class="fa-solid fa-rotate-right"></i>
                                <span>Reset</span>
                            </button>
                        </div>
                    </div>

                    <div class="menu-table backup-history-table">
                        <table>
                            <thead>
                                <tr>
                                    <th>File name</th>
                                    <th>Date created</th>
                                    <th>Type</th>
                                    <th>Size</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="backup-list">
                                <tr>
                                    <td colspan="6" class="backup-empty-cell">Loading backup history...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <script>
        const backupButton = document.getElementById('backup-now');
        const chooseRestoreButton = document.getElementById('choose-restore');
        const restoreUploadedButton = document.getElementById('restore-uploaded');
        const restoreFileInput = document.getElementById('restore-file');
        const restoreFileName = document.getElementById('restore-file-name');
        const restoreFileHelp = document.getElementById('restore-file-help');
        const validationPanel = document.getElementById('validation-panel');
        const backupSearchInput = document.getElementById('backup-search');
        const backupTypeFilter = document.getElementById('backup-type-filter');
        const backupStatusFilter = document.getElementById('backup-status-filter');
        const backupFilterReset = document.getElementById('backup-filter-reset');

        let selectedRestoreFile = null;
        let backupHistory = [];

        const escapeHtml = (value) => String(value)
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');

        const setLoading = (button, loading, label) => {
            button.disabled = loading;
            button.dataset.originalText = button.dataset.originalText || button.innerHTML;
            button.innerHTML = loading
                ? `<i class="fa-solid fa-spinner fa-spin"></i><span>${label}</span>`
                : button.dataset.originalText;
        };

        const showValidation = (type, message) => {
            validationPanel.className = `validation-panel ${type}`;
            validationPanel.innerHTML = `<i class="fa-solid ${type === 'valid' ? 'fa-circle-check' : type === 'invalid' ? 'fa-triangle-exclamation' : 'fa-circle-info'}"></i><span>${message}</span>`;
        };

        [backupSearchInput, backupTypeFilter, backupStatusFilter].forEach((control) => {
            control.addEventListener('input', renderBackupHistory);
            control.addEventListener('change', renderBackupHistory);
        });

        backupFilterReset.addEventListener('click', () => {
            backupSearchInput.value = '';
            backupTypeFilter.value = 'all';
            backupStatusFilter.value = 'all';
            renderBackupHistory();
        });

        backupButton.addEventListener('click', async () => {
            const result = await Swal.fire({
                title: 'Create database backup?',
                text: 'The system will export all current database tables into a full SQL backup.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Create backup',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#1c5515',
            });

            if (!result.isConfirmed) {
                return;
            }

            setLoading(backupButton, true, 'Creating...');
            try {
                const response = await fetch('../../controllers/backup.php?action=backup', { method: 'POST' });
                const data = await response.json();

                if (data.status === 'success') {
                    await Swal.fire('Backup created', `${data.filename} was created successfully.`, 'success');
                    loadBackupHistory();
                } else {
                    Swal.fire('Backup failed', data.message || 'Unable to create backup.', 'error');
                }
            } catch (error) {
                Swal.fire('Backup failed', 'The server returned an unexpected response.', 'error');
            } finally {
                setLoading(backupButton, false, '');
            }
        });

        chooseRestoreButton.addEventListener('click', () => restoreFileInput.click());

        restoreFileInput.addEventListener('change', async (event) => {
            selectedRestoreFile = event.target.files[0] || null;
            restoreUploadedButton.disabled = true;

            if (!selectedRestoreFile) {
                restoreFileName.textContent = 'No restore file selected';
                restoreFileHelp.textContent = 'Only valid .sql database backup files are accepted.';
                showValidation('neutral', 'Select a backup file to validate it before recovery.');
                return;
            }

            restoreFileName.textContent = selectedRestoreFile.name;
            restoreFileHelp.textContent = `${(selectedRestoreFile.size / 1024).toFixed(2)} KB selected`;

            if (!selectedRestoreFile.name.toLowerCase().endsWith('.sql')) {
                selectedRestoreFile = null;
                showValidation('invalid', 'Invalid file type. Please choose a .sql backup file.');
                return;
            }

            const formData = new FormData();
            formData.append('action', 'validate');
            formData.append('backup_file', selectedRestoreFile);

            showValidation('neutral', 'Validating restore file...');
            try {
                const response = await fetch('../../controllers/backup.php', {
                    method: 'POST',
                    body: formData,
                });
                const data = await response.json();

                if (data.status === 'success') {
                    restoreUploadedButton.disabled = false;
                    showValidation('valid', 'Backup file passed validation and is ready to restore.');
                } else {
                    selectedRestoreFile = null;
                    showValidation('invalid', data.message || 'Backup file failed validation.');
                }
            } catch (error) {
                selectedRestoreFile = null;
                showValidation('invalid', 'The selected file could not be validated.');
            }
        });

        restoreUploadedButton.addEventListener('click', async () => {
            if (!selectedRestoreFile) {
                showValidation('invalid', 'Please choose a valid backup file first.');
                return;
            }

            const result = await Swal.fire({
                title: 'Restore database?',
                html: 'This may overwrite current database data. A safety backup will be created automatically before restoring.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Restore database',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#c82828',
            });

            if (!result.isConfirmed) {
                return;
            }

            const formData = new FormData();
            formData.append('action', 'restore');
            formData.append('backup_file', selectedRestoreFile);

            setLoading(restoreUploadedButton, true, 'Restoring...');
            try {
                const response = await fetch('../../controllers/backup.php', {
                    method: 'POST',
                    body: formData,
                });
                const data = await response.json();

                if (data.status === 'success') {
                    await Swal.fire('Restore complete', `Database restored successfully. Safety backup: ${data.safety_backup}`, 'success');
                    restoreFileInput.value = '';
                    selectedRestoreFile = null;
                    restoreFileName.textContent = 'No restore file selected';
                    restoreFileHelp.textContent = 'Only valid .sql database backup files are accepted.';
                    showValidation('neutral', 'Select a backup file to validate it before recovery.');
                    loadBackupHistory();
                } else {
                    Swal.fire('Restore failed', data.message || 'Unable to restore backup.', 'error');
                }
            } catch (error) {
                Swal.fire('Restore failed', 'The server returned an unexpected response.', 'error');
            } finally {
                setLoading(restoreUploadedButton, false, '');
                restoreUploadedButton.disabled = selectedRestoreFile === null;
            }
        });

        async function loadBackupHistory() {
            const tbody = document.getElementById('backup-list');
            tbody.innerHTML = '<tr><td colspan="6" class="backup-empty-cell">Loading backup history...</td></tr>';

            try {
                const response = await fetch('../../controllers/backup.php?action=list');
                const result = await response.json();

                if (result.status !== 'success') {
                    throw new Error(result.message || 'Unable to load backups.');
                }

                backupHistory = result.data || [];
                renderBackupHistory();
            } catch (error) {
                tbody.innerHTML = '<tr><td colspan="6" class="backup-empty-cell">Backup history could not be loaded.</td></tr>';
            }
        }

        function getFilteredBackups() {
            const keyword = backupSearchInput.value.trim().toLowerCase();
            const type = backupTypeFilter.value;
            const status = backupStatusFilter.value;

            return backupHistory.filter((backup) => {
                const matchesKeyword = keyword === ''
                    || backup.filename.toLowerCase().includes(keyword)
                    || backup.date.toLowerCase().includes(keyword);
                const matchesType = type === 'all' || backup.type === type;
                const matchesStatus = status === 'all'
                    || (status === 'valid' && backup.valid)
                    || (status === 'invalid' && !backup.valid);

                return matchesKeyword && matchesType && matchesStatus;
            });
        }

        function renderBackupHistory() {
            const tbody = document.getElementById('backup-list');
            const backups = getFilteredBackups();

            if (backupHistory.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6">
                            <div class="backup-empty-state">
                                <i class="fa-solid fa-box-archive"></i>
                                <strong>No backup history yet</strong>
                                <span>Create your first backup to see records here.</span>
                                <button type="button" class="backup-command primary compact" onclick="document.getElementById('backup-now').click()">
                                    <i class="fa-solid fa-database"></i>
                                    <span>Backup now</span>
                                </button>
                            </div>
                        </td>
                    </tr>`;
                return;
            }

            if (backups.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="backup-empty-cell">No backup files match the selected filters.</td></tr>';
                return;
            }

            tbody.innerHTML = backups.map((backup) => `
                <tr>
                    <td><span class="backup-file-name">${escapeHtml(backup.filename)}</span></td>
                    <td>${escapeHtml(backup.date)}</td>
                    <td>${escapeHtml(backup.type)}</td>
                    <td>${escapeHtml(backup.readable_size)}</td>
                    <td><span class="backup-badge ${backup.valid ? 'valid' : 'invalid'}">${backup.valid ? 'Valid' : 'Needs review'}</span></td>
                    <td>
                        <div class="backup-row-actions">
                            <button onclick="downloadBackup('${escapeHtml(backup.filename)}')" class="history-action download" title="Download backup" aria-label="Download backup">
                                <i class="fa-solid fa-download"></i>
                            </button>
                            <button onclick="restoreSpecific('${escapeHtml(backup.filename)}')" class="history-action restore" title="Restore backup" aria-label="Restore backup" ${backup.valid ? '' : 'disabled'}>
                                <i class="fa-solid fa-rotate-left"></i>
                            </button>
                            <button onclick="deleteBackup('${escapeHtml(backup.filename)}')" class="history-action delete" title="Delete backup" aria-label="Delete backup">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `).join('');
        }

        function downloadBackup(filename) {
            window.location.href = `../../controllers/backup.php?action=download&file=${encodeURIComponent(filename)}`;
        }

        async function deleteBackup(filename) {
            const result = await Swal.fire({
                title: 'Delete this backup?',
                text: filename,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Delete backup',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#c82828',
            });

            if (!result.isConfirmed) {
                return;
            }

            const formData = new FormData();
            formData.append('action', 'delete');
            formData.append('filename', filename);

            try {
                const response = await fetch('../../controllers/backup.php', {
                    method: 'POST',
                    body: formData,
                });
                const data = await response.json();

                if (data.status === 'success') {
                    await Swal.fire('Backup deleted', `${data.filename} was deleted successfully.`, 'success');
                    loadBackupHistory();
                } else {
                    Swal.fire('Delete failed', data.message || 'Unable to delete backup.', 'error');
                }
            } catch (error) {
                Swal.fire('Delete failed', 'The server returned an unexpected response.', 'error');
            }
        }

        async function restoreSpecific(filename) {
            const result = await Swal.fire({
                title: 'Restore this backup?',
                text: filename,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Restore backup',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#c82828',
            });

            if (!result.isConfirmed) {
                return;
            }

            const formData = new FormData();
            formData.append('action', 'restore_specific');
            formData.append('filename', filename);

            try {
                Swal.fire({
                    title: 'Restoring database...',
                    text: 'Please wait while the backup is applied.',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading(),
                });

                const response = await fetch('../../controllers/backup.php', {
                    method: 'POST',
                    body: formData,
                });
                const data = await response.json();

                if (data.status === 'success') {
                    await Swal.fire('Restore complete', `Database restored successfully. Safety backup: ${data.safety_backup}`, 'success');
                    loadBackupHistory();
                } else {
                    Swal.fire('Restore failed', data.message || 'Unable to restore backup.', 'error');
                }
            } catch (error) {
                Swal.fire('Restore failed', 'The server returned an unexpected response.', 'error');
            }
        }

        loadBackupHistory();
    </script>

    <style>
        .backup-page {
            max-width: 1440px;
            margin: 0 auto;
        }

        .backup-workspace {
            display: grid;
            grid-template-columns: minmax(320px, 0.9fr) minmax(520px, 1.6fr);
            gap: 18px;
            align-items: start;
        }

        .backup-action-card,
        .backup-history-card {
            border-radius: 8px;
            padding: 20px;
        }

        .backup-action-card {
            position: sticky;
            top: 78px;
        }

        .backup-card-header {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 18px;
        }

        .backup-card-header h3 {
            margin: 0 0 4px;
            color: #111827;
            font-size: 15px;
            font-weight: 700;
        }

        .backup-card-header p {
            margin: 0;
            color: #8a8f98;
            font-size: 12px;
            line-height: 1.45;
        }

        .backup-icon,
        .restore-file-icon {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #1c5515;
            background: rgba(28, 85, 21, 0.08);
            flex-shrink: 0;
        }

        .backup-action-stack {
            display: grid;
            grid-template-columns: 1fr;
            gap: 10px;
            margin-bottom: 14px;
        }

        .backup-command {
            min-height: 40px;
            border: 1px solid transparent;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 14px;
            font-size: 13px;
            font-weight: 700;
            line-height: 1;
            cursor: pointer;
            transition: background 0.18s ease, color 0.18s ease, border-color 0.18s ease, transform 0.18s ease;
        }

        .backup-command:hover:not(:disabled) {
            transform: translateY(-1px);
        }

        .backup-command.primary {
            background: #1c5515;
            color: #fff;
        }

        .backup-command.secondary {
            background: #f4f7f4;
            border-color: rgba(28, 85, 21, 0.12);
            color: #1c5515;
        }

        .backup-command.danger {
            background: rgba(200, 40, 40, 0.08);
            border-color: rgba(200, 40, 40, 0.18);
            color: #b91c1c;
        }

        .backup-command.compact {
            min-height: 36px;
            padding: 9px 12px;
            font-size: 12px;
        }

        .restore-file-card {
            display: flex;
            align-items: center;
            gap: 12px;
            border: 1px dashed rgba(28, 85, 21, 0.28);
            border-radius: 8px;
            padding: 14px;
            background: #f7faf5;
            margin-bottom: 12px;
        }

        .restore-file-copy strong,
        .restore-file-copy span {
            display: block;
        }

        .restore-file-copy strong {
            color: #111827;
            font-size: 13px;
            overflow-wrap: anywhere;
        }

        .restore-file-copy span {
            color: #6b7280;
            font-size: 11px;
            margin-top: 3px;
            line-height: 1.45;
        }

        .restore-warning,
        .validation-panel {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            border-radius: 8px;
            padding: 11px 12px;
            font-size: 12px;
            line-height: 1.45;
        }

        .restore-warning {
            background: rgba(243, 156, 18, 0.09);
            color: #8a5a09;
            margin-bottom: 10px;
        }

        .validation-panel {
            background: #f4f7f2;
            color: #4f5f44;
            margin-bottom: 14px;
        }

        .validation-panel.valid {
            background: rgba(28, 85, 21, 0.08);
            color: #1c5515;
        }

        .validation-panel.invalid {
            background: rgba(200, 40, 40, 0.08);
            color: #c82828;
        }

        .restore-submit {
            width: 100%;
        }

        .backup-history-header {
            display: grid;
            grid-template-columns: minmax(240px, 1fr);
            gap: 14px;
            margin-bottom: 14px;
        }

        .backup-history-filters {
            display: grid;
            grid-template-columns: minmax(220px, 1fr) minmax(130px, 0.35fr) minmax(130px, 0.35fr) auto;
            gap: 8px;
            align-items: center;
        }

        .backup-search {
            display: flex;
            align-items: center;
            gap: 8px;
            height: 38px;
            padding: 0 11px;
            background: #f4f7f4;
            border: 1px solid rgba(28, 85, 21, 0.12);
            border-radius: 8px;
        }

        .backup-search i {
            color: #8a8f98;
            font-size: 12px;
        }

        .backup-search input {
            width: 100%;
            border: 0;
            outline: 0;
            background: transparent;
            color: #1f2937;
            font-size: 12px;
        }

        .backup-search input::placeholder {
            color: #9ca3af;
        }

        .backup-history-filters select,
        .backup-filter-reset {
            height: 38px;
            border-radius: 8px;
            border: 1px solid rgba(28, 85, 21, 0.12);
            background: #f4f7f4;
            color: #374151;
            font-size: 12px;
            outline: none;
        }

        .backup-history-filters select {
            padding: 0 10px;
        }

        .backup-filter-reset {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            padding: 0 12px;
            color: #1c5515;
            cursor: pointer;
            font-weight: 700;
        }

        .backup-history-table {
            max-height: 560px;
        }

        .backup-history-table table {
            min-width: 840px;
        }

        .backup-file-name {
            color: #1f2937;
            font-weight: 600;
            overflow-wrap: anywhere;
        }

        .backup-badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 9px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
        }

        .backup-badge.valid {
            color: #1c5515;
            background: rgba(28, 85, 21, 0.08);
        }

        .backup-badge.invalid {
            color: #c82828;
            background: rgba(200, 40, 40, 0.08);
        }

        .backup-row-actions {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .history-action {
            width: 32px;
            height: 32px;
            border: 1px solid transparent;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background 0.18s ease, transform 0.18s ease;
        }

        .history-action:hover:not(:disabled) {
            transform: translateY(-1px);
        }

        .history-action.download {
            color: #1c5515;
            background: rgba(28, 85, 21, 0.08);
        }

        .history-action.restore {
            color: #8a5a09;
            background: rgba(243, 156, 18, 0.1);
        }

        .history-action.delete {
            color: #b91c1c;
            background: rgba(200, 40, 40, 0.08);
        }

        .backup-empty-cell {
            color: #8a8f98;
            text-align: center;
            padding: 28px 16px;
        }

        .backup-empty-state {
            min-height: 220px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 8px;
            color: #8a8f98;
            text-align: center;
        }

        .backup-empty-state i {
            width: 42px;
            height: 42px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            color: #1c5515;
            background: rgba(28, 85, 21, 0.08);
            font-size: 18px;
        }

        .backup-empty-state strong {
            color: #111827;
            font-size: 14px;
        }

        button:disabled {
            cursor: not-allowed;
            opacity: 0.58;
            transform: none;
        }

        body.dark-mode .backup-card-header h3,
        body.dark-mode .backup-file-name,
        body.dark-mode .restore-file-copy strong,
        body.dark-mode .backup-empty-state strong {
            color: #eef2f7;
        }

        body.dark-mode .backup-card-header p,
        body.dark-mode .restore-file-copy span,
        body.dark-mode .backup-empty-cell,
        body.dark-mode .backup-empty-state {
            color: #b4becd;
        }

        body.dark-mode .backup-icon,
        body.dark-mode .restore-file-icon,
        body.dark-mode .backup-empty-state i {
            color: #8fd48d;
            background: rgba(143, 212, 141, 0.12);
        }

        body.dark-mode .restore-file-card,
        body.dark-mode .backup-search,
        body.dark-mode .backup-history-filters select,
        body.dark-mode .backup-filter-reset,
        body.dark-mode .validation-panel {
            background: #111827;
            border-color: #334155;
            color: #d7dee8;
        }

        body.dark-mode .backup-search input {
            color: #eef2f7;
        }

        body.dark-mode .backup-search input::placeholder {
            color: #7f8b9c;
        }

        body.dark-mode .restore-warning {
            background: rgba(251, 191, 36, 0.1);
            color: #facc6b;
        }

        body.dark-mode .backup-command.secondary,
        body.dark-mode .backup-filter-reset {
            color: #8fd48d;
            background: rgba(143, 212, 141, 0.1);
            border-color: rgba(143, 212, 141, 0.18);
        }

        body.dark-mode .backup-command.danger {
            color: #fca5a5;
            background: rgba(252, 165, 165, 0.1);
            border-color: rgba(252, 165, 165, 0.2);
        }

        body.dark-mode .backup-badge.valid {
            color: #8fd48d;
            background: rgba(143, 212, 141, 0.12);
        }

        body.dark-mode .backup-badge.invalid {
            color: #fca5a5;
            background: rgba(252, 165, 165, 0.12);
        }

        body.dark-mode .history-action.download {
            color: #8fd48d;
            background: rgba(143, 212, 141, 0.12);
        }

        body.dark-mode .history-action.restore {
            color: #facc6b;
            background: rgba(251, 191, 36, 0.1);
        }

        body.dark-mode .history-action.delete {
            color: #fca5a5;
            background: rgba(252, 165, 165, 0.1);
        }

        @media (max-width: 1180px) {
            .backup-workspace {
                grid-template-columns: 1fr;
            }

            .backup-action-card {
                position: static;
            }
        }

        @media (max-width: 760px) {
            .backup-action-card,
            .backup-history-card {
                padding: 16px;
            }

            .backup-action-stack,
            .backup-history-filters {
                grid-template-columns: 1fr;
            }

            .backup-command,
            .backup-filter-reset {
                width: 100%;
            }
        }
    </style>
</body>
</html>
