Repository Title: TEST_SILENTMODE
Software Engineer: Bintang Passa
CV Website: https://bintangpassa.com

Scenario from the test assignment document:
You are designing a system that involves a cloud-hosted server and multiple on-premise clients (e.g., deployed at restaurants). Each on-premise client resiged within a private local network and is not directly accessible from the public internet. Each client maintains a file of approximately 100MB at local example $HOME/file_to_download.txt

Your tasks is to design and implement a solution where the server can download this file from any connected client on demand.

My strategy:

- Build the server using PHP (Laravel framework)
- Build client script using Python
- Build API communication between server and client using websocket communication

Why we need websocket? because client is not directly accessible from the public internet. CLient will try connecting to server and after success connection server able to send the event to client to trigger the file upload function to server using HTTP request.

OUT OF SCOPE (not a must):

- Auth and or validation mechanism.
- Real tenant applications or database integration.
- Persistent multi-tenant management UI.
- Complex job scheduling or queues.
- Real file encryption or production-grade security.
- Specific File upload protocol (HTTP, Websocket, QUIC and etc)
- File chunking.
- Cloud deployment.
- Horizontal scaling or multi-server clustering.
- UI/UX styling or design quality (use default Laravel Breeze).
- And anything that un-mentioned in the Home Assignment document

How to run server:

- docker compose up

How to run client:

- python3 client_python/ws_client.py --api-base http://127.0.0.1:8080 --uuid 9035b613-b0dd-4cea-8e6e-4ea3ba9715aa

How to test the function (After server and client are running):

- Create new acoount on http://127.0.0.1:8080/register
- Login on http://127.0.0.1:8080/register
- Click "Download to server" button in the UI
- Set the UUID to None if want to create new tenant (new UUID)

Please feel free to reach me at bintangpassa@gmail.com
