# flux-rest-api

Rest Api

## Installation

### Native

#### Download

```dockerfile
RUN (mkdir -p /%path%/libs/flux-rest-api && cd /%path%/libs/flux-rest-api && wget -O - https://github.com/fluxfw/flux-rest-api/releases/download/%tag%/flux-rest-api-%tag%-build.tar.gz | tar -xz --strip-components=1)
```

or

Download https://github.com/fluxfw/flux-rest-api/releases/download/%tag%/flux-rest-api-%tag%-build.tar.gz and extract it to `/%path%/libs/flux-rest-api`

#### Load

```php
require_once __DIR__ . "/%path%/libs/flux-rest-api/autoload.php";
```

### Composer

```json
{
    "repositories": [
        {
            "type": "package",
            "package": {
                "name": "flux/flux-rest-api",
                "version": "%tag%",
                "dist": {
                    "url": "https://github.com/fluxfw/flux-rest-api/releases/download/%tag%/flux-rest-api-%tag%-build.tar.gz",
                    "type": "tar"
                },
                "autoload": {
                    "files": [
                        "autoload.php"
                    ]
                }
            }
        }
    ],
    "require": {
        "flux/flux-rest-api": "*"
    }
}
```

## Example

[examples](examples)
