import styled from 'styled-components'

interface ContainerProps {
  background?: string
}

const Container = styled.div<ContainerProps>`
  padding: 4rem 2rem;

  > h1,
  > h2,
  > h3,
  > h4,
  > h5,
  > h6 {
    text-align: center;
  }

  ${({ background }) => (background ? `background:${background};` : null)}

  @media (min-width: 501px) {
    padding: 4rem 5rem;
  }
`

export default Container
