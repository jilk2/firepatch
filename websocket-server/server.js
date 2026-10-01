const http = require("http");
const { WebSocketServer, WebSocket } = require("ws");

const port = Number(process.env.PORT || 8080);

const httpServer = http.createServer((request, response) => {
  if (request.url === "/health") {
    response.writeHead(200, { "Content-Type": "application/json" });
    response.end(JSON.stringify({ ok: true, clients: wss.clients.size }));
    return;
  }

  response.writeHead(200, { "Content-Type": "text/plain; charset=utf-8" });
  response.end("Firepatch WebSocket server is running.\n");
});

const wss = new WebSocketServer({ server: httpServer });

wss.on("connection", (socket, request) => {
  console.log(`Client connected: ${request.socket.remoteAddress}`);

  socket.on("message", (data, isBinary) => {
    if (isBinary || data.length > 65536) {
      return;
    }

    const text = data.toString("utf8");

    try {
      const message = JSON.parse(text);
      if (message === null || typeof message !== "object" || Array.isArray(message)) {
        return;
      }
    } catch {
      return;
    }

    for (const client of wss.clients) {
      if (client !== socket && client.readyState === WebSocket.OPEN) {
        client.send(text);
      }
    }
  });

  socket.on("close", () => console.log("Client disconnected"));
  socket.on("error", (error) => console.error("WebSocket client error:", error.message));
});

httpServer.listen(port, "0.0.0.0", () => {
  console.log(`Firepatch WebSocket server listening on port ${port}`);
});
