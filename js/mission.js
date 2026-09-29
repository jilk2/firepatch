const startTime = document.querySelector("#start");
const endTime = document.querySelector("#end");
const range = document.querySelector("#range");
// const changeMission = document.querySelector('#change-mission');

startTime.addEventListener("input", () => syncTimeRange(startTime));
endTime.addEventListener("input", () => syncTimeRange(endTime));
// changeMission.addEventListener('click', changeMissionHandler)

function formatTime(minutes) {
  const hours = Math.floor(minutes / 60);
  const mins = minutes % 60;

  return String(hours).padStart(2, "0") + ":" +
         String(mins).padStart(2, "0");
}

function syncTimeRange(changed) {
  // console.log(changed)
  let startValue = Number(startTime.value);
  let endValue = Number(endTime.value);

  // Houd minimaal 15 minuten tussen de tijden, zodat ze niet overlappen
  if (changed === startTime && startValue >= endValue) {
    startValue = endValue - 15;
    startTime.value = startValue;
  }

  if (changed === endTime && endValue <= startValue) {
    endValue = startValue + 15;
    endTime.value = endValue;
  }

  range.style.left = (startValue / 1440 * 100) + "%";
  range.style.width = ((endValue - startValue) / 1440 * 100) + "%";

  document.querySelector("#startLabel").textContent =
    formatTime(startValue);

  document.querySelector("#endLabel").textContent =
    formatTime(endValue);
}

syncTimeRange(); //zodat hij balk laat zien zonder te tijden veranderd te hebben

const urgentMissionDialog = document.querySelector("#urgentMissionDialog");
if (urgentMissionDialog) {
  urgentMissionDialog.showModal();
}

const missionForm = document.querySelector("#mission-form");
const notification = document.querySelector(".notification");
const claimIdInput = missionForm?.querySelector('input[name="claim_id"]');

if (missionForm && notification && claimIdInput?.value) {
  missionForm.addEventListener("submit", () => {
    notification.classList.add("is-fading");
  });
}

// function changeMissionHandler(e) {
//   e.preventDefault();
  
// }
