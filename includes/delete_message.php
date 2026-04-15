<?php if (!empty($delete_success)): ?>
    <div class="success-message"><?= htmlspecialchars($delete_success) ?></div>
<?php endif; ?>
<?php if (!empty($delete_error)): ?>
    <div class="error-message"><?= htmlspecialchars($delete_error) ?></div>
<?php endif; ?>