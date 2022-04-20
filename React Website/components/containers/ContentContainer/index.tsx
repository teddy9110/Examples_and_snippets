import styled from 'styled-components'

const ContentContainer = styled.div`
  box-sizing: border-box;
  display: grid;
  grid-template-columns: 1fr;
  list-style: none;
  column-gap: 0.3rem;
  text-align: left;
  align-items: center;

  .light-background {
    background: #fdf0e3;
    margin-top:1em;
    margin-bottom:1em;
    padding:1em;
    border-radius: 25px;
  }

  .left-align {
    display: inline-block;
    grid-template-columns: unset;
  }

  .right-align {
    display: flex;
    grid-template-columns: unset;
    flex-direction: column-reverse;
  }

  .entry-emoji {
    max-width: 20px;
    height:20px;
    display: inline-block;
    padding-bottom: 1%;
  }

  > section {
    direction: ltr;
    text-align: center;
    order: 0;

    &:first-child {
      padding: 2rem 10% 0 10%;
    }
  }

  h2,
  h3,
  h4,
  h5,
  h6 {
    font-size: 2rem;
  }

  .button {
    margin: 0 auto;
  }

  img {
    width: 100%;
    height: auto;
    order: 1;
    display: block;
    object-fit: contain;
  }

  .img {
    &.bottom {
      align-self: end;
    }

    &.full {
      padding-right: 0;
      align-self: end;
    }

    &.center {
      text-align: center;
      align-self: center;
    }
  }

  p {
    margin: 1rem 0;
  }

  p img {
    width: fit-content;
    max-width: 100%;
  }

  strong {
    color: ${(p) => p.theme.colors.primary};
  }

  &.contrast {
    background: ${(p) => p.theme.colors.secondary_bg};
    color: #fff;

    strong {
      color: ${(p) => p.theme.colors.tertiary};
    }

    a {
      color: ${(p) => p.theme.colors.tertiary};
      border-color: ${(p) => p.theme.colors.tertiary};
    }
  }

  &.reverse {
    direction: ltr;

    section {
      &:first-child {
        order: 0;
        padding: 1rem 2rem;
      }
    }

    .img {
      order: 1;

      &.full {
        padding-left: 0;
        align-self: end;
      }
    }
  }

  .embed {
    padding: 1rem;

    iframe {
      width: 100%;
      min-height: 340px;
    }
  }

  .video {
    text-align: center;
    padding: 2rem;
    overflow: hidden;

    video {
      border: 6px solid #fff;
      max-width: 400px;
      width: 100%;
    }
  }

  @media (min-width: 450px) {
    h1 {
      font-size: 2.5rem;
    }
  }

  @media (min-width: 901px) {
    grid-template-columns: 1fr 1fr;

    .content {
      box-sizing: border-box;
      display: grid;
      grid-gap: 50px;
      grid-template-columns: 1fr 1fr;
      list-style: none;
      column-gap: 0.3rem;
      text-align: left;
      align-items: center;
    }

    &.reverse {
      section {
        &:first-child {
          padding: 4rem 20% 4rem 4rem;
        }
      }
    }

    > section {
      text-align: left;
      padding: 0 4rem;
      order: 1;

      &:first-child {
        padding: 4rem 4rem 4rem 20%;
      }
    }

    &.reverse {
      section {
        &:first-child {
          order: 1;
        }
      }

      .img,
      .video {
        order: 0;
      }
    }

    .icon {
      width: auto;
    }

    .button {
      margin: 0;
    }

    img {
      order: 0;
    }

    .img {
      &.center {
        img {
          width: inital;
        }
      }
    }

    h2,
    h3,
    h4,
    h5,
    h6 {
      font-size: 3rem;
    }
  }
`

export default ContentContainer
