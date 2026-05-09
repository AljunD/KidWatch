import AsyncStorage from "@react-native-async-storage/async-storage";
import Constants from "expo-constants";

function getBaseUrl(primary = true): string {
  const hostUri = Constants.expoConfig?.hostUri || Constants.manifest?.debuggerHost;

  if (hostUri) {
    const host = hostUri.split(":")[0]; 

    if (host.includes("localhost") || host === "127.0.0.1") {
      return primary
        ? "http://10.0.2.2:8000/api/v1" 
        : "http://127.0.0.1:8000/api/v1"; 
    } else {
      return primary
        ? `http://${host}:8000/api/v1`
        : "http://127.0.0.1:8000/api/v1";
    }
  }

  return "http://127.0.0.1:8000/api/v1";
}

let BASE_URL = getBaseUrl();

export const ENDPOINTS = {
  login: "guardian/login",
  logout: "guardian/logout",
  profile: "guardian/profile",
  updateProfile: "guardian/profile",
  students: "guardian/students",
  studentDetail: (id: number) => `guardian/students/${id}`,
  studentProgress: (id: number) => `guardian/students/${id}/progress`,
  studentProgressHistory: (id: number) => `guardian/students/${id}/progress-history`,
  studentSummaries: (id: number) => `guardian/students/${id}/summaries`,
  studentSummary: (id: number, week: number) => `guardian/students/${id}/summaries/${week}`,
  generateSummary: (id: number, week: number) => `guardian/students/${id}/summaries/${week}/generate`,
};

export interface ApiResponse<T> {
  success: boolean;
  message: string;
  data?: T;
  errors?: string[];
}

export interface LoginResponse {
  user: {
    id: number;
    email: string;
    role: string;
    email_verified_at: string | null;
  };
  guardian: {
    id: number;
    first_name: string;
    middle_name?: string;
    last_name: string;
    relationship_to_child: string;
    contact_number: string;
    address: string;
  };
  token: string;
  token_type: string;
}

export async function apiRequest<T>(
  endpoint: string,
  method: "GET" | "POST" | "PUT" | "DELETE" = "GET",
  body?: any
): Promise<ApiResponse<T>> {
  const token = await AsyncStorage.getItem("token");

  const headers: Record<string, string> = {
    "Content-Type": "application/json",
  };
  if (token) headers["Authorization"] = `Bearer ${token}`;

  async function tryFetch(url: string): Promise<Response | null> {
    try {
      return await fetch(`${url}/${endpoint}`, {
        method,
        headers,
        body: body ? JSON.stringify(body) : undefined,
      });
    } catch {
      return null;
    }
  }

  let response = await tryFetch(BASE_URL);

  if (!response) {
    const fallbackUrl = getBaseUrl(false);
    BASE_URL = fallbackUrl;
    response = await tryFetch(fallbackUrl);
  }

  if (!response) {
    return {
      success: false,
      message: "Network error",
      errors: ["Failed to connect to server"],
    };
  }

  if (response.status === 404) {
    return {
      success: false,
      message: "No records found",
      data: [] as any,
      errors: ["Resource not found"],
    };
  }

  const text = await response.text();
  let data: ApiResponse<T>;
  try {
    data = JSON.parse(text);
  } catch {
    return {
      success: false,
      message: "Invalid JSON response",
      errors: [text.substring(0, 100) + "..."],
    };
  }

  return {
    ...data,
    success: response.ok && data.success !== false,
  };
}
