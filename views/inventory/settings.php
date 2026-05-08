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
            <div class="charts-row">
                <div class="chart-card">
                    <div class="card-header">
                        <i class="fa-solid fa-shield-halved"></i>
                        <div>
                            <h3>Backup settings</h3>
                            <p>Configure how and when your data is backed up</p>
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
                                <p class="field-hint">Select how often backups should be created.</p>
                            </div>

                            <div class="form-field">
                                <label for="backup-location">Backup location</label>
                                <div class="input-wrap">
                                    <select id="backup-location" name="backup_location">
                                        <option value="local">Local storage</option>
                                        <option value="cloud" disabled>Cloud sync (coming soon)</option>
                                    </select>
                                </div>
                                <p class="field-hint">Local backups are stored on the server.</p>
                            </div>

                            <div class="form-field">
                                <label>Automatic backups</label>
                                <label class="field-hint" style="display:flex; align-items:center; gap:8px;">
                                    <input type="checkbox" id="auto-backup" name="auto_backup" checked>
                                    Enable automatic backups
                                </label>
                            </div>

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

                        <div class="form-actions" style="margin-top: 18px; gap: 10px;">
                            <button type="button" class="btn btn-in" onclick="backupNow()">
                                <i class="fa-solid fa-download"></i>
                                Backup now
                            </button>
                            <button type="button" class="btn btn-out" onclick="restoreBackup()">
                                <i class="fa-solid fa-upload"></i>
                                Restore backup
                            </button>
                        </div>
                    </form>
                </div>

                <div class="chart-card">
                    <div class="card-header">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                        <div>
                            <h3>Backup history</h3>
                            <p>View and restore previous backup files</p>
                        </div>
                    </div>
                    <div class="menu-table">
                        <table>
                            <thead>
                                <tr>
                                    <th>File name</th>
                                    <th>Date created</th>
                                    <th>Type</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="backup-list">
                                <!-- Backup files will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Hidden file input for restore -->
    <input type="file" id="restore-file" accept=".sql" style="display: none;">

    <script>
        // Toggle weekly day selection
        const frequencySelect = document.getElementById('frequency-select');
        const weeklyDaySelect = document.getElementById('weekly-day-select');
        const weeklyDayWrapper = document.querySelector('.weekly-day');

        const updateWeeklyDayState = () => {
            const isWeekly = frequencySelect.value === 'weekly';
            weeklyDaySelect.disabled = !isWeekly;
            weeklyDayWrapper.style.display = isWeekly ? 'block' : 'none';
        };

        updateWeeklyDayState();
        frequencySelect.addEventListener('change', updateWeeklyDayState);

        // Load backup history
        loadBackupHistory();

        function loadBackupHistory() {
            fetch('controllers/backup.php?action=list')
                .then(response => response.json())
                .then(data => {
                    const tbody = document.getElementById('backup-list');
                    tbody.innerHTML = '';
                    data.forEach(backup => {
                        const row = `
                            <tr>
                                <td>${backup.filename}</td>
                                <td>${backup.date}</td>
                                <td>${backup.type}</td>
                                <td>
                                    <button onclick="downloadBackup('${backup.filename}')" class="btn btn-in">
                                        <i class="fa-solid fa-download"></i> Download
                                    </button>
                                    <button onclick="restoreSpecific('${backup.filename}')" class="btn btn-out">
                                        <i class="fa-solid fa-upload"></i> Restore
                                    </button>
                                </td>
                            </tr>
                        `;
                        tbody.innerHTML += row;
                    });
                });
        }

        function backupNow() {
            if (confirm('Are you sure you want to create a backup now?')) {
                fetch('controllers/backup.php?action=backup', { method: 'POST' })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Success', 'Backup created successfully!', 'success');
                            loadBackupHistory();
                        } else {
                            Swal.fire('Error', data.message, 'error');
                        }
                    });
            }
        }

        function restoreBackup() {
            document.getElementById('restore-file').click();
        }

        document.getElementById('restore-file').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                if (confirm('Are you sure you want to restore from this backup? This will overwrite current data.')) {
                    const formData = new FormData();
                    formData.append('backup_file', file);
                    formData.append('action', 'restore');

                    fetch('controllers/backup.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Success', 'Backup restored successfully!', 'success');
                        } else {
                            Swal.fire('Error', data.message, 'error');
                        }
                    });
                }
            }
        });

        function downloadBackup(filename) {
            window.location.href = `controllers/backup.php?action=download&file=${filename}`;
        }

        function restoreSpecific(filename) {
            if (confirm('Are you sure you want to restore from this backup? This will overwrite current data.')) {
                const formData = new FormData();
                formData.append('filename', filename);
                formData.append('action', 'restore_specific');

                fetch('controllers/backup.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('Success', 'Backup restored successfully!', 'success');
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                });
            }
        }
    </script>

    <style>
        .form-field select,
        .input-wrap select {
            width: 100%;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            padding: 10px 12px;
            border-radius: 10px;
            border: 1px solid rgba(28, 85, 21, 0.18);
            background: #f4f7f2;
            color: #1f2937;
            font-size: 13px;
            outline: none;
            transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
        }

        .form-field select:focus,
        .input-wrap select:focus {
            border-color: #1c5515;
            box-shadow: 0 0 0 3px rgba(28, 85, 21, 0.08);
            background: #ffffff;
        }

        .card-header i {
            color: #1c5515;
        }

        .card-header h3 {
            color: #164e1a;
        }

        .field-hint {
            color: #4f5f44;
        }

        .form-field select option,
        .input-wrap select option {
            background: #f4f7f2;
            color: #1f2937;
        }

        .form-field select option:hover,
        .input-wrap select option:hover,
        .form-field select option:checked,
        .input-wrap select option:checked {
            background: #d3ebd4;
            color: #1c5515;
        }

        .form-field select:focus option,
        .input-wrap select:focus option {
            background: #eef4ec;
        }

        select {
            accent-color: #1c5515;
        }

        .chart-card {
            background: #fbfcf8;
            border-color: rgba(28, 85, 21, 0.08);
        }

        .menu-table {
            background: #fbfcf8;
            border: 1px solid rgba(28, 85, 21, 0.08);
        }

        .menu-table thead {
            background: #edf5eb;
        }

        .menu-table th {
            color: #1c5515;
        }

        body.dark-mode .form-field select,
        body.dark-mode .input-wrap select {
            background: #1f2937;
            border-color: rgba(255, 255, 255, 0.12);
            color: #d1d5db;
        }

        body.dark-mode .form-field select:focus,
        body.dark-mode .input-wrap select:focus {
            background: #111827;
            box-shadow: 0 0 0 3px rgba(58, 125, 58, 0.14);
        }

        body.dark-mode .form-field select option,
        body.dark-mode .input-wrap select option {
            background: #111827;
            color: #d1d5db;
        }

        .charts-row {
            align-items: stretch;
        }

        .charts-row > .chart-card {
            min-width: 0;
        }

        .charts-row > .chart-card .menu-table table {
            min-width: 0;
        }

        .form-actions {
            justify-content: flex-start;
        }

        .btn {
            padding: 8px 14px;
            font-size: 12px;
        }

        .btn-in i, .btn-out i {
            font-size: 12px;
        }

        .menu-table {
            min-width: 0;
        }
    </style>
</body>
</html>