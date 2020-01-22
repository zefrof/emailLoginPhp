<?php
        $link = str_replace('/var/www/html', '', dirname(__DIR__) . '/js/main.js');
        $foundMin = str_replace('/var/www/html', '', dirname(__DIR__) . '/style/css/foundation.min.css');
        $found = str_replace('/var/www/html', '', dirname(__DIR__) . '/style/css/foundation.css');
        $app = str_replace('/var/www/html', '', dirname(__DIR__) . '/style/css/app.css');
?>
        
        <script type="text/javascript" src="<?php echo $link; ?>"></script>
        <link rel="stylesheet" href="<?php echo $foundMin; ?>">
        <link rel="stylesheet" href="<?php echo $found; ?>">
        <link rel="stylesheet" href="<?php echo $app; ?>">
</body>
</html>