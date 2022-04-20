import styled from 'styled-components'

export const DesktopPerksContiner = styled.article`

  @media (max-width: 901px) {
    .hide-on-mobile {
      display: none;
    }
  }
  @media (min-width: 901px) {
    .hide-on-mobile {
      display: block;
    }
  }

`

export const TeamPanelContainer = styled.article`

  @media (max-width: 901px) {
    .hide-on-desktop {
      display: block;
    }
  }
  @media (min-width: 901px) {
    .hide-on-desktop {
      display: none;
    }
  }



  text-align: center;

  .button {
    margin: 0 auto 2rem auto;
  }

  .item > article {
    padding-top: 0px;
    margin-top: 50px;
  }
`

export const TeamPanelCard = styled.article`
  display: grid;
  margin: 0 7% 3rem;
  color: #fff;
  text-align: left;

  > picture {
    margin-top: 0 !important;
  }

  .content {
    padding: 2rem;
  }

  h1,
  h2,
  h3,
  h4 {
    font-size: 2rem;
    margin-bottom: 0.1rem;
  }

  picture,
  img {
    width: 100%;
    height: auto;
    border-radius: 0 0 1000px 1000px/260px;
  }

  .benefits-header {
    text-align: center;
    color: black;
    margin: auto;
    margin-bottom: 1em;
    text-transform: unset !important;
    justify-content: center;
  }

  .benefits {
    color: black;
    text-transform: unset !important;
    text-align: center;
    margin: auto;
    width: 80%;
  }

  .icon-image {
    width: 50px;
    height: 50px;
    margin:auto;
    justify-content: center;
    display: flex;
    object-fit: unset;
    border-radius: unset;
  }

  span {
    text-transform: uppercase;
  }

  @media (min-width: 901px) {

    margin: 0 10% 3rem 10%;
    grid-template-columns: 1fr 2fr;

    .hide-on-desktop {
      display: none;
    }

    .content {
      padding: 1rem 2rem;
      text-align: left;
      justify-content: center;
      display: flex;
      flex-direction: column;
    }

    picture,
    img {
      height: 100%;
      object-fit: cover;
      border-radius: 0 340px 340px 0/510px;
    }
  }
`
