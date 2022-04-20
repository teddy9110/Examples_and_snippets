import React from 'react'
import { InputContainer, Label, SelectInput } from './styles'

const Select = (props: any) => {
  const { label, name, children } = props
  return (
    <InputContainer className="select">
      <Label htmlFor={name}>{label}</Label>
      <SelectInput {...props} id={name}>
        {children}
      </SelectInput>
    </InputContainer>
  )
}

export default Select
