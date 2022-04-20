import styled from 'styled-components'

export const PageStyle = styled.div`
  padding: 130px 5%;

  h1 {
    font-size: 2rem;
    text-align: center;
  }

  form {
    max-width: 546px;
    margin: 0 auto;
    display: flex;
    gap: 0.5rem;

    .input {
      flex: 2;
    }
  }

  @media (min-width: 901px) {
    padding: 130px 15%;
  }
`

export const List = styled.ul`
  padding: 0;
  margin: 4rem 0 0;
  list-style: none;
  flex-direction: column;
`
