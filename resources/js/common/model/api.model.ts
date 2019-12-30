export interface ResponseApi {
  success: boolean
  message: string
  data: any
  error: any
}

export interface ResponseApiAuth {
  success: boolean
  token: string
  message: string
  error: any
}
