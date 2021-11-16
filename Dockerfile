ARG REST_BASE_API_IMAGE
FROM $REST_BASE_API_IMAGE AS rest_base_api

FROM alpine:latest AS build

COPY --from=rest_base_api /flux-rest-base-api /flux-rest-api/libs/flux-rest-base-api
COPY . /flux-rest-api

FROM scratch

LABEL org.opencontainers.image.source="https://github.com/fluxapps/flux-rest-api"
LABEL maintainer="fluxlabs <support@fluxlabs.ch> (https://fluxlabs.ch)"

COPY --from=build /flux-rest-api /flux-rest-api
