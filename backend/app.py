from typing import Union

import uvicorn
from fastapi import FastAPI
from fastapi.responses import JSONResponse
from mangum import Mangum

app = FastAPI()
handler = Mangum(app)


@app.get("/")
def read_root():
    return {"Welcome to": "My first FastAPI depolyment using Docker image"}


@app.get("/{text}")
def read_item(text: str):
    return JSONResponse({"result": text})


@app.get("/items/{item_id}")
def read_item_2(item_id: int, q: Union[str, None] = None):
    return JSONResponse({"item_id": item_id, "q": q})


if __name__ == "__main__":
    uvicorn.run(app, host="0.0.0.0", port=8080)
