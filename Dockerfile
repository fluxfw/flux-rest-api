FROM alpine:latest AS build

COPY --from=docker-registry.fluxpublisher.ch/flux-rest/base-api:latest /FluxRestBaseApi /FluxRestApi/libs/FluxRestBaseApi
COPY . /FluxRestApi

FROM scratch

LABEL org.opencontainers.image.source="https://github.com/fluxapps/FluxRestApi"
LABEL maintainer="fluxlabs <support@fluxlabs.ch> (https://fluxlabs.ch)"

COPY --from=build /FluxRestApi /FluxRestApi
