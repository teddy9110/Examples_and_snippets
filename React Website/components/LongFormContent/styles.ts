import styled from 'styled-components'

export const Body = styled.div`
  padding: 0 5%;

  strong {
    color: ${(p) => p.theme.colors.secondary};
  }

  img {
    width: 100%;
  }

  p {
    hyphens: auto;
  }

  a {
    hyphens: auto;
    word-break: break-all;
  }

  @media (min-width: 901px) {
    padding: 0 20%;
  }
`
