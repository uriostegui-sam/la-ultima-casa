import { Languages } from "../Translation"

export function capitalizeFirstLetter(string?: string): string {
  if (!string) return ''
  return string[0].toUpperCase() + string.slice(1)
}

export function formatDateRange(startDateStr: string, endDateStr: string, locale: string) {
  const startDate = new Date(startDateStr)
  const endDate = new Date(endDateStr)

  const weekdayFormatter = new Intl.DateTimeFormat(locale, { weekday: 'long' })
  const dayMonthFormatter = new Intl.DateTimeFormat(locale, {
    day: 'numeric',
    month: 'long',
  })

  const startWeekday = weekdayFormatter.format(startDate)
  const startDayMonth = dayMonthFormatter.format(startDate)

  const endWeekday = weekdayFormatter.format(endDate)
  const endDayMonth = dayMonthFormatter.format(endDate)

  const labels = {
    en: { from: 'from', until: 'until' },
    es: { from: 'del', until: 'al' },
  }

  const { from, until } = labels[locale as 'en' | 'es'] || labels.en

  return `${from} ${startWeekday} ${startDayMonth} ${until} ${endWeekday} ${endDayMonth}`
}


export const choseCurrentLanguage = (
interfaceObj: Record<string, string>,
current: Languages
) => {
  if (current === Languages.English) {
    return interfaceObj['en'] ? interfaceObj['en'] : interfaceObj['es']
  } else if (current === Languages.Spanish) {
    return interfaceObj['es']
  }
  return ''
}