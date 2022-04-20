import styled from 'styled-components'

interface GridListProps {
  columns?: string
}

const GridList = styled.ul<GridListProps>`
  all: unset;
  display: grid;
  grid-template-columns: 1fr;
  list-style: none;
  column-gap: 1rem;
  row-gap: 1rem;

  @media (min-width: 501px) {
    grid-template-columns: ${({ columns }) => columns ?? '1fr 1fr'};
  }
`

export default GridList
