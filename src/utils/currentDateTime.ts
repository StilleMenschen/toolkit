export default function currentDateTime(): string {
  const now = new Date(+new Date() + 8 * 60 * 60 * 1000).toISOString();
  return `${now.slice(0, 10)} ${now.slice(11, 19)}`;
}
