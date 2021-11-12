ARG REST_BASE_API_IMAGE
FROM $REST_BASE_API_IMAGE AS rest_base_api

FROM alpine:latest AS build

COPY --from=rest_base_api /FluxRestBaseApi /FluxRestApi/libs/FluxRestBaseApi
COPY . /FluxRestApi

FROM scratch

LABEL org.opencontainers.image.source="https://github.com/fluxapps/FluxRestApi"
LABEL maintainer="fluxlabs <support@fluxlabs.ch> (https://fluxlabs.ch)"

COPY --from=build /FluxRestApi /FluxRestApi
