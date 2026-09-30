const sectorGrid = document.querySelector("#sector-grid");
const sectorOverlay = document.querySelector("#sector-overlay");
const claimPins = document.querySelector("#claim-pins");

const sectorStates = new Map();
const problematicSectors = new Set();
let statusesSynchronized = false;

function stateClass(state) {
  return state.toLowerCase().replace(/[^a-z0-9]+/g, "-");
}

function showSector(sectorNumber, state) {
  sectorOverlay.innerHTML = "";

  const name = document.createElement("strong");
  const status = document.createElement("span");

  name.textContent = `Sector ${sectorNumber}`;
  status.className = `sector-state ${stateClass(state)}`;
  status.textContent = "Status: " + state;
  sectorOverlay.append(name, status);
}

function sectorNumberForCoordinates(x, y) {
  const column = Math.min(5, Math.max(0, Math.floor(x * 6)));
  const row = Math.min(5, Math.max(0, Math.floor(y * 6)));

  return row * 6 + column + 1;
}

function syncSectorStatuses(sectorCounts) {
  if (!sectorGrid) return Promise.resolve();

  const updates = [];

  for (let sectorNumber = 1; sectorNumber <= 36; sectorNumber += 1) {
    const claimCount = sectorCounts.get(sectorNumber) || 0;
    const state = claimCount >= 3 ? "Problematic" : "Healthy";

    if (state === "Problematic") {
      problematicSectors.add(sectorNumber);
    } else {
      problematicSectors.delete(sectorNumber);
    }

    sectorStates.set(sectorNumber, state);
    const sectorElement = sectorGrid.querySelector(
      `[data-sector="${sectorNumber}"]`,
    );
    sectorElement.dataset.state = state;
    sectorElement.setAttribute(
      "aria-label",
      `Sector ${sectorNumber}: ${state}`,
    );

    updates.push(
      fetch("update_sector_status.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ sector_number: sectorNumber, state }),
      })
        .then((response) => {
          if (!response.ok) throw new Error(`HTTP ${response.status}`);
          return response.json();
        })
        .then((result) => {
          if (!result.success)
            throw new Error(result.message || "Statusupdate mislukt");
        }),
    );
  }

  statusesSynchronized = true;

  return Promise.all(updates);
}

if (sectorGrid) {
  for (let sectorNumber = 1; sectorNumber <= 36; sectorNumber += 1) {
    const sector = document.createElement("button");
    const state = sectorStates.get(sectorNumber) || "Unknown";

    sector.type = "button";
    sector.className = "sector";
    sector.dataset.sector = sectorNumber;
    sector.dataset.state = state;
    sector.setAttribute("aria-label", `Sector ${sectorNumber}: ${state}`);

    const updateOverlay = () =>
      showSector(sectorNumber, sectorStates.get(sectorNumber) || "Unknown");
    sector.addEventListener("mouseenter", updateOverlay);
    sector.addEventListener("focus", updateOverlay);

    sectorGrid.appendChild(sector);
  }

  fetch("get_sectors.php", { cache: "no-store" })
    .then((response) => {
      if (!response.ok) throw new Error(`HTTP ${response.status}`);
      return response.json();
    })
    .then((result) => {
      if (!result.success || !Array.isArray(result.data)) {
        throw new Error("Ongeldige sector-response");
      }

      result.data.forEach((sector) => {
        const sectorNumber = Number(sector.sector_number);
        if (
          sectorNumber >= 1 &&
          sectorNumber <= 36 &&
          typeof sector.state === "string"
        ) {
          const state = statusesSynchronized
            ? sectorStates.get(sectorNumber) || sector.state
            : problematicSectors.has(sectorNumber)
              ? "problematic"
              : sector.state;
          sectorStates.set(sectorNumber, state);
          const sectorElement = sectorGrid.querySelector(
            `[data-sector="${sectorNumber}"]`,
          );
          sectorElement.dataset.state = state;
          sectorElement.setAttribute(
            "aria-label",
            `Sector ${sectorNumber}: ${state}`,
          );
        }
      });
    })
    .catch((error) =>
      console.error("Sectoren konden niet worden geladen:", error),
    );
}

function renderClaim(claim, claimX, claimY) {
  const anchor = document.createElement("a");
  const image = document.createElement("img");
  const isSectorAlert = Boolean(sectorGrid);

  anchor.className = isSectorAlert ? "claim-pin claim-alert" : "claim-pin";
  anchor.href = `article.php?id=${encodeURIComponent(claim.id)}`;
  anchor.setAttribute(
    "aria-label",
    isSectorAlert
      ? `Openstaande claim ${claim.id}`
      : `Bekijk claim ${claim.id}`,
  );

  if (isSectorAlert) {
    const sectorNumber = sectorNumberForCoordinates(claimX, claimY);
    const column = (sectorNumber - 1) % 6;
    const row = Math.floor((sectorNumber - 1) / 6);

    anchor.style.left = `${((column + 0.5) / 6) * 100}%`;
    anchor.style.top = `${((row + 0.5) / 6) * 100}%`;
    image.src = "./images/fire_alert.png";
    image.alt = "Openstaande claim";
  } else {
    anchor.style.left = `${claimX * 100}%`;
    anchor.style.top = `${claimY * 100}%`;
    image.src = "./images/pin.png";
    image.alt = "";
    image.setAttribute("aria-hidden", "true");
  }

  anchor.appendChild(image);
  claimPins.appendChild(anchor);
}

function renderClaims(claims) {
  if (!claimPins) return new Map();

  claimPins.replaceChildren();
  const sectorCounts = new Map();

  claims.forEach((claim) => {
    const x = Number(claim.x_value);
    const y = Number(claim.y_value);
    const hasLocation =
      Number.isFinite(x) &&
      Number.isFinite(y) &&
      x >= 0 &&
      x <= 1 &&
      y >= 0 &&
      y <= 1;
    const claimX = hasLocation ? x : 0;
    const claimY = hasLocation ? y : 0;
    const sectorNumber = sectorNumberForCoordinates(claimX, claimY);

    sectorCounts.set(
      sectorNumber,
      (sectorCounts.get(sectorNumber) || 0) + 1,
    );

    const status = String(claim.status ?? claim.Status ?? "").toLowerCase();
    const isResolved = ["true", "false", "resolved"].includes(status);

    if (sectorGrid && !isResolved) {
      renderClaim(claim, claimX, claimY);
    } else if (!sectorGrid) {
      renderClaim(claim, claimX, claimY);
    }
  });

  return sectorCounts;
}

function loadClaims() {
  fetch("get_claims.php", { cache: "no-store" })
    .then((response) => {
      if (!response.ok) throw new Error(`HTTP ${response.status}`);
      return response.json();
    })
    .then((result) => {
      if (!result.success || !Array.isArray(result.data)) {
        throw new Error("Ongeldige claims-response");
      }

      const sectorCounts = renderClaims(result.data);
      return syncSectorStatuses(sectorCounts);
    })
    .catch((error) =>
      console.error("Claims konden niet op de kaart worden geladen:", error),
    );
}

loadClaims();
setInterval(loadClaims, 1000);
