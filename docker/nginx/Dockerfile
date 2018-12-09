FROM nginx:mainline

ARG UID

RUN set -ex \
 && addgroup --gid $UID --system app \
 && adduser --uid $UID --system --disabled-login --disabled-password --gid $UID app