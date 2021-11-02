FROM alpine:latest AS build

RUN (mkdir -p /FluxRestApi/libs/polyfill-php80 && cd /FluxRestApi/libs/polyfill-php80 && wget -O - https://github.com/symfony/polyfill-php80/archive/main.tar.gz | tar -xz --strip-components=1)
COPY . /FluxRestApi

FROM scratch

LABEL org.opencontainers.image.source="https://github.com/fluxapps/FluxRestApi"
LABEL maintainer="fluxlabs <support@fluxlabs.ch> (https://fluxlabs.ch)"

COPY --from=build /FluxRestApi /FluxRestApi
