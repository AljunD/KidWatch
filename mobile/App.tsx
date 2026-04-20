import React, { useState } from "react";
import {
  ImageBackground,
  Text,
  TextInput,
  TouchableOpacity,
  View,
  StyleSheet,
  Alert,
} from "react-native";
import AsyncStorage from "@react-native-async-storage/async-storage";

export default function App() {
  const [username, setUsername] = useState("");
  const [password, setPassword] = useState("");

  const handleLogin = async () => {
    try {
      const response = await fetch("http://your-domain.com/api/v1/auth/login", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Accept: "application/json",
        },
        body: JSON.stringify({
          username: username,
          password: password,
        }),
      });

      const data = await response.json();

      if (response.ok) {
        // Save token securely
        await AsyncStorage.setItem("authToken", data.token);

        Alert.alert("Login Successful", "Welcome back!");
        // Navigate to next screen here if using React Navigation
      } else {
        Alert.alert("Login Failed", data.message || "Invalid credentials");
      }
    } catch (error) {
      Alert.alert("Error", "Something went wrong. Please try again.");
    }
  };

  return (
    <ImageBackground
      source={require("./assets/back.jpg")}
      style={styles.background}
      resizeMode="cover"
    >
      <View style={styles.container}>
        <Text style={styles.title}>Welcome Back!</Text>
        <Text style={styles.subtitle}>Login to continue your adventure.</Text>

        <TextInput
          placeholder="Username"
          placeholderTextColor="#888"
          style={styles.input}
          value={username}
          onChangeText={setUsername}
        />

        <TextInput
          placeholder="Password"
          placeholderTextColor="#888"
          secureTextEntry
          style={styles.input}
          value={password}
          onChangeText={setPassword}
        />

        <TouchableOpacity style={styles.button} onPress={handleLogin}>
          <Text style={styles.buttonText}>LOGIN</Text>
        </TouchableOpacity>

        <Text style={styles.forgot}>Forget Password?</Text>
      </View>
    </ImageBackground>
  );
}

const styles = StyleSheet.create({
  background: {
    flex: 1,
  },
  container: {
    flex: 1,
    justifyContent: "center",
    paddingHorizontal: 25,
  },
  title: {
    fontSize: 22,
    fontWeight: "bold",
    color: "#fff",
    textAlign: "center",
    marginBottom: 10,
  },
  subtitle: {
    fontSize: 14,
    color: "#eee",
    textAlign: "center",
    marginBottom: 30,
  },
  input: {
    backgroundColor: "#e0e0e0",
    borderRadius: 25,
    paddingVertical: 15,
    paddingHorizontal: 20,
    marginBottom: 15,
  },
  button: {
    backgroundColor: "#2d8ac7",
    paddingVertical: 15,
    borderRadius: 25,
    alignItems: "center",
    marginTop: 10,
  },
  buttonText: {
    color: "#fff",
    fontWeight: "bold",
    letterSpacing: 1,
  },
  forgot: {
    textAlign: "center",
    marginTop: 15,
    color: "#ddd",
    fontSize: 12,
  },
});
