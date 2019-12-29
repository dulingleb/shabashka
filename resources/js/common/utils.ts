
export function capitalizeFirst(s: string): string {
  if (typeof s !== 'string' || !s.length) return ''
  return s.charAt(0).toUpperCase() + s.slice(1)
}

export function stringToHslColor(str: string, s: number, l: number): string {
  let hash = 0
  for (let i = 0; i < str.length; i++) {
    hash = str.charCodeAt(i) + ((hash << 5) - hash)
  }

  const h = hash % 360
  return `hsl(${h}, ${s}%, ${l}%)`
}

export function isImage(fileName: string): boolean {
  return /\.(jpe?g|png|gif)$/i.test(fileName)
}

export function getFileNameByUrl(url: string): string {
  return url ? url.replace(/^.*[\\\/]/, '') : ''
}
