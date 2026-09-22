const mapImage = document.getElementById("map");
const ws = new WebSocket("ws://localhost:8080");
let coordinates = { X: 0, Y: 0 };

ws.addEventListener("open", () => {
  console.log("Connected to the WebSocket server");
});

mapImage.addEventListener("click", (event) => {
  const rect = mapImage.getBoundingClientRect();

  const x = (event.clientX - rect.left) / rect.width;
  const y = (event.clientY - rect.top) / rect.height;

  console.log("-------------------------------------");
  console.log(`x: ${x}, y: ${y}`);
  coordinates = {
    X: x,
    Y: y,
  };

  if (ws.readyState === WebSocket.OPEN) {
    ws.send(JSON.stringify(coordinates));
  } else {
    console.error("WebSocket is not connected");
  }
});

ws.addEventListener("message", (event) => {
  console.log(`Server: ${event.data}`);
});

// Handle errors
ws.addEventListener("error", (error) => {
  console.error("WebSocket error:", error);
});

// Handle connection close
ws.addEventListener("close", () => {
  console.log("Disconnected from the server");
  process.exit(0);
});
