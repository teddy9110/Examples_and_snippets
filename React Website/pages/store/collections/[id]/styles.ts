import styled from 'styled-components'

export const PageStyle = styled.div`
  margin: 75px 0 0 0;
  padding: 0 0;

  h1 {
    text-transform: capitalize;
  }

  @media (min-width: 901px) {
    margin: 100px 0 0 0;
  }
`

export const PageHeader = styled.header`
  border-bottom: 1px solid #e8e8e8;
  display: flex;
  flex-direction: column;

  h1 {
    font-size: 1.5rem;
    padding: 0.5rem 5%;
    margin: 0;
    border-bottom: 1px solid #e8e8e8;
    text-transform: uppercase;
  }

  .filters {
    padding: 0.5rem 5% 0.7rem;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;

    > div {
      margin: 0;
    }
  }

  @media (min-width: 901px) {
    padding: 0 5%;
    align-items: center;
    justify-content: space-between;
    flex-direction: row;

    h1,
    .filters {
      padding: 0.5rem 0;
    }

    h1 {
      border-bottom: 0;
    }
  }
`
