import styled from 'styled-components'

export const Container = styled.div`
  position: fixed;
  bottom: 0;
  width: 100%;
  z-index: 999999;
  display: none;

  &[data-active="true"] {
    display: block;
  }
`

export const Wrapper = styled.article`
  max-width: 900px;
  margin: 1rem;
  border-radius: 0.5rem;
  background: #fff;
  padding: 1rem;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
  display: flex;
  flex-direction: column;
  align-items: center;

  p {
    flex: 3;
  }

  @media (min-width: 901px) {
    margin: 1rem auto;
    flex-direction: row;
  }
`
