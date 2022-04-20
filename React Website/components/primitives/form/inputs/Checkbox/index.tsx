import React from 'react'
import { Wrapper } from './styles'

const Checkbox = (props: any) => {
  const { label, name } = props
  return (
    <Wrapper>
      <input type="checkbox" id={name} name={name} {...props} />
      <label htmlFor={name}>{label}</label>
    </Wrapper>
  )
}

export default Checkbox
