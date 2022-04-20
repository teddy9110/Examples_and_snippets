import styled from 'styled-components'

export const TestimonialContainer = styled.article`
  text-align: center;
  padding: 2rem 0;

  section {
    h3 {
      text-align: center;
    }
  }

  h1,
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

  @media (min-width: 450px) {
    h1,
    h2,
    h3,
    h4,
    h5,
    h6 {
      font-size: 2.5rem;
      padding: 0 1rem;
    }
  }

  @media (min-width: 901px) {
    h1,
    h2,
    h3,
    h4,
    h5,
    h6 {
      padding: 0;
    }
  }

  .swiper-button-prev,
  .swiper-button-next {
    color: ${(p) => p.theme.colors.secondary};

    &:after {
      font: normal normal normal 1.2rem FontAwesome;
      content: "\f054";
    }
  }

  .swiper-button-prev {
    &:after {
      content: "\f053";
    }
  }
`

export const TestimonialCard = styled.article`
  text-align: center;
  padding: 2rem 1rem;
  box-sizing: border-box;

  .button {
    margin: 1rem auto;
  }

  .text-cta {
    color: ${(p) => p.theme.colors.secondary};
    border-color: ${(p) => p.theme.colors.secondary};
  }

  .lead {
    img {
      max-width: 215px;
    }
  }

  i {
    color: ${(p) => p.theme.colors.tertiary};
  }

  strong {
    color: ${(p) => p.theme.colors.primary};
  }

  h3 {
    font-size: 1.2rem;
  }

  > section {
    padding: 1rem 2rem;
  }

  img {
    width: 100%;
    height: auto;
    max-width: 300px;
    margin: 0 auto;
    object-fit: cover;
  }

  .stat {
    position: relative;
    display: flex;
    width: 100%;
    align-items: center;
    justify-content: center;
    height: 48px;
    font-size: 1.4rem;

    > span {
      font-weight: bold;
      position: relative;
      z-index: 1;
      background: #fff;
      padding: 0 1rem;
    }
  }

  section.quote {
    height: 140px;

    p {
      margin-bottom: 0.5rem;
    }

    h3 {
      font-size: 20px;
    }
  }

  section.trustpilot {
    img {
      max-width: 200px;
    }
  }

  @media (max-width: 900px) {
    section.trustpilot {
      padding-top: 0;
    }

    section.quote {
      height: 100px;
    }
  }

  @media (max-width: 403px) {
    section.quote {
      height: 140px;
    }
  }

  @media (min-width: 500px) {
    section.quote {
      height: 160px;
    }
  }

  @media (min-width: 901px) {
    display: flex;
    flex-direction: column;
    justify-content: center;
    max-width: 328px;
    margin: 0 auto;

    > section {
      text-align: center;
      padding: 0;
    }

    section.quote {
      width: 80%;
      margin: 0 auto;
    }
  }
`
