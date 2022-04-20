import styled from 'styled-components'

export const PageStyle = styled.div`
  margin: 130px 10% 4rem 10%;
`

export const CartLayout = styled.div`
  display: grid;
  margin-top: 4rem;

  @media (min-width: 901px) {
    gap: 4rem;
    grid-template-columns: 1.5fr 1fr;
  }
`

export const CartSummary = styled.article`
  border: 1px solid #c6c6c8;
  padding: 2rem;

  label {
    font-size: 0.8rem;
  }

  > div > div {
    padding: 0.5rem 0;
    display: grid;
    grid-template-columns: 1fr 1fr;
  }

  h4 {
    font-size: 1.5rem;
  }

  .total {
    margin-top: 1rem;
    padding: 1rem 0 2rem 0;
    border-top: 1px solid #c6c6c8;
    font-size: 1.5rem;
  }

  .button {
    max-width: 100%;
    width: 100%;
  }

  .continue {
    padding: 2rem;
    text-align: center;

    a {
      color: ${(p) => p.theme.colors.secondary};
    }
  }

  .price {
    text-align: right;
  }

  strong {
    color: ${(p) => p.theme.colors.secondary};
  }
`

export const CartItems = styled.li`
  list-style: none;
  display: grid;
  grid-template-columns: 1fr 2.5fr 1fr;
  border-bottom: 1px solid #c6c6c8;

  > section {
    display: flex;
    align-items: center;
  }

  h3 {
    font-size: 1.3rem;
  }

  .details {
    padding: 2rem 1rem;
    flex-direction: column;
    align-items: flex-start;
  }

  label {
    visibility: hidden;
  }

  .remove {
    all: unset;
    text-align: center;

    &:hover {
      cursor: pointer;
    }
  }

  form {
    display: flex;
    align-items: center;
    justify-content: center;
  }

  i {
    font-size: 1.4rem;
    color: ${(p) => p.theme.colors.primary};
  }
`

export const PageHeader = styled.header`
  border-bottom: 1px solid #e8e8e8;
  display: flex;
  flex-direction: column;

  h1 {
    font-size: 1.5rem;
    padding: 0.5rem 0;
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
    align-items: center;
    justify-content: space-between;
    flex-direction: row;

    .collection-select {
      display: none;
    }

    h1 {
      border-bottom: 0;
    }
  }
`

export const Loader = styled.span`
  display: flex;
  height: 200px;
  min-width: 100%;
  align-items: center;
  justify-content: center;

  i {
    font-size: 1.5rem;
    animation-name: spin;
    animation-duration: 2000ms;
    animation-iteration-count: infinite;
    animation-timing-function: linear;
    color: ${(p) => p.color ?? '#48484b'};
  }

  @keyframes spin {
    from {
      transform: rotate(0deg);
    }

    to {
      transform: rotate(360deg);
    }
  }
`
