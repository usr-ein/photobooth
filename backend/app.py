import datetime
import re
import uuid
from typing import Union

import boto3
import uvicorn
from fastapi import FastAPI, HTTPException
from fastapi.responses import JSONResponse
from mangum import Mangum

from .settings import Settings

app = FastAPI()
handler = Mangum(app)


settings = Settings()  # type: ignore


@app.get("/")
def read_root():
    return {"Welcome to": "My first FastAPI depolyment using Docker image"}


valid_booth_name_regex = r"^[a-zA-Z0-9-]{1,200}$"


@app.get("/gen-presigned/{booth_name}/{ext}")
def generate_presigned_url(booth_name: str, ext: str):
    # generate a presigned URL to upload a jpeg or png file
    # to an S3 bucket under a specific booth name
    if not re.match(valid_booth_name_regex, booth_name):
        raise HTTPException(status_code=400, detail="Invalid booth name.")

    if ext not in ["jpg", "png"]:
        raise HTTPException(status_code=400, detail="Invalid file extension.")

    if settings.environment != "local":
        s3_client = boto3.client("s3")
    else:
        s3_client = boto3.client(
            "s3",
            endpoint_url="http://localhost:9000",
            aws_access_key_id="minioadmin",
            aws_secret_access_key="minioadmin",
        )

    # 2025-04-19T22-03-13
    timestamp_filename = datetime.datetime.now().strftime("%Y-%m-%dT%H-%M-%S")
    # e.g.
    # 2025-04-19T22-03-13-ead453.jpg
    filename = f"{timestamp_filename}-{str(uuid.uuid4())[:5]}.{ext}"

    presigned_url = s3_client.generate_presigned_url(
        "put_object",
        Params={
            "Bucket": settings.bucket_name,
            "Key": f"{booth_name}/{filename}",
            "ContentType": "image/jpeg" if ext == "jpg" else "image/png",
        },
        ExpiresIn=3600,
    )
    return JSONResponse({"presigned_url": presigned_url})


@app.get("/{text}")
def read_item(text: str):
    return JSONResponse({"result": text})


@app.get("/items/{item_id}")
def read_item_2(item_id: int, q: Union[str, None] = None):
    return JSONResponse({"item_id": item_id, "q": q})


if __name__ == "__main__":
    uvicorn.run(app, host="0.0.0.0", port=8080)
