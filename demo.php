<!DOCTYPE html>
<html>
<head>
    <title>pCloud upload</title>
</head>
<body>
    <?php
        // It's important to set this
        ini_set('max_execution_time', 300);

        require_once 'pCloud.php';
    
        $pcloud = new pCloud();

        // Replace with yours
        $pcloud->login('username', 'password');

        $pcloud->upload('filename', 'folderId', function ($data) use ($pcloud) {

            $pcloud->log('Done.');

            $link = $pcloud->getfilepublink($data->fileid);
            $pcloud->log($link);
        });
    ?>
</body>
</html>