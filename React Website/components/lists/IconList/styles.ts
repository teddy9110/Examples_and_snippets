import styled from 'styled-components'

interface WrapperProps {
  contrast?: boolean
  align?: 'left' | 'right'
  columns?: number
}

export const Wrapper = styled.article<WrapperProps>`
  padding: 2rem 5% 0;
  display: grid;
  grid-template-columns: 1;
  box-sizing: border-box;
  column-gap: 3rem;
  row-gap: 1rem;
  text-align: center;

  .benefit-icon {
    max-width:42px;
  }

  img {
    margin-bottom: 1rem;
    width: 100%;
    height: auto;
  }

  h3 {
    font-size: 1.2rem;
  }

  a {
    color: ${(p) => p.theme.colors.secondary};
  }

  > article {
    max-width: 700px;
    margin: 0 auto;
    padding: 1rem;
    display: grid;
    grid-template-columns: 1fr;
    grid-template-rows: max-content;

    section {
      order: 1;
    }

    img {
      order: 0;
      object-fit:cover;
      object-position: center center;
      width: fit-content;

      &.photo {
        width: 100%;
      }
    }
  }

  ${(p) => !p.contrast
    ? `
      background: ${p.theme.colors.primary_bg};

      strong {
        color: ${p.theme.colors.primary};
      }
    `
    : `
      background: ${p.theme.colors.secondary_bg};
      color: #fff;

      strong {
        color: ${p.theme.colors.tertiary};
      }

      a {
        color: ${p.theme.colors.tertiary};
      }
    `
}

  @media (min-width: 1001px) {
    grid-template-columns: repeat(${(p) => p.columns ?? 3}, 1fr);

    ${(p) => {
    switch (p.align) {
      case 'left':
        return `
          > article {

            text-align:left;
            ${
  (p.columns ?? 0) < 2
    ? `
                  grid-template-columns: 1fr 2fr;
                  grid-template-rows: max-content;
                `
    : `grid-template-columns: 1fr;
                   grid-template-rows: max-content;
                   max-width: 400px;`
}`
      case 'right':
        return `
            > article {
              text-align:left;
              ${
  (p.columns ?? 0)
    ? `
                    grid-template-columns: 2fr 1fr;
                    grid-template-rows: 1fr;
                    section {
                      order: 0;
                    }
                    img {
                      order: 1;
                    }
                  `
    : `grid-template-columns: 1fr;
                     grid-template-rows: max-content;
                     max-width: 400px;`
}`
      default:
        return ''
    }
  }}
  }
`
