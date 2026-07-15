export function getPerformanceDateRange(year: number, fromValue?: string, toValue?: string) {
  const yearStart = `${year}-01-01`;
  const yearEnd = `${year}-12-31`;
  const today = new Date().toISOString().slice(0, 10);
  const defaultTo = today.startsWith(`${year}-`) ? today : yearEnd;
  const validDate = (value: string | undefined) => Boolean(value && /^\d{4}-\d{2}-\d{2}$/.test(value) && value >= yearStart && value <= yearEnd);
  let from = validDate(fromValue) ? fromValue! : yearStart;
  let to = validDate(toValue) ? toValue! : defaultTo;

  if (from > to) {
    from = yearStart;
    to = defaultTo;
  }

  const formatter = new Intl.DateTimeFormat("en", { month: "short", day: "numeric", year: "numeric", timeZone: "UTC" });
  return {
    from,
    to,
    fromDate: new Date(`${from}T00:00:00.000Z`),
    toDate: new Date(`${to}T23:59:59.999Z`),
    label: `${formatter.format(new Date(`${from}T00:00:00.000Z`))} - ${formatter.format(new Date(`${to}T00:00:00.000Z`))}`,
  };
}
