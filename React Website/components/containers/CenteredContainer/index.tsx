import styled from 'styled-components'

interface ContentContainerProps {
  contrast?: boolean
}

const ContentContainer = styled.div<ContentContainerProps>`
  padding: 2rem 5%;
  display: flex;
  box-sizing: border-box;
  align-items: center;
  flex-direction: column;
  list-style: none;
  column-gap: 0.3rem;
  text-align: center;

  section {
    width: 100%;
    order: 0;
  }

  img {
    width: 100%;
    order: 1;
    margin: 0.5rem auto;
  }

  h2,
  h3,
  h4,
  h5,
  h6 {
    font-size: 2rem;
    margin: 0 0 0 0;
  }

  p {
    margin: 1rem 0;
  }

  ${(p) => !p.contrast
    ? `
      background: ${p.theme.colors.primary_bg};

      strong {
        color: ${p.theme.colors.primary}
      }
    `
    : `
      background: ${p.theme.colors.secondary_bg};
      color: #fff; 

      strong {
        color: ${p.theme.colors.tertiary}
      }

      a {
        color: ${p.theme.colors.tertiary}
      }
    `
}

  @media (min-width: 901px) {
    padding: 4rem 5%;
    grid-template-columns: 1fr 1fr;

    > section {
      padding: 0 5rem;
      order: 1;
    }

    > p {
      width: 50%;
    }

    img {
      order: 0;
    }
  }
`

export default ContentContainer
