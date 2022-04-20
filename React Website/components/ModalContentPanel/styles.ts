import styled from 'styled-components'

export const ResponsiveControl = styled.div`
  .desktop-only {
    display: none;
  }

  .mobile-only {
    display: block;
    padding-bottom: 2rem;
  }

  @media (min-width: 901px) {
    .desktop-only {
      display: block;
    }

    .mobile-only {
      display: none;
    }
  }
`
