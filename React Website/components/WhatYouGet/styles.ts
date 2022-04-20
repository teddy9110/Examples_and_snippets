import styled from 'styled-components'

export const WhatYouGetContainer = styled.div`
  display: flex;
  justify-content: center;
  flex-direction: column;
  padding: 0.8rem;

  h3 {
    font-size: 2rem;
    color: #000;
  }

  .list {
    color: #000;
    display: flex;
    align-items: center;
    flex-direction: column;

    i {
      margin-right: 0.6rem;
      color: #15c78c;
      font-size: 1.5rem;
    }

    > section {
      min-width: 100%;
      box-sizing: border-box;
      margin: 0;
      display: flex;
      align-items: center;
      flex-direction: column;
    }

    ul {
      max-width: 520px;
      margin: 1rem auto;
      list-style: none;
    }

    ul li {
      font-size: 1.125rem;
      line-height: 1.4rem;
      text-align: left;
    }

    ul li:before {
      font-family: 'FontAwesome';
      content: '\f058';
      color: #15c78c;
      margin-right: 0.8rem;
      text-align: left;
      float: left;
      align-self: center;
      font-size: 1rem;
    }

    @media (min-width: 768px) {
      .list section {
        min-width: 0;
      }

      ul {
        columns: 2 !important;
        max-width: 650px;
        width: 100%;
      }
    }

    @media(width: 901px) {
      h3 {
        font-size: 2rem;
        color: #000;
      }

      .list section {
        max-width: 70%;
        margin: 0 auto;
      }
    }
  }
`
