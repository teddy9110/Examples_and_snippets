import styled from 'styled-components'

const ProductGrid = styled.ul`
  padding: 1rem 2%;
  margin: 0;
  display: grid;
  grid-template-columns: 1fr 1fr;
  list-style: none;
  column-gap: 1rem;
  row-gap: 1rem;

  .button {
    font-size: 0.9rem;
  }

  @media (max-width: 1024px) {
    grid-template-columns: repeat(3, 1fr);
  }
  @media (max-width: 700px) {
    grid-template-columns: repeat(2, 1fr);
  }
  @media (min-width: 901px) {
    padding: 1rem 5%;
    column-gap: 4rem;
    row-gap: 4rem;
    .button {
      font-size: 1rem;
    }
    grid-template-columns: repeat(4, 1fr);
  }
`

export default ProductGrid
