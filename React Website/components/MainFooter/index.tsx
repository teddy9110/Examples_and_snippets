import styled from 'styled-components'

const MastBase = styled.footer`
  text-align: left;
  padding: 2rem 0;
  margin: 0 5%;

  div {
    position: relative;
    flex-wrap: wrap;

    > section {
      flex: 1;
      min-width: 33%;
      margin-bottom: 2rem;
    }
  }

  a {
    text-transform: capitalize;
    font-weight: normal;
    text-decoration: none;
    color: ${(p) => p.theme.colors.secondary};
  }

  h4 {
    font-size: 1.3rem;
  }

  ul {
    padding: 0;
    margin: 0;
    list-style: none;

    li {
      margin: 0.3rem 0;
    }
  }

  .newsletter {
    text-align: center;
  }

  .social {
    display: flex;
    font-size: 2rem;
    justify-content: space-around;
    max-width: 200px;

    li {
      text-align: center;
      margin: 1rem;

      &:last-of-type {
        margin: 1rem;
      }
    }
  }

  .social-container {
    min-width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
  }

  .credits {
    width: 100%;
    margin: -40px auto 0 auto;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    font-weight: bold;

    a,
    p {
      flex: 1;
      font-size: 0.8rem;
      font-weight: bold;
      text-align: center;
    }
  }

  @media (max-width: 320px) {
    div {
      position: relative;
      flex-wrap: wrap;

      > section {
        flex: 1;
        min-width: 50%;
        margin-bottom: 2rem;
      }
    }
  }

  @media (min-width: 1010px) {
    div {
      position: relative;
      flex-wrap: wrap;

      > section {
        flex: 1;
        min-width: auto;
      }
    }

    .newsletter {
      > div {
        flex-direction: row;

        input {
          margin-right: 1rem;
        }

        .button {
          min-width: auto;
        }
      }
    }

    .social-container {
      min-width: 0%;
    }

    .social {
      display: flex;
      font-size: 2rem;
      justify-content: space-around;
      max-width: 200px;

      li {
        text-align: center;
        margin: 1rem;

        &:last-of-type {
          margin: 1rem;
        }
      }
    }

    .social-container {
      min-width: 0%;
      display: block;
      align-items: center;
      justify-content: center;
      flex-direction: row;
    }

    .credits {
      flex-direction: row;
      width: 80%;
    }
  }
`

export default MastBase
