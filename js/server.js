// get the installed ws package
const WebSocket = require("ws");

// setup the server to the port 8080
const wss = new WebSocket.Server({ port: 8080 });

//shows that the server is running
console.log("WebSocket server is running on ws://localhost:8080");

//When a client connects, handle the connection
wss.on("connection", (ws) => {
  //sends message to the newly connected
  ws.send("New client connected");

  console.log("Welcome to the WebSocket server!");

  // when receiving a message, console.log this message.
  ws.on("message", (message) => {
    console.log(`Received: ${message}`);

    // Iterate over all connected clients
    wss.clients.forEach((client) => {
      if (client !== ws && client.readyState === WebSocket.OPEN) {
        // send message to everyone else excluding the sender
        client.send(`${message}`);
      }
    });
  });
  //when websockets closes show this message in console
  ws.on("close", () => {
    console.log("Client disconnected");
  });
});
