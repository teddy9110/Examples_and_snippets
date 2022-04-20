import axios from 'axios'

export const strapiAxiosInstance = axios.create({
  baseURL: `${process.env.NEXT_PUBLIC_STRAPI_URL}/api`,
  headers: {
    Authorization: `bearer ${process.env.STRAPI_API_TOKEN}`,
  },
})

if (process.env.STRAPI_LOG_REQUESTS?.toLowerCase() === 'true') {
  strapiAxiosInstance.interceptors.request.use(request => {
    console.log('[Strapi Request]', JSON.stringify(request, null, 2))
    return request
  })
}
