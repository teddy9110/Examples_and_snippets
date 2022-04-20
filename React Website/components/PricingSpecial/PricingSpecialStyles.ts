import styled from 'styled-components'

export const PricingCardContainer = styled.div`
  display: flex;
  flex-direction: row;
  justify-content: center;
  column-gap: 30px;
`

export const PricingCard = styled.div`
  display: flex;
  flex-direction: column;
  min-width: 255px;
  max-width: 255px;
  padding: 0 0.8rem;
  margin-bottom: 8px;

  .blue-gradient {
    min-height: 335px;
    margin-bottom: 0.3rem;
    border-radius: 8px;
    background-image: linear-gradient(180deg, #3A4B7D 0%, #1C253D 100%);
    box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
  }

  .orange-gradient {
    margin-top: 2.8rem;
    margin-bottom: 0.3rem;
    min-height: 335px;
    border-radius: 8px;
    background-image: linear-gradient(180deg, #FFAD20 0%, #FF6935 58.36%);
    box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
  }

  @media(min-width: 768px) {
    padding: 0 !important;
  }

  @media(max-width: 768px) {
    padding-left: 16px;
    padding-right: 16px;
  }
`

export const CardHeader = styled.div`
  position: relative;
  text-align:center;

  .question-white {
    position: absolute;
    top: 40px;
    left: 0;
    margin: 0.5rem 0 0 0.5rem;
    font-size: 1.2rem;
    font-weight: 500;
    font-style: normal;
    color: #fff !important;

    &:hover {
      cursor: pointer;
    }
  }

  .savings {
    position: relative;
    top: 23px;
    font-weight: bold;
    text-transform: uppercase;
    margin: 0 auto;
    background-color: #F4B946 !important;
    min-width: 139px;
    height: 32px;
    border-radius: 1.4rem;
    padding: 0.4rem 1rem;
    max-width: 139px;
  }
`

export const CardInner = styled.div`
  position: relative;
  padding: 0.2rem 0.8rem 0.8rem;
  min-height: 190px;

  background-color: #fff;
  border-radius: 8px;

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
    float: right;
    align-self: center;
    font-size: 1rem;
  }

  .signUp {
    background: linear-gradient(180deg, #F22A60 1.19%, #CF194A 100%) !important;
    font-weight: 900 !important;
    min-width: 216px;
    border-radius: 1.4rem;
    height: 40px;
    font-size: 1rem;
    line-height: 1.5rem;
    box-shadow: none;
  }

  form {
    position: absolute;
    bottom: 10px;
    left: 19.5px;
  }
`

export const CardTop = styled.div`
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  text-align: center;

  min-height: 160px;
  color: #fff !important;
  padding: 0 1rem;

  .cardTitle {
    font-size: 1.2rem;
    line-height: 1.8rem;
    font-weight: 700;
    margin-top: 0.75rem;
  }

  .price {
    margin: 0 !important;
    padding: 0 !important;
    color: #fff !important;
    font-weight: 700;
    font-size: 3.3rem;
    line-height: 3.3rem;
    letter-spacing: -2px;
    padding: 0.4rem 1rem;
  }

  .smallText {
    margin-top: 2px;
    line-height: 1.1rem;
    font-size: 1.125rem;
    min-height:35.188px;
  }
`
