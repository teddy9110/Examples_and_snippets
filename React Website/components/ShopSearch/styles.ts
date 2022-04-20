import styled from 'styled-components'

export const Filters = styled.section`
  top: 0;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0.3rem;
  border-bottom: 1px solid #e8e8e8;
  white-space: nowrap;

  .button {
    background: ${(p) => p.theme.colors.secondary};

    &:hover {
      background: ${(p) => p.theme.colors.secondary};
    }
  }

  .items {
    display: none;
    align-items: center;
    gap: 0.5rem;
    overflow-x: scroll;
    scroll-snap-type: x mandatory;
    scroll-behavior: smooth;
    -ms-overflow-style: none;

    &::-webkit-scrollbar {
      display: none;
    }
  }

  a {
    flex-shrink: 0;
    scroll-snap-align: start;
    display: inline-block;
    padding: 0 1rem;
    text-decoration: none;
    text-transform: capitalize;
    color: #000;
    font-weight: 500;
  }

  form {
    min-width: 100%;
    display: flex;
    align-items: center;

    .input {
      min-width: 234px;
      flex: 1;
      margin: 0;
    }
  }

  @media (min-width: 901px) {
    padding: 0.5rem 5% 0.5rem 5%;

    form {
      min-width: auto;
    }

    .items {
      display: flex;
    }
  }
`
