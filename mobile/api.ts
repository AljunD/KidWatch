import AsyncStorage from "@react-native-async-storage/async-storage";
import Constants from "expo-constants";

/**
 * Detect environment: emulator vs physical device vs web
 */
function getBaseUrl(): string {
  // Default for emulator
  let url = "http://localhost:8000/api/v1";

  // If running on physical device (Expo Go), use LAN IP
  if (!Constants.manifest?.debuggerHost?.includes("localhost")) {
    url = "http://192.168.1.5:8000/api/v1"; // replace with your LAN IP
  }

  return url;
}

const BASE_URL = getBaseUrl();

/**
 * Centralized endpoints (match Laravel routes with ProfileController)
 */
export const ENDPOINTS = {
  // Authentication
  login: "guardian/login",
  logout: "guardian/logout",

  // Guardian profile
  profile: "guardian/profile",
  updateProfile: "guardian/profile",

  // Students linked to guardian
  students: "guardian/students",
  studentDetail: (id: number) => `guardian/students/${id}`,

  // Progress records
  studentProgress: (id: number) => `guardian/students/${id}/progress`,
  studentProgressHistory: (id: number) => `guardian/students/${id}/progress-history`,

  // Weekly summaries
  studentSummaries: (id: number) => `guardian/students/${id}/summaries`,
  studentSummary: (id: number, week: number) => `guardian/students/${id}/summaries/${week}`,
  generateSummary: (id: number, week: number) => `guardian/students/${id}/summaries/${week}/generate`,
};

/**
 * TypeScript interfaces for API responses
 */
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

/**
 * Generic request function
 */
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

  const response = await fetch(`${BASE_URL}/${endpoint}`, {
    method,
    headers,
    body: body ? JSON.stringify(body) : undefined,
  });

  // ✅ Handle 404 gracefully
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
