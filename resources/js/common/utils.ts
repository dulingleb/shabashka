
export function capitalizeFirst(s: string): string {
  if (typeof s !== 'string' || !s.length) return ''
  return s.charAt(0).toUpperCase() + s.slice(1)
}

export function stringToHslColor(title: string, s: number, l: number): string {
  const str = title || ''
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
  if (!date) { return '' }
  const nowTime = new Date().getTime()
  const dateTime = date.getTime()
  const diferendTime = Math.floor((nowTime - dateTime) / (24 * 60 * 60 * 1000))
  const fixDate = (date: number) => date > 9 ? date : `0${date}`
  switch (diferendTime) {
    case 0:
      return 'сегодня'
    case 1:
      return 'вчера'
    case -1:
      return 'завтра'
  }
  return `${fixDate(date.getDate())}.${fixDate(date.getMonth() + 1)}.${date.getFullYear()}`
}

export function getStringInputDate(date: Date, addDays = 0): string {
  const dateRes = new Date(date.getTime() + (addDays * 24 * 60 * 60 * 1000))
  return dateRes.toISOString().split('T')[0]
}

export function getAssessmentTitle(countRate: number): string {
  if (!countRate) { return 'нет отзывов' }
  if (countRate > 5 && countRate < 21) { return `${countRate} отзывов` }
  const lastNumber = countRate % 10
  if (lastNumber === 1) { return `${countRate} отзыв` }
  if (lastNumber > 1 && lastNumber < 5) { return `${countRate} отзыва` }
  return `${countRate} отзывов`
}

export function getOrderDoneTitle(countDone: number): string {
  if (!countDone) { return 'нет заказов' }
  if (countDone > 5 && countDone < 21) { return `выполнил ${countDone} заказов` }
  const lastNumber = countDone % 10
  if (lastNumber === 1) { return `выполнил ${countDone} заказ` }
  if (lastNumber > 1 && lastNumber < 5) { return `выполнил ${countDone} заказа` }
  return `выполнил ${countDone} заказов`
}
