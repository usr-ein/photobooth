from typing import Annotated

from pydantic import StringConstraints
from pydantic_settings import BaseSettings, SettingsConfigDict


class Settings(BaseSettings):
    model_config = SettingsConfigDict(
        env_file=".env", env_file_encoding="utf-8", env_prefix="PHOTOBOOTH__"
    )

    environment: Annotated[str, StringConstraints(pattern="^(local|remote)$")]
    bucket_name: str
