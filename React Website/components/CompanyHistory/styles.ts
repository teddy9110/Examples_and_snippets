import styled from 'styled-components'

export const Wrapper = styled.div`
  display: flex;
  width: 100%;
  overflow-x: auto;
  scroll-behavior: smooth;
  -ms-overflow-style: none;

  img {
    height: 38px;
    width: 42px;
    object-fit: fill;
  }

  article {
    min-width: 300px;
    padding: 0rem 4rem 4rem;

    h3 {
      color: ${(p) => p.theme.colors.tertiary};
    }
  }

  &::-webkit-scrollbar {
    width: 10px;
    height: 10px;
  }

  &::-webkit-scrollbar-track {
    border-radius: 0;
    background-color: #3c4354;
  }

  &::-webkit-scrollbar-thumb {
    border-radius: 5px;
    border: 1px solid #3c4354;
    background-color: #fff;
  }

  .dot {
    display: block;
    margin: 1rem 0;
    height: 14px;
    width: 14px;
    border-radius: 1000px;
    background: ${(p) => p.theme.colors.primary};
    color: ${(p) => p.theme.colors.primary};
    overflow: hidden;
  }
`

export const HistoryContainer = styled.article`
  color: #fff;
  height: 600px;
  padding: 6rem 0 4rem 0;
  background: ${(p) => p.theme.colors.secondary};
  background-repeat: repeat-x;
  background-position: 10% 38.5%;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='20' height='20' viewBox='0 0 100 100'%3E%3Ccircle cx='50%' cy='50%' r='10' fill='%23f8fff5'/%3E%3C/svg%3E");

  header {
    padding: 0 4rem;
    display: flex;
    justify-content: space-between;

    div {
      display: flex;
      align-items: center;
      justify-content: center;
    }

    a {
      display: inline-block;
      font-size: 1.5rem;
      margin: 0rem 1rem;
      color: #fff;
    }
  }

  @media (min-width: 901px) {
    background-position: 9% 244px;
  }

  @media (min-width: 1187px) {
    header {
      a {
        display: none;
      }
    }
  }
`
