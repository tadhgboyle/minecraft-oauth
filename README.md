# Microsoft Minecraft Profile OAuth

```php
require 'vendor/autoload.php';

$client_id = '<Azure OAuth Client ID>';
$client_secret = '<Azure OAuth Client Secret>';
$redirect_uri = '<URL to this file>';

$profile = (new \Aberdeener\MicrosoftMinecraftOauthProfile\MicrosoftMinecraftLinker())->fetchMinecraftProfile(
    $client_id,
    $client_secret,
    $_GET['code'],
    $redirect_uri
);

echo 'Minecraft UUID: ' . $profile->uuid();
echo 'Minecraft Username: ' . $profile->username();
echo 'Minecraft Skin URL: ' . $profile->skins()[0]->url();
echo 'Minecraft Cape URL: ' . $profile->capes()[0]->url();
```
