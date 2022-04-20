import styled from 'styled-components'

export const PageHeader = styled.div`
  background-color: ${(p) => p.theme.colors.secondary};

  .spacing-mobile {
    margin-top:4em;
  }

  section {
    width: 100%;
    margin: 0 auto;
    padding-bottom: 1em;
  }

  h3 {
    text-align: center;
    padding-top: 10%;
    color: white;
  }

  .header-coloured {
    color: ${(p) => p.theme.colors.tertiary};
  }

  p {
    text-align: center;
    color: white;
  }

  @media (min-width: 901px) {
    section {
      width: 60%;
    }
    .spacing-mobile {
      margin-top:0em;
    }
  }
`

export const PageStyle = styled.div`

  > div {
    padding: 0 10%;
  }

  h1 {
    font-size: 2rem;
    margin-bottom: 0;
  }

  @media (min-width: 804px) {
    padding: 88px 0 0;
  }

  @media (min-width: 901px) {
    padding: 70px 0 0;

    > div {
      padding: 0 20%;
    }

    h1 {
      font-size: 3.5rem;
    }

    h3 {
      font-size: 2rem;
    }
  }
`

export const PageContent = styled.div`

  .centered-div {
    text-align: center;
    padding-top: 2em;
    padding-bottom:2em;
    margin-left:20%;
    margin-right:20%
  }

  .button-group {
    display:inline-block;
  }

  button {
    display:block !important
  }

  .tour-link {
    margin-top:1em;
    display: block !important;
  }

  @media (min-width: 901px) {
    button {
      display:inline-block !important;
    }

    .button-group {
      display:flex;
    }

    .tour-link {
      margin-left: 1em;
    }
  }
`
