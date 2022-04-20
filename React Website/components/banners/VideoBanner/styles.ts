import styled from 'styled-components'

export const Heading = styled.h1`
  .mobile-break {
    display: block;
  }

  @media (min-width: 901px) {
    .mobile-break {
      display: inline;
    }

    .desktop-break {
      display: block;
    }
  }
`

export const List = styled.ul`
  padding: 0;
  margin: 0 0 1rem 0;
  font-size: 1.2rem;
  list-style: none;
`

export const Wrapper = styled.i`
  color: ${(p) => p.theme.colors.valid};
  margin-right: 0.5rem;
`
