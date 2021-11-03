FROM alpine:latest AS build

COPY --from=docker-registry.fluxpublisher.ch/flux-autoload/api:latest /FluxAutoloadApi /FluxRestApi/libs/FluxAutoloadApi
COPY . /FluxRestApi

FROM scratch

LABEL org.opencontainers.image.source="https://github.com/fluxapps/FluxRestApi"
LABEL maintainer="fluxlabs <support@fluxlabs.ch> (https://fluxlabs.ch)"

COPY --from=build /FluxRestApi /FluxRestApi
