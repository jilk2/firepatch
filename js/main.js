// const map = document.querySelector(".map");
// const markerClasses = ["m1", "m2", "m3", "m4"];
// const saveButton = document.getElementById("save-marker-positions");
// const storageKey = "mapMarkerPositions";
//
// if (map) {
//     const markers = markerClasses
//         .map((className) => map.querySelector(`.marker.${className}`))
//         .filter(Boolean);
//
//     const getCurrentPositions = () => {
//         const positions = {};
//         markerClasses.forEach((className) => {
//             const marker = map.querySelector(`.marker.${className}`);
//             if (!marker) return;
//             positions[className] = {
//                 left: marker.style.left,
//                 top: marker.style.top
//             };
//         });
//         return positions;
//     };
//
//     const applySavedPositions = () => {
//         const raw = localStorage.getItem(storageKey);
//         if (!raw) return;
//
//         const saved = JSON.parse(raw);
//         markerClasses.forEach((className) => {
//             const marker = map.querySelector(`.marker.${className}`);
//             const position = saved[className];
//             if (!marker || !position) return;
//
//             if (typeof position.left === "string" && typeof position.top === "string") {
//                 marker.style.left = position.left;
//                 marker.style.top = position.top;
//                 marker.style.right = "auto";
//                 marker.style.bottom = "auto";
//             }
//         });
//     };
//
//     markers.forEach((marker) => {
//         marker.style.position = "absolute";
//         marker.style.touchAction = "none";
//         marker.style.cursor = "grab";
//         marker.style.userSelect = "none";
//
//         marker.addEventListener("pointerdown", (event) => {
//             event.preventDefault();
//
//             marker.style.cursor = "grabbing";
//             marker.setPointerCapture(event.pointerId);
//
//             const mapRect = map.getBoundingClientRect();
//             const markerRect = marker.getBoundingClientRect();
//
//             const offsetX = event.clientX - markerRect.left;
//             const offsetY = event.clientY - markerRect.top;
//
//             const move = (e) => {
//                 const maxLeft = mapRect.width - markerRect.width;
//                 const maxTop = mapRect.height - markerRect.height;
//
//                 let left = e.clientX - mapRect.left - offsetX;
//                 let top = e.clientY - mapRect.top - offsetY;
//
//                 left = Math.max(0, Math.min(left, maxLeft));
//                 top = Math.max(0, Math.min(top, maxTop));
//
//                 marker.style.left = `${left}px`;
//                 marker.style.top = `${top}px`;
//                 marker.style.right = "auto";
//                 marker.style.bottom = "auto";
//             };
//
//             const end = () => {
//                 marker.style.cursor = "grab";
//                 marker.removeEventListener("pointermove", move);
//                 marker.removeEventListener("pointerup", end);
//                 marker.removeEventListener("pointercancel", end);
//             };
//
//             marker.addEventListener("pointermove", move);
//             marker.addEventListener("pointerup", end);
//             marker.addEventListener("pointercancel", end);
//         });
//     });
//
//     applySavedPositions();
//
//     if (saveButton) {
//         saveButton.addEventListener("click", () => {
//             localStorage.setItem(storageKey, JSON.stringify(getCurrentPositions()));
//             saveButton.textContent = "Posities opgeslagen";
//             setTimeout(() => {
//                 saveButton.textContent = "Posities opslaan";
//             }, 1200);
//         });
//     }
// }