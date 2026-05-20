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
        <section class="page-content">
            <div class="backup-status-grid">
                <div class="backup-status-card">
                    <i class="fa-solid fa-database"></i>
                    <div>
                        <span>Total backups</span>
                        <strong id="total-backups">0</strong>
                    </div>
                </div>
                <div class="backup-status-card">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                    <div>
                        <span>Latest backup</span>
                        <strong id="latest-backup">None yet</strong>
                    </div>
                </div>
                <div class="backup-status-card">
                    <i class="fa-solid fa-shield-halved"></i>
                    <div>
                        <span>Restore protection</span>
                        <strong>Safety backup enabled</strong>
                    </div>
                </div>
            </div>

            <div class="charts-row backup-layout">
                <div class="chart-card backup-panel">
                    <div class="card-header">
                        <i class="fa-solid fa-shield-halved"></i>
                        <div>
                            <h3>Backup and recovery</h3>
                            <p>Create full database backups and restore verified SQL files</p>
                        </div>
                    </div>

                    <div class="backup-actions">
                        <button type="button" class="btn btn-in backup-primary" id="backup-now">
                            <i class="fa-solid fa-download"></i>
                            Backup now
                        </button>
                        <button type="button" class="btn btn-out" id="choose-restore">
                            <i class="fa-solid fa-file-arrow-up"></i>
                            Upload restore file
                        </button>
                    </div>

                    <div class="restore-dropzone" id="restore-dropzone">
                        <i class="fa-solid fa-file-shield"></i>
                        <div>
                            <strong id="restore-file-name">No restore file selected</strong>
                            <span id="restore-file-help">Only valid .sql database backup files are accepted.</span>
                        </div>
                    </div>

                    <div class="validation-panel" id="validation-panel">
                        <i class="fa-solid fa-circle-info"></i>
                        <span>Select a backup file to validate it before recovery.</span>
                    </div>

                    <button type="button" class="btn btn-out restore-submit" id="restore-uploaded" disabled>
                        <i class="fa-solid fa-rotate-left"></i>
                        Restore selected file
                    </button>

                    <input type="file" id="restore-file" accept=".sql" style="display: none;">
                </div>

                <div class="chart-card backup-panel">
                    <div class="card-header">
                        <i class="fa-solid fa-gears"></i>
                        <div>
                            <h3>Backup settings</h3>
                            <p>Local full backups for defense-ready recovery demonstrations</p>
                        </div>
                    </div>

                    <form id="backup-settings-form">
                        <div class="form-grid">
                            <div class="form-field">
                                <label for="frequency-select">Backup frequency</label>
                                <div class="input-wrap">
                                    <select id="frequency-select" name="frequency">
                                        <option value="daily" selected>Daily</option>
                                        <option value="weekly">Weekly</option>
                                    </select>
                                </div>
                                <p class="field-hint">Use cron_backup.php for scheduled backups.</p>
                            </div>

                            <div class="form-field">
                                <label for="backup-location">Backup location</label>
                                <div class="input-wrap">
                                    <select id="backup-location" name="backup_location">
                                        <option value="local">Local /backups folder</option>
                                    </select>
                                </div>
                                <p class="field-hint">Manual backups are stored in the project backups folder.</p>
                            </div>

                            <!-- <div class="form-field">
                                <label>Automatic backups</label>
                                <label class="field-toggle">
                                    <input type="checkbox" id="auto-backup" name="auto_backup" checked>
                                    Enabled for scheduled jobs
                                </label>
                            </div> -->

                            <div class="form-field weekly-day" style="display: none;">
                                <label for="weekly-day-select">Weekly backup day</label>
                                <div class="input-wrap">
                                    <select id="weekly-day-select" name="weekly_day" disabled>
                                        <option value="monday">Monday</option>
                                        <option value="tuesday">Tuesday</option>
                                        <option value="wednesday">Wednesday</option>
                                        <option value="thursday">Thursday</option>
                                        <option value="friday">Friday</option>
                                        <option value="saturday">Saturday</option>
                                        <option value="sunday">Sunday</option>
                                    </select>
                                </div>
                                <p class="field-hint">Used only when weekly backups are enabled.</p>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="chart-card backup-history-card">
                <div class="card-header">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                    <div>
                        <h3>Backup history</h3>
                        <p>Download or restore previous full database backup files</p>
                    </div>
                </div>
                <div class="menu-table">
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
                                <td colspan="6" class="empty-state">Loading backup history...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </main>

    <script>
        const frequencySelect = document.getElementById('frequency-select');
        const weeklyDaySelect = document.getElementById('weekly-day-select');
        const weeklyDayWrapper = document.querySelector('.weekly-day');
        const backupButton = document.getElementById('backup-now');
        const chooseRestoreButton = document.getElementById('choose-restore');
        const restoreUploadedButton = document.getElementById('restore-uploaded');
        const restoreFileInput = document.getElementById('restore-file');
        const restoreFileName = document.getElementById('restore-file-name');
        const restoreFileHelp = document.getElementById('restore-file-help');
        const validationPanel = document.getElementById('validation-panel');

        let selectedRestoreFile = null;

        const updateWeeklyDayState = () => {
            const isWeekly = frequencySelect.value === 'weekly';
            weeklyDaySelect.disabled = !isWeekly;
            weeklyDayWrapper.style.display = isWeekly ? 'block' : 'none';
        };

        const setLoading = (button, loading, label) => {
            button.disabled = loading;
            button.dataset.originalText = button.dataset.originalText || button.innerHTML;
            button.innerHTML = loading
                ? `<i class="fa-solid fa-spinner fa-spin"></i> ${label}`
                : button.dataset.originalText;
        };

        const showValidation = (type, message) => {
            validationPanel.className = `validation-panel ${type}`;
            validationPanel.innerHTML = `<i class="fa-solid ${type === 'valid' ? 'fa-circle-check' : type === 'invalid' ? 'fa-triangle-exclamation' : 'fa-circle-info'}"></i><span>${message}</span>`;
        };

        const escapeHtml = (value) => String(value)
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');

        frequencySelect.addEventListener('change', updateWeeklyDayState);
        updateWeeklyDayState();

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
                    showValidation('invalid', data.message || 'Backup file failed validation.');
                }
            } catch (error) {
                showValidation('invalid', 'The selected file could not be validated.');
            }
        });

        restoreUploadedButton.addEventListener('click', async () => {
            if (!selectedRestoreFile) {
                showValidation('invalid', 'Please choose a backup file first.');
                return;
            }

            const result = await Swal.fire({
                title: 'Restore database?',
                html: 'This will replace the current database state. A safety backup will be created automatically before restoring.',
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
            tbody.innerHTML = '<tr><td colspan="6" class="empty-state">Loading backup history...</td></tr>';

            try {
                const response = await fetch('../../controllers/backup.php?action=list');
                const result = await response.json();

                if (result.status !== 'success') {
                    throw new Error(result.message || 'Unable to load backups.');
                }

                const backups = result.data || [];
                document.getElementById('total-backups').textContent = backups.length;
                document.getElementById('latest-backup').textContent = backups[0] ? backups[0].date : 'None yet';

                if (backups.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="6" class="empty-state">No backups have been created yet.</td></tr>';
                    return;
                }

                tbody.innerHTML = backups.map((backup) => `
                    <tr>
                        <td>${escapeHtml(backup.filename)}</td>
                        <td>${escapeHtml(backup.date)}</td>
                        <td>${escapeHtml(backup.type)}</td>
                        <td>${escapeHtml(backup.readable_size)}</td>
                        <td><span class="backup-badge ${backup.valid ? 'valid' : 'invalid'}">${backup.valid ? 'Valid' : 'Needs review'}</span></td>
                        <td>
                            <div class="actions">
                                <button onclick="downloadBackup('${escapeHtml(backup.filename)}')" class="btn btn-in" title="Download backup">
                                    <i class="fa-solid fa-download"></i>
                                </button>
                                <button onclick="restoreSpecific('${escapeHtml(backup.filename)}')" class="btn btn-out" title="Restore backup" ${backup.valid ? '' : 'disabled'}>
                                    <i class="fa-solid fa-rotate-left"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `).join('');
            } catch (error) {
                tbody.innerHTML = '<tr><td colspan="6" class="empty-state">Backup history could not be loaded.</td></tr>';
            }
        }

        function downloadBackup(filename) {
            window.location.href = `../../controllers/backup.php?action=download&file=${encodeURIComponent(filename)}`;
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
        .backup-status-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
            margin-bottom: 18px;
        }

        .backup-status-card {
            display: flex;
            align-items: center;
            gap: 12px;
            background: #fff;
            border: 1px solid rgba(28, 85, 21, 0.08);
            border-left: 3px solid #32702b;
            border-radius: 8px;
            padding: 14px 16px;
            min-width: 0;
        }

        .backup-status-card i {
            color: #1c5515;
            background: rgba(28, 85, 21, 0.08);
            width: 32px;
            height: 32px;
            border-radius: 7px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .backup-status-card span {
            display: block;
            color: #8a8f98;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.6px;
            text-transform: uppercase;
        }

        .backup-status-card strong {
            display: block;
            color: #1c5515;
            font-size: 14px;
            margin-top: 4px;
            overflow-wrap: anywhere;
        }

        .backup-layout {
            align-items: stretch;
        }

        .backup-panel {
            min-width: 0;
        }

        .card-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 16px;
        }

        .card-header > i {
            color: #1c5515;
            background: rgba(28, 85, 21, 0.08);
            width: 32px;
            height: 32px;
            border-radius: 7px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .card-header h3 {
            color: #164e1a;
            margin: 0 0 3px;
        }

        .card-header p {
            margin: 0;
        }

        .backup-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 14px;
        }

        .backup-primary {
            background: #1c5515;
            color: #fff;
        }

        .backup-primary i {
            color: #fff;
        }

        .restore-dropzone {
            display: flex;
            align-items: center;
            gap: 12px;
            border: 1px dashed rgba(28, 85, 21, 0.28);
            border-radius: 8px;
            padding: 14px;
            background: #f7faf5;
            margin-bottom: 12px;
        }

        .restore-dropzone i {
            color: #1c5515;
            font-size: 20px;
        }

        .restore-dropzone strong,
        .restore-dropzone span {
            display: block;
        }

        .restore-dropzone strong {
            color: #1f2937;
            font-size: 13px;
            overflow-wrap: anywhere;
        }

        .restore-dropzone span {
            color: #6b7280;
            font-size: 11px;
            margin-top: 3px;
        }

        .validation-panel {
            display: flex;
            align-items: center;
            gap: 8px;
            border-radius: 8px;
            padding: 10px 12px;
            background: #f4f7f2;
            color: #4f5f44;
            font-size: 12px;
            margin-bottom: 12px;
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
            justify-content: center;
        }

        .field-toggle {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #4f5f44;
            font-size: 12px;
            text-transform: none;
            letter-spacing: 0;
        }

        .form-field select,
        .input-wrap select {
            width: 100%;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            padding: 10px 12px;
            border-radius: 8px;
            border: 1px solid rgba(28, 85, 21, 0.18);
            background: #f4f7f2;
            color: #1f2937;
            font-size: 13px;
            outline: none;
        }

        .backup-history-card {
            margin-top: 18px;
        }

        .backup-history-card .menu-table table {
            min-width: 840px;
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

        .empty-state {
            color: #8a8f98;
            text-align: center;
        }

        button:disabled {
            cursor: not-allowed;
            opacity: 0.58;
            transform: none;
            box-shadow: none;
        }

        body.dark-mode .backup-status-card,
        body.dark-mode .restore-dropzone,
        body.dark-mode .validation-panel {
            background: #1a2235;
            border-color: #2a3a4a;
        }

        body.dark-mode .backup-status-card strong,
        body.dark-mode .restore-dropzone strong {
            color: #d1d5db;
        }

        body.dark-mode .restore-dropzone span {
            color: #8a9bb0;
        }

        body.dark-mode .validation-panel {
            color: #8a9bb0;
        }

        @media (max-width: 900px) {
            .backup-status-grid {
                grid-template-columns: 1fr;
            }

            .backup-layout {
                flex-direction: column;
            }
        }
    </style>
</body>
</html>
