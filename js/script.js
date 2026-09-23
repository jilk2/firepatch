async function laadFeed() {
    const feedContainer = document.getElementById("feed-container");
    if (!feedContainer) return; 

    try {
        const response = await fetch('/php/get_claims.php');
        const result = await response.json();

        if (result.success) {
            // We bouwen eerst de header van de lijst (de kolomnamen)
            let feedHTML = `
                <div class="dashboard-list-container">
                    <div class="list-header">
                        <div class="col-tijd">Tijd</div>
                        <div class="col-activiteit">Claim / Activiteit</div>
                        <div class="col-locatie">Locatie / Bron</div>
                        <div class="col-afbeelding">Bewijs</div>
                    </div>
            `;
            
            result.data.forEach(artikel => {
                // Alleen de tijd uit de timestamp halen (bijv. "14:21")
                const tijd = new Date(artikel.timestamp).toLocaleTimeString('nl-NL', {hour: '2-digit', minute:'2-digit'});
                
                // Een placeholder afbeelding als er geen is opgegeven
                const imageSrc = artikel.image_path ? artikel.image_path : 'images/placeholder-dashboard.jpg';

                // Status kleuring toevoegen aan de tijd (bijv. groen of blauw zoals in je design)
                let tijdColor = "#00aaff";
                if(artikel.Status === "true") tijdColor = "#00ff00";
                if(artikel.Status === "false") tijdColor = "#ff4444";

                feedHTML += `
                    <a href="article.html?id=${artikel.id}" class="list-row-link">
                        <div class="list-row">
                            <div class="col-tijd" style="color: ${tijdColor}; font-weight: bold;">${tijd}</div>
                            <div class="col-activiteit">${artikel.Title}</div>
                            <div class="col-locatie">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                                ${artikel.source ? artikel.source : 'Onbekend'}
                            </div>
                            <div class="col-afbeelding">
                                <div class="image-thumbnail" style="background-image: url('${imageSrc}');"></div>
                            </div>
                        </div>
                    </a>
                `;
            });

            feedHTML += `</div>`; // Sluit de container

            feedContainer.innerHTML = feedHTML;
        }
    } catch (error) {
        console.error("Fout bij laden feed:", error);
    }
}

async function laadArtikel() {
    const articleContainer = document.getElementById("article-container");
    if (!articleContainer) return; 

    // Lees welk artikel-ID we moeten laden uit de URL (bijv: article.html?id=5)
    const urlParams = new URLSearchParams(window.location.search);
    const artikelId = urlParams.get('id');

    if (!artikelId) {
        articleContainer.innerHTML = `<p style="color:red;">Fout: Geen artikel geselecteerd.</p>`;
        return;
    }

    try {
        const response = await fetch(`/php/get_article.php?id=${artikelId}`);
        const result = await response.json();

        if (result.success) {
            const artikel = result.data;
            
            let statusLabel = "IN ONDERZOEK";
            if(artikel.Status === "true") statusLabel = "WAAR";
            if(artikel.Status === "false") statusLabel = "ONWAAR";
            
            const tijd = new Date(artikel.timestamp).toLocaleString('nl-NL');

            // Maak de HTML voor alle comments
            let notesHTML = "";
            if(artikel.notes && artikel.notes.length > 0) {
                artikel.notes.forEach(note => {
                    const noteTijd = new Date(note.timestamp).toLocaleString('nl-NL');
                    notesHTML += `
                        <div class="note">
                            <div class="note-header">
                                <span class="note-author">${note.author_email} [Geverifieerd]</span>
                                <span style="font-size: 0.7em; color: #666; float: right;">${noteTijd}</span>
                            </div>
                            <p class="note-text">${note.text}</p>
                            <a href="${note.source}" target="_blank" class="source-link">[Bron: ${note.source}]</a>
                        </div>
                    `;
                });
            } else {
                notesHTML = `<p style="color: #666; font-style: italic;">Nog geen geverifieerde notities gevonden. Wees de eerste node die bewijs toevoegt.</p>`;
            }

            // Bouw het complete artikel op het scherm, inclusief beschrijving en bron!
            articleContainer.innerHTML = `
                <article class="claim-card">
                    <div class="claim-meta">Gespot op: VerifiNET Database | ${tijd}</div>
                    <h2 class="claim-title">${artikel.Title}</h2>
                    
                    <p class="claim-description" style="margin-top: 15px; color: #ccc; line-height: 1.5;">
                        ${artikel.description || 'Geen extra beschrijving opgegeven.'}
                    </p>
                    <div style="margin-bottom: 20px; margin-top: 10px;">
                        <a href="${artikel.source}" target="_blank" class="source-link" style="color: #00aaff; text-decoration: none;">
                            [Bron van claim: ${artikel.source || 'Geen link beschikbaar'}]
                        </a>
                    </div>
                    
                    <div class="verdict-banner verdict-${artikel.Status}">
                        <strong>${statusLabel}</strong> - Gemarkeerd door de community
                    </div>

                    <div class="community-notes" id="notes-lijst">
                        <h3>Community Verificaties</h3>
                        ${notesHTML}
                    </div>

                    <div class="note-form-container" id="note-form" style="display: none;">
                        <h4>NIEUWE COMMUNITY NOTE (NODE: ${localStorage.getItem('verifinet_user') || 'ONBEKEND'})</h4>
                        <textarea id="input-tekst" class="note-textarea" rows="4" placeholder="Typ hier de feitelijke onderbouwing..."></textarea>
                        <input type="text" id="input-bron" class="note-input" placeholder="Bron (bijv. URL of documentnaam)">
                        <div class="form-buttons">
                            <button class="btn-save" onclick="opslaanNotitie(${artikel.id}, event)">PLAATSEN</button>
                            <button class="btn-cancel" onclick="toggleForm()">ANNULEREN</button>
                        </div>
                    </div>

                    <button class="btn-add-note" id="btn-toggle-form" onclick="toggleForm()">+ Voeg Notitie Toe (Hardware Geautoriseerd)</button>
                </article>
            `;
        } else {
            articleContainer.innerHTML = `<p style="color:red;">Claim bestaat niet (meer).</p>`;
        }
    } catch (error) {
        console.error("Fout bij laden artikel:", error);
        articleContainer.innerHTML = `<p style="color:red; text-align:center;">SYSTEEMFOUT: Kan claim niet inladen.</p>`;
    }
}

