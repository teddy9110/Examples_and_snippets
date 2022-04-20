import styled from 'styled-components'

export const PageStyle = styled.div`
  padding: 4.8rem 0 3rem;

  > div {
    padding: 0 10%;
  }

  h1 {
    font-size: 2rem;
    margin-bottom: 0;
  }

  @media (min-width: 901px) {
    padding: 5rem 0 3rem;

    > div {
      padding: 0 25%;
    }

    h1 {
      font-size: 3.5rem;
    }
  }
`

export const TransformationEmbed = styled.div`
  display: flex;
  justify-content: center;
`
