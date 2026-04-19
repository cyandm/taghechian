import { css, elapsedTime, flipClock, theme } from "flipclock";
import "flipclock/themes/flipclock";

const EXTENSION_MS = 24 * 60 * 60 * 1000; // 24 hours when renewing

function createClock(parent, targetDate, renewOnEnd) {
  const oneDayMs = 24 * 60 * 60 * 1000;
  const hasDays = targetDate - Date.now() >= oneDayMs;

  const format = hasDays ? "[ss][mm][hh][DD]" : "[ss][mm][hh]";
  const labels = hasDays
    ? [[""], [""], [""], [""]]
    : [[""], [""], [""]];

  const clockInstance = flipClock({
    parent,
    face: elapsedTime({
      to: targetDate,
      format,
    }),
    theme: theme({
      dividers: false,
      labels,
      css: css({
        fontSize: "clamp(2.25rem, 3vw, 2.5rem)",
      }),
    }),
  });

  if (clockInstance?.el) {
    clockInstance.el.classList.add("flipclock-sales-clock");
  }

  return clockInstance;
}

/**
 * Countdown flip clock for sales section (home).
 * Target date from data-countdown-to (ISO 8601).
 * data-renew-on-end: "yes" = extend by 24h when zero; "no" = hide section when zero.
 */
export function Countdown() {
  const parent = document.querySelector("#flipclock-sales");
  if (!parent) return;

  const targetIso = parent.getAttribute("data-countdown-to");
  if (!targetIso) return;

  let targetDate = new Date(targetIso);
  if (Number.isNaN(targetDate.getTime())) return;

  const renewOnEnd = parent.getAttribute("data-renew-on-end") === "yes";
  let clockInstance = createClock(parent, targetDate, renewOnEnd);

  const section = parent.closest("section");
  const checkEnd = () => {
    const remaining = targetDate - Date.now();
    if (remaining > 0) return;

    clearInterval(intervalId);

    if (renewOnEnd) {
      targetDate = new Date(Date.now() + EXTENSION_MS);
      parent.setAttribute("data-countdown-to", targetDate.toISOString());
      if (clockInstance?.unmount) clockInstance.unmount();
      clockInstance = createClock(parent, targetDate, renewOnEnd);
      startEndCheck();
    } else if (section) {
      section.style.display = "none";
    }
  };

  let intervalId;
  function startEndCheck() {
    intervalId = setInterval(checkEnd, 1000);
  }
  startEndCheck();
}
