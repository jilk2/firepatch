const sectorGrid = document.querySelector('#sector-grid');
const sectorOverlay = document.querySelector('#sector-overlay');

const sectorStates = new Map();

function stateClass(state) {
	return state.toLowerCase().replace(/[^a-z0-9]+/g, '-');
}

function showSector(sectorNumber, state) {
	sectorOverlay.innerHTML = '';

	const name = document.createElement('strong');
	const status = document.createElement('span');

	name.textContent = `Sector ${sectorNumber}`;
	status.className = `sector-state ${stateClass(state)}`;
	status.textContent = state;
	sectorOverlay.append(name, status);
}

for (let sectorNumber = 1; sectorNumber <= 36; sectorNumber += 1) {
	const sector = document.createElement('button');
	const state = sectorStates.get(sectorNumber) || 'Unknown';

	sector.type = 'button';
	sector.className = 'sector';
	sector.dataset.sector = sectorNumber;
	sector.dataset.state = state;
	sector.setAttribute('aria-label', `Sector ${sectorNumber}: ${state}`);

	const updateOverlay = () => showSector(sectorNumber, sectorStates.get(sectorNumber) || 'Unknown');
	sector.addEventListener('mouseenter', updateOverlay);
	sector.addEventListener('focus', updateOverlay);

	sectorGrid.appendChild(sector);
}

fetch('get_sectors.php', { cache: 'no-store' })
	.then((response) => {
		if (!response.ok) throw new Error(`HTTP ${response.status}`);
		return response.json();
	})
	.then((result) => {
		if (!result.success || !Array.isArray(result.data)) {
			throw new Error('Ongeldige sector-response');
		}

		result.data.forEach((sector) => {
			const sectorNumber = Number(sector.sector_number);
			if (sectorNumber >= 1 && sectorNumber <= 36 && typeof sector.state === 'string') {
				sectorStates.set(sectorNumber, sector.state);
				const sectorElement = sectorGrid.querySelector(`[data-sector="${sectorNumber}"]`);
				sectorElement.dataset.state = sector.state;
				sectorElement.setAttribute('aria-label', `Sector ${sectorNumber}: ${sector.state}`);
			}
		});
	})
	.catch((error) => console.error('Sectoren konden niet worden geladen:', error));
