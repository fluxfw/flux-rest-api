ARG FLUX_AUTOLOAD_API_IMAGE=docker-registry.fluxpublisher.ch/flux-autoload/api
ARG FLUX_LEGACY_ENUM_IMAGE=docker-registry.fluxpublisher.ch/flux-enum/legacy
ARG FLUX_NAMESPACE_CHANGER_IMAGE=docker-registry.fluxpublisher.ch/flux-namespace-changer

FROM $FLUX_AUTOLOAD_API_IMAGE:latest AS flux_autoload_api
FROM $FLUX_LEGACY_ENUM_IMAGE:latest AS flux_legacy_enum

FROM $FLUX_NAMESPACE_CHANGER_IMAGE:latest AS build_namespaces

COPY --from=flux_autoload_api /flux-autoload-api /code/flux-autoload-api
RUN change-namespace /code/flux-autoload-api FluxAutoloadApi FluxRestApi\\Libs\\FluxAutoloadApi

COPY --from=flux_legacy_enum /flux-legacy-enum /code/flux-legacy-enum
RUN change-namespace /code/flux-legacy-enum FluxLegacyEnum FluxRestApi\\Libs\\FluxLegacyEnum

FROM alpine:latest AS build

COPY --from=build_namespaces /code/flux-autoload-api /flux-rest-api/libs/flux-autoload-api
COPY --from=build_namespaces /code/flux-legacy-enum /flux-rest-api/libs/flux-legacy-enum
COPY . /flux-rest-api

FROM scratch

LABEL org.opencontainers.image.source="https://github.com/flux-eco/flux-rest-api"
LABEL maintainer="fluxlabs <support@fluxlabs.ch> (https://fluxlabs.ch)"

COPY --from=build /flux-rest-api /flux-rest-api

ARG COMMIT_SHA
LABEL org.opencontainers.image.revision="$COMMIT_SHA"
