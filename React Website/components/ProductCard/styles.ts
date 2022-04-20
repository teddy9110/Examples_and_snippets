import styled from 'styled-components'

export const Wrapper = styled.li`
  img {
    width: 100%;
    height: auto;
    min-height: 176px;
    max-height: 195px;
    object-fit: contain;
  }

  .button {
    background: ${(p) => p.theme.colors.secondary};

    &:hover {
      background: ${(p) => p.theme.colors.secondary};
    }
  }

  h3 {
    height: 40px;
    font-size: 1rem;
    margin-bottom: 0.3rem;
    overflow: hidden;
    font-weight: 400;
  }

  header {
    padding: 0.5rem 0;
  }

  a {
    text-decoration: none;
    color: ${(p) => p.theme.colors.secondary};
  }

  .button {
    font-weight: normal;
    width: 100%;
    height: 36px;
    max-width: 100%;
  }
`
