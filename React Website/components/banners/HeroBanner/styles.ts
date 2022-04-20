import styled from 'styled-components'

interface HeroBannerBaseProps {
  height?: number
}

export const HeroBannerBase = styled.article<HeroBannerBaseProps>`
  position: relative;
  padding: 4rem 2rem 2rem;
  display: flex;
  align-items: flex-end;
  height: ${({ height }) => height ?? '100vh'};
  box-sizing: border-box;
  background: ${({ theme }) => theme.colors.secondary_bg};
  color: ${({ theme }) => theme.colors.secondary_text};

  h1 {
    font-size: 2.5rem;
    line-height: 110%;
    width: 100%;
  }

  ${({ theme }) => `
    strong {
      color: ${theme.colors.tertiary};
    }
    a {
      color: ${theme.colors.tertiary};
    }
  `}

  p {
    font-size: 1rem;
  }

  > video {
    z-index: 0;
    position: absolute;
    top: 0;
    left: 0;
    object-fit: cover;
    width: 100%;
    height: 100%;
    opacity: 0.5;
  }

  > section {
    z-index: 1;
    position: relative;
  }

  button {
    width: 333px;
  }

  .cta {
    display: flex;
    flex-direction: column;

    a,
    button {
      margin: 0 0 1rem 0;
    }
  }

  @media (min-width: 450px) {
    align-items: center;

    h1 {
      font-size: 4rem;
    }

    p {
      font-size: 1.5rem;

      span {
        display: block;
      }
    }
  }

  @media (min-width: 375px) {
    align-items: center;

    h1 {
      font-size: 3.5rem;
    }

    p {
      font-size: 1.5rem;
    }
  }
  @media (min-width: 807px) {
    padding: 0 5%;
    align-items: center;

    > section {
      width: 50%;
    }

    h1 {
      line-height: 110%;
    }

    .cta {
      flex-direction: row;

      a,
      button {
        margin: 0 1rem 0 0;
      }
    }
  }
`
