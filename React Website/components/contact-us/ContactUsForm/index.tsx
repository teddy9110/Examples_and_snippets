import React, { useReducer, useState } from 'react'
import { Contact, FormComplete } from './style'
import Button from 'Components/primitives/form/Button'
import axios from 'axios'
import ConfirmEmailInput from 'Components/primitives/form/inputs/ConfirmEmailInput'
import Dialog from 'Components/Dialog'

const MAX_FILE_UPLOAD = 5

const formReducer = (state, event) => {
  return {
    ...state,
    [event.name]: event.value,
  }
}

const ContactUsForm = () => {
  const [form, setFormData] = useReducer(formReducer, {})
  const [submitted, setSubmitted] = useState(false)
  const [isLoading, setIsLoading] = useState(false)
  const [showModal, setShowModal] = useState(false)
  const [modalMessage, setModalMessage] = useState('')

  function uploadMultipleFiles (e) {
    const files = Array.from(e.target.files)
    if (files.length > MAX_FILE_UPLOAD) {
      e.preventDefault()
      setShowModal(true)
      setModalMessage(`Sorry, you are only able to upload ${MAX_FILE_UPLOAD} files. Only 5 files will be attached to your support request`)
    }
    files.length = MAX_FILE_UPLOAD
    const attachments: any[] = []
    files.forEach((file: any) => {
      attachments.push({
        name: file.name,
        value: new File([file], file.name,
          {
            type: file.type,
            lastModified: file.last,
          }),
      })
    })
    setFormData({
      name: 'attachments',
      value: attachments,
    })
  }

  const handleChange = event => {
    setFormData({
      name: event.target.name,
      value: event.target.value,
    })
  }

  const submitForm = async (e) => {
    e.preventDefault()
    const formData = new FormData()

    Object.keys(form).forEach((key) => {
      if (key === 'attachments') {
        form[key].forEach((k, item: string) => {
          formData.append('attachments[' + item + ']', k.value)
        })
      } else {
        formData.append(key, form[key])
      }
    })

    try {
      setIsLoading(true)
      const response = await axios.post(
        `${process.env.NEXT_PUBLIC_URL ?? ''}/web/zendesk`,
        formData,
        {
          headers: {
            'content-type': 'multipart/form-data',
          },
        }
      )

      if (response.status === 200) {
        setSubmitted(true)
      }
      setIsLoading(false)
    } catch (e) {
      setIsLoading(false)
      setModalMessage('Sorry, there was an error with your submission')
    }
  }

  return (
    <>
      { submitted
        ? <FormComplete>
          <h3>Your message has been sent</h3>
          <p>We will try to get back to you as soon as possible.</p>
          <i className="fa fa-5x fa-check-circle" style={{ color: '#15c78c' }}></i>
        </FormComplete>
        : <Contact>
          <h3>Contact Form</h3>
          <article>
            We will get back to you as soon as possible, this is the easiest way to contact us
          </article>
          <form name="contact-us-form" encType="multipart/form-data" onSubmit={submitForm}>

            <label htmlFor="name">Name</label>
            <input
              type="text"
              id="name"
              name="name"
              placeholder="Jane Smith"
              required
              onChange={handleChange}
              maxLength={100}
            />

            <label htmlFor="email">Email</label>
            <input
              type="email"
              id="email"
              name="email"
              placeholder="jane.smith@example.com"
              required
              onChange={handleChange}
              pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$"
            />

            <label htmlFor="confirm-email">Confirm Email</label>
            <ConfirmEmailInput
              type="email"
              id="confirm-email"
              name="confirm-email"
              placeholder="jane.smith@example.com"
              required
              onChange={handleChange}
              pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$"
              email={form.email}
              confirmEmail={form['confirm-email']}
            />

            <label htmlFor="subject">Subject</label>
            <select id="subject" name="subject" required onChange={handleChange} >
              <option selected disabled>Please Select One</option>
              <option value="general-enquiry">General Enquiry</option>
              <option value="app">App Query</option>
              <option value="subscription">Membership</option>
              <option value="product-order">Product Order Query</option>
            </select>

            <label htmlFor="attachments">Attachments (max: 5)</label>
            <input
              type="file"
              name="attachments"
              id="attachments"
              multiple
              accept="image/jpg, image/jpeg, image/png, application/pdf"
              onChange={uploadMultipleFiles}
            />

            <label htmlFor="message">Message</label>
            <textarea
              id="message"
              name="message"
              placeholder="You can write up to 550 characters"
              cols={10}
              rows={20}
              required
              onChange={handleChange}
              maxLength={550}
            />

            <div style={{ margin: '1.5rem auto 0' }}>
              <Button
                type="submit"
                style={{ padding: '0 3rem;' }}
                loading={isLoading}
              >
                Submit
              </Button>
            </div>
          </form>
        </Contact>
      }

      <Dialog
        control={() => setShowModal(false)}
        open={showModal}
        title="File Restriction"
      >
        <p>{modalMessage}</p>
      </Dialog>
    </>
  )
}

export default ContactUsForm
