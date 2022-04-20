import styled from 'styled-components'

export const ComparisonTable = styled.table`
  position: relative;
  width: 100%;
  background: #fff;
  box-sizing: border-box;
  border-collapse: collapse;
  text-align: center;
  border: 0;
  color: #1a2237;
  font-weight: 500;
  margin-bottom: 2rem;

  thead td {
    padding: 2rem;
  }

  td {
    padding: 0.8rem 0.5rem;
    box-sizing: border-box;
    text-align: center;
    font-size: 1.125rem; // 18px but looks weird
  }

  img {
    width: auto;
    height: 90px;
    display: inline-block;
  }

  .fa-check {
    font-size: 1.3rem;
    color: #15c78d;
  }

  .fa-times {
    font-size: 1.3rem;
    color: #d81235;
  }

  .button {
    min-width: 100%;
  }

  .align-left {
    text-align: left;
    width: 30%;
  }

  @media (min-width: 901px) {
    td {
      border-right: 0;
      border-left: 0;
    }

    .heading {
      background: ${(p) => p.theme.colors.tertiary};
      color: ${(p) => p.theme.colors.secondary};
    }

    .details {
      display: flex;
      flex-direction: column;
      align-items: Center;
    }

    span {
      display: block;

      &.price {
        margin: 1rem 0 0.5rem 0;
        font-size: 1.5rem;
        font-weight: bold;
      }
    }

    .borderHighlight {
      position: absolute;
      height: 540px; // If Font is changed this will need changed also
      width: 20%;
      top: 0px;
      left: 31.5%;

      border: 4px solid #1a2238;
      box-sizing: border-box;
      border-radius: 12px;
    }

    thead td {
      padding: 1rem;
    }

    .rh {

      &.heading {
        background: ${(p) => p.theme.colors.secondary};
        color: #fff;
      }

      &:last-of-type {
        border-bottom: 5px solid ${(p) => p.theme.colors.secondary};
      }
    }
  }
`

export const BlankCell = styled.td`
  border: 0 !important;
`

export const HeaderTitleCell = styled.td`
  border-top: 0 !important;
  border-left: 0  !important;
  text-align: left;
`

export const HeaderTitle = styled.h2`
  font-size: 2rem;
  width: 100%;
  display: inline-block;
`