function toggleClaimForm() {
    const form = document.getElementById('claim-form');
    const toggleBtn = document.getElementById('btn-toggle-claim');
    if (form.style.display === 'block') {
        form.style.display = 'none';
        toggleBtn.style.display = 'block';
    } else {
        form.style.display = 'block';
        toggleBtn.style.display = 'none';
    }
}

function toggleForm() {
    const form = document.getElementById('note-form');
    const toggleBtn = document.getElementById('btn-toggle-form');
    if (form.style.display === 'block') {
        form.style.display = 'none';      
        toggleBtn.style.display = 'block'; 
    } else {
        form.style.display = 'block';     
        toggleBtn.style.display = 'none';  
    }
}

// --- DATABASE CONNECTIE FUNCTIES (API) ---

async function opslaanClaim(event) {
    if(event) event.preventDefault(); 

    const titleInput = document.getElementById('claim-titel').value; 
    const sourceInput = document.getElementById('source').value;           // NIEUW
    const descriptionInput = document.getElementById('description').value; // NIEUW
    const currentUser = localStorage.getItem('verifinet_user');

    if (!currentUser) {
        alert("ACCES DENIED: Je moet ingelogd zijn om een claim in te dienen.");
        return;
    }
    if (titleInput.trim() === '') {
        alert("Vul een bewering in.");
        return;
    }

    try {
        const response = await fetch('/php/save_claim.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                title: titleInput,
                description: descriptionInput, // NIEUW
                source: sourceInput,           // NIEUW
                author_email: currentUser
            })
        });

        const data = await response.json();

        if (data.success) {
            window.location.reload(); 
        } else {
            alert("FOUT: " + data.message);
        }
    } catch (error) {
        console.error("Netwerk error:", error);
        alert("Systeem offline: Kan de VerifiNET server niet bereiken.");
    }
}

async function opslaanNotitie(claimId, event) {
    if(event) event.preventDefault();

    const noteText = document.getElementById('input-tekst').value; 
    const noteSource = document.getElementById('input-bron').value; 
    const currentUser = localStorage.getItem('verifinet_user');

    if (!currentUser) {
        alert("ACCES DENIED: Geen geldige sessie gevonden.");
        return;
    }
    if (noteText.trim() === '') {
        alert("Je moet een tekst invullen.");
        return;
    }

    try {
        const response = await fetch('/php/save_comment.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                claim_id: claimId,
                text: noteText,
                source: noteSource,
                author_email: currentUser
            })
        });

        const data = await response.json();

        if (data.success) {
            alert("SUCCES: Notitie gekoppeld aan database!");
            window.location.reload(); 
        } else {
            alert("FOUT: " + data.message);
        }
    } catch (error) {
        console.error("Netwerk error:", error);
        alert("Systeem offline: Kan de server niet bereiken.");
    }
}

// --- AUTHENTICATIE CHECK ---

function checkInlogStatus() {
    const ingelogdeGebruiker = localStorage.getItem('verifinet_user');
    const statusDot = document.getElementById('status-dot');
    const statusText = document.getElementById('status-text');
    const authBtn = document.getElementById('auth-btn');

    if (!statusDot || !authBtn) return; // Stop stilletjes als elementen niet op pagina staan

    if (ingelogdeGebruiker) {
        statusDot.style.backgroundColor = '#00ff00';
        statusDot.style.boxShadow = '0 0 8px #00ff00';
        statusText.innerText = `NODE: ${ingelogdeGebruiker}`;

        authBtn.innerText = "UITLOGGEN";
        authBtn.href = "#";
        authBtn.style.color = '#ff4444';
        authBtn.style.borderColor = '#ff4444';
        authBtn.style.backgroundColor = '#330000';

        authBtn.onclick = function(e) {
            e.preventDefault();
            localStorage.removeItem('verifinet_user');
            window.location.reload();
        };
    }
}

// Initieer alles wanneer de pagina laadt
laadFeed();
laadArtikel();
checkInlogStatus();