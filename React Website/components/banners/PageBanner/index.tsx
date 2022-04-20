import styled from 'styled-components'

const PageBanner = styled.header`
  background-color: ${(p) => p.theme.colors.secondary};
  background-image: url("/images/squiggle_bk.svg");
  background-size: cover;
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-direction: column;

  strong {
    color: ${(p) => p.theme.colors.tertiary};
  }
`

export default PageBanner
