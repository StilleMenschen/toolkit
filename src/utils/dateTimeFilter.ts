export default function dateTimeFormatter(value: Date | string) {
  if (!value) return "1970-01-01 00:00:00";
  const now = new Date(+new Date(value) + 8 * 60 * 60 * 1000).toISOString();
  return `${now.slice(0, 10)} ${now.slice(11, 19)}`;
}
