import React, { useState } from 'react'
import { InputContainer, Label, Wrapper } from './styles'
import useValidation from 'Hooks/useValidation'

const TextArea = (props: any) => {
  const { label, maxlength } = props
  const [message, validate] = useValidation()
  const [value, setValue] = useState('')

  const validateOnInput = (e) => {
    validate(e)
  }

  return (
    <InputContainer>
      <Label>{label}</Label>
      <Wrapper
        {...props}
        onBlur={validateOnInput}
        onInvalid={validate}
        onChange={(e) => setValue(e.target.value)}
      />
      {message.type !== 'error'
        ? null
        : (
          <div className={`message ${message.type}`}>{message.message}</div>
        )}
      <div className={value.length > parseInt(maxlength) ? 'red' : undefined}>
        {value.length} / {maxlength}
      </div>
    </InputContainer>
  )
}

export default TextArea
