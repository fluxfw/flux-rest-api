ARG FLUX_AUTOLOAD_API_IMAGE=docker-registry.fluxpublisher.ch/flux-autoload-api
ARG FLUX_NAMESPACE_CHANGER_IMAGE=docker-registry.fluxpublisher.ch/flux-namespace-changer

FROM $FLUX_AUTOLOAD_API_IMAGE:v2022-06-22-1 AS flux_autoload_api

FROM $FLUX_NAMESPACE_CHANGER_IMAGE:v2022-06-23-1 AS build_namespaces

COPY --from=flux_autoload_api /flux-autoload-api /code/flux-autoload-api
RUN change-namespace /code/flux-autoload-api FluxAutoloadApi FluxRestApi\\Libs\\FluxAutoloadApi

FROM alpine:latest AS build

COPY --from=build_namespaces /code/flux-autoload-api /build/flux-rest-api/libs/flux-autoload-api
COPY . /build/flux-rest-api

RUN (cd /build && tar -czf flux-rest-api.tar.gz flux-rest-api)

FROM scratch

LABEL org.opencontainers.image.source="https://github.com/flux-eco/flux-rest-api"
LABEL maintainer="fluxlabs <support@fluxlabs.ch> (https://fluxlabs.ch)"

COPY --from=build /build /

ARG COMMIT_SHA
LABEL org.opencontainers.image.revision="$COMMIT_SHA"
