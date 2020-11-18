import { AuthModes } from "../../Authentication";

export interface ILoginCredentials {
  email: string;
  password: string;
  url?: string;
  project?: string;
  persist?: boolean;
  otp?: string;
}

export interface ILoginOptions {
  persist: boolean;
  storage: boolean;
  mode: AuthModes;
}

export interface ILoginBody {
  email: string;
  password: string;
  otp?: string;
  mode: AuthModes;
}
