async function updateUnreadCount() {
	const unreadElement = document.getElementById('unread');
	if (!unreadElement) return;

	try {
		const response = await fetch('get_claims.php', { cache: 'no-store' });
		if (!response.ok) throw new Error(`HTTP ${response.status}`);

		const result = await response.json();
		if (!result.success || !Array.isArray(result.data)) {
			throw new Error('Ongeldige claims-response');
		}

		unreadElement.textContent = result.data.length;
	} catch (error) {
		console.error('Fout bij ophalen van het aantal claims:', error);
		unreadElement.textContent = '0';
	}
}

document.addEventListener('DOMContentLoaded', () => {
	updateUnreadCount();
	setInterval(updateUnreadCount, 30000);
});
