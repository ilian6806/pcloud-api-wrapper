# pcloud-api-wrapper
pCloud service API wrapper using HTTP interface

Usage:

```php
$pcloud = new pCloud();

$pcloud->login('username', 'password');

$pcloud->upload('filename', 'folderId', function ($data) use ($pcloud) {

    $pcloud->log('Done.');
    
    $link = $pcloud->getfilepublink($data->fileid);
    $pcloud->log($link);
});
```

Feel free to add more of the API methods [https://docs.pcloud.com/methods/](https://docs.pcloud.com/methods/).
