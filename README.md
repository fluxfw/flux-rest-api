# flux-rest-api

Rest Api

## Installation

### Non-Composer

```dockerfile
COPY --from=docker-registry.fluxpublisher.ch/flux-rest-api:%tag% /flux-rest-api /%path%/libs/flux-rest-api
```

or

```dockerfile
RUN (mkdir -p /%path%/libs/flux-rest-api && cd /%path%/libs/flux-rest-api && wget -O - https://github.com/flux-eco/flux-rest-api/releases/download/%tag%/flux-rest-api-%tag%-build.tar.gz | tar -xz --strip-components=1)
```

or

Download https://github.com/flux-eco/flux-rest-api/releases/download/%tag%/flux-rest-api-%tag%-build.tar.gz and extract it to `/%path%/libs/flux-rest-api`

#### Usage

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
                    "url": "https://github.com/flux-eco/flux-rest-api/releases/download/%tag%/flux-rest-api-%tag%-build.tar.gz",
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
