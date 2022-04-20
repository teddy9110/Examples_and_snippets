import styled from 'styled-components'

export const Wrapper = styled.li`
  border-bottom: 1px solid #e8e8e8;

  img {
    width: 100%;
    height: auto;
    object-fit: contain;
  }

  section {
    padding: 1rem 0;
    display: flex;
    flex-direction: column;

    > * {
      flex: 1;
    }
  }

  h3 {
    font-weight: 500;
    font-size: 1.2rem;
    margin-bottom: 0.3rem;
    overflow: hidden;
  }

  a {
    display: grid;
    grid-template-columns: 160px 1fr;
    text-decoration: none;
    color: ${(p) => p.theme.colors.secondary};
  }

  p {
    font-weight: normal;
    text-transform: lowercase;
  }
`
