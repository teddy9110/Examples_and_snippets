import React from 'react'
import { InputContainer, Label, Wrapper } from './styles'
import useValidation from 'Hooks/useValidation'

const Input = (props: any) => {
  const { label, onBlur, onInput } = props
  const [message, validate] = useValidation()

  const validateOnInput = (e) => {
    validate(e)

    if (typeof onBlur === 'function') {
      onBlur(e)
    }

    if (typeof onInput === 'function') {
      onInput(e)
    }
  }

  return (
    <InputContainer className="input">
      <Label>{label}</Label>
      <Wrapper {...props} onBlur={validateOnInput} onInvalid={validate} />
      {message.type !== 'error'
        ? null
        : (
          <div className={`message ${message.type}`}>{message.message}</div>
        )}
    </InputContainer>
  )
}

export default Input
