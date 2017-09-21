# pcloud-api-wrapper
pCloud service API wrapper using HTTP interface

[![Code Climate](https://codeclimate.com/github/ilian6806/pcloud-api-wrapper/badges/gpa.svg)](https://codeclimate.com/github/ilian6806/pcloud-api-wrapper) [![Issue Count](https://codeclimate.com/github/ilian6806/pcloud-api-wrapper/badges/issue_count.svg)](https://codeclimate.com/github/ilian6806/pcloud-api-wrapper) ![](https://img.shields.io/gemnasium/mathiasbynens/he.svg) ![](https://img.shields.io/npm/l/express.svg)



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
