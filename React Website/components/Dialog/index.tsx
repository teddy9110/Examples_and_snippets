import React from 'react'
import { Wrapper } from './styles'

const customStyles = {
  overlay: {
    zIndex: 9999,
  },
  content: {
    top: '50%',
    left: '50%',
    right: 'auto',
    bottom: 'auto',
    marginRight: '-50%',
    border: 0,
    outline: 'none',
    transform: 'translate(-50%, -50%)',
  },
}

const Dialog = (props: any) => {
  const { open, control, title, children } = props
  return (
    <Wrapper isOpen={open} style={customStyles}>
      <header>
        <div>
          <h2>{title}</h2>
        </div>
        <div className="close">
          <button id="closeDialog" onClick={() => control(false)}>
            <i className="fa fa-times" aria-hidden="true"></i>
          </button>
        </div>
      </header>
      {children}
    </Wrapper>
  )
}

export default Dialog
