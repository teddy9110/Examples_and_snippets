import { createGlobalStyle } from 'styled-components'

const GlobalStyle = createGlobalStyle`
  html,body
  {
      width: 100%;
      height: 100%;
      margin: 0px;
      padding: 0px;
      //overflow-x: hidden;
  }
  html, body, #__next{
    height:100%;
  }
  body {
    padding: 0;
    margin: 0;
    overflow-x:hidden;
    background: ${({ theme }) => theme.colors.primary_bg};
    font-family: 'Poppins', sans-serif;
    line-height: 110%;
    color: ${({ theme }) => theme.colors.primary_text};
    font-size: ${({ theme }) => theme.sizes.base_size}px;
    -moz-osx-font-smoothing: grayscale;
    -webkit-font-smoothing: antialiased;
  }
  h1,h2,h3,h4,h5,h6 {
    margin: 0 0 1rem 0;
    line-height: 130%;
  }
  h1 {
    font-size: 2.25rem;
  }

  h2 {
    font-size: 2rem;
  }

  h3 {
    font-size: 1.777rem;
  }

  h4 {
    font-size: 1.333rem;
  }

  h5 {
    font-size: 1rem;
  }

  a {
    font-weight: bold;
    color: ${({ theme }) => theme.colors.secondary};
  }

  p {
    line-height: 170%;
    font-size: 1rem;
    font-weight: light;
    &.lead {
      font-size: 1.2rem;
    }
  }

  strong {
    color: ${(p) => p.theme.colors.primary}
  }

  .text-cta {
    color: ${(p) => p.theme.colors.secondary};
    text-align: auto;
    width: auto;
    display: inline-block;
    align-items: center;
    text-decoration: none;
    font-size: 1.2rem;
    padding: 0.5rem 0;
    border-bottom: 1px solid ${(p) => p.theme.colors.secondary};
    i {
      margin-left: 0.2rem;
    }
  }

  .hide {
    position:absolute;
    left:-10000px;
    top:auto;
    width:1px;
    height:1px;
    overflow:hidden;
  }

  @media (min-width:901px) {
    h1,h2,h3,h4,h5,h6 {
      margin: 0 0 1rem 0;
      line-height: 130%;
    }
    h1 {
      font-size: 4rem !important;
    }

    h2 {
      font-size: 3rem;
    }

    h3 {
      font-size: 2.369rem;
    }

    h4 {
      font-size: 1.777rem;
    }

    h5 {
      font-size: 1.333rem;
    }
  }

  ul,ol {
    margin: 0;
    padding-left: 1.3rem;
    li {
      margin-bottom: 0.5rem;
      line-height:150%;
    }
  }

  .small {
    font-size:0.8rem;
  }

  .swiper-pagination-bullet-active {
    opacity: 1;
    background: var(--swiper-pagination-color, var(--swiper-theme-color));
    background-color: ${(p) => p.theme.colors.secondary};
  }

  .noPadding {
    padding: 0;
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

  .embed embed-youtube {

  }
  .thumb{
    margin-right: 1em;
  }
`

export default GlobalStyle
