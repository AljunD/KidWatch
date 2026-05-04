import React, { useState, useContext } from "react";
import {
  ImageBackground,
  Text,
  TextInput,
  TouchableOpacity,
  View,
  StyleSheet,
  SafeAreaView,
  ActivityIndicator,
} from "react-native";
import { Ionicons } from "@expo/vector-icons";
import AsyncStorage from "@react-native-async-storage/async-storage";
import { StackNavigationProp } from "@react-navigation/stack";

import { apiRequest, ENDPOINTS, LoginResponse } from "./api";
import { AuthContext } from "./App";

type RootStackParamList = {
  Welcome: undefined;
  Login: undefined;
  SelectStudent: undefined;
  Dashboard: undefined;
};

type LoginProps = {
  navigation: StackNavigationProp<RootStackParamList, "Login">;
};

export default function Login({ navigation }: LoginProps) {
  const [email, setEmail] = useState<string>("");
  const [password, setPassword] = useState<string>("");
  const [passwordVisible, setPasswordVisible] = useState<boolean>(false);
  const [loading, setLoading] = useState<boolean>(false);
  const [bannerMessage, setBannerMessage] = useState<string | null>(null);
  const [bannerType, setBannerType] = useState<"success" | "error" | null>(null);

  const { setIsAuthenticated } = useContext(AuthContext);

  const handleLogin = async () => {
    if (!email || !password) {
      setBannerMessage("Please enter both email and password.");
      setBannerType("error");
      return;
    }

    setLoading(true);
    setBannerMessage(null);

    try {
      const res = await apiRequest<LoginResponse>(
        ENDPOINTS.guardianLogin,
        "POST",
        { email, password }
      );

      if (res.success && res.data?.token) {
        await AsyncStorage.setItem("token", res.data.token);
        setIsAuthenticated(true);

        setBannerMessage(res.message || "Login successful!");
        setBannerType("success");

        navigation.replace("SelectStudent");
      } else {
        const errorDetails = res.errors?.join("\n") || "";
        setBannerMessage(
          `${res.message || "Login failed."}${errorDetails ? "\n" + errorDetails : ""}`
        );
        setBannerType("error");
      }
    } catch (error: any) {
      setBannerMessage(error.message || "Unable to connect to server.");
      setBannerType("error");
    } finally {
      setLoading(false);
    }
  };

  return (
    <ImageBackground
      source={require("./assets/back.jpg")}
      style={styles.background}
      resizeMode="cover"
    >
      <SafeAreaView style={styles.safeArea}>
        <TouchableOpacity
          style={styles.backButton}
          onPress={() => navigation.goBack()}
        >
          <Ionicons name="chevron-back" size={32} color="#fff" />
        </TouchableOpacity>

        <View style={styles.overlay}>
          <View style={styles.card}>
            {/* ✅ Inline Banner */}
            {bannerMessage && (
              <View
                style={[
                  styles.banner,
                  bannerType === "success" ? styles.bannerSuccess : styles.bannerError,
                ]}
              >
                <Text style={styles.bannerText}>{bannerMessage}</Text>
              </View>
            )}

            <Text style={styles.title}>Welcome Back!</Text>
            <Text style={styles.subtitle}>
              Track your child's weekly learning progress
            </Text>

            <View style={styles.inputContainer}>
              <TextInput
                placeholder="Email"
                placeholderTextColor="#888"
                style={styles.input}
                value={email}
                onChangeText={setEmail}
                autoCapitalize="none"
                keyboardType="email-address"
              />
              <Ionicons name="person-outline" size={20} color="#555" />
            </View>

            <View style={styles.inputContainer}>
              <TextInput
                placeholder="Password"
                placeholderTextColor="#888"
                secureTextEntry={!passwordVisible}
                style={styles.input}
                value={password}
                onChangeText={setPassword}
              />
              <TouchableOpacity
                onPress={() => setPasswordVisible(!passwordVisible)}
              >
                <Ionicons
                  name={passwordVisible ? "eye-off-outline" : "eye-outline"}
                  size={20}
                  color="#555"
                />
              </TouchableOpacity>
            </View>

            <TouchableOpacity
              style={styles.button}
              onPress={handleLogin}
              disabled={loading}
            >
              {loading ? (
                <ActivityIndicator color="#fff" />
              ) : (
                <Text style={styles.buttonText}>LOGIN</Text>
              )}
            </TouchableOpacity>

            <TouchableOpacity>
              <Text style={styles.forgot}>Forgot Password?</Text>
            </TouchableOpacity>
          </View>
        </View>
      </SafeAreaView>
    </ImageBackground>
  );
}

const styles = StyleSheet.create({
  background: { flex: 1 },
  safeArea: { flex: 1 },
  overlay: {
    flex: 1,
    justifyContent: "center",
    alignItems: "center",
    paddingHorizontal: 20,
    marginTop: -60,
  },
  backButton: { padding: 20, width: 60 },
  card: { width: "100%", padding: 25, borderRadius: 20, alignItems: "center" },
  banner: {
    width: "100%",
    padding: 12,
    borderRadius: 10,
    marginBottom: 15,
  },
  bannerSuccess: { backgroundColor: "#4CAF50" },
  bannerError: { backgroundColor: "#F44336" },
  bannerText: { color: "#fff", fontWeight: "700", textAlign: "center" },
  title: { fontSize: 25, fontWeight: "bold", color: "#fff", marginBottom: 8 },
  subtitle: {
    fontSize: 15,
    color: "#f0f0f0",
    textAlign: "center",
    marginBottom: 25,
  },
  inputContainer: {
    flexDirection: "row",
    alignItems: "center",
    backgroundColor: "#e5e5e5",
    borderRadius: 25,
    paddingHorizontal: 15,
    paddingVertical: 12,
    marginBottom: 15,
    width: "100%",
  },
  input: { flex: 1, color: "#000" },
  button: {
    backgroundColor: "#2d8ac7",
    paddingVertical: 14,
    borderRadius: 25,
    width: "100%",
    alignItems: "center",
    marginTop: 10,
  },
  buttonText: { color: "#fff", fontWeight: "bold", letterSpacing: 1 },
  forgot: { marginTop: 15, color: "#919191", fontSize: 12 },
});
