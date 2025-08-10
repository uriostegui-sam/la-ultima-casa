import { capitalizeFirstLetter, choseCurrentLanguage, formatDateRange } from '../../../../src/shared/services/Helpers'
import { describe, expect, test } from 'vitest'
import { Languages } from '../../../../src/shared/services/Translation'

describe('capitalizes the first letter', () => {
  test('capitalizes the first letter of a lowercase word', () => {
    expect(capitalizeFirstLetter('test')).toBe('Test')
  })

  test('returns empty string when input is undefined', () => {
    expect(capitalizeFirstLetter(undefined)).toBe('')
  })

  test('does not change the rest of the word', () => {
    expect(capitalizeFirstLetter('tEST')).toBe('TEST')
  })

  test('handles single character strings', () => {
    expect(capitalizeFirstLetter('a')).toBe('A')
  })

  test('handles already capitalized words', () => {
    expect(capitalizeFirstLetter('Word')).toBe('Word')
  })
})

describe('format the date range', () => {
    test('it transform the date to text in english', () => {
        expect(formatDateRange('2025-03-03', '2025-08-26', 'en')).toBe('from Monday March 3 until Tuesday August 26')
    })

    test('it transform the date to text in spanish', () => {
        expect(formatDateRange('2025-03-03', '2025-08-26', 'es')).toBe('del lunes 3 de marzo al martes 26 de agosto')
    })

    test('handles same start and end date correctly', () => {
        expect(formatDateRange('2025-04-04', '2025-04-04', 'en'))
        .toBe('from Friday April 4 until Friday April 4')
    })
})

describe('chose the current language', () => {
    test('it transform the date to text in english', () => {
        const titleExample = {"en":"Dicta ad et iste odio.","es":"Voluptatem eum voluptas quae praesentium sed."}
        expect(choseCurrentLanguage(titleExample,Languages.English)).toBe('Dicta ad et iste odio.')
    })

    test('it transform the date to text in spanish', () => {
        const titleExample = {"en":"Dicta ad et iste odio.","es":"Voluptatem eum voluptas quae praesentium sed."}
        expect(choseCurrentLanguage(titleExample,Languages.Spanish)).toBe('Voluptatem eum voluptas quae praesentium sed.')
    })

    test('it returns an empty string if another language is chosen', () => {
        const titleExample = {"en":"Dicta ad et iste odio.","es":"Voluptatem eum voluptas quae praesentium sed."}
        expect(choseCurrentLanguage(titleExample,Languages.French)).toBe('')
    })
    test('returns Spanish if English is missing and current is English', () => {
        const titleExample = { es: 'Solo en español' }
        expect(choseCurrentLanguage(titleExample, Languages.English)).toBe('Solo en español')
    })

    test('returns empty string if value is empty for chosen language', () => {
        const titleExample = { en: '', es: 'Contenido en español' }
        expect(choseCurrentLanguage(titleExample, Languages.English)).toBe('Contenido en español') // fallback
    })
})