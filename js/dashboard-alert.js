const claimAlert = document.getElementById('claim-alert');
const claimAlertImage = document.getElementById('claim-alert-image');
const claimAlertMessage = document.getElementById('claim-alert-message');
const claimAlertLink = document.getElementById('claim-alert-link');

function handleClaimLocation(claim) {
    const location = claim.source || null;

    document.dispatchEvent(new CustomEvent('firepatch:claim-location', {
        detail: { claim, location },
    }));
}

function showLatestClaim(claim) {
    if (!claimAlert || !claimAlertImage || !claimAlertMessage || !claimAlertLink || !claim) return;

    const title = claim.title || 'Claim zonder titel';

    claimAlertMessage.textContent = title;
    claimAlertLink.href = `article.php?id=${encodeURIComponent(claim.id)}`;
    claimAlertImage.hidden = !claim.image_path;
    claimAlertImage.src = claim.image_path || '';
    claimAlertImage.alt = claim.image_path ? `Bewijsafbeelding voor ${title}` : '';
    claimAlert.hidden = false;
    handleClaimLocation(claim);
}

async function updateClaimAlert() {
    try {
        const response = await fetch('get_latest_claim.php', { cache: 'no-store' });
        if (!response.ok) throw new Error(`HTTP ${response.status}`);

        const result = await response.json();
        if (!result.success || !result.data) return;

        showLatestClaim(result.data);
    } catch (error) {
        console.error('Fout bij ophalen van de nieuwste claim:', error);
    }
}


updateClaimAlert();
setInterval(updateClaimAlert, 1000);

