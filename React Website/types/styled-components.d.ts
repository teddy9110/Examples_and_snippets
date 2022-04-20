import 'styled-components'

declare module 'styled-components' {
  export interface DefaultTheme {
    colors: {
      primary: string
      secondary: string
      tertiary: string
      quaternary: string
      primary_text: string
      secondary_text: string
      tertiary_text: string
      primary_bg: string
      secondary_bg: string
      tertiary_bg: string
      button_text: string
      error: string
      valid: string
      space_cadette_blue: string
    }

    sizes: {
      base_size: number
      type_scale: number
      line_height: number
    }
  }
}
