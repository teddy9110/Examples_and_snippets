import styled from 'styled-components'

export const PageContainer = styled.div`
  max-width: 946px;
  margin: 100px auto;
  text-align: center;

  .button {
    margin: 0 auto;
  }

  h1 {
    margin-top: 2rem;
    font-size: 3rem;
  }

  .banner {
    width: 100%;
    max-width: 523px;
  }
`

export const List = styled.ul`
  padding: 0;
  margin: 0;
  text-align: left;
  list-style: none;
  display: grid;
  grid-template-columns: repeat(1, 1fr);
  gap: 2rem;
  margin-top: 6rem;

  h2 {
    font-size: 1.5rem;
  }

  img {
    object-fit: contain;
  }

  @media (min-width: 901px) {
    grid-template-columns: repeat(3, 1fr);
  }
`
