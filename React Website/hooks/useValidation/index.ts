import { useState } from 'react'

const useValidation = (initialMessage = { message: null, type: null }) => {
  const [message, setMessage] = useState(initialMessage)

  const validate = (e) => {
    e.preventDefault()

    if (e.target.validity.valid) {
      setMessage({ message: null, type: 'valid' })
    } else {
      setMessage({ message: e.target.validationMessage, type: 'error' })
    }
  }

  return [message, validate, setMessage] as const
}

export default useValidation
