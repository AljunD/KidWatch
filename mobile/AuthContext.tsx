import { createContext } from "react";

export interface AuthContextType {
  authenticated: boolean;
  setAuthenticated: (value: boolean) => void;

  selectedStudent: any | null;
  setSelectedStudent: (student: any | null) => void;

  logout: () => void; // ✅ added logout to context type
}

export const AuthContext = createContext<AuthContextType>({
  authenticated: false,
  setAuthenticated: () => {},

  selectedStudent: null,
  setSelectedStudent: () => {},

  logout: () => {}, // ✅ default no-op function
});
