import 'react-native-gesture-handler';
import React, { useEffect, useState, createContext } from 'react';
import { NavigationContainer } from '@react-navigation/native';
import { createStackNavigator } from '@react-navigation/stack';
import AsyncStorage from "@react-native-async-storage/async-storage";
import { SafeAreaProvider } from 'react-native-safe-area-context';

import WelcomeScreen from './WelcomeScreen'; 
import LoginScreen from './Login'; 
import SelectStudentScreen from './SelectStudent'; 
import DashboardScreen from './Dashboard';
import StudentProfileScreen from './StudentProfile';
import WeeklyProgressScreen from './WeeklyProgress';
import ProgressHistoryScreen from './ProgressHistory';
import ProgressDetailScreen from './ProgressDetail';

const Stack = createStackNavigator();

// Auth context to share login state globally
export const AuthContext = createContext<{
  isAuthenticated: boolean;
  setIsAuthenticated: (value: boolean) => void;
}>({
  isAuthenticated: false,
  setIsAuthenticated: () => {},
});

function AuthStack() {
  return (
    <Stack.Navigator initialRouteName="Welcome" screenOptions={{ headerShown: false }}>
      <Stack.Screen name="Welcome" component={WelcomeScreen} />
      <Stack.Screen name="Login" component={LoginScreen} />
    </Stack.Navigator>
  );
}

function AppStack() {
  return (
    <Stack.Navigator initialRouteName="Dashboard" screenOptions={{ headerShown: false }}>
      <Stack.Screen name="Dashboard" component={DashboardScreen} />
      <Stack.Screen name="SelectStudent" component={SelectStudentScreen} />
      <Stack.Screen name="StudentProfile" component={StudentProfileScreen} />
      <Stack.Screen name="WeeklyProgress" component={WeeklyProgressScreen} />
      <Stack.Screen name="ProgressHistory" component={ProgressHistoryScreen} />
      <Stack.Screen name="ProgressDetail" component={ProgressDetailScreen} />
    </Stack.Navigator>
  );
}

export default function App() {
  const [isAuthenticated, setIsAuthenticated] = useState(false);
  const [loading, setLoading] = useState(true);

  // Check token on startup
  useEffect(() => {
    const checkAuth = async () => {
      try {
        const token = await AsyncStorage.getItem('token');
        setIsAuthenticated(!!token);
      } catch (error) {
        console.error('Error checking auth token:', error);
      } finally {
        setLoading(false);
      }
    };
    checkAuth();
  }, []);

  if (loading) {
    return null; // or a splash screen component
  }

  return (
    <SafeAreaProvider>
      <AuthContext.Provider value={{ isAuthenticated, setIsAuthenticated }}>
        <NavigationContainer>
          {isAuthenticated ? <AppStack /> : <AuthStack />}
        </NavigationContainer>
      </AuthContext.Provider>
    </SafeAreaProvider>
  );
}
