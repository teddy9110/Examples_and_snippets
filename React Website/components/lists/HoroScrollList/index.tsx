import styled from 'styled-components'

const HoroScrollList = styled.ul`
  all: unset;
  display: flex;
  overflow-x: auto;
  max-width: 100%;
  list-style: none;
  row-gap: 1rem;
  justify-content: flex-start;
  padding: 0 2rem;

  > * {
    margin-right: 1rem;

    &:last-of-type {
      margin-right: 0;
    }
  }

  @media (min-width: 501px) {
    justify-content: center;
  }
`

export default HoroScrollList
