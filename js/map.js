<<<<<<< HEAD
// const mapImage = document.getElementById("map");
// const ws = new WebSocket("ws://localhost:8080");
// let coordinates = { X: 0, Y: 0 };
=======
const mapImage = document.getElementById("map");
const ws = new WebSocket("ws://localhost:8080");
let coordinates = { coordinates: { x: 0, y: 0 } };
>>>>>>> main

// ws.addEventListener("open", () => {
//   console.log("Connected to the WebSocket server");
// });

// mapImage.addEventListener("click", (event) => {
//   const rect = mapImage.getBoundingClientRect();

//   const x = (event.clientX - rect.left) / rect.width;
//   const y = (event.clientY - rect.top) / rect.height;

<<<<<<< HEAD
//   console.log("-------------------------------------");
//   console.log(`x: ${x}, y: ${y}`);
//   coordinates = {
//     X: x,
//     Y: y,
//   };

//   if (ws.readyState === WebSocket.OPEN) {
//     ws.send(JSON.stringify(coordinates));
//   } else {
//     console.error("WebSocket is not connected");
//   }
// });
=======
  console.log("-------------------------------------");
  console.log(`x: ${x}, y: ${y}`);
  coordinates = {
    coordinates: {
      x: x,
      y: y,
    },
  };

  console.log(JSON.stringify(coordinates))
  if (ws.readyState === WebSocket.OPEN) {
    ws.send(JSON.stringify(coordinates));
  } else {
    console.error("WebSocket is not connected");
  }
});
>>>>>>> main

// ws.addEventListener("message", (event) => {
//   console.log(`Server: ${event.data}`);
// });

// // Handle errors
// ws.addEventListener("error", (error) => {
//   console.error("WebSocket error:", error);
// });

<<<<<<< HEAD
// // Handle connection close
// ws.addEventListener("close", () => {
//   console.log("Disconnected from the server");
//   process.exit(0);
// });
=======
// Handle connection close
ws.addEventListener("close", () => {
  console.log("Disconnected from the server");
  process.exit(0);
});
>>>>>>> main
