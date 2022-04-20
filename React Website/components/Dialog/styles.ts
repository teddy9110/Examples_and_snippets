import Modal from 'react-modal'
import styled from 'styled-components'

export const Wrapper = styled(Modal)`
  max-width: 500px;
  width: 80%;
  max-height: 540px;
  overflow-y: auto;
  position: relative;
  min-width: 200px;
  display: flex;
  flex-direction: column;
  background: #fff;
  border-radius: 0.5rem;
  border: 0;

  > * {
    padding: 1rem 1.5rem;
  }

  > header {
    z-index: 1;
    background: #fff;
    top: -1rem;
    display: grid;
    grid-template-columns: 9fr 1fr;
    border-bottom: 1px solid #f1f1f1;

    > div {
      width: 100%;
      overflow: hidden;
      display: flex;
      align-items: center;

      &.close {
        text-align: center;
        align-items: center;
        justify-content: center;
      }
    }

    h2 {
      font-size: 1.2rem;
      line-height: 120%;
      margin: 0;
      width: 100%;
    }

    button {
      text-align: right;
      all: unset;
      font-size: 1.4rem;
      opacity: 0.8;
      height: 40px;

      &:hover {
        opacity: 1;
        cursor: pointer;
      }
    }
  }

  @media (min-width: 901px) {
    max-height: 600px;
  }
`

const W: any = Wrapper
W.defaultStyles.overlay.backgroundColor = 'rgba(0,0,0,0.4)'
W.border = '0'
