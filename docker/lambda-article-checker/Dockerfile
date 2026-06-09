FROM public.ecr.aws/lambda/python:3.12

ENV PLAYWRIGHT_BROWSERS_PATH=/ms-playwright
ENV PLAYWRIGHT_SKIP_BROWSER_GC=1

RUN dnf install -y \
    atk \
    cups-libs \
    gtk3 \
    libXcomposite \
    libXcursor \
    libXdamage \
    libXext \
    libXi \
    libXrandr \
    libXScrnSaver \
    libXtst \
    pango \
    alsa-lib \
    nss \
    xorg-x11-fonts-Type1 \
    xorg-x11-fonts-misc \
    libdrm \
    mesa-libgbm \
    libxkbcommon \
    at-spi2-atk \
    at-spi2-core \
    && dnf clean all

RUN pip install --no-cache-dir playwright==1.44.0

RUN mkdir -p /ms-playwright \
    && python -m playwright install chromium \
    && chmod -R 755 /ms-playwright

COPY app.py ${LAMBDA_TASK_ROOT}/app.py

CMD ["app.lambda_handler"]
