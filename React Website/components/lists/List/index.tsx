import styled from 'styled-components'

interface ListProps {
  direction?: 'column' | 'row'
}

const List = styled.ul<ListProps>`
  all: unset;
  list-style: none;
  display: flex;
  align-items: center;
  flex-direction: column;

  > li {
    margin-bottom: 0.5rem;
  }

  @media(min-width: 501px) {
    flex-direction: ${(props) => props.direction ?? 'row'};

    > li {
      margin: 0;
      margin-right: 0.5rem;
    }
  }
`

export default List
