import styled from 'styled-components'

export const MemberPanelContainer = styled.article`
  margin: 2rem 5% 2rem 5%;
  padding: 0 5%;

  section:first-child {
    padding: 0;
  }

  .reverse section:first-child {
    padding: 0 !important;
  }

  .reverse {
    .content {
      order:1;
    }

    .img {
      order: 0;
    }
  }

  > div > section {
    padding: 0 !important;
  }

  .content {
    align-items: start;

    h3 {
      font-size: 1.1rem;
      margin-bottom: 0.1rem;
    }

    .location {
      text-transform: uppercase;
    }
  }

  blockquote {
    padding: 0;
    margin: 2rem 0;
    font-size: 2rem;
    line-height: 130%;
    font-weight: bold;

    &:before {
      content: '"';
      font-size: 3rem;
      color: ${(p) => p.theme.colors.primary};
    }
  }
  > div {
    > section {
      padding: 0 2rem;

      &.img {
        display:flex;
        text-align: center;
        justify-content: center;
        align-items: center;
        align-self: center;
        margin-bottom: 1rem;

        img {
          max-width: 100%;
          max-width: 450px;
        }
      }
    }

    section:first-child {
      padding: 0;
    }
  }

  @media (min-width: 901px) {
    .content {
      padding: 0 4rem !important;
    }

    .reverse {
      .content {
        order: 0 !important;
      }

      .img {
        order: 1 !important;
      }
    }
  }
`
