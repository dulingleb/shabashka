
export function capitalizeFirst(s: string): string {
  if (typeof s !== 'string' || !s.length) return ''
  return s.charAt(0).toUpperCase() + s.slice(1)
}
