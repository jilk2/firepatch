const sectorGrid = document.querySelector('#sector-grid');
const sectorOverlay = document.querySelector('#sector-overlay');

const sectorStates = [
	'Healthy', 'Healthy', 'Moderate', 'Dangerous', 'Moderate', 'Healthy',
	'Healthy', 'Moderate', 'Moderate', 'Dangerous', 'Healthy', 'Healthy',
	'Moderate', 'Healthy', 'Dangerous', 'Dangerous', 'Moderate', 'Healthy',
	'Healthy', 'Healthy', 'Moderate', 'Moderate', 'Dangerous', 'Healthy',
	'Dangerous', 'Moderate', 'Healthy', 'Healthy', 'Moderate', 'Dangerous',
	'Healthy', 'Moderate', 'Healthy', 'Dangerous', 'Moderate', 'Healthy'
];

function showSector(sectorNumber, state) {
	sectorOverlay.innerHTML = `<strong>Sector ${sectorNumber}</strong><span class="sector-state ${state.toLowerCase()}">${state}</span>`;
}

for (let sectorNumber = 1; sectorNumber <= 36; sectorNumber += 1) {
	const sector = document.createElement('button');
	const state = sectorStates[sectorNumber - 1];

	sector.type = 'button';
	sector.className = 'sector';
	sector.dataset.sector = sectorNumber;
	sector.dataset.state = state;
	sector.setAttribute('aria-label', `Sector ${sectorNumber}: ${state}`);

	sector.addEventListener('mouseenter', () => showSector(sectorNumber, state));
	sector.addEventListener('focus', () => showSector(sectorNumber, state));

	sectorGrid.appendChild(sector);
}
