const mapImage = document.getElementById("map");
const droneImage = document.getElementById("drone");
let coordinates = { X: 0, Y: 0 };
let ws;
let reconnectTimer;

const websocketUrl = ["localhost", "127.0.0.1"].includes(window.location.hostname)
  ? "ws://localhost:8080"
  : "wss://ws.joeymalta.com";

function connectWebSocket() {
  clearTimeout(reconnectTimer);
  ws = new WebSocket(websocketUrl);

  ws.addEventListener("open", () => {
    console.log(`Connected to ${websocketUrl}`);
  });

  ws.addEventListener("message", (event) => {
    console.log(`Server: ${event.data}`);

    try {
      const message = JSON.parse(event.data);
      const receivedCoordinates = message.coordinates ?? message;
      const x = Number(receivedCoordinates.X ?? receivedCoordinates.x);
      const y = Number(receivedCoordinates.Y ?? receivedCoordinates.y);

      if (Number.isFinite(x) && Number.isFinite(y)) {
        moveDrone(x, y);
      }
    } catch {
      console.warn("Ignored invalid WebSocket message");
    }
  });

  ws.addEventListener("error", (error) => {
    console.error("WebSocket error:", error);
  });

  ws.addEventListener("close", () => {
    console.log("Disconnected; retrying in 3 seconds");
    reconnectTimer = setTimeout(connectWebSocket, 3000);
  });
}

connectWebSocket();

function moveDrone(x, y) {
  const normalizedX = Math.min(1, Math.max(0, x));
  const normalizedY = Math.min(1, Math.max(0, y));

  droneImage.style.left = `${normalizedX * 100}%`;
  droneImage.style.top = `${normalizedY * 100}%`;
}

mapImage.addEventListener("click", (event) => {
  const rect = mapImage.getBoundingClientRect();

  const x = Number(Math.min(1, Math.max(0, (event.clientX - rect.left) / rect.width)).toFixed(6));
  const y = Number(Math.min(1, Math.max(0, (event.clientY - rect.top) / rect.height)).toFixed(6));

  console.log("-------------------------------------");
  console.log(`x: ${x}, y: ${y}`);
  coordinates = {
    X: x,
    Y: y,
  };

  moveDrone(x, y);

  if (ws && ws.readyState === WebSocket.OPEN) {
    ws.send(JSON.stringify({ coordinates }));
  } else {
    console.error("WebSocket is not connected");
  }
});
