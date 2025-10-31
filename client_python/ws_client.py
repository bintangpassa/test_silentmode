#!/usr/bin/env python3
"""
WebSocket client for tenant upload flow, ignoring how the file was created by the client application

All configuration is hard-coded so please just run:
    python3 client_python/ws_client.py

    pip install requests websocket-client
"""

from __future__ import annotations

import json
import ssl
import sys
from pathlib import Path
from typing import Any, Dict

import requests
import websocket


# ---------------------------------
# Configuration
# ---------------------------------
API_BASE_URL = "http://localhost:8080"
TENANT_UUID = "9035b613-b0dd-4cea-8e6e-4ea3ba9715aa"  # replace with actual UUID if any
TENANT_NAME = "Tenant Demo"
FILES_DIRECTORY = "client_python/files"
SPECIFIC_UPLOAD_FILE = "client_python/files/example_files.txt"  # fallback file to upload

WS_HOST_OVERRIDE = "localhost"  # WebSocket host visible from this script
INSECURE_SSL = False

CHANNEL_NAME = f"tenant-events.{TENANT_UUID}"
REQUEST_TIMEOUT = 15


# ---------------------------------
# Helper functions
# ---------------------------------
def register_connection() -> Dict[str, Any]:
    """Call the API endpoint to register/refresh the tenant connection"""
    url = f"{API_BASE_URL.rstrip('/')}/api/websocket/connections"
    payload: Dict[str, Any] = {"uuid": TENANT_UUID}

    if TENANT_NAME:
        payload["tenant_name"] = TENANT_NAME

    response = requests.post(
        url,
        headers={
            "Accept": "application/json",
            "Content-Type": "application/json",
        },
        json=payload,
        timeout=REQUEST_TIMEOUT,
    )
    response.raise_for_status()
    return response.json()


def build_ws_url(config: Dict[str, Any]) -> str:
    """Construct the WebSocket URL based on API response and overrides"""
    reverb = config.get("reverb", {})
    host = WS_HOST_OVERRIDE or reverb.get("host", "127.0.0.1")
    port = reverb.get("port", 8080)
    path = reverb.get("path", "") or ""
    if path and not path.startswith("/"):
        path = f"/{path}"
    scheme = reverb.get("scheme", "https")
    ws_scheme = "wss" if scheme == "https" else "ws"
    app_key = reverb.get("app_key")

    return (
        f"{ws_scheme}://{host}:{port}{path}/app/{app_key}"
        "?protocol=7&client=python-ws-client&version=0.1&flash=false"
    )


def ensure_absolute_url(url: str) -> str:
    if url.startswith(("http://", "https://")):
        return url
    return f"{API_BASE_URL.rstrip('/')}/{url.lstrip('/')}"


def pick_file_to_upload() -> Path | None:
    """Decide which file to upload when the server asks."""
    if SPECIFIC_UPLOAD_FILE:
        candidate = Path(SPECIFIC_UPLOAD_FILE).expanduser().resolve()
        if candidate.is_file():
            return candidate

    base_dir = Path(FILES_DIRECTORY).expanduser().resolve()
    if not base_dir.is_dir():
        return None

    matched = sorted(base_dir.glob(f"{TENANT_UUID}*"))
    if matched:
        return matched[0]

    for candidate in base_dir.iterdir():
        if candidate.is_file():
            return candidate

    return None


def upload_to_server(upload_url: str, file_path: Path) -> Dict[str, Any]:
    with file_path.open("rb") as handle:
        files = {"file": (file_path.name, handle)}
        response = requests.post(
            upload_url,
            files=files,
            data={"uuid": TENANT_UUID},
            timeout=60,
        )
    response.raise_for_status()
    try:
        return response.json()
    except ValueError:
        return {"status": "ok"}


# ---------------------------------
# WebSocket handling
# ---------------------------------
def connect_and_listen(ws_url: str) -> None:
    sslopt = {"cert_reqs": ssl.CERT_NONE} if INSECURE_SSL and ws_url.startswith("wss://") else None

    print(f"[WS] Connecting to {ws_url}")
    ws = websocket.create_connection(ws_url, sslopt=sslopt, timeout=REQUEST_TIMEOUT)
    ws.settimeout(None)

    try:
        # Initial handshake
        message = ws.recv()
        print(f"[WS] <- {message}")
        event = json.loads(message)
        if event.get("event") != "pusher:connection_established":
            raise RuntimeError("Unexpected first event, expected connection_established")

        # Subscribe
        subscribe_payload = {
            "event": "pusher:subscribe",
            "data": {"channel": CHANNEL_NAME},
        }
        print(f"[WS] -> {subscribe_payload}")
        ws.send(json.dumps(subscribe_payload))

        message = ws.recv()
        print(f"[WS] <- {message}")
        subscribed = json.loads(message)
        if subscribed.get("event") != "pusher_internal:subscription_succeeded":
            raise RuntimeError("Subscription did not succeed")

        # Say hello to test :)
        hello_payload = {
            "event": "client-tenant-hello",
            "channel": CHANNEL_NAME,
            "data": {
                "uuid": TENANT_UUID,
                "tenant_name": TENANT_NAME,
            },
        }
        print(f"[WS] -> {hello_payload}")
        ws.send(json.dumps(hello_payload))

        print("[WS] Waiting for events. . . . . . . . . . . . . . . . . .  Press Ctrl+C to exit.")

        while True:
            try:
                raw = ws.recv()
            except KeyboardInterrupt:
                print("[WS] Interrupted by user, closing connection")
                break
            except websocket.WebSocketConnectionClosedException:
                print("[WS] Conection closed by server")
                break

            if not raw:
                continue

            print(f"[WS RAW] {raw}")

            try:
                payload = json.loads(raw)
            except json.JSONDecodeError:
                continue

            event_name = payload.get("event")

            if event_name == "pusher:ping":
                ws.send(json.dumps({"event": "pusher:pong"}))
                continue

            if event_name == "tenant-upload-request":
                handle_upload_request(payload)
                continue

    finally:
        ws.close()


def handle_upload_request(payload: Dict[str, Any]) -> None:
    data = payload.get("data", {})
    if isinstance(data, str):
        try:
            data = json.loads(data)
        except json.JSONDecodeError:
            data = {}

    if not isinstance(data, dict):
        print("[UPLOAD] Invalid data payload")
        return

    upload_url = data.get("upload_url") or data.get("upload_path")
    if not upload_url:
        print("[UPLOAD] Missing upload_url in event payload")
        return

    upload_url = ensure_absolute_url(upload_url)

    file_path = pick_file_to_upload()
    if not file_path:
        print("[UPLOAD] No suitable file found to upload! Please, check file or path config bro")
        return

    try:
        result = upload_to_server(upload_url, file_path)
    except requests.RequestException as exc:
        print(f"[UPLOAD] Failed: {exc}")
    else:
        print(f"[UPLOAD] Uploaded {file_path.name} -> {result}")


# ----------------------------
# main
# ----------------------------
def main() -> int:
    try:
        result = register_connection()
    except requests.RequestException as exc:
        print(f"[API] Request error: {exc}", file=sys.stderr)
        return 1

    print(f"[API] Tenant registered: {json.dumps(result, indent=2, default=str)}")

    ws_url = build_ws_url(result)

    try:
        connect_and_listen(ws_url)
    except Exception as exc:
        print(f"[WS] Error: {exc}", file=sys.stderr)
        return 1

    return 0


if __name__ == "__main__":
    sys.exit(main())
