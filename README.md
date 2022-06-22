# flux-rest-api

Rest Api

## Installation

### Non-Composer

```dockerfile
COPY --from=docker-registry.fluxpublisher.ch/flux-rest/api:%tag% /flux-rest-api /%path%/libs/flux-rest-api
```

or

```dockerfile
RUN (mkdir -p /%path%/libs/flux-rest-api && cd /%path%/libs/flux-rest-api && wget -O - https://docker-registry.fluxpublisher.ch/api/get-build-archive/flux-rest/api.tar.gz?tag=%tag% | tar -xz --strip-components=1)
```

or

Download https://docker-registry.fluxpublisher.ch/api/get-build-archive/flux-rest/api.tar.gz?tag=%tag% and extract it to `/%path%/libs/flux-rest-api`

Hint: If you use `wget` without pipe use `--content-disposition` to get the correct file name

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
                "name": "flux/rest-api",
                "version": "%tag%",
                "dist": {
                    "url": "https://docker-registry.fluxpublisher.ch/api/get-build-archive/flux-rest/api.tar.gz?tag=%tag%",
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
        "flux/rest-api": "*"
    }
}
```

## Example

[examples](examples)
