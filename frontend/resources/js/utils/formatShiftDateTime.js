export function formatShiftDateTime(date, startTime, endTime) {
    const options = {
        weekday: "long",
        year: "numeric",
        month: "long",
        day: "numeric",
    };

    const formattedDate = new Date(date).toLocaleDateString("id-ID", options);
    const formattedStart = startTime.substring(0, 5);
    const formattedEnd = endTime.substring(0, 5);

    return `${formattedDate} — ${formattedStart} - ${formattedEnd}`;
}
