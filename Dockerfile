FROM python:3.13.3-alpine3.21 as base
COPY --from=ghcr.io/astral-sh/uv:latest /uv /uvx /bin/

ENV UV_LINK_MODE=copy
WORKDIR /app

RUN --mount=type=cache,target=/root/.cache/uv \
    --mount=type=bind,source=./pyproject.toml,target=/app/pyproject.toml \
    --mount=type=bind,source=./uv.lock,target=/app/uv.lock \
    uv sync --frozen --no-dev --no-install-project --compile-bytecode

COPY pyproject.toml /app/pyproject.toml
COPY uv.lock /app/uv.lock
COPY backend /app/backend

CMD ["uv", "run", "--no-sync", "backend.app"]