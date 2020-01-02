
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

export function getErrTitles(errData: any): string[] {
  const defaultErr = ['Ошибка. Проверьте данные и поробуйте ещё раз.']
  if (!errData) { return defaultErr }
  const res = Object.values(errData).filter(err => err && Array.isArray(err)).map(err => err[0])
  return res.length ? res : defaultErr
}

export function getTextDate(date: Date): string {
  const nowTime = new Date().getTime()
  const dateTime = date.getTime()
  const diferendTime = Math.floor(nowTime - dateTime)
  switch (diferendTime) {
    case 0:
      return 'сегодня'
    case 1:
      return 'вчера'
    case -1:
      return 'завтра'
  }
  return `${date.getDate()}.${date.getMonth() + 1}.${date.getFullYear()}`
}
