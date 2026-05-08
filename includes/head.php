<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grocer Ease</title>
    <script>
        (function () {
            const STORAGE_KEY = 'grocer-ease-theme';
            const DARK_CLASS = 'dark-mode';
            const saved = localStorage.getItem(STORAGE_KEY);
            if (saved === 'dark') {
                document.documentElement.classList.add(DARK_CLASS);
                document.body.classList.add(DARK_CLASS);
            }
        })();
    </script>
    <link rel="stylesheet" href="<?= ASSET_URL ?>/css/home.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="icon" type="image/png" href="<?= IMAGE_URL ?>/favicon.png">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="<?= ASSET_URL ?>/js/Darkmode.js" defer></script>

</head>