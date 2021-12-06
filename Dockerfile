ARG ALPINE_IMAGE=alpine:latest
ARG FLUX_REST_BASE_API_IMAGE=docker-registry.fluxpublisher.ch/flux-rest/base-api:latest

FROM $FLUX_REST_BASE_API_IMAGE AS flux_rest_base_api

FROM $ALPINE_IMAGE AS build

COPY --from=flux_rest_base_api /flux-rest-base-api /flux-rest-api/libs/flux-rest-base-api
COPY . /flux-rest-api

FROM scratch

LABEL org.opencontainers.image.source="https://github.com/fluxapps/flux-rest-api"
LABEL maintainer="fluxlabs <support@fluxlabs.ch> (https://fluxlabs.ch)"

COPY --from=build /flux-rest-api /flux-rest-api
