import React, { useState } from 'react'
import Input from 'Components/primitives/form/inputs/Input'
import Select from 'Components/primitives/form/inputs/Select'
import TextArea from 'Components/primitives/form/inputs/TextArea'
import Checkbox from 'Components/primitives/form/inputs/Checkbox'
import Button from 'Components/primitives/form/Button'
import useForm from 'Hooks/useForm'
import axios from 'axios'
import imageCompression from 'browser-image-compression'
import { PageHeader, PageStyle, ErrorSpan, SuccessSpan } from './styles'

const Index = (values) => {
  const [afterPhoto, setAfterPhoto] = useState(null)
  const [beforePhoto, setBeforePhoto] = useState(null)
  const [disabled, setDisabled] = useState(true)
  const [loading, setLoading] = useState(false)
  const [successMessage, setSuccessMessage] = useState(null)
  const [errorMessage, setErrorMessage] = useState({})

  const submitStory = async (values) => {
    setLoading(true)
    const formData = new FormData()

    // build my Form Data from state.
    Object.keys(values).forEach((key) => {
      if (key === 'marketing_accepted') {
        return formData.append(
          'marketing_accepted',
          (!!values.marketing_accepted).toString()
        )
      } else if (key === 'after_photo') {
        formData.append('after_photo', afterPhoto)
      } else if (key === 'before_photo') {
        formData.append('before_photo', beforePhoto)
      } else {
        formData.append(key, values[key])
      }
    })

    try {
      await axios.post(
        process.env.NEXT_PUBLIC_STORY_ENDPOINT,
        formData,
        {
          headers: {
            'content-type': 'multipart/form-data',
          },
        }
      )
      setLoading(false)
      setErrorMessage({})
      setSuccessMessage('Thank you for submitting your story!')
    } catch (e) {
      setLoading(false)
      setSuccessMessage(null)
      setErrorMessage(e.response.data.message || e.response.data.errors)
    }
  }

  const [submitAction] = useForm(submitStory as any)

  return (
    <>
      <PageHeader>
        <h1>Ready to share your story?</h1>
      </PageHeader>
      <PageStyle>
        <h2>Share your inspirational story</h2>
        <p>
          We would love to hear about your success so far and what motivated you
          to kick-start your weight loss journey.
        </p>

        <p>
          Please take a moment to read through all the questions. The more
          detailed you can be with your answers, the better.
        </p>

        <p>
          At the end of the form you’ll be able to upload that all important
          before &amp; after photo! Ok, let’s do this… 🙂
        </p>

        <hr style={{ margin: '4rem 0' }} />

        <form onSubmit={submitAction}>
          <Input label="First Name *" name="first_name" required />
          <Input label="Second Name *" name="second_name" required />

          <br />
          <Input
            type="date"
            label="Date of birth *"
            name="date_of_birth"
            required
          />
          <Select label="Gender *" name="gender" required>
            <option value="male">Male</option>
            <option value="female">Female</option>
          </Select>
          <Input type="email" label="Email *" name="email" required />

          <hr style={{ margin: '4rem 0' }} />

          <h3>Your Story</h3>
          <Input
            type="number"
            label="total weight loss(lbs) *"
            name="weight_loss"
            required
          />
          <Input
            type="number"
            label="start weight (lbs) *"
            name="start_weight"
            required
          />
          <Input
            type="number"
            label="current weight (lbs) *"
            name="current_weight"
            required
          />

          <TextArea
            label="Tell us your Team RH story (400 character limit) *"
            name="story"
            required
            minLength="10"
            maxlength="400"
          />

          <h3>Upload Before &amp; After Photos</h3>
          <p>
            To guarantee the best progression photos, please view our top tips.
            Follow these and you’ll produce pictures that help you track your
            progress effectively.
          </p>

          <Input
            type="file"
            label="Upload before photo *"
            accept="image/*"
            name="before_photo"
            onChange={async (e) => {
              const imageFile = e.target.files[0]

              const options = {
                maxSizeMB: 1,
                maxWidthOrHeight: 1920,
                useWebWorker: true,
              }
              const compressedFile = await imageCompression(imageFile, options)
              setBeforePhoto(compressedFile)
            }}
            required
          />

          <Input
            type="file"
            accept="image/*"
            label="Upload after photo *"
            name="after_photo"
            onChange={async (e) => {
              const imageFile = e.target.files[0]

              const options = {
                maxSizeMB: 1,
                maxWidthOrHeight: 1920,
                useWebWorker: true,
              }
              const compressedFile = await imageCompression(imageFile, options)
              setAfterPhoto(compressedFile)
            }}
            required
          />

          <h4>Agree to be published</h4>

          <p>
            When you submit your story, you agree to our Progress Photo Consent
            Terms.
          </p>
          <p>
            <Checkbox
              label="I agree to allow Team RH to share my submitted images, story, weight loss and first name via their social media, website or other marketing platforms for marketing and advertising purposes."
              name="marketing_accepted"
              onChange={() => setDisabled(!disabled)}
              defaultValue={false}
              required
            />
            <Checkbox
              label="If you wish to remain anonymous, please check this box and we will ensure your face is blurred on all submitted images."
              name="remain_anonymous"
              defaultValue={false}
            />
          </p>
          <Button type="submit" loading={loading} disabled={disabled}>
            Submit
          </Button>

          {
            typeof errorMessage === 'object'
              ? (
                Object.values(errorMessage).map((item, index) => (
                  <ErrorSpan key={index}>{item[0]}</ErrorSpan>
                ))
              )
              : (
                <ErrorSpan>{errorMessage}</ErrorSpan>
              )
          }
          {successMessage && (
            <SuccessSpan>{successMessage}</SuccessSpan>
          )}
        </form>
      </PageStyle>
    </>
  )
}

export async function getStaticProps (context) {
  return {
    props: {},
    revalidate: 60,
  }
}

export default Index
