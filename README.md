# Minecraft OAuth

Provides easy layer to get a Minecraft profile (UUID, username, skins, capes) from a Microsoft Live OAuth session.

```php
require 'vendor/autoload.php';

$client_id = '<Azure OAuth Client ID>';
$client_secret = '<Azure OAuth Client Secret>';
$redirect_uri = '<URL to this file>';

try {
    $profile = (new \Aberdeener\MinecraftOauth\MinecraftOauth)->fetchProfile(
        $client_id,
        $client_secret,
        $_GET['code'],
        $redirect_uri,
    );
} catch (\Aberdeener\MinecraftOauth\Exceptions\MinecraftOauthException $e) {
    echo $e->getMessage();
}

echo 'Minecraft UUID: ' . $profile->uuid();
echo 'Minecraft Username: ' . $profile->username();
echo 'Minecraft Skin URL: ' . $profile->skins()[0]->url();
echo 'Minecraft Cape URL: ' . $profile->capes()[0]->url();
```
