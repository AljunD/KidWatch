import 'react-native-gesture-handler';
import React, { useEffect, useState, useCallback } from 'react';
import { NavigationContainer } from '@react-navigation/native';
import { createStackNavigator } from '@react-navigation/stack';
import AsyncStorage from "@react-native-async-storage/async-storage";
import { SafeAreaProvider, SafeAreaView } from 'react-native-safe-area-context';
import { AuthContext } from './AuthContext';
import { apiRequest, ENDPOINTS } from './api';

// Screens
import WelcomeScreen from './WelcomeScreen';
import LoginScreen from './Login';
import SelectStudentScreen from './SelectStudent';
import DashboardScreen from './Dashboard';
import ProfileScreen from './Profile';
import WeeklyProgressScreen from './WeeklyProgress';
import ProgressHistoryScreen from './ProgressHistory';
import ProgressDetailScreen from './ProgressDetail';

const Stack = createStackNavigator();

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
    <Stack.Navigator initialRouteName="SelectStudent" screenOptions={{ headerShown: false }}>
      <Stack.Screen name="SelectStudent" component={SelectStudentScreen} />
      <Stack.Screen name="Dashboard" component={DashboardScreen} />
      <Stack.Screen name="Profile" component={ProfileScreen} />
      <Stack.Screen name="WeeklyProgress" component={WeeklyProgressScreen} />
      <Stack.Screen name="ProgressHistory" component={ProgressHistoryScreen} />
      <Stack.Screen name="ProgressDetail" component={ProgressDetailScreen} />
    </Stack.Navigator>
  );
}

export default function App() {
  const [authenticated, setAuthenticated] = useState(false);
  const [loading, setLoading] = useState(true);
  const [selectedStudent, setSelectedStudent] = useState<any | null>(null);

  // ✅ Logout function
  const logout = useCallback(async () => {
    try {
      await AsyncStorage.removeItem('token');
      setSelectedStudent(null);
      setAuthenticated(false);
    } catch (error) {
      console.error('Error during logout:', error);
    }
  }, []);

  useEffect(() => {
    const checkAuth = async () => {
      try {
        const token = await AsyncStorage.getItem('token');

        if (token) {
          // ✅ Validate token with backend before trusting it
          const res = await apiRequest<any>(ENDPOINTS.profile, "GET");

          if (res.success && res.data) {
            setAuthenticated(true);
          } else {
            // ❌ Invalid token → clear and force login
            await AsyncStorage.removeItem('token');
            setAuthenticated(false);
          }
        } else {
          setAuthenticated(false);
        }
      } catch (error) {
        console.error('Error checking auth token:', error);
        setAuthenticated(false);
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
      <SafeAreaView style={{ flex: 1 }}>
        <AuthContext.Provider
          value={{
            authenticated,
            setAuthenticated,
            selectedStudent,
            setSelectedStudent,
            logout, // ✅ expose logout in context
          }}
        >
          <NavigationContainer>
            {authenticated ? <AppStack /> : <AuthStack />}
          </NavigationContainer>
        </AuthContext.Provider>
      </SafeAreaView>
    </SafeAreaProvider>
  );
}
