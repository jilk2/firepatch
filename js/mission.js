const startTime = document.getElementById("start");
const endTime = document.getElementById("end");
const range = document.getElementById("range");

function formatTime(minutes) {
  const hours = Math.floor(minutes / 60);
  const mins = minutes % 60;

  return String(hours).padStart(2, "0") + ":" +
         String(mins).padStart(2, "0");
}

function update(changed) {
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

  document.getElementById("startLabel").textContent =
    formatTime(startValue);

  document.getElementById("endLabel").textContent =
    formatTime(endValue);
}

startTime.addEventListener("input", () => update(startTime));
endTime.addEventListener("input", () => update(endTime));

update(); //zodat hij balk laat zien zonder te tijden veranderd te hebben